<?php
use App\Helpers\GeoHelper;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analisis Perubahan Tutupan Hutan - <?= htmlspecialchars($regionName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; font-size: 11pt; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 p-4 sm:p-8 font-sans">

    <!-- Action Toolbar (Hidden during print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-xl shadow-md border border-slate-200">
        <div class="text-xs text-slate-600">
            Tekan tombol cetak di samping untuk menyimpan sebagai <strong>PDF</strong> atau mencetak ke kertas.
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.history.back()" class="px-3 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-xs font-semibold">
                &larr; Kembali
            </button>
            <button onclick="window.print()" class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Official Report Document Paper -->
    <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 rounded-xl shadow-lg border border-slate-200 print:shadow-none print:border-none print:p-0">
        
        <!-- Kop Dokumen Resmi -->
        <div class="border-b-4 border-double border-slate-800 pb-4 mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl bg-emerald-800 text-white flex items-center justify-center font-bold text-2xl">
                    FW
                </div>
                <div>
                    <h2 class="text-sm font-bold tracking-wider text-slate-600 uppercase">Sistem Penginderaan Jauh &amp; Konservasi Geospasial</h2>
                    <h1 class="text-lg font-black text-slate-900 uppercase tracking-tight">ForestWatch WebGIS &mdash; Analisis Tutupan Hutan</h1>
                    <p class="text-xs text-slate-500">Pusat Data Citra Satelit Pengamatan Bumi (Sentinel-2, Landsat 9, MODIS, PlanetScope)</p>
                </div>
            </div>
            <div class="text-right text-xs text-slate-500">
                <p>Dokumen: <strong class="font-mono text-slate-800">FW-RPT-<?= date('Ymd-His') ?></strong></p>
                <p>Tanggal: <?= date('d F Y') ?></p>
                <p>Klasifikasi: Laporan Resmi</p>
            </div>
        </div>

        <!-- Judul Laporan -->
        <div class="text-center my-6">
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight uppercase">
                Laporan Hasil Analisis Perubahan Tutupan Hutan (Deforestasi)
            </h2>
            <p class="text-sm text-emerald-800 font-semibold mt-1">
                Wilayah Pengamatan: <?= htmlspecialchars($regionName) ?>
            </p>
        </div>

        <!-- Ringkasan Eksekutif -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 text-xs text-slate-700 leading-relaxed">
            <h3 class="font-bold text-slate-900 uppercase tracking-wide mb-1.5 text-[11px]">Ringkasan Eksekutif:</h3>
            <p>
                Berdasarkan analisis spatiotemporal citra satelit multispektral (Sentinel-2 MSI Level-2A dan Landsat 9 OLI) periode 2018 hingga 2026, wilayah <strong><?= htmlspecialchars($regionName) ?></strong> tercatat memiliki total tutupan hutan seluas <strong><?= number_format($kpi['total_forest_ha'], 1) ?> Hektar</strong> atau mencakup <strong><?= $kpi['forest_percentage'] ?>%</strong> dari total luas wilayah amatan. Laju deforestasi tercatat sebesar <strong><?= number_format($kpi['deforestation_rate_ha'], 1) ?> Ha/tahun</strong> dengan rata-rata indeks kehijauan vegetasi (NDVI) berada pada nilai <strong><?= $kpi['avg_ndvi'] ?></strong>.
            </p>
        </div>

        <!-- 4 Kotak KPI Utama -->
        <div class="grid grid-cols-4 gap-3 mb-6">
            <div class="border border-slate-200 bg-slate-50 p-3 rounded-lg text-center">
                <span class="text-[10px] text-slate-500 block uppercase font-medium">Tutupan Hutan (2026)</span>
                <span class="text-base font-bold text-slate-900"><?= number_format($kpi['total_forest_ha'], 0) ?> Ha</span>
            </div>
            <div class="border border-slate-200 bg-slate-50 p-3 rounded-lg text-center">
                <span class="text-[10px] text-slate-500 block uppercase font-medium">Laju Deforestasi</span>
                <span class="text-base font-bold text-red-600">-<?= number_format($kpi['deforestation_rate_ha'], 0) ?> Ha/Thn</span>
            </div>
            <div class="border border-slate-200 bg-slate-50 p-3 rounded-lg text-center">
                <span class="text-[10px] text-slate-500 block uppercase font-medium">Upaya Reforestasi</span>
                <span class="text-base font-bold text-blue-600">+<?= number_format($kpi['reforestation_ha'], 0) ?> Ha/Thn</span>
            </div>
            <div class="border border-slate-200 bg-slate-50 p-3 rounded-lg text-center">
                <span class="text-[10px] text-slate-500 block uppercase font-medium">Rata-Rata NDVI</span>
                <span class="text-base font-bold text-emerald-700"><?= $kpi['avg_ndvi'] ?></span>
            </div>
        </div>

        <!-- Tabel Tren Multi-Tahun -->
        <div class="mb-6">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wide mb-2">1. Tabel Dinamika Spatiotemporal Tutupan Hutan (2018 &ndash; 2026)</h3>
            <table class="w-full text-left text-xs border border-slate-200">
                <thead class="bg-slate-100 text-slate-700 border-b border-slate-200 font-bold">
                    <tr>
                        <th class="p-2 border-r border-slate-200">Tahun</th>
                        <th class="p-2 border-r border-slate-200">Hutan Primer (Ha)</th>
                        <th class="p-2 border-r border-slate-200">Hutan Sekunder (Ha)</th>
                        <th class="p-2 border-r border-slate-200">Total Hutan (Ha)</th>
                        <th class="p-2 border-r border-slate-200">Deforestasi (Ha)</th>
                        <th class="p-2 border-r border-slate-200">Reforestasi (Ha)</th>
                        <th class="p-2">NDVI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($trends as $t): ?>
                        <tr>
                            <td class="p-2 font-bold font-mono border-r border-slate-200"><?= $t['year'] ?></td>
                            <td class="p-2 border-r border-slate-200"><?= number_format($t['primary_forest'], 1) ?></td>
                            <td class="p-2 border-r border-slate-200"><?= number_format($t['secondary_forest'], 1) ?></td>
                            <td class="p-2 font-bold text-emerald-800 border-r border-slate-200"><?= number_format($t['total_forest'], 1) ?></td>
                            <td class="p-2 text-red-600 font-semibold border-r border-slate-200"><?= number_format($t['deforestation_rate'], 1) ?></td>
                            <td class="p-2 text-blue-600 border-r border-slate-200"><?= number_format($t['reforestation'], 1) ?></td>
                            <td class="p-2 font-mono"><?= $t['avg_ndvi'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Tabel Hotspot Deforestasi Terkini -->
        <div class="mb-8">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wide mb-2">2. Daftar Temuan Titik Peringatan Deforestasi Lapangan (Ground Check)</h3>
            <table class="w-full text-left text-xs border border-slate-200">
                <thead class="bg-slate-100 text-slate-700 border-b border-slate-200 font-bold">
                    <tr>
                        <th class="p-2 border-r border-slate-200">Kode Alert</th>
                        <th class="p-2 border-r border-slate-200">Koordinat</th>
                        <th class="p-2 border-r border-slate-200">Tgl Deteksi</th>
                        <th class="p-2 border-r border-slate-200">Jenis Ancaman</th>
                        <th class="p-2 border-r border-slate-200">Luas Hilang</th>
                        <th class="p-2">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach (array_slice($alerts, 0, 8) as $a): ?>
                        <tr>
                            <td class="p-2 font-mono font-bold border-r border-slate-200"><?= htmlspecialchars($a['alert_code']) ?></td>
                            <td class="p-2 font-mono text-[10px] border-r border-slate-200"><?= $a['latitude'] ?>, <?= $a['longitude'] ?></td>
                            <td class="p-2 border-r border-slate-200"><?= htmlspecialchars($a['detection_date']) ?></td>
                            <td class="p-2 border-r border-slate-200"><?= htmlspecialchars($a['threat_type']) ?></td>
                            <td class="p-2 text-red-600 font-bold border-r border-slate-200"><?= number_format($a['area_loss_ha'], 1) ?> Ha</td>
                            <td class="p-2 font-medium"><?= htmlspecialchars($a['verification_status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Tanda Tangan & Pengesahan -->
        <div class="grid grid-cols-2 gap-8 pt-6 border-t border-slate-200 text-xs">
            <div>
                <p class="text-slate-500 mb-1">Diverifikasi oleh Tim GIS &amp; Pemetaan:</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900 underline">Ir. Dian Pratama, S.Si., M.Sc.</p>
                <p class="text-slate-500">Spesialis Penginderaan Jauh Kehutanan</p>
            </div>
            <div class="text-right">
                <p class="text-slate-500 mb-1">Mengetahui Kepala Balai Pemantauan:</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900 underline">Dr. Ahmad Zulkarnain, S.Hut., M.Si.</p>
                <p class="text-slate-500">NIP. 19780514 200212 1 002</p>
            </div>
        </div>

    </div>

</body>
</html>
