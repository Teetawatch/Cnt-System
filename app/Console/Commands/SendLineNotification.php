<?php

namespace App\Console\Commands;

use App\Models\LineNotificationSetting;
use App\Services\LineNotifyService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendLineNotification extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'line:send-notification {--date= : Send for specific date (Y-m-d), defaults to today}';

    /**
     * The console command description.
     */
    protected $description = 'ส่งแจ้งเตือนกิจกรรมประจำวันผ่าน LINE Notify';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $settings = LineNotificationSetting::instance();

        if (!$settings->is_enabled || !$settings->schedule_enabled) {
            $this->info('LINE Notify scheduling is disabled. Skipping...');
            return self::SUCCESS;
        }

        $date = $this->option('date') ?: Carbon::today()->format('Y-m-d');

        $this->info("Sending LINE Notification for date: {$date}");

        $service = new LineNotifyService();
        $result = $service->sendForDate($date, 'scheduled');

        if ($result['success']) {
            $this->info('✅ ' . $result['message']);
        } else {
            $this->error('❌ ' . $result['message']);
        }

        return $result['success'] ? self::SUCCESS : self::FAILURE;
    }
}
