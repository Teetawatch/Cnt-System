import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:provider/provider.dart';
import '../core/theme/tokens.dart';
import '../data/api_models.dart';
import '../providers/notification_provider.dart';
import '../providers/home_provider.dart';
import '../widgets/status_chip.dart';
import '../widgets/shimmer_loader.dart';
import '../widgets/fade_slide_in.dart';
import '../widgets/error_state.dart';
import '../widgets/empty_state.dart';
import '../widgets/app_button.dart';

class StaffDetailScreen extends StatelessWidget {
  const StaffDetailScreen({super.key});

  static const _thaiMonths = [
    '', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.',
  ];

  static const _avatarGradients = [
    [Color(0xFF2563EB), Color(0xFF60A5FA)],
    [Color(0xFF7C3AED), Color(0xFFA78BFA)],
    [Color(0xFF059669), Color(0xFF34D399)],
    [Color(0xFFD97706), Color(0xFFFBBF24)],
    [Color(0xFFDC2626), Color(0xFFF87171)],
  ];

  @override
  Widget build(BuildContext context) {
    return Consumer<StaffListProvider>(
      builder: (context, provider, _) {
        final staff = provider.selectedStaff!;
        final gradientColors =
            _avatarGradients[staff.id % _avatarGradients.length];

        return Scaffold(
          backgroundColor: bg0,
          body: RefreshIndicator(
            color: accent,
            backgroundColor: bg1,
            onRefresh: provider.refreshDetail,
            child: CustomScrollView(
              slivers: [
                _buildAppBar(context, staff, gradientColors),
                SliverToBoxAdapter(
                  child: _buildMonthPicker(context, provider),
                ),
                SliverPadding(
                  padding: const EdgeInsets.symmetric(horizontal: sp20),
                  sliver: SliverToBoxAdapter(
                    child: _buildBody(provider),
                  ),
                ),
                const SliverToBoxAdapter(child: SizedBox(height: 100)),
              ],
            ),
          ),
        );
      },
    );
  }

