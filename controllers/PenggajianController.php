<?php
/**
 * Penggajian Controller
 */

require_once dirname(__DIR__) . '/models/Penggajian.php';
require_once dirname(__DIR__) . '/models/Karyawan.php';
require_once dirname(__DIR__) . '/models/Piutang.php';
require_once dirname(__DIR__) . '/models/Absensi.php';

class PenggajianController {
    private $penggajianModel;
    private $karyawanModel;

    public function __construct() {
        $this->penggajianModel = new Penggajian();
        $this->karyawanModel = new Karyawan();
    }

    public function index() {
        // Default to current year, month, and period
        $tahun = (int)($_GET['tahun'] ?? date('Y'));
        $bulan = (int)($_GET['bulan'] ?? date('m'));
        $periode_ke = (int)($_GET['periode_ke'] ?? ((int)date('d') <= 15 ? 1 : 2));

        // Get saved payrolls for this period
        $filters = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'periode_ke' => $periode_ke
        ];
        $saved_payrolls = $this->penggajianModel->getAll($filters);

        // Find active employees who are NOT yet in the saved payrolls for this period
        $all_active = $this->karyawanModel->getAll(['status_aktif' => 1]);
        $pending_employees = [];
        
        // Extract saved employee IDs
        $saved_ids = array_map(function($p) {
            return $p['id_karyawan'];
        }, $saved_payrolls);

        foreach ($all_active as $emp) {
            if (!in_array($emp['id'], $saved_ids)) {
                // Run calculation simulation
                $calc = $this->penggajianModel->calculateEmployeePayroll($emp['id'], $tahun, $bulan, $periode_ke);
                if ($calc) {
                    $pending_employees[] = $calc;
                }
            }
        }

