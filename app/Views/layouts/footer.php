    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-900/60 py-6 text-xs text-slate-400 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-lg bg-emerald-600/30 flex items-center justify-center text-emerald-400 font-bold text-xs">
                    FW
                </div>
                <span>&copy; <?= date('Y') ?> ForestWatch WebGIS &mdash; Analisis Penginderaan Jauh Tutupan Hutan Satelit.</span>
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <span>Sensor: Sentinel-2 MSI / Landsat 9 OLI</span>
                <span class="text-slate-600">•</span>
                <span>Resolusi: 10m - 30m</span>
                <span class="text-slate-600">•</span>
                <span class="text-emerald-400 font-mono">v<?= \App\Config\App::APP_VERSION ?></span>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?= \App\Config\App::baseUrl('assets/js/app.js') ?>"></script>
</body>
</html>
