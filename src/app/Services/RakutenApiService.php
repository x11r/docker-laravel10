<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RakutenApiService
{
    protected int $cacheExpire = 0;
    protected string $applicationId = '';

    /**
     * エリア情報取得URL
     * @var string
     */
    protected string $getAreaUrl = 'https://app.rakuten.co.jp/services/api/Travel/GetAreaClass/20131024?'
    . 'format=json&applicationId=';

    protected string $getHotelUrl = 'https://app.rakuten.co.jp/services/api/Travel/SimpleHotelSearch/20170426?'
        . 'format=json&applicationId=';

    public function __construct(
        private readonly Cache $cache,
        private readonly Http $http
    ) {
        $this->cacheExpire = 60 * 60 * 24;
        $this->applicationId = (string)config('app.RAKUTEN_APPLICATION_ID');
        $this->getAreaUrl .= $this->applicationId;
        $this->getHotelUrl .= $this->applicationId;
    }

    /**
     * @return string
     */
    public function getApplicationId(): string
    {
        return $this->applicationId;
    }

    /**
     * 取得したエリアをキャッシュを利用する
     * @return mixed
     * @throws Exception
     */
    public function getAreasJson(): mixed
    {
        $cacheExpire = 60 * 60 * 24 * 3;
        $cacheKey = __METHOD__;

        if ($this->cache::has($cacheKey)) {
            // キャッシュから取得
            $body = $this->cache::get($cacheKey);
        } else {
            $response = $this->getAreas();

            $status = $response->status();

            if ($status !== 200) {
                return throw new exception('error (' . $status . ')');
            }

            $body = $response->body();

            $this->cache::put($cacheKey . $body, $cacheExpire);
        }

        return json_decode($body);
    }

    /**
     * 楽天APIのエリア情報を取得する
     *
     * @return Response
     */
    public function getAreas(): Response
    {
        $url = $this->getAreaUrl;

        return $this->http::get($url);
    }

    /**
     * エリアを絞り込んでホテル一覧を返却する
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getArea(array $params): array
    {
        $url = $this->getHotelUrl;

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

        $cacheKey = __METHOD__ . ' ' . serialize($params);

        $this->cache::forget($cacheKey);

        if ($this->cache::has($cacheKey)) {
            // キャッシュがあったらキャッシュを返す
            $body = $this->cache::get($cacheKey);
        } else {
            $response = $this->http::get($url);
            $status = $response->status();
            if ($status !== 200) {
                throw new Exception('情報を取得できませんでした');
            }

            $body = $response->body();
            $this->cache::put($cacheKey, $body, $this->cacheExpire);
        }

        $json = json_decode($body, true);

        return $json;
    }

    /**
     * ホテル情報を返す
     *
     * @param int $hotelNo
     * @return array
     */
    public function getHotel(int $hotelNo): array
    {
        $applicationId = $this->applicationId;

        $url = 'https://app.rakuten.co.jp/services/api/Travel/VacantHotelSearch/20170426?format=json'
            . '&applicationId=' . $applicationId
            . '&hotelNo=' . $hotelNo;

//        $cacheKey = __METHOD__ . ' ' . $hotelNo;
//        $cacheExpire = 60 * 60 * 24 * 1;
        try {
//            if (Cache::has($cacheKey)) {
//                $response = Cache::get($cacheKey);
//                Log::debug(__LINE__ . ' ' . __METHOD__ . ' use cache');
//            } else {
//                $response = Http::get($url);
//
//                Log::debug(__LINE__ . ' ' . __METHOD__ . ' no cache');
//
////                Cache::put($cacheKey, $response, $cacheExpire);
//            }

            // 強制取得
            $response = $this->http::get($url);

            $body = $response->body();
            $status = $response->status();

            if ($status !== 200) {
                throw new Exception('情報取得できませんでした。');
            }

            $json = json_decode($body);

            $result = [
                'hotels' => $json->hotels,
            ];
        } catch (Exception $e) {
            echo $e->getMessage();

            session()->flush();
            exit();
        }

        return $result;
    }
}
