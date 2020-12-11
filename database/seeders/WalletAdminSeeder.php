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
    $wallet->name = 'wallet 1';
    $wallet->wallet_btc = '1CiAqMLHrCA7UUqQNd8GgHC4px8rmzVFdT';
    $wallet->wallet_doge = '1CiAqMLHrCA7UUqQNd8GgHC4px8rmzVFdT';
    $wallet->wallet_ltc = '1CiAqMLHrCA7UUqQNd8GgHC4px8rmzVFdT';
    $wallet->wallet_eth = '1CiAqMLHrCA7UUqQNd8GgHC4px8rmzVFdT';
    $wallet->save();
  }
}
