<?php
/**
 * Configuration for Database Connection
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'kazajawa_hris');
define('DB_PASS', 'hrisKazajawara123_');
define('DB_NAME', 'kazajawa_app_hris');

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // Coba koneksi menggunakan kredensial di config
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
            if (isset($_GET['run_migration'])) {
                $this->runRoleMigration();
            }
        } catch (PDOException $e) {
            // Jika akses ditolak (1045), coba fallback ke 'root' tanpa password (default XAMPP lokal)
            if ($e->getCode() == 1045 || strpos($e->getMessage(), 'Access denied') !== false) {
                try {
                    $this->conn = new PDO($dsn, 'root', '', $options);
                    if (isset($_GET['run_migration'])) {
                        $this->runRoleMigration();
                    }
                    return; // Berhasil tersambung
                } catch (PDOException $fallbackEx) {
                    // Jika gagal juga, lanjutkan ke error handling di bawah
                }
            }

            // Jika database belum ada, coba buat database otomatis
            try {
                $dsn_no_db = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
                
                // Coba buat koneksi tanpa nama DB menggunakan kredensial config
                try {
                    $temp_conn = new PDO($dsn_no_db, DB_USER, DB_PASS, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]);
                } catch (PDOException $ex_no_db) {
                    // Fallback ke root jika gagal
                    $temp_conn = new PDO($dsn_no_db, 'root', '', [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]);
                }
                
                $temp_conn->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
                
                // Coba hubungkan kembali ke database baru
                try {
                    $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
                } catch (PDOException $ex_reconnect) {
                    $this->conn = new PDO($dsn, 'root', '', $options);
                }
                
                // Jalankan inisialisasi tabel jika database masih kosong
                $stmt = $this->conn->query("SHOW TABLES");
                if ($stmt->rowCount() == 0) {
                    $sql_file = dirname(__DIR__) . '/database/schema.sql';
                    if (file_exists($sql_file)) {
                        $sql = file_get_contents($sql_file);
                        $this->conn->exec($sql);
                    }
                }
                
                if (isset($_GET['run_migration'])) {
                    $this->runRoleMigration();
                }
            } catch (PDOException $ex) {
                die("Koneksi Database Gagal: " . $ex->getMessage() . "<br><br>Pastikan server MySQL (XAMPP) sudah aktif.");
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    /**
     * Auto-migration: Tambah kolom role & karyawan_id ke tabel users jika belum ada.
     * Dijalankan otomatis setiap koneksi — aman dijalankan berulang kali.
     */
    private function runRoleMigration() {
        try {
            // 1. Cek apakah kolom 'role' sudah ada
            $stmt = $this->conn->query("SHOW COLUMNS FROM `users` LIKE 'role'");
            if ($stmt->rowCount() === 0) {
                // Tambah kolom foto jika belum ada
                $this->conn->exec("ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `foto` VARCHAR(255) DEFAULT NULL AFTER `nama_lengkap`");
                // Tambah kolom role
                $this->conn->exec("ALTER TABLE `users` ADD COLUMN `role` ENUM('admin','pegawai') NOT NULL DEFAULT 'admin' AFTER `foto`");
                // Tambah kolom karyawan_id
                $this->conn->exec("ALTER TABLE `users` ADD COLUMN `karyawan_id` INT DEFAULT NULL AFTER `role`");
                // Pastikan semua existing user jadi admin
                $this->conn->exec("UPDATE `users` SET `role` = 'admin' WHERE `role` IS NULL OR `role` = ''");
            }

            // 2. Migrasi ke RBAC Baru (admin_utama, admin_divisi, pegawai)
            $stmtType = $this->conn->query("SHOW COLUMNS FROM `users` LIKE 'role'");
            $columnInfo = $stmtType->fetch();
            if ($columnInfo && strpos($columnInfo['Type'], 'admin_utama') === false) {
                // Ubah tipe ENUM agar menampung role baru
                $this->conn->exec("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin_utama', 'admin_divisi', 'pegawai', 'admin') NOT NULL DEFAULT 'admin_divisi'");
                // Migrasikan role 'admin' lama menjadi 'admin_utama'
                $this->conn->exec("UPDATE `users` SET `role` = 'admin_utama' WHERE `role` = 'admin'");
                // Ubah lagi enum untuk membuang 'admin' lama
                $this->conn->exec("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin_utama', 'admin_divisi', 'pegawai') NOT NULL DEFAULT 'admin_divisi'");
            }

            // 3. Tambahkan kolom 'id_divisi' pada users jika belum ada
            $stmtDiv = $this->conn->query("SHOW COLUMNS FROM `users` LIKE 'id_divisi'");
            if ($stmtDiv->rowCount() === 0) {
                $this->conn->exec("ALTER TABLE `users` ADD COLUMN `id_divisi` INT DEFAULT NULL AFTER `role`");
                $this->conn->exec("ALTER TABLE `users` ADD CONSTRAINT `fk_users_divisi` FOREIGN KEY (`id_divisi`) REFERENCES `divisi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Jika tabel users belum ada sama sekali, abaikan
        }
    }
}
