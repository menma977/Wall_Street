<?php

namespace Database\Seeders;

use App\Models\WalletAdmin;
use Illuminate\Database\Seeder;

class WalletAdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $wallet = new WalletAdmin();
    $wallet->name = 'Admin 1';
    $wallet->wallet_camel = '1CiAqMLHrCA7UUqQNd8GgHC4px8rmzVFdT';
    $wallet->wallet_btc = '1JWfKbLhkFfe9N7b4zVriHPpWXCAAMecdZ';
    $wallet->wallet_doge = 'DM9L2mUkLwwbdoHMBjj5g2NLVXoVKPFyag';
    $wallet->wallet_ltc = 'LQQnR56oN7WHLsoiTj4RP9MaVb1u7rzAnp';
    $wallet->wallet_eth = '0xda6b7abb9830d11bbde456178f435e1a23ae4776';
    $wallet->save();
  }
}
