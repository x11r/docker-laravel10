<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RakutenController extends Controller
{
    //

    public function __construct()
    {
        //

    }

    public function index()
    {
        $applicationId = config('app.RAKUTEN_APPLICATION_ID');

        $url = 'https://app.rakuten.co.jp/services/api/Travel/GetAreaClass/20131024?'
            . 'format=json&applicationId=' . $applicationId;

        $response = Http::get($url);
        $body = $response->body();
//        $areas = json_decode($body, true);

        $params = [
            'areas' => json_decode($body),
            'areas_array' => json_decode($body, true),
        ];

        return view('admin.rakuten.area', $params);
    }

//    public function area()
//    {
//        dd(__LINE__);
//    }

    public function areaMiddle($middle)
    {
        $params = ['middle' => $middle];

        $body = $this->getHotel($params);
        $json = json_decode($body, true);

        $params = [
            'hotels' => $json,
        ];

        return view('admin.rakuten.hotel', $params);
    }

    public function areaSmall($middle, $small)
    {
        $params = ['middle' => $middle, 'small' => $small];
        $body = $this->getHotel($params);
        $json = json_decode($body, true);

        $params = [
            'hotels' => $json,
        ];

        return view('admin.rakuten.hotel', $params);
    }

    private function getHotel($params)
    {
        $applicationId = config('app.RAKUTEN_APPLICATION_ID');

        $url = 'https://app.rakuten.co.jp/services/api/Travel/SimpleHotelSearch/20170426?format=json&applicationId=' . $applicationId;

        $url .= '&largeClassCode=japan';

        if (isset($params['middle'])) {
            $url .= '&middleClassCode=' . $params['middle'];
        }
        if (isset($params['small'])) {
            $url .= '&smallClassCode=' . $params['small'];
        }

//        try {
        Log::debug(__LINE__ . ' ' . __METHOD__ . ' [url] ' . $url);
            $response = Http::get($url);

//        dd($response->status());

            $body = $response->body();

            return $body;
//        } catch (Exception $e) {

//        }
    }
}
