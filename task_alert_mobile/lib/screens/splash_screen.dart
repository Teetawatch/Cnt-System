import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:google_fonts/google_fonts.dart';
import '../core/theme/tokens.dart';
import '../services/notification_service.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with TickerProviderStateMixin {
  late AnimationController _logoController;
  late AnimationController _textController;
  late Animation<double> _logoScale;
  late Animation<double> _logoOpacity;
  late Animation<double> _textOpacity;
  late Animation<Offset> _textSlide;

  @override
  void initState() {
    super.initState();

    // Logo animations
    _logoController = AnimationController(
      duration: const Duration(milliseconds: 800),
      vsync: this,
    );

    _logoScale = Tween<double>(
      begin: 0.6,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _logoController,
      curve: Curves.elasticOut,
    ));

    _logoOpacity = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _logoController,
      curve: Curves.easeOut,
    ));

    // Text animations
    _textController = AnimationController(
      duration: const Duration(milliseconds: 600),
      vsync: this,
    );

    _textOpacity = Tween<double>(
      begin: 0.0,
      end: 1.0,
    ).animate(CurvedAnimation(
      parent: _textController,
      curve: Curves.easeOut,
    ));

    _textSlide = Tween<Offset>(
      begin: const Offset(0, 0.3),
      end: Offset.zero,
    ).animate(CurvedAnimation(
      parent: _textController,
      curve: Curves.easeOutCubic,
    ));

    // Start animations
    _logoController.forward();
    Future.delayed(const Duration(milliseconds: 400), () {
      if (mounted) _textController.forward();
    });

    // Init notifications in background (don't block animation)
    NotificationService.instance.init().catchError((e) {
      debugPrint('[Splash] NotificationService init failed: $e');
    });

    // Navigate to main app after 2.5 seconds
    Future.delayed(const Duration(milliseconds: 2500), () {
      if (mounted) _navigateToHome();
    });
  }

  void _navigateToHome() {
    Navigator.of(context).pushReplacementNamed('/home');
  }

  @override
  void dispose() {
    _logoController.dispose();
    _textController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: bg0,
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Logo with animation
            AnimatedBuilder(
              animation: _logoController,
              builder: (context, child) {
                return Transform.scale(
                  scale: _logoScale.value,
                  child: Opacity(
                    opacity: _logoOpacity.value,
                    child: _buildLogo(),
                  ),
                );
              },
            ),
            const SizedBox(height: sp32),
            // Organization name with animation
            AnimatedBuilder(
              animation: _textController,
              builder: (context, child) {
                return SlideTransition(
                  position: _textSlide,
                  child: FadeTransition(
                    opacity: _textOpacity,
                    child: _buildOrganizationName(),
                  ),
                );
              },
            ),
            const SizedBox(height: sp12),
            // Subtitle with animation
            AnimatedBuilder(
              animation: _textController,
              builder: (context, child) {
                return FadeTransition(
                  opacity: _textOpacity,
                  child: _buildSubtitle(),
                );
              },
            ),
            const SizedBox(height: sp48),
            // Loading indicator
            AnimatedBuilder(
              animation: _textController,
              builder: (context, child) {
                return FadeTransition(
                  opacity: _textOpacity,
                  child: _buildLoadingIndicator(),
                );
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLogo() {
    return Container(
      width: 120,
      height: 120,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(32),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF2563EB).withValues(alpha: 0.35),
            blurRadius: 24,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(32),
        child: CachedNetworkImage(
          imageUrl: 'https://workcnt.nass.ac.th/images/logo.png',
          fit: BoxFit.contain,
          placeholder: (context, url) => _logoFallback(),
          errorWidget: (context, url, error) => _logoFallback(),
        ),
      ),
    );
  }

  Widget _logoFallback() {
    return Container(
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF1D4ED8), Color(0xFF3B82F6)],
        ),
        borderRadius: BorderRadius.circular(32),
      ),
      child: const Center(
        child: Icon(
          Icons.account_balance_rounded,
          color: Colors.white,
          size: 56,
        ),
      ),
    );
  }

  Widget _buildOrganizationName() {
    return Text(
      'สพป.ชัยนาท',
      style: GoogleFonts.sarabun(
        fontSize: 32,
        fontWeight: FontWeight.w800,
        color: const Color(0xFF1D4ED8),
        letterSpacing: -0.5,
        height: 1.2,
      ),
    );
  }

  Widget _buildSubtitle() {
    return Text(
      'ตารางปฏิบัติงานประจำวัน',
      style: GoogleFonts.sarabun(
        fontSize: 16,
        fontWeight: FontWeight.w500,
        color: txt3,
        letterSpacing: 0.3,
      ),
    );
  }

  Widget _buildLoadingIndicator() {
    return Column(
      children: [
        SizedBox(
          width: 32,
          height: 32,
          child: CircularProgressIndicator(
            strokeWidth: 2.5,
            valueColor: const AlwaysStoppedAnimation<Color>(accent),
          ),
        ),
        const SizedBox(height: sp12),
        Text(
          'กำลังเริ่มระบบ...',
          style: GoogleFonts.sarabun(
            fontSize: 13,
            fontWeight: FontWeight.w500,
            color: txt3,
          ),
        ),
      ],
    );
  }
}
