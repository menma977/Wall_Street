<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpgradesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('upgrades', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('from');
      $table->bigInteger('to');
      $table->text('description')->nullable();
      $table->string('debit');
      $table->string('credit');
      $table->string('level');
      $table->string('type');
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
    Schema::dropIfExists('upgrades');
  }
}
