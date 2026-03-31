import 'package:flutter/material.dart';
import '../core/theme/tokens.dart';

// ── Staff Model ──────────────────────────────────────────────────
class Staff {
  final int id;
  final String name;
  final String position;
  final String? department;
  final String? description;
  final String? photoUrl;
  final int eventsCount;
  final List<CalendarEvent> events;

  const Staff({
    required this.id,
    required this.name,
    required this.position,
    this.department,
    this.description,
    this.photoUrl,
    this.eventsCount = 0,
    this.events = const [],
  });

  factory Staff.fromJson(Map<String, dynamic> json) {
    return Staff(
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      position: json['position'] as String? ?? '',
      department: json['department'] as String?,
      description: json['description'] as String?,
      photoUrl: json['photo_url'] as String?,
      eventsCount: json['events_count'] as int? ?? 0,
      events: (json['events'] as List<dynamic>?)
              ?.map((e) => CalendarEvent.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
    );
  }

  String get initials {
    final parts = name.split(' ');
    if (parts.length >= 2) {
      return '${parts[0][0]}${parts[1][0]}';
    }
    return name.isNotEmpty ? name[0] : '?';
  }

  bool get hasPhoto => photoUrl != null && photoUrl!.isNotEmpty;
}

// ── Calendar Event Model ─────────────────────────────────────────
class CalendarEvent {
  final int id;
  final String title;
  final String? description;
  final String? remark;
  final String location;
  final String? organization;
  final String eventDate;
  final String? endDate;
  final String? startTime;
  final String? endTime;
  final String status;
  final String? statusLabel;
  final String? timeRange;
  final Staff? staff;

  const CalendarEvent({
    required this.id,
    required this.title,
    this.description,
    this.remark,
    required this.location,
    this.organization,
    required this.eventDate,
    this.endDate,
    this.startTime,
    this.endTime,
    required this.status,
    this.statusLabel,
    this.timeRange,
    this.staff,
  });

  factory CalendarEvent.fromJson(Map<String, dynamic> json) {
    return CalendarEvent(
      id: json['id'] as int,
      title: json['title'] as String? ?? '',
      description: json['description'] as String?,
      remark: json['remark'] as String?,
      location: json['location'] as String? ?? '',
      organization: json['organization'] as String?,
      eventDate: json['event_date'] as String? ?? '',
      endDate: json['end_date'] as String?,
      startTime: json['start_time'] as String?,
      endTime: json['end_time'] as String?,
      status: json['status'] as String? ?? 'confirmed',
      statusLabel: json['status_label'] as String?,
      timeRange: json['time_range'] as String?,
      staff: json['staff'] != null
          ? Staff.fromJson(json['staff'] as Map<String, dynamic>)
          : null,
    );
  }

  Color get statusColor {
    switch (status) {
      case 'pending':
        return warning;
      case 'confirmed':
        return success;
      case 'cancelled':
        return danger;
      default:
        return accent;
    }
  }

  String get statusText {
    return statusLabel ?? switch (status) {
      'pending' => 'รอยืนยัน',
      'confirmed' => 'ยืนยันแล้ว',
      'cancelled' => 'ยกเลิก',
      _ => status,
    };
  }

  String get displayTimeRange {
    if (timeRange != null && timeRange!.isNotEmpty) return timeRange!;
    if (startTime == null) return '';
    if (endTime != null) return '$startTime - $endTime น.';
    return '$startTime น.';
  }

  bool get isMultiDay => endDate != null && endDate != eventDate;
}

// ── Dashboard Summary Model ──────────────────────────────────────
class DashboardSummary {
  final int totalEvents;
  final int confirmed;
  final int pending;
  final int cancelled;
  final int totalStaff;
  final int staffWithEvents;

  const DashboardSummary({
    required this.totalEvents,
    required this.confirmed,
    required this.pending,
    required this.cancelled,
    required this.totalStaff,
    required this.staffWithEvents,
  });

  factory DashboardSummary.fromJson(Map<String, dynamic> json) {
    return DashboardSummary(
      totalEvents: json['total_events'] as int? ?? 0,
      confirmed: json['confirmed'] as int? ?? 0,
      pending: json['pending'] as int? ?? 0,
      cancelled: json['cancelled'] as int? ?? 0,
      totalStaff: json['total_staff'] as int? ?? 0,
      staffWithEvents: json['staff_with_events'] as int? ?? 0,
    );
  }
}

// ── Dashboard Response Model ─────────────────────────────────────
class DashboardResponse {
  final String date;
  final String thaiDate;
  final DashboardSummary summary;
  final List<Staff> staff;

  const DashboardResponse({
    required this.date,
    required this.thaiDate,
    required this.summary,
    required this.staff,
  });

  factory DashboardResponse.fromJson(Map<String, dynamic> json) {
    return DashboardResponse(
      date: json['date'] as String? ?? '',
      thaiDate: json['thai_date'] as String? ?? '',
      summary: DashboardSummary.fromJson(json['summary'] as Map<String, dynamic>),
      staff: (json['staff'] as List<dynamic>?)
              ?.map((e) => Staff.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
    );
  }
}

// ── Monthly Stats Models ─────────────────────────────────────────
class DailyData {
  final String date;
  final int day;
  final String dayName;
  final int total;
  final int confirmed;
  final int pending;

  const DailyData({
    required this.date,
    required this.day,
    required this.dayName,
    required this.total,
    required this.confirmed,
    required this.pending,
  });

  factory DailyData.fromJson(Map<String, dynamic> json) {
    return DailyData(
      date: json['date'] as String? ?? '',
      day: json['day'] as int? ?? 0,
      dayName: json['day_name'] as String? ?? '',
      total: json['total'] as int? ?? 0,
      confirmed: json['confirmed'] as int? ?? 0,
      pending: json['pending'] as int? ?? 0,
    );
  }
}

class StaffStat {
  final int id;
  final String name;
  final String position;
  final String? photoUrl;
  final int eventsCount;

  const StaffStat({
    required this.id,
    required this.name,
    required this.position,
    this.photoUrl,
    required this.eventsCount,
  });

  factory StaffStat.fromJson(Map<String, dynamic> json) {
    return StaffStat(
      id: json['id'] as int,
      name: json['name'] as String? ?? '',
      position: json['position'] as String? ?? '',
      photoUrl: json['photo_url'] as String?,
      eventsCount: json['events_count'] as int? ?? 0,
    );
  }
}

class MonthlyStatsResponse {
  final int month;
  final int year;
  final String thaiMonth;
  final DashboardSummary summary;
  final List<DailyData> dailyData;
  final List<StaffStat> staffStats;

  const MonthlyStatsResponse({
    required this.month,
    required this.year,
    required this.thaiMonth,
    required this.summary,
    required this.dailyData,
    required this.staffStats,
  });

  factory MonthlyStatsResponse.fromJson(Map<String, dynamic> json) {
    return MonthlyStatsResponse(
      month: json['month'] as int,
      year: json['year'] as int,
      thaiMonth: json['thai_month'] as String? ?? '',
      summary: DashboardSummary.fromJson(json['summary'] as Map<String, dynamic>),
      dailyData: (json['daily_data'] as List<dynamic>?)
              ?.map((e) => DailyData.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      staffStats: (json['staff_stats'] as List<dynamic>?)
              ?.map((e) => StaffStat.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
    );
  }
}
