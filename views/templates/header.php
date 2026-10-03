<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Presensi & Penggajian Karyawan</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Select2 & Theme -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="assets/css/style.css?v=<?= time() ?>" rel="stylesheet">
    
    <!-- Inline Professional Glassmorphism Theme Overrides -->
    <style>
    body {
        background-color: #f4f6f9 !important;
        background-image: url('assets/images/bg.png') !important;
        background-size: cover !important;
        background-position: center !important;
        background-attachment: fixed !important;
        background-repeat: no-repeat !important;
    }

    body::before {
        content: "";
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        z-index: -1;
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        background: rgba(255, 255, 255, 0.1);
    }

    #sidebar {
        background: rgba(255, 255, 255, 0.6) !important;
        backdrop-filter: blur(25px) saturate(200%) !important;
        -webkit-backdrop-filter: blur(25px) saturate(200%) !important;
        border-right: 1px solid rgba(255, 255, 255, 0.9) !important;
        box-shadow: 4px 0 24px 0 rgba(0, 0, 0, 0.02) !important;
    }

    .main-navbar {
        background: rgba(255, 255, 255, 0.65) !important;
        backdrop-filter: blur(20px) saturate(200%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(200%) !important;
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        border-radius: 16px !important;
        margin: 20px 30px 10px 30px !important;
        box-shadow: 0 8px 32px 0 rgba(15, 23, 42, 0.04) !important;
    }

    .card-premium, .card {
        background-color: rgba(255, 255, 255, 0.75) !important;
        backdrop-filter: blur(20px) saturate(200%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(200%) !important;
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        box-shadow: 0 8px 32px 0 rgba(15, 23, 42, 0.04) !important;
        border-radius: 16px !important;
    }

    .card-stat {
        border: 1px solid rgba(255, 255, 255, 0.8) !important;
        background: rgba(255, 255, 255, 0.75) !important;
        backdrop-filter: blur(20px) saturate(200%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(200%) !important;
        box-shadow: 0 8px 32px 0 rgba(15, 23, 42, 0.04) !important;
        border-radius: 16px !important;
    }

    .form-control, .form-select {
        border: 1px solid rgba(15, 23, 42, 0.1) !important;
        background-color: rgba(255, 255, 255, 0.7) !important;
        border-radius: 8px !important;
        box-shadow: none !important;
    }

    .form-control:focus, .form-select:focus {
        background-color: #ffffff !important;
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
    }

    .text-accent-primary {
        color: #dc2626 !important;
    }
    .bg-accent-soft {
        background-color: rgba(220, 38, 38, 0.08) !important;
    }
    .badge.bg-accent-soft {
        border: 1px solid transparent !important;
        box-shadow: none !important;
    }
    </style>
</head>
<body>

    <!-- Loading Spinner Overlay -->
    <div id="loader-overlay">
        <div class="spinner-custom"></div>
    </div>

    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <a href="index.php?page=dashboard" class="sidebar-logo">
                    <img src="assets/images/logo.png" alt="Logo HRIS"
                         style="height:36px;width:36px;object-fit:cover;border-radius:8px;flex-shrink:0;">
                    <span>HRIS</span>
                </a>
            </div>

            <ul class="list-unstyled components">
                <li class="<?= is_active_page('dashboard') ?>">
                    <a href="index.php?page=dashboard">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <?php if (is_admin_utama()): ?>
                <p>Master Data</p>
                <li class="<?= is_active_page('divisi') ?>">
                    <a href="index.php?page=divisi">
                        <i class="fa-solid fa-building"></i>
                        <span>Data Divisi</span>
                    </a>
                </li>
                <li class="<?= is_active_page('jabatan') ?>">
                    <a href="index.php?page=jabatan">
                        <i class="fa-solid fa-briefcase"></i>
                        <span>Data Jabatan</span>
                    </a>
                </li>
                <li class="<?= is_active_page('karyawan') ?>">
                    <a href="index.php?page=karyawan">
                        <i class="fa-solid fa-users"></i>
                        <span>Data Karyawan</span>
                    </a>
                </li>
                <?php endif; ?>

                <p>Transaksi</p>
                <li class="<?= is_active_page('absensi') ?>">
                    <a href="index.php?page=absensi">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Absensi Harian</span>
                    </a>
                </li>
                <li class="<?= is_active_page('lembur') ?>">
                    <a href="index.php?page=lembur">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Data Lembur</span>
                    </a>
                </li>
                <?php if (is_admin_utama()): ?>
                <li class="<?= is_active_page('piutang') ?>">
                    <a href="index.php?page=piutang">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                        <span>Piutang Karyawan</span>
                    </a>
                </li>

                <p>Keuangan</p>
                <li class="<?= is_active_page('penggajian') ?>">
                    <a href="index.php?page=penggajian">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span>Penggajian (Payroll)</span>
                    </a>
                </li>
                <li class="<?= is_active_page('potongan_lainnya') ?>">
                    <a href="index.php?page=potongan_lainnya">
                        <i class="fa-solid fa-scissors"></i>
                        <span>Potongan Lainnya</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (is_admin()): ?>
                <p>Laporan</p>
                <li class="<?= is_active_page('laporan') && isset($_GET['action']) && $_GET['action'] == 'absensi' ? 'active' : '' ?>">
                    <a href="index.php?page=laporan&action=absensi">
                        <i class="fa-regular fa-file-lines"></i>
                        <span>Laporan Absensi</span>
                    </a>
                </li>
                <li class="<?= is_active_page('laporan') && isset($_GET['action']) && $_GET['action'] == 'lembur' ? 'active' : '' ?>">
                    <a href="index.php?page=laporan&action=lembur">
                        <i class="fa-regular fa-file-lines"></i>
                        <span>Laporan Lembur</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (is_admin_utama()): ?>
                <li class="<?= is_active_page('laporan') && isset($_GET['action']) && $_GET['action'] == 'piutang' ? 'active' : '' ?>">
                    <a href="index.php?page=laporan&action=piutang">
                        <i class="fa-regular fa-file-lines"></i>
                        <span>Laporan Piutang</span>
                    </a>
                </li>
                <li class="<?= is_active_page('laporan') && isset($_GET['action']) && $_GET['action'] == 'penggajian' ? 'active' : '' ?>">
                    <a href="index.php?page=laporan&action=penggajian">
                        <i class="fa-regular fa-file-lines"></i>
                        <span>Laporan Gaji</span>
                    </a>
                </li>
                <?php endif; ?>

                <p>Pengaturan</p>
                <li class="<?= is_active_page('profile') ?>">
                    <a href="index.php?page=profile">
                        <i class="fa-solid fa-circle-user"></i>
                        <span>Profil Saya</span>
                    </a>
                </li>
                <?php if (is_admin_utama()): ?>
                <li class="<?= is_active_page('admin') ?>">
                    <a href="index.php?page=admin">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>Kelola Akun</span>
                    </a>
                </li>
                <?php endif; ?>

                <li class="mt-2">
                    <a href="#" id="btn-logout" class="text-danger">
                        <i class="fa-solid fa-right-from-bracket text-danger"></i>
                        <span>Keluar</span>
                    </a>
                </li>
            </ul>
        </nav>


        <!-- Page Content -->
        <div id="content">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg main-navbar">
                <div class="container-fluid p-0">
                    <button type="button" id="sidebarCollapse" class="btn btn-outline-accent">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center gap-3">
                        <?php
                            if (is_admin_utama()) {
                                $role_label = 'Admin Utama';
                                $role_color = 'var(--accent-primary)';
                                $role_bg    = 'var(--accent-soft)';
                            } elseif (is_admin_divisi()) {
                                require_once __DIR__ . '/../../models/Divisi.php';
                                $divModel = new Divisi();
                                $myDiv = $divModel->getById($_SESSION['id_divisi']);
                                $role_label = 'Admin Divisi ' . ($myDiv['nama_divisi'] ?? '');
                                $role_color = '#d35400';
                                $role_bg    = '#fdebd0';
                            } else {
                                $role_label = 'Pegawai';
                                $role_color = '#388e3c';
                                $role_bg    = '#e8f5e9';
                            }
                        ?>
                        <span class="badge d-none d-sm-inline-flex align-items-center gap-1"
                              style="background:<?= $role_bg ?>;color:<?= $role_color ?>;font-size:0.75rem;padding:5px 10px;border-radius:20px;font-weight:600;">
                            <i class="fa-solid <?= is_admin_utama() ? 'fa-shield-halved' : (is_admin_divisi() ? 'fa-user-shield' : 'fa-id-card') ?>" style="font-size:0.7rem;"></i>
                            <?= $role_label ?>
                        </span>
                        <a href="index.php?page=profile" class="text-decoration-none d-flex align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <span class="text-muted d-none d-sm-inline">Halo, <strong><?= $_SESSION['nama_lengkap'] ?? 'Admin' ?></strong></span>
                                <?php
                                    // Cek apakah admin punya foto
                                    require_once __DIR__ . '/../../models/User.php';
                                    $userModelNav = new User();
                                    $currentUserNav = $userModelNav->getById($_SESSION['user_id']);
                                ?>
                                <?php if (!empty($currentUserNav['foto'])): ?>
                                    <img src="<?= htmlspecialchars($currentUserNav['foto']) ?>" alt="foto"
                                         class="rounded-circle" style="width:40px;height:40px;object-fit:cover;border:2px solid var(--accent-border);">
                                <?php else: ?>
                                    <div class="avatar d-flex align-items-center justify-content-center rounded-circle"
                                         style="width:40px;height:40px;background:var(--accent-soft);color:var(--accent-primary);font-weight:bold;">
                                        <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                </div>
            </nav>
            
            <!-- Dynamic Content Area -->
            <div class="page-content">
