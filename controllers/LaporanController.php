<?php
/**
 * Laporan Controller
 */

require_once dirname(__DIR__) . '/models/Absensi.php';
require_once dirname(__DIR__) . '/models/Lembur.php';
require_once dirname(__DIR__) . '/models/Piutang.php';
require_once dirname(__DIR__) . '/models/Penggajian.php';
require_once dirname(__DIR__) . '/models/Karyawan.php';
require_once dirname(__DIR__) . '/models/Divisi.php';
require_once dirname(__DIR__) . '/models/Jabatan.php';

class LaporanController {
    private $absensiModel;
    private $lemburModel;
    private $piutangModel;
    private $penggajianModel;
    private $karyawanModel;
    private $divisiModel;
    private $jabatanModel;

    public function __construct() {
        $this->absensiModel = new Absensi();
        $this->lemburModel = new Lembur();
        $this->piutangModel = new Piutang();
        $this->penggajianModel = new Penggajian();
        $this->karyawanModel = new Karyawan();
        $this->divisiModel = new Divisi();
        $this->jabatanModel = new Jabatan();
    }

    public function absensi() {
        $filters = [
            'start_date' => sanitize($_GET['start_date'] ?? date('Y-m-01')),
            'end_date' => sanitize($_GET['end_date'] ?? date('Y-m-t')),
            'id_karyawan' => sanitize($_GET['id_karyawan'] ?? ''),
            'id_divisi' => sanitize($_GET['id_divisi'] ?? ''),
            'id_jabatan' => sanitize($_GET['id_jabatan'] ?? ''),
            'search' => sanitize($_GET['search'] ?? '')
        ];

        $export = sanitize($_GET['export'] ?? '');
        $data = $this->absensiModel->getReport($filters);

        if ($export === 'excel') {
            $this->exportAbsensiExcel($data, $filters);
        } elseif ($export === 'pdf') {
            // Render standalone print view
            require_once dirname(__DIR__) . '/views/laporan/absensi_print.php';
        } else {
            $karyawan = $this->karyawanModel->getAll(['status_aktif' => 1]);
            $divisi = $this->divisiModel->getAll();
            $jabatan = $this->jabatanModel->getAll();

            require_once dirname(__DIR__) . '/views/templates/header.php';
            require_once dirname(__DIR__) . '/views/laporan/absensi.php';
            require_once dirname(__DIR__) . '/views/templates/footer.php';
        }
    }

    public function lembur() {
        $filters = [
            'start_date' => sanitize($_GET['start_date'] ?? date('Y-m-01')),
            'end_date' => sanitize($_GET['end_date'] ?? date('Y-m-t')),
            'id_karyawan' => sanitize($_GET['id_karyawan'] ?? ''),
            'id_divisi' => sanitize($_GET['id_divisi'] ?? ''),
            'id_jabatan' => sanitize($_GET['id_jabatan'] ?? ''),
            'search' => sanitize($_GET['search'] ?? '')
        ];

        $export = sanitize($_GET['export'] ?? '');
        $data = $this->lemburModel->getAll($filters);

        if ($export === 'excel') {
            $this->exportLemburExcel($data, $filters);
        } elseif ($export === 'pdf') {
            require_once dirname(__DIR__) . '/views/laporan/lembur_print.php';
        } else {
            $karyawan = $this->karyawanModel->getAll(['status_aktif' => 1]);
            $divisi = $this->divisiModel->getAll();
            $jabatan = $this->jabatanModel->getAll();

            require_once dirname(__DIR__) . '/views/templates/header.php';
            require_once dirname(__DIR__) . '/views/laporan/lembur.php';
            require_once dirname(__DIR__) . '/views/templates/footer.php';
        }
    }

