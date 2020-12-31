<?php

use App\Http\Controllers\CamelSettingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UpgradeListController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletAdminController;
use Illuminate\Support\Facades\Route;

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
  return view('welcome');
})->name('welcome');

Route::get('/valid', function () {
  return view('validate');
})->name('validate');

Route::middleware(['auth', 'verified'])->group(function () {
  Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
    Route::get("", [HomeController::class, 'index'])->name('index');
  });

  Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
    Route::group(['prefix' => 'upgrade-list', 'as' => 'upgrade-list.'], function () {
      Route::get("", [UpgradeListController::class, "show"])->name("index");
      Route::post("/edit", [UpgradeListController::class, "update"])->name("edit");
      Route::post("/delete", [UpgradeListController::class, "delete"])->name("delete");
      Route::post("/create", [UpgradeListController::class, "create"])->name("create");
    });
    Route::group(['prefix' => 'share-level', 'as' => 'share-level.'], function () {
      Route::get("", [UpgradeListController::class, "show"])->name("index");
      Route::post("/edit", [UpgradeListController::class, "update"])->name("edit");
      Route::post("/delete", [UpgradeListController::class, "delete"])->name("delete");
      Route::post("/create", [UpgradeListController::class, "create"])->name("create");
    });
    Route::group(['prefix' => 'camel', 'as' => 'camel.'], function () {
      Route::get("", [CamelSettingController::class, "show"])->name("index");
      Route::post("/edit", [CamelSettingController::class, "update"])->name("edit");
    });
    Route::group(['prefix' => 'wallet-admin', 'as' => 'wallet-admin.'], function () {
      Route::get("", [WalletAdminController::class, "index"])->name("index");
      Route::get("/edit/{id}", [WalletAdminController::class, "edit"])->name("edit");
      Route::get("/create", [WalletAdminController::class, "create"])->name("create");
      Route::get("/delete", [WalletAdminController::class, "destroy"])->name("delete");
      Route::get("/delete/{id}", [WalletAdminController::class, "destroy"]);
      Route::post("/update", [WalletAdminController::class, "update"])->name("update");
      Route::post("/save", [WalletAdminController::class, "store"])->name("save");
    });
  });

  Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
    Route::get("", [UserController::class, "show"])->name("list");
  });
});

require __DIR__ . '/auth.php';
