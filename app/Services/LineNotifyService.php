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
    private const PUSH_API = 'https://api.line.me/v2/bot/message/push';
    private const BROADCAST_API = 'https://api.line.me/v2/bot/message/broadcast';

    /**
     * Send notification for a specific date
     */
    public function sendForDate(string $date, string $sendType = 'manual', ?int $sentBy = null): array
    {
        $settings = LineNotificationSetting::instance();

        if (!$settings->is_enabled) {
            return ['success' => false, 'message' => 'การแจ้งเตือน LINE ถูกปิดอยู่'];
        }

        if (empty($settings->channel_access_token)) {
            return ['success' => false, 'message' => 'ยังไม่ได้ตั้งค่า Channel Access Token'];
        }

        if ($settings->send_mode === 'push' && empty($settings->destination_id)) {
            return ['success' => false, 'message' => 'ยังไม่ได้ตั้งค่า Destination ID (User ID หรือ Group ID)'];
        }

        $targetDate = Carbon::parse($date);
        $messageText = $this->buildMessage($targetDate, $settings);
        $eventsCount = $this->getEventsCount($targetDate);

        try {
            // Build Messaging API request
            $messages = $this->splitMessages($messageText);

            if ($settings->send_mode === 'push') {
                $response = Http::withToken($settings->channel_access_token)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post(self::PUSH_API, [
                        'to' => $settings->destination_id,
                        'messages' => $messages,
                    ]);
            } else {
                // Broadcast to all friends
                $response = Http::withToken($settings->channel_access_token)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post(self::BROADCAST_API, [
                        'messages' => $messages,
                    ]);
            }

            $success = $response->successful();

            // Log the notification
            LineNotificationLog::create([
                'notification_date' => $targetDate->format('Y-m-d'),
                'send_type' => $sendType,
                'status' => $success ? 'success' : 'failed',
                'message_sent' => $messageText,
                'error_message' => $success ? null : $response->body(),
                'events_count' => $eventsCount,
                'sent_by' => $sentBy,
            ]);

            if ($success) {
                return ['success' => true, 'message' => 'ส่งแจ้งเตือน LINE สำเร็จ'];
            }

            $errorBody = json_decode($response->body(), true);
            $errorMsg = $errorBody['message'] ?? $response->body();
            return ['success' => false, 'message' => 'ส่งแจ้งเตือนไม่สำเร็จ: ' . $errorMsg];
        } catch (\Exception $e) {
            Log::error('LINE Messaging API Error: ' . $e->getMessage());

            LineNotificationLog::create([
                'notification_date' => $targetDate->format('Y-m-d'),
                'send_type' => $sendType,
                'status' => 'failed',
                'message_sent' => $messageText,
                'error_message' => $e->getMessage(),
                'events_count' => $eventsCount,
                'sent_by' => $sentBy,
            ]);

            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    /**
     * Split long message into multiple LINE message objects (max 5000 chars each, max 5 messages)
     */
    private function splitMessages(string $text): array
    {
        $maxLength = 5000;
        $messages = [];

        if (mb_strlen($text) <= $maxLength) {
            return [['type' => 'text', 'text' => $text]];
        }

        // Split by staff sections
        $chunks = str_split($text, $maxLength);
        foreach (array_slice($chunks, 0, 5) as $chunk) { // LINE allows max 5 messages
            $messages[] = ['type' => 'text', 'text' => $chunk];
        }

        return $messages;
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

        return $message;
    }

    /**
     * Get events count for a date
     */
    private function getEventsCount(Carbon $date): int
    {
        return CalendarEvent::forDate($date)->count();
    }

    /**
     * Test the Channel Access Token by calling the bot info endpoint
     */
    public function testToken(string $token): array
    {
        try {
            // Verify token by getting bot info
            $response = Http::withToken($token)
                ->get('https://api.line.me/v2/bot/info');

            if ($response->successful()) {
                $botInfo = $response->json();
                $botName = $botInfo['displayName'] ?? 'Unknown';
                return [
                    'success' => true,
                    'message' => "เชื่อมต่อ Bot สำเร็จ! ชื่อ Bot: {$botName}",
                    'bot_name' => $botName,
                ];
            }

            $errorBody = json_decode($response->body(), true);
            $errorMsg = $errorBody['message'] ?? $response->body();
            return ['success' => false, 'message' => 'Token ไม่ถูกต้อง: ' . $errorMsg];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    /**
     * Send a test message to verify everything works
     */
    public function sendTestMessage(LineNotificationSetting $settings): array
    {
        if (empty($settings->channel_access_token)) {
            return ['success' => false, 'message' => 'ยังไม่ได้ตั้งค่า Channel Access Token'];
        }

        $testMessage = "🔔 ทดสอบการเชื่อมต่อ\n✅ เชื่อมต่อ LINE Messaging API สำเร็จ!\n📅 ระบบปฏิทินการปฏิบัติงาน";

        try {
            $body = [
                'messages' => [['type' => 'text', 'text' => $testMessage]],
            ];

            if ($settings->send_mode === 'push') {
                if (empty($settings->destination_id)) {
                    return ['success' => false, 'message' => 'กรุณากรอก Destination ID ก่อน'];
                }
                $body['to'] = $settings->destination_id;
                $url = self::PUSH_API;
            } else {
                $url = self::BROADCAST_API;
            }

            $response = Http::withToken($settings->channel_access_token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $body);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'ส่งข้อความทดสอบสำเร็จ! ตรวจสอบใน LINE ได้เลย'];
            }

            $errorBody = json_decode($response->body(), true);
            $errorMsg = $errorBody['message'] ?? $response->body();
            return ['success' => false, 'message' => 'ส่งไม่สำเร็จ: ' . $errorMsg];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }
}
