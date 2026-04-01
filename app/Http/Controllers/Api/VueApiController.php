<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use App\Models\Staff;
use App\Models\LineNotificationLog;
use App\Models\LineNotificationSetting;
use App\Services\LineNotifyService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class VueApiController extends Controller
{
    // ─────────────────────────────────────────────
    // CALENDAR VIEW
    // ─────────────────────────────────────────────

    public function calendarData(Request $request): JsonResponse
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : today();
        $filterStaff = $request->input('staff', '');

        $staffWithEvents = Staff::active()
            ->ordered()
            ->when($filterStaff, fn($q) => $q->where('id', $filterStaff))
            ->with(['calendarEvents' => function ($q) use ($date) {
                $q->forDate($date)->orderByTime();
            }])
            ->get()
            ->map(function ($staff) {
                return [
                    'id'       => $staff->id,
                    'name'     => $staff->name,
                    'position' => $staff->position,
                    'photo_url' => $staff->photo_url,
                    'events'   => $staff->calendarEvents->map(fn($e) => $this->formatEvent($e))->values(),
                ];
            });

        $allStaff = Staff::active()->ordered()->get(['id', 'name']);

        $totalEvents = CalendarEvent::forDate($date)
            ->when($filterStaff, fn($q) => $q->where('staff_id', $filterStaff))
            ->count();

        $thaiDate = Carbon::parse($date)->locale('th')->translatedFormat('l ที่ j F พ.ศ. ') .
                    (Carbon::parse($date)->year + 543);

        return response()->json([
            'date'            => $date->format('Y-m-d'),
            'formatted_date'  => $thaiDate,
            'is_today'        => $date->isToday(),
            'total_events'    => $totalEvents,
            'staff_with_events' => $staffWithEvents,
            'all_staff'       => $allStaff,
        ]);
    }

    public function calendarEventDetail($id): JsonResponse
    {
        $event = CalendarEvent::with('staff')->findOrFail($id);
        return response()->json($this->formatEvent($event, true));
    }

    // ─────────────────────────────────────────────
    // STAFF MANAGEMENT
    // ─────────────────────────────────────────────

    public function staffList(Request $request): JsonResponse
    {
        $search  = $request->input('search', '');
        $perPage = (int) $request->input('per_page', 10);

        $staff = Staff::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            })
            ->ordered()
            ->paginate($perPage);

        $totalActive   = Staff::where('is_active', true)->count();
        $totalInactive = Staff::where('is_active', false)->count();

        return response()->json([
            'data'           => $staff->map(fn($s) => $this->formatStaff($s)),
            'total'          => $staff->total(),
            'per_page'       => $staff->perPage(),
            'current_page'   => $staff->currentPage(),
            'last_page'      => $staff->lastPage(),
            'total_active'   => $totalActive,
            'total_inactive' => $totalInactive,
        ]);
    }

    public function staffStore(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|max:255',
            'position'   => 'required|max:255',
            'department' => 'nullable|max:255',
            'photo'      => 'nullable|image|max:2048',
            'is_active'  => 'boolean',
            'sort_order' => 'integer|min:0',
        ], [
            'name.required'     => 'กรุณากรอกชื่อ-นามสกุล',
            'position.required' => 'กรุณากรอกตำแหน่ง',
            'photo.image'       => 'ไฟล์ต้องเป็นรูปภาพ',
            'photo.max'         => 'ขนาดรูปภาพต้องไม่เกิน 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'position', 'department', 'is_active', 'sort_order']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $staff = Staff::create($data);

        return response()->json(['message' => 'เพิ่มผู้ปฏิบัติงานสำเร็จ', 'staff' => $this->formatStaff($staff)], 201);
    }

    public function staffUpdate(Request $request, $id): JsonResponse
    {
        $staff = Staff::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'       => 'required|max:255',
            'position'   => 'required|max:255',
            'department' => 'nullable|max:255',
            'photo'      => 'nullable|image|max:2048',
            'is_active'  => 'boolean',
            'sort_order' => 'integer|min:0',
        ], [
            'name.required'     => 'กรุณากรอกชื่อ-นามสกุล',
            'position.required' => 'กรุณากรอกตำแหน่ง',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'position', 'department', 'is_active', 'sort_order']);

        if ($request->hasFile('photo')) {
            if ($staff->photo) {
                $old = public_path($staff->photo);
                if (File::exists($old)) File::delete($old);
            }
            $data['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $staff->update($data);

        return response()->json(['message' => 'แก้ไขข้อมูลผู้ปฏิบัติงานสำเร็จ', 'staff' => $this->formatStaff($staff->fresh())]);
    }

    public function staffDestroy($id): JsonResponse
    {
        $staff = Staff::findOrFail($id);

        if ($staff->photo) {
            $path = public_path($staff->photo);
            if (File::exists($path)) File::delete($path);
        }

        $staff->delete();

        return response()->json(['message' => 'ลบผู้ปฏิบัติงานสำเร็จ']);
    }

    // ─────────────────────────────────────────────
    // EVENT MANAGEMENT
    // ─────────────────────────────────────────────

    public function eventList(Request $request): JsonResponse
    {
        $search       = $request->input('search', '');
        $filterDate   = $request->input('filter_date', '');
        $filterStaff  = $request->input('filter_staff', '');
        $filterStatus = $request->input('filter_status', '');
        $perPage      = 10;

        $events = CalendarEvent::with('staff')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('title', 'like', "%{$search}%")
                          ->orWhere('location', 'like', "%{$search}%")
                          ->orWhere('organization', 'like', "%{$search}%");
                });
            })
            ->when($filterDate, fn($q) => $q->whereDate('event_date', $filterDate))
            ->when($filterStaff, fn($q) => $q->where('staff_id', $filterStaff))
            ->when($filterStatus, fn($q) => $q->where('status', $filterStatus))
            ->orderByTime()
            ->paginate($perPage);

        $totalAll     = CalendarEvent::count();
        $totalMonth   = CalendarEvent::whereMonth('event_date', now()->month)->count();
        $totalPending = CalendarEvent::where('status', 'pending')->count();
        $staffList    = Staff::active()->ordered()->get(['id', 'name', 'photo']);

        return response()->json([
            'data'          => $events->map(fn($e) => $this->formatEvent($e, false, true)),
            'total'         => $events->total(),
            'per_page'      => $events->perPage(),
            'current_page'  => $events->currentPage(),
            'last_page'     => $events->lastPage(),
            'total_all'     => $totalAll,
            'total_month'   => $totalMonth,
            'total_pending' => $totalPending,
            'staff_list'    => $staffList->map(fn($s) => [
                'id'        => $s->id,
                'name'      => $s->name,
                'photo_url' => $s->photo_url,
            ]),
        ]);
    }

    public function eventStore(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'staff_id'    => 'required|exists:staff,id',
            'event_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:event_date',
            'start_time'  => 'required',
            'end_time'    => 'nullable',
            'title'       => 'required|max:255',
            'description' => 'nullable',
            'location'    => 'required|max:255',
            'organization'=> 'nullable|max:255',
            'status'      => 'required|in:pending,confirmed,cancelled',
        ], [
            'staff_id.required'   => 'กรุณาเลือกผู้ปฏิบัติงาน',
            'event_date.required' => 'กรุณาระบุวันที่เริ่มต้น',
            'start_time.required' => 'กรุณาระบุเวลาเริ่ม',
            'title.required'      => 'กรุณากรอกรายการงาน',
            'location.required'   => 'กรุณากรอกสถานที่',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $startDate = Carbon::parse($request->event_date);
        $endDate   = $request->end_date ? Carbon::parse($request->end_date) : $startDate;
        $days      = $startDate->diffInDays($endDate) + 1;

        for ($i = 0; $i < $days; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            CalendarEvent::create([
                'staff_id'     => $request->staff_id,
                'event_date'   => $currentDate->format('Y-m-d'),
                'start_time'   => $request->start_time,
                'end_time'     => $request->end_time ?: null,
                'title'        => $request->title,
                'description'  => $request->description,
                'location'     => $request->location,
                'organization' => $request->organization,
                'status'       => $request->status,
                'created_by'   => auth()->id(),
            ]);
        }

        return response()->json(['message' => "เพิ่มกิจกรรมสำเร็จ ({$days} วัน)"], 201);
    }

    public function eventUpdate(Request $request, $id): JsonResponse
    {
        $event = CalendarEvent::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'staff_id'    => 'required|exists:staff,id',
            'event_date'  => 'required|date',
            'start_time'  => 'required',
            'end_time'    => 'nullable',
            'title'       => 'required|max:255',
            'description' => 'nullable',
            'location'    => 'required|max:255',
            'organization'=> 'nullable|max:255',
            'status'      => 'required|in:pending,confirmed,cancelled',
        ], [
            'staff_id.required'   => 'กรุณาเลือกผู้ปฏิบัติงาน',
            'event_date.required' => 'กรุณาระบุวันที่',
            'start_time.required' => 'กรุณาระบุเวลาเริ่ม',
            'title.required'      => 'กรุณากรอกรายการงาน',
            'location.required'   => 'กรุณากรอกสถานที่',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $event->update([
            'staff_id'     => $request->staff_id,
            'event_date'   => $request->event_date,
            'start_time'   => $request->start_time,
            'end_time'     => $request->end_time ?: null,
            'title'        => $request->title,
            'description'  => $request->description,
            'location'     => $request->location,
            'organization' => $request->organization,
            'status'       => $request->status,
        ]);

        return response()->json(['message' => 'แก้ไขกิจกรรมสำเร็จ']);
    }

    public function eventDestroy($id): JsonResponse
    {
        CalendarEvent::findOrFail($id)->delete();
        return response()->json(['message' => 'ลบกิจกรรมสำเร็จ']);
    }

    // ─────────────────────────────────────────────
    // LINE NOTIFY
    // ─────────────────────────────────────────────

    public function lineNotifyData(Request $request): JsonResponse
    {
        $settings  = LineNotificationSetting::instance();
        $sendDate  = $request->input('send_date', now()->format('Y-m-d'));

        $todayEvents = CalendarEvent::forDate(Carbon::parse($sendDate))
            ->with('staff')
            ->orderByTime()
            ->get()
            ->map(fn($e) => $this->formatEvent($e));

        $logs = LineNotificationLog::orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'settings'     => $this->formatSettings($settings),
            'today_events' => $todayEvents->groupBy('staff_id'),
            'today_events_flat' => $todayEvents,
            'logs'         => [
                'data'         => $logs->map(fn($l) => $this->formatLog($l)),
                'total'        => $logs->total(),
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
            ],
        ]);
    }

    public function lineNotifyEventsForDate(Request $request): JsonResponse
    {
        $sendDate = $request->input('send_date', now()->format('Y-m-d'));

        $todayEvents = CalendarEvent::forDate(Carbon::parse($sendDate))
            ->with('staff')
            ->orderByTime()
            ->get()
            ->map(fn($e) => $this->formatEvent($e));

        $grouped = $todayEvents->groupBy('staff_id')->map(function ($events) {
            $first = $events->first();
            return [
                'staff_id'   => $first['staff_id'],
                'staff_name' => $first['staff_name'],
                'staff_photo_url' => $first['staff_photo_url'] ?? null,
                'staff_position' => $first['staff_position'] ?? null,
                'events'     => $events->values(),
            ];
        })->values();

        return response()->json([
            'events'  => $todayEvents,
            'grouped' => $grouped,
            'count'   => $todayEvents->count(),
        ]);
    }

    public function lineNotifyToggleEnabled(): JsonResponse
    {
        $settings = LineNotificationSetting::instance();
        $settings->update(['is_enabled' => !$settings->is_enabled]);
        $msg = $settings->fresh()->is_enabled ? 'เปิดการแจ้งเตือน LINE แล้ว' : 'ปิดการแจ้งเตือน LINE แล้ว';
        return response()->json(['message' => $msg, 'is_enabled' => $settings->fresh()->is_enabled]);
    }

    public function lineNotifyToggleSchedule(): JsonResponse
    {
        $settings = LineNotificationSetting::instance();
        $settings->update(['schedule_enabled' => !$settings->schedule_enabled]);
        $fresh = $settings->fresh();
        $msg   = $fresh->schedule_enabled ? 'เปิดการส่งอัตโนมัติแล้ว' : 'ปิดการส่งอัตโนมัติแล้ว';
        return response()->json(['message' => $msg, 'schedule_enabled' => $fresh->schedule_enabled]);
    }

    public function lineNotifySaveSettings(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel_access_token' => 'nullable|string',
            'send_mode'            => 'required|in:broadcast,push',
            'destination_id'       => 'nullable|string',
            'destination_name'     => 'nullable|string|max:255',
            'schedule_time'        => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $settings = LineNotificationSetting::instance();
        $settings->update([
            'channel_access_token' => $request->channel_access_token ?: null,
            'is_enabled'           => $request->boolean('is_enabled'),
            'send_mode'            => $request->send_mode,
            'destination_id'       => $request->destination_id ?: null,
            'destination_name'     => $request->destination_name ?: null,
            'schedule_enabled'     => $request->boolean('schedule_enabled'),
            'schedule_time'        => $request->schedule_time,
            'message_template'     => $request->message_template ?: LineNotificationSetting::defaultTemplate(),
        ]);

        return response()->json(['message' => 'บันทึกการตั้งค่าสำเร็จ', 'settings' => $this->formatSettings($settings->fresh())]);
    }

    public function lineNotifyTestToken(Request $request): JsonResponse
    {
        $token = $request->input('token', '');
        if (empty($token)) {
            return response()->json(['success' => false, 'message' => 'กรุณากรอก Channel Access Token ก่อน'], 422);
        }
        $service = new LineNotifyService();
        $result  = $service->testToken($token);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function lineNotifySendTest(Request $request): JsonResponse
    {
        $settings = LineNotificationSetting::instance();
        $settings->update([
            'channel_access_token' => $request->input('channel_access_token') ?: $settings->channel_access_token,
            'send_mode'            => $request->input('send_mode', $settings->send_mode),
            'destination_id'       => $request->input('destination_id') ?: $settings->destination_id,
        ]);
        $service = new LineNotifyService();
        $result  = $service->sendTestMessage($settings->fresh());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function lineNotifySendNow(Request $request): JsonResponse
    {
        $sendDate = $request->input('send_date', '');
        if (empty($sendDate)) {
            return response()->json(['success' => false, 'message' => 'กรุณาเลือกวันที่ก่อนส่ง'], 422);
        }
        $service = new LineNotifyService();
        $result  = $service->sendForDate($sendDate, 'manual', auth()->id());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function lineNotifyDeleteLog($id): JsonResponse
    {
        LineNotificationLog::findOrFail($id)->delete();
        return response()->json(['message' => 'ลบ Log สำเร็จ']);
    }

    public function lineNotifyLogs(Request $request): JsonResponse
    {
        $logs = LineNotificationLog::orderBy('created_at', 'desc')->paginate(10);
        return response()->json([
            'data'         => $logs->map(fn($l) => $this->formatLog($l)),
            'total'        => $logs->total(),
            'current_page' => $logs->currentPage(),
            'last_page'    => $logs->lastPage(),
        ]);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    private function formatEvent($event, bool $withStaffDetail = false, bool $withStaffBasic = false): array
    {
        $data = [
            'id'           => $event->id,
            'staff_id'     => $event->staff_id,
            'title'        => $event->title,
            'description'  => $event->description,
            'location'     => $event->location,
            'organization' => $event->organization,
            'event_date'   => $event->event_date->format('Y-m-d'),
            'event_date_display' => $event->event_date->translatedFormat('d M Y'),
            'event_month'  => $event->event_date->translatedFormat('M Y'),
            'start_time'   => $event->start_time ? Carbon::parse($event->start_time)->format('H:i') : null,
            'end_time'     => $event->end_time ? Carbon::parse($event->end_time)->format('H:i') : null,
            'time_range'   => $event->time_range,
            'status'       => $event->status,
            'status_label' => $event->status_label,
            'status_color' => match($event->status) {
                'confirmed' => 'emerald',
                'pending'   => 'amber',
                'cancelled' => 'rose',
                default     => 'indigo',
            },
        ];

        if ($withStaffDetail && $event->relationLoaded('staff') && $event->staff) {
            $data['staff'] = [
                'id'        => $event->staff->id,
                'name'      => $event->staff->name,
                'position'  => $event->staff->position,
                'photo_url' => $event->staff->photo_url,
            ];
            $data['staff_name']      = $event->staff->name;
            $data['staff_position']  = $event->staff->position;
            $data['staff_photo_url'] = $event->staff->photo_url;
        }

        if ($withStaffBasic && $event->relationLoaded('staff') && $event->staff) {
            $data['staff_name']      = $event->staff->name;
            $data['staff_position']  = $event->staff->position;
            $data['staff_photo_url'] = $event->staff->photo_url;
        }

        if ($event->relationLoaded('staff') && $event->staff) {
            $data['staff_name']      = $event->staff->name;
            $data['staff_position']  = $event->staff->position ?? null;
            $data['staff_photo_url'] = $event->staff->photo_url ?? null;
        }

        return $data;
    }

    private function formatStaff($staff): array
    {
        return [
            'id'         => $staff->id,
            'name'       => $staff->name,
            'position'   => $staff->position,
            'department' => $staff->department,
            'photo'      => $staff->photo,
            'photo_url'  => $staff->photo_url,
            'is_active'  => $staff->is_active,
            'sort_order' => $staff->sort_order,
            'email'      => $staff->email ?? null,
        ];
    }

    private function formatSettings($settings): array
    {
        return [
            'channel_access_token' => $settings->channel_access_token,
            'is_enabled'           => (bool) $settings->is_enabled,
            'send_mode'            => $settings->send_mode ?? 'broadcast',
            'destination_id'       => $settings->destination_id,
            'destination_name'     => $settings->destination_name,
            'schedule_enabled'     => (bool) $settings->schedule_enabled,
            'schedule_time'        => $settings->schedule_time ? Carbon::parse($settings->schedule_time)->format('H:i') : '07:00',
            'message_template'     => $settings->message_template ?? LineNotificationSetting::defaultTemplate(),
        ];
    }

    private function formatLog($log): array
    {
        return [
            'id'                => $log->id,
            'created_at'        => $log->created_at->format('d/m/Y H:i'),
            'notification_date' => $log->notification_date ? $log->notification_date->locale('th')->translatedFormat('j M Y') : '-',
            'send_type'         => $log->send_type,
            'send_type_label'   => $log->send_type_label,
            'status'            => $log->status,
            'status_label'      => $log->status_label,
            'events_count'      => $log->events_count,
            'sender_name'       => $log->sender ? $log->sender->name : 'ระบบ',
        ];
    }

    private function uploadPhoto($file): string
    {
        $uploadPath = public_path('uploads/staff-photos');
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }
        $ext      = $file->getClientOriginalExtension();
        $name     = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '', str_replace(' ', '_', $name));
        $filename = time() . '_' . $safeName . '.' . $ext;
        File::copy($file->getRealPath(), $uploadPath . '/' . $filename);
        return 'uploads/staff-photos/' . $filename;
    }
}
