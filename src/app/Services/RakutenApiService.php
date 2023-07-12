<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class RakutenApiService
{

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
