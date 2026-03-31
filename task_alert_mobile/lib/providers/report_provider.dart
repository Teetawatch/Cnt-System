import 'package:flutter/material.dart';
import '../data/api_models.dart';
import '../services/api_service.dart';
import 'home_provider.dart';

class ReportProvider extends ChangeNotifier {
  final ApiService _api = ApiService();

  ScreenState _state = ScreenState.loading;
  StatsResponse? _stats;
  String? _errorMessage;
  int _selectedMonth = DateTime.now().month;
  int _selectedYear = DateTime.now().year;

  ScreenState get state => _state;
  StatsResponse? get stats => _stats;
  String? get errorMessage => _errorMessage;
  int get selectedMonth => _selectedMonth;
  int get selectedYear => _selectedYear;

  ReportProvider() {
    loadReport();
  }

  void selectMonth(int month, int year) {
    _selectedMonth = month;
    _selectedYear = year;
    loadReport();
  }

  Future<void> loadReport() async {
    _state = ScreenState.loading;
    notifyListeners();

    try {
      _stats = await _api.getStats(
        month: _selectedMonth,
        year: _selectedYear,
      );
      _state = ScreenState.success;
    } catch (e) {
      _state = ScreenState.error;
      _errorMessage = e.toString();
    }
    notifyListeners();
  }

  Future<void> refresh() async {
    await loadReport();
  }
}
