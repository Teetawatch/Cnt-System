import 'package:flutter/material.dart';
import '../core/theme/tokens.dart';
import 'app_button.dart';

class ErrorStateWidget extends StatelessWidget {
  final String message;
  final VoidCallback? onRetry;

  const ErrorStateWidget({
    super.key,
    required this.message,
    this.onRetry,
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
              width: 64,
              height: 64,
              decoration: BoxDecoration(
                color: danger.withValues(alpha: 0.1),
                borderRadius: rFull,
              ),
              child: const Icon(
                Icons.error_outline_rounded,
                color: danger,
                size: 32,
              ),
            ),
            const SizedBox(height: sp16),
            Text(
              'เกิดข้อผิดพลาด',
              style: titleSM.copyWith(fontSize: 18),
            ),
            const SizedBox(height: sp8),
            Text(
              message,
              style: labelSM.copyWith(height: 1.7),
              textAlign: TextAlign.center,
            ),
            if (onRetry != null) ...[
              const SizedBox(height: sp24),
              SizedBox(
                width: 180,
                child: PrimaryButton(
                  label: 'ลองอีกครั้ง',
                  icon: Icons.refresh_rounded,
                  onPressed: onRetry,
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
