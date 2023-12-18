<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Exception;
use Facebook\WebDriver\Exception\Internal\UnexpectedResponseException;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Illuminate\Support\Facades\Log;

class WeatherService
{

	// SeleniumのURL
	const BASE_URL = 'http://localhost:4444/wd/hub';

	// ダウンロードURL
	const DOWNLOAD_URL = 'https://www.data.jma.go.jp/gmd/risk/obsdl/';

	/**
	 * 気象庁からCSVをダウンロードしてDBに入れる
	 *
	 * @return void
	 */
	public static function downloadWeatherCsv(): void
	{

		self::downloadTokyo();
	}

	public static function downloadTokyo()
	{
		// 東京のCSVをダウンロードする

		$baseUrl = self::BASE_URL;
		$downloadUrl = self::DOWNLOAD_URL;

		// ダウンロードディレクトリ
		$downloadDir = storage_path('app/download');

		// 画面キャプチャー保存ファイルパス
		$filePath = $downloadDir . DIRECTORY_SEPARATOR . __LINE__  . '.png';

		$options = new ChromeOptions();

		$options->addArguments([
//			'--headless',
//			'--disable-gpu',
//			'--no-sandbox',
			'--lang=ja',
			'--url-base=wd/hd',
		]);

		$options->setExperimentalOption('prefs', [
			'download.prompt_for_download' => false,
			'download.default_directory' => $downloadDir,
		]);

		try {
			$capabilities = DesiredCapabilities::chrome();
			$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
			$driver = RemoteWebDriver::create($baseUrl, $capabilities);
			$driver->get($downloadUrl);
			$driver->wait(10, 1000);

			// 都道府県から東京をクリック
			$driver->findElement(WebDriverBy::cssSelector('#pr44'))->click();
			$driver->wait(10, 1000);

			$action = new WebDriverActions();
			$driver->takeScreenshot(self::fileName(__LINE__));


			Log::debug(__LINE__ . ' ' . __METHOD__ . ' [save path] ' . $filePath);
			$driver->quit();

		} catch (UnexpectedResponseException $e) {

			Log::error($e->getMessage() . ' [line] ' . $e->getLine());
			Log::debug(__LINE__ . ' ' . __METHOD__ . ' ' . $e->getMessage());
		} catch (WebDriverException $e) {
			Log::error($e->getMessage() . ' [line] ' . $e->getLine());
			Log::debug(__LINE__ . ' ' . __METHOD__ . ' ' . $e->getMessage());
		}
	}

	public static function fileName($line)
	{
		$dd = (new Carbon)->format('Ymd-His');
		return __CLASS__ . '-' . $dd . '-' . $line;
	}



}
