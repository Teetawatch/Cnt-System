import 'package:flutter/material.dart';
import '../core/theme/tokens.dart';

// ── Staff (ผู้บังคับบัญชา) ─────────────────────────────────────────
class StaffModel {
  final int id;
  final String name;
  final String position;
  final String department;
  final String? description;
  final String? photoUrl;
  final List<EventModel> events;

  const StaffModel({
    required this.id,
    required this.name,
    required this.position,
    required this.department,
    this.description,
    this.photoUrl,
    this.events = const [],
  });

  factory StaffModel.fromJson(Map<String, dynamic> json) {
    return StaffModel(
      id: int.tryParse('${json['id']}') ?? 0,
      name: json['name'] as String? ?? '',
      position: json['position'] as String? ?? '',
      department: json['department'] as String? ?? '',
      description: json['description'] as String?,
      photoUrl: json['photo_url'] as String?,
      events: (json['events'] as List<dynamic>? ?? [])
          .map((e) => EventModel.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }

  String get initials {
    final parts = name.trim().split(' ');
    if (parts.length >= 2) {
      return '${parts[0][0]}${parts[1][0]}';
    }
    return name.isNotEmpty ? name[0] : '?';
  }

  bool get hasPhoto => photoUrl != null && photoUrl!.isNotEmpty;
}

// ── Calendar Event ────────────────────────────────────────────────
class EventModel {
  final int id;
  final String title;
  final String? description;
  final String? remark;
  final String? location;
  final String? organization;
  final DateTime eventDate;
  final DateTime? endDate;
  final String? startTime;
  final String? endTime;
  final String status;
  final String statusLabel;
  final String? timeRange;

  const EventModel({
    required this.id,
    required this.title,
    this.description,
    this.remark,
    this.location,
    this.organization,
    required this.eventDate,
    this.endDate,
    this.startTime,
    this.endTime,
    required this.status,
    required this.statusLabel,
    this.timeRange,
  });

  factory EventModel.fromJson(Map<String, dynamic> json) {
    return EventModel(
      id: int.tryParse('${json['id']}') ?? 0,
      title: json['title'] as String? ?? '',

      description: json['description'] as String?,
      remark: json['remark'] as String?,
      location: json['location'] as String?,
      organization: json['organization'] as String?,
      eventDate: DateTime.parse(json['event_date'] as String),
      endDate: json['end_date'] != null
          ? DateTime.parse(json['end_date'] as String)
          : null,
      startTime: json['start_time'] as String?,
      endTime: json['end_time'] as String?,
      status: json['status'] as String? ?? 'pending',
      statusLabel: json['status_label'] as String? ?? 'รอดำเนินการ',
      timeRange: json['time_range'] as String?,
    );
  }

  String get displayTimeRange {
    if (timeRange != null && timeRange!.isNotEmpty) return timeRange!;
    if (startTime != null && endTime != null) return '$startTime - $endTime';
    if (startTime != null) return startTime!;
    return 'ทั้งวัน';
  }

  bool get isAllDay => startTime == null && endTime == null;

  bool get isMultiDay =>
      endDate != null &&
      endDate!.difference(eventDate).inDays > 0;

  Color get statusColor {
    switch (status) {
      case 'confirmed':
        return success;
      case 'pending':
        return warning;
      case 'cancelled':
        return danger;
      default:
        return txt3;
    }
  }

  Color get statusBgColor {
    switch (status) {
      case 'confirmed':
        return successLight;
      case 'pending':
        return warningLight;
      case 'cancelled':
        return dangerLight;
      default:
        return bg2;
    }
  }

  IconData get statusIcon {
    switch (status) {
      case 'confirmed':
        return Icons.check_circle_outline_rounded;
      case 'pending':
        return Icons.schedule_rounded;
      case 'cancelled':
        return Icons.cancel_outlined;
      default:
        return Icons.help_outline_rounded;
    }
  }
}

// ── Dashboard Response ────────────────────────────────────────────
class DashboardResponse {
  final String date;
  final String thaiDate;
  final DashboardSummary summary;
  final List<StaffModel> staff;

  const DashboardResponse({
    required this.date,
    required this.thaiDate,
    required this.summary,
    required this.staff,
  });

  factory DashboardResponse.fromJson(Map<String, dynamic> json) {
    return DashboardResponse(
      date: json['date'] as String,
      thaiDate: json['thai_date'] as String,
      summary: DashboardSummary.fromJson(
          json['summary'] as Map<String, dynamic>),
      staff: (json['staff'] as List<dynamic>)
          .map((s) => StaffModel.fromJson(s as Map<String, dynamic>))
          .toList(),
    );
  }

  List<StaffModel> get staffWithEvents =>
      staff.where((s) => s.events.isNotEmpty).toList();
}

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
      totalEvents: int.tryParse('${json['total_events']}') ?? 0,
      confirmed: int.tryParse('${json['confirmed']}') ?? 0,
      pending: int.tryParse('${json['pending']}') ?? 0,
      cancelled: int.tryParse('${json['cancelled']}') ?? 0,
      totalStaff: int.tryParse('${json['total_staff']}') ?? 0,
      staffWithEvents: int.tryParse('${json['staff_with_events']}') ?? 0,
    );
  }
}

