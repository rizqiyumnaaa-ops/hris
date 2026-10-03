<?php
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

try {
    // Disable foreign key checks
    $db->exec('SET FOREIGN_KEY_CHECKS = 0');

    // Truncate tables
    $db->exec('TRUNCATE TABLE absensi');
    $db->exec('TRUNCATE TABLE lembur');
    $db->exec('TRUNCATE TABLE piutang');
    $db->exec('TRUNCATE TABLE penggajian');
    
    // Set user's karyawan_id to NULL
    $db->exec('UPDATE users SET karyawan_id = NULL');
    
    $db->exec('TRUNCATE TABLE karyawan');
    
    // Enable foreign key checks
    $db->exec('SET FOREIGN_KEY_CHECKS = 1');
    
    // Get available divisi and jabatan
    $stmtDivisi = $db->query('SELECT id FROM divisi');
    $divisiList = $stmtDivisi->fetchAll(PDO::FETCH_COLUMN);
    
    $stmtJabatan = $db->query('SELECT id FROM jabatan');
    $jabatanList = $stmtJabatan->fetchAll(PDO::FETCH_COLUMN);
    
    if(empty($divisiList) || empty($jabatanList)) {
        die("Error: Divisi or Jabatan tables are empty. Please seed them first.\n");
    }

    $db->beginTransaction();
    
    // Insert 50 employees
    $firstNames = ['Budi', 'Andi', 'Siti', 'Ani', 'Ahmad', 'Rina', 'Dedi', 'Eka', 'Agus', 'Sri', 'Fajar', 'Indah', 'Joko', 'Kartika', 'Rizal', 'Putri', 'Mita', 'Doni'];
    $lastNames = ['Santoso', 'Wijaya', 'Kusuma', 'Pratama', 'Hidayat', 'Putri', 'Sari', 'Wahyudi', 'Lestari', 'Nugroho', 'Saputra', 'Gani'];
    $statuses = ['Tetap', 'Kontrak', 'Magang'];
    
    $stmtInsert = $db->prepare("
        INSERT INTO karyawan (
            nik, nama_lengkap, id_jabatan, id_divisi, no_hp, alamat, 
            tanggal_masuk, status_kerja, status_aktif, gaji_pokok, tarif_lembur
        ) VALUES (
            :nik, :nama_lengkap, :id_jabatan, :id_divisi, :no_hp, :alamat, 
            :tanggal_masuk, :status_kerja, 1, :gaji_pokok, :tarif_lembur
        )
    ");
    
    for ($i = 1; $i <= 50; $i++) {
        $nik = '100' . str_pad($i, 3, '0', STR_PAD_LEFT);
        $nama = $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
        $id_jabatan = $jabatanList[array_rand($jabatanList)];
        $id_divisi = $divisiList[array_rand($divisiList)];
        $no_hp = '0812' . rand(10000000, 99999999);
        $alamat = 'Jalan Merdeka No. ' . rand(1, 100) . ', Jakarta';
        $tanggal_masuk = date('Y-m-d', strtotime('-' . rand(100, 1000) . ' days'));
        $status_kerja = $statuses[array_rand($statuses)];
        $gaji_pokok = rand(10, 30) * 10000; // 100.000 to 300.000 (Daily Wage)
        $tarif_lembur = rand(2, 5) * 10000;   // 20.000 to 50.000
        
        $stmtInsert->execute([
            'nik' => $nik,
            'nama_lengkap' => $nama,
            'id_jabatan' => $id_jabatan,
            'id_divisi' => $id_divisi,
            'no_hp' => $no_hp,
            'alamat' => $alamat,
            'tanggal_masuk' => $tanggal_masuk,
            'status_kerja' => $status_kerja,
            'gaji_pokok' => $gaji_pokok,
            'tarif_lembur' => $tarif_lembur
        ]);
    }
    
    $db->commit();
    echo "Successfully generated 50 employees.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if ($db->inTransaction()) {
        $db->rollBack();
    }
}
