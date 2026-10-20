<?php

namespace App\Controllers;

use App\Helpers\GeoHelper;
use App\Models\DeforestationAlert;

class MapController
{
    private DeforestationAlert $alertModel;

    public function __construct()
    {
        $this->alertModel = new DeforestationAlert();
    }

    public function index(): void
    {
        $selectedRegion = $_GET['region'] ?? 'ikn_kaltim';
        $regions = GeoHelper::getRegions();
        $currentRegion = $regions[$selectedRegion] ?? $regions['ikn_kaltim'];
        
        $alertSummary = $this->alertModel->getCountsByStatus();

        $pageTitle = "Peta WebGIS & Deteksi Satelit Interaktif";
        $activeNav = "map";

        require dirname(__DIR__) . '/Views/map/index.php';
    }
}
