<?php

namespace App\Console;

use App\Console\Commands\CheckExpiredCampaigns;
use App\Console\Commands\DeleteMealsData;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
         $schedule->command(DeleteMealsData::class)->daily()->at('14:00');
         $schedule->command(CheckExpiredCampaigns::class)->daily()->at('19:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
