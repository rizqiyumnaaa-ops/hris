<?php
// Prevent browser from caching pages
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

/**
 * Index - Front Controller and Router
 */

// Initialize helpers
require_once __DIR__ . '/helpers/helpers.php';

// Route parser
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';

// Handle Authentication Middleware
// Pages that do NOT require authentication
$public_pages = ['login'];

if (!in_array($page, $public_pages)) {
    check_login();
} else {
    if ($page === 'login') {
        check_already_logged_in();
    }
}

// Pages only accessible by Admin Utama (full permissions)
$admin_utama_only_pages = ['karyawan', 'divisi', 'jabatan', 'piutang', 'potongan_lainnya', 'penggajian', 'admin'];

if (in_array($page, $admin_utama_only_pages)) {
    check_admin_utama_only();
}

// Pages accessible by both Admin Utama and Admin Divisi
$admin_shared_pages = ['laporan'];

if (in_array($page, $admin_shared_pages)) {
    check_admin();
    
    // Admin Divisi is restricted to 'absensi' and 'lembur' reports only
    if (is_admin_divisi()) {
        $action = $_GET['action'] ?? '';
        if (!in_array($action, ['absensi', 'lembur'])) {
            http_response_code(403);
            require_once __DIR__ . '/views/403.php';
            exit();
        }
    }
}

// Controller routing mapping
switch ($page) {
    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;
        
    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;
        
    case 'karyawan':
        require_once __DIR__ . '/controllers/KaryawanController.php';
        $controller = new KaryawanController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } elseif ($action === 'detail') {
            $controller->detail();
        } elseif ($action === 'toggle') {
            $controller->toggle();
        } else {
            $controller->index();
        }
        break;
        
    case 'divisi':
        require_once __DIR__ . '/controllers/DivisiController.php';
        $controller = new DivisiController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;
        
    case 'jabatan':
        require_once __DIR__ . '/controllers/JabatanController.php';
        $controller = new JabatanController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;
        
    case 'absensi':
        // Absensi: semua role bisa akses penuh
        require_once __DIR__ . '/controllers/AbsensiController.php';
        $controller = new AbsensiController();
        if ($action === 'save') {
            $controller->save();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;
        
    case 'lembur':
        // Lembur: admin bisa semua, pegawai bisa index + create + edit/hapus milik sendiri
        require_once __DIR__ . '/controllers/LemburController.php';
        $controller = new LemburController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;
        
    case 'piutang':
        require_once __DIR__ . '/controllers/PiutangController.php';
        $controller = new PiutangController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    case 'potongan_lainnya':
        require_once __DIR__ . '/controllers/PotonganLainnyaController.php';
        $controller = new PotonganLainnyaController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'toggle') {
            $controller->toggle();
        } elseif ($action === 'delete') {
            $controller->delete();
        } elseif ($action === 'save_karyawan') {
            $controller->save_karyawan();
        } else {
            $controller->index();
        }
        break;
        
    case 'penggajian':
        require_once __DIR__ . '/controllers/PenggajianController.php';
        $controller = new PenggajianController();
        if ($action === 'hitung') {
            $controller->hitung();
        } elseif ($action === 'simpan') {
            $controller->simpan();
        } elseif ($action === 'delete') {
            $controller->delete();
        } elseif ($action === 'slip') {
            $controller->slip();
        } elseif ($action === 'slip_all' || $action === 'slip_selected') {
            $controller->slip_all();
        } elseif ($action === 'zip_all' || $action === 'zip_selected') {
            $controller->zip_all();
        } elseif ($action === 'pdf_all' || $action === 'pdf_selected') {
            $controller->pdf_all();
        } elseif ($action === 'download_tmp') {
            $controller->download_tmp();
        } elseif ($action === 'excel') {
            $controller->excel();
        } else {
            $controller->index();
        }
        break;
        
    case 'laporan':
        require_once __DIR__ . '/controllers/LaporanController.php';
        $controller = new LaporanController();
        if ($action === 'absensi') {
            $controller->absensi();
        } elseif ($action === 'lembur') {
            $controller->lembur();
        } elseif ($action === 'piutang') {
            $controller->piutang();
        } elseif ($action === 'penggajian') {
            $controller->penggajian();
        } else {
            redirect('index.php?page=dashboard');
        }
        break;
        
    case 'profile':
        require_once __DIR__ . '/controllers/ProfileController.php';
        $controller = new ProfileController();
        if ($action === 'update') {
            $controller->update();
        } elseif ($action === 'update_photo') {
            $controller->updatePhoto();
        } elseif ($action === 'update_password') {
            $controller->updatePassword();
        } else {
            $controller->index();
        }
        break;

    case 'admin':
        require_once __DIR__ . '/controllers/ProfileController.php';
        $controller = new ProfileController();
        if ($action === 'create') {
            $controller->adminCreate();
        } elseif ($action === 'create_pegawai') {
            $controller->pegawaiCreate();
        } elseif ($action === 'delete') {
            $controller->adminDelete();
        } else {
            $controller->adminList();
        }
        break;

    default:
        redirect('index.php?page=dashboard');
        break;
}
