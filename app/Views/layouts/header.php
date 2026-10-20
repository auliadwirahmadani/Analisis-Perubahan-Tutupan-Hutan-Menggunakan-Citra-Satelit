<?php
use App\Config\App;
use App\Helpers\GeoHelper;

$allRegions = GeoHelper::getRegions();
$currentRegionKey = $_GET['region'] ?? 'all';
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? App::APP_NAME) ?> - <?= App::APP_NAME ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        forest: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace']
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= App::baseUrl('assets/css/custom.css') ?>">

    <script>
        window.APP_BASE_URL = "<?= rtrim(App::baseUrl(), '/') ?>";
    </script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col font-sans transition-colors duration-200">

    <!-- Top Navigation Bar -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <a href="<?= App::baseUrl() ?>" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-600/25 group-hover:scale-105 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-base sm:text-lg text-slate-100 group-hover:text-emerald-400 transition-colors tracking-tight">ForestWatch</span>
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">WebGIS</span>
                            </div>
                            <p class="text-[11px] text-slate-400 hidden sm:block">Analisis Perubahan Tutupan Hutan Satelit</p>
                        </div>
                    </a>
                </div>

                <!-- Main Menu Links -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="<?= App::baseUrl() ?>" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all <?= ($activeNav ?? '') === 'dashboard' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' ?>">
                        📊 Dashboard
                    </a>
                    <a href="<?= App::baseUrl('map') ?>" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all <?= ($activeNav ?? '') === 'map' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' ?>">
                        🗺️ WebGIS & Swipe
                    </a>
                    <a href="<?= App::baseUrl('ndvi') ?>" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all <?= ($activeNav ?? '') === 'ndvi' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' ?>">
                        🛰️ Spektral NDVI
                    </a>
                    <a href="<?= App::baseUrl('alerts') ?>" class="px-3 py-1.5 rounded-lg text-xs font-medium transition-all <?= ($activeNav ?? '') === 'alerts' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800' ?>">
                        🚨 Peringatan Deforestasi
                    </a>
                </nav>

                <!-- Right Controls: Region Selector, Export, Theme, Reset -->
                <div class="flex items-center gap-2">
                    
                    <!-- Region Filter -->
                    <div class="relative">
                        <select id="globalRegionSelect" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-1 focus:ring-emerald-500 max-w-[140px] sm:max-w-[200px] truncate">
                            <option value="all" <?= $currentRegionKey === 'all' ? 'selected' : '' ?>>🌏 Seluruh Wilayah</option>
                            <?php foreach ($allRegions as $key => $r): ?>
                                <option value="<?= $key ?>" <?= $currentRegionKey === $key ? 'selected' : '' ?>>
                                    📍 <?= htmlspecialchars($r['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Export Report Dropdown -->
                    <div class="relative group">
                        <button class="bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs rounded-lg px-2.5 py-1.5 flex items-center gap-1 transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span class="hidden sm:inline">Ekspor</span>
                        </button>
                        <div class="absolute right-0 mt-1 w-44 bg-slate-900 border border-slate-800 rounded-lg shadow-xl py-1 text-xs hidden group-hover:block z-50">
                            <a href="<?= App::baseUrl('reports/print?region=' . $currentRegionKey) ?>" target="_blank" class="block px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white flex items-center gap-2">
                                <span>📄 Cetak Laporan PDF</span>
                            </a>
                            <a href="<?= App::baseUrl('export/csv?type=stats&region=' . $currentRegionKey) ?>" class="block px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white flex items-center gap-2">
                                <span>📊 Ekspor CSV Tutupan</span>
                            </a>
                            <a href="<?= App::baseUrl('export/csv?type=alerts&region=' . $currentRegionKey) ?>" class="block px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white flex items-center gap-2">
                                <span>🚨 Ekspor CSV Hotspots</span>
                            </a>
                            <a href="<?= App::baseUrl('export/geojson?region=' . $currentRegionKey) ?>" class="block px-3 py-2 text-slate-300 hover:bg-slate-800 hover:text-white flex items-center gap-2">
                                <span>🌐 Ekspor GeoJSON</span>
                            </a>
                        </div>
                    </div>

                    <!-- Reset Demo Button -->
                    <button id="btnResetDemo" title="Reset ke Data Awal Demo" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-slate-800 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>

                    <!-- Theme Toggle -->
                    <button id="themeToggleBtn" title="Ganti Tema Gelap/Terang" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Nav bar for small screens -->
        <div class="md:hidden border-t border-slate-800/80 px-4 py-2 flex items-center justify-around text-xs bg-slate-900/95">
            <a href="<?= App::baseUrl() ?>" class="py-1 px-2 rounded <?= ($activeNav ?? '') === 'dashboard' ? 'text-emerald-400 font-bold' : 'text-slate-400' ?>">Dashboard</a>
            <a href="<?= App::baseUrl('map') ?>" class="py-1 px-2 rounded <?= ($activeNav ?? '') === 'map' ? 'text-emerald-400 font-bold' : 'text-slate-400' ?>">WebGIS</a>
            <a href="<?= App::baseUrl('ndvi') ?>" class="py-1 px-2 rounded <?= ($activeNav ?? '') === 'ndvi' ? 'text-emerald-400 font-bold' : 'text-slate-400' ?>">NDVI</a>
            <a href="<?= App::baseUrl('alerts') ?>" class="py-1 px-2 rounded <?= ($activeNav ?? '') === 'alerts' ? 'text-emerald-400 font-bold' : 'text-slate-400' ?>">Alerts</a>
        </div>
    </header>

    <main class="flex-1">
