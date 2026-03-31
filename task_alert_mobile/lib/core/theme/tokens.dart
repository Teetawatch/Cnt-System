import 'package:flutter/material.dart';

// ── Colors ────────────────────────────────────────────────────────
// Background layers (light theme)
const bg0 = Color(0xFFF6F8FC);   // deepest background — soft blue-gray
const bg1 = Color(0xFFFFFFFF);   // card / surface — pure white
const bg2 = Color(0xFFF0F3F9);   // elevated surface / input bg
const bg3 = Color(0xFFE4E9F2);   // hover / pressed

// Accent
const accent  = Color(0xFF2563EB);   // primary action — vivid blue
const accent2 = Color(0xFF7C3AED);   // secondary — purple
const success = Color(0xFF059669);   // green
const warning = Color(0xFFD97706);   // amber
const danger  = Color(0xFFDC2626);   // red

// Accent light tints (for backgrounds)
const accentLight  = Color(0xFFEFF6FF);
const accent2Light = Color(0xFFF5F3FF);
const successLight = Color(0xFFECFDF5);
const warningLight = Color(0xFFFFFBEB);
const dangerLight  = Color(0xFFFEF2F2);

// Text
const txt1 = Color(0xFF0F172A);   // primary — near-black
const txt2 = Color(0xFF475569);   // secondary — slate
const txt3 = Color(0xFF94A3B8);   // placeholder / disabled

// Border
const border1 = Color(0xFFE2E8F0);   // subtle border
const border2 = Color(0xFFBFDBFE);   // accent border (blue tint)

// ── Typography ────────────────────────────────────────────────────
// Font: Sarabun (Thai + Latin)
// Scale: 10 / 12 / 13 / 14 / 16 / 20 / 24 / 32 / 40
const labelXS = TextStyle(fontSize: 10, letterSpacing: 0.8, color: txt3);
const labelSM = TextStyle(fontSize: 12, letterSpacing: 0.3, color: txt2);
const body    = TextStyle(fontSize: 14, height: 1.6,       color: txt1);
const bodyMd  = TextStyle(fontSize: 16, height: 1.5,       color: txt1);
const titleSM = TextStyle(fontSize: 20, fontWeight: FontWeight.w700, color: txt1);
const titleMD = TextStyle(fontSize: 24, fontWeight: FontWeight.w700, color: txt1);
const titleLG = TextStyle(fontSize: 32, fontWeight: FontWeight.w800, color: txt1);
const display = TextStyle(fontSize: 40, fontWeight: FontWeight.w900, color: txt1, letterSpacing: -1.2);

// ── Spacing ───────────────────────────────────────────────────────
const sp4  =  4.0;
const sp6  =  6.0;
const sp8  =  8.0;
const sp10 = 10.0;
const sp12 = 12.0;
const sp16 = 16.0;
const sp20 = 20.0;
const sp24 = 24.0;
const sp32 = 32.0;
const sp48 = 48.0;

// ── Border Radius ─────────────────────────────────────────────────
final r6  = BorderRadius.circular(6);
final r8  = BorderRadius.circular(8);
final r12 = BorderRadius.circular(12);
final r14 = BorderRadius.circular(14);
final r16 = BorderRadius.circular(16);
final r20 = BorderRadius.circular(20);
final r24 = BorderRadius.circular(24);
final rFull = BorderRadius.circular(999);

// ── Elevation / Shadow ────────────────────────────────────────────
final shadowSM = BoxShadow(color: const Color(0x0A0F172A), blurRadius: 8,  offset: const Offset(0, 2));
final shadowMD = BoxShadow(color: const Color(0x120F172A), blurRadius: 20, offset: const Offset(0, 6));
final shadowLG = BoxShadow(color: const Color(0x1A0F172A), blurRadius: 40, offset: const Offset(0, 12));
final glowBlue = BoxShadow(color: const Color(0x1A2563EB), blurRadius: 20, offset: const Offset(0, 6));
