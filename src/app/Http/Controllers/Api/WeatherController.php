<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WeatherDaily;
use App\Services\WeatherService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    //

	public function __construct()
	{

	}

	public function get(Request $request)
	{
		$prefectureId = $request->input('prefectureId');
		$station = $request->input('station');

		$dateStart = preg_match('/^(\d{4})(\d{2})(\d{2})$/', $request->input('startDate'), $match1)
			? $match1[1] . '-' . $match1[2] . '-' . $match1[3]
			: null;

		$dateEnd = preg_match('/^(\d{4})(\d{2})(\d{2})$/', $request->input('endDate'), $match2)
			? $match2[1] . '-' . $match2[2] . '-' . $match2[3]
			: null;

		$params = [
			'prefecture_id' => $request->input('prefectureId'),
			'station' => $request->input('station'),
			'dateStart' => $dateStart,
			'dateEnd' => $dateEnd,
		];
		$weathers = WeatherService::getWeathers($params);

		$count = count($weathers);

		$result = [
			'search' => [
				'startDate' => $dateStart,
				'endDate' => $dateEnd,
				'prefecture' => $prefectureId,
				'station' => $station,
			],
			'data' => $weathers,
			'count' => $count,
		];

		return response()->json($result)->header('Access-Control-Allow-Origin', '*');
	}
}
