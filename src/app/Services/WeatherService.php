<?php

declare(strict_types=1);

namespace App\Services;

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
	const BASE_URL = 'http://localhost:4444/wd/hub';

	// ダウンロードURL
	const DOWNLOAD_URL = 'https://www.data.jma.go.jp/gmd/risk/obsdl/index.php';

	// 都道府県
	private static int $prefectureSelect = 44;

	// 地点
	private static array $stationSelects = ['東京', '八王子'];

	// 選択年
	private static int $year = 1885;

	/**
	 * 気象庁からCSVをダウンロードしてDBに入れる
	 *
	 * @return void
	 */
	public static function downloadWeatherCsv(): void
	{
		$prefectures = [
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

		foreach ($prefectures as $prefecture) {

			$yearTarget = self::$year;
			$yearCurrent = (new Carbon)->format('Y');

			self::$prefectureSelect = $prefecture['prefecture_id'];
			self::$stationSelects = $prefecture['station'];

			while ($yearTarget <= $yearCurrent) {

				Log::debug(__LINE__
					. ' [prefecture id] ' . self::$prefectureSelect
					. ' [station] ' . print_r(self::$stationSelects, true)
					. ' [year] ' . $yearTarget
				);
				self::$year = $yearTarget;

				// ダウンロード
				self::downloadByBrowser();

				$yearTarget++;
				sleep(3);
			}
		}
	}

	public static function downloadByBrowser()
	{
		// 東京のCSVをダウンロードする

		$baseUrl = self::BASE_URL;
		$downloadUrl = self::DOWNLOAD_URL;

		// 環境変数から取得するように要修正
		$shareDir = config('app.CHROMIUM_DOWNLOAD_DIR');

		// ダウンロードディレクトリ
		$downloadDir = $shareDir
			. DIRECTORY_SEPARATOR . self::$prefectureSelect
			. DIRECTORY_SEPARATOR . self::$year;

		Log::debug(__LINE__ . ' [download dir]' . $downloadDir);

		if (! file_exists($downloadDir)) {
			@mkdir($downloadDir, 0777, true);
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
		]);

		$dimension = new WebDriverDimension(1600, 1400);
		$capabilities = DesiredCapabilities::chrome();
		$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
		$driver = RemoteWebDriver::create($baseUrl, $capabilities);

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

			// 都道府県から東京都をクリック
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
			$select->selectByValue('1');

			// //*[@id="selectPeriod"]/div/div[2]/div[2]/div[1]/select[2]
			// 開始日
			$startDay = $driver->findElements(WebDriverBy::name('inid'));
			$select = new WebDriverSelect($startDay[1]);
			$select->selectByValue('1');

			// 最終月
			$endMonth = $driver->findElements(WebDriverBy::name('endm'));
			$select = new WebDriverSelect($endMonth[1]);
			$select->selectByValue('12');

			// 最終日
			$endDay = $driver->findElements(WebDriverBy::name('endd'));
			$select = new WebDriverSelect($endDay[1]);
			$select->selectByValue('31');

			// 開始年
			$startYear = $driver->findElements(WebDriverBy::name('iniy'));
			$select = new WebDriverSelect($startYear[1]);
			$select->selectByValue(self::$year);

			// 最終年（開始年と同じで良い）
			$startYear = $driver->findElements(WebDriverBy::name('iniy'));
			$select = new WebDriverSelect($startYear[1]);
			$select->selectByValue(self::$year);

			// ローカルストレージの確認
			$localStorage = $driver->executeScript('return localStorage.getItem(\'obsdl_stationList\');');

			// ダウンロードボタン
			$driver->findElement(WebDriverBy::id('csvdl'))->click();
			$driver->wait(5, 10);
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
}
