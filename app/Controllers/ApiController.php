<?php

namespace App\Controllers;

use App\Models\ForestChange;
use App\Models\DeforestationAlert;
use App\Models\Database;
use App\Helpers\GeoHelper;
use App\Helpers\Seeder;

class ApiController
{
    private ForestChange $forestModel;
    private DeforestationAlert $alertModel;

    public function __construct()
    {
        $this->forestModel = new ForestChange();
        $this->alertModel = new DeforestationAlert();
    }

    private function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    public function stats(): void
    {
        $region = $_GET['region'] ?? 'all';
        $kpi = $this->forestModel->getSummaryKPI($region);
        $this->jsonResponse(['success' => true, 'data' => $kpi]);
    }

    public function trend(): void
    {
        $region = $_GET['region'] ?? 'all';
        $trend = $this->forestModel->getYearlyTrend($region);
        $this->jsonResponse(['success' => true, 'data' => $trend]);
    }

    public function lulc(): void
    {
        $region = $_GET['region'] ?? 'all';
        $year = (int)($_GET['year'] ?? 2026);
        $lulc = $this->forestModel->getLulcComposition($region, $year);
        $this->jsonResponse(['success' => true, 'data' => $lulc]);
    }

    public function comparison(): void
    {
        $year = (int)($_GET['year'] ?? 2026);
        $comparison = $this->forestModel->getRegionComparison($year);
        $this->jsonResponse(['success' => true, 'data' => $comparison]);
    }

    public function geojsonAlerts(): void
    {
        $region = $_GET['region'] ?? 'all';
        $geojson = $this->alertModel->getGeoJsonFeatures($region);
        $this->jsonResponse($geojson);
    }

    public function geojsonRegions(): void
    {
        $regions = GeoHelper::getRegions();
        $this->jsonResponse(['success' => true, 'data' => $regions]);
    }

    /**
     * Analisis Area of Interest (AOI) dari koordinat poligon yang digambar pengguna
     */
    public function aoiAnalyze(): void
    {
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true) ?: [];
        $coordinates = $body['coordinates'] ?? [];

        if (empty($coordinates) || count($coordinates) < 3) {
            $this->jsonResponse(['success' => false, 'message' => 'Koordinat poligon tidak valid (minimal 3 titik).'], 400);
        }

        $areaHa = GeoHelper::calculatePolygonAreaHa($coordinates);

        // Simulasi rasio kanopi berbasis lokasi geografis titik pusat poligon
        $firstPoint = $coordinates[0];
        $lat = (float)($firstPoint[0] ?? $firstPoint['lat'] ?? 0);
        $lng = (float)($firstPoint[1] ?? $firstPoint['lng'] ?? 0);

        // Basis estimasi tutupan hutan berdasarkan koordinat regional
        $estimatedForestPct = 72.4;
        $regionName = 'Area Pengamatan Kustom';
        $avgNdvi = 0.73;

        if ($lng > 115 && $lng < 118) {
            $regionName = 'Sektor Kalimantan Timur';
            $estimatedForestPct = 68.5;
            $avgNdvi = 0.70;
        } elseif ($lng > 100 && $lng < 103) {
            $regionName = 'Sektor Riau / Sumatera Tengah';
            $estimatedForestPct = 48.2;
            $avgNdvi = 0.58;
        } elseif ($lng > 96 && $lng < 99) {
            $regionName = 'Sektor Aceh / Ekosistem Leuser';
            $estimatedForestPct = 89.1;
            $avgNdvi = 0.81;
        } elseif ($lng > 130 && $lng < 135) {
            $regionName = 'Sektor Papua Barat Daya';
            $estimatedForestPct = 91.4;
            $avgNdvi = 0.83;
        }

        $forestAreaHa = round($areaHa * ($estimatedForestPct / 100), 2);
        $nonForestAreaHa = round($areaHa - $forestAreaHa, 2);
        // Estimasi cadangan karbon (rata-rata hutan tropis Indonesia ~ 150 - 250 ton C/Ha)
        $estimatedCarbonTonnes = round($forestAreaHa * 180, 0);

        $this->jsonResponse([
            'success' => true,
            'data' => [
                'region_name' => $regionName,
                'total_area_ha' => $areaHa,
                'total_area_km2' => round($areaHa / 100, 2),
                'forest_percentage' => $estimatedForestPct,
                'forest_area_ha' => $forestAreaHa,
                'non_forest_area_ha' => $nonForestAreaHa,
                'avg_ndvi' => $avgNdvi,
                'ndvi_classification' => GeoHelper::classifyNdvi($avgNdvi)['label'],
                'estimated_carbon_tonnes' => $estimatedCarbonTonnes,
                'risk_level' => $estimatedForestPct < 60 ? 'Tinggi' : ($estimatedForestPct < 80 ? 'Sedang' : 'Rendah')
            ]
        ]);
    }

    /**
     * Endpoint Reset Demo Data
     */
    public function resetDemo(): void
    {
        $db = Database::getConnection();
        Seeder::resetDatabase($db);
        $this->jsonResponse(['success' => true, 'message' => 'Basis data berhasil direset ke data sampel awal.']);
    }
}
