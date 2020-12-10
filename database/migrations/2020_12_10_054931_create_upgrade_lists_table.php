<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpgradeListsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('upgrade_lists', function (Blueprint $table) {
      $table->id();
      $table->string('dollar')->default(0);
      $table->string('idr')->default(15000);
      $table->string('btc')->default(0);
      $table->string('ltc')->default(0);
      $table->string('doge')->default(0);
      $table->string('eth')->default(0);
      $table->string('btc_usd')->default(0);
      $table->string('ltc_usd')->default(0);
      $table->string('doge_usd')->default(0);
      $table->string('eth_usd')->default(0);
      $table->timestamps();
      $table->softDeletes();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('upgrade_lists');
  }
}
