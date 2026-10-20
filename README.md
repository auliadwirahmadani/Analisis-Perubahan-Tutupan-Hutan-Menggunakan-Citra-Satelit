# 🛰️ ForestWatch WebGIS &mdash; Analisis Perubahan Tutupan Hutan Menggunakan Citra Satelit

Sistem informasi geospasial modern, interaktif, dan komprehensif untuk **Analisis, Pemantauan, dan Deteksi Perubahan Tutupan Hutan (Deforestasi & Reforestasi) Menggunakan Citra Satelit Penginderaan Jauh**. 

Dibangun dengan arsitektur modular **PHP 8.3 (MVC)**, basis data instan (*zero-config*) **SQLite (dengan dukungan opsi MySQL)**, visualisasi grafik spasial **ApexCharts**, peta interaktif **Leaflet WebGIS (Swipe Curtain & AOI Polygon Analysis)**, serta engine komputasi spektral citra satelit **Python 3 (NumPy & Pillow)**.

---

## 🌟 Fitur Unggulan Sistem

### 1. 📊 Dashboard Analitik Eksekutif Spatiotemporal
- **4 Key Performance Indicator (KPI) Metric Cards**:
  - **Total Tutupan Hutan**: Luas tutupan hutan primer dan sekunder dalam Hektar (Ha) dan km², dilengkapi persentase proporsi tutupan terhadap luas daratan.
  - **Laju Deforestasi Tahunan**: Kehilangan kanopi hutan neto per tahun beserta angka kumulatif.
  - **Upaya Reforestasi & Restorasi**: Pertumbuhan hutan sekunder dan program rehabilitasi vegetasi.
  - **Rata-Rata Indeks NDVI**: Angka indeks kehijauan vegetasi ternormalisasi dan jumlah peringatan hotspot deforestasi aktif.
- **3 Visualisasi Grafik Interaktif (ApexCharts)**:
  - **Dinamika Tutupan Hutan (2018 &ndash; 2026)**: Area chart multi-tahun memantau pergerakan hutan primer, sekunder, deforestasi, dan reforestasi.
  - **Komposisi Tutupan Lahan (LULC Donut Chart)**: Proporsi 7 kelas klasifikasi citra satelit (Hutan Primer, Sekunder, Perkebunan/HTI, Semak Belukar, Lahan Terbuka/Tambang, Badan Air, Pemukiman).
  - **Perbandingan Laju Deforestasi Antar Wilayah**: Stacked bar chart membandingkan tingkat kerentanan deforestasi di berbagai bentang alam hutan Indonesia.
- **Tabel Metadata Log Satelit**: Catatan riwayat akuisisi sensor Sentinel-2 MSI Level-2A, Landsat 9 OLI, dan PlanetScope dengan info tutupan awan (*cloud cover*) dan resolusi spasial.

---

### 2. 🗺️ Peta WebGIS Interaktif & Deteksi Satelit
- **Mode Geser Perbandingan (Side-by-Side / Swipe Map)**:
  - Slider interaktif yang memungkinkan pengguna menggeser tirai pembanding antara citra satelit kondisi awal (*baseline*) dan citra satelit terkini untuk melihat secara kasat mata pembukaan lahan dan hilangnya tajuk pohon.
- **Multi-Basemap Resolusi Tinggi**:
  - Citra Satelit Esri World Imagery (High-Resolution Satellite).
  - OpenStreetMap Standard Carto.
  - CartoDB Dark Matter untuk kontras visualisasi analisis malam hari.
- **Layer Klasifikasi & Sebaran Hotspot**:
  - Poligon zona konservasi dan batas tutupan kanopi hutan.
  - Titik peringatan deforestasi dengan efek radar berkedip (*pulsing radar marker*) dan popup informasi lengkap (estimasi luas hilang, tanggal deteksi, jenis ancaman, status verifikasi).
- **Alat Ukur Area Minat (AOI - Area of Interest Drawing Tool)**:
  - Pengguna dapat menggambar poligon bebas di mana saja pada peta.
  - Sistem secara dinamis menghitung luas area dalam Hektar dan km² menggunakan formula geodetik bola bumi (*Spherical Excess*).
  - Mengestimasi proporsi tutupan hutan, nilai rata-rata NDVI, cadangan karbon hutan (*carbon stock* dalam Ton), dan tingkat risiko deforestasi pada area yang digambar.
