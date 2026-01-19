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
        
        body {
            font-family: "THSarabunNew", sans-serif;
            font-size: 16pt;
            line-height: 1.2;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .title {
            font-size: 20pt;
            font-weight: bold;
        }
        
        .date {
            font-size: 18pt;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }
        
        th {
            background-color: #f2f2f2;
            text-align: left;
            font-weight: bold;
        }
        
        .staff-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .staff-name {
            font-size: 18pt;
            font-weight: bold;
            color: #000;
            background-color: #e6e6e6;
            padding: 5px 10px;
            margin-bottom: 10px;
        }
        
        .event-time {
            width: 15%;
            font-weight: bold;
            color: #2563eb;
        }
        
        .event-detail {
            width: 65%;
        }
        
        .event-status {
            width: 20%;
            text-align: center;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 14pt;
            color: white;
            background-color: #6b7280;
        }
        .badge-primary { background-color: #3b82f6; } /* นัดหมาย */
        .badge-success { background-color: #22c55e; } /* ประชุม */
        .badge-warning { background-color: #f59e0b; } /* ภารกิจ */
        .badge-danger { background-color: #ef4444; }  /* วันหยุด */
    </style>
</head>
<body>
    <div class="header">
        <div class="title">ปฏิทินการปฏิบัติงานผู้บริหาร</div>
        <div class="date">
            ประจำวัน{{ $selectedDate->locale('th')->translatedFormat('lที่ j F พ.ศ.') }} {{ $selectedDate->year + 543 }}
        </div>
    </div>

    @foreach($staffList as $staff)
        @if($staff->calendarEvents->count() > 0)
            <div class="staff-section">
                <div class="staff-name">
                    {{ $staff->name }} ({{ $staff->position }})
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th style="width: 15%">เวลา</th>
                            <th style="width: 65%">กิจกรรม</th>
                            <th style="width: 20%">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff->calendarEvents as $event)
                            <tr>
                                <td class="event-time">
                                    {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }}
                                    @if($event->end_time)
                                        - {{ \Carbon\Carbon::parse($event->end_time)->format('H:i') }}
                                    @endif
                                    น.
                                </td>
                                <td class="event-detail">
                                    <strong>{{ $event->title }}</strong>
                                    @if($event->location)
                                        <br>
                                        <span style="color: #666;">สถานที่: {{ $event->location }}</span>
                                    @endif
                                    @if($event->organization)
                                        <br>
                                        <span style="color: #666;">หน่วยงาน: {{ $event->organization }}</span>
                                    @endif
                                </td>
                                <td class="event-status">
                                    <span class="badge badge-{{ $event->status_color }}">
                                        {{ $event->status_label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    @if($staffList->pluck('calendarEvents')->flatten()->isEmpty())
        <div style="text-align: center; margin-top: 50px; color: #888;">
            - ไม่มีภารกิจในวันนี้ -
        </div>
    @endif
</body>
</html>
