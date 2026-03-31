class AppConfig {
  static const String baseUrl = 'https://workcnt.nass.ac.th';
  static const String apiBaseUrl = '$baseUrl/api/mobile';

  // Timeouts
  static const Duration connectTimeout = Duration(seconds: 15);
  static const Duration receiveTimeout = Duration(seconds: 15);
}
