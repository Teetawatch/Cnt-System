<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // LINE Notify: Send daily notifications at the configured time
        $schedule->command('line:send-notification')
            ->dailyAt(
                \App\Models\LineNotificationSetting::first()?->schedule_time 
                    ? \Carbon\Carbon::parse(\App\Models\LineNotificationSetting::first()->schedule_time)->format('H:i') 
                    : '07:00'
            )
            ->when(function () {
                $settings = \App\Models\LineNotificationSetting::first();
                return $settings && $settings->is_enabled && $settings->schedule_enabled;
            });
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
