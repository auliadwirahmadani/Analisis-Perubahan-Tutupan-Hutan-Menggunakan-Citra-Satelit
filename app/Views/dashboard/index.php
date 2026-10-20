<?php
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Top Banner / Headline -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Pemantauan Citra Satelit Aktif</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-100 tracking-tight">
                Dashboard Analisis Tutupan Hutan
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Data Spatiotemporal Sentinel-2 MSI & Landsat 9 OLI Periode 2018 &ndash; 2026
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?= \App\Config\App::baseUrl('map?region=' . $currentRegionKey) ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/30 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                Buka Peta WebGIS & Swipe
            </a>
            <a href="<?= \App\Config\App::baseUrl('ndvi') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition-all">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Uji Spektral NDVI
            </a>
        </div>
    </div>

    <!-- 4 Key Performance Indicator (KPI) Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Total Tutupan Hutan -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm hover:border-emerald-500/40 transition-colors relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-medium">Total Tutupan Hutan</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-slate-100 tracking-tight">
                <?= number_format($kpi['total_forest_ha'], 0, ',', '.') ?> <span class="text-sm font-normal text-slate-400">Ha</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-3 border-t border-slate-800/80">
                <span class="text-slate-400">Proporsi Kanopi: <strong class="text-emerald-400"><?= $kpi['forest_percentage'] ?>%</strong></span>
                <span class="text-slate-500 font-mono"><?= number_format($kpi['total_forest_ha'] / 100, 1, ',', '.') ?> km²</span>
            </div>
        </div>

        <!-- Card 2: Laju Deforestasi Tahunan -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm hover:border-red-500/40 transition-colors relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-red-500/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-medium">Laju Deforestasi 2026</span>
                <span class="p-1.5 rounded-lg bg-red-500/10 text-red-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-red-400 tracking-tight">
                -<?= number_format($kpi['deforestation_rate_ha'], 0, ',', '.') ?> <span class="text-sm font-normal text-slate-400">Ha/thn</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-3 border-t border-slate-800/80">
                <span class="text-slate-400">Deforestasi Kumulatif:</span>
                <span class="text-red-400 font-medium">-<?= number_format($kpi['net_loss_ha'], 0, ',', '.') ?> Ha</span>
            </div>
        </div>

        <!-- Card 3: Reforestasi & Pemulihan -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm hover:border-blue-500/40 transition-colors relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-500/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-medium">Reforestasi & Restorasi</span>
                <span class="p-1.5 rounded-lg bg-blue-500/10 text-blue-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-blue-400 tracking-tight">
                +<?= number_format($kpi['reforestation_ha'], 0, ',', '.') ?> <span class="text-sm font-normal text-slate-400">Ha/thn</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-3 border-t border-slate-800/80">
                <span class="text-slate-400">Hutan Sekunder:</span>
                <span class="text-slate-200 font-medium"><?= number_format($kpi['secondary_forest_ha'], 0, ',', '.') ?> Ha</span>
            </div>
        </div>

        <!-- Card 4: Indeks NDVI & Alert Aktif -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm hover:border-amber-500/40 transition-colors relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-amber-500/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span class="font-medium">Rata-Rata Indeks NDVI</span>
                <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-400 tracking-tight">
                <?= $kpi['avg_ndvi'] ?> <span class="text-xs font-normal text-slate-400">(-1 s/d +1)</span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-3 border-t border-slate-800/80">
                <span class="text-slate-400">Titik Hotspot Deforestasi:</span>
                <a href="<?= \App\Config\App::baseUrl('alerts') ?>" class="text-red-400 font-bold hover:underline flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-ping inline-block"></span>
                    <?= $kpi['active_alerts_count'] ?> Titik
                </a>
            </div>
        </div>

    </div>

    <!-- Charts Row 1: Yearly Trend & LULC Composition -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Yearly Trend Area Chart (Span 2) -->
        <div class="lg:col-span-2 bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                        📈 Dinamika Tutupan Hutan & Deforestasi (2018 - 2026)
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pemantauan laju kehilangan hutan primer, sekunder, dan upaya reforestasi</p>
                </div>
                <span class="text-[11px] font-mono px-2 py-1 rounded bg-slate-800 text-slate-400 border border-slate-700">Satuan: Hektar</span>
            </div>
            <div id="yearlyTrendChart" class="w-full"></div>
        </div>

        <!-- LULC Donut Chart (Span 1) -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="mb-4">
                <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                    🍩 Komposisi Tutupan Lahan 2026
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Distribusi klasifikasi spektral citra satelit</p>
            </div>
            <div id="lulcDonutChart" class="w-full"></div>
        </div>

    </div>

    <!-- Charts Row 2: Region Comparison & Quick Table -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Region Comparison Bar Chart -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-100">
                        📊 Perbandingan Laju Deforestasi Antar Wilayah
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Laju deforestasi tahunan per bentang wilayah hutan</p>
                </div>
            </div>
            <div id="regionComparisonChart" class="w-full"></div>
        </div>

        <!-- Recent Deforestation Hotspots Table -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                            🚨 Peringatan Deforestasi Terkini
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Indikasi kehilangan kanopi dari algoritma deteksi anomali satelit</p>
                    </div>
                    <a href="<?= \App\Config\App::baseUrl('alerts') ?>" class="text-xs text-emerald-400 hover:text-emerald-300 font-medium">
                        Lihat Semua &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-800/60 text-slate-400 uppercase text-[10px]">
                            <tr>
                                <th class="px-3 py-2 rounded-l-lg">Kode Alert</th>
                                <th class="px-3 py-2">Jenis Ancaman</th>
                                <th class="px-3 py-2">Luas Hilang</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 rounded-r-lg text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            <?php foreach ($recentAlerts as $alert): ?>
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="px-3 py-2.5 font-mono text-slate-300 font-medium">
                                        <?= htmlspecialchars($alert['alert_code']) ?>
                                    </td>
                                    <td class="px-3 py-2.5 text-slate-300">
                                        <?= htmlspecialchars($alert['threat_type']) ?>
                                    </td>
                                    <td class="px-3 py-2.5 text-red-400 font-bold">
                                        <?= number_format($alert['area_loss_ha'], 1) ?> Ha
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <?php if ($alert['verification_status'] === 'Terverifikasi Valid'): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Terverifikasi</span>
                                        <?php elseif ($alert['verification_status'] === 'Ditindaklanjuti'): ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-blue-500/20 text-blue-400 border border-blue-500/30">Ditindak</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-amber-500/20 text-amber-400 border border-amber-500/30">Penyelidikan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-3 py-2.5 text-right">
                                        <a href="<?= \App\Config\App::baseUrl('map?region=' . $alert['region_id']) ?>" class="text-emerald-400 hover:text-emerald-300">
                                            Peta &rarr;
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer summary inside card -->
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                <span>Total kasus terverifikasi: <strong class="text-slate-200"><?= $alertSummary['verified'] ?? 0 ?></strong></span>
                <span class="text-slate-500">Total area terancam: <strong class="text-red-400"><?= number_format($alertSummary['total_loss_ha'] ?? 0, 1) ?> Ha</strong></span>
            </div>
        </div>

    </div>

    <!-- Row 3: Satellite Observations Table -->
    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-100 flex items-center gap-2">
                    🛰️ Log Akuisisi Citra Satelit Penginderaan Jauh
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Metadata citra optis dan multispektral yang telah melalui koreksi atmosferik (Level-2A BOA)</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/60 text-slate-400 uppercase text-[10px]">
                    <tr>
                        <th class="px-4 py-2.5 rounded-l-lg">Satelit / Misi</th>
                        <th class="px-4 py-2.5">Sensor</th>
                        <th class="px-4 py-2.5">Tanggal Akuisisi</th>
                        <th class="px-4 py-2.5">Wilayah Pengamatan</th>
                        <th class="px-4 py-2.5">Resolusi</th>
                        <th class="px-4 py-2.5">Tutupan Awan</th>
                        <th class="px-4 py-2.5">Rata-Rata NDVI</th>
                        <th class="px-4 py-2.5 rounded-r-lg">Status Pipeline</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php foreach ($observations as $obs): ?>
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-3 font-semibold text-emerald-400 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <?= htmlspecialchars($obs['satellite_name']) ?>
                            </td>
                            <td class="px-4 py-3 text-slate-300 font-mono text-[11px]">
                                <?= htmlspecialchars($obs['sensor']) ?>
                            </td>
                            <td class="px-4 py-3 text-slate-300">
                                <?= htmlspecialchars($obs['acquisition_date']) ?>
                            </td>
                            <td class="px-4 py-3 text-slate-200">
                                <?= htmlspecialchars($obs['region_name']) ?>
                            </td>
                            <td class="px-4 py-3 text-slate-300 font-mono">
                                <?= $obs['resolution_meters'] ?> meter
                            </td>
                            <td class="px-4 py-3 text-slate-300">
                                <span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 border border-slate-700">
                                    <?= $obs['cloud_cover_percent'] ?>%
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold text-amber-400">
                                <?= $obs['avg_ndvi'] ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    <?= htmlspecialchars($obs['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Inisialisasi Data ApexCharts dari Backend -->
<script src="<?= \App\Config\App::baseUrl('assets/js/charts.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const trendData = <?= json_encode($yearlyTrend) ?>;
        const lulcData = <?= json_encode($lulcComposition) ?>;
        const comparisonData = <?= json_encode($regionComparison) ?>;

        renderDashboardCharts(trendData, lulcData, comparisonData);
    });
</script>

<?php
require dirname(__DIR__) . '/layouts/footer.php';
?>
