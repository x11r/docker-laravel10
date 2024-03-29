<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\WeatherDaily;
use Carbon\Carbon;
use Exception;
use Facebook\WebDriver\Exception\Internal\UnexpectedResponseException;
use Facebook\WebDriver\Exception\UnrecognizedExceptionException;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverSelect;
use Illuminate\Support\Facades\Log;

class WeatherService
{

	// SeleniumのURL
	const BASE_URL = 'http://chronium:4444/wd/hub';

	// ダウンロードURL
	const DOWNLOAD_URL = 'https://www.data.jma.go.jp/gmd/risk/obsdl/index.php';

	// 都道府県
	private static ?int $prefectureSelect = null;

	// 地点
	private static array $stationSelects = [];

	// ブラウザーのダウンロードリトライ上限回数
	private static int $browserDownloadRetryLimit = 3;

	// ブラウザーのダウンロードリトライ回数
	private static int $browserDownloadRetry = 0;

	private static ?string $dateStart = null;
	private static ?string $dateEnd = null;

	private static array $prefectures = [
		[
			'prefecture_id' => 44,
			'station' => ['東京', '八王子']
		],
		[
			'prefecture_id' => 82,
			'station' => ['福岡']
		],
		[
			'prefecture_id' => 85,
			'station' => ['唐津', '佐賀']
		],
	];

	/**
	 * 毎日のバッチ用
	 * @return void
	 */
	public static function importDaily()
	{
		Log::info(__METHOD__ . ' [start]');
		// ダウンロード
		// 地区一覧
		$prefectures = self::$prefectures;
		foreach ($prefectures as $prefecture) {

			$dateStart = (new Carbon)->addDays(-30);
			self::$yearEnd = (int)$dateStart->format('Y');
			self::$monthEnd = (int)$dateStart->format('m');
			self::$dayEnd = (int)$dateStart->format('d');

			$dateEnd = (new Carbon)->addDays(-1);
			self::$yearEnd = (int)$dateEnd->format('Y');
			self::$monthEnd = (int)$dateEnd->format('m');
			self::$dayEnd = (int)$dateEnd->format('d');

			echo __LINE__ . ' [start] ' . $dateStart->format('Y-m-d')
				. ' [end]' . $dateEnd->format('Y-m-d')
				. PHP_EOL;

			// 都道府県
			self::$prefectureSelect = $prefecture['prefecture_id'];

			// 地点
			self::$stationSelects = $prefecture['station'];

//			self::downloadByBrowser();

		}

		Log::debug(__LINE__ . ' ' . __METHOD__ . ' [start import]');
		// インポート
		self::importCsv();
	}

	/**
	 * 気象庁からCSVをダウンロードしてDBに入れる
	 *
	 * @param null $year
	 * @return void
	 */
	public static function downloadWeatherCsv($prefectureId = null, $start = null, $end = null): void
	{
		Log::info(__METHOD__ . ' [START] [prefecture id] ' . $prefectureId
			. ' [start] ' . $start
			. ' [end] ' . $end
		);

		$prefectures = self::$prefectures;

		foreach ($prefectures as $prefecture) {

			if ($prefectureId && $prefectureId != $prefecture['prefecture_id']) {

				continue;
			}

			// 取得開始
			$yearTarget = $start ?? date('Y');

			// 取得年の最終がない場合は1年だけ取得する
			$yearCurrent = $end ?? $start;

			self::$prefectureSelect = $prefecture['prefecture_id'];
			self::$stationSelects = $prefecture['station'];

			while ($yearTarget <= $yearCurrent) {

				Log::debug(__LINE__
					. ' [prefecture id] ' . self::$prefectureSelect
					. ' [station] ' . print_r(self::$stationSelects, true)
					. ' [year] ' . $yearTarget
				);

				// ダウンロード
				self::downloadByBrowser($yearTarget);

				$yearTarget++;
				sleep(3);
			}
		}

		Log::info(__METHOD__ . ' [END]');
	}

