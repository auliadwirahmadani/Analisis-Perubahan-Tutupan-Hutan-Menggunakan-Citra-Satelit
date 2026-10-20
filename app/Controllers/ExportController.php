<?php

namespace App\Controllers;

use App\Models\ForestChange;
use App\Models\DeforestationAlert;
use App\Helpers\GeoHelper;

class ExportController
{
    private ForestChange $forestModel;
    private DeforestationAlert $alertModel;

    public function __construct()
    {
        $this->forestModel = new ForestChange();
        $this->alertModel = new DeforestationAlert();
    }

    /**
     * Ekspor Data Tutupan Hutan / Alert ke CSV
     */
    public function csv(): void
    {
        $type = $_GET['type'] ?? 'stats';
        $region = $_GET['region'] ?? 'all';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="forestwatch_' . $type . '_' . date('Ymd_His') . '.csv"');

        // Output UTF-8 BOM for Microsoft Excel compatibility
        echo "\xEF\xBB\xBF";

        $output = fopen('php://output', 'w');

        if ($type === 'alerts') {
            fputcsv($output, [
                'ID', 'Kode Alert', 'Wilayah', 'Latitude', 'Longitude', 'Tingkat Keyakinan',
                'Tanggal Deteksi', 'Luas Hilang (Ha)', 'Jenis Ancaman', 'Status Verifikasi',
                'Nama Pelapor / Sumber', 'Catatan Lapangan'
            ]);

            $alerts = $this->alertModel->getAll($region);
            foreach ($alerts as $a) {
                fputcsv($output, [
                    $a['id'],
                    $a['alert_code'],
                    $a['region_name'] ?? $a['region_id'],
                    $a['latitude'],
                    $a['longitude'],
                    $a['confidence'],
                    $a['detection_date'],
                    $a['area_loss_ha'],
                    $a['threat_type'],
                    $a['verification_status'],
                    $a['reporter_name'],
                    $a['notes']
                ]);
            }
        } else {
            // Stats export
            fputcsv($output, [
                'Tahun', 'Kode Wilayah', 'Hutan Primer (Ha)', 'Hutan Sekunder (Ha)',
                'Total Hutan (Ha)', 'Perkebunan (Ha)', 'Semak Belukar (Ha)', 'Lahan Terbuka (Ha)',
                'Laju Deforestasi (Ha/Thn)', 'Reforestasi (Ha)', 'Rata-rata NDVI'
            ]);

            $trends = $this->forestModel->getYearlyTrend($region);
            foreach ($trends as $t) {
                fputcsv($output, [
                    $t['year'],
                    $region,
                    $t['primary_forest'],
                    $t['secondary_forest'],
                    $t['total_forest'],
                    $t['plantation'],
                    $t['shrub'],
                    $t['bare_land'],
                    $t['deforestation_rate'],
                    $t['reforestation'],
                    $t['avg_ndvi']
                ]);
            }
        }

        fclose($output);
        exit;
    }

    /**
     * Ekspor Titik Deforestasi ke GeoJSON
     */
    public function geojson(): void
    {
        $region = $_GET['region'] ?? 'all';
        $geojson = $this->alertModel->getGeoJsonFeatures($region);

        header('Content-Type: application/geo+json; charset=utf-8');
        header('Content-Disposition: attachment; filename="deforestation_alerts_' . date('Ymd_His') . '.geojson"');

        echo json_encode($geojson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Halaman Template Cetak PDF Resmi
     */
    public function printReport(): void
    {
        $region = $_GET['region'] ?? 'all';
        $regions = GeoHelper::getRegions();
        $regionName = $regions[$region]['name'] ?? 'Seluruh Wilayah Pengamatan Indonesia';

        $kpi = $this->forestModel->getSummaryKPI($region);
        $trends = $this->forestModel->getYearlyTrend($region);
        $comparison = $this->forestModel->getRegionComparison(2026);
        $alerts = $this->alertModel->getAll($region);
        $observations = $this->forestModel->getObservations(5);

        require dirname(__DIR__) . '/Views/reports/print.php';
    }
}
