<?php

namespace App\Models;

use PDO;
use PDOException;
use App\Config\App;
use App\Helpers\Seeder;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                if (App::DB_DRIVER === 'sqlite') {
                    $dbFile = App::getSqlitePath();
                    $dbDir = dirname($dbFile);
                    if (!is_dir($dbDir)) {
                        mkdir($dbDir, 0777, true);
                    }

                    $firstTime = !file_exists($dbFile) || filesize($dbFile) === 0;

                    self::$instance = new PDO("sqlite:" . $dbFile);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                    // Ensure foreign keys & WAL mode for SQLite performance
                    self::$instance->exec("PRAGMA foreign_keys = ON;");
                    self::$instance->exec("PRAGMA journal_mode = WAL;");

                    self::createTables();

                    if ($firstTime) {
                        Seeder::seed(self::$instance);
                    }
                } else {
                    $dsn = sprintf(
                        "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
                        App::DB_HOST,
                        App::DB_PORT,
                        App::DB_NAME
                    );
                    self::$instance = new PDO($dsn, App::DB_USER, App::DB_PASS);
                    self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    self::createTables();
                }
            } catch (PDOException $e) {
                die("Database Connection Error: " . htmlspecialchars($e->getMessage()));
            }
        }

        return self::$instance;
    }

    private static function createTables(): void
    {
        $db = self::$instance;

        // Table: forest_statistics
        $db->exec("
            CREATE TABLE IF NOT EXISTS forest_statistics (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                region_id TEXT NOT NULL,
                region_name TEXT NOT NULL,
                year INTEGER NOT NULL,
                primary_forest_ha REAL DEFAULT 0,
                secondary_forest_ha REAL DEFAULT 0,
                plantation_ha REAL DEFAULT 0,
                shrub_ha REAL DEFAULT 0,
                bare_land_ha REAL DEFAULT 0,
                water_body_ha REAL DEFAULT 0,
                built_up_ha REAL DEFAULT 0,
                total_area_ha REAL DEFAULT 0,
                deforestation_rate_ha REAL DEFAULT 0,
                reforestation_ha REAL DEFAULT 0,
                avg_ndvi REAL DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

        // Table: deforestation_alerts
        $db->exec("
            CREATE TABLE IF NOT EXISTS deforestation_alerts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                alert_code TEXT UNIQUE NOT NULL,
                region_id TEXT NOT NULL,
                latitude REAL NOT NULL,
                longitude REAL NOT NULL,
                confidence TEXT NOT NULL,
                detection_date DATE NOT NULL,
                area_loss_ha REAL NOT NULL,
                threat_type TEXT NOT NULL,
                verification_status TEXT NOT NULL,
                reporter_name TEXT NOT NULL,
                notes TEXT,
                photo_url TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

        // Table: satellite_observations
        $db->exec("
            CREATE TABLE IF NOT EXISTS satellite_observations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                satellite_name TEXT NOT NULL,
                sensor TEXT NOT NULL,
                acquisition_date DATE NOT NULL,
                cloud_cover_percent REAL NOT NULL,
                region_name TEXT NOT NULL,
                resolution_meters REAL NOT NULL,
                avg_ndvi REAL NOT NULL,
                status TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }
}
