<?php
/**
 * Auth Controller
 */

require_once dirname(__DIR__) . '/models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize inputs
            $username = sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                set_flash_message('error', 'Login Gagal', 'Username dan password wajib diisi.');
                redirect('index.php?page=login');
            }

            // Fetch user
            $user = $this->userModel->getByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']      = $user['id'];
                $_SESSION['username']     = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role']         = $user['role'] ?? 'admin_utama';
                $_SESSION['id_divisi']    = $user['id_divisi'] ?? null;
                $_SESSION['karyawan_id']  = $user['karyawan_id'] ?? null;

                set_flash_message('success', 'Login Berhasil', 'Selamat datang kembali, ' . $user['nama_lengkap'] . '!');
                redirect('index.php?page=dashboard');
            } else {
                set_flash_message('error', 'Login Gagal', 'Username atau password salah.');
                redirect('index.php?page=login');
            }
        } else {
            // Render login view
            require_once dirname(__DIR__) . '/views/login.php';
        }
    }

    public function logout() {
        // Clear session variables
        $_SESSION = [];
        
        // Destroy session cookies if any
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy session
        session_destroy();
        
        // Start a temporary session to pass the logout notification
        session_start();
        set_flash_message('success', 'Logout Berhasil', 'Anda telah keluar dari sistem.');
        redirect('index.php?page=login');
    }
}
