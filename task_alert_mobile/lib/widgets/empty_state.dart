import 'package:flutter/material.dart';
import '../core/theme/tokens.dart';

class EmptyStateWidget extends StatelessWidget {
  final IconData icon;
  final String title;
  final String subtitle;

  const EmptyStateWidget({
    super.key,
    required this.icon,
    required this.title,
    required this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(sp32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 80,
              height: 80,
              decoration: BoxDecoration(
                color: bg2,
                borderRadius: rFull,
                border: Border.all(color: border1),
              ),
              child: Icon(icon, color: txt3, size: 36),
            ),
            const SizedBox(height: sp20),
            Text(
              title,
              style: titleSM.copyWith(fontSize: 18),
            ),
            const SizedBox(height: sp8),
            Text(
              subtitle,
              style: labelSM.copyWith(height: 1.7),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }
}
