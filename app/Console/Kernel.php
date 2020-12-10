<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
  /**
   * Define the application's command schedule.
   *
   * @param Schedule $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
    $schedule->command('updateBTC')->everyFiveMinutes()->withoutOverlapping();
    $schedule->command('updateDoge')->everyFiveMinutes()->withoutOverlapping();
    $schedule->command('updateETH')->everyFiveMinutes()->withoutOverlapping();
    $schedule->command('updateLTC')->everyFiveMinutes()->withoutOverlapping();
  }

  /**
   * Register the commands for the application.
   *
   * @return void
   */
  protected function commands()
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