	public static function downloadByBrowser($year = null)
	{
		$baseUrl = self::BASE_URL;
		$downloadUrl = self::DOWNLOAD_URL;

		// 環境変数から取得するように要修正
		$shareDir = config('app.CHROMIUM_DOWNLOAD_DIR');

        $prefectureDir = $shareDir . DIRECTORY_SEPARATOR . self::$prefectureSelect;

		// ダウンロードディレクトリ
		$downloadDir = $prefectureDir
			. DIRECTORY_SEPARATOR . $year;

		Log::debug(__LINE__ . ' [download dir]' . $downloadDir);

		if (! file_exists($downloadDir)) {
			mkdir($downloadDir, 0777, true);
        }

        // ダウンロードディレクトリのパーミッションを緩くする
        if (! chmod($downloadDir, 0777)) {
            Log::debug(__LINE__ . ' ' . __METHOD__ . ' ' . $downloadDir . ' [failure chmod]');
        }

        // 都道府県ディレクトリのパーミッションを緩くする
        if (! chmod($prefectureDir, 0777)) {
            Log::debug(__LINE__ . ' ' . __METHOD__ . ' ' . $prefectureDir. ' [failure chmod]');
        }

        if (! is_writable($downloadDir)) {
            Log::error(__LINE__ . ' ' . __METHOD__ . ' ' . $downloadDir . ' is not writable.');
            exit();
        }

		$options = new ChromeOptions();

		$options->addArguments([
//			'--headless',
//			'--disable-gpu',
			'--no-sandbox',
			'--lang=ja',
			'--url-base=wd/hd',
		]);

		$options->setExperimentalOption('prefs', [
			'download.prompt_for_download' => false,
			'download.default_directory' => $downloadDir,
            'download.directory_upgrade' => true,
		]);

        // メモリ不足になることが増えたので、解像度はあまり上げないようにする
		$dimension = new WebDriverDimension(1600, 1200);
		$capabilities = DesiredCapabilities::chrome();
		$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
		$driver = RemoteWebDriver::create($baseUrl, $capabilities);

		// 年の指定が会った場合

		// 開始年月日
		$yearStart = $year ?? date('Y');
		$monthStart = 1;
		$dayStart = 1;

		// 最終の年月日の設定
		if ($year == date('Y')) {
			// 今年を取得するときは、前日分の情報しか取得できない対応
			$dateEndObj = new Carbon(date('Y-m-d'));
			$yearEnd = (int)$dateEndObj->format('Y');
			$monthEnd = (int)$dateEndObj->format('m');
			$dayEnd = (int)$dateEndObj->addDay(-1)->format('d');
		} else {
			$yearEnd = $year;
			$monthEnd = 12;
			$dayEnd = 31;
		}

		Log::debug(__LINE__ . ' ' . __METHOD__
			. ' [year] ' . $year
			. ' [start] ' . $yearStart . ' ' . $monthStart . ' ' . $dayStart
			. ' [end] ' . $yearEnd . ' ' . $monthEnd . ' ' . $dayEnd
		);

		try {
			$driver->manage()->window()->setSize($dimension);

			$driver->get($downloadUrl);
			$driver->wait(10, 2000);

			// 「選択地点・項目をクリア」をクリックして選択をすべて解除する
			$driver->wait()->until(
				WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id('buttonDelAll'))
			)->click();
			$driver->wait(10, 2000);

			// 地点選択がクリック可能だったらクリックする
			$elementId = 'stationButton';
			$driver->wait()->until(
				WebDriverExpectedCondition::presenceofElementLocated(WebDriverBy::Id($elementId))
			)->click();

			$driver->wait(10, 2000);

			$elementId = 'pr' . self::$prefectureSelect;
			$driver->wait()->until(
				 WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::id($elementId))
			)->click();
			$driver->wait(10, 2000);

