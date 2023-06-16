<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RakutenController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        $applicationId = config('app.RAKUTEN_APPLICATION_ID');

        $url = 'https://app.rakuten.co.jp/services/api/Travel/GetAreaClass/20131024?'
            . 'format=json&applicationId=' . $applicationId;

        // キャッシュから取得
        $cacheKey = __METHOD__;
        $cacheExpire = 60 * 60 * 24 * 3;
        $jsonArray = Cache::remember($cacheKey, $cacheExpire, function() use ($url) {
            $response = Http::get($url);
            $body = $response->body();
            $json = json_decode($body, true);
            return $json;
        });

        $params = [
            'areas_array' => $jsonArray,
        ];

        return view('admin.rakuten.index', $params);
    }

    public function areaMulti(string $middle, string $small, string $detail = '')
    {
        $params = [
            'middle' => $middle,
            'small' => $small,
        ];

        if ($detail != '') {
            $params['detail'] = $detail;
        }

        $hotels = $this->getHotels($params);

        return view('admin.rakuten.area', ['hotels' => $hotels]);
    }

    public function hotelDetail($hotelId)
    {

        $applicationId = config('app.RAKUTEN_APPLICATION_ID');

        $params = [];

        $url = 'https://app.rakuten.co.jp/services/api/Travel/VacantHotelSearch/20170426?format=json'
            . '&applicationId=' . $applicationId
            . '&hotelNo=' . $hotelId;

        Log::debug(__LINE__ . ' ' .  __METHOD__ . ' [url] ' . $url);
        // キャッシュ設定
        $cacheKey = __METHOD__ . ' ' . $hotelId;
        $cacheExpire = 60 * 60 * 24 * 5;

        try {

            $response = Http::get($url);
            $body = $response->body();
            $json = json_decode($body, true);

            $hotel = $json;

            $params = [
                'hotel' => $hotel,
            ];

        } catch (Exception|HandleExceptions $e) {
            Log::debug(__LINE__ . ' ' . __METHOD__ . ' ' . $e->getMessage());

            session()->flush();
        }

        dd($params['hotel']);

        return view('admin.rakuten.hotelDetail', $params);
    }

    private function getHotels($params)
    {
        $applicationId = config('app.RAKUTEN_APPLICATION_ID');

        $url = 'https://app.rakuten.co.jp/services/api/Travel/SimpleHotelSearch/20170426?format=json&applicationId=' . $applicationId;

        $url .= '&largeClassCode=japan';

        if (! empty($params['middle'])) {
            $url .= '&middleClassCode=' . $params['middle'];
        }
        if (! empty($params['small'])) {
            $url .= '&smallClassCode=' . $params['small'];
        }
        if (! empty($params['detail'])) {
            $url .= '&detailClassCode=' . $params['detail'];
        }

        $cacheKey = __METHOD__ . ' ' . $url;

        if (Cache::has($cacheKey)) {
            Log::debug(__LINE__ . ' ' . __METHOD__ . ' cache exists');
        } else {
            Log::debug(__LINE__ . ' ' . __METHOD__ . ' cache none');
        }

        $cacheExpire = 60 * 60 * 24 * 3;
        $json = Cache::remember($cacheKey, $cacheExpire, function () use ($url) {
            $response = Http::get($url);
            $body = $response->body();
            return json_decode($body, true);
        });

        return $json;
    }

    private function vacantHotel(array $params)
    {
        $applicationId = config('app.RAKUTEN_APPLICATION_ID');
        $url = 'https://app.rakuten.co.jp/services/api/Travel/VacantHotelSearch/20170426?format=json';
        $url .= '&applicationId=' . $applicationId;
        $url .= '&largeClassCode=japan';

        if (isset($params['middle'])) {
            $url .= '&middleClassCode=' . $params['middle'];
        }
        if (isset($params['small'])) {
            $url .= '&smallClassCode=' . $params['small'];
        }

        // キャッシュ設定
        $cacheKey = __METHOD__ . ' ' . $url;
        $cacheExpire = 60 * 60 * 24;
        $json = Cache::remember($cacheKey, $cacheExpire, function () use ($url) {
            $response = Http::get($url);
            $body = $response->body();
            return json_decode($body, true);
        });

        if (isset($json->hotels)) {
//            Log::debug(__LINE__);
            foreach ($json->hotels as $hotel) {
                Log::debug(__LINE__ . ' ' . print_r($hotel->hotel, true));
                Log::debug(__LINE__
                    . ' [hotelNo] ' . $hotel->hotel[0]->hotelBasicInfo->hotelNo
                    . ' [hotelName] ' . $hotel->hotel[0]->hotelBasicInfo->hotelName
                );
//                Log::debug(__LINE__ . ' ' . __METHOD__ . ' [hotelNo] ' . $hotel->hotelBasicInfo->);
            }
        }

    }

    public function hotelRanking()
    {
        $applicationId = config('app.RAKUTEN_APPLICATION_ID');
        $url = 'https://app.rakuten.co.jp/services/api/Travel/HotelRanking/20170426?format=json';
        $url .= '&applicationId=' . $applicationId;

        $cacheKey = __METHOD__ . ' ' . $url;
        $cacheExpire = 60 * 60 * 24;

        $json = Cache::remember($cacheKey, $cacheExpire, function () use ($url) {
            Log::debug(__LINE__ . ' ' . __METHOD__ . ' ' . $url);
            $response = Http::get($url);
            $body = $response->body();
            return json_decode($body);
        });
    }
}
