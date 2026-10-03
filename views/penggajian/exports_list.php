<?php
// $files: array of ['name' => ..., 'url' => ...]
// $tahun, $bulan, $periode_ke, $bulan_nama available
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Daftar Export Slip Gaji - <?= htmlspecialchars($bulan_nama) ?> <?= htmlspecialchars($tahun) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h4>Export Slip Gaji - <?= htmlspecialchars($bulan_nama) ?> <?= htmlspecialchars($tahun) ?> (Periode <?= htmlspecialchars($periode_ke) ?>)</h4>
        <p>Berikut file yang tersimpan di folder <code>assets/exports/payroll_<?= $tahun ?>_<?= $bulan ?>_p<?= $periode_ke ?></code></p>

        <?php if (empty($files)): ?>
            <div class="alert alert-warning">Belum ada file export untuk periode ini.</div>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($files as $f): 
                    $fileName = is_array($f) ? ($f['name'] ?? '') : (string)$f;
                    $fileUrl  = is_array($f) ? ($f['url'] ?? '#') : 'assets/exports/payroll_' . $tahun . '_' . $bulan . '_p' . $periode_ke . '/' . $fileName;
                ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fa-solid fa-file-pdf" style="margin-right:8px"></i>
                            <?= htmlspecialchars($fileName) ?>
                        </div>
                        <a href="<?= htmlspecialchars($fileUrl) ?>" class="btn btn-sm btn-primary" target="_blank" download>Unduh</a>
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
