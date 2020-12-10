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
  }
}
