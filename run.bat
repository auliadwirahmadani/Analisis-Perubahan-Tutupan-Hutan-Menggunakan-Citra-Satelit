@echo off
title ForestWatch WebGIS - Server
echo ========================================================
echo   ForestWatch WebGIS - Analisis Tutupan Hutan Satelit
echo ========================================================
echo Membuka server di http://localhost:8000 ...
echo Tekan CTRL+C untuk menghentikan server.
echo.

start http://localhost:8000
php -S localhost:8000 index.php
pause
