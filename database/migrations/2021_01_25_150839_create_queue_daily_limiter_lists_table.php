<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueueDailyLimiterListsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('queue_daily_limiter_lists', function (Blueprint $table) {
      $table->id();
      $table->string('min')->default('10');
      $table->string('max')->default('49');
      $table->string('value')->default('0.001');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('queue_daily_limiter_lists');
  }
}
