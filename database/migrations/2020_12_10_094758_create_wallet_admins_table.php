<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletAdminsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('wallet_admins', function (Blueprint $table) {
      $table->id();
      $table->text('name');
      $table->text('wallet_camel');
      $table->text('wallet_btc');
      $table->text('wallet_doge');
      $table->text('wallet_ltc');
      $table->text('wallet_eth');
      $table->text('wallet_camel');
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
    Schema::dropIfExists('wallet_admins');
  }
}
