import 'package:flutter/material.dart';
import '../core/config.dart';
import 'home_provider.dart';

class SettingsProvider extends ChangeNotifier {
  final ScreenState _state = ScreenState.success;
  String _serverUrl = AppConfig.baseUrl;

  ScreenState get state => _state;
  String get serverUrl => _serverUrl;

  SettingsProvider();

  void updateServerUrl(String url) {
    _serverUrl = url;
    notifyListeners();
  }
}
