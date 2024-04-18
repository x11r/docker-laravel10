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
     * @return array|null
     */
    public function getDetail(string $middle, string $small, string $detail): array|null
    {
        return $this->getHotelMulti(['middle' => $middle, 'small' => $small, 'detail' => $detail]);
    }

    /**
     * エリアを取得
     *
     * @param array $params
     * @return array|null
     */
    public function getHotelMulti(array $params) :array|null
    {
        // キャッシュから取得
        $cacheKey = __METHOD__ . ' ' . json_encode($params);

        $cacheExpire = 60 * 60 * 24 * 3;

        try {
            if (Cache::has($cacheKey)) {
                Log::debug(__LINE__ . ' ' . __METHOD__. ' use cache ');
                $json = Cache::get($cacheKey);
            } else {
                $json = Cache::remember($cacheKey, $cacheExpire, function () use ($params) {

                    $response = RakutenApiService::getArea($params);
                    $body = $response->body();
                    $json = json_decode($body, true);
                    return $json;
                });

                Cache::put($cacheKey, $json, $cacheExpire);
            }

            $params = [
                'hotels' => $json,
            ];
        } catch (Exception $e) {
            Log::debug($e->getLine() . ' ' . $e->getMessage());
        }

        return view('rakuten.hotels', $params);
    }

    /**
     * @param string $hotel
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function hotelDetail(int $hotelNo)
    {

        $cacheKey = __METHOD__ . ' ' . $hotelNo;
        $cacheExpire = 60 * 60 * 24 * 1;
        try {
            if (Cache::has($cacheKey)) {
                $json = Cache::get($cacheKey);
                Log::debug(__LINE__ . ' ' . __METHOD__ . ' use cache');
            } else {
                $response = RakutenApiService::getHotel((int)$hotelNo);
                $body = $response->body();
                $status = $response->status();

                if ($status != 200) {
                    throw new Exception('情報取得できませんでした。');
                }

                Log::debug(__LINE__ . ' ' . __METHOD__ . ' no cache requesting');

                // JSONを配列に変換
                $json = json_decode($body);

                Cache::put($cacheKey, $json, $cacheExpire);
            }

            $params = [
                'hotels' => $json->hotels,
            ];
        } catch (Exception $e) {
            echo $e->getMessage();

            session()->flush();
            exit();
        }

        return view('rakuten.hotelDetail', $params);
    }

    public function vueArea()
    {
        return view('rakuten.vue');
    }
}
