<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\RakutenApiService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RakutenController extends Controller
{
    // 楽天APIのアプリケーションID
    protected string $applicationId;

    public function __construct()
    {
        $this->applicationId = config('app.RAKUTEN_APPLICATION_ID');
    }

    /**
     * @return View|\Illuminate\Foundation\Application|Factory|Application
     * @throws Exception
     */
    public function getAreas(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $json = RakutenApiService::getAreasJson();

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
     * @return null
     */
    public function getSmall(string $middle, string $small)
    {
        return $this->getHotelMulti(['middle' => $middle, 'small' => $small]);
    }

    /**
     * Detailでホテル一覧を返す
     *
     * @param string $middle
     * @param string $small
     * @param string $detail
     * @return Application|Factory|\Illuminate\Foundation\Application|View
     */
    public function getDetail(string $middle, string $small, string $detail)
    {
        $params = [
            'middle' => $middle,
            'small' => $small,
            'detail' => $detail,
        ];

        $result = [
            'hotels' => RakutenApiService::getArea($params)
        ];

        return view('rakuten.hotels', $result);

    }

    /**
     * エリアを取得
     *
     * @param array $params
     * @return Factory|\Illuminate\Foundation\Application|View|Application
     */
    public function getHotelMulti(array $params): Factory|\Illuminate\Foundation\Application|View|Application
    {
        $result = [
            'hotels' => RakutenApiService::getArea($params)
        ];

        return view('rakuten.hotels', $result);
    }

    /**
     * @param string $hotel
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function hotelDetail(int $hotelNo)
    {
        $result = RakutenApiService::getHotel($hotelNo);
        return view('rakuten.hotelDetail', $result);
    }

    public function vueArea()
    {
        return view('rakuten.vue');
    }
}
