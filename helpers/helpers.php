<?php
/**
 * Helper Functions for Sistem Presensi & Penggajian Karyawan
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect helper
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Security sanitization helper
function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
        return $data;
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Check if user is logged in (middleware)
function check_login() {
    if (!isset($_SESSION['user_id'])) {
        set_flash_message('warning', 'Akses Ditolak', 'Silakan login terlebih dahulu untuk mengakses halaman.');
        redirect('index.php?page=login');
    }
}

// Check if user is already logged in (redirect from login page)
function check_already_logged_in() {
    if (isset($_SESSION['user_id'])) {
        redirect('index.php?page=dashboard');
    }
}

// Get current user role from session
function get_current_role() {
    return $_SESSION['role'] ?? 'pegawai';
}

// Check if current logged-in user is Admin Utama (has full permissions)
function is_admin_utama() {
    return get_current_role() === 'admin_utama';
}

// Check if current logged-in user is Admin Divisi (has division-restricted permissions)
function is_admin_divisi() {
    return get_current_role() === 'admin_divisi';
}

// Check if current logged-in user is admin (either admin_utama or admin_divisi)
function is_admin() {
    $role = get_current_role();
    return $role === 'admin_utama' || $role === 'admin_divisi';
}

// Middleware: abort with 403 if not admin (either admin_utama or admin_divisi)
function check_admin() {
    if (!is_admin()) {
        http_response_code(403);
        require_once __DIR__ . '/../views/403.php';
        exit();
    }
}

// Middleware: abort with 403 if not admin_utama (only Admin Utama allowed)
function check_admin_utama_only() {
    check_login();
    if (!is_admin_utama()) {
        http_response_code(403);
        require_once __DIR__ . '/../views/403.php';
        exit();
    }
}

// Get karyawan_id linked to current logged-in pegawai
function get_session_karyawan_id() {
    return $_SESSION['karyawan_id'] ?? null;
}

// Format number to Rupiah currency (handles negative numbers gracefully)
function format_rupiah($number) {
    if ($number < 0) {
        return '-Rp' . number_format(abs($number), 0, ',', '.');
    }
    return 'Rp' . number_format($number, 0, ',', '.');
}

// Format SQL date to Indonesian date format (e.g. 15 Juli 2026)
function format_tanggal($date) {
    if (empty($date)) return '-';
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $timestamp = strtotime($date);
    $d = date('j', $timestamp);
    $m = $months[(int)date('n', $timestamp)];
    $y = date('Y', $timestamp);
    return "$d $m $y";
}

// Format month name only
function format_bulan($month_num) {
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return $months[(int)$month_num] ?? '';
}

// Count working days in range (all calendar days included)
function get_working_days($start_date, $end_date) {
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $diff = $start->diff($end);
    return $diff->days + 1; // inclusive start and end date
}

// Set flash message
function set_flash_message($type, $title, $text) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'title' => $title,
        'text' => $text
    ];
}

// Display flash message (Toast or SweetAlert2)
function get_flash_message() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Helper to get active menu class
function is_active_page($pages) {
    $current_page = $_GET['page'] ?? 'dashboard';
    if (is_array($pages)) {
        return in_array($current_page, $pages) ? 'active' : '';
    }
    return $current_page === $pages ? 'active' : '';
}

// Photo Upload validation and handler
function upload_foto($file, $nik) {
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    $filename = $file['name'];
    $file_size = $file['size'];
    $tmp_name = $file['tmp_name'];
    $error = $file['error'];
    
    if ($error !== UPLOAD_ERR_OK) {
        return ['status' => false, 'message' => 'Gagal mengupload gambar.'];
    }
    
    if ($file_size > $max_size) {
        return ['status' => false, 'message' => 'Ukuran foto maksimal 2MB.'];
    }
    
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_extensions)) {
        return ['status' => false, 'message' => 'Hanya file JPG, JPEG, dan PNG yang diperbolehkan.'];
    }
    
    $target_dir = dirname(__DIR__) . '/assets/uploads/foto_karyawan/';
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    // Generate new unique name based on NIK and timestamp
    $new_filename = $nik . '_' . time() . '.' . $ext;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($tmp_name, $target_file)) {
        return ['status' => true, 'filename' => $new_filename];
    } else {
        return ['status' => false, 'message' => 'Gagal memindahkan file ke direktori tujuan.'];
    }
}
