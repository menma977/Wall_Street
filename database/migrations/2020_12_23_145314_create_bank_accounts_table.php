<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('bank_accounts', function (Blueprint $table) {
      $table->id();
      $table->string('username');
      $table->string('password');
      $table->string('wallet_btc');
      $table->string('wallet_doge');
      $table->string('wallet_ltc');
      $table->string('wallet_eth');
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
    Schema::dropIfExists('bank_accounts');
  }
}
