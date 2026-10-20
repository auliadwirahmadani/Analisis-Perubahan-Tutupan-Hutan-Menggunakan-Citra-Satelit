<?php

namespace App\Controllers;

use App\Models\ForestChange;
use App\Helpers\GeoHelper;

class NdviController
{
    private ForestChange $forestModel;

    public function __construct()
    {
        $this->forestModel = new ForestChange();
    }

    public function index(): void
    {
        $observations = $this->forestModel->getObservations(10);
        $regions = GeoHelper::getRegions();

        $pageTitle = "Kalkulator Spektral & Analisis NDVI Satelit";
        $activeNav = "ndvi";

        require dirname(__DIR__) . '/Views/ndvi/index.php';
    }
}
