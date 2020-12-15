<?php

use App\Http\Controllers\API\BTCController;
use App\Http\Controllers\API\DogeController;
use App\Http\Controllers\API\ETHController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\LTCController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\UpgradeController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VersionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/version', [VersionController::class, 'index']);
Route::post('/login', [LoginController::class, 'index'])->middleware(['throttle:3,1']);
Route::post('/registration', [RegisterController::class, 'out'])->middleware(['throttle:6,1']);

Route::middleware(['auth:api', 'verified'])->group(function () {
  Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
    Route::get('/show', [UserController::class, 'self']);
    Route::post('/registered', [RegisterController::class, 'in'])->middleware(['throttle:6,1']);
    Route::post('/update', [UserController::class, 'update'])->middleware(['throttle:2,1']);
  });

  Route::group(['prefix' => 'upgrade', 'as' => 'upgrade.'], function () {
    Route::post('', [UpgradeController::class, 'upgrade']);
    Route::get('/index', [UpgradeController::class, 'index'])->middleware(['throttle:2,1']);
  });

  Route::group(['prefix' => 'btc', 'as' => 'btc.'], function () {
    Route::get('create', [BTCController::class, 'create']);
    Route::post('store/{username}', [BTCController::class, 'store'])->middleware(['throttle:1,1']);
  });

  Route::group(['prefix' => 'doge', 'as' => 'doge.'], function () {
    Route::get('create', [DogeController::class, 'create']);
    Route::post('store/{username}', [DogeController::class, 'store'])->middleware(['throttle:1,1']);
  });

  Route::group(['prefix' => 'ltc', 'as' => 'ltc.'], function () {
    Route::get('create', [LTCController::class, 'create']);
    Route::post('store/{username}', [LTCController::class, 'store'])->middleware(['throttle:1,1']);
  });

  Route::group(['prefix' => 'eth', 'as' => 'eth.'], function () {
    Route::get('create', [ETHController::class, 'create']);
    Route::post('store/{username}', [ETHController::class, 'store'])->middleware(['throttle:1,1']);
  });
});
