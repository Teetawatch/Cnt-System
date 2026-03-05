<?php

namespace App\Services;

use App\Models\CalendarEvent;
use App\Models\LineNotificationLog;
use App\Models\LineNotificationSetting;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineNotifyService
{
    private const API_URL = 'https://notify-api.line.me/api/notify';

    /**
     * Send notification for a specific date
     */
    public function sendForDate(string $date, string $sendType = 'manual', ?int $sentBy = null): array
    {
        $settings = LineNotificationSetting::instance();

        if (!$settings->is_enabled) {
            return ['success' => false, 'message' => 'การแจ้งเตือน LINE ถูกปิดอยู่'];
        }

        if (empty($settings->line_notify_token)) {
            return ['success' => false, 'message' => 'ยังไม่ได้ตั้งค่า LINE Notify Token'];
        }

        $targetDate = Carbon::parse($date);
        $message = $this->buildMessage($targetDate, $settings);
        $eventsCount = $this->getEventsCount($targetDate);

        try {
            $response = Http::withToken($settings->line_notify_token)
                ->asForm()
                ->post(self::API_URL, [
                    'message' => $message,
                ]);

            $success = $response->successful();

            // Log the notification
            LineNotificationLog::create([
                'notification_date' => $targetDate->format('Y-m-d'),
                'send_type' => $sendType,
                'status' => $success ? 'success' : 'failed',
                'message_sent' => $message,
                'error_message' => $success ? null : $response->body(),
                'events_count' => $eventsCount,
                'sent_by' => $sentBy,
            ]);

            if ($success) {
                return ['success' => true, 'message' => 'ส่งแจ้งเตือน LINE สำเร็จ'];
            }

            return ['success' => false, 'message' => 'ส่งแจ้งเตือนไม่สำเร็จ: ' . $response->body()];
        } catch (\Exception $e) {
            Log::error('LINE Notify Error: ' . $e->getMessage());

            LineNotificationLog::create([
                'notification_date' => $targetDate->format('Y-m-d'),
                'send_type' => $sendType,
                'status' => 'failed',
                'message_sent' => $message,
                'error_message' => $e->getMessage(),
                'events_count' => $eventsCount,
                'sent_by' => $sentBy,
            ]);

            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    /**
     * Build the notification message
     */
    private function buildMessage(Carbon $date, LineNotificationSetting $settings): string
    {
        $template = $settings->message_template ?: LineNotificationSetting::defaultTemplate();

        // Get all staff with events for this date
        $staffWithEvents = Staff::active()
            ->ordered()
            ->with(['calendarEvents' => function ($query) use ($date) {
                $query->forDate($date)->orderByTime();
            }])
            ->get();

        $thaiDate = $date->locale('th')->translatedFormat('l ที่ j F') . ' พ.ศ. ' . ($date->year + 543);

        $eventsText = '';
        $totalEvents = 0;

        foreach ($staffWithEvents as $staff) {
            if ($staff->calendarEvents->isEmpty()) {
                continue;
            }

            $eventsText .= "\n👤 {$staff->name}";
            if ($staff->position) {
                $eventsText .= " ({$staff->position})";
            }
            $eventsText .= "\n";

            foreach ($staff->calendarEvents as $event) {
                $totalEvents++;
                $timeRange = $event->time_range;
                $eventsText .= "  ⏰ {$timeRange}\n";
                $eventsText .= "  📌 {$event->title}\n";
                $eventsText .= "  📍 {$event->location}\n";
                if ($event->organization) {
                    $eventsText .= "  🏢 {$event->organization}\n";
                }
            }
        }

        if ($totalEvents === 0) {
            $eventsText = "\n🔕 ไม่มีกิจกรรมในวันนี้";
        }

        $message = str_replace(
            ['{date}', '{events}', '{total}'],
            [$thaiDate, $eventsText, $totalEvents],
            $template
        );

        return "\n" . $message;
    }

    /**
     * Get events count for a date
     */
    private function getEventsCount(Carbon $date): int
    {
        return CalendarEvent::forDate($date)->count();
    }

    /**
     * Test the LINE Notify token
     */
    public function testToken(string $token): array
    {
        try {
            $response = Http::withToken($token)
                ->asForm()
                ->post(self::API_URL, [
                    'message' => "\n🔔 ทดสอบการเชื่อมต่อ LINE Notify\n✅ เชื่อมต่อสำเร็จ!\n📅 ระบบปฏิทินการปฏิบัติงาน",
                ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'ทดสอบ Token สำเร็จ! เชื่อมต่อ LINE Notify ได้แล้ว'];
            }

            return ['success' => false, 'message' => 'Token ไม่ถูกต้อง: ' . $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }
}
