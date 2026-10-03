-- Database Schema for Sistem Presensi & Penggajian Karyawan
-- Create database if not exists
CREATE DATABASE IF NOT EXISTS `db_presensi` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_presensi`;

-- Table users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('admin_utama', 'admin_divisi', 'pegawai') NOT NULL DEFAULT 'admin_divisi',
  `id_divisi` INT DEFAULT NULL,
  `karyawan_id` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_divisi`) REFERENCES `divisi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY (`karyawan_id`) REFERENCES `karyawan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table divisi
CREATE TABLE IF NOT EXISTS `divisi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_divisi` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table jabatan
CREATE TABLE IF NOT EXISTS `jabatan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_jabatan` VARCHAR(100) NOT NULL UNIQUE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table karyawan
CREATE TABLE IF NOT EXISTS `karyawan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nik` VARCHAR(20) NOT NULL UNIQUE,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `id_jabatan` INT NOT NULL,
  `id_divisi` INT NOT NULL,
  `no_hp` VARCHAR(20) NOT NULL,
  `alamat` TEXT NOT NULL,
  `tanggal_masuk` DATE NOT NULL,
  `status_kerja` ENUM('Tetap', 'Kontrak', 'Magang') NOT NULL,
  `status_aktif` TINYINT(1) DEFAULT 1,
  `gaji_pokok` DECIMAL(15,2) NOT NULL,
  `tarif_lembur` DECIMAL(15,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_jabatan`) REFERENCES `jabatan` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  FOREIGN KEY (`id_divisi`) REFERENCES `divisi` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table absensi
CREATE TABLE IF NOT EXISTS `absensi` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_karyawan` INT NOT NULL,
  `tanggal` DATE NOT NULL,
  `status_absensi` ENUM('Hadir', 'Setengah Hari', 'Izin', 'Sakit', 'Alpha') NOT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `jam_absen` TIME NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `karyawan_tanggal` (`id_karyawan`, `tanggal`),
  FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table lembur
CREATE TABLE IF NOT EXISTS `lembur` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_karyawan` INT NOT NULL,
  `tanggal` DATE NOT NULL,
  `jumlah_jam` DECIMAL(5,2) NOT NULL,
  `tarif_lembur` DECIMAL(15,2) NOT NULL,
  `total_lembur` DECIMAL(15,2) NOT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table piutang
CREATE TABLE IF NOT EXISTS `piutang` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_karyawan` INT NOT NULL,
  `tanggal` DATE NOT NULL,
  `nominal` DECIMAL(15,2) NOT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `status_piutang` ENUM('Belum Lunas', 'Lunas') DEFAULT 'Belum Lunas',
  `id_penggajian` INT DEFAULT NULL, -- will link to penggajian if settled in that payroll
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table penggajian
CREATE TABLE IF NOT EXISTS `penggajian` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_karyawan` INT NOT NULL,
  `periode_tahun` INT NOT NULL,
  `periode_bulan` INT NOT NULL,
  `periode_ke` TINYINT NOT NULL, -- 1 atau 2
  `tanggal_payroll` DATE NOT NULL,
  `gaji_pokok` DECIMAL(15,2) NOT NULL,
  `gaji_harian` DECIMAL(15,2) NOT NULL,
  `jumlah_hari_kerja` INT NOT NULL,
  `total_hadir` DECIMAL(5,2) NOT NULL,
  `total_setengah_hari` INT NOT NULL,
  `total_izin` INT NOT NULL,
  `total_sakit` INT NOT NULL,
  `total_alpha` INT NOT NULL,
  `potongan_setengah_hari` DECIMAL(15,2) NOT NULL,
  `potongan_alpha` DECIMAL(15,2) NOT NULL,
  `total_lembur` DECIMAL(15,2) NOT NULL,
  `total_piutang` DECIMAL(15,2) NOT NULL,
  `gaji_bersih` DECIMAL(15,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `karyawan_periode` (`id_karyawan`, `periode_tahun`, `periode_bulan`, `periode_ke`),
  FOREIGN KEY (`id_karyawan`) REFERENCES `karyawan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add foreign key constraint to piutang once penggajian table is defined
ALTER TABLE `piutang` ADD CONSTRAINT `fk_piutang_penggajian` FOREIGN KEY (`id_penggajian`) REFERENCES `penggajian` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Seed default admin (username: admin, password: admin)
-- Hashed value for 'admin': $2y$10$Y5n2lE05Z1f8P5o4p/jLye8K23Lh/P7fBuxNveE5C9L5o711.gZ5m
INSERT INTO `users` (`username`, `password`, `nama_lengkap`)
VALUES ('admin', '$2y$10$Y5n2lE05Z1f8P5o4p/jLye8K23Lh/P7fBuxNveE5C9L5o711.gZ5m', 'Administrator HRIS')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- Seed default divisi
INSERT INTO `divisi` (`nama_divisi`) VALUES ('IT'), ('HRD'), ('Finance'), ('Marketing'), ('Operational')
ON DUPLICATE KEY UPDATE `nama_divisi`=`nama_divisi`;

-- Seed default jabatan
INSERT INTO `jabatan` (`nama_jabatan`) VALUES ('Manager'), ('Supervisor'), ('Senior Staff'), ('Staff'), ('Intern')
ON DUPLICATE KEY UPDATE `nama_jabatan`=`nama_jabatan`;
