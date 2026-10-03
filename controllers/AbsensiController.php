<?php
/**
 * Absensi Controller
 */

require_once dirname(__DIR__) . '/models/Absensi.php';
require_once dirname(__DIR__) . '/models/Karyawan.php';
require_once dirname(__DIR__) . '/models/Divisi.php';
require_once dirname(__DIR__) . '/models/Jabatan.php';

class AbsensiController {
    private $absensiModel;
    private $divisiModel;
    private $jabatanModel;

    public function __construct() {
        $this->absensiModel = new Absensi();
        $this->divisiModel = new Divisi();
        $this->jabatanModel = new Jabatan();
    }

    public function index() {
        // Default to today
        $tanggal = sanitize($_GET['tanggal'] ?? date('Y-m-d'));

        $filters = [
            'search'     => sanitize($_GET['search'] ?? ''),
            'id_divisi'  => sanitize($_GET['id_divisi'] ?? ''),
            'id_jabatan' => sanitize($_GET['id_jabatan'] ?? ''),
        ];

        // Semua role bisa melihat & input absensi seluruh karyawan
        $data    = $this->absensiModel->getByDate($tanggal, $filters);
        $divisi  = $this->divisiModel->getAll();
        $jabatan = $this->jabatanModel->getAll();

        // Pegawai tetap pakai tampilan form penuh (bukan read-only)
        $is_pegawai_view = false;

        // Render view
        require_once dirname(__DIR__) . '/views/templates/header.php';
        require_once dirname(__DIR__) . '/views/absensi/index.php';
        require_once dirname(__DIR__) . '/views/templates/footer.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tanggal = sanitize($_POST['tanggal'] ?? date('Y-m-d'));
            $absensi_input = $_POST['absensi'] ?? [];

            if (empty($absensi_input)) {
                set_flash_message('error', 'Gagal', 'Tidak ada data absensi untuk disimpan.');
                redirect('index.php?page=absensi&tanggal=' . $tanggal);
            }

            // Prepare records
            $records = [];
            $jam_sekarang = date('H:i:s');
            
            foreach ($absensi_input as $id_karyawan => $item) {
                $status = sanitize($item['status_absensi'] ?? 'Hadir');
                $keterangan = sanitize($item['keterangan'] ?? '');
                
                $records[] = [
                    'id_karyawan' => (int)$id_karyawan,
                    'status_absensi' => $status,
                    'keterangan' => $keterangan,
                    // If a custom time is needed we can pass it, or just use now
                    'jam_absen' => $jam_sekarang
                ];
            }

            try {
                $this->absensiModel->saveBulk($tanggal, $records);
                set_flash_message('success', 'Berhasil', 'Seluruh data absensi tanggal ' . format_tanggal($tanggal) . ' berhasil disimpan.');
            } catch (Exception $e) {
                set_flash_message('error', 'Gagal', 'Terjadi kesalahan saat menyimpan absensi: ' . $e->getMessage());
            }

            redirect('index.php?page=absensi&tanggal=' . $tanggal);
        }
    }

    public function edit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $status_absensi = sanitize($_POST['status_absensi'] ?? 'Hadir');
            $keterangan = sanitize($_POST['keterangan'] ?? '');
            
            $redirect = sanitize($_POST['redirect'] ?? 'laporan');
            $urlRedirect = ($redirect === 'laporan') ? 'index.php?page=laporan&action=absensi' : 'index.php?page=absensi';

            if ($id <= 0) {
                set_flash_message('error', 'Gagal', 'Data edit tidak valid.');
                redirect($urlRedirect);
            }

            $data = [
                'status_absensi' => $status_absensi,
                'keterangan' => $keterangan
            ];

            if ($this->absensiModel->update($id, $data)) {
                set_flash_message('success', 'Berhasil', 'Data absensi berhasil diperbarui.');
            } else {
                set_flash_message('error', 'Gagal', 'Gagal memperbarui data absensi.');
            }
            
            redirect($urlRedirect);
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $redirect = sanitize($_GET['redirect'] ?? 'laporan');
        
        $urlRedirect = ($redirect === 'laporan') ? 'index.php?page=laporan&action=absensi' : 'index.php?page=absensi';

        if ($id <= 0) {
            set_flash_message('error', 'Gagal', 'ID tidak valid.');
            redirect($urlRedirect);
        }

        if ($this->absensiModel->delete($id)) {
            set_flash_message('success', 'Berhasil', 'Catatan absensi berhasil dihapus.');
        } else {
            set_flash_message('error', 'Gagal', 'Gagal menghapus catatan absensi.');
        }

        redirect($urlRedirect);
    }
}
