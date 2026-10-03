<?php
/**
 * Lembur Controller
 */

require_once dirname(__DIR__) . '/models/Lembur.php';
require_once dirname(__DIR__) . '/models/Karyawan.php';

class LemburController {
    private $lemburModel;
    private $karyawanModel;

    public function __construct() {
        $this->lemburModel  = new Lembur();
        $this->karyawanModel = new Karyawan();
    }

    public function index() {
        $is_admin      = is_admin();
        $karyawan_id   = get_session_karyawan_id();

        // Jika pegawai, paksa filter ke data milik sendiri
        if (!$is_admin) {
            if (!$karyawan_id) {
                // Akun pegawai belum dikaitkan dengan data karyawan
                set_flash_message('warning', 'Perhatian', 'Akun Anda belum dikaitkan dengan data karyawan. Hubungi admin.');
                $data     = [];
                $karyawan = [];
                require_once dirname(__DIR__) . '/views/templates/header.php';
                require_once dirname(__DIR__) . '/views/lembur/index.php';
                require_once dirname(__DIR__) . '/views/templates/footer.php';
                return;
            }
            $filters = [
                'start_date'  => sanitize($_GET['start_date'] ?? ''),
                'end_date'    => sanitize($_GET['end_date'] ?? ''),
                'id_karyawan' => $karyawan_id, // paksa milik sendiri
                'search'      => ''
            ];
            // Ambil hanya data karyawan sendiri untuk form
            $karyawan = [$this->karyawanModel->getById($karyawan_id)];
        } else {
            $filters = [
                'start_date'  => sanitize($_GET['start_date'] ?? ''),
                'end_date'    => sanitize($_GET['end_date'] ?? ''),
                'id_karyawan' => sanitize($_GET['id_karyawan'] ?? ''),
                'search'      => sanitize($_GET['search'] ?? '')
            ];
            $karyawan = $this->karyawanModel->getAll(['status_aktif' => 1]);
        }

        $data = $this->lemburModel->getAll($filters);

        // Render view
        require_once dirname(__DIR__) . '/views/templates/header.php';
        require_once dirname(__DIR__) . '/views/lembur/index.php';
        require_once dirname(__DIR__) . '/views/templates/footer.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $is_admin    = is_admin();
            $karyawan_id_session = get_session_karyawan_id();

            // Jika pegawai, paksa id_karyawan = milik sendiri
            if (!$is_admin) {
                if (!$karyawan_id_session) {
                    set_flash_message('error', 'Gagal', 'Akun Anda belum dikaitkan dengan data karyawan.');
                    redirect('index.php?page=lembur');
                }
                $id_karyawan = (int)$karyawan_id_session;
            } else {
                $id_karyawan = (int)($_POST['id_karyawan'] ?? 0);
            }

            $tanggal    = sanitize($_POST['tanggal'] ?? date('Y-m-d'));
            $jumlah_jam = (double)($_POST['jumlah_jam'] ?? 0);
            $keterangan = sanitize($_POST['keterangan'] ?? '');

            if ($id_karyawan <= 0 || $jumlah_jam <= 0) {
                set_flash_message('error', 'Gagal', 'Pilih karyawan dan masukkan jumlah jam lembur.');
                redirect('index.php?page=lembur');
            }

            // Fetch employee to get current rate
            $emp = $this->karyawanModel->getById($id_karyawan);
            if (!$emp) {
                set_flash_message('error', 'Gagal', 'Karyawan tidak ditemukan.');
                redirect('index.php?page=lembur');
            }

            $tarif_lembur = $emp['tarif_lembur'];
            $total_lembur = $jumlah_jam * $tarif_lembur;

            $data = [
                'id_karyawan'  => $id_karyawan,
                'tanggal'      => $tanggal,
                'jumlah_jam'   => $jumlah_jam,
                'tarif_lembur' => $tarif_lembur,
                'total_lembur' => $total_lembur,
                'keterangan'   => $keterangan
            ];

            if ($this->lemburModel->create($data)) {
                set_flash_message('success', 'Berhasil', 'Data lembur berhasil dicatat.');
            } else {
                set_flash_message('error', 'Gagal', 'Gagal menyimpan data lembur.');
            }
            
            redirect('index.php?page=lembur');
        }
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id          = (int)($_POST['id'] ?? 0);
            $is_admin    = is_admin();
            $karyawan_id_session = get_session_karyawan_id();

