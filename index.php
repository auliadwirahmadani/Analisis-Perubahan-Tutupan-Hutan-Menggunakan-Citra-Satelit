<?php

/**
 * ForestWatch WebGIS Entry Point & Router
 * Analisis Perubahan Tutupan Hutan Menggunakan Citra Satelit
 */

declare(strict_types=1);

// PSR-4 Style Autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Resolve URI Path
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

// Remove query string
$uriPath = parse_url($requestUri, PHP_URL_PATH) ?? '/';

// Normalize relative folder path if hosted inside subfolder (e.g. /analisis perubahan tutupan hutan...)
$scriptDir = str_replace('\\', '/', dirname($scriptName));
if ($scriptDir !== '/' && $scriptDir !== '.') {
    if (strpos($uriPath, $scriptDir) === 0) {
        $uriPath = substr($uriPath, strlen($scriptDir));
    }
}

$uriPath = '/' . trim($uriPath, '/');

// Simple Router Dispatcher
switch ($uriPath) {
    case '/':
    case '/index.php':
    case '/dashboard':
        (new \App\Controllers\DashboardController())->index();
        break;

    case '/map':
        (new \App\Controllers\MapController())->index();
        break;

    case '/ndvi':
        (new \App\Controllers\NdviController())->index();
        break;

    case '/alerts':
        (new \App\Controllers\AlertController())->index();
        break;

    case '/alerts/store':
        (new \App\Controllers\AlertController())->store();
        break;

    case '/alerts/update':
        (new \App\Controllers\AlertController())->update();
        break;

    case '/alerts/delete':
        (new \App\Controllers\AlertController())->delete();
        break;

    case '/export/csv':
        (new \App\Controllers\ExportController())->csv();
        break;

    case '/export/geojson':
        (new \App\Controllers\ExportController())->geojson();
        break;

    case '/reports/print':
        (new \App\Controllers\ExportController())->printReport();
        break;

    // REST API Endpoints
    case '/api/stats':
        (new \App\Controllers\ApiController())->stats();
        break;

    case '/api/trend':
        (new \App\Controllers\ApiController())->trend();
        break;

    case '/api/lulc':
        (new \App\Controllers\ApiController())->lulc();
        break;

    case '/api/comparison':
        (new \App\Controllers\ApiController())->comparison();
        break;

    case '/api/geojson/alerts':
        (new \App\Controllers\ApiController())->geojsonAlerts();
        break;

    case '/api/geojson/regions':
        (new \App\Controllers\ApiController())->geojsonRegions();
        break;

    case '/api/aoi-analyze':
        (new \App\Controllers\ApiController())->aoiAnalyze();
        break;

    case '/api/reset-demo':
        (new \App\Controllers\ApiController())->resetDemo();
        break;

    default:
        http_response_code(404);
        echo "<!DOCTYPE html><html><head><title>404 Not Found</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-slate-950 text-white min-h-screen flex items-center justify-center p-4'><div class='text-center'><h1 class='text-6xl font-black text-emerald-500'>404</h1><p class='text-lg mt-2 text-slate-300'>Halaman tidak ditemukan.</p><a href='" . \App\Config\App::baseUrl() . "' class='mt-4 inline-block px-4 py-2 bg-emerald-600 rounded-lg text-sm font-semibold'>Kembali ke Dashboard</a></div></body></html>";
        break;
}
