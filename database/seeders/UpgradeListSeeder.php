<?php

namespace Database\Seeders;

use App\Models\UpgradeList;
use Illuminate\Database\Seeder;

class UpgradeListSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $upgradeList = new UpgradeList();
    $upgradeList->dollar = 10;
    $upgradeList->save();

    $upgradeList = new UpgradeList();
    $upgradeList->dollar = 50;
    $upgradeList->save();

    $upgradeList = new UpgradeList();
    $upgradeList->dollar = 100;
    $upgradeList->save();

    $upgradeList = new UpgradeList();
    $upgradeList->dollar = 500;
    $upgradeList->save();

    $upgradeList = new UpgradeList();
    $upgradeList->dollar = 1000;
    $upgradeList->save();

    $upgradeList = new UpgradeList();
    $upgradeList->dollar = 5000;
    $upgradeList->save();

    $upgradeList = new UpgradeList();
    $upgradeList->dollar = 10000;
    $upgradeList->save();
  }
}
