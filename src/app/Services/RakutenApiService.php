<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RakutenApiService
{

    /**
     * 取得したエリアをキャッシュを利用する
     * @return mixed
     */
    public static function getAreasJson(): mixed
    {

        $cacheExpire = 60 * 60 * 24 * 3;
        $cacheKey = __METHOD__;

        if (Cache::has($cacheKey)) {
            $body = Cache::get($cacheKey);
        } else {
            $response = self::getAreas();

            $status = $response->status();

            if ($status !== 200) {
                return throw new exception('error (' . $status . ')');
            }

            $body = $response->body();
//            Log::debug(__LINE__ . ' '. __METHOD__
//                . ' [key] ' . $cacheKey
//            );

//            Cache::put($cacheKey . $body, $cacheExpire);
        }

        $json = json_decode($body);

        return $json;
    }

	/**
	 * 楽天APIのエリア情報を取得する
     *
	 * @return Response
	 */
	public static function getAreas(): Response
	{
		$applicationId = config('app.RAKUTEN_APPLICATION_ID');

		$url = 'https://app.rakuten.co.jp/services/api/Travel/GetAreaClass/20131024?'
			. 'format=json&applicationId=' . $applicationId;

		return Http::get($url);
	}

    /**
     * エリアを絞り込んでホテル一覧を返却する
     *
     * @param array $params
     * @return Response
     */
    public static function getArea(array $params): Response
    {
        $applicationId = config('app.RAKUTEN_APPLICATION_ID');

        $url = 'https://app.rakuten.co.jp/services/api/Travel/SimpleHotelSearch/20170426?'
            . 'format=json'
            . '&applicationId=' . $applicationId;

        $url .= '&largeClassCode=japan';

        if (isset($params['middle'])) {
            $url .= '&middleClassCode=' . $params['middle'];
        }

        if (isset($params['small'])) {
            $url .= '&smallClassCode=' . $params['small'];
        }

        if (isset($params['detail'])) {
            $url .= '&detailClassCode=' . $params['detail'];
        }

        return Http::get($url);
    }

    public static function getHotel(int $hotelNo)
    {
        $applicationId = config('app.RAKUTEN_APPLICATION_ID');

        $url = 'https://app.rakuten.co.jp/services/api/Travel/VacantHotelSearch/20170426?format=json'
            . '&applicationId=' . $applicationId
            . '&hotelNo=' . $hotelNo;

        return Http::get($url);
    }
}
