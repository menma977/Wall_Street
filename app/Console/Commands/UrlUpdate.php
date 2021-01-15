<?php

namespace App\Console\Commands;

use App\Models\ListUrl;
use Illuminate\Console\Command;

class UrlUpdate extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'urlUpdate';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'update url every 10 minutes';

  /**
   * Execute the console command.
   *
   */
  public function handle()
  {
    $listUrl = ListUrl::where('block', true)->first();
    if ($listUrl) {
      $listUrl->block = false;
      $listUrl->save();
    }
  }
}
