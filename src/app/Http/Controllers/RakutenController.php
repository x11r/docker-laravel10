<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RakutenController extends Controller
{

    // 楽天APIのアプリケーションID
    protected string $applicationId;

    public function __construct()
    {

        $this->applicationId = config('app.RAKUTEN_APPLICATION_ID');
    }

    public function getAreas()
    {
        $url = 'https://app.rakuten.co.jp/services/api/Travel/GetAreaClass/20131024?'
            . 'format=json&applicationId=' . $this->applicationId;

        // キャッシュから取得
        $cacheKey = __METHOD__;

        $cacheExpire = 60 * 60 * 24 * 3;
        $json = Cache::remember($cacheKey, $cacheExpire, function() use ($url) {
            $response = Http::get($url);
            $body = $response->body();
            $json = json_decode($body, true);
            return $json;
        });

        $params = [
            'areas' => $json,
        ];

        return view('rakuten.area', $params);
    }


    public function getSmall(string $middle, string $small)
    {
        return $this->getHotelMulti(['middle' => $middle, 'small' => $small]);
    }

    public function getDetail(string $middle, string $small, string $detail)
    {
        return $this->getHotelMulti(['middle' => $middle, 'small' => $small, 'detail' => $detail]);
    }

    /**
     * エリアを取得
     *
     * @param string $large
     * @param string $middle
     * @param string $small
     * @param string $detail
     * @return void
     */
    public function getHotelMulti(array $params)
    {
        $url = 'https://app.rakuten.co.jp/services/api/Travel/SimpleHotelSearch/20170426?'
            . 'format=json'
            . '&applicationId=' . $this->applicationId;

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

        // キャッシュから取得
        $cacheKey = __METHOD__ . ' ' . $url;

        $cacheExpire = 60 * 60 * 24 * 3;
        $json = Cache::remember($cacheKey, $cacheExpire, function() use ($url) {
            $response = Http::get($url);
            $body = $response->body();
            $json = json_decode($body, true);
            return $json;
        });

        $params = [
            'hotels' => $json,
        ];

        return view('rakuten.hotels', $params);
    }

    /**
     * @param string $hotel
     * @return Application|Factory|View|\Illuminate\Foundation\Application
     */
    public function hotelDetail(string $hotel)
    {
        $url = 'https://app.rakuten.co.jp/services/api/Travel/VacantHotelSearch/20170426?format=json'
            . '&applicationId=' . $this->applicationId
            . '&hotelNo=' . $hotel;

        try {

            $response = Http::get($url);
            $body = $response->body();
            $status = $response->status();

            if ($status != 200) {
                throw new Exception('情報取得できませんでした。');
            }

            // JSONを配列に変換
            $json = json_decode($body, true);
            
            $params = [
                'hotel' => $json,
            ];

        } catch (Exception $e) {
            echo $e->getMessage();

            session()->flush();
            exit();
        }

        return view('rakuten.hotelDetail', $params);
    }
}
