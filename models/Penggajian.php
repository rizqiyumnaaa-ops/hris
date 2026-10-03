<?php
/**
 * Penggajian Model
 */

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Absensi.php';
require_once __DIR__ . '/Lembur.php';
require_once __DIR__ . '/Piutang.php';
require_once __DIR__ . '/PotonganLainnya.php';
require_once dirname(__DIR__) . '/helpers/helpers.php';

class Penggajian extends BaseModel {
    
    public function getAll($filters = []) {
        $sql = "SELECT p.*, k.nik, k.nama_lengkap, j.nama_jabatan, d.nama_divisi 
                FROM penggajian p
                JOIN karyawan k ON p.id_karyawan = k.id
                JOIN jabatan j ON k.id_jabatan = j.id
                JOIN divisi d ON k.id_divisi = d.id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['tahun'])) {
            $where[] = "p.periode_tahun = ?";
            $params[] = $filters['tahun'];
        }
        
        if (!empty($filters['bulan'])) {
            $where[] = "p.periode_bulan = ?";
            $params[] = $filters['bulan'];
        }
        
        if (!empty($filters['periode_ke'])) {
            $where[] = "p.periode_ke = ?";
            $params[] = $filters['periode_ke'];
        }
        
        if (!empty($filters['id_karyawan'])) {
            $where[] = "p.id_karyawan = ?";
            $params[] = $filters['id_karyawan'];
        }
        
        if (!empty($filters['id_divisi'])) {
            $where[] = "k.id_divisi = ?";
            $params[] = $filters['id_divisi'];
        }
        
        if (!empty($filters['id_jabatan'])) {
            $where[] = "k.id_jabatan = ?";
            $params[] = $filters['id_jabatan'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(k.nama_lengkap LIKE ? OR k.nik LIKE ?)";
            $params[] = "%" . $filters['search'] . "%";
            $params[] = "%" . $filters['search'] . "%";
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY p.periode_tahun DESC, p.periode_bulan DESC, p.periode_ke DESC, k.nik ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, k.nik, k.nama_lengkap, k.alamat, k.no_hp, j.nama_jabatan, d.nama_divisi 
            FROM penggajian p
            JOIN karyawan k ON p.id_karyawan = k.id
            JOIN jabatan j ON k.id_jabatan = j.id
            JOIN divisi d ON k.id_divisi = d.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByIds($ids) {
        if (empty($ids)) return [];
        $ids = array_map('intval', array_filter((array)$ids));
        if (empty($ids)) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT p.*, k.nik, k.nama_lengkap, k.alamat, k.no_hp, j.nama_jabatan, d.nama_divisi 
                FROM penggajian p
                JOIN karyawan k ON p.id_karyawan = k.id
                JOIN jabatan j ON k.id_jabatan = j.id
                JOIN divisi d ON k.id_divisi = d.id
                WHERE p.id IN ($placeholders)
                ORDER BY k.nik ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($ids));
        return $stmt->fetchAll();
    }
    
    public function checkExists($id_karyawan, $tahun, $bulan, $periode_ke) {
        $stmt = $this->db->prepare("
            SELECT id FROM penggajian 
            WHERE id_karyawan = ? AND periode_tahun = ? AND periode_bulan = ? AND periode_ke = ?
        ");
        $stmt->execute([$id_karyawan, $tahun, $bulan, $periode_ke]);
        return $stmt->fetch();
    }
    
    // Save Payroll Record & Settle Piutang
    public function savePayroll($data) {
        $this->db->beginTransaction();
        try {
            // 1. Insert into penggajian
            $sql = "INSERT INTO penggajian (
                        id_karyawan, periode_tahun, periode_bulan, periode_ke, tanggal_payroll,
                        gaji_pokok, gaji_harian, jumlah_hari_kerja, total_hadir, total_setengah_hari,
                        total_izin, total_sakit, total_alpha, total_kerja_proyek, potongan_setengah_hari, potongan_alpha,
                        total_lembur, gaji_proyek, total_piutang, potongan_lainnya, gaji_bersih
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['id_karyawan'],
                $data['periode_tahun'],
                $data['periode_bulan'],
                $data['periode_ke'],
                $data['tanggal_payroll'],
                $data['gaji_pokok'],
                $data['gaji_harian'],
                $data['jumlah_hari_kerja'],
                $data['total_hadir'],
                $data['total_setengah_hari'],
                $data['total_izin'],
                $data['total_sakit'],
                $data['total_alpha'],
                $data['total_kerja_proyek'] ?? 0,
                $data['potongan_setengah_hari'] ?? 0.00,
                $data['potongan_alpha'] ?? 0.00,
                $data['total_lembur'],
                $data['gaji_proyek'] ?? 0.00,
                $data['total_piutang'],
                $data['potongan_lainnya'] ?? 0.00,
                $data['gaji_bersih']
            ]);
            
            $payroll_id = $this->db->lastInsertId();
            
            // 2. Mark piutang in this period as Lunas and link to this payroll
            $piutang_model = new Piutang();
            
            // Calculate date ranges
            $start_day = ($data['periode_ke'] == 1) ? 1 : 16;
            $end_day = ($data['periode_ke'] == 1) ? 15 : cal_days_in_month(CAL_GREGORIAN, $data['periode_bulan'], $data['periode_tahun']);
            
            $start_date = sprintf("%04d-%02d-%02d", $data['periode_tahun'], $data['periode_bulan'], $start_day);
            $end_date = sprintf("%04d-%02d-%02d", $data['periode_tahun'], $data['periode_bulan'], $end_day);
            
            $piutang_model->settlePiutangInPayroll($data['id_karyawan'], $start_date, $end_date, $payroll_id);
            
            $this->db->commit();
            return $payroll_id;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    // Delete Payroll & reset piutang
    public function deletePayroll($id) {
        $this->db->beginTransaction();
        try {
            // 1. Reset piutang linked to this payroll
            $piutang_model = new Piutang();
            $piutang_model->resetSettleByPayroll($id);
            
            // 2. Delete payroll record
            $stmt = $this->db->prepare("DELETE FROM penggajian WHERE id = ?");
            $stmt->execute([$id]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    // Calculate payroll parameters for a single employee (temporary simulation)
    public function calculateEmployeePayroll($id_karyawan, $tahun, $bulan, $periode_ke) {
        // Fetch Employee
        $stmt = $this->db->prepare("SELECT * FROM karyawan WHERE id = ?");
        $stmt->execute([$id_karyawan]);
        $karyawan = $stmt->fetch();
        if (!$karyawan) return null;
        
        // Define range
        $start_day = ($periode_ke == 1) ? 1 : 16;
        $end_day = ($periode_ke == 1) ? 15 : date('t', strtotime("$tahun-$bulan-01"));
        
        $start_date = sprintf("%04d-%02d-%02d", $tahun, $bulan, $start_day);
        $end_date = sprintf("%04d-%02d-%02d", $tahun, $bulan, $end_day);
        
        // 1. Working Days in period
        $working_days = get_working_days($start_date, $end_date);
        
        // Gaji Per Hari (gaji_pokok sudah merupakan gaji harian)
        $gaji_harian = (float)$karyawan['gaji_pokok'];
        
        // 2. Attendance Summary
        $absensi_model = new Absensi();
        $abs = $absensi_model->getSummaryByPeriod($id_karyawan, $start_date, $end_date);
        
        $total_hadir         = $abs['hadir'] + ($abs['setengah_hari'] * 0.5);
        $total_setengah_hari = $abs['setengah_hari'];
        $total_izin          = $abs['izin'];
        $total_sakit         = $abs['sakit'];
        $total_alpha         = $abs['alpha'];
        $total_kerja_proyek  = $abs['kerja_proyek'] ?? 0;
        
        // Deductions dari gaji harian
        $potongan_setengah_hari = 0.00; // Sudah terpotong otomatis di total_hadir
        $potongan_alpha         = 0.00; // Alpha tidak memotong gaji (hanya tidak terhitung hadir)
        
        // Gaji dasar (Reguler) = hari hadir reguler × gaji harian
        $gaji_dasar_reguler = $total_hadir * $gaji_harian;
        
        // Gaji Proyek = hari kerja proyek × Rp150.000
        $gaji_proyek = $total_kerja_proyek * 150000;
        
        // 3. Overtime
        $lembur_model = new Lembur();
        $lemb = $lembur_model->getSumByPeriod($id_karyawan, $start_date, $end_date);
        $total_lembur     = $lemb['total_nominal'];
        $total_jam_lembur = $lemb['total_jam'];
        
        // 4. Piutang
        $piutang_model = new Piutang();
        $total_piutang = $piutang_model->getUnpaidSumByPeriod($id_karyawan, $start_date, $end_date);
        
        // 5. Potongan Lainnya (Arisan, dll) - hanya yang diikuti oleh karyawan ini
        $potongan_model = new PotonganLainnya();
        $potongan_aktif = $potongan_model->getActiveForEmployee($id_karyawan);
        $total_potongan_lainnya = $potongan_model->getActiveSumForEmployee($id_karyawan);
        
        // Gaji Bersih = Gaji Reguler + Gaji Proyek + Lembur - Piutang - Potongan Lainnya
        $gaji_bersih = $gaji_dasar_reguler + $gaji_proyek + $total_lembur - $total_piutang - $total_potongan_lainnya;
        
        return [
            'id_karyawan'          => $karyawan['id'],
            'nik'                  => $karyawan['nik'],
            'nama_lengkap'         => $karyawan['nama_lengkap'],
            'gaji_pokok'           => $gaji_dasar_reguler,
            'gaji_harian'          => $gaji_harian,
            'jumlah_hari_kerja'    => $working_days,
            'total_hadir'          => $total_hadir,
            'total_setengah_hari'  => $total_setengah_hari,
            'total_izin'           => $total_izin,
            'total_sakit'          => $total_sakit,
            'total_alpha'          => $total_alpha,
            'total_kerja_proyek'   => $total_kerja_proyek,
            'potongan_setengah_hari' => $potongan_setengah_hari,
            'potongan_alpha'       => $potongan_alpha,
            'total_lembur'         => $total_lembur,
            'total_jam_lembur'     => $total_jam_lembur,
            'tarif_lembur'         => $karyawan['tarif_lembur'],
            'gaji_proyek'          => $gaji_proyek,
            'total_piutang'        => $total_piutang,
            'potongan_lainnya'     => $total_potongan_lainnya,
            'detail_potongan_lainnya' => $potongan_aktif,
            'gaji_bersih'          => $gaji_bersih
        ];
    }
    
    // Estimate total payroll cost for the current period
    public function getEstimatedPayrollCurrentPeriod() {
        // Find current period
        $today = date('Y-m-d');
        $day = (int)date('d');
        $tahun = (int)date('Y');
        $bulan = (int)date('m');
        $periode_ke = ($day <= 15) ? 1 : 2;
        
        // Get all active employees
        $stmt = $this->db->query("SELECT id FROM karyawan WHERE status_aktif = 1");
        $employees = $stmt->fetchAll();
        
        $total_estimate = 0;
        foreach ($employees as $emp) {
            $calc = $this->calculateEmployeePayroll($emp['id'], $tahun, $bulan, $periode_ke);
            if ($calc) {
                $total_estimate += $calc['gaji_bersih'];
            }
        }
        
        return $total_estimate;
    }
    
    // Get total payroll expenditure grouped by department
    public function getExpensesByDivisi() {
        $stmt = $this->db->query("
            SELECT d.nama_divisi, SUM(p.gaji_bersih) as total_gaji
            FROM penggajian p
            JOIN karyawan k ON p.id_karyawan = k.id
            JOIN divisi d ON k.id_divisi = d.id
            GROUP BY d.nama_divisi
        ");
        return $stmt->fetchAll();
    }
}
