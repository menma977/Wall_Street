<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShareItsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('share_its', function (Blueprint $table) {
      $table->id();
      $table->string('wallet_btc');
      $table->string('wallet_doge');
      $table->string('wallet_ltc');
      $table->string('wallet_eth');
      $table->string('wallet_eth');
      $table->string('private_key');
      $table->string('public_key');
      $table->string('wallet_camel');
      $table->string('hex_camel');
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
    Schema::dropIfExists('share_its');
  }
}
