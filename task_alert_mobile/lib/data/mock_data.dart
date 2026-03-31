import 'package:flutter/material.dart';
import '../core/theme/tokens.dart';

// ── Commander (ผู้บังคับบัญชา) Model ──────────────────────────────
class Commander {
  final String id;
  final String name;
  final String rank;
  final String position;
  final String photoUrl;

  const Commander({
    required this.id,
    required this.name,
    required this.rank,
    required this.position,
    this.photoUrl = '',
  });

  String get initials {
    final parts = name.split(' ');
    if (parts.length >= 2) return '${parts[0][0]}${parts[1][0]}';
    return name.isNotEmpty ? name[0] : '?';
  }
}

// ── Task Model ────────────────────────────────────────────────────
enum TaskPriority { urgent, high, medium, low }
enum TaskStatus { pending, inProgress, completed, overdue }

class TaskItem {
  final String id;
  final String title;
  final String description;
  final Commander commander;
  final TaskPriority priority;
  final TaskStatus status;
  final DateTime date;
  final TimeOfDay startTime;
  final TimeOfDay endTime;
  final String location;
  final String note;
  final DateTime createdAt;

  const TaskItem({
    required this.id,
    required this.title,
    required this.description,
    required this.commander,
    required this.priority,
    required this.status,
    required this.date,
    required this.startTime,
    required this.endTime,
    required this.location,
    this.note = '',
    required this.createdAt,
  });

  Color get priorityColor {
    switch (priority) {
      case TaskPriority.urgent:
        return danger;
      case TaskPriority.high:
        return warning;
      case TaskPriority.medium:
        return accent;
      case TaskPriority.low:
        return success;
    }
  }

  String get priorityLabel {
    switch (priority) {
      case TaskPriority.urgent:
        return 'เร่งด่วน';
      case TaskPriority.high:
        return 'สำคัญ';
      case TaskPriority.medium:
        return 'ปกติ';
      case TaskPriority.low:
        return 'ต่ำ';
    }
  }

  Color get statusColor {
    switch (status) {
      case TaskStatus.pending:
        return txt3;
      case TaskStatus.inProgress:
        return accent;
      case TaskStatus.completed:
        return success;
      case TaskStatus.overdue:
        return danger;
    }
  }

  String get statusLabel {
    switch (status) {
      case TaskStatus.pending:
        return 'รอดำเนินการ';
      case TaskStatus.inProgress:
        return 'กำลังดำเนินการ';
      case TaskStatus.completed:
        return 'เสร็จสิ้น';
      case TaskStatus.overdue:
        return 'เกินกำหนด';
    }
  }

  String get timeRange {
    String fmt(TimeOfDay t) =>
        '${t.hour.toString().padLeft(2, '0')}:${t.minute.toString().padLeft(2, '0')}';
    return '${fmt(startTime)} - ${fmt(endTime)}';
  }
}

// ── Notification Model ────────────────────────────────────────────
enum NotificationType { taskAssigned, taskDue, taskCompleted, taskUpdated }

class NotificationItem {
  final String id;
  final String title;
  final String body;
  final NotificationType type;
  final Commander? commander;
  final DateTime timestamp;
  final bool isRead;

  const NotificationItem({
    required this.id,
    required this.title,
    required this.body,
    required this.type,
    this.commander,
    required this.timestamp,
    this.isRead = false,
  });

  IconData get icon {
    switch (type) {
      case NotificationType.taskAssigned:
        return Icons.assignment_outlined;
      case NotificationType.taskDue:
        return Icons.schedule_outlined;
      case NotificationType.taskCompleted:
        return Icons.check_circle_outline;
      case NotificationType.taskUpdated:
        return Icons.update_outlined;
    }
  }

  Color get iconColor {
    switch (type) {
      case NotificationType.taskAssigned:
        return accent;
      case NotificationType.taskDue:
        return warning;
      case NotificationType.taskCompleted:
        return success;
      case NotificationType.taskUpdated:
        return accent2;
    }
  }
}

