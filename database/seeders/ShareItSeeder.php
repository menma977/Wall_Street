<?php

namespace Database\Seeders;

use App\Models\ShareIt;
use Illuminate\Database\Seeder;

class ShareItSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $share = new ShareIt();
    $share->wallet_btc = '1BAt6h3kbbxs7UkMMrj96TsKAYpwZ9JYtZ';
    $share->wallet_doge = 'DH2RsGMm4dN7napXN5KKxpNSjvHhyTA9GD';
    $share->wallet_ltc = 'LcTyXdU6bFvn3fV2B3GPtfENrRjZvev1t1';
    $share->wallet_eth = '0xd7e9a71aee9a3bc394db39b94a6ce8c0fa019932';
    $share->private_key = '3CD1630CC57343615EBC3816E9C20F40D2999EFF174F0F406E3BBCB55EDF7EBB';
    $share->public_key = '04B074099CD6F253D541430B04D0E2307523E76828340FB3BACBC2B1DB5C3E443B6673E3CD2873A390AD7154E791B18E6AA72285BC120A4205140865CA72264BCA';
    $share->wallet_camel = 'TD4mffNBqpWQudvcNTDPiEkY4HXZFNHAvE';
    $share->hex_camel = '4121F5B99A23167CCBE3172822281925ECF1DF8363';
    $share->save();
  }
}