// ── Stats Response ────────────────────────────────────────────────
class StatsResponse {
  final int month;
  final int year;
  final String thaiMonth;
  final StatsSummary summary;
  final List<DailyData> dailyData;
  final List<StaffStat> staffStats;

  const StatsResponse({
    required this.month,
    required this.year,
    required this.thaiMonth,
    required this.summary,
    required this.dailyData,
    required this.staffStats,
  });

  factory StatsResponse.fromJson(Map<String, dynamic> json) {
    return StatsResponse(
      month: int.tryParse('${json['month']}') ?? DateTime.now().month,
      year: int.tryParse('${json['year']}') ?? DateTime.now().year,
      thaiMonth: json['thai_month'] as String? ?? '',
      summary: StatsSummary.fromJson(
          json['summary'] as Map<String, dynamic>),
      dailyData: (json['daily_data'] as List<dynamic>)
          .map((d) => DailyData.fromJson(d as Map<String, dynamic>))
          .toList(),
      staffStats: (json['staff_stats'] as List<dynamic>)
          .map((s) => StaffStat.fromJson(s as Map<String, dynamic>))
          .toList(),
    );
  }
}

class StatsSummary {
  final int totalEvents;
  final int confirmed;
  final int pending;
  final int cancelled;

  const StatsSummary({
    required this.totalEvents,
    required this.confirmed,
    required this.pending,
    required this.cancelled,
  });

  factory StatsSummary.fromJson(Map<String, dynamic> json) {
    return StatsSummary(
      totalEvents: int.tryParse('${json['total_events']}') ?? 0,
      confirmed: int.tryParse('${json['confirmed']}') ?? 0,
      pending: int.tryParse('${json['pending']}') ?? 0,
      cancelled: int.tryParse('${json['cancelled']}') ?? 0,
    );
  }

  double get completionRate =>
      totalEvents > 0 ? confirmed / totalEvents : 0.0;
}

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
      day: int.tryParse('${json['day']}') ?? 0,
      dayName: json['day_name'] as String? ?? '',
      total: int.tryParse('${json['total']}') ?? 0,
      confirmed: int.tryParse('${json['confirmed']}') ?? 0,
      pending: int.tryParse('${json['pending']}') ?? 0,
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
      id: int.tryParse('${json['id']}') ?? 0,
      name: json['name'] as String? ?? '',
      position: json['position'] as String? ?? '',
      photoUrl: json['photo_url'] as String?,
      eventsCount: int.tryParse('${json['events_count']}') ?? 0,
    );
  }
}
