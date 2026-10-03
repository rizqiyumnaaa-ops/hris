<?php
/**
 * Divisi Controller
 */

require_once dirname(__DIR__) . '/models/Divisi.php';

class DivisiController {
    private $divisiModel;

    public function __construct() {
        $this->divisiModel = new Divisi();
    }

    public function index() {
        $data = $this->divisiModel->getAll();
        
        // Render view
        require_once dirname(__DIR__) . '/views/templates/header.php';
        require_once dirname(__DIR__) . '/views/divisi/index.php';
        require_once dirname(__DIR__) . '/views/templates/footer.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_divisi = sanitize($_POST['nama_divisi'] ?? '');

            if (empty($nama_divisi)) {
                set_flash_message('error', 'Gagal', 'Nama divisi tidak boleh kosong.');
                redirect('index.php?page=divisi');
            }

            try {
                $this->divisiModel->create($nama_divisi);
                set_flash_message('success', 'Berhasil', 'Divisi baru berhasil ditambahkan.');
            } catch (Exception $e) {
                set_flash_message('error', 'Gagal', 'Gagal menambahkan divisi. Kemungkinan nama divisi sudah terdaftar.');
            }
            
            redirect('index.php?page=divisi');
        }
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $nama_divisi = sanitize($_POST['nama_divisi'] ?? '');

            if ($id <= 0 || empty($nama_divisi)) {
                set_flash_message('error', 'Gagal', 'Data tidak valid.');
                redirect('index.php?page=divisi');
            }

            try {
                $this->divisiModel->update($id, $nama_divisi);
                set_flash_message('success', 'Berhasil', 'Nama divisi berhasil diubah.');
            } catch (Exception $e) {
                set_flash_message('error', 'Gagal', 'Gagal mengubah divisi. Kemungkinan nama divisi sudah terdaftar.');
            }
            
            redirect('index.php?page=divisi');
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            set_flash_message('error', 'Gagal', 'ID tidak valid.');
            redirect('index.php?page=divisi');
        }

        try {
            $this->divisiModel->delete($id);
            set_flash_message('success', 'Berhasil', 'Divisi berhasil dihapus.');
        } catch (Exception $e) {
            set_flash_message('error', 'Gagal', 'Divisi tidak dapat dihapus karena masih digunakan oleh data karyawan.');
        }

        redirect('index.php?page=divisi');
    }
}
