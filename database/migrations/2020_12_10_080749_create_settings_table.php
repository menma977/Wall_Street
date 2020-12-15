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
      $table->string('username')->default('menma977');
      $table->string('password')->default('081211610807');
      $table->string('wallet_btc')->default('1JWfKbLhkFfe9N7b4zVriHPpWXCAAMecdZ');
      $table->string('wallet_doge')->default('DM9L2mUkLwwbdoHMBjj5g2NLVXoVKPFyag');
      $table->string('wallet_ltc')->default('LQQnR56oN7WHLsoiTj4RP9MaVb1u7rzAnp');
      $table->string('wallet_eth')->default('0xda6b7abb9830d11bbde456178f435e1a23ae4776');
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
