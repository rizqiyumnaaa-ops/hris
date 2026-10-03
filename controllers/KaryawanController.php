<?php
/**
 * Karyawan Controller
 */

require_once dirname(__DIR__) . '/models/Karyawan.php';
require_once dirname(__DIR__) . '/models/Divisi.php';
require_once dirname(__DIR__) . '/models/Jabatan.php';

class KaryawanController {
    private $karyawanModel;
    private $divisiModel;
    private $jabatanModel;

    public function __construct() {
        $this->karyawanModel = new Karyawan();
        $this->divisiModel = new Divisi();
        $this->jabatanModel = new Jabatan();
    }

    public function index() {
        $filters = [
            'search' => sanitize($_GET['search'] ?? ''),
            'id_divisi' => sanitize($_GET['id_divisi'] ?? ''),
            'id_jabatan' => sanitize($_GET['id_jabatan'] ?? ''),
            'status_aktif' => sanitize($_GET['status_aktif'] ?? '')
        ];

        $data = $this->karyawanModel->getAll($filters);
        $divisi = $this->divisiModel->getAll();
        $jabatan = $this->jabatanModel->getAll();

        // Render views
        require_once dirname(__DIR__) . '/views/templates/header.php';
        require_once dirname(__DIR__) . '/views/karyawan/index.php';
        require_once dirname(__DIR__) . '/views/templates/footer.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nik = sanitize($_POST['nik'] ?? '');
            $nama_lengkap = sanitize($_POST['nama_lengkap'] ?? '');
            $id_divisi = (int)($_POST['id_divisi'] ?? 0);
            $id_jabatan = (int)($_POST['id_jabatan'] ?? 0);
            $no_hp = sanitize($_POST['no_hp'] ?? '');
            $alamat = sanitize($_POST['alamat'] ?? '');
            $tanggal_masuk = sanitize($_POST['tanggal_masuk'] ?? '');
            $status_kerja = sanitize($_POST['status_kerja'] ?? '');
            $gaji_pokok = (double)($_POST['gaji_pokok'] ?? 0);
            $tarif_lembur = (double)($_POST['tarif_lembur'] ?? 0);
            
            // Check NIK unique
            if ($this->karyawanModel->getByNik($nik)) {
                set_flash_message('error', 'Gagal', 'NIK sudah terdaftar di sistem.');
                redirect('index.php?page=karyawan&action=create');
            }

            $foto_name = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $upload = upload_foto($_FILES['foto'], $nik);
                if ($upload['status']) {
                    $foto_name = $upload['filename'];
                } else {
                    set_flash_message('error', 'Upload Gagal', $upload['message']);
                    redirect('index.php?page=karyawan&action=create');
                }
            }

            $data = [
                'nik' => $nik,
                'nama_lengkap' => $nama_lengkap,
                'foto' => $foto_name,
                'id_jabatan' => $id_jabatan,
                'id_divisi' => $id_divisi,
                'no_hp' => $no_hp,
                'alamat' => $alamat,
                'tanggal_masuk' => $tanggal_masuk,
                'status_kerja' => $status_kerja,
                'status_aktif' => 1,
                'gaji_pokok' => $gaji_pokok,
                'tarif_lembur' => $tarif_lembur
            ];

