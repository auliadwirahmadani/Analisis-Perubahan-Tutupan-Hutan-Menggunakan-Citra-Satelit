<?php
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Top WebGIS Header Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/70 border border-slate-800 rounded-2xl p-4 sm:p-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    <?= htmlspecialchars($currentRegion['province']) ?>
                </span>
                <span class="text-xs text-slate-400">Koordinat: <?= $currentRegion['lat'] ?>, <?= $currentRegion['lng'] ?></span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-100 flex items-center gap-2">
                🗺️ <?= htmlspecialchars($currentRegion['name']) ?>
            </h1>
            <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">
                <?= htmlspecialchars($currentRegion['description']) ?>
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- AOI Drawing Tool Button -->
            <button id="btnDrawAoi" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold flex items-center shadow-lg shadow-emerald-600/20 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Gambar Area Minat (AOI)
            </button>

            <!-- Swipe Comparison Toggle -->
            <label class="flex items-center gap-2 bg-slate-800 hover:bg-slate-750 border border-slate-700 px-3 py-2 rounded-xl cursor-pointer text-xs text-slate-200 select-none">
                <input type="checkbox" id="toggleSwipeMode" class="w-4 h-4 text-emerald-600 bg-slate-900 border-slate-700 rounded focus:ring-emerald-500">
                <span class="font-medium">Mode Geser (Swipe Map)</span>
            </label>
        </div>
    </div>

    <!-- Swipe Control Slider Bar (hidden until toggle checked) -->
    <div id="swipeControlPanel" class="hidden bg-slate-900 border border-emerald-500/40 rounded-xl p-4 transition-all">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-xs">
                <span class="font-bold text-emerald-400">Layer Dasar:</span> Citra Satelit Esri (Kondisi Awal)
                <span class="text-slate-600">vs</span>
                <span class="font-bold text-blue-400">Layer Pembanding:</span> Overlay Citra Multi-Temporal
            </div>
            <div class="w-full sm:w-1/2 flex items-center gap-3">
                <span class="text-xs text-slate-400">0%</span>
                <input type="range" id="swipeSlider" min="0" max="100" value="65" class="swipe-range-slider">
                <span class="text-xs text-slate-400">100%</span>
            </div>
        </div>
    </div>

    <!-- Main Map Container with Relative Positioning for Overlays -->
    <div class="relative rounded-2xl overflow-hidden border border-slate-800 shadow-2xl">
        <div id="mapContainer"></div>

        <!-- Floating Map Legend -->
        <div class="absolute bottom-6 left-6 z-[1000] glass-panel rounded-xl p-3.5 text-xs text-slate-200 max-w-xs shadow-xl hidden sm:block">
            <h4 class="font-bold text-slate-100 text-xs mb-2 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 1.586l-4 4v10.828l4-4V1.586zM3.707 3.293A1 1 0 002 4v10a1 1 0 00.293.707L6 18.414V7.586L3.707 3.293zM14 7.586l3.707-3.707A1 1 0 0119 4v10a1 1 0 01-.293.707L15 18.414V7.586z" clip-rule="evenodd"/></path></svg>
                Legenda Klasifikasi Tutupan
            </h4>
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#065f46] inline-block border border-white/20"></span>
                    <span class="text-slate-300">Hutan Primer Rapat (NDVI &gt; 0.6)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#10b981] inline-block border border-white/20"></span>
                    <span class="text-slate-300">Hutan Sekunder / Kebun (0.4 - 0.6)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#eab308] inline-block border border-white/20"></span>
                    <span class="text-slate-300">Semak Belukar (0.2 - 0.4)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-[#ef4444] inline-block border border-white/20"></span>
                    <span class="text-slate-300">Lahan Terbuka / Tambang (&lt; 0.2)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500 animate-ping inline-block"></span>
                    <span class="text-red-400 font-semibold">Titik Hotspot Deforestasi</span>
                </div>
            </div>
        </div>

        <!-- Floating AOI Result Modal (Dynamic Pop-up upon drawing completion) -->
        <div id="aoiResultModal" class="hidden absolute top-6 right-6 z-[1000] glass-panel rounded-2xl p-5 text-xs text-slate-200 w-80 sm:w-96 shadow-2xl border border-emerald-500/40">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3 mb-3">
                <div class="flex items-center gap-2">
                    <span class="p-1 rounded bg-emerald-500/20 text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <h3 class="font-bold text-slate-100 text-sm">Hasil Analisis Area Minat (AOI)</h3>
                </div>
                <button id="aoiCloseBtn" class="text-slate-400 hover:text-white p-1 rounded hover:bg-slate-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-2.5">
                <div class="flex justify-between items-center bg-slate-800/60 p-2 rounded-lg">
                    <span class="text-slate-400">Sektor Wilayah:</span>
                    <span id="aoiRegionName" class="font-bold text-slate-200 truncate ml-2">-</span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="bg-slate-800/60 p-2.5 rounded-lg">
                        <span class="text-slate-400 block text-[11px]">Total Luas Poligon:</span>
                        <span id="aoiAreaHa" class="text-base font-bold text-slate-100">0 Ha</span>
                        <span id="aoiAreaKm2" class="text-[10px] text-slate-500 block">0 km²</span>
                    </div>
                    <div class="bg-slate-800/60 p-2.5 rounded-lg">
                        <span class="text-slate-400 block text-[11px]">Proporsi Hutan:</span>
                        <span id="aoiForestPct" class="text-base font-bold text-emerald-400">0%</span>
                        <span id="aoiForestHa" class="text-[10px] text-slate-500 block">0 Ha</span>
                    </div>
                </div>

                <div class="bg-slate-800/60 p-2.5 rounded-lg space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Rata-rata NDVI:</span>
                        <span id="aoiNdviVal" class="font-mono font-bold text-amber-400">0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Klasifikasi NDVI:</span>
                        <span id="aoiNdviClass" class="text-slate-200">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Estimasi Karbon:</span>
                        <span id="aoiCarbonTonnes" class="font-bold text-blue-400">0 Ton</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <span class="text-slate-400">Status Kerentanan:</span>
                    <span id="aoiRiskBadge" class="px-2.5 py-1 rounded-full text-xs font-bold border">-</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Region Info Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-slate-900/70 border border-slate-800 rounded-xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Koordinat Pusat</span>
                <span class="text-sm font-bold text-slate-200"><?= $currentRegion['lat'] ?>, <?= $currentRegion['lng'] ?></span>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Kasus Deforestasi Terverifikasi</span>
                <span class="text-sm font-bold text-red-400"><?= $alertSummary['verified'] ?? 0 ?> Kasus Aktif</span>
            </div>
        </div>

        <div class="bg-slate-900/70 border border-slate-800 rounded-xl p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
            </div>
            <div>
                <span class="text-xs text-slate-400 block">Satelit Resolusi Tinggi</span>
                <span class="text-sm font-bold text-blue-400">Sentinel-2 (10m) &amp; Landsat 9 (30m)</span>
            </div>
        </div>
    </div>

</div>

<!-- Inisialisasi WebGIS Leaflet -->
<script src="<?= \App\Config\App::baseUrl('assets/js/webgis.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const regionConfig = <?= json_encode($currentRegion) ?>;
        const allRegions = <?= json_encode($regions) ?>;
        initForestWebGis(regionConfig, allRegions);
    });
</script>

<?php
require dirname(__DIR__) . '/layouts/footer.php';
?>
