<?php

namespace App\Models;

use PDO;

class DeforestationAlert
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(?string $regionId = null, ?string $threatType = null, ?string $status = null, ?string $search = null): array
    {
        $sql = "SELECT a.*, fs.region_name 
                FROM deforestation_alerts a 
                LEFT JOIN (SELECT DISTINCT region_id, region_name FROM forest_statistics) fs 
                ON a.region_id = fs.region_id 
                WHERE 1=1";
        $params = [];

        if ($regionId && $regionId !== 'all') {
            $sql .= " AND a.region_id = :region_id";
            $params[':region_id'] = $regionId;
        }

        if ($threatType && $threatType !== 'all') {
            $sql .= " AND a.threat_type = :threat_type";
            $params[':threat_type'] = $threatType;
        }

        if ($status && $status !== 'all') {
            $sql .= " AND a.verification_status = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $sql .= " AND (a.alert_code LIKE :search OR a.notes LIKE :search OR a.reporter_name LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY a.detection_date DESC, a.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM deforestation_alerts WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        // Auto-generate code if not provided
        if (empty($data['alert_code'])) {
            $prefix = strtoupper(substr($data['region_id'] ?? 'ALT', 0, 3));
            $data['alert_code'] = "ALT-{$prefix}-" . date('Y') . '-' . str_pad((string)rand(100, 999), 3, '0', STR_PAD_LEFT);
        }

        $sql = "INSERT INTO deforestation_alerts (
            alert_code, region_id, latitude, longitude, confidence,
            detection_date, area_loss_ha, threat_type, verification_status,
            reporter_name, notes, photo_url
        ) VALUES (
            :alert_code, :region_id, :latitude, :longitude, :confidence,
            :detection_date, :area_loss_ha, :threat_type, :verification_status,
            :reporter_name, :notes, :photo_url
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':alert_code' => $data['alert_code'],
            ':region_id' => $data['region_id'] ?? 'ikn_kaltim',
            ':latitude' => (float)$data['latitude'],
            ':longitude' => (float)$data['longitude'],
            ':confidence' => $data['confidence'] ?? 'Tinggi',
            ':detection_date' => $data['detection_date'] ?? date('Y-m-d'),
            ':area_loss_ha' => (float)$data['area_loss_ha'],
            ':threat_type' => $data['threat_type'] ?? 'Illegal Logging',
            ':verification_status' => $data['verification_status'] ?? 'Belum Verifikasi',
            ':reporter_name' => $data['reporter_name'] ?? 'Petugas Lapangan',
            ':notes' => $data['notes'] ?? '',
            ':photo_url' => $data['photo_url'] ?? ''
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE deforestation_alerts SET
            region_id = :region_id,
            latitude = :latitude,
            longitude = :longitude,
            confidence = :confidence,
            detection_date = :detection_date,
            area_loss_ha = :area_loss_ha,
            threat_type = :threat_type,
            verification_status = :verification_status,
            reporter_name = :reporter_name,
            notes = :notes,
            photo_url = :photo_url
        WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':region_id' => $data['region_id'],
            ':latitude' => (float)$data['latitude'],
            ':longitude' => (float)$data['longitude'],
            ':confidence' => $data['confidence'],
            ':detection_date' => $data['detection_date'],
            ':area_loss_ha' => (float)$data['area_loss_ha'],
            ':threat_type' => $data['threat_type'],
            ':verification_status' => $data['verification_status'],
            ':reporter_name' => $data['reporter_name'],
            ':notes' => $data['notes'] ?? '',
            ':photo_url' => $data['photo_url'] ?? ''
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM deforestation_alerts WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getCountsByStatus(): array
    {
        $sql = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN verification_status = 'Belum Verifikasi' THEN 1 ELSE 0 END) as unverified,
            SUM(CASE WHEN verification_status = 'Dalam Penyelidikan' THEN 1 ELSE 0 END) as investigating,
            SUM(CASE WHEN verification_status = 'Terverifikasi Valid' THEN 1 ELSE 0 END) as verified,
            SUM(CASE WHEN verification_status = 'Ditindaklanjuti' THEN 1 ELSE 0 END) as resolved,
            SUM(area_loss_ha) as total_loss_ha
        FROM deforestation_alerts";

        $stmt = $this->db->query($sql);
        return $stmt->fetch() ?: [
            'total' => 0, 'unverified' => 0, 'investigating' => 0, 'verified' => 0, 'resolved' => 0, 'total_loss_ha' => 0
        ];
    }

    /**
     * Konversi data titik alert ke GeoJSON FeatureCollection
     */
    public function getGeoJsonFeatures(?string $regionId = null): array
    {
        $alerts = $this->getAll($regionId);
        $features = [];

        foreach ($alerts as $a) {
            $features[] = [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$a['longitude'], (float)$a['latitude']]
                ],
                'properties' => [
                    'id' => $a['id'],
                    'code' => $a['alert_code'],
                    'region' => $a['region_name'] ?? $a['region_id'],
                    'date' => $a['detection_date'],
                    'loss_ha' => (float)$a['area_loss_ha'],
                    'threat' => $a['threat_type'],
                    'status' => $a['verification_status'],
                    'confidence' => $a['confidence'],
                    'reporter' => $a['reporter_name'],
                    'notes' => $a['notes'],
                    'photo' => $a['photo_url']
                ]
            ];
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }
}