  // ── App Bar with staff hero ─────────────────────────────────────
  Widget _buildAppBar(BuildContext context, StaffModel staff,
      List<Color> gradientColors) {
    return SliverAppBar(
      expandedHeight: 220,
      pinned: true,
      backgroundColor: gradientColors[0],
      surfaceTintColor: Colors.transparent,
      leading: IconButton(
        icon: const Icon(Icons.arrow_back_ios_new_rounded,
            color: Colors.white, size: 20),
        onPressed: () => Navigator.pop(context),
      ),
      flexibleSpace: FlexibleSpaceBar(
        background: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: gradientColors,
            ),
          ),
          child: SafeArea(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const SizedBox(height: 48),
                // Photo
                _buildLargeAvatar(staff, gradientColors),
                const SizedBox(height: sp16),
                Text(
                  staff.name,
                  style: bodyMd.copyWith(
                    fontWeight: FontWeight.w800,
                    color: Colors.white,
                    fontSize: 18,
                  ),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: sp4),
                Text(
                  staff.position,
                  style: labelSM.copyWith(
                    color: Colors.white.withValues(alpha: 0.8),
                  ),
                  textAlign: TextAlign.center,
                ),
                if (staff.department.isNotEmpty) ...[
                  const SizedBox(height: 2),
                  Text(
                    staff.department,
                    style: labelXS.copyWith(
                      color: Colors.white.withValues(alpha: 0.6),
                    ),
                    textAlign: TextAlign.center,
                  ),
                ],
              ],
            ),
          ),
        ),
      ),
    );
  }

  // ── Large Avatar ───────────────────────────────────────────────
  Widget _buildLargeAvatar(StaffModel staff, List<Color> gradientColors) {
    return Container(
      width: 84,
      height: 84,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(
          color: Colors.white.withValues(alpha: 0.6),
          width: 3,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.2),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: ClipOval(
        child: staff.hasPhoto
            ? CachedNetworkImage(
                imageUrl: staff.photoUrl!,
                fit: BoxFit.cover,
                placeholder: (context, url) => _avatarFallback(staff, gradientColors),
                errorWidget: (context, url, error) =>
                    _avatarFallback(staff, gradientColors),
              )
            : _avatarFallback(staff, gradientColors),
      ),
    );
  }

  Widget _avatarFallback(StaffModel staff, List<Color> gradientColors) {
    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.white.withValues(alpha: 0.3),
            Colors.white.withValues(alpha: 0.1),
          ],
        ),
      ),
      child: Center(
        child: Text(
          staff.initials,
          style: const TextStyle(
            fontSize: 28,
            fontWeight: FontWeight.w800,
            color: Colors.white,
          ),
        ),
      ),
    );
  }

  // ── Month Picker ───────────────────────────────────────────────
  Widget _buildMonthPicker(BuildContext context, StaffListProvider provider) {
    final month = provider.detailMonth;
    final year = provider.detailYear;
    final buddhistYear = year + 543;

    return Container(
      color: bg0,
      padding: const EdgeInsets.symmetric(horizontal: sp20, vertical: sp12),
      child: Row(
        children: [
          AppIconButton(
            icon: Icons.chevron_left_rounded,
            onPressed: () {
              int m = month - 1;
              int y = year;
              if (m < 1) {
                m = 12;
                y--;
              }
              provider.changeDetailMonth(m, y);
            },
          ),
          Expanded(
            child: Center(
              child: Text(
                '${_thaiMonths[month]} พ.ศ. $buddhistYear',
                style: bodyMd.copyWith(fontWeight: FontWeight.w700),
              ),
            ),
          ),
          AppIconButton(
            icon: Icons.chevron_right_rounded,
            onPressed: () {
              int m = month + 1;
              int y = year;
              if (m > 12) {
                m = 1;
                y++;
              }
              provider.changeDetailMonth(m, y);
            },
          ),
        ],
      ),
    );
  }

  // ── Body ───────────────────────────────────────────────────────
  Widget _buildBody(StaffListProvider provider) {
    switch (provider.detailState) {
      case ScreenState.loading:
        return Column(
          children: [
            const SizedBox(height: sp16),
            ...List.generate(
              3,
              (i) => Padding(
                padding: const EdgeInsets.only(bottom: sp12),
                child: ShimmerLoader(height: 110, borderRadius: r16),
              ),
            ),
          ],
        );
      case ScreenState.error:
        return ErrorStateWidget(
          message: provider.detailError ?? 'เกิดข้อผิดพลาด',
          onRetry: provider.refreshDetail,
        );
      case ScreenState.empty:
        return const EmptyStateWidget(
          icon: Icons.event_busy_rounded,
          title: 'ไม่มีกิจกรรม',
          subtitle: 'ไม่มีกิจกรรมในเดือนที่เลือก',
        );
      case ScreenState.success:
        return _buildEventList(provider.selectedStaffEvents);
    }
  }

  Widget _buildEventList(List<EventModel> events) {
    // Group by date
    final Map<String, List<EventModel>> grouped = {};
    for (final e in events) {
      final key = e.eventDate.toIso8601String().substring(0, 10);
      grouped.putIfAbsent(key, () => []).add(e);
    }
    final sortedKeys = grouped.keys.toList()..sort();

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: sp16),
        Text(
          '${events.length} กิจกรรมในเดือนนี้',
          style: labelSM.copyWith(color: txt3),
        ),
        const SizedBox(height: sp16),
        ...sortedKeys.asMap().entries.map((entry) {
          final dateKey = entry.key;
          final dateStr = entry.value;
          final dayEvents = grouped[dateStr]!;
          final date = DateTime.parse(dateStr);

          return FadeSlideIn(
            index: dateKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Date header
                _buildDateHeader(date),
                const SizedBox(height: sp8),
                // Events
                ...dayEvents.map((e) => Padding(
                      padding: const EdgeInsets.only(bottom: sp10),
                      child: _buildEventCard(e),
                    )),
                const SizedBox(height: sp8),
              ],
            ),
          );
        }),
      ],
    );
  }

  Widget _buildDateHeader(DateTime date) {
    final thaiDays = ['อา.', 'จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.'];
    final dayName = thaiDays[date.weekday % 7];
    final isToday = _isToday(date);

    return Row(
      children: [
        Container(
          padding: const EdgeInsets.symmetric(horizontal: sp12, vertical: sp6),
          decoration: BoxDecoration(
            color: isToday ? accent : bg2,
            borderRadius: r8,
          ),
          child: Text(
            '$dayName ${date.day} ${_thaiMonths[date.month]}',
            style: labelSM.copyWith(
              fontWeight: FontWeight.w700,
              color: isToday ? Colors.white : txt2,
            ),
          ),
        ),
        if (isToday) ...[
          const SizedBox(width: sp8),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: sp8, vertical: 3),
            decoration: BoxDecoration(
              color: accent.withValues(alpha: 0.1),
              borderRadius: rFull,
            ),
            child: Text(
              'วันนี้',
              style: labelXS.copyWith(
                color: accent,
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
        ],
      ],
    );
  }

  bool _isToday(DateTime date) {
    final now = DateTime.now();
    return date.year == now.year &&
        date.month == now.month &&
        date.day == now.day;
  }

  Widget _buildEventCard(EventModel event) {
    return Container(
      decoration: BoxDecoration(
        color: bg1,
        borderRadius: r16,
        border: Border.all(color: border1),
        boxShadow: [shadowSM],
      ),
      padding: const EdgeInsets.all(sp16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Time + status
          Row(
            children: [
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: sp8, vertical: 3),
                decoration: BoxDecoration(
                  color: accent.withValues(alpha: 0.08),
                  borderRadius: rFull,
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.schedule_rounded, size: 12, color: accent),
                    const SizedBox(width: sp4),
                    Text(
                      event.displayTimeRange,
                      style: labelSM.copyWith(
                        fontWeight: FontWeight.w700,
                        color: accent,
                      ),
                    ),
                  ],
                ),
              ),
              if (event.isMultiDay) ...[
                const SizedBox(width: sp6),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: sp8, vertical: 3),
                  decoration: BoxDecoration(
                    color: warningLight,
                    borderRadius: rFull,
                  ),
                  child: Text(
                    'หลายวัน',
                    style: labelXS.copyWith(
                        color: warning, fontWeight: FontWeight.w600),
                  ),
                ),
              ],
              const Spacer(),
              StatusChip(
                label: event.statusLabel,
                color: event.statusColor,
              ),
            ],
          ),
          const SizedBox(height: sp10),
          // Title
          Text(
            event.title,
            style: body.copyWith(fontWeight: FontWeight.w700, height: 1.4),
          ),
          // Description
          if (event.description != null && event.description!.isNotEmpty) ...[
            const SizedBox(height: sp6),
            Text(
              event.description!,
              style: labelSM.copyWith(color: txt2, height: 1.5),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
          ],
          // Location
          if (event.location != null && event.location!.isNotEmpty) ...[
            const SizedBox(height: sp8),
            _buildDetailRow(
                Icons.location_on_rounded, event.location!, accent),
          ],
          // Organization
          if (event.organization != null &&
              event.organization!.isNotEmpty) ...[
            const SizedBox(height: sp4),
            _buildDetailRow(
                Icons.business_rounded, event.organization!, accent2),
          ],
          // Remark
          if (event.remark != null && event.remark!.isNotEmpty) ...[
            const SizedBox(height: sp4),
            _buildDetailRow(Icons.notes_rounded, event.remark!, warning),
          ],
        ],
      ),
    );
  }

  Widget _buildDetailRow(IconData icon, String text, Color color) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: 20,
          height: 20,
          decoration: BoxDecoration(
            color: color.withValues(alpha: 0.1),
            borderRadius: r6,
          ),
          child: Icon(icon, size: 12, color: color),
        ),
        const SizedBox(width: sp8),
        Expanded(
          child: Padding(
            padding: const EdgeInsets.only(top: 2),
            child: Text(
              text,
              style: labelSM.copyWith(height: 1.5, color: txt2),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
          ),
        ),
      ],
    );
  }
}
