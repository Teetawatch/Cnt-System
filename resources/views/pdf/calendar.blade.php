<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ปฏิทินการปฏิบัติงาน - {{ $date->format('d/m/Y') }}</title>
    <style>
        @font-face {
            font-family: 'sarabun';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'sarabun';
            font-style: normal;
            font-weight: bold;
            src: url("{{ storage_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'sarabun', 'THSarabunNew', sans-serif;
            font-size: 16pt;
            line-height: 1.4;
            color: #333;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            font-size: 26pt;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .header .date {
            font-size: 20pt;
            color: #374151;
            font-weight: bold;
        }

        .filter-info {
            text-align: center;
            font-size: 14pt;
            color: #6b7280;
            margin-bottom: 15px;
            padding: 8px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
        }

        /* Staff Section */
        .staff-section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .staff-header {
            background: #1e3a5f;
            color: white;
            padding: 12px 15px;
            margin-bottom: 0;
        }

        .staff-name {
            font-size: 18pt;
            font-weight: bold;
        }

        .staff-position {
            font-size: 14pt;
            color: #fbbf24;
        }

        .event-count {
            float: right;
            background: #f59e0b;
            color: white;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12pt;
            font-weight: bold;
        }

        /* Events Table */
        .events-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .events-table th {
            background: #f3f4f6;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #d1d5db;
            font-size: 14pt;
            color: #374151;
        }

        .events-table td {
            padding: 10px 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
            font-size: 14pt;
        }

        .time-cell {
            width: 90px;
            text-align: center;
            font-weight: bold;
            color: #1e40af;
            background: #f8fafc;
        }

        .title-cell {
            font-weight: bold;
            color: #111827;
        }

        .location {
            color: #4b5563;
            font-size: 13pt;
        }

        .organization {
            color: #6b7280;
            font-size: 12pt;
        }

        .status-cell {
            width: 80px;
            text-align: center;
        }

        .status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 11pt;
            font-weight: bold;
        }

        .status-confirmed {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .no-events {
            text-align: center;
            padding: 25px;
            color: #9ca3af;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            color: #6b7280;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 30px;
            right: 30px;
            text-align: center;
            font-size: 11pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        .page-number:after {
            content: counter(page);
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>ปฏิทินการปฏิบัติงาน</h1>
        <div class="date">วัน{{ $thaiDate }}</div>
    </div>

    <!-- Filter Info -->
    @if($filterStaff)
        <div class="filter-info">
            แสดงเฉพาะ: <strong>{{ $filterStaff->name }}</strong> ({{ $filterStaff->position }})
        </div>
    @endif

    <!-- Staff Sections -->
    @forelse($staffWithEvents as $staff)
        <div class="staff-section">
            <!-- Staff Header -->
            <div class="staff-header clearfix">
                <span class="event-count">{{ $staff->calendarEvents->count() }} กิจกรรม</span>
                <span class="staff-name">{{ $staff->name }}</span>
                <span class="staff-position"> - {{ $staff->position }}</span>
            </div>

            <!-- Events Table -->
            @if($staff->calendarEvents->count() > 0)
                <table class="events-table">
                    <thead>
                        <tr>
                            <th style="width: 120px;">เวลา</th>
                            <th>รายการ / สถานที่</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff->calendarEvents as $event)
                            <tr>
                                <td class="time-cell">
                                    {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}@if($event->end_time) - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}@endif น.
                                </td>
                                <td>
                                    <div class="title-cell">{{ $event->title }}</div>
                                    <div class="location">สถานที่: {{ $event->location }}</div>
                                    @if($event->organization)
                                        <div class="organization">หน่วยงาน: {{ $event->organization }}</div>
                                    @endif
                                    @if($event->description)
                                        <div class="organization">หมายเหตุ: {{ $event->description }}</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-events">
                    ไม่มีกิจกรรมในวันนี้
                </div>
            @endif
        </div>
    @empty
        <div class="empty-state">
            <p style="font-size: 18pt;">ไม่พบข้อมูลผู้ปฏิบัติงาน</p>
        </div>
    @endforelse

    <!-- Footer -->
    <div class="footer">
        พิมพ์เมื่อ {{ now()->locale('th')->translatedFormat('j F Y') }} เวลา {{ now()->format('H:i') }} น. &nbsp;|&nbsp; หน้า <span class="page-number"></span>
    </div>
</body>
</html>
