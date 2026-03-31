import 'package:flutter/material.dart';
import 'package:shimmer/shimmer.dart';
import '../core/theme/tokens.dart';

class ShimmerLoader extends StatelessWidget {
  final double width;
  final double height;
  final BorderRadius? borderRadius;

  const ShimmerLoader({
    super.key,
    this.width = double.infinity,
    required this.height,
    this.borderRadius,
  });

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: bg2,
      highlightColor: bg3,
      child: Container(
        width: width,
        height: height,
        decoration: BoxDecoration(
          color: bg2,
          borderRadius: borderRadius ?? r12,
        ),
      ),
    );
  }
}

class ShimmerCard extends StatelessWidget {
  const ShimmerCard({super.key});

  @override
  Widget build(BuildContext context) {
    return Shimmer.fromColors(
      baseColor: bg2,
      highlightColor: bg3,
      child: Container(
        padding: const EdgeInsets.all(sp16),
        decoration: BoxDecoration(
          color: bg1,
          borderRadius: r16,
          border: Border.all(color: border1),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 120,
              height: 12,
              decoration: BoxDecoration(color: bg2, borderRadius: r8),
            ),
            const SizedBox(height: sp12),
            Container(
              width: double.infinity,
              height: 14,
              decoration: BoxDecoration(color: bg2, borderRadius: r8),
            ),
            const SizedBox(height: sp8),
            Container(
              width: 200,
              height: 14,
              decoration: BoxDecoration(color: bg2, borderRadius: r8),
            ),
            const SizedBox(height: sp16),
            Row(
              children: [
                Container(
                  width: 60,
                  height: 20,
                  decoration: BoxDecoration(color: bg2, borderRadius: rFull),
                ),
                const SizedBox(width: sp8),
                Container(
                  width: 80,
                  height: 20,
                  decoration: BoxDecoration(color: bg2, borderRadius: rFull),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class ShimmerListLoader extends StatelessWidget {
  final int itemCount;

  const ShimmerListLoader({super.key, this.itemCount = 5});

  @override
  Widget build(BuildContext context) {
    return ListView.separated(
      physics: const NeverScrollableScrollPhysics(),
      shrinkWrap: true,
      itemCount: itemCount,
      separatorBuilder: (context, index) => const SizedBox(height: sp12),
      itemBuilder: (context, index) => const ShimmerCard(),
    );
  }
}
