<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\RakutenApiService;
use Exception;
use Illuminate\Contracts\View\View;

class RakutenController extends Controller
{
    public function __construct(
        private RakutenApiService $rakutenApiService
    ) {
    }

    /**
     * @return View
     * @throws Exception
     */
    public function getAreas(): View
    {
        $json = $this->rakutenApiService->getAreasJson();

        $params = [
            'areas' => $json,
        ];

        return view('rakuten.area', $params);
    }

    /**
     * Smallでホテル一覧を返却する
     *
     * @param string $middle
     * @param string $small
     * @return View
     */
    public function getSmall(string $middle, string $small): View
    {
        return $this->getHotelMulti(['middle' => $middle, 'small' => $small]);
    }

    /**
     * Detailでホテル一覧を返す
     *
     * @param string $middle
     * @param string $small
     * @param string $detail
     * @return View
     */
    public function getDetail(string $middle, string $small, string $detail): View
    {
        $params = [
            'middle' => $middle,
            'small' => $small,
            'detail' => $detail,
        ];

        $result = [
            'hotels' => $this->rakutenApiService->getArea($params)
        ];

        return view('rakuten.hotels', $result);
    }

    /**
     * エリアを取得
     *
     * @param array $params
     * @return View
     */
    public function getHotelMulti(array $params): View
    {
        $result = [
            'hotels' => $this->rakutenApiService->getArea($params)
        ];

        return view('rakuten.hotels', $result);
    }

    /**
     * ホテル情報を表示
     * @param int $hotelNo
     * @return View
     */
    public function hotelDetail(int $hotelNo): View
    {
        $result = $this->rakutenApiService->getHotel($hotelNo);
        return view('rakuten.hotelDetail', $result);
    }

    /**
     * @return View
     */
    public function vueArea(): View
    {
        return view('rakuten.vue');
    }
}
