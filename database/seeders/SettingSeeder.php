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
    $backAccount->username = 'menma977';
    $backAccount->password = '081211610807';
    $backAccount->wallet_btc = '1JWfKbLhkFfe9N7b4zVriHPpWXCAAMecdZ';
    $backAccount->wallet_doge = 'DM9L2mUkLwwbdoHMBjj5g2NLVXoVKPFyag';
    $backAccount->wallet_ltc = 'LQQnR56oN7WHLsoiTj4RP9MaVb1u7rzAnp';
    $backAccount->wallet_eth = '0xda6b7abb9830d11bbde456178f435e1a23ae4776';
    $backAccount->save();

    $camelSetting = new CamelSetting();
    $camelSetting->private_key = '5CDC02DD97C2EE8F92C41C696A2867E8B07D57E7A13990E36C49DA60237F7182';
    $camelSetting->public_key = '0456B7DD22361ACD8509834B746B941C71877BD9EFC4FFBFCD9175A4C3672B7A1F17C72F4793F25B2801B17981F7AA7E0564DDCC8BE7D18591D2F2EE99FEE30349';
    $camelSetting->wallet_camel = 'TLuW8TP1qcQkgFeJXriSMeCf4Wb8uXJ3gG';
    $camelSetting->hex_camel = '4177F60461A542CEA90794551FDE20380651311767';
    $camelSetting->share_time = 1;
    $camelSetting->share_value = 1;
    $camelSetting->save();
  }
}
