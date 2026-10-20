<?php

namespace App\Controllers;

use App\Models\DeforestationAlert;
use App\Helpers\GeoHelper;
use App\Config\App;

class AlertController
{
    private DeforestationAlert $alertModel;

    public function __construct()
    {
        $this->alertModel = new DeforestationAlert();
    }

    public function index(): void
    {
        $region = $_GET['region'] ?? 'all';
        $threat = $_GET['threat'] ?? 'all';
        $status = $_GET['status'] ?? 'all';
        $search = $_GET['search'] ?? null;

        $alerts = $this->alertModel->getAll($region, $threat, $status, $search);
        $summary = $this->alertModel->getCountsByStatus();
        $regions = GeoHelper::getRegions();

        $pageTitle = "Manajemen Peringatan Deforestasi & Verifikasi Lapangan";
        $activeNav = "alerts";

        require dirname(__DIR__) . '/Views/alerts/index.php';
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . App::baseUrl('alerts'));
            exit;
        }

        $photoUrl = '';
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__, 2) . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $fileName = 'alert_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                $photoUrl = 'uploads/' . $fileName;
            }
        }

        $data = [
            'alert_code' => trim($_POST['alert_code'] ?? ''),
            'region_id' => $_POST['region_id'] ?? 'ikn_kaltim',
            'latitude' => $_POST['latitude'] ?? 0,
            'longitude' => $_POST['longitude'] ?? 0,
            'confidence' => $_POST['confidence'] ?? 'Tinggi',
            'detection_date' => $_POST['detection_date'] ?? date('Y-m-d'),
            'area_loss_ha' => $_POST['area_loss_ha'] ?? 0,
            'threat_type' => $_POST['threat_type'] ?? 'Illegal Logging',
            'verification_status' => $_POST['verification_status'] ?? 'Belum Verifikasi',
            'reporter_name' => trim($_POST['reporter_name'] ?? 'Petugas Lapangan'),
            'notes' => trim($_POST['notes'] ?? ''),
            'photo_url' => $photoUrl
        ];

        $this->alertModel->create($data);
        header('Location: ' . App::baseUrl('alerts?msg=created'));
        exit;
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . App::baseUrl('alerts'));
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        $existing = $this->alertModel->getById($id);
        if (!$existing) {
            header('Location: ' . App::baseUrl('alerts?err=not_found'));
            exit;
        }

        $photoUrl = $existing['photo_url'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__, 2) . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $fileName = 'alert_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                $photoUrl = 'uploads/' . $fileName;
            }
        }

        $data = [
            'region_id' => $_POST['region_id'] ?? $existing['region_id'],
            'latitude' => $_POST['latitude'] ?? $existing['latitude'],
            'longitude' => $_POST['longitude'] ?? $existing['longitude'],
            'confidence' => $_POST['confidence'] ?? $existing['confidence'],
            'detection_date' => $_POST['detection_date'] ?? $existing['detection_date'],
            'area_loss_ha' => $_POST['area_loss_ha'] ?? $existing['area_loss_ha'],
            'threat_type' => $_POST['threat_type'] ?? $existing['threat_type'],
            'verification_status' => $_POST['verification_status'] ?? $existing['verification_status'],
            'reporter_name' => trim($_POST['reporter_name'] ?? $existing['reporter_name']),
            'notes' => trim($_POST['notes'] ?? $existing['notes']),
            'photo_url' => $photoUrl
        ];

        $this->alertModel->update($id, $data);
        header('Location: ' . App::baseUrl('alerts?msg=updated'));
        exit;
    }

    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        if ($id > 0) {
            $this->alertModel->delete($id);
        }
        header('Location: ' . App::baseUrl('alerts?msg=deleted'));
        exit;
    }
}
