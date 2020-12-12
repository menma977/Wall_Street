<?php

use App\Http\Controllers\API\LoginController;
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

Route::get('/version', [VersionController::class, 'index'])->middleware(['throttle:6,1']);
Route::post('/login', [LoginController::class, 'index'])->middleware(['throttle:3,1']);
Route::post('/registration', [RegisterController::class, 'out'])->middleware(['throttle:6,1']);

Route::middleware(['auth:api', 'verified'])->group(function () {
  Route::group(['prefix' => 'user', 'as' => 'user.'], static function () {
    Route::get('/show', [UserController::class, 'self']);
    Route::post('/registered', [RegisterController::class, 'in'])->middleware(['throttle:6,1']);
    Route::post('/update', [UserController::class, 'update'])->middleware(['throttle:2,1']);
  });

  Route::group(['prefix' => 'upgrade', 'as' => 'upgrade.'], static function () {
    Route::post('/', [UpgradeController::class, 'upgrade']);
    Route::get('/index', [UpgradeController::class, 'index']);
  });
});
