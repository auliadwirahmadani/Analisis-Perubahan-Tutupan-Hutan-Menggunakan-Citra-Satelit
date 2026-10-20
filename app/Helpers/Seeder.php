<?php

namespace App\Helpers;

use PDO;

class Seeder
{
    public static function seed(PDO $db): void
    {
        // 1. Seed Forest Statistics (Multi-temporal 2018 - 2026)
        $stmtCheck = $db->query("SELECT COUNT(*) FROM forest_statistics");
        if ($stmtCheck->fetchColumn() == 0) {
            $stats = [
                // IKN & Sepaku, Kalimantan Timur (Total Area: 256,000 Ha)
                [
                    'region_id' => 'ikn_kaltim',
                    'region_name' => 'Kawasan Nusantara (IKN & Sepaku, Kaltim)',
                    'year' => 2018,
                    'primary_forest_ha' => 42150.0,
                    'secondary_forest_ha' => 98400.0,
                    'plantation_ha' => 67200.0,
                    'shrub_ha' => 28500.0,
                    'bare_land_ha' => 7450.0,
                    'water_body_ha' => 6100.0,
                    'built_up_ha' => 6200.0,
                    'total_area_ha' => 256000.0,
                    'deforestation_rate_ha' => 2340.0,
                    'reforestation_ha' => 850.0,
                    'avg_ndvi' => 0.74
                ],
                [
                    'region_id' => 'ikn_kaltim',
                    'region_name' => 'Kawasan Nusantara (IKN & Sepaku, Kaltim)',
                    'year' => 2020,
                    'primary_forest_ha' => 41200.0,
                    'secondary_forest_ha' => 96100.0,
                    'plantation_ha' => 68900.0,
                    'shrub_ha' => 27800.0,
                    'bare_land_ha' => 8900.0,
                    'water_body_ha' => 6050.0,
                    'built_up_ha' => 7050.0,
                    'total_area_ha' => 256000.0,
                    'deforestation_rate_ha' => 2650.0,
                    'reforestation_ha' => 1100.0,
                    'avg_ndvi' => 0.72
                ],
                [
                    'region_id' => 'ikn_kaltim',
                    'region_name' => 'Kawasan Nusantara (IKN & Sepaku, Kaltim)',
                    'year' => 2022,
                    'primary_forest_ha' => 40300.0,
                    'secondary_forest_ha' => 92800.0,
                    'plantation_ha' => 67500.0,
                    'shrub_ha' => 29200.0,
                    'bare_land_ha' => 12400.0,
                    'water_body_ha' => 5950.0,
                    'built_up_ha' => 7850.0,
                    'total_area_ha' => 256000.0,
                    'deforestation_rate_ha' => 3800.0,
                    'reforestation_ha' => 1850.0,
                    'avg_ndvi' => 0.69
                ],
                [
                    'region_id' => 'ikn_kaltim',
                    'region_name' => 'Kawasan Nusantara (IKN & Sepaku, Kaltim)',
                    'year' => 2024,
                    'primary_forest_ha' => 39850.0,
                    'secondary_forest_ha' => 90150.0,
                    'plantation_ha' => 64800.0,
                    'shrub_ha' => 28100.0,
                    'bare_land_ha' => 17200.0,
                    'water_body_ha' => 5800.0,
                    'built_up_ha' => 10100.0,
                    'total_area_ha' => 256000.0,
                    'deforestation_rate_ha' => 3100.0,
                    'reforestation_ha' => 3450.0,
                    'avg_ndvi' => 0.67
                ],
                [
                    'region_id' => 'ikn_kaltim',
                    'region_name' => 'Kawasan Nusantara (IKN & Sepaku, Kaltim)',
                    'year' => 2026,
                    'primary_forest_ha' => 39500.0,
                    'secondary_forest_ha' => 91800.0,
                    'plantation_ha' => 61200.0,
                    'shrub_ha' => 26400.0,
                    'bare_land_ha' => 16500.0,
                    'water_body_ha' => 6100.0,
                    'built_up_ha' => 14500.0,
                    'total_area_ha' => 256000.0,
                    'deforestation_rate_ha' => 1950.0,
                    'reforestation_ha' => 4800.0,
                    'avg_ndvi' => 0.70
                ],

                // Taman Nasional Tesso Nilo & Sekitarnya, Riau (Total: 83,000 Ha)
                [
                    'region_id' => 'tesso_nilo_riau',
                    'region_name' => 'Lansekap Hutan Tesso Nilo, Riau',
                    'year' => 2018,
                    'primary_forest_ha' => 18500.0,
                    'secondary_forest_ha' => 24200.0,
                    'plantation_ha' => 29400.0,
                    'shrub_ha' => 6800.0,
                    'bare_land_ha' => 2100.0,
                    'water_body_ha' => 1200.0,
                    'built_up_ha' => 800.0,
                    'total_area_ha' => 83000.0,
                    'deforestation_rate_ha' => 2400.0,
                    'reforestation_ha' => 320.0,
                    'avg_ndvi' => 0.65
                ],
                [
                    'region_id' => 'tesso_nilo_riau',
                    'region_name' => 'Lansekap Hutan Tesso Nilo, Riau',
                    'year' => 2020,
                    'primary_forest_ha' => 15200.0,
                    'secondary_forest_ha' => 22800.0,
                    'plantation_ha' => 33500.0,
                    'shrub_ha' => 7100.0,
                    'bare_land_ha' => 2300.0,
                    'water_body_ha' => 1150.0,
                    'built_up_ha' => 950.0,
                    'total_area_ha' => 83000.0,
                    'deforestation_rate_ha' => 2850.0,
                    'reforestation_ha' => 410.0,
                    'avg_ndvi' => 0.61
                ],
                [
                    'region_id' => 'tesso_nilo_riau',
                    'region_name' => 'Lansekap Hutan Tesso Nilo, Riau',
                    'year' => 2022,
                    'primary_forest_ha' => 13400.0,
                    'secondary_forest_ha' => 21900.0,
                    'plantation_ha' => 36800.0,
                    'shrub_ha' => 6400.0,
                    'bare_land_ha' => 2450.0,
                    'water_body_ha' => 1100.0,
                    'built_up_ha' => 950.0,
                    'total_area_ha' => 83000.0,
                    'deforestation_rate_ha' => 1950.0,
                    'reforestation_ha' => 520.0,
                    'avg_ndvi' => 0.58
                ],
                [
                    'region_id' => 'tesso_nilo_riau',
                    'region_name' => 'Lansekap Hutan Tesso Nilo, Riau',
                    'year' => 2024,
                    'primary_forest_ha' => 12100.0,
                    'secondary_forest_ha' => 21200.0,
                    'plantation_ha' => 38900.0,
                    'shrub_ha' => 5900.0,
                    'bare_land_ha' => 2750.0,
                    'water_body_ha' => 1050.0,
                    'built_up_ha' => 1100.0,
                    'total_area_ha' => 83000.0,
                    'deforestation_rate_ha' => 1650.0,
                    'reforestation_ha' => 780.0,
                    'avg_ndvi' => 0.56
                ],
                [
                    'region_id' => 'tesso_nilo_riau',
                    'region_name' => 'Lansekap Hutan Tesso Nilo, Riau',
                    'year' => 2026,
                    'primary_forest_ha' => 11800.0,
                    'secondary_forest_ha' => 21700.0,
                    'plantation_ha' => 38400.0,
                    'shrub_ha' => 5800.0,
                    'bare_land_ha' => 2900.0,
                    'water_body_ha' => 1100.0,
                    'built_up_ha' => 1300.0,
                    'total_area_ha' => 83000.0,
                    'deforestation_rate_ha' => 890.0,
                    'reforestation_ha' => 1250.0,
                    'avg_ndvi' => 0.59
                ],

                // Kawasan Ekosistem Leuser, Aceh - Sumut (Sampel Blok 150,000 Ha)
                [
                    'region_id' => 'leuser_aceh',
                    'region_name' => 'Kawasan Hutan Lindung Leuser, Aceh',
                    'year' => 2018,
                    'primary_forest_ha' => 118500.0,
                    'secondary_forest_ha' => 21200.0,
                    'plantation_ha' => 4500.0,
                    'shrub_ha' => 3200.0,
                    'bare_land_ha' => 1100.0,
                    'water_body_ha' => 1200.0,
                    'built_up_ha' => 300.0,
                    'total_area_ha' => 150000.0,
                    'deforestation_rate_ha' => 750.0,
                    'reforestation_ha' => 210.0,
                    'avg_ndvi' => 0.81
                ],
                [
                    'region_id' => 'leuser_aceh',
                    'region_name' => 'Kawasan Hutan Lindung Leuser, Aceh',
                    'year' => 2020,
                    'primary_forest_ha' => 117800.0,
                    'secondary_forest_ha' => 21400.0,
                    'plantation_ha' => 4900.0,
                    'shrub_ha' => 3350.0,
                    'bare_land_ha' => 1050.0,
                    'water_body_ha' => 1150.0,
                    'built_up_ha' => 350.0,
                    'total_area_ha' => 150000.0,
                    'deforestation_rate_ha' => 680.0,
                    'reforestation_ha' => 320.0,
                    'avg_ndvi' => 0.80
                ],
                [
                    'region_id' => 'leuser_aceh',
                    'region_name' => 'Kawasan Hutan Lindung Leuser, Aceh',
                    'year' => 2022,
                    'primary_forest_ha' => 116900.0,
                    'secondary_forest_ha' => 21650.0,
                    'plantation_ha' => 5400.0,
                    'shrub_ha' => 3450.0,
                    'bare_land_ha' => 1100.0,
                    'water_body_ha' => 1100.0,
                    'built_up_ha' => 400.0,
                    'total_area_ha' => 150000.0,
                    'deforestation_rate_ha' => 820.0,
                    'reforestation_ha' => 450.0,
                    'avg_ndvi' => 0.79
                ],
                [
                    'region_id' => 'leuser_aceh',
                    'region_name' => 'Kawasan Hutan Lindung Leuser, Aceh',
                    'year' => 2024,
                    'primary_forest_ha' => 116100.0,
                    'secondary_forest_ha' => 21800.0,
                    'plantation_ha' => 5800.0,
                    'shrub_ha' => 3600.0,
                    'bare_land_ha' => 1150.0,
                    'water_body_ha' => 1100.0,
                    'built_up_ha' => 450.0,
                    'total_area_ha' => 150000.0,
                    'deforestation_rate_ha' => 740.0,
                    'reforestation_ha' => 510.0,
                    'avg_ndvi' => 0.79
                ],
                [
                    'region_id' => 'leuser_aceh',
                    'region_name' => 'Kawasan Hutan Lindung Leuser, Aceh',
                    'year' => 2026,
                    'primary_forest_ha' => 115650.0,
                    'secondary_forest_ha' => 22100.0,
                    'plantation_ha' => 5950.0,
                    'shrub_ha' => 3500.0,
                    'bare_land_ha' => 1180.0,
                    'water_body_ha' => 1120.0,
                    'built_up_ha' => 500.0,
                    'total_area_ha' => 150000.0,
                    'deforestation_rate_ha' => 490.0,
                    'reforestation_ha' => 640.0,
                    'avg_ndvi' => 0.80
                ],

                // Hutan Alam Sorong - Tambrauw, Papua Barat Daya (Total 320,000 Ha)
                [
                    'region_id' => 'papua_barat',
                    'region_name' => 'Hutan Hujan Tropis Sorong, Papua Barat Daya',
                    'year' => 2018,
                    'primary_forest_ha' => 275000.0,
                    'secondary_forest_ha' => 31200.0,
                    'plantation_ha' => 5400.0,
                    'shrub_ha' => 4800.0,
                    'bare_land_ha' => 1800.0,
                    'water_body_ha' => 1200.0,
                    'built_up_ha' => 600.0,
                    'total_area_ha' => 320000.0,
                    'deforestation_rate_ha' => 950.0,
                    'reforestation_ha' => 150.0,
                    'avg_ndvi' => 0.84
                ],
                [
                    'region_id' => 'papua_barat',
                    'region_name' => 'Hutan Hujan Tropis Sorong, Papua Barat Daya',
                    'year' => 2020,
                    'primary_forest_ha' => 273400.0,
                    'secondary_forest_ha' => 31900.0,
                    'plantation_ha' => 6200.0,
                    'shrub_ha' => 5100.0,
                    'bare_land_ha' => 1950.0,
                    'water_body_ha' => 1180.0,
                    'built_up_ha' => 670.0,
                    'total_area_ha' => 320000.0,
                    'deforestation_rate_ha' => 1200.0,
                    'reforestation_ha' => 210.0,
                    'avg_ndvi' => 0.83
                ],
                [
                    'region_id' => 'papua_barat',
                    'region_name' => 'Hutan Hujan Tropis Sorong, Papua Barat Daya',
                    'year' => 2022,
                    'primary_forest_ha' => 271200.0,
                    'secondary_forest_ha' => 32800.0,
                    'plantation_ha' => 7100.0,
                    'shrub_ha' => 5200.0,
                    'bare_land_ha' => 2150.0,
                    'water_body_ha' => 1150.0,
                    'built_up_ha' => 800.0,
                    'total_area_ha' => 320000.0,
                    'deforestation_rate_ha' => 1550.0,
                    'reforestation_ha' => 290.0,
                    'avg_ndvi' => 0.82
                ],
                [
                    'region_id' => 'papua_barat',
                    'region_name' => 'Hutan Hujan Tropis Sorong, Papua Barat Daya',
                    'year' => 2024,
                    'primary_forest_ha' => 268900.0,
                    'secondary_forest_ha' => 33900.0,
                    'plantation_ha' => 8300.0,
                    'shrub_ha' => 5300.0,
                    'bare_land_ha' => 2200.0,
                    'water_body_ha' => 1120.0,
                    'built_up_ha' => 980.0,
                    'total_area_ha' => 320000.0,
                    'deforestation_rate_ha' => 1620.0,
                    'reforestation_ha' => 350.0,
                    'avg_ndvi' => 0.81
                ],
                [
                    'region_id' => 'papua_barat',
                    'region_name' => 'Hutan Hujan Tropis Sorong, Papua Barat Daya',
                    'year' => 2026,
                    'primary_forest_ha' => 267400.0,
                    'secondary_forest_ha' => 34500.0,
                    'plantation_ha' => 8900.0,
                    'shrub_ha' => 5400.0,
                    'bare_land_ha' => 2350.0,
                    'water_body_ha' => 1100.0,
                    'built_up_ha' => 1150.0,
                    'total_area_ha' => 320000.0,
                    'deforestation_rate_ha' => 1340.0,
                    'reforestation_ha' => 480.0,
                    'avg_ndvi' => 0.81
                ]
            ];

            $insertSql = "INSERT INTO forest_statistics (
                region_id, region_name, year, primary_forest_ha, secondary_forest_ha, 
                plantation_ha, shrub_ha, bare_land_ha, water_body_ha, built_up_ha, 
                total_area_ha, deforestation_rate_ha, reforestation_ha, avg_ndvi
            ) VALUES (
                :region_id, :region_name, :year, :primary_forest_ha, :secondary_forest_ha,
                :plantation_ha, :shrub_ha, :bare_land_ha, :water_body_ha, :built_up_ha,
                :total_area_ha, :deforestation_rate_ha, :reforestation_ha, :avg_ndvi
            )";

            $stmt = $db->prepare($insertSql);
            foreach ($stats as $row) {
                $stmt->execute($row);
            }
        }

