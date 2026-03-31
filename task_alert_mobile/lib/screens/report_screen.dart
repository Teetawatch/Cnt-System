import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:provider/provider.dart';
import '../core/theme/tokens.dart';
import '../data/api_models.dart';
import '../providers/report_provider.dart';
import '../providers/home_provider.dart';
import '../widgets/app_card.dart';
import '../widgets/shimmer_loader.dart';
import '../widgets/fade_slide_in.dart';
import '../widgets/error_state.dart';
import '../widgets/empty_state.dart';
import '../widgets/app_button.dart';

class ReportScreen extends StatelessWidget {
  const ReportScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<ReportProvider>(
      builder: (context, provider, _) {
        return RefreshIndicator(
          color: accent,
          backgroundColor: bg1,
          onRefresh: provider.refresh,
          child: CustomScrollView(
            slivers: [
              _buildAppBar(context, provider),
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

  static const _thaiMonths = [
    '', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน',
    'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม',
    'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม',
  ];

  static const _avatarColors = [
    [Color(0xFF2563EB), Color(0xFF60A5FA)],
    [Color(0xFF7C3AED), Color(0xFFA78BFA)],
    [Color(0xFF059669), Color(0xFF34D399)],
    [Color(0xFFD97706), Color(0xFFFBBF24)],
    [Color(0xFFDC2626), Color(0xFFF87171)],
  ];

  Widget _buildAppBar(BuildContext context, ReportProvider provider) {
    final buddhistYear = provider.selectedYear + 543;
    final monthName = _thaiMonths[provider.selectedMonth];

    return SliverAppBar(
      floating: true,
      snap: true,
      backgroundColor: bg0,
      surfaceTintColor: Colors.transparent,
      toolbarHeight: 60,
      title: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text('สรุปผลประจำเดือน', style: titleSM.copyWith(fontSize: 18)),
          Text(
            '$monthName พ.ศ. $buddhistYear',
            style: labelSM.copyWith(color: accent, fontWeight: FontWeight.w600),
          ),
        ],
      ),
      actions: [
        AppIconButton(
          icon: Icons.chevron_left_rounded,
          onPressed: () {
            int m = provider.selectedMonth - 1;
            int y = provider.selectedYear;
            if (m < 1) { m = 12; y--; }
            provider.selectMonth(m, y);
          },
        ),
        AppIconButton(
          icon: Icons.chevron_right_rounded,
          onPressed: () {
            int m = provider.selectedMonth + 1;
            int y = provider.selectedYear;
            if (m > 12) { m = 1; y++; }
            provider.selectMonth(m, y);
          },
        ),
        const SizedBox(width: sp8),
      ],
    );
  }

  Widget _buildBody(BuildContext context, ReportProvider provider) {
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
          icon: Icons.bar_chart_rounded,
          title: 'ไม่มีข้อมูลรายงาน',
          subtitle: 'ยังไม่มีข้อมูลเพียงพอสำหรับแสดงรายงาน',
        );
      case ScreenState.success:
        return _buildSuccessState(provider);
    }
  }

  Widget _buildLoadingState() {
    return Column(
      children: [
        const SizedBox(height: sp16),
        ShimmerLoader(height: 160, borderRadius: r20),
        const SizedBox(height: sp16),
        Row(
          children: [
            Expanded(child: ShimmerLoader(height: 100, borderRadius: r16)),
            const SizedBox(width: sp12),
            Expanded(child: ShimmerLoader(height: 100, borderRadius: r16)),
          ],
        ),
        const SizedBox(height: sp24),
        ShimmerLoader(height: 200, borderRadius: r16),
      ],
    );
  }

  Widget _buildSuccessState(ReportProvider provider) {
    final stats = provider.stats!;
    final buddhistYear = stats.year + 543;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: sp12),

        // Month title
        FadeSlideIn(
          index: 0,
          child: Center(
            child: Text(
              '${stats.thaiMonth} พ.ศ. $buddhistYear',
              style: bodyMd.copyWith(fontWeight: FontWeight.w600, color: txt2),
            ),
          ),
        ),
        const SizedBox(height: sp16),

        // Summary hero card
        FadeSlideIn(
          index: 1,
          child: _buildSummaryCard(stats.summary),
        ),
        const SizedBox(height: sp16),

        // Stats grid
        FadeSlideIn(
          index: 2,
          child: Row(
            children: [
              Expanded(child: _buildStatCard('ยืนยันแล้ว', '${stats.summary.confirmed}', success, Icons.check_circle_outline)),
              const SizedBox(width: sp12),
              Expanded(child: _buildStatCard('รอยืนยัน', '${stats.summary.pending}', warning, Icons.hourglass_empty_rounded)),
            ],
          ),
        ),
        const SizedBox(height: sp12),
        FadeSlideIn(
          index: 3,
          child: Row(
            children: [
              Expanded(child: _buildStatCard('ยกเลิก', '${stats.summary.cancelled}', danger, Icons.cancel_outlined)),
              const SizedBox(width: sp12),
              Expanded(child: _buildStatCard('ทั้งหมด', '${stats.summary.totalEvents}', accent2, Icons.event_note_rounded)),
            ],
          ),
        ),
        const SizedBox(height: sp24),

        // Daily activity chart
        FadeSlideIn(
          index: 4,
          child: _buildSectionTitle('กิจกรรมรายวัน'),
        ),
        const SizedBox(height: sp12),
        FadeSlideIn(
          index: 5,
          child: _buildDailyChart(stats.dailyData),
        ),
        const SizedBox(height: sp24),

        // Staff rankings
        FadeSlideIn(
          index: 6,
          child: _buildSectionTitle('กิจกรรมรายบุคคล'),
        ),
        const SizedBox(height: sp12),
        FadeSlideIn(
          index: 7,
          child: _buildStaffRanking(stats.staffStats),
        ),
      ],
    );
  }

  Widget _buildSummaryCard(StatsSummary summary) {
    final rate = summary.completionRate;
    final pct = (rate * 100).toStringAsFixed(1);

    return ElevatedAppCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'ภาพรวมกิจกรรม',
                style: labelSM.copyWith(
                  letterSpacing: 1.2,
                  fontWeight: FontWeight.w600,
                  color: Colors.white70,
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: sp12, vertical: sp4),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.15),
                  borderRadius: rFull,
                ),
                child: Text(
                  '$pct%',
                  style: labelSM.copyWith(
                    color: Colors.white,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: sp12),
          Row(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                '${summary.totalEvents}',
                style: display.copyWith(color: Colors.white, fontSize: 36),
              ),
              Padding(
                padding: const EdgeInsets.only(bottom: 6, left: sp8),
                child: Text('กิจกรรม', style: bodyMd.copyWith(color: Colors.white60)),
              ),
            ],
          ),
          const SizedBox(height: sp12),
          // Completion progress bar
          ClipRRect(
            borderRadius: rFull,
            child: LinearProgressIndicator(
              value: rate,
              backgroundColor: Colors.white.withValues(alpha: 0.2),
              valueColor: const AlwaysStoppedAnimation(Colors.white),
              minHeight: 6,
            ),
          ),
          const SizedBox(height: sp16),
          Row(
            children: [
              _buildMiniStat('ยืนยันแล้ว', summary.confirmed, const Color(0xFF4ADE80)),
              const SizedBox(width: sp20),
              _buildMiniStat('รอยืนยัน', summary.pending, const Color(0xFFFBBF24)),
              const SizedBox(width: sp20),
              _buildMiniStat('ยกเลิก', summary.cancelled, const Color(0xFFF87171)),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildMiniStat(String label, int value, Color color) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Container(
              width: 8,
              height: 8,
              decoration: BoxDecoration(color: color, borderRadius: rFull),
            ),
            const SizedBox(width: sp4),
            Text(
              '$value',
              style: bodyMd.copyWith(fontWeight: FontWeight.w700, color: Colors.white),
            ),
          ],
        ),
        Text(label, style: labelXS.copyWith(color: Colors.white.withValues(alpha: 0.6))),
      ],
    );
  }

  Widget _buildStatCard(String label, String value, Color color, IconData icon) {
    return AppCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container(
                width: 36,
                height: 36,
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.12),
                  borderRadius: r8,
                ),
                child: Icon(icon, size: 18, color: color),
              ),
              Text(value, style: titleMD.copyWith(fontWeight: FontWeight.w800)),
            ],
          ),
          const SizedBox(height: sp12),
          Text(label, style: labelSM),
        ],
      ),
    );
  }

  Widget _buildDailyChart(List<DailyData> data) {
    if (data.isEmpty) {
      return AppCard(
        child: Center(
          child: Padding(
            padding: const EdgeInsets.all(sp20),
            child: Text('ไม่มีข้อมูล', style: labelSM.copyWith(color: txt3)),
          ),
        ),
      );
    }

    final maxVal = data
        .map((d) => d.total)
        .reduce((a, b) => a > b ? a : b)
        .toDouble();

    // Show only days with events for cleaner chart, up to 15 days
    final activeDays = data.where((d) => d.total > 0).toList();
    final displayDays = activeDays.length > 15 ? activeDays.sublist(0, 15) : activeDays;

    if (displayDays.isEmpty) {
      return AppCard(
        child: Center(
          child: Padding(
            padding: const EdgeInsets.all(sp20),
            child: Text('ไม่มีกิจกรรมในเดือนนี้', style: labelSM.copyWith(color: txt3)),
          ),
        ),
      );
    }

    return AppCard(
      child: Column(
        children: [
          Row(
            children: [
              _buildLegendDot('ทั้งหมด', accent),
              const SizedBox(width: sp16),
              _buildLegendDot('ยืนยัน', success),
            ],
          ),
          const SizedBox(height: sp20),
          SizedBox(
            height: 140,
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: displayDays.map((d) {
                return Expanded(
                  child: Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 2),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            _buildBar(maxVal > 0 ? d.total / maxVal : 0, accent, 8),
                            const SizedBox(width: 2),
                            _buildBar(maxVal > 0 ? d.confirmed / maxVal : 0, success, 8),
                          ],
                        ),
                        const SizedBox(height: sp4),
                        Text('${d.day}', style: labelXS),
                      ],
                    ),
                  ),
                );
              }).toList(),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildBar(double ratio, Color color, double width) {
    return Container(
      width: width,
      height: (100 * ratio).clamp(2, 100),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.8),
        borderRadius: const BorderRadius.vertical(top: Radius.circular(4)),
      ),
    );
  }

  Widget _buildLegendDot(String label, Color color) {
    return Row(
      children: [
        Container(
          width: 8,
          height: 8,
          decoration: BoxDecoration(color: color, borderRadius: rFull),
        ),
        const SizedBox(width: sp4),
        Text(label, style: labelXS.copyWith(color: txt2)),
      ],
    );
  }

  Widget _buildStaffRanking(List<StaffStat> staffStats) {
    if (staffStats.isEmpty) {
      return AppCard(
        child: Center(
          child: Padding(
            padding: const EdgeInsets.all(sp20),
            child: Text('ไม่มีข้อมูล', style: labelSM.copyWith(color: txt3)),
          ),
        ),
      );
    }

    final sorted = [...staffStats]..sort((a, b) => b.eventsCount.compareTo(a.eventsCount));
    final maxCount = sorted.isEmpty ? 1.0 : sorted.first.eventsCount.toDouble();
    final medalColors = [const Color(0xFFFFD700), const Color(0xFFC0C0C0), const Color(0xFFCD7F32)];

    return AppCard(
      padding: EdgeInsets.zero,
      child: Column(
        children: sorted.asMap().entries.map((entry) {
          final rank = entry.key;
          final staff = entry.value;
          final isLast = rank == sorted.length - 1;
          final gradColors = _avatarColors[staff.id % _avatarColors.length];

          return Column(
            children: [
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: sp16, vertical: sp12),
                child: Row(
                  children: [
                    // Rank number / medal
                    SizedBox(
                      width: 28,
                      child: rank < 3
                          ? Icon(Icons.emoji_events_rounded,
                              color: medalColors[rank], size: 20)
                          : Text(
                              '${rank + 1}',
                              style: labelSM.copyWith(
                                fontWeight: FontWeight.w700,
                                color: txt3,
                              ),
                              textAlign: TextAlign.center,
                            ),
                    ),
                    const SizedBox(width: sp8),
                    // Staff avatar
                    _buildRankAvatar(staff, gradColors),
                    const SizedBox(width: sp12),
                    // Name + progress bar
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            staff.name,
                            style: body.copyWith(fontWeight: FontWeight.w600),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                          Text(
                            staff.position,
                            style: labelXS.copyWith(color: txt3),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: sp6),
                          ClipRRect(
                            borderRadius: rFull,
                            child: LinearProgressIndicator(
                              value: maxCount > 0 ? staff.eventsCount / maxCount : 0,
                              backgroundColor: bg2,
                              valueColor: AlwaysStoppedAnimation(
                                rank < 3 ? gradColors[0] : txt3.withValues(alpha: 0.4),
                              ),
                              minHeight: 5,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: sp12),
                    // Count badge
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: sp10, vertical: sp4),
                      decoration: BoxDecoration(
                        color: rank < 3
                            ? gradColors[0].withValues(alpha: 0.1)
                            : bg2,
                        borderRadius: r8,
                      ),
                      child: Text(
                        '${staff.eventsCount}',
                        style: bodyMd.copyWith(
                          fontWeight: FontWeight.w800,
                          color: rank < 3 ? gradColors[0] : txt2,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              if (!isLast)
                Divider(height: 1, color: border1, indent: sp16 + 28 + sp8),
            ],
          );
        }).toList(),
      ),
    );
  }

  Widget _buildRankAvatar(StaffStat staff, List<Color> gradColors) {
    return Container(
      width: 40,
      height: 40,
      decoration: BoxDecoration(
        borderRadius: r12,
        boxShadow: [
          BoxShadow(
            color: gradColors[0].withValues(alpha: 0.2),
            blurRadius: 6,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: r12,
        child: staff.photoUrl != null && staff.photoUrl!.isNotEmpty
            ? CachedNetworkImage(
                imageUrl: staff.photoUrl!,
                fit: BoxFit.cover,
                placeholder: (context, url) => _avatarInitials(staff, gradColors),
                errorWidget: (context, url, error) => _avatarInitials(staff, gradColors),
              )
            : _avatarInitials(staff, gradColors),
      ),
    );
  }

  Widget _avatarInitials(StaffStat staff, List<Color> gradColors) {
    final parts = staff.name.trim().split(' ');
    final initials = parts.length >= 2
        ? '${parts[0][0]}${parts[1][0]}'
        : (staff.name.isNotEmpty ? staff.name[0] : '?');
    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: gradColors,
        ),
      ),
      child: Center(
        child: Text(
          initials,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w700,
            color: Colors.white,
          ),
        ),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(
      title.toUpperCase(),
      style: labelSM.copyWith(
        letterSpacing: 1.5,
        fontWeight: FontWeight.w600,
      ),
    );
  }
}
