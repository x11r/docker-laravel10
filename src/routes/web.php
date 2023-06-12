<?php

use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\RakutenController as AdminRakutenController;
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

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'auth'], function () {
    Route::resources([
        'posts' => AdminPostController::class
    ]);

    Route::group(['prefix' => 'rakuten', 'as' => 'rakuten.'], function () {
        Route::get('/', [AdminRakutenController::class,'index'])->name('index');
        Route::get('/area/{middle}', [AdminRakutenController::class, 'areaMiddle'])->name('area-middle');
        Route::get('/area/{middle}/{small}', [AdminRakutenController::class, 'areaSmall'])->name('area-small');
        Route::get('/hotel/{hotel}', [AdminRakutenController::class, 'hotelDetail'])->name('hotel-detail');
    });
});

require __DIR__.'/auth.php';