			// 地図のブロックが表示のあとに処理する
			$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(
				WebDriverBy::xpath('//*[@id="stationMap"]/img')
			));

			$stations = $driver->findElements(WebDriverBy::xpath('//*[@class=\'station\']'));
			foreach ($stations as $station) {
				$stationName = $station->findElement(WebDriverBy::name('stname'))->getAttribute('value');
				if (in_array($stationName, self::$stationSelects)) {
					$station->click();
				}
			}

			// 「項目を選ぶ」をクリック
			$driver->findElement(WebDriverBy::id('elementButton'))->click();
			$driver->wait(10, 2000);

			// 最高気温
			$xpath = '//input[@name=\'element\' and @value=\'202\']';
			$driver->wait()->until(
				WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::xpath($xpath))
			)->click();

			// 最低気温
			$xpath = '//input[@name=\'element\' and @value=\'203\']';
			$driver->wait()->until(
				WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::xpath($xpath))
			)->click();

			// 期間を選択する
			$driver->findElement(WebDriverBy::id('periodButton'))->click();
			$xpath = '//input[@name=\'interAnnualFlag\' and @value=\'2\']';
			$driver->wait()->until(
				WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::xpath($xpath))
			)->click();

			// 開始月
			$startMonth = $driver->findElements(WebDriverBy::name('inim'));
			$select = new WebDriverSelect($startMonth[1]);
			$select->selectByValue($monthStart);

			// 開始日
			$startDay = $driver->findElements(WebDriverBy::name('inid'));
			$select = new WebDriverSelect($startDay[1]);
			$select->selectByValue($dayStart);

			// 最終月
			$endMonth = $driver->findElements(WebDriverBy::name('endm'));
			$select = new WebDriverSelect($endMonth[1]);
			$select->selectByValue($monthEnd);

			// 最終日
			$endDay = $driver->findElements(WebDriverBy::name('endd'));
			$select = new WebDriverSelect($endDay[1]);
			$select->selectByValue($dayEnd);

			// 開始年
			$startYear = $driver->findElements(WebDriverBy::name('iniy'));
			$select = new WebDriverSelect($startYear[1]);
			$select->selectByValue($yearStart);

			// 最終年（開始年と同じで良い）
			$startYear = $driver->findElements(WebDriverBy::name('iniy'));
			$select = new WebDriverSelect($startYear[1]);
			$select->selectByValue($yearEnd);

			// ローカルストレージの確認
			$driver->executeScript('return localStorage.getItem(\'obsdl_stationList\');');

			// ダウンロードボタン
			$driver->findElement(WebDriverBy::id('csvdl'))->click();

            sleep(3);
			$driver->wait(5, 10);

			$body = $driver->findElement(WebDriverBy::tagName('body'))->getText();

			$searchString = 'ただいまアクセスが集中しています。';
			if (mb_strstr($searchString, $body)) {
				// 時間をおいても、ダウンロードのエラーか動作しないとき

				// ブラウザーでダウンロードの再開回数
				self::$browserDownloadRetry++;

				if (self::$browserDownloadRetry <= self::$browserDownloadRetryLimit) {

					// だめっぽいときは再度ダウンロードしてみる
					$driver->quit();

					sleep(3);
					Log::info(__LINE__ . ' ' . __METHOD__ . ' [retry] ' . self::$browserDownloadRetry);
					self::downloadByBrowser($year);
					return null;
				} else {
					// 制限回数を超えたら終了する
					Log::info(__LINE__ . ' '. __METHOD__ . ' [download failure]');
					return null;
				}
			}

			sleep(3);

			$driver->quit();
			Log::debug(__LINE__ . ' ' . __METHOD__ . ' [finish]');

		} catch (UnexpectedResponseException|UnrecognizedExceptionException|WebDriverException|Exception $e) {
			Log::error($e->getMessage() . ' [line] ' . $e->getLine());
			Log::debug(__LINE__ . ' ' . __METHOD__ . ' ' . $e->getMessage());

		} finally {
			// 最後にブラウザを閉じる
			$driver->quit();
		}
	}

	public static function fileName($line)
	{
		$dd = (new Carbon)->format('Ymd-His');
		return storage_path('app/download/') . $dd . '-' . $line . '.png';
	}

	public static function ImportCsv()
	{
		Log::info(__METHOD__ . ' [start]');
		$csvDir = config('app.CHROMIUM_DOWNLOAD_DIR');

		// スキップするディレクトリ一覧のオブジェクト
		$notDir = ['.', '..', '.DS_Store', '.gitignore'];

		if ($handle = opendir($csvDir)) {
			while (false !== ($prefectureId = readdir($handle))) {
				if (! in_array($prefectureId, $notDir, true)) {
					$dir2 = $csvDir . DIRECTORY_SEPARATOR . $prefectureId;
					if ($handle2 = opendir($dir2)) {
						while (false !== ($year = readdir($handle2))) {
							if (! in_array($year, $notDir, true)) {
								$dir3 = $dir2 . DIRECTORY_SEPARATOR . $year;
								if ($handle3 = opendir($dir3)) {
									while (false !== ($dataFile = readdir($handle3))) {
										if (! in_array($dataFile, $notDir, true)) {
											$filePath = $dir3 . DIRECTORY_SEPARATOR . $dataFile;
											self::importWeatherData($filePath, (integer)$prefectureId);
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	public static function importWeatherData($filePath, int $prefectureId)
	{

		Log::info(__METHOD__ . ' [filePath] ' . $filePath);
		if (($handle = fopen($filePath, 'r')) !== false) {

			$stationNames = [];
			$inserts = [];
			$n = 0;
			while (($buffer = fgetcsv($handle, 4096, ',')) !== false) {

				$decoded = mb_convert_encoding($buffer, 'UTF-8', 'SJIS');

				// 地点名
				if ($n === 2) {
					$stationNames[0] = $decoded[1];
					if (isset($decoded[7])) {
						$stationNames[1] = $decoded[7];
					}
				}

				usleep(10000);
				if ($n > 5) {

					// 品質番号
					if ((integer)$decoded[2] === 8 && (integer)$decoded[5] === 8) {
						$date = (new Carbon($decoded[0]))->format('Y-m-d');
						$inserts[] = [
							'date' => $date,
							'prefecture_id' => $prefectureId,
							'station_name' =>  $stationNames[0],
							'temperature_highest' => $decoded[1],
							'temperature_lowest' => $decoded[4],
						];
					}

					if (isset($decoded[7]) &&
						(integer)$decoded[8] === 8 && (integer)$decoded[11] === 8
						&& ! empty($decoded[7]) && ! empty($decoded[11])
					) {
						$date = (new Carbon($decoded[0]))->format('Y-m-d');
						$inserts[] = [
							'date' => $date,
							'prefecture_id' => $prefectureId,
							'station_name' =>  $stationNames[1],
							'temperature_highest' => $decoded[7],
							'temperature_lowest' => $decoded[10],
						];
					}

					if (! empty($inserts)) {
						// upsert
						WeatherDaily::upsert($inserts,
							[
								'date',
								'prefecture_id',
								'station_name'
							],
							[
								'temperature_highest',
								'temperature_lowest',
							]
						);
					}
				}

				$n++;
			}
		}
	}

	public static function getWeathers(array $params)
	{
		$prefecture_id = $params['prefecture_id'];

		$results = [];

		$weathers = WeatherDaily::where('prefecture_id', $prefecture_id)
			->where('station_name', $params['station'])
			->whereBetween('date', [$params['dateStart'], $params['dateEnd']])
			->orderBy('date')
			->get();

		// 日付をキーに変換
		$weatherByDates = [];
		foreach ($weathers as $weather) {
			$weatherByDates[$weather['date']] = $weather;
		}

		$format = 'Y-m-d';
		$date = new Carbon($params['dateStart']);
		$dateCurrent = $date->format($format);
		if (count($weatherByDates) > 1) {
			$dateEnd = max(array_keys($weatherByDates));

			$n = 0;
			// 日付範囲を全て返す
			while ($dateCurrent <= $dateEnd) {

				$dateCurrent = $date->addDays(1)->format($format);

				$results[] = $weatherByDates[$dateCurrent] ?? ['date' => $dateCurrent];

				$memoryUsage = memory_get_usage();

				if ($n % 100 === 1) {
					Log::debug(__LINE__. ' [memory usage]' . number_format($memoryUsage));
				}
				$n++;
			}
		}

		return $results;
	}
}
