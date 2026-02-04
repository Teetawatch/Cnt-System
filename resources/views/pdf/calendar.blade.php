<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ปฏิทินการปฏิบัติงาน</title>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
        }

        @page {
            size: A4 landscape;
            margin: 8mm;
        }
        
        body {
            font-family: "THSarabunNew", sans-serif;
            font-size: 14pt;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 8px;
            padding: 8px;
            background-color: #1e3a8a;
            color: white;
        }
        
        .title {
            font-size: 20pt;
            font-weight: bold;
        }
        
        .subtitle {
            font-size: 12pt;
        }
        
        .date-badge {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 3px;
        }
        
        .stats-table {
            width: 100%;
            margin-bottom: 8px;
            background-color: #f0f9ff;
            border-collapse: collapse;
        }
        
        .stats-table td {
            text-align: center;
            padding: 5px;
            border: 1px solid #bae6fd;
            width: 33.33%;
        }
        
        .stat-number {
            font-size: 18pt;
            font-weight: bold;
            color: #0369a1;
        }
        
        .stat-label {
            font-size: 10pt;
            color: #64748b;
        }
        
        .staff-box {
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 8px;
            margin-bottom: 8px;
        }
        
        .staff-header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 6px;
        }
        
        .staff-header-table td {
            vertical-align: middle;
            padding: 0;
        }
        
        .photo-td {
            width: 60px;
            padding-right: 10px;
        }
        
        .staff-photo {
            width: 55px;
            height: 55px;
        }
        
        .staff-initial-box {
            width: 55px;
            height: 55px;
            background-color: #3b82f6;
            color: white;
            text-align: center;
            font-size: 24pt;
            font-weight: bold;
            line-height: 55px;
        }
        
        .staff-name {
            font-size: 16pt;
            font-weight: bold;
            color: #1e293b;
        }
        
        .staff-position {
            font-size: 12pt;
            color: #64748b;
        }
        
        .events-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .events-table td {
            padding: 5px 8px;
            vertical-align: top;
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .events-table tr:last-child td {
            border-bottom: none;
        }
        
        .event-time-cell {
            width: 100px;
            background-color: #dbeafe;
            font-weight: bold;
            color: #1e40af;
            font-size: 12pt;
            text-align: center;
        }
        
        .event-title {
            font-weight: bold;
            color: #1e293b;
            font-size: 13pt;
        }
        
        .event-meta {
            font-size: 11pt;
            color: #64748b;
        }
        
        .no-events {
            text-align: center;
            padding: 30px;
            color: #94a3b8;
            font-size: 14pt;
        }
        
        .legend-table {
            width: 100%;
            margin-top: 8px;
            border-collapse: collapse;
        }
        
        .legend-table td {
            text-align: center;
            padding: 3px 10px;
            font-size: 10pt;
            color: #64748b;
            width: 25%;
        }
        
        .legend-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            margin-right: 5px;
            vertical-align: middle;
        }
        
        .legend-box.primary { background-color: #3b82f6; }
        .legend-box.success { background-color: #22c55e; }
        .legend-box.warning { background-color: #f59e0b; }
        .legend-box.danger { background-color: #ef4444; }
        
        .footer {
            text-align: center;
            margin-top: 8px;
            padding-top: 5px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 10pt;
        }
        
        .color-bar {
            width: 4px;
            padding: 0;
        }
        
        .color-bar.primary { background-color: #3b82f6; }
        .color-bar.success { background-color: #22c55e; }
        .color-bar.warning { background-color: #f59e0b; }
        .color-bar.danger { background-color: #ef4444; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="title">ปฏิทินการปฏิบัติงานผู้บริหาร</div>
        <div class="date-badge">วัน{{ $selectedDate->locale('th')->translatedFormat('l') }}ที่ {{ $selectedDate->format('j') }} {{ $selectedDate->locale('th')->translatedFormat('F') }} พ.ศ. {{ $selectedDate->year + 543 }}</div>
    </div>

    <!-- Statistics -->
    @php
        $totalEvents = $staffList->pluck('calendarEvents')->flatten()->count();
        $staffWithEventsCount = $staffList->filter(fn($s) => $s->calendarEvents->count() > 0)->count();
    @endphp
    <table class="stats-table">
        <tr>
            <td>
                <div class="stat-number">{{ $staffWithEventsCount }}</div>
                <div class="stat-label">ผู้บริหารมีภารกิจ</div>
            </td>
            <td>
                <div class="stat-number">{{ $totalEvents }}</div>
                <div class="stat-label">กิจกรรมทั้งหมด</div>
            </td>
            <td>
                <div class="stat-number">{{ $staffList->count() }}</div>
                <div class="stat-label">ผู้บริหารทั้งหมด</div>
            </td>
        </tr>
    </table>

    <!-- Staff List - แสดงเป็น Box เรียงลงมา -->
    @php
        $staffWithEvents = $staffList->filter(fn($s) => $s->calendarEvents->count() > 0)->values();
    @endphp

    @if($staffWithEvents->count() > 0)
        @foreach($staffWithEvents as $staff)
            <div class="staff-box">
                <!-- Staff Header -->
                <table class="staff-header-table">
                    <tr>
                        <td class="photo-td">
                            @php
                                $photoPath = null;
                                if ($staff->photo) {
                                    $possiblePaths = [
                                        public_path('uploads/staff-photos/' . $staff->photo),
                                        public_path('uploads/staff-photos/' . basename($staff->photo)),
                                        public_path($staff->photo),
                                        public_path('uploads/' . $staff->photo),
                                    ];
                                    foreach ($possiblePaths as $testPath) {
                                        if (file_exists($testPath)) {
                                            $photoPath = $testPath;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            @if($photoPath)
                                <img src="{{ $photoPath }}" class="staff-photo">
                            @else
                                <div class="staff-initial-box">{{ mb_substr($staff->name, 0, 1) }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="staff-name">{{ $staff->name }}</div>
                            <div class="staff-position">{{ $staff->position }}</div>
                        </td>
                    </tr>
                </table>
                
                <!-- Events Table -->
                <table class="events-table">
                    @foreach($staff->calendarEvents as $event)
                        <tr>
                            <td class="color-bar {{ $event->status_color }}"></td>
                            <td class="event-time-cell">
                                {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}@if($event->end_time)-{{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}@endif น.
                            </td>
                            <td>
                                <div class="event-title">{{ $event->title }}</div>
                                <div class="event-meta">
                                    @if($event->location)สถานที่: {{ $event->location }}@endif
                                    @if($event->organization) | หน่วยงาน: {{ $event->organization }}@endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endforeach
    @else
        <div class="no-events">ไม่มีภารกิจในวันนี้</div>
    @endif


    <!-- Footer -->
    <div class="footer">
        พิมพ์เมื่อ: {{ now()->locale('th')->translatedFormat('j F') }} พ.ศ. {{ now()->year + 543 }} เวลา {{ now()->format('H:i') }} น.
    </div>
</body>
</html>