// ── Report Stats Model ────────────────────────────────────────────
class ReportStats {
  final int totalTasks;
  final int completedTasks;
  final int pendingTasks;
  final int overdueTasks;
  final double completionRate;
  final List<WeeklyData> weeklyData;

  const ReportStats({
    required this.totalTasks,
    required this.completedTasks,
    required this.pendingTasks,
    required this.overdueTasks,
    required this.completionRate,
    required this.weeklyData,
  });
}

class WeeklyData {
  final String day;
  final int completed;
  final int assigned;

  const WeeklyData({
    required this.day,
    required this.completed,
    required this.assigned,
  });
}

// ── User Profile Model ────────────────────────────────────────────
class UserProfile {
  final String name;
  final String email;
  final String role;
  final String department;
  final String avatarUrl;
  final int totalTasksCompleted;
  final int streak;

  const UserProfile({
    required this.name,
    required this.email,
    required this.role,
    required this.department,
    required this.avatarUrl,
    required this.totalTasksCompleted,
    required this.streak,
  });
}

// ── Mock Commanders ───────────────────────────────────────────────
class MockCommanders {
  static const commander1 = Commander(
    id: 'c1',
    name: 'พล.ท. สุรชัย วงศ์ประเสริฐ',
    rank: 'พลโท',
    position: 'ผู้บัญชาการหน่วย',
  );
  static const commander2 = Commander(
    id: 'c2',
    name: 'พล.ต. อนันต์ ศรีสุข',
    rank: 'พลตรี',
    position: 'รองผู้บัญชาการ',
  );
  static const commander3 = Commander(
    id: 'c3',
    name: 'พ.อ. ธนากร เจริญพร',
    rank: 'พันเอก',
    position: 'เสนาธิการ',
  );
  static const commander4 = Commander(
    id: 'c4',
    name: 'พ.อ. วิทยา มั่นคง',
    rank: 'พันเอก',
    position: 'ผู้อำนวยการกอง',
  );
}

// ── Mock Data ─────────────────────────────────────────────────────
class MockData {
  static DateTime _today({int hour = 0, int minute = 0}) {
    final now = DateTime.now();
    return DateTime(now.year, now.month, now.day, hour, minute);
  }

  static final List<TaskItem> todayTasks = [
    TaskItem(
      id: '1',
      title: 'ประชุมติดตามความก้าวหน้าโครงการ',
      description: 'ประชุมรายงานความก้าวหน้าโครงการระบบ CNT ประจำสัปดาห์ พร้อมนำเสนอปัญหาและแนวทางแก้ไข',
      commander: MockCommanders.commander1,
      priority: TaskPriority.urgent,
      status: TaskStatus.inProgress,
      date: _today(),
      startTime: const TimeOfDay(hour: 9, minute: 0),
      endTime: const TimeOfDay(hour: 10, minute: 30),
      location: 'ห้องประชุมชั้น 3 อาคาร บก.',
      note: 'เตรียมเอกสารสรุปผลงานประจำเดือน และ PowerPoint นำเสนอ',
      createdAt: DateTime.now().subtract(const Duration(days: 1)),
    ),
    TaskItem(
      id: '2',
      title: 'ตรวจสอบระบบเครือข่ายภายใน',
      description: 'ตรวจสอบและทดสอบระบบเครือข่ายภายในอาคาร ให้พร้อมสำหรับการใช้งานระบบใหม่',
      commander: MockCommanders.commander3,
      priority: TaskPriority.high,
      status: TaskStatus.pending,
      date: _today(),
      startTime: const TimeOfDay(hour: 11, minute: 0),
      endTime: const TimeOfDay(hour: 12, minute: 0),
      location: 'ห้อง Server Room ชั้น 2',
      note: 'ประสาน จนท. เครือข่ายให้อยู่ด้วยระหว่างตรวจสอบ',
      createdAt: DateTime.now().subtract(const Duration(hours: 12)),
    ),
    TaskItem(
      id: '3',
      title: 'ส่งรายงานสรุปผลการฝึก',
      description: 'จัดทำรายงานสรุปผลการฝึกอบรมหลักสูตร IT Security เสนอผู้บังคับบัญชา',
      commander: MockCommanders.commander2,
      priority: TaskPriority.medium,
      status: TaskStatus.pending,
      date: _today(),
      startTime: const TimeOfDay(hour: 13, minute: 30),
      endTime: const TimeOfDay(hour: 15, minute: 0),
      location: 'กองบัญชาการ ชั้น 4',
      note: 'แนบรูปถ่ายกิจกรรมและแบบประเมินผล',
      createdAt: DateTime.now().subtract(const Duration(days: 2)),
    ),
    TaskItem(
      id: '4',
      title: 'ประสานงานจัดเตรียมห้องประชุม VDO Conference',
      description: 'เตรียมห้องประชุมและอุปกรณ์ VDO Conference สำหรับการประชุมผู้บังคับบัญชาระดับสูง',
      commander: MockCommanders.commander4,
      priority: TaskPriority.high,
      status: TaskStatus.pending,
      date: _today(),
      startTime: const TimeOfDay(hour: 15, minute: 30),
      endTime: const TimeOfDay(hour: 16, minute: 30),
      location: 'ห้องประชุม VIP ชั้น 5',
      note: 'ทดสอบระบบภาพและเสียงก่อนเวลาประชุมจริง 30 นาที',
      createdAt: DateTime.now().subtract(const Duration(hours: 6)),
    ),
  ];

