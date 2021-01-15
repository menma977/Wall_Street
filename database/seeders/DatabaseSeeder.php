<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call(SettingSeeder::class);
    $this->call(ShareLevelSeeder::class);
    $this->call(UpgradeListSeeder::class);
    $this->call(UserSeeder::class);
    $this->call(WalletAdminSeeder::class);
    $this->call(ShareItSeeder::class);
    $this->call(ListUrlSeeder::class);
  }
}
