<?php
/**
 * Dashboard Controller
 */

require_once dirname(__DIR__) . '/models/Karyawan.php';
require_once dirname(__DIR__) . '/models/Absensi.php';
require_once dirname(__DIR__) . '/models/Lembur.php';
require_once dirname(__DIR__) . '/models/Piutang.php';
require_once dirname(__DIR__) . '/models/Penggajian.php';

class DashboardController {
    private $karyawanModel;
    private $absensiModel;
    private $lemburModel;
    private $piutangModel;
    private $penggajianModel;

    public function __construct() {
        $this->karyawanModel = new Karyawan();
        $this->absensiModel = new Absensi();
        $this->lemburModel = new Lembur();
        $this->piutangModel = new Piutang();
        $this->penggajianModel = new Penggajian();
    }

    public function index() {
        // Calculate date ranges for the current running period
        $today = date('Y-m-d');
        $day = (int)date('d');
        $tahun = date('Y');
        $bulan = date('m');
        
        if ($day <= 15) {
            $start_period = "$tahun-$bulan-01";
            $end_period = "$tahun-$bulan-15";
            $periode_ke = 1;
        } else {
            $start_period = "$tahun-$bulan-16";
            $end_period = date('Y-m-t'); // last day of month
            $periode_ke = 2;
        }

        // Check if role is admin_divisi to apply division filters
        $id_divisi = is_admin_divisi() ? $_SESSION['id_divisi'] : null;

        // 1. Fetch Metrics (filtered by division if admin_divisi)
        $total_karyawan = $this->karyawanModel->countAllActive($id_divisi);
        
        $stats_today = $this->absensiModel->getStatsByDate($today, $id_divisi);
        
        $total_lembur = $this->lemburModel->getSumActivePeriod($start_period, $end_period, $id_divisi);
        
        // Only fetch payroll metrics if admin_utama
        $total_piutang = is_admin_utama() ? $this->piutangModel->getSumActiveReceivables() : 0.00;
        
        $estimasi_gaji = is_admin_utama() ? $this->penggajianModel->getEstimatedPayrollCurrentPeriod() : 0.00;

        // 2. Fetch Chart.js Datasets
        // Trend attendance last 15 days (filtered by division if admin_divisi)
        $attendance_trend = $this->absensiModel->getTrendLast15Days($id_divisi);
        
        // Department expenses doughnut data (only for admin_utama)
        $expenses_by_divisi = is_admin_utama() ? $this->penggajianModel->getExpensesByDivisi() : [];

        // Render views
        require_once dirname(__DIR__) . '/views/templates/header.php';
        require_once dirname(__DIR__) . '/views/dashboard.php';
        require_once dirname(__DIR__) . '/views/templates/footer.php';
    }
}
