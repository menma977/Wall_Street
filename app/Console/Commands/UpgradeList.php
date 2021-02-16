<?php

namespace App\Console\Commands;

use App\Models\UpgradeList as upgrade_list;
use App\Models\Queue;
use App\Models\ShareQueue;
use Carbon\Carbon;
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
   * @return void
   */
  public function handle()
  {
    $queue = Queue::where('status', false)->where('type', 'not like', 'camel_%')->count();
    if (!$queue) {
      try {
        $get = Http::get("https://indodax.com/api/summaries");
        $tronResponse = Http::get("https://api.cameltoken.io/tronapi/tokenprice");
        if ($get->ok() || $get->status() === 200 || str_contains($get->body(), 'ticker')) {
          $ticker = $get->json()['tickers'];
          $upgradeList = upgrade_list::all();
          foreach ($upgradeList as $item) {
            $item->btc = $ticker['btc_idr']['buy'];
            $item->btc_usd = number_format((($item->dollar * $item->idr) / $item->btc) / 2, 8, '', '');
            $item->doge = $ticker['doge_idr']['buy'];
            $item->doge_usd = number_format((($item->dollar * $item->idr) / $item->doge) / 2, 8, '', '');
            $item->eth = $ticker['eth_idr']['buy'];
            $item->eth_usd = number_format((($item->dollar * $item->idr) / $item->eth) / 2, 8, '', '');
            $item->ltc = $ticker['ltc_idr']['buy'];
            $item->ltc_usd = number_format((($item->dollar * $item->idr) / $item->ltc) / 2, 8, '', '');
            if ($tronResponse->ok() && $tronResponse->successful()) {
              $item->camel = $tronResponse->json()["price_usd"];
            }
            $item->camel_usd = ($item->dollar / $item->camel) / 2;
            $item->save();
          }
        } else {
          Log::error($get);
        }
      } catch (Exception $e) {
        Log::warning($e->getMessage() . " Update BTC LINE : " . $e->getLine());
      }
    }

    Queue::where('status', true)->where('created_at', '<', Carbon::now()->addDays(-1))->delete();
    ShareQueue::where('status', true)->where('created_at', '<', Carbon::now()->addDays(-1))->delete();
  }
}
