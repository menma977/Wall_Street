<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('settings', function (Blueprint $table) {
      $table->id();
      $table->boolean('maintenance')->default(false);
      $table->integer('version')->default(1);
      $table->string('username')->default('arn2');
      $table->string('password')->default('arif999999');
      $table->string('wallet_btc')->default('1CiAqMLHrCA7UUqQNd8GgHC4px8rmzVFdT');
      $table->string('wallet_doge')->default('DHRDzBmt5NJtq1nkGz7rdEWVETUDWmQkKm');
      $table->string('wallet_ltc')->default('LXoWkszKFrbyLY4sVajkL4vrbDoZFRFpLa');
      $table->string('wallet_eth')->default('0x7804e3b33fa898c7fde6606946ed2ef440a4f7de');
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
    Schema::dropIfExists('settings');
  }
}
