<?php

namespace Database\Seeders;

use App\Models\ShareLevel;
use Illuminate\Database\Seeder;

class ShareLevelSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $shareLevel = new ShareLevel();
    $shareLevel->level = "Level 1";
    $shareLevel->percent = 0.2;
    $shareLevel->save();

    $shareLevel = new ShareLevel();
    $shareLevel->level = "Level 2";
    $shareLevel->percent = 0.1;
    $shareLevel->save();

    $shareLevel = new ShareLevel();
    $shareLevel->level = "Level 3";
    $shareLevel->percent = 0.05;
    $shareLevel->save();

    $shareLevel = new ShareLevel();
    $shareLevel->level = "Level 4";
    $shareLevel->percent = 0.05;
    $shareLevel->save();

    $shareLevel = new ShareLevel();
    $shareLevel->level = "Level 5";
    $shareLevel->percent = 0.02;
    $shareLevel->save();

    $shareLevel = new ShareLevel();
    $shareLevel->level = "Level 6";
    $shareLevel->percent = 0.02;
    $shareLevel->save();

    $shareLevel = new ShareLevel();
    $shareLevel->level = "Level 7";
    $shareLevel->percent = 0.02;
    $shareLevel->save();

    $shareLevel = new ShareLevel();
    $shareLevel->level = "Level 8";
    $shareLevel->percent = 0.02;
    $shareLevel->save();

    $shareLevel = new ShareLevel();
    $shareLevel->level = "Level 9";
    $shareLevel->percent = 0.02;
    $shareLevel->save();

    $shareLevel = new ShareLevel();
    $shareLevel->level = "IT";
    $shareLevel->percent = 0.01;
    $shareLevel->save();

    $shareLevel = new ShareLevel();
    $shareLevel->level = "BuyWall";
    $shareLevel->percent = 0.05;
    $shareLevel->save();

    $shareLevel = new ShareLevel();
    $shareLevel->level = "Share";
    $shareLevel->percent = 0.44;
    $shareLevel->save();
  }
}
