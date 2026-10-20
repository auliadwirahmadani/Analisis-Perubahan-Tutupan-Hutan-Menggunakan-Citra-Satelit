<?php

namespace App\Helpers;

class GeoHelper
{
    /**
     * Definisi Region Observasi Hutan di Indonesia
     */
    public static function getRegions(): array
    {
        return [
            'ikn_kaltim' => [
                'id' => 'ikn_kaltim',
                'name' => 'Kawasan Nusantara (IKN & Sepaku, Kaltim)',
                'province' => 'Kalimantan Timur',
                'lat' => -0.9654,
                'lng' => 116.7321,
                'zoom' => 11,
                'bounds' => [
                    [-1.1500, 116.5500],
                    [-0.7800, 116.9500]
                ],
                'description' => 'Zona inti pembangunan IKN, Hutan Tanaman Industri (HTI), Hutan Lindung Sungai Wain, dan Taman Hutan Raya Bukit Soeharto.'
            ],
            'tesso_nilo_riau' => [
                'id' => 'tesso_nilo_riau',
                'name' => 'Lansekap Hutan Tesso Nilo, Riau',
                'province' => 'Riau',
                'lat' => -0.1500,
                'lng' => 101.6200,
                'zoom' => 11,
                'bounds' => [
                    [-0.3500, 101.4000],
                    [0.0500, 101.8500]
                ],
                'description' => 'Kawasan konservasi gajah sumatera yang mengalami dinamika deforestasi tinggi akibat konversi ke perkebunan kelapa sawit dan degradasi lahan gambut.'
            ],
            'leuser_aceh' => [
                'id' => 'leuser_aceh',
                'name' => 'Kawasan Hutan Lindung Leuser, Aceh',
                'province' => 'Aceh & Sumatera Utara',
                'lat' => 3.8500,
                'lng' => 97.5000,
                'zoom' => 11,
                'bounds' => [
                    [3.5000, 97.2000],
                    [4.2000, 97.8000]
                ],
                'description' => 'Salah satu bentang hutan hujan tropis terluas di Asia Tenggara dengan kanopi primer rapat dan cadangan karbon sangat tinggi.'
            ],
            'papua_barat' => [
                'id' => 'papua_barat',
                'name' => 'Hutan Hujan Tropis Sorong, Papua Barat Daya',
                'province' => 'Papua Barat Daya',
                'lat' => -0.8500,
                'lng' => 131.5000,
                'zoom' => 10,
                'bounds' => [
                    [-1.1000, 131.2000],
                    [-0.6000, 131.8000]
                ],
                'description' => 'Kawasan hutan alam perawan dengan keanekaragaman hayati mega-biodiversitas Papua yang sedang dipantau dari ancaman logging dan pembukaan koridor jalan.'
            ]
        ];
    }

    /**
     * Hitung luas poligon (array koordinat lat/lng) dalam Hektar menggunakan Spherical Excess
     */
    public static function calculatePolygonAreaHa(array $coordinates): float
    {
        if (count($coordinates) < 3) {
            return 0.0;
        }

        $radius = 6378137; // Jari-jari bumi dalam meter
        $area = 0.0;
        $totalPoints = count($coordinates);

        for ($i = 0; $i < $totalPoints; $i++) {
            $j = ($i + 1) % $totalPoints;
            $p1 = $coordinates[$i];
            $p2 = $coordinates[$j];

            $lat1 = deg2rad($p1[0] ?? $p1['lat']);
            $lat2 = deg2rad($p2[0] ?? $p2['lat']);
            $lng1 = deg2rad($p1[1] ?? $p1['lng']);
            $lng2 = deg2rad($p2[1] ?? $p2['lng']);

            $area += ($lng2 - $lng1) * (2 + sin($lat1) + sin($lat2));
        }

        $area = abs($area * $radius * $radius / 2.0);
        
        // Konversi dari meter persegi (m2) ke Hektar (1 Ha = 10,000 m2)
        return round($area / 10000.0, 2);
    }

    /**
     * Format angka Hektar dan persentase
     */
    public static function formatHa(float $value): string
    {
        return number_format($value, 1, ',', '.') . ' Ha';
    }

    public static function formatKm2(float $ha): string
    {
        return number_format($ha / 100, 2, ',', '.') . ' km²';
    }

    /**
     * Klasifikasi kategori NDVI
     */
    public static function classifyNdvi(float $ndvi): array
    {
        if ($ndvi < 0.0) {
            return ['label' => 'Badan Air / Awan / Salju', 'color' => '#3b82f6', 'code' => 'water'];
        } elseif ($ndvi < 0.2) {
            return ['label' => 'Lahan Terbuka / Batuan / Tambang', 'color' => '#d97706', 'code' => 'bare'];
        } elseif ($ndvi < 0.4) {
            return ['label' => 'Vegetasi Rendah / Semak Belukar', 'color' => '#84cc16', 'code' => 'shrub'];
        } elseif ($ndvi < 0.6) {
            return ['label' => 'Vegetasi Sedang / Perkebunan', 'color' => '#10b981', 'code' => 'plantation'];
        } else {
            return ['label' => 'Hutan Tropis Kerapatan Tinggi', 'color' => '#065f46', 'code' => 'dense_forest'];
        }
    }
}
