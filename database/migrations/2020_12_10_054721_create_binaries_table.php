<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBinariesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('binaries', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('sponsor');
      $table->bigInteger('up_line');
      $table->bigInteger('down_line');
      $table->boolean('active')->default(false);
      $table->string('profit')->default(0);
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
    Schema::dropIfExists('binaries');
  }
}
