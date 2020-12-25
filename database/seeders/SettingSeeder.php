<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\CamelSetting;
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

    $backAccount = new BankAccount();
    $backAccount->username = 'bankwall';
    $backAccount->password = '123456+A';
    $backAccount->wallet_btc = '1BNr4qg1b8tEmvVU1LPLqQZNcxkW7XERax';
    $backAccount->wallet_doge = 'DNinFWZDow6KgHwgKtvoEkAKDZzq9JddqT';
    $backAccount->wallet_ltc = 'LVrhjRoAvQ6K6ACp1trXd6FV2nTZvkage4';
    $backAccount->wallet_eth = '0xe48183f994292ace1d14301f84887747e4539434';
    $backAccount->save();

    $camelSetting = new CamelSetting();
    $camelSetting->private_key = '5CDC02DD97C2EE8F92C41C696A2867E8B07D57E7A13990E36C49DA60237F7182';
    $camelSetting->public_key = '0456B7DD22361ACD8509834B746B941C71877BD9EFC4FFBFCD9175A4C3672B7A1F17C72F4793F25B2801B17981F7AA7E0564DDCC8BE7D18591D2F2EE99FEE30349';
    $camelSetting->wallet_camel = 'TLuW8TP1qcQkgFeJXriSMeCf4Wb8uXJ3gG';
    $camelSetting->hex_camel = '4177F60461A542CEA90794551FDE20380651311767';
    $camelSetting->share_time = 1;
    $camelSetting->share_value = 1;
    $camelSetting->to_dollar = 1;
    $camelSetting->save();
  }
}
