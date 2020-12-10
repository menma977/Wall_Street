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
          "name" => "btc",
          "wallet" => "1CiAqMLHrCA7UUqQNd8GgHC4px8rmzVFdT"
        ]))->save();
        (new WalletAdmin([
          "name" => "ltc",
          "wallet" => "LXoWkszKFrbyLY4sVajkL4vrbDoZFRFpLa"
        ]))->save();
        (new WalletAdmin([
          "name" => "eth",
          "wallet" => "0x7804e3b33fa898c7fde6606946ed2ef440a4f7de"
        ]))->save();
        (new WalletAdmin([
          "name" => "doge",
          "wallet" => "DHRDzBmt5NJtq1nkGz7rdEWVETUDWmQkKm"
        ]))->save();
    }
}
