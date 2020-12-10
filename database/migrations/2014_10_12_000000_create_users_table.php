<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->integer("role")->default(2);
      $table->string('name');
      $table->string('username')->unique();
      $table->string('email')->unique();
      $table->string('phone')->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password');
      $table->string('password_junk');
      $table->string('secondary_password');
      $table->string('secondary_password_junk');
      $table->string('username_doge')->nullable();
      $table->string('password_doge')->nullable();
      $table->text('account_cookie')->nullable();
      $table->text('wallet_btc')->nullable();
      $table->text('wallet_ltc')->nullable();
      $table->text('wallet_doge')->nullable();
      $table->text('wallet_eth')->nullable();
      $table->integer('level')->default(1);
      $table->boolean('suspend')->default(false);
      $table->boolean('active')->default(false);
      $table->rememberToken();
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
    Schema::dropIfExists('users');
  }
}
