<?php
/**
 * 403 - Akses Ditolak
 */

require_once __DIR__ . '/../helpers/helpers.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | HRIS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #0f1117;
            color: #e5e7eb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card {
            background: #1a1d27;
            border: 1px solid rgba(220,38,38,0.2);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            text-align: center;
            max-width: 460px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .error-icon {
            width: 90px;
            height: 90px;
            background: rgba(220,38,38,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            border: 2px solid rgba(220,38,38,0.2);
        }
        .error-code {
            font-size: 4rem;
            font-weight: 700;
            color: #dc2626;
            line-height: 1;
        }
        .error-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin: 0.5rem 0;
        }
        .error-desc {
            color: #9ca3af;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }
        .btn-back {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        .btn-back:hover {
            background: linear-gradient(135deg, #ef4444, #f87171);
            color: white;
            transform: translateY(-1px);
        }
        .role-badge {
            display: inline-block;
            background: rgba(56,142,60,0.15);
            color: #4caf50;
            border: 1px solid rgba(56,142,60,0.3);
            border-radius: 20px;
            padding: 3px 12px;
            font-size: 0.78rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="fa-solid fa-shield-halved fa-2x" style="color:#dc2626;"></i>
        </div>
        <div class="error-code">403</div>
        <h1 class="error-title">Akses Ditolak</h1>
        <?php if (!is_admin()): ?>
        <div class="role-badge">
            <i class="fa-solid fa-id-card me-1"></i>Anda Login Sebagai Pegawai
        </div>
        <?php endif; ?>
        <p class="error-desc">
            Anda tidak memiliki izin untuk mengakses halaman ini.<br>
            Fitur ini hanya tersedia untuk <strong>Administrator</strong>.
        </p>
        <a href="index.php?page=dashboard" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
