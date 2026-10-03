<?php
/**
 * Piutang Controller
 */

require_once dirname(__DIR__) . '/models/Piutang.php';
require_once dirname(__DIR__) . '/models/Karyawan.php';

class PiutangController {
    private $piutangModel;
    private $karyawanModel;

    public function __construct() {
        $this->piutangModel = new Piutang();
        $this->karyawanModel = new Karyawan();
    }

    public function index() {
        $filters = [
            'start_date' => sanitize($_GET['start_date'] ?? ''),
            'end_date' => sanitize($_GET['end_date'] ?? ''),
            'id_karyawan' => sanitize($_GET['id_karyawan'] ?? ''),
            'status_piutang' => sanitize($_GET['status_piutang'] ?? ''),
            'search' => sanitize($_GET['search'] ?? '')
        ];

        $data = $this->piutangModel->getAll($filters);
        $karyawan = $this->karyawanModel->getAll(['status_aktif' => 1]);

        // Render view
        require_once dirname(__DIR__) . '/views/templates/header.php';
        require_once dirname(__DIR__) . '/views/piutang/index.php';
        require_once dirname(__DIR__) . '/views/templates/footer.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_karyawan = (int)($_POST['id_karyawan'] ?? 0);
            $tanggal = sanitize($_POST['tanggal'] ?? date('Y-m-d'));
            $nominal = (double)($_POST['nominal'] ?? 0);
            $keterangan = sanitize($_POST['keterangan'] ?? '');

            if ($id_karyawan <= 0 || $nominal <= 0) {
                set_flash_message('error', 'Gagal', 'Pilih karyawan dan masukkan nominal piutang.');
                redirect('index.php?page=piutang');
            }

            $data = [
                'id_karyawan' => $id_karyawan,
                'tanggal' => $tanggal,
                'nominal' => $nominal,
                'keterangan' => $keterangan,
                'status_piutang' => 'Belum Lunas'
            ];

            if ($this->piutangModel->create($data)) {
                set_flash_message('success', 'Berhasil', 'Data piutang karyawan berhasil dicatat.');
            } else {
                set_flash_message('error', 'Gagal', 'Gagal menyimpan data piutang.');
            }
            
            redirect('index.php?page=piutang');
        }
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $id_karyawan = (int)($_POST['id_karyawan'] ?? 0);
            $tanggal = sanitize($_POST['tanggal'] ?? date('Y-m-d'));
            $nominal = (double)($_POST['nominal'] ?? 0);
            $status_piutang = sanitize($_POST['status_piutang'] ?? 'Belum Lunas');
            $keterangan = sanitize($_POST['keterangan'] ?? '');
            
            $redirect = sanitize($_POST['redirect'] ?? 'piutang');
            $urlRedirect = ($redirect === 'laporan') ? 'index.php?page=laporan&action=piutang' : 'index.php?page=piutang';

            if ($id <= 0 || $id_karyawan <= 0 || $nominal <= 0) {
                set_flash_message('error', 'Gagal', 'Data edit tidak valid.');
                redirect($urlRedirect);
            }

            $data = [
                'id_karyawan' => $id_karyawan,
                'tanggal' => $tanggal,
                'nominal' => $nominal,
                'status_piutang' => $status_piutang,
                'keterangan' => $keterangan
            ];

            if ($this->piutangModel->update($id, $data)) {
                set_flash_message('success', 'Berhasil', 'Data piutang berhasil diperbarui.');
            } else {
                set_flash_message('error', 'Gagal', 'Gagal memperbarui data piutang.');
            }
            
            redirect($urlRedirect);
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $redirect = sanitize($_GET['redirect'] ?? 'piutang');
        
        $urlRedirect = ($redirect === 'laporan') ? 'index.php?page=laporan&action=piutang' : 'index.php?page=piutang';

        if ($id <= 0) {
            set_flash_message('error', 'Gagal', 'ID tidak valid.');
            redirect($urlRedirect);
        }

        if ($this->piutangModel->delete($id)) {
            set_flash_message('success', 'Berhasil', 'Catatan piutang berhasil dihapus.');
        } else {
            set_flash_message('error', 'Gagal', 'Gagal menghapus catatan piutang.');
        }

        redirect($urlRedirect);
    }
}
