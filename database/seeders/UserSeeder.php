<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $user = new User();
    $user->role = 1;
    $user->name = 'ADMIN';
    $user->username = 'admin';
    $user->email = 'wall@street.info';
    $user->phone = '081200000000';
    $user->password = Hash::make('admin');
    $user->password_junk = 'admin';
    $user->secondary_password = Hash::make('123456');
    $user->secondary_password_junk = '123456';
    $user->username_doge = 'arn2';
    $user->password_doge = 'arif999999';
    $user->wallet_btc = '1CiAqMLHrCA7UUqQNd8GgHC4px8rmzVFdT';
    $user->wallet_ltc = 'LXoWkszKFrbyLY4sVajkL4vrbDoZFRFpLa';
    $user->wallet_doge = 'DHRDzBmt5NJtq1nkGz7rdEWVETUDWmQkKm';
    $user->wallet_eth = '0x7804e3b33fa898c7fde6606946ed2ef440a4f7de';
    $user->level = 0;
    $user->save();
  }
}
