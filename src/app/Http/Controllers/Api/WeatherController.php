<?php
declare(strict_types=1);
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __construct(
		private Request $request
    )
    {
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get(): JsonResponse
    {
        $prefectureId = $this->request->input('prefectureId');
        $station = $this->request->input('station');

        $dateStart = preg_match('/^(\d{4})(\d{2})(\d{2})$/', $this->request->input('startDate'), $match1)
            ? $match1[1] . '-' . $match1[2] . '-' . $match1[3]
            : null;

        $dateEnd = preg_match('/^(\d{4})(\d{2})(\d{2})$/', $this->request->input('endDate'), $match2)
            ? $match2[1] . '-' . $match2[2] . '-' . $match2[3]
            : null;

        $params = [
            'prefecture_id' => $this->request->input('prefectureId'),
            'station' => $this->request->input('station'),
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

        return response()->json($result)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET')
            ->header('Access-Control-Allow-Headers', 'Accept, X-Requested-With, Origin, Content-Type');
    }

	public function getConstants()
	{
		$results = WeatherService::getConstants();

		return response()->json($results)
			->header('Access-Controll-Allow-Origin', '*')
			->header('Access-Control-Allow-Methods', 'GET')
			->header('Access-Control-Allow-Headers', 'Accept, X-Requested-With, Origin, Content-Type');
	}

}
