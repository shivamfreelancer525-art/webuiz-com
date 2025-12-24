<?php

namespace App\Console;

use App\Console\Commands\ResetDemoSite;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        if (config('common.site.demo')) {
            $schedule->command(ResetDemoSite::class)->cron('0 */2 * * *');
        }

        // Validate pending custom domains every 10 minutes
        $schedule->command('custom-domains:validate-pending')
            ->everyTenMinutes()
            ->withoutOverlapping();
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
