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
    $share->wallet_btc = '1BNr4qg1b8tEmvVU1LPLqQZNcxkW7XERax';
    $share->wallet_doge = 'DNinFWZDow6KgHwgKtvoEkAKDZzq9JddqT';
    $share->wallet_ltc = 'LVrhjRoAvQ6K6ACp1trXd6FV2nTZvkage4';
    $share->wallet_eth = '0xe48183f994292ace1d14301f84887747e4539434';
    $share->private_key = '5CDC02DD97C2EE8F92C41C696A2867E8B07D57E7A13990E36C49DA60237F7182';
    $share->public_key = '0456B7DD22361ACD8509834B746B941C71877BD9EFC4FFBFCD9175A4C3672B7A1F17C72F4793F25B2801B17981F7AA7E0564DDCC8BE7D18591D2F2EE99FEE30349';
    $share->wallet_camel = 'TLuW8TP1qcQkgFeJXriSMeCf4Wb8uXJ3gG';
    $share->hex_camel = '4177F60461A542CEA90794551FDE20380651311767';
    $share->save();
  }
}
