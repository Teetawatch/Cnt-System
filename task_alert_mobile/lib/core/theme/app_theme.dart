import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'tokens.dart';

class AppTheme {
  static ThemeData get light {
    return ThemeData(
      brightness: Brightness.light,
      scaffoldBackgroundColor: bg0,
      canvasColor: bg0,
      cardColor: bg1,
      colorScheme: const ColorScheme.light(
        primary: accent,
        secondary: accent2,
        surface: bg1,
        error: danger,
        onPrimary: Colors.white,
        onSecondary: Colors.white,
        onSurface: txt1,
        onError: Colors.white,
      ),
      textTheme: GoogleFonts.sarabunTextTheme(
        const TextTheme(
          displayLarge: display,
          headlineLarge: titleLG,
          headlineMedium: titleMD,
          headlineSmall: titleSM,
          bodyLarge: bodyMd,
          bodyMedium: body,
          labelSmall: labelSM,
          labelMedium: labelXS,
        ),
      ),
      appBarTheme: AppBarTheme(
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: false,
        titleTextStyle: GoogleFonts.sarabunTextTheme().headlineSmall?.copyWith(
          fontSize: 20,
          fontWeight: FontWeight.w700,
          color: txt1,
        ),
        iconTheme: const IconThemeData(color: txt1),
      ),
      bottomNavigationBarTheme: BottomNavigationBarThemeData(
        backgroundColor: bg1,
        selectedItemColor: accent,
        unselectedItemColor: txt3,
        type: BottomNavigationBarType.fixed,
        elevation: 0,
        selectedLabelStyle: GoogleFonts.sarabun(
          fontSize: 12,
          fontWeight: FontWeight.w600,
        ),
        unselectedLabelStyle: GoogleFonts.sarabun(
          fontSize: 12,
          fontWeight: FontWeight.w400,
        ),
      ),
      dividerColor: border1,
      splashColor: accent.withValues(alpha: 0.08),
      highlightColor: accent.withValues(alpha: 0.04),
    );
  }
}
