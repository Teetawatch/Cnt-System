@echo off
echo Creating keystore for Play Store...
echo.
echo Please enter: cnt2026 for both passwords
echo.
keytool -genkey -v -keystore android\app\release-key.jks -keyalg RSA -keysize 2048 -validity 10000 -alias release
pause
