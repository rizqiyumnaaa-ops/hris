<?php
/**
 * Profile & Admin Management Controller
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Karyawan.php';
require_once __DIR__ . '/../models/Divisi.php';
require_once __DIR__ . '/../helpers/helpers.php';

class ProfileController {
    private $userModel;
    private $karyawanModel;
    private $divisiModel;

    public function __construct() {
        $this->userModel     = new User();
        $this->karyawanModel = new Karyawan();
        $this->divisiModel   = new Divisi();
    }

    // -------------------------
    // Profil User yang Login
    // -------------------------
    public function index() {
        $user = $this->userModel->getById($_SESSION['user_id']);
        require_once __DIR__ . '/../views/profile/index.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=profile');
        }

        $id       = $_SESSION['user_id'];
        $nama     = sanitize($_POST['nama_lengkap'] ?? '');
        $username = sanitize($_POST['username'] ?? '');

        if (empty($nama) || empty($username)) {
            set_flash_message('error', 'Gagal', 'Nama dan username wajib diisi.');
            redirect('index.php?page=profile');
        }

        if ($this->userModel->isUsernameTaken($username, $id)) {
            set_flash_message('error', 'Gagal', 'Username sudah digunakan oleh pengguna lain.');
            redirect('index.php?page=profile');
        }

        $this->userModel->updateProfile($id, [
            'username'     => $username,
            'nama_lengkap' => $nama,
        ]);

        // Update session
        $_SESSION['username']     = $username;
        $_SESSION['nama_lengkap'] = $nama;

        set_flash_message('success', 'Berhasil', 'Profil berhasil diperbarui.');
        redirect('index.php?page=profile');
    }

    public function updatePhoto() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=profile');
        }

        $id = $_SESSION['user_id'];

        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            set_flash_message('error', 'Gagal', 'File foto tidak valid atau tidak diunggah.');
            redirect('index.php?page=profile');
        }

        $upload_dir = __DIR__ . '/../assets/uploads/admins/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo   = finfo_open(FILEINFO_MIME_TYPE);
        $mime    = finfo_file($finfo, $_FILES['foto']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed)) {
            set_flash_message('error', 'Gagal', 'Format file harus JPG, PNG, WebP, atau GIF.');
            redirect('index.php?page=profile');
        }

        if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
            set_flash_message('error', 'Gagal', 'Ukuran foto maksimal 2 MB.');
            redirect('index.php?page=profile');
        }

        // Hapus foto lama jika ada
        $user      = $this->userModel->getById($id);
        $foto_lama = $user['foto'] ?? null;
        if ($foto_lama && file_exists(__DIR__ . '/../' . $foto_lama)) {
            unlink(__DIR__ . '/../' . $foto_lama);
        }

        $ext      = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = 'admin_' . $id . '_' . time() . '.' . $ext;
        $path     = $upload_dir . $filename;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $path)) {
            set_flash_message('error', 'Gagal', 'Gagal menyimpan file foto.');
            redirect('index.php?page=profile');
        }

        $this->userModel->updatePhoto($id, 'assets/uploads/admins/' . $filename);

        set_flash_message('success', 'Berhasil', 'Foto profil berhasil diperbarui.');
        redirect('index.php?page=profile');
    }

    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=profile');
        }

        $id           = $_SESSION['user_id'];
        $old_pass     = $_POST['password_lama'] ?? '';
        $new_pass     = $_POST['password_baru'] ?? '';
        $confirm_pass = $_POST['password_konfirmasi'] ?? '';

        $user = $this->userModel->getById($id);

        if (!password_verify($old_pass, $user['password'])) {
            set_flash_message('error', 'Gagal', 'Password lama tidak cocok.');
            redirect('index.php?page=profile');
        }

        if (strlen($new_pass) < 6) {
            set_flash_message('error', 'Gagal', 'Password baru minimal 6 karakter.');
            redirect('index.php?page=profile');
        }

        if ($new_pass !== $confirm_pass) {
            set_flash_message('error', 'Gagal', 'Konfirmasi password baru tidak cocok.');
            redirect('index.php?page=profile');
        }

        $this->userModel->updatePassword($id, $new_pass);

        set_flash_message('success', 'Berhasil', 'Password berhasil diperbarui. Silakan login kembali.');
        redirect('index.php?page=logout');
    }

    // -------------------------
    // Manajemen Akun (Admin)
    // -------------------------
    public function adminList() {
        $users    = $this->userModel->getAll();
        $karyawan = $this->karyawanModel->getAll(['status_aktif' => 1]);
        $divisi   = $this->divisiModel->getAll();
        require_once __DIR__ . '/../views/profile/admin_list.php';
    }

    public function adminCreate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin');
        }

        $username   = sanitize($_POST['username'] ?? '');
        $nama       = sanitize($_POST['nama_lengkap'] ?? '');
        $password   = $_POST['password'] ?? '';
        $konfirmasi = $_POST['password_konfirmasi'] ?? '';
        $role       = sanitize($_POST['role'] ?? 'admin_divisi');
        $id_divisi  = !empty($_POST['id_divisi']) ? (int)$_POST['id_divisi'] : null;

        if ($role === 'admin_utama') {
            $id_divisi = null;
        }

        if (empty($username) || empty($nama) || empty($password)) {
            set_flash_message('error', 'Gagal', 'Semua field wajib diisi.');
            redirect('index.php?page=admin');
        }

        if ($password !== $konfirmasi) {
            set_flash_message('error', 'Gagal', 'Konfirmasi password tidak cocok.');
            redirect('index.php?page=admin');
        }

        if (strlen($password) < 6) {
            set_flash_message('error', 'Gagal', 'Password minimal 6 karakter.');
            redirect('index.php?page=admin');
        }

        if ($this->userModel->isUsernameTaken($username)) {
            set_flash_message('error', 'Gagal', 'Username sudah digunakan.');
            redirect('index.php?page=admin');
        }

        $this->userModel->create([
            'username'     => $username,
            'nama_lengkap' => $nama,
            'password'     => $password,
            'foto'         => null,
            'role'         => $role,
            'id_divisi'    => $id_divisi,
            'karyawan_id'  => null,
        ]);

        set_flash_message('success', 'Berhasil', "Admin '$username' berhasil ditambahkan.");
        redirect('index.php?page=admin');
    }

    public function pegawaiCreate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin');
        }

        $karyawan_id = (int)($_POST['karyawan_id'] ?? 0);
        $username    = sanitize($_POST['username'] ?? '');
        $password    = $_POST['password'] ?? '';
        $konfirmasi  = $_POST['password_konfirmasi'] ?? '';

        if ($karyawan_id <= 0 || empty($username) || empty($password)) {
            set_flash_message('error', 'Gagal', 'Semua field wajib diisi.');
            redirect('index.php?page=admin');
        }

        if ($password !== $konfirmasi) {
            set_flash_message('error', 'Gagal', 'Konfirmasi password tidak cocok.');
            redirect('index.php?page=admin');
        }

        if (strlen($password) < 6) {
            set_flash_message('error', 'Gagal', 'Password minimal 6 karakter.');
            redirect('index.php?page=admin');
        }

        if ($this->userModel->isUsernameTaken($username)) {
            set_flash_message('error', 'Gagal', 'Username sudah digunakan.');
            redirect('index.php?page=admin');
        }

        if ($this->userModel->isKaryawanLinked($karyawan_id)) {
            set_flash_message('error', 'Gagal', 'Karyawan ini sudah memiliki akun login.');
            redirect('index.php?page=admin');
        }

        $karyawan = $this->karyawanModel->getById($karyawan_id);
        if (!$karyawan) {
            set_flash_message('error', 'Gagal', 'Karyawan tidak ditemukan.');
            redirect('index.php?page=admin');
        }

        $this->userModel->create([
            'username'     => $username,
            'nama_lengkap' => $karyawan['nama_lengkap'],
            'password'     => $password,
            'foto'         => null,
            'role'         => 'pegawai',
            'karyawan_id'  => $karyawan_id,
        ]);

        set_flash_message('success', 'Berhasil', "Akun pegawai untuk '{$karyawan['nama_lengkap']}' berhasil dibuat.");
        redirect('index.php?page=admin');
    }

    public function adminDelete() {
        $id = (int)($_GET['id'] ?? 0);

        if ($id === (int)$_SESSION['user_id']) {
            set_flash_message('error', 'Gagal', 'Kamu tidak bisa menghapus akun yang sedang aktif.');
            redirect('index.php?page=admin');
        }

        $this->userModel->delete($id);
        set_flash_message('success', 'Berhasil', 'Akun berhasil dihapus.');
        redirect('index.php?page=admin');
    }
}
