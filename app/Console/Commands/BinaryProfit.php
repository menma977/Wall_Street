<?php

namespace App\Console\Commands;

use App\Models\Binary;
use App\Models\Upgrade;
use Illuminate\Console\Command;

class BinaryProfit extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'binaryProfit';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

  /**
   * Execute the console command.
   *
   */
  public function handle()
  {
    $upgrade = Upgrade::where('status', false)->where('description', 'like', '%did an upgrade%')->first();
    if ($upgrade) {
      $this->binaryHandler($upgrade->from, $upgrade->debit / 3);
      $upgrade->status = true;
      $upgrade->save();
    }
  }

  private function binaryHandler($user, $value)
  {
    $binary = Binary::where('down_line', $user)->first();
    if ($binary) {
      $binary->profit += $value;
      $binary->save();

      $this->binaryHandler($binary->up_line, $value);
    }
  }
}
