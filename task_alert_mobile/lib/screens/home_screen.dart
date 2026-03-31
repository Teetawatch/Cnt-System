import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:provider/provider.dart';
import '../core/theme/tokens.dart';
import '../data/api_models.dart';
import '../providers/home_provider.dart';
import '../widgets/status_chip.dart';
import '../widgets/shimmer_loader.dart';
import '../widgets/fade_slide_in.dart';
import '../widgets/error_state.dart';
import '../widgets/empty_state.dart';
import '../widgets/app_button.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  static const _thaiMonths = [
    '', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.',
  ];

  // Gradient palette for staff without photos
  static const _avatarGradients = [
    [Color(0xFF2563EB), Color(0xFF60A5FA)],
    [Color(0xFF7C3AED), Color(0xFFA78BFA)],
    [Color(0xFF059669), Color(0xFF34D399)],
    [Color(0xFFD97706), Color(0xFFFBBF24)],
    [Color(0xFFDC2626), Color(0xFFF87171)],
    [Color(0xFF0891B2), Color(0xFF22D3EE)],
  ];

  @override
  Widget build(BuildContext context) {
    return Consumer<HomeProvider>(
      builder: (context, provider, _) {
        return RefreshIndicator(
          color: accent,
          backgroundColor: bg1,
          onRefresh: provider.refresh,
          child: CustomScrollView(
            slivers: [
              _buildAppBar(context, provider),
              SliverPersistentHeader(
                pinned: true,
                delegate: _DatePickerDelegate(
                  selectedDate: provider.selectedDate,
                  onDateSelected: provider.selectDate,
                ),
              ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(sp20, sp16, sp20, sp8),
                  child: _buildDateHeader(provider),
                ),
              ),
              SliverPadding(
                padding: const EdgeInsets.symmetric(horizontal: sp20),
                sliver: SliverToBoxAdapter(
                  child: _buildBody(context, provider),
                ),
              ),
              const SliverToBoxAdapter(child: SizedBox(height: 100)),
            ],
          ),
        );
      },
    );
  }

  Widget _buildAppBar(BuildContext context, HomeProvider provider) {
    return SliverAppBar(
      floating: true,
      snap: true,
      backgroundColor: bg0,
      surfaceTintColor: Colors.transparent,
      toolbarHeight: 64,
      title: Row(
        children: [
          // Logo
          Container(
            width: 44,
            height: 44,
            decoration: BoxDecoration(
              borderRadius: r14,
              boxShadow: [
                BoxShadow(
                  color: const Color(0xFF2563EB).withValues(alpha: 0.35),
                  blurRadius: 12,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: ClipRRect(
              borderRadius: r14,
              child: CachedNetworkImage(
                imageUrl: 'https://workcnt.nass.ac.th/images/logo.png',
                fit: BoxFit.contain,
                placeholder: (context, url) => _logoFallback(),
                errorWidget: (context, url, error) => _logoFallback(),
              ),
            ),
          ),
          const SizedBox(width: sp12),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                'สพป.ชัยนาท',
                style: bodyMd.copyWith(
                  fontWeight: FontWeight.w800,
                  letterSpacing: -0.3,
                  height: 1.2,
                ),
              ),
              Row(
                children: [
                  Container(
                    width: 6,
                    height: 6,
                    decoration: const BoxDecoration(
                      color: Color(0xFF22C55E),
                      shape: BoxShape.circle,
                    ),
                  ),
                  const SizedBox(width: sp4),
                  Text(
                    'ตารางปฏิบัติงาน',
                    style: labelXS.copyWith(
                      color: txt3,
                      letterSpacing: 0.2,
                    ),
                  ),
                ],
              ),
            ],
          ),
        ],
      ),
      actions: [
        // Today shortcut
        if (!provider.isToday)
          Padding(
            padding: const EdgeInsets.only(right: sp4),
            child: GestureDetector(
              onTap: () => provider.selectDate(DateTime.now()),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: sp10, vertical: sp6),
                decoration: BoxDecoration(
                  color: accent.withValues(alpha: 0.1),
                  borderRadius: r8,
                  border: Border.all(color: accent.withValues(alpha: 0.25)),
                ),
                child: Text(
                  'วันนี้',
                  style: labelSM.copyWith(
                    color: accent,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ),
          ),
        Padding(
          padding: const EdgeInsets.only(right: sp12),
          child: AppIconButton(
            icon: Icons.edit_calendar_rounded,
            onPressed: () async {
              final picked = await showDatePicker(
                context: context,
                initialDate: provider.selectedDate,
                firstDate: DateTime.now().subtract(const Duration(days: 365)),
                lastDate: DateTime.now().add(const Duration(days: 365)),
                builder: (context, child) {
                  return Theme(
                    data: Theme.of(context).copyWith(
                      colorScheme: const ColorScheme.light(primary: accent),
                    ),
                    child: child!,
                  );
                },
              );
              if (picked != null) provider.selectDate(picked);
            },
          ),
        ),
      ],
    );
  }

  Widget _buildDateHeader(HomeProvider provider) {
    final d = provider.selectedDate;
    final thaiDaysFull = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัส', 'ศุกร์', 'เสาร์'];
    final dayName = thaiDaysFull[d.weekday % 7];
    final dateStr = '${d.day} ${_thaiMonths[d.month]} ${d.year + 543}';
    final summary = provider.dashboard?.summary;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Date title row
        Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (provider.isToday)
                    Container(
                      margin: const EdgeInsets.only(bottom: sp4),
                      padding: const EdgeInsets.symmetric(horizontal: sp8, vertical: 2),
                      decoration: BoxDecoration(
                        color: const Color(0xFF22C55E).withValues(alpha: 0.1),
                        borderRadius: rFull,
                        border: Border.all(color: const Color(0xFF22C55E).withValues(alpha: 0.3)),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Container(
                            width: 6, height: 6,
                            decoration: const BoxDecoration(
                              color: Color(0xFF22C55E), shape: BoxShape.circle,
                            ),
                          ),
                          const SizedBox(width: sp4),
                          Text('วันนี้', style: labelXS.copyWith(
                            color: const Color(0xFF16A34A), fontWeight: FontWeight.w700,
                          )),
                        ],
                      ),
                    ),
                  Text(
                    'วัน$dayName ที่ $dateStr',
                    style: titleSM.copyWith(fontSize: 17, height: 1.3),
                  ),
                  if (provider.dashboard?.thaiDate != null)
                    Text(
                      provider.dashboard!.thaiDate,
                      style: labelXS.copyWith(color: txt3),
                    ),
                ],
              ),
            ),
          ],
        ),
        // Summary stats row
        if (summary != null && summary.totalEvents > 0) ...[  
          const SizedBox(height: sp12),
          Row(
            children: [
              _buildStatMini('ทั้งหมด', '${summary.totalEvents}',
                  accent, Icons.event_note_rounded),
              const SizedBox(width: sp8),
              _buildStatMini('ยืนยัน', '${summary.confirmed}',
                  success, Icons.check_circle_outline_rounded),
              const SizedBox(width: sp8),
              _buildStatMini('รอยืนยัน', '${summary.pending}',
                  warning, Icons.schedule_rounded),
              const SizedBox(width: sp8),
              _buildStatMini('บุคลากร', '${summary.staffWithEvents}/${summary.totalStaff}',
                  accent2, Icons.people_alt_outlined),
            ],
          ),
        ],
      ],
    );
  }

  Widget _buildStatMini(String label, String value, Color color, IconData icon) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: sp8, horizontal: sp6),
        decoration: BoxDecoration(
          color: color.withValues(alpha: 0.07),
          borderRadius: r12,
          border: Border.all(color: color.withValues(alpha: 0.18)),
        ),
        child: Column(
          children: [
            Icon(icon, size: 16, color: color),
            const SizedBox(height: 3),
            Text(
              value,
              style: labelSM.copyWith(
                fontWeight: FontWeight.w800,
                color: color,
                height: 1.1,
              ),
            ),
            Text(
              label,
              style: TextStyle(
                fontSize: 9,
                color: color.withValues(alpha: 0.7),
                fontWeight: FontWeight.w500,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildBody(BuildContext context, HomeProvider provider) {
    switch (provider.state) {
      case ScreenState.loading:
        return _buildLoadingState();
      case ScreenState.error:
        return ErrorStateWidget(
          message: provider.errorMessage ?? 'เกิดข้อผิดพลาด',
          onRetry: provider.refresh,
        );
      case ScreenState.empty:
        return const EmptyStateWidget(
          icon: Icons.event_available_rounded,
          title: 'ไม่มีกิจกรรมในวันนี้',
          subtitle: 'ยังไม่มีตารางปฏิบัติงานในวันที่เลือก\nลองเลือกดูวันอื่น',
        );
      case ScreenState.success:
        return _buildSuccessState(provider);
    }
  }

  Widget _buildLoadingState() {
    return Column(
      children: [
        const SizedBox(height: sp12),
        ...List.generate(3, (i) => Padding(
          padding: const EdgeInsets.only(bottom: sp16),
          child: ShimmerLoader(height: 220, borderRadius: r20),
        )),
      ],
    );
  }

  Widget _buildSuccessState(HomeProvider provider) {
    final staffWithEvents = provider.staffWithEvents;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: sp8),
        ...staffWithEvents.asMap().entries.map(
          (entry) => FadeSlideIn(
            index: entry.key,
            child: Padding(
              padding: const EdgeInsets.only(bottom: sp20),
              child: _buildStaffCard(entry.value),
            ),
          ),
        ),
      ],
    );
  }

  // ── Staff Card ─────────────────────────────────────────────────────
  Widget _buildStaffCard(StaffModel staff) {
    final gradientColors = _avatarGradients[staff.id % _avatarGradients.length];

    return Container(
      decoration: BoxDecoration(
        color: bg1,
        borderRadius: r20,
        border: Border.all(color: border1, width: 1),
        boxShadow: [shadowMD],
      ),
      clipBehavior: Clip.antiAlias,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // ── Hero header with photo ────────────────────────────
          Container(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [
                  gradientColors[0].withValues(alpha: 0.08),
                  gradientColors[1].withValues(alpha: 0.04),
                ],
              ),
            ),
            padding: const EdgeInsets.all(sp16),
            child: Row(
              children: [
                // Staff photo
                _buildStaffPhoto(staff, gradientColors),
                const SizedBox(width: sp16),
                // Name & position
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        staff.name,
                        style: bodyMd.copyWith(
                          fontWeight: FontWeight.w800,
                          height: 1.3,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: sp4),
                      Row(
                        children: [
                          Icon(Icons.badge_outlined, size: 13, color: gradientColors[0]),
                          const SizedBox(width: sp4),
                          Expanded(
                            child: Text(
                              staff.position,
                              style: labelSM.copyWith(
                                color: txt2,
                                fontWeight: FontWeight.w500,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                      if (staff.department.isNotEmpty) ...[
                        const SizedBox(height: 2),
                        Row(
                          children: [
                            Icon(Icons.apartment_rounded, size: 13, color: gradientColors[0].withValues(alpha: 0.6)),
                            const SizedBox(width: sp4),
                            Expanded(
                              child: Text(
                                staff.department,
                                style: labelXS.copyWith(color: txt3),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ],
                  ),
                ),
                // Events count badge
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: sp12, vertical: sp8),
                  decoration: BoxDecoration(
                    color: gradientColors[0].withValues(alpha: 0.1),
                    borderRadius: r12,
                    border: Border.all(color: gradientColors[0].withValues(alpha: 0.2)),
                  ),
                  child: Column(
                    children: [
                      Text(
                        '${staff.events.length}',
                        style: titleSM.copyWith(
                          color: gradientColors[0],
                          fontWeight: FontWeight.w800,
                          fontSize: 18,
                        ),
                      ),
                      Text(
                        'กิจกรรม',
                        style: labelXS.copyWith(
                          color: gradientColors[0].withValues(alpha: 0.7),
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // ── Events timeline ──────────────────────────────────
          ...staff.events.asMap().entries.map((entry) {
            final event = entry.value;
            final isLast = entry.key == staff.events.length - 1;
            return _buildEventItem(event, gradientColors[0], isLast: isLast);
          }),
        ],
      ),
    );
  }

  // ── Staff Photo ────────────────────────────────────────────────────
  Widget _buildStaffPhoto(StaffModel staff, List<Color> gradientColors) {
    return Container(
      width: 64,
      height: 64,
      decoration: BoxDecoration(
        borderRadius: r16,
        gradient: !staff.hasPhoto
            ? LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: gradientColors,
              )
            : null,
        boxShadow: [
          BoxShadow(
            color: gradientColors[0].withValues(alpha: 0.3),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      clipBehavior: Clip.antiAlias,
      child: staff.hasPhoto
          ? CachedNetworkImage(
              imageUrl: staff.photoUrl!,
              fit: BoxFit.cover,
              placeholder: (context, url) => Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: gradientColors,
                  ),
                ),
                child: Center(
                  child: SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: Colors.white.withValues(alpha: 0.7),
                    ),
                  ),
                ),
              ),
              errorWidget: (context, url, error) => Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: gradientColors,
                  ),
                ),
                child: Center(
                  child: Text(
                    staff.initials,
                    style: const TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                ),
              ),
            )
          : Center(
              child: Text(
                staff.initials,
                style: const TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.w700,
                  color: Colors.white,
                ),
              ),
            ),
    );
  }

  // ── Event timeline item ────────────────────────────────────────────
  Widget _buildEventItem(EventModel event, Color accentColor, {bool isLast = false}) {
    return Container(
      decoration: BoxDecoration(
        border: Border(
          top: BorderSide(color: border1, width: 1),
        ),
      ),
      child: IntrinsicHeight(
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // ── Left timeline rail ──
            SizedBox(
              width: 48,
              child: Column(
                children: [
                  const SizedBox(height: sp16),
                  Container(
                    width: 10,
                    height: 10,
                    decoration: BoxDecoration(
                      color: event.statusColor,
                      borderRadius: rFull,
                      boxShadow: [
                        BoxShadow(
                          color: event.statusColor.withValues(alpha: 0.4),
                          blurRadius: 6,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                  ),
                  if (!isLast)
                    Expanded(
                      child: Container(
                        width: 2,
                        margin: const EdgeInsets.symmetric(vertical: sp4),
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                            colors: [
                              event.statusColor.withValues(alpha: 0.3),
                              event.statusColor.withValues(alpha: 0.05),
                            ],
                          ),
                        ),
                      ),
                    )
                  else
                    const Expanded(child: SizedBox()),
                ],
              ),
            ),

            // ── Event content ──
            Expanded(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(0, sp12, sp16, sp16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Time + status row
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: sp8, vertical: 3),
                          decoration: BoxDecoration(
                            color: accentColor.withValues(alpha: 0.08),
                            borderRadius: rFull,
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(Icons.schedule_rounded, size: 12, color: accentColor),
                              const SizedBox(width: sp4),
                              Text(
                                event.displayTimeRange,
                                style: labelSM.copyWith(
                                  fontWeight: FontWeight.w700,
                                  color: accentColor,
                                ),
                              ),
                            ],
                          ),
                        ),
                        if (event.isMultiDay) ...[
                          const SizedBox(width: sp4),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: sp8, vertical: 3),
                            decoration: BoxDecoration(
                              color: warningLight,
                              borderRadius: rFull,
                            ),
                            child: Text(
                              'หลายวัน',
                              style: labelXS.copyWith(
                                color: warning,
                                fontWeight: FontWeight.w600,
                              ),
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

                    const SizedBox(height: sp8),

                    // Event title
                    Text(
                      event.title,
                      style: body.copyWith(fontWeight: FontWeight.w700, height: 1.4),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),

                    const SizedBox(height: sp8),

                    // Location
                    if (event.location != null && event.location!.isNotEmpty)
                      _buildDetailRow(
                        Icons.location_on_rounded,
                        event.location!,
                        accent,
                      ),

                    // Organization
                    if (event.organization != null && event.organization!.isNotEmpty) ...[
                      const SizedBox(height: sp4),
                      _buildDetailRow(
                        Icons.business_rounded,
                        event.organization!,
                        accent2,
                      ),
                    ],

                    // Description
                    if (event.description != null && event.description!.isNotEmpty) ...[
                      const SizedBox(height: sp4),
                      _buildDetailRow(
                        Icons.notes_rounded,
                        event.description!,
                        txt3,
                      ),
                    ],
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ── Detail row for location/org/description ────────────────────────
  Widget _buildDetailRow(IconData icon, String text, Color color) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: 20,
          height: 20,
          decoration: BoxDecoration(
            color: color.withValues(alpha: 0.08),
            borderRadius: r8,
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

  // ── Logo fallback widget ─────────────────────────────────────────────
  Widget _logoFallback() {
    return Container(
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF1D4ED8), Color(0xFF3B82F6)],
        ),
        borderRadius: r14,
      ),
      child: const Center(
        child: Icon(Icons.account_balance_rounded, color: Colors.white, size: 22),
      ),
    );
  }
}

// ── Horizontal Date Picker ─────────────────────────────────────────
class _DatePickerDelegate extends SliverPersistentHeaderDelegate {
  final DateTime selectedDate;
  final ValueChanged<DateTime> onDateSelected;

  _DatePickerDelegate({
    required this.selectedDate,
    required this.onDateSelected,
  });

  static const _thaiDaysShort = ['จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส', 'อา'];

  @override
  double get minExtent => 88;
  @override
  double get maxExtent => 88;

  @override
  Widget build(BuildContext context, double shrinkOffset, bool overlapsContent) {
    final today = DateTime.now();
    final startOfWeek = today.subtract(const Duration(days: 3));

    // Find which month/year range is visible
    final midDate = startOfWeek.add(const Duration(days: 7));
    final thaiMonthsFull = [
      '', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน',
      'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม',
      'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม',
    ];
    final monthLabel = '${thaiMonthsFull[midDate.month]} ${midDate.year + 543}';

    return Container(
      color: bg0,
      child: Column(
        children: [
          const Divider(height: 1, color: border1),
          // Month label strip
          Padding(
            padding: const EdgeInsets.only(left: sp16, top: sp6),
            child: Row(
              children: [
                Text(
                  monthLabel,
                  style: const TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w700,
                    color: txt3,
                    letterSpacing: 0.5,
                  ),
                ),
              ],
            ),
          ),
          // Date pills
          Expanded(
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.fromLTRB(sp12, sp4, sp12, sp10),
              itemCount: 14,
              itemBuilder: (context, index) {
                final date = startOfWeek.add(Duration(days: index));
                final isSelected = date.year == selectedDate.year &&
                    date.month == selectedDate.month &&
                    date.day == selectedDate.day;
                final isToday = date.year == today.year &&
                    date.month == today.month &&
                    date.day == today.day;

                return GestureDetector(
                  onTap: () => onDateSelected(date),
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    curve: Curves.easeOutCubic,
                    width: 48,
                    margin: const EdgeInsets.symmetric(horizontal: 3),
                    decoration: BoxDecoration(
                      gradient: isSelected
                          ? const LinearGradient(
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                              colors: [Color(0xFF2563EB), Color(0xFF3B82F6)],
                            )
                          : null,
                      color: isSelected ? null : Colors.transparent,
                      borderRadius: BorderRadius.circular(14),
                      border: isToday && !isSelected
                          ? Border.all(color: accent, width: 1.5)
                          : Border.all(color: Colors.transparent),
                      boxShadow: isSelected
                          ? [
                              BoxShadow(
                                color: accent.withValues(alpha: 0.35),
                                blurRadius: 10,
                                offset: const Offset(0, 4),
                              ),
                            ]
                          : null,
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(
                          _thaiDaysShort[(date.weekday - 1) % 7],
                          style: TextStyle(
                            fontSize: 10,
                            fontWeight: FontWeight.w600,
                            color: isSelected
                                ? Colors.white.withValues(alpha: 0.75)
                                : (isToday ? accent : txt3),
                          ),
                        ),
                        const SizedBox(height: 3),
                        Text(
                          '${date.day}',
                          style: TextStyle(
                            fontSize: 17,
                            fontWeight: FontWeight.w800,
                            color: isSelected
                                ? Colors.white
                                : (isToday ? accent : txt1),
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  @override
  bool shouldRebuild(covariant _DatePickerDelegate oldDelegate) {
    return selectedDate != oldDelegate.selectedDate;
  }
}
