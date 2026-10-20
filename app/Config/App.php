<?php

namespace App\Config;

class App
{
    public const APP_NAME = 'ForestWatch WebGIS';
    public const APP_SUBTITLE = 'Analisis Perubahan Tutupan Hutan Menggunakan Citra Satelit';
    public const APP_VERSION = '2.4.0';

    // DB Driver: 'sqlite' or 'mysql'
    public const DB_DRIVER = 'sqlite';

    // SQLite config
    public static function getSqlitePath(): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'forest_watch.sqlite';
    }

    // MySQL config (optional alternative for Laragon)
    public const DB_HOST = '127.0.0.1';
    public const DB_PORT = '3306';
    public const DB_NAME = 'forest_watch';
    public const DB_USER = 'root';
    public const DB_PASS = '';

    /**
     * Get dynamic base URL for Laragon virtual host or localhost subfolder
     */
    public static function baseUrl(string $path = ''): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = str_replace('\\', '/', dirname($script));
        if ($dir === '/' || $dir === '.') {
            $base = $protocol . $host;
        } else {
            $base = $protocol . $host . rtrim($dir, '/');
        }

        $path = ltrim($path, '/');
        return $path === '' ? $base : $base . '/' . $path;
    }
}
