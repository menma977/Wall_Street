<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateETHSTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('e_t_h_s', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('user_id');
      $table->text('description')->nullable();
      $table->string('debit')->default(0);
      $table->string('credit')->default(0);
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
    Schema::dropIfExists('e_t_h_s');
  }
}
