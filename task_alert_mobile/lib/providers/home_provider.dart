import 'package:flutter/material.dart';
import '../data/api_models.dart';
import '../services/api_service.dart';

enum ScreenState { loading, success, empty, error }

class HomeProvider extends ChangeNotifier {
  final ApiService _api = ApiService();

  ScreenState _state = ScreenState.loading;
  DashboardResponse? _dashboard;
  String? _errorMessage;
  DateTime _selectedDate = DateTime.now();

  ScreenState get state => _state;
  DashboardResponse? get dashboard => _dashboard;
  String? get errorMessage => _errorMessage;
  DateTime get selectedDate => _selectedDate;

  List<StaffModel> get staffWithEvents =>
      _dashboard?.staff.where((s) => s.events.isNotEmpty).toList() ?? [];

  int get totalEvents => _dashboard?.summary.totalEvents ?? 0;
  int get confirmedEvents => _dashboard?.summary.confirmed ?? 0;

  bool get isToday {
    final now = DateTime.now();
    return _selectedDate.year == now.year &&
        _selectedDate.month == now.month &&
        _selectedDate.day == now.day;
  }

  HomeProvider() {
    loadDashboard();
  }

  void selectDate(DateTime date) {
    _selectedDate = date;
    loadDashboard();
  }

  Future<void> loadDashboard() async {
    _state = ScreenState.loading;
    _errorMessage = null;
    notifyListeners();

    try {
      final result = await _api.getDashboard(date: _selectedDate);
      _dashboard = result;
      _state = (result.staff.where((s) => s.events.isNotEmpty).isEmpty)
          ? ScreenState.empty
          : ScreenState.success;
    } catch (e) {
      _state = ScreenState.error;
      _errorMessage = e.toString();
    }
    notifyListeners();
  }

  Future<void> refresh() async {
    await loadDashboard();
  }
}