            // Verifikasi ownership untuk pegawai
            if (!$is_admin) {
                $existing = $this->lemburModel->getById($id);
                if (!$existing || (int)$existing['id_karyawan'] !== (int)$karyawan_id_session) {
                    set_flash_message('error', 'Akses Ditolak', 'Anda hanya bisa mengubah catatan lembur milik Anda sendiri.');
                    redirect('index.php?page=lembur');
                }
                $id_karyawan = (int)$karyawan_id_session;
            } else {
                $id_karyawan = (int)($_POST['id_karyawan'] ?? 0);
            }

            $tanggal    = sanitize($_POST['tanggal'] ?? date('Y-m-d'));
            $jumlah_jam = (double)($_POST['jumlah_jam'] ?? 0);
            $keterangan = sanitize($_POST['keterangan'] ?? '');
            
            $redirect    = sanitize($_POST['redirect'] ?? 'lembur');
            $urlRedirect = ($redirect === 'laporan') ? 'index.php?page=laporan&action=lembur' : 'index.php?page=lembur';

            if ($id <= 0 || $id_karyawan <= 0 || $jumlah_jam <= 0) {
                set_flash_message('error', 'Gagal', 'Data edit tidak valid.');
                redirect($urlRedirect);
            }

            // Fetch employee to get current rate
            $emp = $this->karyawanModel->getById($id_karyawan);
            if (!$emp) {
                set_flash_message('error', 'Gagal', 'Karyawan tidak ditemukan.');
                redirect($urlRedirect);
            }

            $tarif_lembur = $emp['tarif_lembur'];
            $total_lembur = $jumlah_jam * $tarif_lembur;

            $data = [
                'id_karyawan'  => $id_karyawan,
                'tanggal'      => $tanggal,
                'jumlah_jam'   => $jumlah_jam,
                'tarif_lembur' => $tarif_lembur,
                'total_lembur' => $total_lembur,
                'keterangan'   => $keterangan
            ];

            if ($this->lemburModel->update($id, $data)) {
                set_flash_message('success', 'Berhasil', 'Data lembur berhasil diperbarui.');
            } else {
                set_flash_message('error', 'Gagal', 'Gagal memperbarui data lembur.');
            }
            
            redirect($urlRedirect);
        }
    }

    public function delete() {
        $id       = (int)($_GET['id'] ?? 0);
        $redirect = sanitize($_GET['redirect'] ?? 'lembur');
        $is_admin = is_admin();
        $karyawan_id_session = get_session_karyawan_id();
        
        $urlRedirect = ($redirect === 'laporan') ? 'index.php?page=laporan&action=lembur' : 'index.php?page=lembur';

        if ($id <= 0) {
            set_flash_message('error', 'Gagal', 'ID tidak valid.');
            redirect($urlRedirect);
        }

        // Verifikasi ownership untuk pegawai
        if (!$is_admin) {
            $existing = $this->lemburModel->getById($id);
            if (!$existing || (int)$existing['id_karyawan'] !== (int)$karyawan_id_session) {
                set_flash_message('error', 'Akses Ditolak', 'Anda hanya bisa menghapus catatan lembur milik Anda sendiri.');
                redirect($urlRedirect);
            }
        }

        if ($this->lemburModel->delete($id)) {
            set_flash_message('success', 'Berhasil', 'Catatan lembur berhasil dihapus.');
        } else {
            set_flash_message('error', 'Gagal', 'Gagal menghapus catatan lembur.');
        }

        redirect($urlRedirect);
    }
}
