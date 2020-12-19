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
    $user->username_doge = 'menma977';
    $user->password_doge = '081211610807';
    $user->wallet_btc = '1JWfKbLhkFfe9N7b4zVriHPpWXCAAMecdZ';
    $user->wallet_ltc = 'LQQnR56oN7WHLsoiTj4RP9MaVb1u7rzAnp';
    $user->wallet_doge = 'DM9L2mUkLwwbdoHMBjj5g2NLVXoVKPFyag';
    $user->wallet_eth = '0xda6b7abb9830d11bbde456178f435e1a23ae4776';
    $user->level = 10;
    $user->save();

    $user = new User();
    $user->role = 1;
    $user->name = 'BANK';
    $user->username = 'bank';
    $user->email = 'bank@street.info';
    $user->phone = '081200000001';
    $user->password = Hash::make('bank');
    $user->password_junk = 'bank';
    $user->secondary_password = Hash::make('123456');
    $user->secondary_password_junk = '123456';
    $user->username_doge = 'bankwall';
    $user->password_doge = '123456+A';
    $user->wallet_btc = '1BNr4qg1b8tEmvVU1LPLqQZNcxkW7XERax';
    $user->wallet_ltc = 'LVrhjRoAvQ6K6ACp1trXd6FV2nTZvkage4';
    $user->wallet_doge = 'DNinFWZDow6KgHwgKtvoEkAKDZzq9JddqT';
    $user->wallet_eth = '0xe48183f994292ace1d14301f84887747e4539434';
    $user->level = 0;
    $user->save();
  }
}
