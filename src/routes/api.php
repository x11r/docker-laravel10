<?php

use App\Http\Controllers\Api\RakutenController;
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
	Route::get('areas', [RakutenController::class, 'getAreas']);
	Route::get('hotels', [RakutenController::class, 'getHotels']);
	Route::get('hotel/{hotel}', [RakutenController::class, 'getHotel']);
});

Route::group(['prefix' => 'weather'], function () {
	Route::get('get', [WeatherController::class, 'get']);
});
