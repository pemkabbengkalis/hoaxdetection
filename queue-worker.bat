@echo off
title Laravel Queue Worker - WhatsApp
color 0A

echo ============================================
echo   Laravel Queue Worker - Auto Restart
echo   WhatsApp Message Processing
echo ============================================
echo.

cd /d "%~dp0"

:loop
echo [%date% %time%] Starting queue worker...
php artisan queue:work database --queue=default --tries=5 --backoff=60,120,300,600 --timeout=90 --sleep=3 --max-jobs=100 --max-time=3600

echo.
echo [%date% %time%] Queue worker stopped. Restarting in 5 seconds...
echo.
timeout /t 5 /nobreak >nul
goto loop
