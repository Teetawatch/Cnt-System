<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileApiController extends Controller
{
    /**
     * Get dashboard data: today's summary + staff with events for a given date
     */
    public function dashboard(Request $request): JsonResponse
    {
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))
            : today();

        $staffList = Staff::active()
            ->ordered()
            ->with(['calendarEvents' => function ($query) use ($date) {
                $query->where(function ($q) use ($date) {
                    $q->whereDate('event_date', $date)
                      ->orWhere(function ($sq) use ($date) {
                          $sq->whereDate('event_date', '<=', $date)
                             ->whereDate('end_date', '>=', $date);
                      });
                })->orderByTime();
            }])
            ->get();

        $totalEvents = 0;
        $confirmedEvents = 0;
        $pendingEvents = 0;
        $cancelledEvents = 0;

        $staffData = [];

        foreach ($staffList as $staff) {
            $events = $staff->calendarEvents;
            $totalEvents += $events->count();
            $confirmedEvents += $events->where('status', 'confirmed')->count();
            $pendingEvents += $events->where('status', 'pending')->count();
            $cancelledEvents += $events->where('status', 'cancelled')->count();

            $staffData[] = [
                'id' => $staff->id,
                'name' => $staff->name,
                'position' => $staff->position,
                'department' => $staff->department,
                'description' => $staff->description,
                'photo_url' => $staff->photo_url,
                'events' => $events->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'description' => $event->description,
                        'remark' => $event->remark,
                        'location' => $event->location,
                        'organization' => $event->organization,
                        'event_date' => $event->event_date->format('Y-m-d'),
                        'end_date' => $event->end_date?->format('Y-m-d'),
                        'start_time' => $event->start_time ? Carbon::parse($event->start_time)->format('H:i') : null,
                        'end_time' => $event->end_time ? Carbon::parse($event->end_time)->format('H:i') : null,
                        'status' => $event->status,
                        'status_label' => $event->status_label,
                        'time_range' => $event->time_range,
                    ];
                })->values(),
            ];
        }

        return response()->json([
            'date' => $date->format('Y-m-d'),
            'thai_date' => $this->formatThaiDate($date),
            'summary' => [
                'total_events' => $totalEvents,
                'confirmed' => $confirmedEvents,
                'pending' => $pendingEvents,
                'cancelled' => $cancelledEvents,
                'total_staff' => $staffList->count(),
                'staff_with_events' => $staffList->filter(fn($s) => $s->calendarEvents->isNotEmpty())->count(),
            ],
            'staff' => $staffData,
        ]);
    }

    /**
     * Get staff list
     */
    public function staffList(): JsonResponse
    {
        $staff = Staff::active()
            ->ordered()
            ->withCount('calendarEvents')
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'position' => $s->position,
                    'department' => $s->department,
                    'description' => $s->description,
                    'photo_url' => $s->photo_url,
                    'events_count' => $s->calendar_events_count,
                ];
            });

        return response()->json(['staff' => $staff]);
    }

    /**
     * Get events for a specific staff member
     */
    public function staffEvents(Request $request, int $staffId): JsonResponse
    {
        $staff = Staff::findOrFail($staffId);

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $events = CalendarEvent::where('staff_id', $staffId)
            ->dateRange($startDate, $endDate)
            ->orderByTime()
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'remark' => $event->remark,
                    'location' => $event->location,
                    'organization' => $event->organization,
                    'event_date' => $event->event_date->format('Y-m-d'),
                    'end_date' => $event->end_date?->format('Y-m-d'),
                    'start_time' => $event->start_time ? Carbon::parse($event->start_time)->format('H:i') : null,
                    'end_time' => $event->end_time ? Carbon::parse($event->end_time)->format('H:i') : null,
                    'status' => $event->status,
                    'status_label' => $event->status_label,
                    'time_range' => $event->time_range,
                ];
            });

        return response()->json([
            'staff' => [
                'id' => $staff->id,
                'name' => $staff->name,
                'position' => $staff->position,
                'department' => $staff->department,
                'photo_url' => $staff->photo_url,
            ],
            'month' => $month,
            'year' => $year,
            'events' => $events,
        ]);
    }

    /**
     * Get monthly stats for reporting
     */
    public function stats(Request $request): JsonResponse
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $events = CalendarEvent::dateRange($startDate, $endDate)->get();

        $totalEvents = $events->count();
        $confirmedEvents = $events->where('status', 'confirmed')->count();
        $pendingEvents = $events->where('status', 'pending')->count();
        $cancelledEvents = $events->where('status', 'cancelled')->count();

        // Group by date for daily breakdown
        $dailyData = [];
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dateStr = $current->format('Y-m-d');
            $dayEvents = $events->filter(function ($e) use ($current) {
                $eventDate = $e->event_date;
                $endDate = $e->end_date ?? $e->event_date;
                return $current->between($eventDate, $endDate);
            });

            $dailyData[] = [
                'date' => $dateStr,
                'day' => $current->day,
                'day_name' => $this->getThaiDayShort($current->dayOfWeek),
                'total' => $dayEvents->count(),
                'confirmed' => $dayEvents->where('status', 'confirmed')->count(),
                'pending' => $dayEvents->where('status', 'pending')->count(),
            ];

            $current->addDay();
        }

        // Staff event counts
        $staffStats = Staff::active()
            ->ordered()
            ->withCount(['calendarEvents' => function ($q) use ($startDate, $endDate) {
                $q->dateRange($startDate, $endDate);
            }])
            ->get()
            ->map(function ($s) {
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'position' => $s->position,
                    'photo_url' => $s->photo_url,
                    'events_count' => $s->calendar_events_count,
                ];
            });

        return response()->json([
            'month' => $month,
            'year' => $year,
            'thai_month' => $this->getThaiMonth($month),
            'summary' => [
                'total_events' => $totalEvents,
                'confirmed' => $confirmedEvents,
                'pending' => $pendingEvents,
                'cancelled' => $cancelledEvents,
            ],
            'daily_data' => $dailyData,
            'staff_stats' => $staffStats,
        ]);
    }

    /**
     * Search events
     */
    public function searchEvents(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $staffId = $request->input('staff_id');

        $events = CalendarEvent::with('staff')
            ->when($query, function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('location', 'like', "%{$query}%")
                  ->orWhere('organization', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->when($staffId, function ($q) use ($staffId) {
                $q->where('staff_id', $staffId);
            })
            ->orderByTime()
            ->limit(50)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'location' => $event->location,
                    'organization' => $event->organization,
                    'event_date' => $event->event_date->format('Y-m-d'),
                    'end_date' => $event->end_date?->format('Y-m-d'),
                    'start_time' => $event->start_time ? Carbon::parse($event->start_time)->format('H:i') : null,
                    'end_time' => $event->end_time ? Carbon::parse($event->end_time)->format('H:i') : null,
                    'status' => $event->status,
                    'time_range' => $event->time_range,
                    'staff' => [
                        'id' => $event->staff->id,
                        'name' => $event->staff->name,
                        'position' => $event->staff->position,
                        'photo_url' => $event->staff->photo_url,
                    ],
                ];
            });

        return response()->json(['events' => $events]);
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function formatThaiDate(Carbon $date): string
    {
        $thaiMonths = [
            '', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน',
            'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม',
            'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม',
        ];

        $thaiDays = [
            'อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์',
        ];

        $dayName = $thaiDays[$date->dayOfWeek];
        $day = $date->day;
        $monthName = $thaiMonths[$date->month];
        $buddhistYear = $date->year + 543;

        return "วัน{$dayName}ที่ {$day} {$monthName} พ.ศ. {$buddhistYear}";
    }

    private function getThaiMonth(int $month): string
    {
        $months = [
            '', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน',
            'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม',
            'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม',
        ];
        return $months[$month] ?? '';
    }

    private function getThaiDayShort(int $dayOfWeek): string
    {
        $days = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];
        return $days[$dayOfWeek] ?? '';
    }
}
