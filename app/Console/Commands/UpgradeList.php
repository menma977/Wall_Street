<?php

namespace App\Console\Commands;

use http\Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpgradeList extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'upgradeList';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Update list upgrade';

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle()
  {
    try {
      $get = Http::get("https://indodax.com/api/summaries");
      if ($get->ok() || $get->status() === 200 || str_contains($get->body(), 'ticker')) {
        $ticker = $get->json()['tickers'];
        $upgradeList = \App\Models\UpgradeList::all();
        foreach ($upgradeList as $item) {
          $item->btc = $ticker['btc_idr']['buy'];
          $item->btc_usd = number_format((($item->dollar * $item->idr) / $item->btc) * 10 ** 8, 0, ',', '');
          $item->doge = $ticker['doge_idr']['buy'];
          $item->doge_usd = number_format((($item->dollar * $item->idr) / $item->doge) * 10 ** 8, 0, ',', '');
          $item->eth = $ticker['eth_idr']['buy'];
          $item->eth_usd = number_format((($item->dollar * $item->idr) / $item->eth) * 10 ** 8, 0, ',', '');
          $item->ltc = $ticker['ltc_idr']['buy'];
          $item->ltc_usd = number_format((($item->dollar * $item->idr) / $item->ltc) * 10 ** 8, 0, ',', '');
          $item->save();
        }
      } else {
        Log::error($get);
      }
    } catch (Exception $e) {
      Log::warning($e->getMessage() . " Update BTC LINE : " . $e->getLine());
    }
  }
}
