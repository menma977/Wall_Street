<?php

namespace App\Console\Commands;

use App\Models\UpgradeList;
use http\Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateDoge extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'updateDoge';

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
    sleep(2);
    try {
      $get = Http::get("https://indodax.com/api/doge_idr/ticker");
      if ($get->ok() || $get->status() === 200 || str_contains($get->body(), 'ticker')) {
        $ticker = $get->json()['ticker'];
        $upgradeList = UpgradeList::all();
        foreach ($upgradeList as $item) {
          $item->doge = $ticker['buy'];
          $item->doge_usd = ($item->dollar * $item->idr) / $item->doge;
          $item->save();
        }
      } else {
        Log::error($get);
      }
    } catch (Exception $e) {
      Log::warning($e->getMessage() . " Update DOGE LINE : " . $e->getLine());
    }
  }
}
