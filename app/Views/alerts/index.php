<?php
require dirname(__DIR__) . '/layouts/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    <!-- Top Headline & Action -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-ping"></span>
                <span class="text-xs font-semibold text-red-400 uppercase tracking-wider">Ground Check &amp; Sat Monitoring</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-100 tracking-tight">
                Peringatan Deforestasi &amp; Verifikasi Lapangan
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Manajemen insiden pembalakan liar, perambahan, dan degradasi kawasan hutan berbasis peringatan dini citra satelit
            </p>
        </div>

        <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/30 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Laporan Baru
        </button>
    </div>

    <!-- Alert Notifications (success or error messages) -->
    <?php if (isset($_GET['msg'])): ?>
        <div class="p-3 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-xs flex items-center justify-between">
            <span>
                <?php if ($_GET['msg'] === 'created') echo '✅ Laporan titik deforestasi baru berhasil didaftarkan!'; ?>
                <?php if ($_GET['msg'] === 'updated') echo '✅ Data laporan deforestasi berhasil diperbarui!'; ?>
                <?php if ($_GET['msg'] === 'deleted') echo '🗑️ Laporan deforestasi berhasil dihapus.'; ?>
            </span>
            <a href="<?= \App\Config\App::baseUrl('alerts') ?>" class="text-emerald-300 font-bold hover:underline">Tutup</a>
        </div>
    <?php endif; ?>

    <!-- Status Overview Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-slate-900/70 border border-slate-800 rounded-xl p-4">
            <span class="text-[11px] text-slate-400 block">Total Hotspot</span>
            <span class="text-xl font-bold text-slate-100"><?= $summary['total'] ?? 0 ?> <span class="text-xs font-normal text-slate-400">Titik</span></span>
        </div>
        <div class="bg-slate-900/70 border border-slate-800 rounded-xl p-4">
            <span class="text-[11px] text-slate-400 block">Belum Terverifikasi</span>
            <span class="text-xl font-bold text-amber-400"><?= $summary['unverified'] ?? 0 ?> <span class="text-xs font-normal text-slate-400">Kasus</span></span>
        </div>
        <div class="bg-slate-900/70 border border-slate-800 rounded-xl p-4">
            <span class="text-[11px] text-slate-400 block">Terverifikasi Valid</span>
            <span class="text-xl font-bold text-emerald-400"><?= $summary['verified'] ?? 0 ?> <span class="text-xs font-normal text-slate-400">Kasus</span></span>
        </div>
        <div class="bg-slate-900/70 border border-slate-800 rounded-xl p-4">
            <span class="text-[11px] text-slate-400 block">Total Kehilangan</span>
            <span class="text-xl font-bold text-red-400"><?= number_format($summary['total_loss_ha'] ?? 0, 1) ?> <span class="text-xs font-normal text-slate-400">Ha</span></span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4">
        <form method="GET" action="<?= \App\Config\App::baseUrl('alerts') ?>" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Cari kode, pelapor, atau catatan..." class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring-1 focus:ring-emerald-500">

            <select name="threat" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="all">Semua Jenis Ancaman</option>
                <option value="Illegal Logging" <?= ($_GET['threat'] ?? '') === 'Illegal Logging' ? 'selected' : '' ?>>Illegal Logging</option>
                <option value="Kebakaran Hutan" <?= ($_GET['threat'] ?? '') === 'Kebakaran Hutan' ? 'selected' : '' ?>>Kebakaran Hutan</option>
                <option value="Ekspansi Perkebunan Sawit" <?= ($_GET['threat'] ?? '') === 'Ekspansi Perkebunan Sawit' ? 'selected' : '' ?>>Ekspansi Perkebunan Sawit</option>
                <option value="Pertambangan Ilegal" <?= ($_GET['threat'] ?? '') === 'Pertambangan Ilegal' ? 'selected' : '' ?>>Pertambangan Ilegal</option>
                <option value="Pembangunan Infrastruktur" <?= ($_GET['threat'] ?? '') === 'Pembangunan Infrastruktur' ? 'selected' : '' ?>>Pembangunan Infrastruktur</option>
            </select>

            <select name="status" class="bg-slate-800 border border-slate-700 text-slate-200 text-xs rounded-xl px-3 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                <option value="all">Semua Status Verifikasi</option>
                <option value="Belum Verifikasi" <?= ($_GET['status'] ?? '') === 'Belum Verifikasi' ? 'selected' : '' ?>>Belum Verifikasi</option>
                <option value="Dalam Penyelidikan" <?= ($_GET['status'] ?? '') === 'Dalam Penyelidikan' ? 'selected' : '' ?>>Dalam Penyelidikan</option>
                <option value="Terverifikasi Valid" <?= ($_GET['status'] ?? '') === 'Terverifikasi Valid' ? 'selected' : '' ?>>Terverifikasi Valid</option>
                <option value="Ditindaklanjuti" <?= ($_GET['status'] ?? '') === 'Ditindaklanjuti' ? 'selected' : '' ?>>Ditindaklanjuti</option>
            </select>

            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-semibold">
                Filter
            </button>
            <?php if (!empty($_GET['search']) || !empty($_GET['threat']) || !empty($_GET['status'])): ?>
                <a href="<?= \App\Config\App::baseUrl('alerts') ?>" class="text-xs text-slate-400 hover:text-white">Reset</a>
            <?php endif; ?>
        </form>

        <a href="<?= \App\Config\App::baseUrl('export/csv?type=alerts') ?>" class="text-xs text-emerald-400 hover:text-emerald-300 flex items-center gap-1 font-semibold">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Unduh CSV Alert
        </a>
    </div>

    <!-- Alerts Table -->
    <div class="bg-slate-900/70 border border-slate-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-800/70 text-slate-400 uppercase text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Kode &amp; Foto</th>
                        <th class="px-4 py-3">Wilayah &amp; Koordinat</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Ancaman</th>
                        <th class="px-4 py-3">Luas Hilang</th>
                        <th class="px-4 py-3">Status Verifikasi</th>
                        <th class="px-4 py-3">Pelapor / Catatan</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <?php if (empty($alerts)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                Tidak ada data peringatan deforestasi yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($alerts as $a): ?>
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <?php if (!empty($a['photo_url'])): ?>
                                            <img src="<?= \App\Config\App::baseUrl($a['photo_url']) ?>" alt="Bukti" class="w-9 h-9 rounded-lg object-cover border border-slate-700">
                                        <?php else: ?>
                                            <div class="w-9 h-9 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500 font-bold text-[10px]">
                                                SAT
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <span class="font-mono font-bold text-slate-200 block"><?= htmlspecialchars($a['alert_code']) ?></span>
                                            <span class="text-[10px] text-slate-500">Keyakinan: <?= htmlspecialchars($a['confidence']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-slate-200 font-medium block"><?= htmlspecialchars($a['region_name'] ?? $a['region_id']) ?></span>
                                    <span class="font-mono text-[10px] text-slate-500"><?= $a['latitude'] ?>, <?= $a['longitude'] ?></span>
                                </td>
                                <td class="px-4 py-3 text-slate-300 font-mono text-[11px]">
                                    <?= htmlspecialchars($a['detection_date']) ?>
                                </td>
                                <td class="px-4 py-3 text-slate-200 font-medium">
                                    <?= htmlspecialchars($a['threat_type']) ?>
                                </td>
                                <td class="px-4 py-3 font-bold text-red-400 font-mono">
                                    <?= number_format($a['area_loss_ha'], 1) ?> Ha
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($a['verification_status'] === 'Terverifikasi Valid'): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Terverifikasi Valid</span>
                                    <?php elseif ($a['verification_status'] === 'Ditindaklanjuti'): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-blue-500/20 text-blue-400 border border-blue-500/30">Ditindaklanjuti</span>
                                    <?php elseif ($a['verification_status'] === 'Dalam Penyelidikan'): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-500/20 text-amber-400 border border-amber-500/30">Penyelidikan</span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-700 text-slate-300 border border-slate-600">Belum Verifikasi</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 max-w-xs">
                                    <span class="text-slate-300 block font-medium truncate"><?= htmlspecialchars($a['reporter_name']) ?></span>
                                    <p class="text-[11px] text-slate-400 truncate"><?= htmlspecialchars($a['notes']) ?></p>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="<?= \App\Config\App::baseUrl('map?region=' . $a['region_id']) ?>" title="Lihat di Peta WebGIS" class="p-1 rounded bg-slate-800 hover:bg-slate-700 text-emerald-400 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <button onclick='openEditModal(<?= json_encode($a) ?>)' title="Ubah Data" class="p-1 rounded bg-slate-800 hover:bg-slate-700 text-blue-400 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button onclick="confirmDelete(<?= $a['id'] ?>)" title="Hapus Laporan" class="p-1 rounded bg-slate-800 hover:bg-slate-700 text-red-400 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Form Create & Edit Alert -->
<div id="alertModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-xl p-6 shadow-2xl relative">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
            <h3 id="modalTitle" class="text-base font-bold text-slate-100">Tambah Laporan Deforestasi Baru</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-white p-1 rounded hover:bg-slate-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="alertForm" method="POST" action="<?= \App\Config\App::baseUrl('alerts/store') ?>" enctype="multipart/form-data" class="space-y-4 text-xs">
            <input type="hidden" name="id" id="formId">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-400 mb-1">Kode Alert (Opsional / Otomatis):</label>
                    <input type="text" name="alert_code" id="formCode" placeholder="Otomatis jika dikosongkan" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Wilayah Pengamatan:</label>
                    <select name="region_id" id="formRegion" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                        <?php foreach ($regions as $key => $r): ?>
                            <option value="<?= $key ?>"><?= htmlspecialchars($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-400 mb-1">Latitude (Lintang):</label>
                    <input type="number" step="0.0001" name="latitude" id="formLat" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Longitude (Bujur):</label>
                    <input type="number" step="0.0001" name="longitude" id="formLng" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-400 mb-1">Luas Hilang (Hektar):</label>
                    <input type="number" step="0.1" name="area_loss_ha" id="formLossHa" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Tanggal Deteksi:</label>
                    <input type="date" name="detection_date" id="formDate" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Tingkat Keyakinan:</label>
                    <select name="confidence" id="formConfidence" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                        <option value="Tinggi">Tinggi</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Rendah">Rendah</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-400 mb-1">Jenis Ancaman:</label>
                    <select name="threat_type" id="formThreat" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                        <option value="Illegal Logging">Illegal Logging</option>
                        <option value="Kebakaran Hutan">Kebakaran Hutan</option>
                        <option value="Ekspansi Perkebunan Sawit">Ekspansi Perkebunan Sawit</option>
                        <option value="Pertambangan Ilegal">Pertambangan Ilegal</option>
                        <option value="Pembangunan Infrastruktur">Pembangunan Infrastruktur</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 mb-1">Status Verifikasi:</label>
                    <select name="verification_status" id="formStatus" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
                        <option value="Belum Verifikasi">Belum Verifikasi</option>
                        <option value="Dalam Penyelidikan">Dalam Penyelidikan</option>
                        <option value="Terverifikasi Valid">Terverifikasi Valid</option>
                        <option value="Ditindaklanjuti">Ditindaklanjuti</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-slate-400 mb-1">Nama Petugas Pelapor / Sumber Deteksi:</label>
                <input type="text" name="reporter_name" id="formReporter" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-slate-200">
            </div>

            <div>
                <label class="block text-slate-400 mb-1">Catatan Hasil Ground Check Lapangan:</label>
                <textarea name="notes" id="formNotes" rows="3" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-slate-200"></textarea>
            </div>

            <div>
                <label class="block text-slate-400 mb-1">Unggah Foto Bukti Lapangan / Citra Drone:</label>
                <input type="file" name="photo" accept="image/*" class="w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold shadow-lg shadow-emerald-600/30">
                    Simpan Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById('alertModal');
    const form = document.getElementById('alertForm');
    const modalTitle = document.getElementById('modalTitle');

    function openCreateModal() {
        form.reset();
        document.getElementById('formId').value = '';
        document.getElementById('formLat').value = '-0.9654';
        document.getElementById('formLng').value = '116.7321';
        modalTitle.innerText = 'Tambah Laporan Deforestasi Baru';
        form.action = '<?= \App\Config\App::baseUrl('alerts/store') ?>';
        modal.classList.remove('hidden');
    }

    function openEditModal(data) {
        document.getElementById('formId').value = data.id;
        document.getElementById('formCode').value = data.alert_code;
        document.getElementById('formRegion').value = data.region_id;
        document.getElementById('formLat').value = data.latitude;
        document.getElementById('formLng').value = data.longitude;
        document.getElementById('formLossHa').value = data.area_loss_ha;
        document.getElementById('formDate').value = data.detection_date;
        document.getElementById('formConfidence').value = data.confidence;
        document.getElementById('formThreat').value = data.threat_type;
        document.getElementById('formStatus').value = data.verification_status;
        document.getElementById('formReporter').value = data.reporter_name;
        document.getElementById('formNotes').value = data.notes || '';
        
        modalTitle.innerText = 'Ubah Laporan Deforestasi #' + data.alert_code;
        form.action = '<?= \App\Config\App::baseUrl('alerts/update') ?>';
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    function confirmDelete(id) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Laporan Deforestasi?',
                text: 'Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#334155',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= \App\Config\App::baseUrl('alerts/delete?id=') ?>' + id;
                }
            });
        } else {
            if (confirm('Yakin ingin menghapus laporan ini?')) {
                window.location.href = '<?= \App\Config\App::baseUrl('alerts/delete?id=') ?>' + id;
            }
        }
    }
</script>

<?php
require dirname(__DIR__) . '/layouts/footer.php';
?>