  static final List<TaskItem> yesterdayTasks = [
    TaskItem(
      id: '5',
      title: 'ซ่อมบำรุงระบบ CCTV',
      description: 'ดำเนินการซ่อมบำรุงระบบ CCTV ที่มีปัญหาในเขตพื้นที่ส่วนหน้า',
      commander: MockCommanders.commander3,
      priority: TaskPriority.medium,
      status: TaskStatus.completed,
      date: _today().subtract(const Duration(days: 1)),
      startTime: const TimeOfDay(hour: 10, minute: 0),
      endTime: const TimeOfDay(hour: 12, minute: 0),
      location: 'อาคารรักษาการณ์ เขต 1',
      note: 'เปลี่ยนกล้องหมายเลข 7, 12 ที่ชำรุด',
      createdAt: DateTime.now().subtract(const Duration(days: 3)),
    ),
    TaskItem(
      id: '6',
      title: 'ฝึกอบรมการใช้ระบบสารสนเทศ',
      description: 'จัดฝึกอบรมการใช้งานระบบสารสนเทศใหม่ให้กับกำลังพลในหน่วย',
      commander: MockCommanders.commander1,
      priority: TaskPriority.high,
      status: TaskStatus.completed,
      date: _today().subtract(const Duration(days: 1)),
      startTime: const TimeOfDay(hour: 13, minute: 0),
      endTime: const TimeOfDay(hour: 16, minute: 0),
      location: 'ห้องอบรม IT ชั้น 2',
      note: 'ผู้เข้าอบรม 30 คน จัดเตรียมคอมพิวเตอร์ให้พร้อม',
      createdAt: DateTime.now().subtract(const Duration(days: 4)),
    ),
  ];

  static final List<TaskItem> tomorrowTasks = [
    TaskItem(
      id: '7',
      title: 'ประชุมวางแผนงบประมาณ IT ปี 68',
      description: 'ร่วมประชุมวางแผนจัดสรรงบประมาณด้าน IT ประจำปี 2568 กับกองแผนงาน',
      commander: MockCommanders.commander2,
      priority: TaskPriority.urgent,
      status: TaskStatus.pending,
      date: _today().add(const Duration(days: 1)),
      startTime: const TimeOfDay(hour: 9, minute: 30),
      endTime: const TimeOfDay(hour: 12, minute: 0),
      location: 'ห้องประชุมกองแผนงาน ชั้น 3',
      note: 'เตรียมข้อมูลค่าใช้จ่ายย้อนหลัง 3 ปี',
      createdAt: DateTime.now(),
    ),
    TaskItem(
      id: '8',
      title: 'ตรวจรับงาน Vendor ระบบ Firewall',
      description: 'ตรวจรับงานติดตั้งระบบ Firewall ใหม่จากบริษัทผู้รับเหมา',
      commander: MockCommanders.commander4,
      priority: TaskPriority.high,
      status: TaskStatus.pending,
      date: _today().add(const Duration(days: 1)),
      startTime: const TimeOfDay(hour: 14, minute: 0),
      endTime: const TimeOfDay(hour: 16, minute: 0),
      location: 'ห้อง Server Room ชั้น 2',
      note: 'ตรวจสอบ spec ตาม TOR ก่อนลงนาม',
      createdAt: DateTime.now(),
    ),
  ];

