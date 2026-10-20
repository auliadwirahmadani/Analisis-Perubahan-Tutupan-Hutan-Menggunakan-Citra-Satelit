<?php

namespace App\Models;

use PDO;

class ForestChange
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Dapatkan ringkasan statistik nasional/global untuk tahun terakhir (2026)
     */
    public function getSummaryKPI(?string $regionId = null): array
    {
        $where = "";
        $params = [];
        if ($regionId && $regionId !== 'all') {
            $where = "WHERE region_id = :region_id";
            $params[':region_id'] = $regionId;
        }

        // Ambil data tahun 2026 dan 2018 untuk melihat komparasi
        $sql2026 = "SELECT 
            SUM(primary_forest_ha) as primary_forest,
            SUM(secondary_forest_ha) as secondary_forest,
            SUM(primary_forest_ha + secondary_forest_ha) as total_forest,
            SUM(deforestation_rate_ha) as deforestation_rate,
            SUM(reforestation_ha) as reforestation,
            SUM(plantation_ha) as plantation,
            SUM(bare_land_ha) as bare_land,
            SUM(total_area_ha) as total_area,
            AVG(avg_ndvi) as avg_ndvi
        FROM forest_statistics 
        WHERE year = 2026 " . ($regionId && $regionId !== 'all' ? "AND region_id = :region_id" : "");

        $stmt2026 = $this->db->prepare($sql2026);
        $stmt2026->execute($params);
        $data2026 = $stmt2026->fetch() ?: [];

        // Data awal 2018
        $sql2018 = "SELECT 
            SUM(primary_forest_ha + secondary_forest_ha) as total_forest_2018,
            AVG(avg_ndvi) as avg_ndvi_2018
        FROM forest_statistics 
        WHERE year = 2018 " . ($regionId && $regionId !== 'all' ? "AND region_id = :region_id" : "");

        $stmt2018 = $this->db->prepare($sql2018);
        $stmt2018->execute($params);
        $data2018 = $stmt2018->fetch() ?: [];

        $forest2018 = (float)($data2018['total_forest_2018'] ?? 0);
        $forest2026 = (float)($data2026['total_forest'] ?? 0);
        $netChangeHa = $forest2026 - $forest2018;
        $netChangePercent = $forest2018 > 0 ? (($netChangeHa / $forest2018) * 100) : 0;

        // Ambil jumlah alert aktif
        $alertSql = "SELECT COUNT(*) FROM deforestation_alerts " . ($regionId && $regionId !== 'all' ? "WHERE region_id = :region_id" : "");
        $stmtAlert = $this->db->prepare($alertSql);
        $stmtAlert->execute($params);
        $totalAlerts = (int)$stmtAlert->fetchColumn();

        return [
            'total_forest_ha' => $forest2026,
            'primary_forest_ha' => (float)($data2026['primary_forest'] ?? 0),
            'secondary_forest_ha' => (float)($data2026['secondary_forest'] ?? 0),
            'total_area_ha' => (float)($data2026['total_area'] ?? 0),
            'forest_percentage' => (float)($data2026['total_area'] ?? 0) > 0 ? round(($forest2026 / (float)$data2026['total_area']) * 100, 1) : 0,
            'deforestation_rate_ha' => (float)($data2026['deforestation_rate'] ?? 0),
            'reforestation_ha' => (float)($data2026['reforestation'] ?? 0),
            'net_loss_ha' => abs($netChangeHa),
            'net_change_percent' => round($netChangePercent, 2),
            'avg_ndvi' => round((float)($data2026['avg_ndvi'] ?? 0), 2),
            'active_alerts_count' => $totalAlerts
        ];
    }

    /**
     * Dapatkan tren tutupan hutan dari tahun ke tahun (2018 - 2026)
     */
    public function getYearlyTrend(?string $regionId = null): array
    {
        $where = "";
        $params = [];
        if ($regionId && $regionId !== 'all') {
            $where = "WHERE region_id = :region_id";
            $params[':region_id'] = $regionId;
        }

        $sql = "SELECT 
            year,
            SUM(primary_forest_ha) as primary_forest,
            SUM(secondary_forest_ha) as secondary_forest,
            SUM(primary_forest_ha + secondary_forest_ha) as total_forest,
            SUM(plantation_ha) as plantation,
            SUM(shrub_ha) as shrub,
            SUM(bare_land_ha) as bare_land,
            SUM(built_up_ha) as built_up,
            SUM(deforestation_rate_ha) as deforestation_rate,
            SUM(reforestation_ha) as reforestation,
            AVG(avg_ndvi) as avg_ndvi
        FROM forest_statistics
        $where
        GROUP BY year
        ORDER BY year ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Dapatkan data perbandingan laju deforestasi antar region
     */
    public function getRegionComparison(int $year = 2026): array
    {
        $sql = "SELECT 
            region_id,
            region_name,
            primary_forest_ha,
            secondary_forest_ha,
            (primary_forest_ha + secondary_forest_ha) as total_forest_ha,
            deforestation_rate_ha,
            reforestation_ha,
            total_area_ha,
            avg_ndvi
        FROM forest_statistics
        WHERE year = :year
        ORDER BY deforestation_rate_ha DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':year' => $year]);
        return $stmt->fetchAll();
    }

    /**
     * Dapatkan data komposisi tutupan lahan (LULC) untuk tahun tertentu
     */
    public function getLulcComposition(?string $regionId = null, int $year = 2026): array
    {
        $where = "WHERE year = :year";
        $params = [':year' => $year];

        if ($regionId && $regionId !== 'all') {
            $where .= " AND region_id = :region_id";
            $params[':region_id'] = $regionId;
        }

        $sql = "SELECT 
            SUM(primary_forest_ha) as primary_forest,
            SUM(secondary_forest_ha) as secondary_forest,
            SUM(plantation_ha) as plantation,
            SUM(shrub_ha) as shrub,
            SUM(bare_land_ha) as bare_land,
            SUM(water_body_ha) as water_body,
            SUM(built_up_ha) as built_up
        FROM forest_statistics
        $where";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch() ?: [];

        return [
            ['name' => 'Hutan Lahan Kering Primer', 'ha' => (float)($row['primary_forest'] ?? 0), 'color' => '#065f46'],
            ['name' => 'Hutan Lahan Kering Sekunder', 'ha' => (float)($row['secondary_forest'] ?? 0), 'color' => '#10b981'],
            ['name' => 'Perkebunan / HTI / Sawit', 'ha' => (float)($row['plantation'] ?? 0), 'color' => '#eab308'],
            ['name' => 'Semak Belukar / Alang-alang', 'ha' => (float)($row['shrub'] ?? 0), 'color' => '#84cc16'],
            ['name' => 'Lahan Terbuka / Tambang', 'ha' => (float)($row['bare_land'] ?? 0), 'color' => '#ef4444'],
            ['name' => 'Badan Air & Sungai', 'ha' => (float)($row['water_body'] ?? 0), 'color' => '#3b82f6'],
            ['name' => 'Pemukiman & Infrastruktur', 'ha' => (float)($row['built_up'] ?? 0), 'color' => '#8b5cf6']
        ];
    }

    /**
     * Dapatkan riwayat observasi citra satelit
     */
    public function getObservations(int $limit = 5): array
    {
        $stmt = $this->db->query("SELECT * FROM satellite_observations ORDER BY acquisition_date DESC LIMIT $limit");
        return $stmt->fetchAll();
    }
}
