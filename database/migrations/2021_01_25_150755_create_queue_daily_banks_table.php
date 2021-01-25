<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueueDailyBanksTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('queue_daily_banks', function (Blueprint $table) {
      $table->id();
      $table->string('private_key')->default('A0CC2F465FC51E0486E6C4C0D5779BA8A7E95CE1A9229741342A117DE693272C');
      $table->string('public_key')->default('04EEA9BBF181FB5AEB51D17EB4F2CD1BBA501384E6F1F85D13264FE2D760FBE79798E5AB774E2851EB5F1BC91E78803CC34B6FC218939FB3614B0770076B1175F4');
      $table->string('wallet_camel')->default('TXB1gXBzrsMmAVWRAgU1SLGUQTeFTXHgm6');
      $table->string('hex_camel')->default('41E8962F1194E18E69EEA2CEACCEBC337F056AEB82');
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
    Schema::dropIfExists('queue_daily_banks');
  }
}