        // Render views
        require_once dirname(__DIR__) . '/views/templates/header.php';
        require_once dirname(__DIR__) . '/views/penggajian/index.php';
        require_once dirname(__DIR__) . '/views/templates/footer.php';
    }

    // Process bulk saving for the selected period
    public function simpan() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tahun = (int)($_POST['tahun'] ?? date('Y'));
            $bulan = (int)($_POST['bulan'] ?? date('m'));
            $periode_ke = (int)($_POST['periode_ke'] ?? 1);
            $selected_employees = $_POST['employees'] ?? [];

            if (empty($selected_employees)) {
                set_flash_message('error', 'Gagal', 'Pilih minimal satu karyawan untuk memproses gaji.');
                redirect("index.php?page=penggajian&tahun=$tahun&bulan=$bulan&periode_ke=$periode_ke");
            }

            $success_count = 0;
            $tanggal_payroll = date('Y-m-d');

            foreach ($selected_employees as $id_karyawan) {
                $id_karyawan = (int)$id_karyawan;
                
                // Check if already exists
                if ($this->penggajianModel->checkExists($id_karyawan, $tahun, $bulan, $periode_ke)) {
                    continue; // skip
                }

                // Calculate fresh values on the backend
                $calc = $this->penggajianModel->calculateEmployeePayroll($id_karyawan, $tahun, $bulan, $periode_ke);
                if ($calc) {
                    $calc['periode_tahun'] = $tahun;
                    $calc['periode_bulan'] = $bulan;
                    $calc['periode_ke'] = $periode_ke;
                    $calc['tanggal_payroll'] = $tanggal_payroll;
                    
                    try {
                        $this->penggajianModel->savePayroll($calc);
                        $success_count++;
                    } catch (Exception $e) {
                        // log error, continue
                    }
                }
            }

            if ($success_count > 0) {
                set_flash_message('success', 'Berhasil', "Berhasil memproses dan menyimpan gaji $success_count karyawan.");
            } else {
                set_flash_message('warning', 'Info', 'Tidak ada gaji baru yang disimpan (kemungkinan sudah disimpan sebelumnya).');
            }

            redirect("index.php?page=penggajian&tahun=$tahun&bulan=$bulan&periode_ke=$periode_ke");
        }
    }

    // Rollback / delete a saved payroll
    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $redirect = sanitize($_GET['redirect'] ?? 'penggajian');
        
        $payroll = $this->penggajianModel->getById($id);
        if (!$payroll) {
            set_flash_message('error', 'Gagal', 'Data penggajian tidak ditemukan.');
            redirect('index.php?page=penggajian');
        }

        $tahun = $payroll['periode_tahun'];
        $bulan = $payroll['periode_bulan'];
        $periode_ke = $payroll['periode_ke'];
        
        $urlRedirect = ($redirect === 'laporan') 
            ? "index.php?page=laporan&action=penggajian&tahun=$tahun&bulan=$bulan&periode_ke=$periode_ke" 
            : "index.php?page=penggajian&tahun=$tahun&bulan=$bulan&periode_ke=$periode_ke";

        try {
            $this->penggajianModel->deletePayroll($id);
            set_flash_message('success', 'Berhasil', 'Data penggajian berhasil dihapus (piutang terkait dikembalikan ke status belum lunas).');
        } catch (Exception $e) {
            set_flash_message('error', 'Gagal', 'Gagal menghapus data penggajian.');
        }

        redirect($urlRedirect);
    }

    // Render single slip view
    public function slip() {
        $id = (int)($_GET['id'] ?? 0);
        $payroll = $this->penggajianModel->getById($id);

        if (!$payroll) {
            die("Slip gaji tidak ditemukan.");
        }

        // Render standalone slip view (no sidebar/navbar)
        require_once dirname(__DIR__) . '/views/penggajian/slip.php';
    }

    // Render all slips print view for the period or selected IDs
    public function slip_all() {
        $tahun = (int)($_REQUEST['tahun'] ?? date('Y'));
        $bulan = (int)($_REQUEST['bulan'] ?? date('m'));
        $periode_ke = (int)($_REQUEST['periode_ke'] ?? 1);

        $selected_ids = $_REQUEST['payrolls'] ?? [];
        if (is_string($selected_ids)) $selected_ids = explode(',', $selected_ids);
        $selected_ids = array_map('intval', array_filter((array)$selected_ids));

        if (!empty($selected_ids)) {
            $payrolls = $this->penggajianModel->getByIds($selected_ids);
        } else {
            $filters = ['tahun' => $tahun, 'bulan' => $bulan, 'periode_ke' => $periode_ke];
            $payrolls = $this->penggajianModel->getAll($filters);
        }

        if (empty($payrolls)) {
            die("Tidak ada slip gaji yang dipilih atau ditemukan pada periode ini.");
        }

        require_once dirname(__DIR__) . '/views/penggajian/slip_all.php';
    }

    // Download all slips as a ZIP archive of individual HTML files
    public function zip_all() {
        $tahun = (int)($_REQUEST['tahun'] ?? date('Y'));
        $bulan = (int)($_REQUEST['bulan'] ?? date('m'));
        $periode_ke = (int)($_REQUEST['periode_ke'] ?? 1);

        $selected_ids = $_REQUEST['payrolls'] ?? [];
        if (is_string($selected_ids)) $selected_ids = explode(',', $selected_ids);
        $selected_ids = array_map('intval', array_filter((array)$selected_ids));

        if (!empty($selected_ids)) {
            $payrolls = $this->penggajianModel->getByIds($selected_ids);
        } else {
            $filters = ['tahun' => $tahun, 'bulan' => $bulan, 'periode_ke' => $periode_ke];
            $payrolls = $this->penggajianModel->getAll($filters);
        }

        if (empty($payrolls)) {
            set_flash_message('error', 'Gagal', 'Tidak ada slip gaji terpilih pada periode ini.');
            redirect("index.php?page=penggajian&tahun=$tahun&bulan=$bulan&periode_ke=$periode_ke");
        }

        // Base64 encode logo.png agar dapat dibuka offline
        $logoPath = dirname(__DIR__) . '/assets/images/logo.png';
        $logoSrc  = '';
        if (file_exists($logoPath)) {
            $logoData = base64_encode(file_get_contents($logoPath));
            $logoSrc  = 'data:image/png;base64,' . $logoData;
        }

        // Create Zip
        $zip     = new ZipArchive();
        $zipName = tempnam(sys_get_temp_dir(), 'payroll_zip_');

        if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            die("Gagal membuat file ZIP.");
        }

        $bulan_nama = format_bulan($bulan);
 
        // Pastikan tidak ada output yang tersisa sebelum loop
        while (ob_get_level()) {
            ob_end_clean();
        }
 
        $usedFilenames = [];

        // Cari binary wkhtmltopdf (coba lokasi umum di Windows lalu PATH)
        $possibleWk = [
            'C:\\Program Files\\wkhtmltopdf\\bin\\wkhtmltopdf.exe',
            'C:\\Program Files (x86)\\wkhtmltopdf\\bin\\wkhtmltopdf.exe',
            'wkhtmltopdf'
        ];
        $wkBinary = null;
        foreach ($possibleWk as $p) {
            $checkCmd = escapeshellcmd($p) . ' --version 2>&1';
            @exec($checkCmd, $outCheck, $retCheck);
            if ($retCheck === 0 || (is_array($outCheck) && count($outCheck) && stripos(implode("\n", $outCheck), 'wkhtmltopdf') !== false)) {
                $wkBinary = $p;
                break;
            }
        }

        if ($wkBinary === null) {
            // Jika wkhtmltopdf tidak ada, fallback: tambahkan HTML seperti sebelumnya
            foreach ($payrolls as $payroll) {
                ob_start();
                include dirname(__DIR__) . '/views/penggajian/slip.php';
                $html = ob_get_clean();

                // Ganti path logo dengan base64 agar bisa dibuka offline
                if (!empty($logoSrc)) {
                    $html = str_replace('src="assets/images/logo.png"',  'src="'  . $logoSrc . '"', $html);
                    $html = str_replace('href="assets/images/logo.png"', 'href="' . $logoSrc . '"', $html);
                }

                // Hapus tombol Print/Tutup yang tidak berguna di file offline
                $html = preg_replace('/<div[^>]*class="[^"]*no-print[^"]*"[^>]*>.*?<\/div>/si', '', $html);

                // Nama file: "Slip gaji (Nama Karyawan) (Bulan) (Periode X).html"
                $nama_bersih = preg_replace('/[^A-Za-z0-9_\-\(\) ]/', '', $payroll['nama_lengkap']);
                $filenameBase = "Slip gaji ({$nama_bersih}) ({$bulan_nama}) (Periode {$payroll['periode_ke']})";
                $filename = $filenameBase . '.html';

                if (isset($usedFilenames[$filename])) {
                    $usedFilenames[$filename]++;
                    $filename = $filenameBase . ' (' . $usedFilenames[$filename] . ').html';
                } else {
                    $usedFilenames[$filename] = 0;
                }

                $zip->addFromString($filename, $html);
            }
        } else {
            // Ada wkhtmltopdf: konversi tiap slip ke PDF lalu tambahkan ke ZIP
            foreach ($payrolls as $payroll) {
                ob_start();
                include dirname(__DIR__) . '/views/penggajian/slip.php';
                $html = ob_get_clean();

                // Ganti path logo dengan base64 agar bisa dibuka offline
                if (!empty($logoSrc)) {
                    $html = str_replace('src="assets/images/logo.png"',  'src="'  . $logoSrc . '"', $html);
                    $html = str_replace('href="assets/images/logo.png"', 'href="' . $logoSrc . '"', $html);
                }

                // Hapus tombol Print/Tutup yang tidak berguna di file offline
                $html = preg_replace('/<div[^>]*class="[^"]*no-print[^"]*"[^>]*>.*?<\/div>/si', '', $html);

                // Nama file: "Slip gaji (Nama Karyawan) (Bulan) (Periode X).pdf"
                $nama_bersih = preg_replace('/[^A-Za-z0-9_\-\(\) ]/', '', $payroll['nama_lengkap']);
                $filenameBase = "Slip gaji ({$nama_bersih}) ({$bulan_nama}) (Periode {$payroll['periode_ke']})";
                $filename = $filenameBase . '.pdf';

                if (isset($usedFilenames[$filename])) {
                    $usedFilenames[$filename]++;
                    $filename = $filenameBase . ' (' . $usedFilenames[$filename] . ').pdf';
                } else {
                    $usedFilenames[$filename] = 0;
                }

                // Simpan HTML sementara dan konversi
                $tmpHtml = tempnam(sys_get_temp_dir(), 'slip_html_') . '.html';
                file_put_contents($tmpHtml, $html);
                $tmpPdf  = tempnam(sys_get_temp_dir(), 'slip_pdf_') . '.pdf';

                $cmd = escapeshellcmd($wkBinary) . ' --enable-local-file-access --print-media-type ' . escapeshellarg($tmpHtml) . ' ' . escapeshellarg($tmpPdf) . ' 2>&1';
                @exec($cmd, $out, $ret);

                if ($ret === 0 && file_exists($tmpPdf)) {
                    // tambahkan file PDF ke ZIP (menggunakan path file agar stream lebih efisien)
                    $zip->addFile($tmpPdf, $filename);
                    @unlink($tmpPdf);
                    @unlink($tmpHtml);
                } else {
                    // jika gagal, fallback: tambahkan HTML
                    $zip->addFromString($filenameBase . '.html', $html);
                    @unlink($tmpPdf);
                    @unlink($tmpHtml);
                }
            }
        }

        $zip->close();

        // Kirim file ZIP ke browser
        $archive_name = "Slip_Gaji_Periode_{$periode_ke}_{$bulan_nama}_{$tahun}.zip";
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=\"$archive_name\"");
        header("Content-Length: " . filesize($zipName));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile($zipName);
        unlink($zipName);
        exit();
    }

    // Generate PDF/Print view for selected or all slips
    public function pdf_all() {
        $this->slip_all();
    }

    // Serve temporary download file created by pdf_all
    public function download_tmp() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $token = $_GET['token'] ?? '';
        $file  = $_GET['file'] ?? '';
        if (empty($token) || empty($file) || !isset($_SESSION['payroll_exports'][$token])) {
            http_response_code(404);
            die('File tidak ditemukan.');
        }
        $map = $_SESSION['payroll_exports'][$token];
        $dir = $map['dir'];
        if (!in_array($file, $map['files'])) {
            http_response_code(403);
            die('Akses dilarang.');
        }
        $filepath = $dir . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($filepath)) {
            http_response_code(404);
            die('File tidak ditemukan.');
        }
        // Kirim header sesuai tipe
        $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            header('Content-Type: application/pdf');
        } elseif ($ext === 'html' || $ext === 'htm') {
            header('Content-Type: text/html');
        } else {
            header('Content-Type: application/octet-stream');
        }
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        // optional: do not delete immediately; allow multiple downloads
        exit();
    }

    // Export Payroll Summary into Excel
    public function excel() {
        $tahun = (int)($_GET['tahun'] ?? date('Y'));
        $bulan = (int)($_GET['bulan'] ?? date('m'));
        $periode_ke = (int)($_GET['periode_ke'] ?? 1);

        $filters = [
            'tahun' => $tahun,
            'bulan' => $bulan,
            'periode_ke' => $periode_ke
        ];
        $data = $this->penggajianModel->getAll($filters);

        $nama_bulan = format_bulan($bulan);
        $filename = "Rekap_Gaji_Periode_{$periode_ke}_{$nama_bulan}_{$tahun}.xls";

        // Send headers for Excel attachment
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$filename");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Write HTML table which Excel will parse beautifully
        echo "<h3>REKAP GAJI KARYAWAN</h3>";
        echo "<h4>Periode Ke: $periode_ke | Bulan: $nama_bulan | Tahun: $tahun</h4>";
        echo "<table border='1'>";
        echo "<thead>
                 <tr style='background-color: #dc2626; color: #fff;'>
                    <th>No</th>
                    <th>NIK</th>
                    <th>Nama Lengkap</th>
                    <th>Jabatan</th>
                    <th>Divisi</th>
                    <th>Gaji Pokok (Reguler)</th>
                    <th>Gaji Harian</th>
                    <th>Hari Kerja</th>
                    <th>Hadir</th>
                    <th>Setengah Hari</th>
                    <th>Izin</th>
                    <th>Sakit</th>
                    <th>Alpha</th>
                    <th>Hari Proyek</th>
                    <th>Gaji Proyek</th>
                    <th>Lembur</th>
                    <th>Piutang</th>
                    <th>Pot. Lainnya</th>
                    <th>Gaji Bersih</th>
                </tr>
              </thead>
              <tbody>";
        
        $no = 1;
        $total_bersih = 0;
        foreach ($data as $row) {
            $total_bersih += $row['gaji_bersih'];
            echo "<tr>
                    <td>{$no}</td>
                    <td>'{$row['nik']}</td>
                    <td>{$row['nama_lengkap']}</td>
                    <td>{$row['nama_jabatan']}</td>
                    <td>{$row['nama_divisi']}</td>
                    <td>" . (int)$row['gaji_pokok'] . "</td>
                    <td>" . (int)$row['gaji_harian'] . "</td>
                    <td>{$row['jumlah_hari_kerja']}</td>
                    <td>" . number_format($row['total_hadir'], 1) . "</td>
                    <td>{$row['total_setengah_hari']}</td>
                    <td>{$row['total_izin']}</td>
                    <td>{$row['total_sakit']}</td>
                    <td>{$row['total_alpha']}</td>
                    <td>{$row['total_kerja_proyek']}</td>
                    <td>" . (int)($row['gaji_proyek'] ?? 0) . "</td>
                    <td>" . (int)$row['total_lembur'] . "</td>
                    <td>" . (int)$row['total_piutang'] . "</td>
                    <td>" . (int)($row['potongan_lainnya'] ?? 0) . "</td>
                    <td>" . (int)$row['gaji_bersih'] . "</td>
                  </tr>";
            $no++;
        }
        
        echo "<tr>
                <td colspan='18' style='text-align: right; font-weight: bold;'>TOTAL PENGELUARAN GAJI</td>
                <td style='font-weight: bold;'>" . (int)$total_bersih . "</td>
              </tr>";
        echo "</tbody></table>";
        exit();
    }
}
