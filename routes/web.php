<?php

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BinaryController;
use App\Http\Controllers\CamelSettingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UpgradeListController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletAdminController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ShareLevelController;
use App\Http\Controllers\ShareQueueController;
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
    Route::get("queue", [HomeController::class, 'queue'])->name('queue');
    Route::get("queue/share", [HomeController::class, 'shareQueue'])->name('queue.share');
  });

  Route::group(['prefix' => 'queue', 'as' => 'queue.'], function () {
    Route::get("", [QueueController::class, 'index'])->name('index');
    Route::get("find", [QueueController::class, 'show'])->name('show');

    Route::group(['prefix' => 'share', 'as' => 'share.'], function () {
      Route::get("", [ShareQueueController::class, 'index'])->name('index');
      Route::get("find", [ShareQueueController::class, 'show'])->name('show');
    });
  });

  Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
    Route::get("/balance", [SettingController::class, "balance"])->name("balance");

    Route::group(['prefix' => 'bank/coin', 'as' => 'bank.'], function () {
      Route::post("/edit", [BankAccountController::class, "update"])->name("edit");
    });

    Route::group(['prefix' => 'upgrade-list', 'as' => 'upgrade-list.'], function () {
      Route::get("", [UpgradeListController::class, "show"])->name("index");
      Route::post("/edit", [UpgradeListController::class, "update"])->name("edit");
      Route::post("/delete", [UpgradeListController::class, "delete"])->name("delete");
      Route::post("/create", [UpgradeListController::class, "create"])->name("create");
    });
    Route::group(['prefix' => 'share-level', 'as' => 'share-level.'], function () {
      Route::get("", [ShareLevelController::class, "show"])->name("index");
      Route::get("/push", [ShareLevelController::class, "push"])->name("push");
      Route::get("/pop", [ShareLevelController::class, "pop"])->name("pop");
      Route::post("/edit", [ShareLevelController::class, "update"])->name("update");
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
    Route::get("", [UserController::class, "index"])->name("index");
    Route::get("filter", [UserController::class, "filter"])->name("filter");
    Route::get("/{id}/show", [UserController::class, "show"])->name("show");
  });

  Route::group(['prefix' => 'binary', 'as' => 'binary.'], function () {
    Route::get("", [BinaryController::class, "index"])->name("index");
    Route::get('/{id}/show', [BinaryController::class, 'show'])->name("show");
  });
});

require __DIR__ . '/auth.php';
