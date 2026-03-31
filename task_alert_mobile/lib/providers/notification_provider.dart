import 'package:flutter/material.dart';
import '../data/api_models.dart';
import '../services/api_service.dart';
import 'home_provider.dart';

class StaffListProvider extends ChangeNotifier {
  final ApiService _api = ApiService();

  ScreenState _state = ScreenState.loading;
  List<StaffModel> _staffList = [];
  String? _errorMessage;

  // Selected staff detail
  StaffModel? _selectedStaff;
  ScreenState _detailState = ScreenState.loading;
  List<EventModel> _selectedStaffEvents = [];
  String? _detailError;
  int _detailMonth = DateTime.now().month;
  int _detailYear = DateTime.now().year;

  ScreenState get state => _state;
  List<StaffModel> get staffList => _staffList;
  String? get errorMessage => _errorMessage;

  StaffModel? get selectedStaff => _selectedStaff;
  ScreenState get detailState => _detailState;
  List<EventModel> get selectedStaffEvents => _selectedStaffEvents;
  String? get detailError => _detailError;
  int get detailMonth => _detailMonth;
  int get detailYear => _detailYear;

  StaffListProvider() {
    loadStaffList();
  }

  Future<void> loadStaffList() async {
    _state = ScreenState.loading;
    notifyListeners();
    try {
      _staffList = await _api.getStaffList();
      _state = _staffList.isEmpty ? ScreenState.empty : ScreenState.success;
    } catch (e) {
      _state = ScreenState.error;
      _errorMessage = e.toString();
    }
    notifyListeners();
  }

  Future<void> selectStaff(StaffModel staff) async {
    _selectedStaff = staff;
    _detailMonth = DateTime.now().month;
    _detailYear = DateTime.now().year;
    await _loadStaffEvents();
  }

  Future<void> changeDetailMonth(int month, int year) async {
    _detailMonth = month;
    _detailYear = year;
    await _loadStaffEvents();
  }

  Future<void> _loadStaffEvents() async {
    if (_selectedStaff == null) return;
    _detailState = ScreenState.loading;
    _detailError = null;
    notifyListeners();
    try {
      _selectedStaffEvents = await _api.getStaffEvents(
        _selectedStaff!.id,
        month: _detailMonth,
        year: _detailYear,
      );
      _detailState = _selectedStaffEvents.isEmpty ? ScreenState.empty : ScreenState.success;
    } catch (e) {
      _detailState = ScreenState.error;
      _detailError = e.toString();
    }
    notifyListeners();
  }

  Future<void> refresh() async {
    await loadStaffList();
  }

  Future<void> refreshDetail() async {
    await _loadStaffEvents();
  }
}
