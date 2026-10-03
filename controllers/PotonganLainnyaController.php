<?php
/**
 * Potongan Lainnya Controller
 */

require_once dirname(__DIR__) . '/models/PotonganLainnya.php';
require_once dirname(__DIR__) . '/models/Karyawan.php';

class PotonganLainnyaController {
    private $potonganModel;
    private $karyawanModel;

    public function __construct() {
        $this->potonganModel = new PotonganLainnya();
        $this->karyawanModel = new Karyawan();
    }

    public function index() {
        $data = $this->potonganModel->getAll();
        
        // Ambil data karyawan aktif untuk checklist relasi
        $karyawan = $this->karyawanModel->getAll(['status_aktif' => 1]);
        
        // Petakan ID karyawan yang terdaftar di masing-masing potongan
        foreach ($data as &$row) {
            $row['karyawan_ids'] = $this->potonganModel->getEmployeeIdsForPotongan($row['id']);
        }
        unset($row);
        
        // Render view
        require_once dirname(__DIR__) . '/views/templates/header.php';
        require_once dirname(__DIR__) . '/views/potongan_lainnya/index.php';
        require_once dirname(__DIR__) . '/views/templates/footer.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_potongan = sanitize($_POST['nama_potongan'] ?? '');
            $nominal = (double)($_POST['nominal'] ?? 0);
            $status_aktif = isset($_POST['status_aktif']) ? (int)$_POST['status_aktif'] : 1;

            if (empty($nama_potongan) || $nominal <= 0) {
                set_flash_message('error', 'Gagal', 'Nama potongan harus diisi dan nominal harus lebih dari 0.');
                redirect('index.php?page=potongan_lainnya');
            }

            $data = [
                'nama_potongan' => $nama_potongan,
                'nominal' => $nominal,
                'status_aktif' => $status_aktif
            ];

            if ($this->potonganModel->create($data)) {
                set_flash_message('success', 'Berhasil', 'Potongan lainnya berhasil ditambahkan.');
            } else {
                set_flash_message('error', 'Gagal', 'Gagal menambahkan potongan lainnya.');
            }
            
            redirect('index.php?page=potongan_lainnya');
        }
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $nama_potongan = sanitize($_POST['nama_potongan'] ?? '');
            $nominal = (double)($_POST['nominal'] ?? 0);
            $status_aktif = isset($_POST['status_aktif']) ? (int)$_POST['status_aktif'] : 1;

            if ($id <= 0 || empty($nama_potongan) || $nominal <= 0) {
                set_flash_message('error', 'Gagal', 'Data tidak valid.');
                redirect('index.php?page=potongan_lainnya');
            }

            $data = [
                'nama_potongan' => $nama_potongan,
                'nominal' => $nominal,
                'status_aktif' => $status_aktif
            ];

            if ($this->potonganModel->update($id, $data)) {
                set_flash_message('success', 'Berhasil', 'Potongan lainnya berhasil diubah.');
            } else {
                set_flash_message('error', 'Gagal', 'Gagal mengubah potongan lainnya.');
            }
            
            redirect('index.php?page=potongan_lainnya');
        }
    }

    public function toggle() {
        $id = (int)($_GET['id'] ?? 0);
        $status = isset($_GET['status']) ? (int)$_GET['status'] : 0;

        if ($id <= 0) {
            set_flash_message('error', 'Gagal', 'Data tidak valid.');
            redirect('index.php?page=potongan_lainnya');
        }

        if ($this->potonganModel->toggleStatus($id, $status)) {
            set_flash_message('success', 'Berhasil', 'Status potongan berhasil diubah.');
        } else {
            set_flash_message('error', 'Gagal', 'Gagal mengubah status potongan.');
        }

        redirect('index.php?page=potongan_lainnya');
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            set_flash_message('error', 'Gagal', 'ID tidak valid.');
            redirect('index.php?page=potongan_lainnya');
        }

        if ($this->potonganModel->delete($id)) {
            set_flash_message('success', 'Berhasil', 'Potongan lainnya berhasil dihapus.');
        } else {
            set_flash_message('error', 'Gagal', 'Gagal menghapus potongan lainnya.');
        }

        redirect('index.php?page=potongan_lainnya');
    }
    
    public function save_karyawan() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_potongan = (int)($_POST['id_potongan'] ?? 0);
            $karyawan_ids = $_POST['karyawan_ids'] ?? [];

            if ($id_potongan <= 0) {
                set_flash_message('error', 'Gagal', 'Potongan tidak valid.');
                redirect('index.php?page=potongan_lainnya');
            }

            if ($this->potonganModel->saveEmployeeMappings($id_potongan, $karyawan_ids)) {
                set_flash_message('success', 'Berhasil', 'Daftar karyawan untuk potongan ini berhasil diperbarui.');
            } else {
                set_flash_message('error', 'Gagal', 'Gagal memperbarui daftar karyawan.');
            }
            redirect('index.php?page=potongan_lainnya');
        }
    }
}
