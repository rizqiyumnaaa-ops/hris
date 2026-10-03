<?php
/**
 * Migration: tambah kolom foto ke tabel users jika belum ada
 * Jalankan sekali: http://localhost/presensi/migrate_users_foto.php
 * Hapus file ini setelah selesai!
 */
require_once __DIR__ . '/config/database.php';
$db = Database::getInstance()->getConnection();

// Cek apakah kolom foto sudah ada
$stmt = $db->query("SHOW COLUMNS FROM users LIKE 'foto'");
if ($stmt->rowCount() === 0) {
    $db->exec("ALTER TABLE users ADD COLUMN foto VARCHAR(255) NULL DEFAULT NULL AFTER nama_lengkap");
    echo "<p style='color:green;font-family:sans-serif;'>✅ Kolom <strong>foto</strong> berhasil ditambahkan ke tabel <strong>users</strong>.</p>";
} else {
    echo "<p style='color:#dc2626;font-family:sans-serif;'>ℹ️ Kolom <strong>foto</strong> sudah ada, tidak perlu migrasi.</p>";
}

echo "<p style='font-family:sans-serif;color:red;'><strong>Hapus file ini setelah selesai!</strong></p>";
echo "<a href='index.php?page=profile' style='background:#dc2626;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-family:sans-serif;'>→ Buka Halaman Profil</a>";
?>
