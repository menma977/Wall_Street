<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\QueueController;
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
});

require __DIR__ . '/auth.php';
