<?php

use App\Http\Controllers\API\BinaryController;
use App\Http\Controllers\API\BTCController;
use App\Http\Controllers\API\CamelController;
use App\Http\Controllers\API\DogeController;
use App\Http\Controllers\API\ETHController;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\LogoutController;
use App\Http\Controllers\API\LTCController;
use App\Http\Controllers\API\PasswordResetLinkController;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\SendVerifyEmailController;
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

Route::get('/version', [VersionController::class, 'index'])->middleware('guest');
Route::get('/upgrade/packages', [UpgradeController::class, 'packages'])->middleware('guest');
Route::post('/login', [LoginController::class, 'index'])->middleware(['throttle:3,1', 'guest']);
Route::post('/registration', [RegisterController::class, 'out'])->middleware(['throttle:6,1', 'guest']);
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->middleware(['throttle:3,1', 'guest']);
Route::post('/email/verify', [SendVerifyEmailController::class, 'store'])->middleware(['throttle:1,1', 'guest']);

Route::middleware(['auth:api', 'verified'])->group(function () {
  Route::get('/logout', [LogoutController::class, 'index']);

  Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
    Route::get('/show', [UserController::class, 'self']);
    Route::post('/registered', [RegisterController::class, 'in'])->middleware(['throttle:3,1']);
    Route::post('/update', [UserController::class, 'update'])->middleware(['throttle:2,1']);
  });

  Route::group(['prefix' => 'upgrade', 'as' => 'upgrade.'], function () {
    Route::get('', [UpgradeController::class, 'index']);
    Route::get('/show', [UpgradeController::class, 'show']);
    Route::get('/create', [UpgradeController::class, 'create']);
    Route::post('/store', [UpgradeController::class, 'store'])->middleware(['throttle:5,1']);
  });

  Route::group(['prefix' => 'btc', 'as' => 'btc.'], function () {
    Route::get('', [BTCController::class, 'index']);
    Route::get('/show', [BTCController::class, 'show']);
    Route::get('/create', [BTCController::class, 'create']);
    Route::post('/store', [BTCController::class, 'store'])->middleware(['throttle:2,1']);
  });

  Route::group(['prefix' => 'doge', 'as' => 'doge.'], function () {
    Route::get('', [DogeController::class, 'index']);
    Route::get('/show', [DogeController::class, 'show']);
    Route::get('/create', [DogeController::class, 'create']);
    Route::post('/store', [DogeController::class, 'store'])->middleware(['throttle:2,1']);
  });

  Route::group(['prefix' => 'ltc', 'as' => 'ltc.'], function () {
    Route::get('', [LTCController::class, 'index']);
    Route::get('/show', [LTCController::class, 'show']);
    Route::get('/create', [LTCController::class, 'create']);
    Route::post('/store', [LTCController::class, 'store'])->middleware(['throttle:2,1']);
  });

  Route::group(['prefix' => 'eth', 'as' => 'eth.'], function () {
    Route::get('', [ETHController::class, 'index']);
    Route::get('/show', [ETHController::class, 'show']);
    Route::get('/create', [ETHController::class, 'create']);
    Route::post('/store', [ETHController::class, 'store'])->middleware(['throttle:2,1']);
  });

  Route::group(['prefix' => 'camel', 'as' => 'camel.'], function () {
    Route::get('', [CamelController::class, 'index']);
    Route::get('/show', [CamelController::class, 'show']);
    Route::get('/create', [CamelController::class, 'create']);
    Route::post('/store', [CamelController::class, 'store'])->middleware(['throttle:2,1']);

    Route::group(['prefix' => 'history', 'as' => 'history.'], function () {
      Route::post('', [CamelController::class, 'index']);
    });
  });

  Route::group(['prefix' => 'tron', 'as' => 'tron.'], function () {
    Route::post('/store', [CamelController::class, 'store'])->middleware(['throttle:2,1']);
  });

  Route::group(['prefix' => 'binary', 'as' => 'api.binary.'], function () {
    Route::get('', [BinaryController::class, 'index'])->name("index");
    Route::get('/show/{id}', [BinaryController::class, 'show'])->name("show");
  });
});
