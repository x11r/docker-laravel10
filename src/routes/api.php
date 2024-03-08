<?php

use App\Http\Controllers\Api\WeatherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'rakuten'], function () {
	Route::get('areas', [ApiRakutenController::class, 'getAreas']);
	Route::get('hotels', [ApiRakutenController::class, 'getHotels']);
	Route::get('hotel/{hotel}', [ApiRakutenController::class, 'getHotel']);

Route::group(['prefix' => 'weather'], function () {
	Route::get('get', [WeatherController::class, 'get']);
});
