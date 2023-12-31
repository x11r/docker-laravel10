<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\WeatherDaily;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WeatherImportService
{

	public static function ImportCsv()
	{
		$csvDir = config('app.CHROMIUM_DOWNLOAD_DIR');

		// スキップするディレクト一覧のオブジェクト
		$notDir = ['.', '..', '.DS_Store'];

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
							'temperature_lowest' => $decoded[11],
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
				if ($n > 100) {
					break;
				}

				$n++;
			}
		}
	}
}
