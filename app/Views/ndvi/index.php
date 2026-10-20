<?php
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Top Headline -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="p-1 rounded bg-emerald-500/10 text-emerald-400 font-mono text-[10px]">REMOTE SENSING ENGINE</span>
                <span class="text-xs text-slate-400">Algoritma Indeks Vegetasi Multi-Spektral</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-100 tracking-tight">
                Kalkulator Spektral &amp; Analisis NDVI Satelit
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-3xl leading-relaxed">
                Pemrosesan piksel spektral citra satelit untuk mendeteksi kerapatan klorofil kanopi hutan dan perubahan biomassa menggunakan indeks vegetasi ternormalisasi (<strong class="text-emerald-400">NDVI</strong>).
            </p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-3 text-xs flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-mono font-bold">
                λ
            </div>
            <div>
                <span class="text-slate-400 block text-[10px]">Formula Standar:</span>
                <span class="font-mono font-bold text-slate-200">NDVI = (NIR - RED) / (NIR + RED)</span>
            </div>
        </div>
    </div>

    <!-- Controls Bar: Choose Samples or Upload Custom Image -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 sm:p-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs text-slate-400 mr-2 font-medium">Pilih Sampel Citra:</span>
            <button id="btnSample2018" class="sample-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 text-white transition-all shadow-sm">
                🌲 Citra 2018 (Hutan Utuh)
            </button>
            <button id="btnSample2024" class="sample-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">
                ⚠️ Citra 2024 (Deforestasi)
            </button>
        </div>

        <div class="flex items-center gap-3">
            <label class="text-xs text-slate-400">Unggah Citra Satelit Sendiri:</label>
            <input type="file" id="customImageInput" accept="image/*" class="text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
        </div>
    </div>

    <!-- Dual Canvas Display: Original vs NDVI Heatmap -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Source Satellite Image -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm flex flex-col items-center">
            <div class="w-full flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                    🛰️ Citra Satelit Sumber (Komposit Warna Alami)
                </h3>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700">Band 4, 3, 2 (RGB)</span>
            </div>
            
            <div class="relative bg-slate-950 rounded-xl overflow-hidden border border-slate-800 shadow-inner flex items-center justify-center p-2 w-full max-w-[480px]">
                <canvas id="sourceCanvas" class="rounded-lg max-w-full h-auto cursor-crosshair"></canvas>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Arahkan kursor ke atas gambar untuk membaca nilai spektral piksel.</p>
        </div>

        <!-- NDVI Heatmap Result Canvas -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm flex flex-col items-center">
            <div class="w-full flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2">
                    🔥 Hasil Klasifikasi Spektral NDVI
                </h3>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Color-Ramp Remote Sensing</span>
            </div>

            <div class="relative bg-slate-950 rounded-xl overflow-hidden border border-slate-800 shadow-inner flex items-center justify-center p-2 w-full max-w-[480px]">
                <canvas id="ndviCanvas" class="rounded-lg max-w-full h-auto cursor-crosshair"></canvas>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Piksel diproses langsung menggunakan formula NDVI ternormalisasi.</p>
        </div>

    </div>

    <!-- Live Pixel Inspector & Stats Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Hover Inspector Card -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-ping"></span>
                <h4 class="font-bold text-slate-100 text-sm">Inspektor Piksel Real-Time</h4>
            </div>
            <p class="text-xs text-slate-400 mb-4">Gerakkan kursor pada citra satelit di atas untuk membaca nilai radiometric piksel tertentu:</p>

            <div class="space-y-2 text-xs">
                <div class="flex justify-between bg-slate-800/60 p-2 rounded-lg">
                    <span class="text-slate-400">Koordinat Piksel (X, Y):</span>
                    <span id="hoverCoords" class="font-mono text-slate-200">X: 0, Y: 0</span>
                </div>
                <div class="flex justify-between bg-slate-800/60 p-2 rounded-lg">
                    <span class="text-slate-400">Radiansi RGB Asli:</span>
                    <span id="hoverRgb" class="font-mono text-slate-200">R:0 G:0 B:0</span>
                </div>
                <div class="flex justify-between bg-slate-800/60 p-2 rounded-lg">
                    <span class="text-slate-400">Nilai NDVI Terhitung:</span>
                    <span id="hoverNdvi" class="font-mono font-bold text-amber-400 text-sm">0.000</span>
                </div>
                <div class="flex justify-between bg-slate-800/60 p-2 rounded-lg">
                    <span class="text-slate-400">Kategori Tutupan:</span>
                    <span id="hoverCategory" class="font-semibold text-emerald-400">Hutan Tropis Lebat</span>
                </div>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm flex flex-col justify-between">
            <div>
                <h4 class="font-bold text-slate-100 text-sm mb-3">Statistik Spektral Citra</h4>
                
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-slate-800/60 p-3 rounded-xl">
                        <span class="text-[11px] text-slate-400 block">Rata-rata NDVI</span>
                        <span id="metricMeanNdvi" class="text-xl font-extrabold text-amber-400 font-mono">0.00</span>
                    </div>
                    <div class="bg-slate-800/60 p-3 rounded-xl">
                        <span class="text-[11px] text-slate-400 block">Tutupan Kanopi Hutan</span>
                        <span id="metricForestCover" class="text-xl font-extrabold text-emerald-400 font-mono">0%</span>
                    </div>
                </div>

                <div class="flex justify-between text-xs bg-slate-800/40 p-2 rounded-lg mb-2">
                    <span class="text-slate-400">NDVI Minimum:</span>
                    <span id="metricMinNdvi" class="font-mono text-slate-200">0.00</span>
                </div>
                <div class="flex justify-between text-xs bg-slate-800/40 p-2 rounded-lg">
                    <span class="text-slate-400">NDVI Maksimum:</span>
                    <span id="metricMaxNdvi" class="font-mono text-slate-200">0.00</span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-800 text-[11px] text-slate-400 flex items-center justify-between">
                <span>Dukungan Python Engine:</span>
                <span class="text-emerald-400 font-bold flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Siap (NumPy + Pillow)
                </span>
            </div>
        </div>

        <!-- Classification Distribution Bars -->
        <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5 shadow-sm">
            <h4 class="font-bold text-slate-100 text-sm mb-3">Distribusi Kategori Tutupan Kanopi</h4>
            
            <div class="space-y-3 text-xs">
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-slate-300 font-medium">Hutan Lebat (&gt; 0.6)</span>
                        <span id="textDensePct" class="font-bold text-emerald-400 font-mono">0%</span>
                    </div>
                    <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                        <div id="barDense" class="bg-[#065f46] h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-slate-300 font-medium">Vegetasi Sedang / Kebun (0.4 - 0.6)</span>
                        <span id="textModeratePct" class="font-bold text-emerald-300 font-mono">0%</span>
                    </div>
                    <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                        <div id="barModerate" class="bg-[#10b981] h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-slate-300 font-medium">Semak Belukar (0.2 - 0.4)</span>
                        <span id="textShrubPct" class="font-bold text-yellow-400 font-mono">0%</span>
                    </div>
                    <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                        <div id="barShrub" class="bg-[#eab308] h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-slate-300 font-medium">Lahan Terbuka / Tambang (0.0 - 0.2)</span>
                        <span id="textBarePct" class="font-bold text-red-400 font-mono">0%</span>
                    </div>
                    <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                        <div id="barBare" class="bg-[#ef4444] h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-slate-300 font-medium">Badan Air / Awan (&lt; 0.0)</span>
                        <span id="textWaterPct" class="font-bold text-blue-400 font-mono">0%</span>
                    </div>
                    <div class="w-full bg-slate-800 h-2 rounded-full overflow-hidden">
                        <div id="barWater" class="bg-[#3b82f6] h-full rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Inisialisasi Script Processor NDVI -->
<script src="<?= \App\Config\App::baseUrl('assets/js/ndvi_processor.js') ?>"></script>

<?php
require dirname(__DIR__) . '/layouts/footer.php';
?>