    public function piutang() {
        $filters = [
            'start_date' => sanitize($_GET['start_date'] ?? date('Y-m-01')),
            'end_date' => sanitize($_GET['end_date'] ?? date('Y-m-t')),
            'id_karyawan' => sanitize($_GET['id_karyawan'] ?? ''),
            'id_divisi' => sanitize($_GET['id_divisi'] ?? ''),
            'id_jabatan' => sanitize($_GET['id_jabatan'] ?? ''),
            'status_piutang' => sanitize($_GET['status_piutang'] ?? ''),
            'search' => sanitize($_GET['search'] ?? '')
        ];

        $export = sanitize($_GET['export'] ?? '');
        $data = $this->piutangModel->getAll($filters);

        if ($export === 'excel') {
            $this->exportPiutangExcel($data, $filters);
        } elseif ($export === 'pdf') {
            require_once dirname(__DIR__) . '/views/laporan/piutang_print.php';
        } else {
            $karyawan = $this->karyawanModel->getAll(['status_aktif' => 1]);
            $divisi = $this->divisiModel->getAll();
            $jabatan = $this->jabatanModel->getAll();

            require_once dirname(__DIR__) . '/views/templates/header.php';
            require_once dirname(__DIR__) . '/views/laporan/piutang.php';
            require_once dirname(__DIR__) . '/views/templates/footer.php';
        }
    }

    public function penggajian() {
        $filters = [
            'tahun' => sanitize($_GET['tahun'] ?? date('Y')),
            'bulan' => sanitize($_GET['bulan'] ?? date('m')),
            'periode_ke' => sanitize($_GET['periode_ke'] ?? ''),
            'id_karyawan' => sanitize($_GET['id_karyawan'] ?? ''),
            'id_divisi' => sanitize($_GET['id_divisi'] ?? ''),
            'id_jabatan' => sanitize($_GET['id_jabatan'] ?? ''),
            'search' => sanitize($_GET['search'] ?? '')
        ];

        $export = sanitize($_GET['export'] ?? '');
        $data = $this->penggajianModel->getAll($filters);

        if ($export === 'excel') {
            $this->exportPenggajianExcel($data, $filters);
        } elseif ($export === 'pdf') {
            require_once dirname(__DIR__) . '/views/laporan/penggajian_print.php';
        } else {
            $karyawan = $this->karyawanModel->getAll();
            $divisi = $this->divisiModel->getAll();
            $jabatan = $this->jabatanModel->getAll();

            require_once dirname(__DIR__) . '/views/templates/header.php';
            require_once dirname(__DIR__) . '/views/laporan/penggajian.php';
            require_once dirname(__DIR__) . '/views/templates/footer.php';
        }
    }

    // EXCEL EXPORTERS
    private function exportAbsensiExcel($data, $filters) {
        $filename = "Laporan_Absensi_" . $filters['start_date'] . "_to_" . $filters['end_date'] . ".xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        
        echo "<h3>LAPORAN ABSENSI KARYAWAN</h3>";
        echo "<h4>Periode: " . format_tanggal($filters['start_date']) . " s/d " . format_tanggal($filters['end_date']) . "</h4>";
        echo "<table border='1'><thead><tr style='background-color: #dc2626; color: #fff;'><th>No</th><th>Tanggal</th><th>NIK</th><th>Nama Karyawan</th><th>Divisi</th><th>Jabatan</th><th>Status Absensi</th><th>Jam Absen</th><th>Keterangan</th></tr></thead><tbody>";
        $no = 1;
        foreach ($data as $row) {
            echo "<tr><td>$no</td><td>{$row['tanggal']}</td><td>'{$row['nik']}</td><td>{$row['nama_lengkap']}</td><td>{$row['nama_divisi']}</td><td>{$row['nama_jabatan']}</td><td>{$row['status_absensi']}</td><td>{$row['jam_absen']}</td><td>{$row['keterangan']}</td></tr>";
            $no++;
        }
        echo "</tbody></table>";
        exit();
    }

    private function exportLemburExcel($data, $filters) {
        $filename = "Laporan_Lembur_" . $filters['start_date'] . "_to_" . $filters['end_date'] . ".xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        
        echo "<h3>LAPORAN LEMBUR KARYAWAN</h3>";
        echo "<h4>Periode: " . format_tanggal($filters['start_date']) . " s/d " . format_tanggal($filters['end_date']) . "</h4>";
        echo "<table border='1'><thead><tr style='background-color: #dc2626; color: #fff;'><th>No</th><th>Tanggal</th><th>NIK</th><th>Nama Karyawan</th><th>Divisi</th><th>Jabatan</th><th>Jam Lembur</th><th>Tarif Lembur</th><th>Total Lembur</th><th>Keterangan</th></tr></thead><tbody>";
        $no = 1;
        $total_nominal = 0;
        foreach ($data as $row) {
            $total_nominal += $row['total_lembur'];
            echo "<tr><td>$no</td><td>{$row['tanggal']}</td><td>'{$row['nik']}</td><td>{$row['nama_lengkap']}</td><td>{$row['nama_divisi']}</td><td>{$row['nama_jabatan']}</td><td>" . number_format($row['jumlah_jam'], 1) . "</td><td>" . (int)$row['tarif_lembur'] . "</td><td>" . (int)$row['total_lembur'] . "</td><td>{$row['keterangan']}</td></tr>";
            $no++;
        }
        echo "<tr><td colspan='8' align='right'><b>TOTAL KOMPENSASI</b></td><td><b>" . (int)$total_nominal . "</b></td><td></td></tr>";
        echo "</tbody></table>";
        exit();
    }