        // 2. Seed Deforestation Alerts (Ground verification / Hotspots)
        $stmtAlertCheck = $db->query("SELECT COUNT(*) FROM deforestation_alerts");
        if ($stmtAlertCheck->fetchColumn() == 0) {
            $alerts = [
                [
                    'alert_code' => 'ALT-IKN-2026-001',
                    'region_id' => 'ikn_kaltim',
                    'latitude' => -0.9654,
                    'longitude' => 116.7321,
                    'confidence' => 'Tinggi',
                    'detection_date' => '2026-03-14',
                    'area_loss_ha' => 14.8,
                    'threat_type' => 'Pembangunan Infrastruktur',
                    'verification_status' => 'Terverifikasi Valid',
                    'reporter_name' => 'Tim Pemantau Satelit Sentinel LAPAN/BRIN',
                    'notes' => 'Pembukaan koridor jalan sekunder melewati penyangga hutan lindung Sungai Wain.',
                    'photo_url' => 'assets/samples/alert_infra.jpg'
                ],
                [
                    'alert_code' => 'ALT-IKN-2026-002',
                    'region_id' => 'ikn_kaltim',
                    'latitude' => -0.9142,
                    'longitude' => 116.6890,
                    'confidence' => 'Tinggi',
                    'detection_date' => '2026-04-02',
                    'area_loss_ha' => 8.5,
                    'threat_type' => 'Pertambangan Ilegal',
                    'verification_status' => 'Ditindaklanjuti',
                    'reporter_name' => 'Polisi Kehutanan (Gakkum KLHK)',
                    'notes' => 'Terdeteksi lubang galian tambang batubara tanpa izin di tepi kawasan hutan produksi terbatas.',
                    'photo_url' => 'assets/samples/alert_tambang.jpg'
                ],
                [
                    'alert_code' => 'ALT-IKN-2026-003',
                    'region_id' => 'ikn_kaltim',
                    'latitude' => -1.0210,
                    'longitude' => 116.7915,
                    'confidence' => 'Sedang',
                    'detection_date' => '2026-05-11',
                    'area_loss_ha' => 5.2,
                    'threat_type' => 'Illegal Logging',
                    'verification_status' => 'Dalam Penyelidikan',
                    'reporter_name' => 'Masyarakat Mitra Polhut Sepaku',
                    'notes' => 'Penebangan selektif jenis meranti dan kapur di zona penyangga Teluk Balikpapan.',
                    'photo_url' => ''
                ],
                [
                    'alert_code' => 'ALT-RSN-2026-004',
                    'region_id' => 'tesso_nilo_riau',
                    'latitude' => -0.1245,
                    'longitude' => 101.6210,
                    'confidence' => 'Tinggi',
                    'detection_date' => '2026-02-19',
                    'area_loss_ha' => 24.6,
                    'threat_type' => 'Ekspansi Perkebunan Sawit',
                    'verification_status' => 'Ditindaklanjuti',
                    'reporter_name' => 'Balai TN Tesso Nilo & WWF',
                    'notes' => 'Pembersihan lahan dengan alat berat di bekas kantong habitat gajah sumatera.',
                    'photo_url' => 'assets/samples/alert_sawit.jpg'
                ],
                [
                    'alert_code' => 'ALT-RSN-2026-005',
                    'region_id' => 'tesso_nilo_riau',
                    'latitude' => -0.1890,
                    'longitude' => 101.5420,
                    'confidence' => 'Tinggi',
                    'detection_date' => '2026-03-28',
                    'area_loss_ha' => 31.2,
                    'threat_type' => 'Kebakaran Hutan',
                    'verification_status' => 'Terverifikasi Valid',
                    'reporter_name' => 'Manggala Agni Daops Riau',
                    'notes' => 'Kebakaran lahan gambut musiman dengan anomali suhu thermal sensor MODIS/VIIRS.',
                    'photo_url' => 'assets/samples/alert_fire.jpg'
                ],
                [
                    'alert_code' => 'ALT-RSN-2026-006',
                    'region_id' => 'tesso_nilo_riau',
                    'latitude' => -0.0980,
                    'longitude' => 101.7105,
                    'confidence' => 'Sedang',
                    'detection_date' => '2026-06-05',
                    'area_loss_ha' => 9.4,
                    'threat_type' => 'Illegal Logging',
                    'verification_status' => 'Belum Verifikasi',
                    'reporter_name' => 'Sistem Otomatis GLAD Alerts',
                    'notes' => 'Penurunan tajam kanopi hutan pada citra Landsat 9 OLI swath 127/060.',
                    'photo_url' => ''
                ],
                [
                    'alert_code' => 'ALT-LSR-2026-007',
                    'region_id' => 'leuser_aceh',
                    'latitude' => 3.7820,
                    'longitude' => 97.4320,
                    'confidence' => 'Tinggi',
                    'detection_date' => '2026-01-15',
                    'area_loss_ha' => 7.8,
                    'threat_type' => 'Perambahan Pertanian',
                    'verification_status' => 'Terverifikasi Valid',
                    'reporter_name' => 'Yayasan HAkA & Ranger Leuser',
                    'notes' => 'Perambahan lereng bukit curam untuk tanaman hortikultura di batas luar hutan lindung.',
                    'photo_url' => ''
                ],
                [
                    'alert_code' => 'ALT-LSR-2026-008',
                    'region_id' => 'leuser_aceh',
                    'latitude' => 3.9100,
                    'longitude' => 97.5890,
                    'confidence' => 'Sedang',
                    'detection_date' => '2026-04-18',
                    'area_loss_ha' => 4.3,
                    'threat_type' => 'Illegal Logging',
                    'verification_status' => 'Dalam Penyelidikan',
                    'reporter_name' => 'Patroli Terpadu BKSDA Aceh',
                    'notes' => 'Jalur lintasan kayu gelondongan di bantaran sungai aliran pegunungan.',
                    'photo_url' => ''
                ],
                [
                    'alert_code' => 'ALT-PPB-2026-009',
                    'region_id' => 'papua_barat',
                    'latitude' => -0.8750,
                    'longitude' => 131.4200,
                    'confidence' => 'Tinggi',
                    'detection_date' => '2026-02-08',
                    'area_loss_ha' => 18.9,
                    'threat_type' => 'Pembangunan Jalan Loging',
                    'verification_status' => 'Terverifikasi Valid',
                    'reporter_name' => 'Jaringan Pemantau Hutan Independen Papua',
                    'notes' => 'Pembuatan akses jalan angkut kayu baru sepanjang 3.4 km menembus hutan primer.',
                    'photo_url' => ''
                ],
                [
                    'alert_code' => 'ALT-PPB-2026-010',
                    'region_id' => 'papua_barat',
                    'latitude' => -0.7620,
                    'longitude' => 131.5830,
                    'confidence' => 'Sedang',
                    'detection_date' => '2026-05-22',
                    'area_loss_ha' => 11.7,
                    'threat_type' => 'Ekspansi Perkebunan Sawit',
                    'verification_status' => 'Dalam Penyelidikan',
                    'reporter_name' => 'Tim GIS BPKH Wilayah XVII Manokwari',
                    'notes' => 'Land clearing pada blok izin pelepasan kawasan hutan yang masih dievaluasi kementerian.',
                    'photo_url' => ''
                ]
            ];

            $alertSql = "INSERT INTO deforestation_alerts (
                alert_code, region_id, latitude, longitude, confidence,
                detection_date, area_loss_ha, threat_type, verification_status,
                reporter_name, notes, photo_url
            ) VALUES (
                :alert_code, :region_id, :latitude, :longitude, :confidence,
                :detection_date, :area_loss_ha, :threat_type, :verification_status,
                :reporter_name, :notes, :photo_url
            )";