            if ($this->karyawanModel->create($data)) {
                set_flash_message('success', 'Berhasil', 'Karyawan baru berhasil didaftarkan.');
                redirect('index.php?page=karyawan');
            } else {
                set_flash_message('error', 'Gagal', 'Terjadi kesalahan saat menyimpan data karyawan.');
                redirect('index.php?page=karyawan&action=create');
            }
        } else {
            $divisi = $this->divisiModel->getAll();
            $jabatan = $this->jabatanModel->getAll();
            
            require_once dirname(__DIR__) . '/views/templates/header.php';
            require_once dirname(__DIR__) . '/views/karyawan/create.php';
            require_once dirname(__DIR__) . '/views/templates/footer.php';
        }
    }

    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $karyawan = $this->karyawanModel->getById($id);
        
        if (!$karyawan) {
            set_flash_message('error', 'Kesalahan', 'Data karyawan tidak ditemukan.');
            redirect('index.php?page=karyawan');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama_lengkap = sanitize($_POST['nama_lengkap'] ?? '');
            $id_divisi = (int)($_POST['id_divisi'] ?? 0);
            $id_jabatan = (int)($_POST['id_jabatan'] ?? 0);
            $no_hp = sanitize($_POST['no_hp'] ?? '');
            $alamat = sanitize($_POST['alamat'] ?? '');
            $tanggal_masuk = sanitize($_POST['tanggal_masuk'] ?? '');
            $status_kerja = sanitize($_POST['status_kerja'] ?? '');
            $status_aktif = isset($_POST['status_aktif']) ? 1 : 0;
            $gaji_pokok = (double)($_POST['gaji_pokok'] ?? 0);
            $tarif_lembur = (double)($_POST['tarif_lembur'] ?? 0);

            $data = [
                'nama_lengkap' => $nama_lengkap,
                'id_jabatan' => $id_jabatan,
                'id_divisi' => $id_divisi,
                'no_hp' => $no_hp,
                'alamat' => $alamat,
                'tanggal_masuk' => $tanggal_masuk,
                'status_kerja' => $status_kerja,
                'status_aktif' => $status_aktif,
                'gaji_pokok' => $gaji_pokok,
                'tarif_lembur' => $tarif_lembur
            ];

            // If photo uploaded
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $upload = upload_foto($_FILES['foto'], $karyawan['nik']);
                if ($upload['status']) {
                    $data['foto'] = $upload['filename'];
                    // Delete old photo
                    if (!empty($karyawan['foto'])) {
                        $old_file = dirname(__DIR__) . '/assets/uploads/foto_karyawan/' . $karyawan['foto'];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                } else {
                    set_flash_message('error', 'Upload Gagal', $upload['message']);
                    redirect('index.php?page=karyawan&action=edit&id=' . $id);
                }
            }

            if ($this->karyawanModel->update($id, $data)) {
                set_flash_message('success', 'Berhasil', 'Data karyawan berhasil diperbarui.');
                redirect('index.php?page=karyawan');
            } else {
                set_flash_message('error', 'Gagal', 'Terjadi kesalahan saat memperbarui data.');
                redirect('index.php?page=karyawan&action=edit&id=' . $id);
            }
        } else {
            $divisi = $this->divisiModel->getAll();
            $jabatan = $this->jabatanModel->getAll();
            
            require_once dirname(__DIR__) . '/views/templates/header.php';
            require_once dirname(__DIR__) . '/views/karyawan/edit.php';
            require_once dirname(__DIR__) . '/views/templates/footer.php';
        }
    }

    public function detail() {
        $id = (int)($_GET['id'] ?? 0);
        $karyawan = $this->karyawanModel->getById($id);
        
        if (!$karyawan) {
            set_flash_message('error', 'Kesalahan', 'Data karyawan tidak ditemukan.');
            redirect('index.php?page=karyawan');
        }

        require_once dirname(__DIR__) . '/views/templates/header.php';
        require_once dirname(__DIR__) . '/views/karyawan/detail.php';
        require_once dirname(__DIR__) . '/views/templates/footer.php';
    }

    public function toggle() {
        $id = (int)($_GET['id'] ?? 0);
        $status = (int)($_GET['status'] ?? 0);
        
        if ($id > 0) {
            $this->karyawanModel->toggleStatus($id, $status);
            $msg = $status === 1 ? 'Karyawan diaktifkan kembali.' : 'Karyawan dinonaktifkan.';
            set_flash_message('success', 'Berhasil', $msg);
        } else {
            set_flash_message('error', 'Gagal', 'ID tidak valid.');
        }
        redirect('index.php?page=karyawan');
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $karyawan = $this->karyawanModel->getById($id);

        if (!$karyawan) {
            set_flash_message('error', 'Kesalahan', 'Data karyawan tidak ditemukan.');
            redirect('index.php?page=karyawan');
        }

        try {
            // Delete photo file
            if (!empty($karyawan['foto'])) {
                $file = dirname(__DIR__) . '/assets/uploads/foto_karyawan/' . $karyawan['foto'];
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            
            $this->karyawanModel->delete($id);
            set_flash_message('success', 'Berhasil', 'Data karyawan berhasil dihapus.');
        } catch (Exception $e) {
            set_flash_message('error', 'Gagal', 'Karyawan tidak dapat dihapus karena memiliki riwayat absensi atau keuangan.');
        }

        redirect('index.php?page=karyawan');
    }
}
