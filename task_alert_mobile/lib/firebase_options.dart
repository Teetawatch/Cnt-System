// Generated from google-services.json
// Project: workcnt-68512

import 'package:firebase_core/firebase_core.dart' show FirebaseOptions;
import 'package:flutter/foundation.dart'
    show defaultTargetPlatform, kIsWeb, TargetPlatform;

class DefaultFirebaseOptions {
  static FirebaseOptions get currentPlatform {
    if (kIsWeb) return web;
    switch (defaultTargetPlatform) {
      case TargetPlatform.android:
        return android;
      case TargetPlatform.iOS:
        return ios;
      default:
        return android;
    }
  }

  static const FirebaseOptions android = FirebaseOptions(
    apiKey: 'AIzaSyAOE0gT5VZIixBDKO2HktLOCUf-KZlxQjY',
    appId: '1:462435775267:android:2bad1f610db3671e77d296',
    messagingSenderId: '462435775267',
    projectId: 'workcnt-68512',
    storageBucket: 'workcnt-68512.firebasestorage.app',
  );

  static const FirebaseOptions ios = FirebaseOptions(
    apiKey: 'AIzaSyAOE0gT5VZIixBDKO2HktLOCUf-KZlxQjY',
    appId: '1:462435775267:android:2bad1f610db3671e77d296',
    messagingSenderId: '462435775267',
    projectId: 'workcnt-68512',
    storageBucket: 'workcnt-68512.firebasestorage.app',
    iosBundleId: 'com.example.taskAlertMobile',
  );

  static const FirebaseOptions web = FirebaseOptions(
    apiKey: 'AIzaSyAOE0gT5VZIixBDKO2HktLOCUf-KZlxQjY',
    appId: '1:462435775267:android:2bad1f610db3671e77d296',
    messagingSenderId: '462435775267',
    projectId: 'workcnt-68512',
    storageBucket: 'workcnt-68512.firebasestorage.app',
  );
}
