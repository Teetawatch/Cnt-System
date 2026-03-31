<?php

namespace App\Observers;

use App\Models\CalendarEvent;
use App\Services\FcmService;
use Illuminate\Support\Facades\Log;

class CalendarEventObserver
{
    public function __construct(private FcmService $fcmService) {}

    /**
     * Handle the CalendarEvent "created" event.
     */
    public function created(CalendarEvent $event): void
    {
        try {
            $staff = $event->staff;
            $staffName = $staff?->name ?? 'ไม่ระบุ';

            $thaiMonths = [
                1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
                5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
                9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.',
            ];
            $eventDate = \Carbon\Carbon::parse($event->event_date);
            $day = $eventDate->day;
            $month = $thaiMonths[$eventDate->month];
            $year = $eventDate->year + 543;
            $thaiDate = "{$day} {$month} {$year}";

            $timeRange = '';
            if ($event->start_time) {
                $start = \Carbon\Carbon::parse($event->start_time)->format('H:i');
                $end = $event->end_time
                    ? \Carbon\Carbon::parse($event->end_time)->format('H:i')
                    : null;
                $timeRange = $end ? " เวลา {$start}-{$end} น." : " เวลา {$start} น.";
            }

            $title = '📅 กิจกรรมใหม่ · ' . $staffName;
            $body = $event->title . "\n"
                . '📆 ' . $thaiDate . $timeRange . "\n"
                . ($event->location ? '📍 ' . $event->location : '');

            $this->fcmService->sendToAll($title, $body, [
                'type'     => 'new_event',
                'event_id' => (string) $event->id,
                'staff_id' => (string) $event->staff_id,
                'date'     => $event->event_date->format('Y-m-d'),
            ]);

            Log::info('[FCM Observer] Notification sent for event', ['id' => $event->id]);
        } catch (\Exception $e) {
            Log::error('[FCM Observer] Failed: ' . $e->getMessage());
        }
    }
}
