<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RakutenApiService;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RakutenController extends Controller
{

	protected string $applicationId;

	/**
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		$this->applicationId = config('app.RAKUTEN_APPLICATION_ID');
		$this->request = $request;
    }

	/**
	 * @return Application|ResponseFactory|\Illuminate\Foundation\Application|JsonResponse|Response
	 */
	public function getAreas(): \Illuminate\Foundation\Application|Response|JsonResponse|Application|ResponseFactory
	{
		// キャッシュ設定
		$cacheKey = __METHOD__;
		$cacheExpire = 60 * 60 * 24 * 3;

		try {
			if (Cache::has($cacheKey)) {
                // キャッシュで取得できたら利用する
				$json = Cache::get($cacheKey);
				Log::debug(__LINE__ . ' ' . __METHOD__ . ' [cache]');

			} else {
                // キャッシュがない場合は、APIで取得する
				$response = RakutenApiService::getAreas();

				$status = $response->status();

				Log::debug(__LINE__ . ' ' . __METHOD__ . ' [request]');

				if ($status != 200) {
					throw new Exception('情報取得できませんでした。(' . $status . ')');
				}

				$body = $response->body();
				$json = json_decode($body);
				Cache::put($cacheKey, $json, $cacheExpire);
			}
		} catch (Exception $e) {
			$json = json_encode($e->getMessage());
			return response($json);
		}

		$params = [
			'areas' => $json,
		];

		return response()->json($params);
	}

	public function getHotels()
	{

		$url = 'https://app.rakuten.co.jp/services/api/Travel/SimpleHotelSearch/20170426?'
			. 'format=json'
			. '&applicationId=' . $this->applicationId;

		$url .= '&largeClassCode=japan';

		if ($this->request->input('middle')) {
			$url .= '&middleClassCode=' . $this->request->input('middle');

		}
		if ($this->request->input('small')) {
			$url .= '&smallClassCode=' . $this->request->input('small');
		}
		if ($this->request->input('detail')) {
			$url .= '&detailClassCode=' . $this->request->input('detail');
		}

//		Log::debug(__LINE__ . ' '. __METHOD__ . ' [url] ' . $url);

		$cacheKey = md5(__METHOD__ . ' ' . $url);

		$cacheExpire = 60 * 60 * 24 * 2;

		// 強制削除 空が保存されてしまう。
		Cache::forget($cacheKey);

		try {

			if (Cache::has($cacheKey)) {
				// キャッシュがあったらキャッシュを利用する
				$json = Cache::get($url);
//				Log::debug(__LINE__ . ' ' . __METHOD__ . ' [cache] ');
			} else {
				$response = Http::get($url);

				$status = $response->status();

				if ($status != 200) {
					throw new Exception('情報取得できませんでした。(' . $status . ')');
				}

				$body = $response->body();
				$json = json_decode($body);
				Cache::put($cacheKey, $json, $cacheExpire);
			}

			return response()->json($json);
		} catch (Exception $e) {
			$message = [
				'message' => $e->getMessage(),
			];
			$json = json_encode($message);
			return response($json);
		}
	}


	public function getHotel($hotel)
	{
		$applicationId = config('app.RAKUTEN_APPLICATION_ID');

		$params = [];

		$url = 'https://app.rakuten.co.jp/services/api/Travel/VacantHotelSearch/20170426?format=json'
			. '&applicationId=' . $applicationId
			. '&hotelNo=' . $hotel;

		// キャッシュ設定
		$cacheKey = __METHOD__ . ' ' . $hotel;
		$cacheExpire = 60 * 60 * 24 * 5;

		try {
			if (Cache::has($cacheKey)) {
				$json = Cache::get($cacheKey);
				Log::debug(__LINE__ . ' ' . __METHOD__ . ' [use cache] ');
			} else {
				Log::debug(__LINE__ . ' ' . __METHOD__ . ' [request] ' );
				$response = Http::get($url);
				$status = $response->status();

				if ($status !== 200) {

					Log::debug(__LINE__ . ' ' . __METHOD__ . ' [status] ' . $status);
					throw new exception('情報取得できませんでした。(' . $status . ')');
				}

				$body = $response->body();

				$json = json_decode($body, true);

				Cache::put($cacheKey, $json, $cacheExpire);
			}

			return response()->json($json);

		} catch (Exception $e) {
			$message = [
				'message' => $e->getMessage(),
				'code' => $e->getCode(),
			];

			$json = json_encode($message);
			return response($json);
		}

	}
}
