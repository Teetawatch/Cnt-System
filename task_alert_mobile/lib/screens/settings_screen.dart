import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../core/theme/tokens.dart';
import '../core/config.dart';
import '../providers/profile_provider.dart';
import '../widgets/app_card.dart';
import '../widgets/fade_slide_in.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Consumer<SettingsProvider>(
      builder: (context, provider, _) {
        return CustomScrollView(
          slivers: [
            _buildAppBar(context),
            SliverPadding(
              padding: const EdgeInsets.symmetric(horizontal: sp20),
              sliver: SliverToBoxAdapter(
                child: _buildContent(context, provider),
              ),
            ),
            const SliverToBoxAdapter(child: SizedBox(height: 100)),
          ],
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
      title: Text('ตั้งค่า', style: titleSM.copyWith(fontSize: 18)),
    );
  }

  Widget _buildContent(BuildContext context, SettingsProvider provider) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const SizedBox(height: sp24),

        // App info
        FadeSlideIn(
          index: 0,
          child: Center(
            child: Column(
              children: [
                Container(
                  width: 80,
                  height: 80,
                  decoration: BoxDecoration(
                    borderRadius: r20,
                    gradient: const LinearGradient(
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                      colors: [accent, Color(0xFF60A5FA)],
                    ),
                    boxShadow: [glowBlue],
                  ),
                  child: const Center(
                    child: Icon(Icons.calendar_month_rounded, color: Colors.white, size: 36),
                  ),
                ),
                const SizedBox(height: sp16),
                Text('CNT Schedule', style: titleSM),
                const SizedBox(height: sp4),
                Text('ระบบตารางปฏิบัติงาน', style: labelSM),
                const SizedBox(height: sp8),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: sp12, vertical: sp4),
                  decoration: BoxDecoration(
                    color: accentLight,
                    borderRadius: rFull,
                    border: Border.all(color: accent.withValues(alpha: 0.25)),
                  ),
                  child: Text(
                    'v1.0.0',
                    style: labelXS.copyWith(color: accent, fontWeight: FontWeight.w600),
                  ),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: sp32),

        // Connection settings
        FadeSlideIn(
          index: 1,
          child: Text(
            'การเชื่อมต่อ'.toUpperCase(),
            style: labelSM.copyWith(
              letterSpacing: 1.5,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
        const SizedBox(height: sp12),
        FadeSlideIn(
          index: 2,
          child: AppCard(
            padding: EdgeInsets.zero,
            child: Column(
              children: [
                _buildMenuItem(
                  icon: Icons.dns_outlined,
                  label: 'เซิร์ฟเวอร์',
                  trailing: AppConfig.baseUrl,
                  color: accent,
                  showDivider: true,
                  onTap: () => _showServerUrlDialog(context, provider),
                ),
                _buildMenuItem(
                  icon: Icons.wifi_rounded,
                  label: 'สถานะการเชื่อมต่อ',
                  trailing: 'เชื่อมต่อแล้ว',
                  color: success,
                  showDivider: false,
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: sp24),

        // General settings
        FadeSlideIn(
          index: 3,
          child: Text(
            'ทั่วไป'.toUpperCase(),
            style: labelSM.copyWith(
              letterSpacing: 1.5,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
        const SizedBox(height: sp12),
        FadeSlideIn(
          index: 4,
          child: AppCard(
            padding: EdgeInsets.zero,
            child: Column(
              children: [
                _buildMenuItem(
                  icon: Icons.palette_outlined,
                  label: 'ธีม',
                  trailing: 'สว่าง',
                  color: warning,
                  showDivider: true,
                ),
                _buildMenuItem(
                  icon: Icons.language_rounded,
                  label: 'ภาษา',
                  trailing: 'ไทย',
                  color: accent2,
                  showDivider: false,
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: sp24),

        // About
        FadeSlideIn(
          index: 5,
          child: Text(
            'เกี่ยวกับ'.toUpperCase(),
            style: labelSM.copyWith(
              letterSpacing: 1.5,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
        const SizedBox(height: sp12),
        FadeSlideIn(
          index: 6,
          child: AppCard(
            padding: EdgeInsets.zero,
            child: Column(
              children: [
                _buildMenuItem(
                  icon: Icons.info_outline_rounded,
                  label: 'เวอร์ชัน',
                  trailing: '1.0.0',
                  color: txt2,
                  showDivider: true,
                ),
                _buildMenuItem(
                  icon: Icons.code_rounded,
                  label: 'พัฒนาโดย',
                  trailing: 'teecodeworks',
                  color: txt2,
                  showDivider: false,
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildMenuItem({
    required IconData icon,
    required String label,
    required Color color,
    required bool showDivider,
    String? trailing,
    VoidCallback? onTap,
  }) {
    return Column(
      children: [
        InkWell(
          onTap: onTap ?? () {},
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: sp16, vertical: sp12),
            child: Row(
              children: [
                Container(
                  width: 32,
                  height: 32,
                  decoration: BoxDecoration(
                    color: color.withValues(alpha: 0.12),
                    borderRadius: r8,
                  ),
                  child: Icon(icon, size: 16, color: color),
                ),
                const SizedBox(width: sp12),
                Expanded(
                  child: Text(label, style: body),
                ),
                if (trailing != null) ...[
                  Flexible(
                    child: Text(
                      trailing,
                      style: labelSM.copyWith(color: txt3),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  const SizedBox(width: sp8),
                ],
                const Icon(Icons.chevron_right_rounded, size: 20, color: txt3),
              ],
            ),
          ),
        ),
        if (showDivider)
          Divider(color: border1, height: 1, indent: sp16 + 32 + sp12),
      ],
    );
  }

  void _showServerUrlDialog(BuildContext context, SettingsProvider provider) {
    final controller = TextEditingController(text: provider.serverUrl);
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: bg1,
        shape: RoundedRectangleBorder(borderRadius: r16),
        title: Text('เปลี่ยน URL เซิร์ฟเวอร์', style: bodyMd.copyWith(fontWeight: FontWeight.w700)),
        content: TextField(
          controller: controller,
          decoration: InputDecoration(
            hintText: 'http://10.0.2.2:8000',
            hintStyle: labelSM.copyWith(color: txt3),
            border: OutlineInputBorder(borderRadius: r12),
            contentPadding: const EdgeInsets.symmetric(horizontal: sp16, vertical: sp12),
          ),
          style: body,
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: Text('ยกเลิก', style: body.copyWith(color: txt3)),
          ),
          TextButton(
            onPressed: () {
              provider.updateServerUrl(controller.text.trim());
              Navigator.pop(context);
            },
            child: Text('บันทึก', style: body.copyWith(color: accent, fontWeight: FontWeight.w600)),
          ),
        ],
      ),
    );
  }
}
