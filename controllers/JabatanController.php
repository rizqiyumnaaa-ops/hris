<?php
/**
 * Jabatan Controller
 */

require_once dirname(__DIR__) . '/models/Jabatan.php';

class JabatanController {
    private $jabatanModel;

    public function __construct() {
        $this->jabatanModel = new Jabatan();
    }

    public function index() {
        $data = $this->jabatanModel->getAll();
        
        // Render view
        require_once dirname(__DIR__) . '/views/templates/header.php';
        require_once dirname(__DIR__) . '/views/jabatan/index.php';
        require_once dirname(__DIR__) . '/views/templates/footer.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_jabatan = sanitize($_POST['nama_jabatan'] ?? '');

            if (empty($nama_jabatan)) {
                set_flash_message('error', 'Gagal', 'Nama jabatan tidak boleh kosong.');
                redirect('index.php?page=jabatan');
            }

            try {
                $this->jabatanModel->create($nama_jabatan);
                set_flash_message('success', 'Berhasil', 'Jabatan baru berhasil ditambahkan.');
            } catch (Exception $e) {
                set_flash_message('error', 'Gagal', 'Gagal menambahkan jabatan. Kemungkinan nama jabatan sudah terdaftar.');
            }
            
            redirect('index.php?page=jabatan');
        }
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $nama_jabatan = sanitize($_POST['nama_jabatan'] ?? '');

            if ($id <= 0 || empty($nama_jabatan)) {
                set_flash_message('error', 'Gagal', 'Data tidak valid.');
                redirect('index.php?page=jabatan');
            }

            try {
                $this->jabatanModel->update($id, $nama_jabatan);
                set_flash_message('success', 'Berhasil', 'Nama jabatan berhasil diubah.');
            } catch (Exception $e) {
                set_flash_message('error', 'Gagal', 'Gagal mengubah jabatan. Kemungkinan nama jabatan sudah terdaftar.');
            }
            
            redirect('index.php?page=jabatan');
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            set_flash_message('error', 'Gagal', 'ID tidak valid.');
            redirect('index.php?page=jabatan');
        }

        try {
            $this->jabatanModel->delete($id);
            set_flash_message('success', 'Berhasil', 'Jabatan berhasil dihapus.');
        } catch (Exception $e) {
            set_flash_message('error', 'Gagal', 'Jabatan tidak dapat dihapus karena masih digunakan oleh data karyawan.');
        }

        redirect('index.php?page=jabatan');
    }
}
