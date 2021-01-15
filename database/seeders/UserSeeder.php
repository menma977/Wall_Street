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
    $user->username = 'Boboom168';
    $user->email = 'wall@street.info';
    $user->phone = '081200000000';
    $user->password = Hash::make('Surabaya2510');
    $user->password_junk = 'Surabaya2510';
    $user->secondary_password = Hash::make('123456');
    $user->secondary_password_junk = '123456';
    $user->username_doge = 'wallstreet.info';
    $user->password_doge = '123456+A';
    $user->private_key = 'BD79796E279FDF250A0F94D7EF50BEC4CCA2351C8AEFBC4DC54C68A75D55942B';
    $user->public_key = '04D8D01D631FFA2D137AB4F5BF9221A3EC0B7015369DCDA192B71A330230CE14DC7F33A3CCFBB67E7F36E789069CD44CE5167A4EAFA58DE24D77AD39D86F15CB96';
    $user->wallet_camel = 'TYSs8UUeEUwThPMPkzuPt6JNfWQgcbn9Xj';
    $user->hex_camel = '41F68DD363C9BBFCA9F012599B68EE13E5BC961F5E';
    $user->wallet_btc = '1PfBXAtQkcuLjtTStZdJPDKx7sUHUadAEx';
    $user->wallet_ltc = 'LeMbZcdxEK4MPK3wuqj3ywYjNhr4jhnnkh';
    $user->wallet_doge = 'DFyhesdDdogR5QkdhJ6rwCe7JDEi9tCnfh';
    $user->wallet_eth = '0x0aa61efefcab11da35fea63ff284d8948084d687';
    $user->level = 10;
    $user->save();
  }
}