    private function exportPiutangExcel($data, $filters) {
        $filename = "Laporan_Piutang_" . $filters['start_date'] . "_to_" . $filters['end_date'] . ".xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        
        echo "<h3>LAPORAN PIUTANG KARYAWAN</h3>";
        echo "<h4>Periode: " . format_tanggal($filters['start_date']) . " s/d " . format_tanggal($filters['end_date']) . "</h4>";
        echo "<table border='1'><thead><tr style='background-color: #dc2626; color: #fff;'><th>No</th><th>Tanggal Pinjam</th><th>NIK</th><th>Nama Karyawan</th><th>Divisi</th><th>Jabatan</th><th>Nominal Piutang</th><th>Status</th><th>Keterangan</th></tr></thead><tbody>";
        $no = 1;
        $total_nominal = 0;
        foreach ($data as $row) {
            $total_nominal += $row['nominal'];
            echo "<tr><td>$no</td><td>{$row['tanggal']}</td><td>'{$row['nik']}</td><td>{$row['nama_lengkap']}</td><td>{$row['nama_divisi']}</td><td>{$row['nama_jabatan']}</td><td>" . (int)$row['nominal'] . "</td><td>{$row['status_piutang']}</td><td>{$row['keterangan']}</td></tr>";
            $no++;
        }
        echo "<tr><td colspan='6' align='right'><b>TOTAL PIUTANG</b></td><td><b>" . (int)$total_nominal . "</b></td><td></td><td></td></tr>";
        echo "</tbody></table>";
        exit();
    }

    private function exportPenggajianExcel($data, $filters) {
        $nama_bulan = format_bulan($filters['bulan']);
        $filename = "Laporan_Gaji_Bulan_{$nama_bulan}_{$filters['tahun']}.xls";
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        
        echo "<h3>LAPORAN PENGGAJIAN KARYAWAN</h3>";
        echo "<h4>Bulan: $nama_bulan | Tahun: {$filters['tahun']}</h4>";
        echo "<table border='1'><thead><tr style='background-color: #dc2626; color: #fff;'><th>No</th><th>NIK</th><th>Nama Karyawan</th><th>Divisi</th><th>Jabatan</th><th>Tahun/Bulan</th><th>Periode Ke</th><th>Gaji Pokok</th><th>Total Lembur</th><th>Pot. ½ Hari</th><th>Pot. Alpha</th><th>Total Piutang</th><th>Gaji Bersih</th></tr></thead><tbody>";
        $no = 1;
        $total_gaji = 0;
        foreach ($data as $row) {
            $total_gaji += $row['gaji_bersih'];
            echo "<tr><td>$no</td><td>'{$row['nik']}</td><td>{$row['nama_lengkap']}</td><td>{$row['nama_divisi']}</td><td>{$row['nama_jabatan']}</td><td>{$row['periode_tahun']}-{$row['periode_bulan']}</td><td>{$row['periode_ke']}</td><td>" . (int)$row['gaji_pokok'] . "</td><td>" . (int)$row['total_lembur'] . "</td><td>" . (int)$row['potongan_setengah_hari'] . "</td><td>" . (int)$row['potongan_alpha'] . "</td><td>" . (int)$row['total_piutang'] . "</td><td>" . (int)$row['gaji_bersih'] . "</td></tr>";
            $no++;
        }
        echo "<tr><td colspan='12' align='right'><b>TOTAL GAJI DIBAYARKAN</b></td><td><b>" . (int)$total_gaji . "</b></td></tr>";
        echo "</tbody></table>";
        exit();
    }
}