            $stmtAlert = $db->prepare($alertSql);
            foreach ($alerts as $a) {
                $stmtAlert->execute($a);
            }
        }

        // 3. Seed Satellite Observations Records
        $stmtObsCheck = $db->query("SELECT COUNT(*) FROM satellite_observations");
        if ($stmtObsCheck->fetchColumn() == 0) {
            $obs = [
                ['Sentinel-2 MSI Level-2A', 'MSI (Multi-Spectral Instrument)', '2026-04-12', 4.2, 'IKN Sepaku & Bukit Soeharto', 10.0, 0.71, 'Selesai Diproses'],
                ['Landsat 9 OLI-2 / TIRS-2', 'Operational Land Imager 2', '2026-03-29', 8.5, 'Taman Nasional Tesso Nilo Riau', 30.0, 0.58, 'Selesai Diproses'],
                ['Sentinel-2 MSI Level-2A', 'MSI (Multi-Spectral Instrument)', '2026-04-05', 2.1, 'Kawasan Ekosistem Leuser Aceh', 10.0, 0.80, 'Selesai Diproses'],
                ['PlanetScope High-Res', 'Dove Multispectral PS2.SD', '2026-05-02', 1.4, 'Hutan Alam Sorong Papua', 3.0, 0.82, 'Selesai Diproses'],
                ['MODIS Terra/Aqua Surface', 'MODIS 250m Vegetation Index', '2026-05-10', 12.0, 'Zona Konservasi Kalimantan', 250.0, 0.69, 'Arsip Otomatis']
            ];

            $obsSql = "INSERT INTO satellite_observations (
                satellite_name, sensor, acquisition_date, cloud_cover_percent,
                region_name, resolution_meters, avg_ndvi, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtObs = $db->prepare($obsSql);
            foreach ($obs as $o) {
                $stmtObs->execute($o);
            }
        }
    }

    public static function resetDatabase(PDO $db): void
    {
        $db->exec("DELETE FROM forest_statistics");
        $db->exec("DELETE FROM deforestation_alerts");
        $db->exec("DELETE FROM satellite_observations");
        self::seed($db);
    }
}
