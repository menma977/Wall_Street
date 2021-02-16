<?php

use App\Http\Controllers\AdvancedSettingController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BinaryController;
use App\Http\Controllers\CamelSettingController;
use App\Http\Controllers\DiceController;
use App\Http\Controllers\HistoryCamelController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListUrlController;
use App\Http\Controllers\QueueDailyBankController;
use App\Http\Controllers\QueueDailyController;
use App\Http\Controllers\QueueDailyLimiterListController;
use App\Http\Controllers\QueueDailySettingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UpgradeListController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletAdminController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ShareLevelController;
use App\Http\Controllers\ShareQueueController;
use App\Http\Controllers\StatsController;
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

  Route::group(['prefix' => 'history', 'as' => 'history.'], function () {
    Route::group(['prefix' => 'camel', 'as' => 'camel.'], function () {
      Route::get("", [HistoryCamelController::class, 'all'])->name("combined");
      Route::get("sent", [HistoryCamelController::class, 'sent'])->name("sent");
      Route::get("not-sent", [HistoryCamelController::class, 'pending'])->name("notSent");
      Route::get("{route}/source", [HistoryCamelController::class, 'sources'])->name("source");
    });
    Route::get("{route}", [StatsController::class, 'index'])->name("stats");
    Route::get("{route}/source", [StatsController::class, 'source'])->name("stats.source");
  });

  Route::group(['prefix' => 'queue', 'as' => 'queue.'], function () {
    Route::get("", [QueueController::class, 'index'])->name('index');
    Route::get("filter", [QueueController::class, 'show'])->name('show');

    Route::group(['prefix' => 'share', 'as' => 'share.'], function () {
      Route::get("", [ShareQueueController::class, 'index'])->name('index');
      Route::get("filter", [ShareQueueController::class, 'show'])->name('show');
    });

    Route::group(['prefix' => 'pool', 'as' => 'pool.'], function () {
      Route::get("", [QueueDailyController::class, 'index'])->name('index');
      Route::get("filter", [QueueDailyController::class, 'show'])->name('show');
      Route::group(['prefix' => 'edit', 'as' => 'edit.'], function () {
        Route::get("{id}/limit", [QueueDailyLimiterListController::class, 'edit'])->name('limit');
      });
      Route::group(['prefix' => 'update', 'as' => 'update.'], function () {
        Route::get("setting/{status}", [QueueDailySettingController::class, 'update'])->name('setting');
        Route::post("bank", [QueueDailyBankController::class, 'update'])->name('bank');
        Route::post("{id}/limit", [QueueDailyLimiterListController::class, 'update'])->name('limit');
      });
    });
  });

  Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
    Route::get("/delete/user/{id}/dice", [SettingController::class, "deleteDice"])->name("delete.dice");
    Route::get("balance", [SettingController::class, "balance"])->name("balance");

    Route::group(['prefix' => 'advanced', 'as' => 'advanced.'], function () {
      Route::get("", [AdvancedSettingController::class, "show"])->name("index");
      Route::post("version", [AdvancedSettingController::class, "version"])->name("version");
      Route::post("maintenance", [AdvancedSettingController::class, "maintenance"])->name("maintenance");
    });

    Route::group(['prefix' => 'bank/coin', 'as' => 'bank.'], function () {
      Route::post("edit", [BankAccountController::class, "update"])->name("edit");
    });

    Route::group(['prefix' => 'upgrade-list', 'as' => 'upgrade-list.'], function () {
      Route::get("", [UpgradeListController::class, "show"])->name("index");
      Route::post("edit", [UpgradeListController::class, "update"])->name("edit");
      Route::post("delete", [UpgradeListController::class, "delete"])->name("delete");
      Route::post("create", [UpgradeListController::class, "create"])->name("create");
    });
    Route::group(['prefix' => 'share-level', 'as' => 'share-level.'], function () {
      Route::get("", [ShareLevelController::class, "show"])->name("index");
      Route::get("push", [ShareLevelController::class, "push"])->name("push");
      Route::get("pop", [ShareLevelController::class, "pop"])->name("pop");
      Route::post("edit", [ShareLevelController::class, "update"])->name("update");
    });
    Route::group(['prefix' => 'camel', 'as' => 'camel.'], function () {
      Route::get("", [CamelSettingController::class, "show"])->name("index");
      Route::post("store", [CamelSettingController::class, "store"])->name("store");
      Route::post("edit", [CamelSettingController::class, "update"])->name("edit");
    });
    Route::group(['prefix' => 'wallet-admin', 'as' => 'wallet-admin.'], function () {
      Route::get("", [WalletAdminController::class, "index"])->name("index");
      Route::get("edit/{id}", [WalletAdminController::class, "edit"])->name("edit");
      Route::get("create", [WalletAdminController::class, "create"])->name("create");
      Route::get("delete", [WalletAdminController::class, "destroy"])->name("delete");
      Route::get("delete/{id}", [WalletAdminController::class, "destroy"]);
      Route::post("update", [WalletAdminController::class, "update"])->name("update");
      Route::post("save", [WalletAdminController::class, "store"])->name("save");
    });
  });

  Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
    Route::get("", [UserController::class, "index"])->name("index");
    Route::get("filter", [UserController::class, "filter"])->name("filter");
    Route::get("{id}/show", [UserController::class, "show"])->name("show");
    Route::get("{id}/balance", [UserController::class, "balance"])->name("balance");
    Route::get("{id}/suspend", [UserController::class, "suspend"])->name("suspend");
    Route::post("{id}/update", [UserController::class, "update"])->name("update");
  });

  Route::group(['prefix' => 'binary', 'as' => 'binary.'], function () {
    Route::get("", [BinaryController::class, "index"])->name("index");
    Route::get('/{id}/show', [BinaryController::class, 'show'])->name("show");
  });

  Route::group(['prefix' => 'dice', 'as' => 'dice.'], function () {
    Route::get("", [DiceController::class, "index"])->name("index");
    Route::get("filter", [DiceController::class, "show"])->name("show");
    Route::post("update", [DiceController::class, "update"])->name("update");
  });

  Route::group(['prefix' => 'url', 'as' => 'url.'], function () {
    Route::get("", [ListUrlController::class, "index"])->name("index");
  });
});

require __DIR__ . '/auth.php';
