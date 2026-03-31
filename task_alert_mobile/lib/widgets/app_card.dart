import 'package:flutter/material.dart';
import '../core/theme/tokens.dart';

class AppCard extends StatelessWidget {
  final Widget child;
  final EdgeInsetsGeometry? padding;
  final VoidCallback? onTap;

  const AppCard({
    super.key,
    required this.child,
    this.padding,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        decoration: BoxDecoration(
          color: bg1,
          borderRadius: r16,
          border: Border.all(color: border1, width: 1),
          boxShadow: [shadowSM],
        ),
        padding: padding ?? const EdgeInsets.all(sp16),
        child: child,
      ),
    );
  }
}

class ElevatedAppCard extends StatelessWidget {
  final Widget child;
  final EdgeInsetsGeometry? padding;
  final VoidCallback? onTap;

  const ElevatedAppCard({
    super.key,
    required this.child,
    this.padding,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Color(0xFF2563EB), Color(0xFF1D4ED8)],
          ),
          borderRadius: r20,
          boxShadow: [shadowMD, glowBlue],
        ),
        padding: padding ?? const EdgeInsets.all(sp20),
        child: child,
      ),
    );
  }
}
