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
    $camelSetting->private_key = 'EB9C34C8ED00876E85F5CCCD4DB5CD7266CDD797BEA5F5B07E3CF5015C47A2F4';
    $camelSetting->public_key = '045EC9C52DFC9EE985FD7EE2C4D027E5297C967CF98D2DB26F640B364F8E8998E62B41AFAAB7A1BAC83E5DDD22DAF357B49D4DA7829F5DAE48108872F52C981D89';
    $camelSetting->wallet_camel = 'TQNcwDzah8QfiXmumWzb7cvt2hMSYBsbBK';
    $camelSetting->hex_camel = '419DFF4E9E41BE9862212F7AC5D94AEE9ED41EAF0E';
    $camelSetting->share_time = 1;
    $camelSetting->share_value = 1;
    $camelSetting->to_dollar = 1;
    $camelSetting->save();
  }
}