  static List<TaskItem> getTasksForDate(DateTime date) {
    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);
    final target = DateTime(date.year, date.month, date.day);
    final diff = target.difference(today).inDays;

    if (diff == 0) return todayTasks;
    if (diff == -1) return yesterdayTasks;
    if (diff == 1) return tomorrowTasks;
    return [];
  }

  static final List<NotificationItem> notifications = [
    NotificationItem(
      id: '1',
      title: 'งานใหม่จากผู้บังคับบัญชา',
      body: 'พล.ท. สุรชัย มอบหมายงาน "ประชุมติดตามความก้าวหน้าโครงการ"',
      type: NotificationType.taskAssigned,
      commander: MockCommanders.commander1,
      timestamp: DateTime.now().subtract(const Duration(minutes: 30)),
    ),
    NotificationItem(
      id: '2',
      title: 'งานใกล้ถึงเวลา',
      body: '"ตรวจสอบระบบเครือข่ายภายใน" จะเริ่มในอีก 1 ชั่วโมง',
      type: NotificationType.taskDue,
      commander: MockCommanders.commander3,
      timestamp: DateTime.now().subtract(const Duration(hours: 1)),
    ),
    NotificationItem(
      id: '3',
      title: 'งานเสร็จสมบูรณ์',
      body: '"ซ่อมบำรุงระบบ CCTV" ถูกทำเครื่องหมายว่าเสร็จสิ้นแล้ว',
      type: NotificationType.taskCompleted,
      timestamp: DateTime.now().subtract(const Duration(hours: 4)),
      isRead: true,
    ),
    NotificationItem(
      id: '4',
      title: 'งานมีการเปลี่ยนแปลง',
      body: '"ประชุมวางแผนงบประมาณ" ย้ายเวลาจาก 09:00 เป็น 09:30',
      type: NotificationType.taskUpdated,
      commander: MockCommanders.commander2,
      timestamp: DateTime.now().subtract(const Duration(hours: 6)),
      isRead: true,
    ),
    NotificationItem(
      id: '5',
      title: 'งานใหม่จากผู้บังคับบัญชา',
      body: 'พ.อ. วิทยา มอบหมายงาน "ตรวจรับงาน Vendor ระบบ Firewall"',
      type: NotificationType.taskAssigned,
      commander: MockCommanders.commander4,
      timestamp: DateTime.now().subtract(const Duration(days: 1)),
      isRead: true,
    ),
  ];

  static const reportStats = ReportStats(
    totalTasks: 28,
    completedTasks: 21,
    pendingTasks: 4,
    overdueTasks: 3,
    completionRate: 0.75,
    weeklyData: [
      WeeklyData(day: 'จ.', completed: 4, assigned: 5),
      WeeklyData(day: 'อ.', completed: 3, assigned: 4),
      WeeklyData(day: 'พ.', completed: 5, assigned: 5),
      WeeklyData(day: 'พฤ.', completed: 4, assigned: 4),
      WeeklyData(day: 'ศ.', completed: 3, assigned: 5),
      WeeklyData(day: 'ส.', completed: 1, assigned: 2),
      WeeklyData(day: 'อา.', completed: 1, assigned: 3),
    ],
  );

  static const userProfile = UserProfile(
    name: 'ร.ท. สมชาย ใจดี',
    email: 'somchai@cnt-system.mil.th',
    role: 'นายทหาร IT',
    department: 'กองสารสนเทศ',
    avatarUrl: '',
    totalTasksCompleted: 142,
    streak: 12,
  );
}
