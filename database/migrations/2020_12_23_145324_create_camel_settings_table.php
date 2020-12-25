<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCamelSettingsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('camel_settings', function (Blueprint $table) {
      $table->id();
      $table->string('private_key');
      $table->string('public_key');
      $table->string('wallet_camel');
      $table->string('hex_camel');
      $table->string('share_value');
      $table->integer('share_time');
      $table->string('to_dollar')->default(1);
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
    Schema::dropIfExists('camel_settings');
  }
}
