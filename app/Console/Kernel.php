<?php

namespace App\Console;

use App\Console\Commands\RunImporters;
use App\Console\Commands\SendDailyJobs;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         $schedule->command(RunImporters::class)->daily()->at('02:00')->timezone('America/Los_Angeles');
         $schedule->command(SendDailyJobs::class)->daily()->at('09:00')->timezone('America/Los_Angeles');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
