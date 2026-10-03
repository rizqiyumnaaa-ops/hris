<?php
/**
 * Script perbaikan password admin - Jalankan sekali lalu hapus file ini!
 */
require_once __DIR__ . '/config/database.php';

$db = Database::getInstance()->getConnection();

// Generate hash baru untuk password 'admin'
$new_password = 'admin';
$hash = password_hash($new_password, PASSWORD_DEFAULT);

// Cek apakah user admin sudah ada
$stmt = $db->prepare("SELECT id FROM users WHERE username = 'admin'");
$stmt->execute();
$user = $stmt->fetch();

if ($user) {
    // Update password
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$hash]);
    echo "<h2 style='color:green;'>✅ Password berhasil diperbaiki!</h2>";
    echo "<p>Username: <strong>admin</strong></p>";
    echo "<p>Password: <strong>admin</strong></p>";
    echo "<p style='color:red;'><strong>PENTING: Segera hapus file fix_password.php setelah ini!</strong></p>";
    echo "<a href='index.php?page=login' style='background:#dc2626;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;'>→ Pergi ke Halaman Login</a>";
} else {
    // Insert user baru
    $stmt = $db->prepare("INSERT INTO users (username, password, nama_lengkap) VALUES ('admin', ?, 'Administrator HRIS')");
    $stmt->execute([$hash]);
    echo "<h2 style='color:green;'>✅ Admin berhasil dibuat!</h2>";
    echo "<p>Username: <strong>admin</strong></p>";
    echo "<p>Password: <strong>admin</strong></p>";
    echo "<p style='color:red;'><strong>PENTING: Segera hapus file fix_password.php setelah ini!</strong></p>";
    echo "<a href='index.php?page=login' style='background:#dc2626;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;'>→ Pergi ke Halaman Login</a>";
}
?>
