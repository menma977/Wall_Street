<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $setting = new Setting();
    $setting->maintenance = false;
    $setting->version = 1;
    $setting->save();
  }
}
