<?php
if (session_status() == PHP_SESSION_NONE) session_start();
$token = $token ?? '';
$files = [];
if (!empty($token) && isset($_SESSION['payroll_exports'][$token])) {
    $files = $_SESSION['payroll_exports'][$token]['files'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Unduh Slip Gaji - <?= htmlspecialchars($bulan_nama ?? '') ?> <?= htmlspecialchars($tahun ?? '') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h4>Slip Gaji Per Karyawan - <?= htmlspecialchars($bulan_nama) ?> <?= htmlspecialchars($tahun) ?></h4>
        <p>Pilih file untuk mengunduh slip gaji per karyawan. Link akan aktif selama sesi ini (atau hingga folder sementara dihapus oleh server).</p>

        <?php if (empty($files)): ?>
            <div class="alert alert-warning">Tidak ada file tersedia untuk diunduh.</div>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($files as $f): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fa-solid fa-file-pdf" style="margin-right:8px"></i>
                            <?= htmlspecialchars($f) ?>
                        </div>
                        <a href="index.php?page=penggajian&action=download_tmp&token=<?= urlencode($token) ?>&file=<?= urlencode($f) ?>" class="btn btn-sm btn-primary">Unduh</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="mt-3">
            <a href="index.php?page=penggajian&tahun=<?= urlencode($tahun) ?>&bulan=<?= urlencode($bulan) ?>&periode_ke=<?= urlencode($periode_ke) ?>" class="btn btn-outline-secondary">Kembali ke Penggajian</a>
        </div>
    </div>
</body>
</html>
