import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:provider/provider.dart';
import '../core/theme/tokens.dart';
import '../data/api_models.dart';
import '../providers/notification_provider.dart';
import '../providers/home_provider.dart';
import '../widgets/app_card.dart';
import '../widgets/shimmer_loader.dart';
import '../widgets/fade_slide_in.dart';
import '../widgets/error_state.dart';
import '../widgets/empty_state.dart';
import 'staff_detail_screen.dart';

class StaffListScreen extends StatelessWidget {
  const StaffListScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<StaffListProvider>(
      builder: (context, provider, _) {
        return RefreshIndicator(
          color: accent,
          backgroundColor: bg1,
          onRefresh: provider.refresh,
          child: CustomScrollView(
            slivers: [
              _buildAppBar(context),
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

  Widget _buildAppBar(BuildContext context) {
    return SliverAppBar(
      floating: true,
      snap: true,
      backgroundColor: bg0,
      surfaceTintColor: Colors.transparent,
      toolbarHeight: 52,
      title: Text('บุคลากร', style: titleSM.copyWith(fontSize: 18)),
    );
  }

  Widget _buildBody(BuildContext context, StaffListProvider provider) {
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
          icon: Icons.people_outline_rounded,
          title: 'ไม่พบข้อมูลบุคลากร',
          subtitle: 'ยังไม่มีข้อมูลบุคลากรในระบบ',
        );
      case ScreenState.success:
        return _buildSuccessState(context, provider);
    }
  }

  Widget _buildLoadingState() {
    return Column(
      children: [
        const SizedBox(height: sp16),
        const ShimmerListLoader(itemCount: 5),
      ],
    );
  }

  Widget _buildSuccessState(BuildContext context, StaffListProvider provider) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: sp12),
        FadeSlideIn(
          index: 0,
          child: Text(
            'รายชื่อบุคลากรทั้งหมด ${provider.staffList.length} คน',
            style: labelSM.copyWith(color: txt3),
          ),
        ),
        const SizedBox(height: sp12),
        ...provider.staffList.asMap().entries.map(
          (entry) => FadeSlideIn(
            index: entry.key + 1,
            child: Padding(
              padding: const EdgeInsets.only(bottom: sp12),
              child: _buildStaffCard(context, entry.value),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildStaffCard(BuildContext context, StaffModel staff) {
    final colors = [
      [const Color(0xFF2563EB), const Color(0xFF60A5FA)],
      [const Color(0xFF7C3AED), const Color(0xFFA78BFA)],
      [const Color(0xFF059669), const Color(0xFF34D399)],
      [const Color(0xFFD97706), const Color(0xFFFBBF24)],
      [const Color(0xFFDC2626), const Color(0xFFF87171)],
    ];
    final gradColors = colors[staff.id % colors.length];

    return AppCard(
      onTap: () async {
        final provider =
            context.read<StaffListProvider>();
        await provider.selectStaff(staff);
        if (context.mounted) {
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (_) => ChangeNotifierProvider.value(
                value: provider,
                child: const StaffDetailScreen(),
              ),
            ),
          );
        }
      },
      child: Row(
        children: [
          _buildAvatar(staff, gradColors),
          const SizedBox(width: sp16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  staff.name,
                  style: bodyMd.copyWith(fontWeight: FontWeight.w700),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: sp4),
                Text(
                  staff.position,
                  style: labelSM.copyWith(color: txt2),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                if (staff.department.isNotEmpty) ...[
                  const SizedBox(height: 2),
                  Text(
                    staff.department,
                    style: labelXS.copyWith(color: txt3),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ],
            ),
          ),
          const SizedBox(width: sp12),
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              const Icon(Icons.chevron_right_rounded, color: txt3, size: 20),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildAvatar(StaffModel staff, List<Color> gradColors) {
    return Container(
      width: 60,
      height: 60,
      decoration: BoxDecoration(
        borderRadius: r16,
        boxShadow: [
          BoxShadow(
            color: gradColors[0].withValues(alpha: 0.25),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: r16,
        child: staff.hasPhoto
            ? CachedNetworkImage(
                imageUrl: staff.photoUrl!,
                fit: BoxFit.cover,
                placeholder: (context, url) => _gradientFallback(staff, gradColors),
                errorWidget: (context, url, error) =>
                    _gradientFallback(staff, gradColors),
              )
            : _gradientFallback(staff, gradColors),
      ),
    );
  }

  Widget _gradientFallback(StaffModel staff, List<Color> gradColors) {
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
          staff.initials,
          style: const TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.w700,
            color: Colors.white,
          ),
        ),
      ),
    );
  }
}
