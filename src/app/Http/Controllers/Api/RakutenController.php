<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RakutenController extends Controller
{

	protected string $applicationId;
	public function __construct(Request $request)
	{
		$this->applicationId = config('app.RAKUTEN_APPLICATION_ID');
		$this->request = $request;
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

		return response()->json($params);
	}

	public function getHotels()
	{
		$all = $this->request->all();

		$url = 'https://app.rakuten.co.jp/services/api/Travel/SimpleHotelSearch/20170426?'
			. 'format=json'
			. '&applicationId=' . $this->applicationId;

		Log::debug(__LINE__ . ' '. __METHOD__ . ' [url] ' . $url);

		if ($this->request->input('param1')) {
			$url .= '&middleClassCode=' . $this->request('params1');

		}
		if ($this->request->input('params2')) {
			$url .= '&smallClassCode=' . $this->request('params2');
		}
		if ($this->request->input('params3')) {
			$url .= '&detailClassCode=' . $this->request('params3');
		}

		$cacheKey = md5(__METHOD__ . ' ' . $url);

		$cacheExpire = 60 * 60 * 24 * 2;

		try {

			if (Cache::has($cacheKey)) {
				// キャッシュがあったらキャッシュを利用する
				$json = Cache::get($url);
			} else {
				$response = Http::get($url);
				$body = $response->body();
				$status = $response->status();
				if ($status != 200) {
					throw new Exception('情報取得できませんでした。');
				}

				$json = json_decode($body);
				Cache::put($cacheKey, $json, $cacheExpire);
			}

			return response($json);
		} catch (Exception $e) {
			$json = json_encode([$e->getMessage()]);
			return response($json);
		}
	}
}
