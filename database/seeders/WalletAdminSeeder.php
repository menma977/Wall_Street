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
        (new WalletAdmin([
          "name" => "a",
          "wallet_btc" => "1CiAqMLHrCA7UUqQNd8GgHC4px8rmzVFdT",
          "wallet_ltc" => "LXoWkszKFrbyLY4sVajkL4vrbDoZFRFpLa",
          "wallet_doge" => "DHRDzBmt5NJtq1nkGz7rdEWVETUDWmQkKm",
          "wallet_eth" => "0x7804e3b33fa898c7fde6606946ed2ef440a4f7de",
        ]))->save();
    }
}