- **Cakupan Wilayah Observasi Kritis di Indonesia**:
  1. **Kawasan Nusantara (IKN & Sepaku, Kalimantan Timur)**: Pemantauan koridor hutan lindung Sungai Wain, Bukit Soeharto, dan pembukaan jalan IKN.
  2. **Lansekap Hutan Tesso Nilo (Riau)**: Pemantauan perambahan perkebunan kelapa sawit dan degradasi lahan gambut.
  3. **Kawasan Hutan Lindung Leuser (Aceh - Sumut)**: Hutan hujan tropis dataran rendah terluas di Asia Tenggara.
  4. **Hutan Hujan Tropis Sorong (Papua Barat Daya)**: Pemantauan pembukaan jalan loging dan perlindungan mega-biodiversitas Papua.

---

### 3. 🛰️ Kalkulator Spektral & Analisis Citra Satelit (NDVI)
- **Komputasi Spektral Ternormalisasi**:
  $$\text{NDVI} = \frac{\text{NIR} - \text{RED}}{\text{NIR} + \text{RED}}$$
- **Dual-Canvas Heatmap Viewer**:
  - Sisi kiri: Citra satelit sumber (Natural Color RGB).
  - Sisi kanan: Hasil konversi spektral NDVI dengan palet warna standar penginderaan jauh:
    - 🔵 **Biru**: Badan Air / Awan ($\text{NDVI} < 0.0$)
    - 🔴 **Cokelat/Merah**: Lahan Terbuka / Tambang / Gundul ($0.0 \le \text{NDVI} < 0.2$)
    - 🟡 **Kuning**: Semak Belukar / Alang-alang ($0.2 \le \text{NDVI} < 0.4$)
    - 🟢 **Hijau Muda**: Vegetasi Sedang / Perkebunan ($0.4 \le \text{NDVI} < 0.6$)
    - 🌲 **Hijau Tua**: Hutan Kanopi Rapat / Primer ($\text{NDVI} \ge 0.6$)
- **Inspektor Piksel Real-Time**: Arahkan kursor ke titik mana pun pada citra untuk membaca koordinat piksel $(X, Y)$, radiansi warna RGB asli, nilai desimal NDVI, dan nama klasifikasinya.
- **Dukungan Citra Kustom**: Pilihan sampel multi-temporal bawaan (Tahun 2018 vs 2024) atau unggah citra satelit mandiri (PNG/JPG).
- **Engine Pemroses Python Terintegrasi**: Skrip CLI `scripts/process_satellite.py` bertenaga NumPy & Pillow untuk komputasi matriks piksel citra satelit skala besar.

---

### 4. 🚨 Manajemen Peringatan Deforestasi & Verifikasi Lapangan (CRUD)
- **Pencatatan Insiden Lapangan (*Ground Check*)**:
  - Kode unik peringatan otomatis (*Auto-generated code*).
  - Koordinat GPS (Latitude / Longitude) dengan tautan langsung ke titik koordinat di peta WebGIS.
  - Estimasi luas tutupan yang hilang (Hektar).
  - Klasifikasi jenis ancaman (*Illegal Logging, Kebakaran Hutan, Ekspansi Sawit, Pertambangan Ilegal, Pembangunan Infrastruktur*).
  - Tingkat keyakinan deteksi satelit (*Tinggi, Sedang, Rendah*).
  - Status alur kerja: *Belum Verifikasi*, *Dalam Penyelidikan*, *Terverifikasi Valid*, dan *Ditindaklanjuti*.
  - Catatan investigasi lapangan dan unggah foto dokumentasi bukti lapangan / drone.
- **Pencarian & Filter Cepat**: Filter multi-kriteria berdasarkan wilayah, tipe ancaman, status verifikasi, dan pencarian kata kunci.

---

### 5. 📑 Ekspor Data & Laporan Resmi Cetak PDF
- **Cetak Laporan PDF Resmi**: Halaman khusus cetak berkop resmi instansi, ringkasan eksekutif, tabel statistik multi-tahun, daftar titik peringatan, serta blok tanda tangan pengesahan pejabat (*window.print* siap pakai).
- **Ekspor Data ke CSV / Excel**: Dilengkapi *UTF-8 BOM encoding* sehingga tabel langsung rapi saat dibuka di Microsoft Excel tanpa masalah karakter.
- **Ekspor ke GeoJSON**: Unduh data spasial titik hotspot peringatan deforestasi untuk diimpor ke aplikasi SIG desktop seperti QGIS atau ArcGIS.

---

## 📁 Struktur Direktori Proyek

