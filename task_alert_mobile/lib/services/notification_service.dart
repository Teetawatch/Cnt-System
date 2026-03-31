import 'dart:convert';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:http/http.dart' as http;
import '../core/config.dart';

// ── Background message handler (top-level required by FCM) ──────────
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  await NotificationService._showLocalNotification(message);
}

class NotificationService {
  NotificationService._();
  static final NotificationService instance = NotificationService._();

  final FirebaseMessaging _fcm = FirebaseMessaging.instance;
  final FlutterLocalNotificationsPlugin _localNotifications =
      FlutterLocalNotificationsPlugin();

  // Android notification channel
  static const AndroidNotificationChannel _channel = AndroidNotificationChannel(
    'cnt_events_channel',
    'กิจกรรมใหม่',
    description: 'แจ้งเตือนเมื่อมีกิจกรรมใหม่ใน สพป.ชัยนาท',
    importance: Importance.max,
    playSound: true,
    enableVibration: true,
    showBadge: true,
  );

  static final FlutterLocalNotificationsPlugin _staticLocalNotifications =
      FlutterLocalNotificationsPlugin();

  // Callback เมื่อ user กดที่ notification
  static void Function(Map<String, dynamic> data)? onNotificationTap;

  // ── Initialise ────────────────────────────────────────────────────
  Future<void> init() async {
    // 1. Request permission
    final settings = await _fcm.requestPermission(
      alert: true,
      badge: true,
      sound: true,
      provisional: false,
    );
    debugPrint('[FCM] Permission: ${settings.authorizationStatus}');

    // 2. Setup local notifications
    await _initLocalNotifications();

    // 3. Create Android channel
    await _localNotifications
        .resolvePlatformSpecificImplementation<
            AndroidFlutterLocalNotificationsPlugin>()
        ?.createNotificationChannel(_channel);

    // 4. Register background handler
    FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);

    // 5. Handle foreground messages
    FirebaseMessaging.onMessage.listen(_handleForegroundMessage);

    // 6. Handle notification tap when app in background/terminated
    FirebaseMessaging.onMessageOpenedApp.listen(_handleNotificationTap);

    // 7. Check if app was opened from terminated state via notification
    final initialMessage = await _fcm.getInitialMessage();
    if (initialMessage != null) {
      _handleNotificationTap(initialMessage);
    }

    // 8. Get + register token
    await _registerToken();

    // 9. Listen for token refresh
    _fcm.onTokenRefresh.listen(_sendTokenToServer);
  }

  // ── Local notifications setup ─────────────────────────────────────
  Future<void> _initLocalNotifications() async {
    const androidSettings =
        AndroidInitializationSettings('@mipmap/launcher_icon');
    const iosSettings = DarwinInitializationSettings(
      requestAlertPermission: false,
      requestBadgePermission: false,
      requestSoundPermission: false,
    );

    await _localNotifications.initialize(
      const InitializationSettings(
        android: androidSettings,
        iOS: iosSettings,
      ),
      onDidReceiveNotificationResponse: (NotificationResponse response) {
        if (response.payload != null) {
          try {
            final data = jsonDecode(response.payload!) as Map<String, dynamic>;
            onNotificationTap?.call(data);
          } catch (_) {}
        }
      },
    );

    // Same for static instance
    await _staticLocalNotifications.initialize(
      const InitializationSettings(
        android: androidSettings,
        iOS: iosSettings,
      ),
    );
  }

  // ── Show local notification ───────────────────────────────────────
  static Future<void> _showLocalNotification(RemoteMessage message) async {
    final notification = message.notification;
    if (notification == null) return;

    final androidDetails = AndroidNotificationDetails(
      _channel.id,
      _channel.name,
      channelDescription: _channel.description,
      importance: Importance.max,
      priority: Priority.high,
      styleInformation: BigTextStyleInformation(
        notification.body ?? '',
        contentTitle: notification.title,
        summaryText: 'สพป.ชัยนาท',
      ),
      icon: '@mipmap/launcher_icon',
      color: const Color(0xFF2563EB),
      largeIcon: const DrawableResourceAndroidBitmap('@mipmap/launcher_icon'),
      showWhen: true,
      when: DateTime.now().millisecondsSinceEpoch,
      ticker: notification.title,
    );

    const iosDetails = DarwinNotificationDetails(
      presentAlert: true,
      presentBadge: true,
      presentSound: true,
    );

    await _staticLocalNotifications.show(
      message.hashCode,
      notification.title,
      notification.body,
      NotificationDetails(android: androidDetails, iOS: iosDetails),
      payload: jsonEncode(message.data),
    );
  }

  // ── Handle foreground message ─────────────────────────────────────
  Future<void> _handleForegroundMessage(RemoteMessage message) async {
    debugPrint('[FCM] Foreground message: ${message.notification?.title}');
    await _showLocalNotification(message);
  }

  // ── Handle notification tap ───────────────────────────────────────
  void _handleNotificationTap(RemoteMessage message) {
    debugPrint('[FCM] Notification tapped: ${message.data}');
    onNotificationTap?.call(message.data);
  }

  // ── Token management ──────────────────────────────────────────────
  Future<void> _registerToken() async {
    final token = await _fcm.getToken();
    if (token != null) {
      debugPrint('[FCM] Token: $token');
      await _sendTokenToServer(token);
    }
  }

  Future<void> _sendTokenToServer(String token) async {
    try {
      await http.post(
        Uri.parse('${AppConfig.baseUrl}/api/fcm/token'),
        headers: {'Content-Type': 'application/json'},
        body: jsonEncode({'token': token, 'platform': 'android'}),
      );
      debugPrint('[FCM] Token sent to server');
    } catch (e) {
      debugPrint('[FCM] Failed to send token: $e');
    }
  }

  // ── Get current token (for debugging) ────────────────────────────
  Future<String?> getToken() async {
    return await _fcm.getToken();
  }
}
