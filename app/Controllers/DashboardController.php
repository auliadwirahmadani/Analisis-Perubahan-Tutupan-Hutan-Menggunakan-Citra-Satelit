<?php

namespace App\Controllers;

use App\Models\ForestChange;
use App\Models\DeforestationAlert;
use App\Helpers\GeoHelper;

class DashboardController
{
    private ForestChange $forestModel;
    private DeforestationAlert $alertModel;

    public function __construct()
    {
        $this->forestModel = new ForestChange();
        $this->alertModel = new DeforestationAlert();
    }

    public function index(): void
    {
        $selectedRegion = $_GET['region'] ?? 'all';
        
        $regions = GeoHelper::getRegions();
        $kpi = $this->forestModel->getSummaryKPI($selectedRegion);
        $yearlyTrend = $this->forestModel->getYearlyTrend($selectedRegion);
        $regionComparison = $this->forestModel->getRegionComparison(2026);
        $lulcComposition = $this->forestModel->getLulcComposition($selectedRegion, 2026);
        $recentAlerts = $this->alertModel->getAll($selectedRegion, null, null, null);
        $recentAlerts = array_slice($recentAlerts, 0, 5);
        $observations = $this->forestModel->getObservations(5);
        $alertSummary = $this->alertModel->getCountsByStatus();

        $pageTitle = "Dashboard Analisis Perubahan Tutupan Hutan";
        $activeNav = "dashboard";

        require dirname(__DIR__) . '/Views/dashboard/index.php';
    }
}