```
analisis perubahan tutupan hutan menggunakan citra satelit/
├── .htaccess                       # Routing rewrite URL Apache Laragon
├── index.php                       # Entry point aplikasi & router
├── README.md                       # Dokumentasi lengkap proyek
├── run.bat                         # Shortcut eksekusi server instan
├── app/
│   ├── Config/
│   │   └── App.php                 # Konfigurasi aplikasi, path base URL, & database
│   ├── Controllers/
│   │   ├── AlertController.php     # CRUD titik peringatan deforestasi lapangan
│   │   ├── ApiController.php       # Endpoint RESTful JSON (stats, trend, geojson, aoi)
│   │   ├── DashboardController.php # Halaman utama dashboard analitik & KPI
│   │   ├── ExportController.php    # Ekspor CSV, GeoJSON, dan cetak PDF resmi
│   │   ├── MapController.php       # Halaman peta WebGIS & Swipe Map
│   │   └── NdviController.php      # Halaman kalkulator spektral NDVI
│   ├── Helpers/
│   │   ├── GeoHelper.php           # Utilitas koordinat, bounding box, & kalkulasi luas poligon
│   │   └── Seeder.php              # Generator data spasial & observasi satelit realistis
│   ├── Models/
│   │   ├── Database.php            # Singleton koneksi PDO (SQLite + opsi MySQL)
│   │   ├── DeforestationAlert.php  # Model data alert & format GeoJSON
│   │   └── ForestChange.php        # Model analitik tutupan lahan & tren multi-tahun
│   └── Views/
│       ├── alerts/
│       │   └── index.php           # Antarmuka manajemen peringatan & modal CRUD
│       ├── dashboard/
│       │   └── index.php           # Tampilan 4 KPI cards, ApexCharts, & log satelit
│       ├── layouts/
│       │   ├── footer.php          # Footer & skrip global
│       │   └── header.php          # Navbar, CDN Leaflet, Tailwind CSS, & ApexCharts
│       ├── map/
│       │   └── index.php           # Tampilan WebGIS Leaflet, Swipe Map, & modal AOI
│       ├── ndvi/
│       │   └── index.php           # Tampilan dual-canvas NDVI & inspektor piksel
│       └── reports/
│           └── print.php           # Template cetak PDF laporan resmi
├── assets/
│   ├── css/
│   │   └── custom.css              # Styling animasi marker radar & swipe slider
│   ├── js/
│   │   ├── app.js                  # Handler tema gelap/terang & reset data demo
│   │   ├── charts.js               # Konfigurasi visualisasi ApexCharts
│   │   ├── ndvi_processor.js       # Engine kanvas HTML5 pemroses piksel spektral
│   │   └── webgis.js               # Inisialisasi peta Leaflet & modul gambar AOI
│   └── samples/                    # Citra satelit multi-temporal sampel
├── database/
│   └── forest_watch.sqlite         # Basis data SQLite lokal (dibuat otomatis)
├── scripts/
│   └── process_satellite.py        # Engine Python pengolah matriks citra satelit
└── uploads/                        # Direktori penyimpanan foto bukti lapangan
```

---

## 🚀 Panduan Menjalankan Proyek

### Cara 1: Menggunakan Laragon (Sangat Direkomendasikan)
1. Buka aplikasi **Laragon**.
2. Klik tombol **Start All** pada Laragon (Apache running).
3. Buka browser dan kunjungi:
   ```
   http://localhost/analisis%20perubahan%20tutupan%20hutan%20menggunakan%20citra%20satelit/
   ```
   *Atau jika menggunakan Virtual Host Laragon:*
   ```
   http://analisis-perubahan-tutupan-hutan-menggunakan-citra-satelit.test
   ```

### Cara 2: Menggunakan File Shortcut `run.bat`
1. Masuk ke folder proyek.
2. Klik ganda pada file `run.bat`.
3. Browser akan otomatis terbuka pada alamat `http://localhost:8000`.

### Cara 3: Menggunakan Terminal / Command Line
Jalankan perintah berikut di PowerShell atau Command Prompt:
```bash
cd "c:\laragon\www\analisis perubahan tutupan hutan menggunakan citra satelit"
php -S localhost:8000 index.php
```

---

## 🧪 Pengujian Skrip Python Satellite Processing

Aplikasi dilengkapi modul Python mandiri untuk pemrosesan citra multispektral. Anda dapat mengujinya dengan perintah:

1. **Uji Coba Algoritma NDVI & Klasifikasi Matriks**:
   ```bash
   python scripts/process_satellite.py --test
   ```
2. **Pemrosesan Citra Satelit Input ke Heatmap Output**:
   ```bash
   python scripts/process_satellite.py --input assets/samples/sample_forest_2024.png --output assets/samples/output_test.png
   ```

---

## 🔄 Reset Data Demo
Jika data peringatan deforestasi telah banyak diubah atau dihapus saat demonstrasi, Anda dapat mengembalikannya ke sampel awal kapan saja:
1. Klik tombol **Reset Data** (ikon putar kembali) di pojok kanan atas navbar aplikasi.
2. Konfirmasi pada dialog pop-up SweetAlert2.
3. Seluruh tabel akan kembali terisi dengan data observasi satelit multi-tahun yang lengkap dan rapi.
