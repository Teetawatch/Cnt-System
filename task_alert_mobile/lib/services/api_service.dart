import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import '../core/config.dart';
import '../data/api_models.dart';

class ApiException implements Exception {
  final String message;
  final int? statusCode;

  ApiException(this.message, {this.statusCode});

  @override
  String toString() => message;
}

class ApiService {
  static final ApiService _instance = ApiService._internal();
  factory ApiService() => _instance;
  ApiService._internal();

  final http.Client _client = http.Client();

  Future<Map<String, dynamic>> _get(String endpoint, {Map<String, String>? params}) async {
    try {
      final uri = Uri.parse('${AppConfig.apiBaseUrl}$endpoint')
          .replace(queryParameters: params);
      final response = await _client
          .get(uri, headers: {'Accept': 'application/json'})
          .timeout(AppConfig.connectTimeout);

      if (response.statusCode == 200) {
        return json.decode(response.body) as Map<String, dynamic>;
      } else {
        throw ApiException(
          'เกิดข้อผิดพลาดจากเซิร์ฟเวอร์ (${response.statusCode})',
          statusCode: response.statusCode,
        );
      }
    } on SocketException {
      throw ApiException(
          'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้\nกรุณาตรวจสอบการเชื่อมต่ออินเทอร์เน็ต');
    } on http.ClientException {
      throw ApiException('การเชื่อมต่อล้มเหลว กรุณาลองใหม่อีกครั้ง');
    } catch (e) {
      if (e is ApiException) rethrow;
      throw ApiException('เกิดข้อผิดพลาด: $e');
    }
  }

  /// Get dashboard: staff list + events for a given date
  Future<DashboardResponse> getDashboard({DateTime? date}) async {
    final params = <String, String>{};
    if (date != null) {
      params['date'] =
          '${date.year}-${date.month.toString().padLeft(2, '0')}-${date.day.toString().padLeft(2, '0')}';
    }
    final raw = await _get('/dashboard', params: params);
    return DashboardResponse.fromJson(raw);
  }

  /// Get list of all active staff
  Future<List<StaffModel>> getStaffList() async {
    final raw = await _get('/staff');
    return (raw['staff'] as List<dynamic>)
        .map((s) => StaffModel.fromJson(s as Map<String, dynamic>))
        .toList();
  }

  /// Get events for a specific staff member (monthly)
  Future<List<EventModel>> getStaffEvents(int staffId,
      {int? month, int? year}) async {
    final params = <String, String>{};
    if (month != null) params['month'] = month.toString();
    if (year != null) params['year'] = year.toString();
    final raw = await _get('/staff/$staffId/events', params: params);
    return (raw['events'] as List<dynamic>)
        .map((e) => EventModel.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  /// Get monthly statistics
  Future<StatsResponse> getStats({int? month, int? year}) async {
    final params = <String, String>{};
    if (month != null) params['month'] = month.toString();
    if (year != null) params['year'] = year.toString();
    final raw = await _get('/stats', params: params);
    return StatsResponse.fromJson(raw);
  }

  /// Search events across all staff
  Future<List<EventModel>> searchEvents({String? query, int? staffId}) async {
    final params = <String, String>{};
    if (query != null && query.isNotEmpty) params['q'] = query;
    if (staffId != null) params['staff_id'] = staffId.toString();
    final raw = await _get('/search', params: params);
    return (raw['events'] as List<dynamic>)
        .map((e) => EventModel.fromJson(e as Map<String, dynamic>))
        .toList();
  }
}
