<?php

use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\RakutenController as AdminRakutenController;
use App\Http\Controllers\RakutenController as RakutenController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 楽天APIを公開ページに設置してみる
Route::group(['prefix' => 'rakuten', 'as' => 'rakuten.'], function () {
    Route::get('/area', [RakutenController::class, 'getAreas'])->name('areas');
    Route::get('/area/{middle}/{small}', [RakutenController::class, 'getSmall'])->name('area-small');
    Route::get('/area/{middle}/{small}/{detail}', [RakutenController::class, 'getDetail'])->name('area-detail');
    Route::get('/hotel/{hotel}', [RakutenController::class, 'hotelDetail'])->name('hotel-detail');
});

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth'], function () {
    Route::resources([
        'posts' => AdminPostController::class
    ]);

    Route::group(['prefix' => 'rakuten', 'as' => 'rakuten.'], function () {
        Route::get('/', [AdminRakutenController::class,'index'])->name('index');
        Route::get('/area/{middle}/{small}', [AdminRakutenController::class, 'areaMulti'])->name('area-small');
        Route::get('/area/{middle}/{small}/{detail}', [AdminRakutenController::class, 'areaMulti'])
            ->name('area-multi');
        Route::get('/hotel/{hotel}', [AdminRakutenController::class, 'hotelDetail'])->name('hotel-detail');
        Route::get('/hotelRanking', [AdminRakutenController::class, 'hotelRanking'])->name('hotel-ranking');
    });
});

require __DIR__.'/auth.php';
