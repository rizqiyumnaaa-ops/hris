<?php
/**
 * Absensi Model
 */

require_once __DIR__ . '/BaseModel.php';

class Absensi extends BaseModel {
    
    public function getByDate($tanggal, $filters = []) {
        $sql = "SELECT k.id as id_karyawan, k.nik, k.nama_lengkap, j.nama_jabatan, d.nama_divisi, 
                       a.id as id_absensi, a.status_absensi, a.keterangan, a.jam_absen
                FROM karyawan k
                JOIN jabatan j ON k.id_jabatan = j.id
                JOIN divisi d ON k.id_divisi = d.id
                LEFT JOIN absensi a ON k.id = a.id_karyawan AND a.tanggal = ?";
        
        $where = [];
        $params = [$tanggal];
        
        // Show active employees OR those who already have attendance records on this date
        $where[] = "(k.status_aktif = 1 OR a.id IS NOT NULL)";
        
        if (!empty($filters['search'])) {
            $where[] = "(k.nama_lengkap LIKE ? OR k.nik LIKE ?)";
            $params[] = "%" . $filters['search'] . "%";
            $params[] = "%" . $filters['search'] . "%";
        }
        
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin_divisi') {
            $where[] = "k.id_divisi = ?";
            $params[] = $_SESSION['id_divisi'];
        } elseif (!empty($filters['id_divisi'])) {
            $where[] = "k.id_divisi = ?";
            $params[] = $filters['id_divisi'];
        }
        
        if (!empty($filters['id_jabatan'])) {
            $where[] = "k.id_jabatan = ?";
            $params[] = $filters['id_jabatan'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY k.nik ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function saveBulk($tanggal, $records) {
        $this->db->beginTransaction();
        try {
            $stmt_check = $this->db->prepare("SELECT id FROM absensi WHERE id_karyawan = ? AND tanggal = ?");
            $stmt_insert = $this->db->prepare("INSERT INTO absensi (id_karyawan, tanggal, status_absensi, keterangan, jam_absen) VALUES (?, ?, ?, ?, ?)");
            $stmt_update = $this->db->prepare("UPDATE absensi SET status_absensi = ?, keterangan = ?, jam_absen = ? WHERE id_karyawan = ? AND tanggal = ?");
            
            foreach ($records as $r) {
                $id_karyawan = $r['id_karyawan'];
                $status = $r['status_absensi'];
                $keterangan = !empty($r['keterangan']) ? $r['keterangan'] : null;
                // If it is an update and jam_absen is empty, keep current time or fetch existing. Let's set it to current time if new, or keep original if updating.
                $jam_absen = !empty($r['jam_absen']) ? $r['jam_absen'] : date('H:i:s');
                
                $stmt_check->execute([$id_karyawan, $tanggal]);
                $existing = $stmt_check->fetch();
                
                if ($existing) {
                    $stmt_update->execute([$status, $keterangan, $jam_absen, $id_karyawan, $tanggal]);
                } else {
                    $stmt_insert->execute([$id_karyawan, $tanggal, $status, $keterangan, $jam_absen]);
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    public function getStatsByDate($tanggal, $id_divisi = null) {
        if ($id_divisi) {
            $stmt = $this->db->prepare("
                SELECT 
                    SUM(CASE WHEN a.status_absensi = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN a.status_absensi = 'Setengah Hari' THEN 1 ELSE 0 END) as setengah_hari,
                    SUM(CASE WHEN a.status_absensi = 'Izin' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN a.status_absensi = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN a.status_absensi = 'Alpha' THEN 1 ELSE 0 END) as alpha,
                    SUM(CASE WHEN a.status_absensi = 'Kerja Proyek' THEN 1 ELSE 0 END) as kerja_proyek
                FROM absensi a
                JOIN karyawan k ON a.id_karyawan = k.id
                WHERE a.tanggal = ? AND k.id_divisi = ?
            ");
            $stmt->execute([$tanggal, $id_divisi]);
        } else {
            $stmt = $this->db->prepare("
                SELECT 
                    SUM(CASE WHEN status_absensi = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN status_absensi = 'Setengah Hari' THEN 1 ELSE 0 END) as setengah_hari,
                    SUM(CASE WHEN status_absensi = 'Izin' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN status_absensi = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN status_absensi = 'Alpha' THEN 1 ELSE 0 END) as alpha,
                    SUM(CASE WHEN status_absensi = 'Kerja Proyek' THEN 1 ELSE 0 END) as kerja_proyek
                FROM absensi
                WHERE tanggal = ?
            ");
            $stmt->execute([$tanggal]);
        }
        $res = $stmt->fetch();
        return [
            'hadir' => $res['hadir'] ?? 0,
            'setengah_hari' => $res['setengah_hari'] ?? 0,
            'izin' => $res['izin'] ?? 0,
            'sakit' => $res['sakit'] ?? 0,
            'alpha' => $res['alpha'] ?? 0,
            'kerja_proyek' => $res['kerja_proyek'] ?? 0
        ];
    }
    
    public function getSummaryByPeriod($id_karyawan, $start_date, $end_date) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(CASE WHEN status_absensi = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status_absensi = 'Setengah Hari' THEN 1 ELSE 0 END) as setengah_hari,
                SUM(CASE WHEN status_absensi = 'Izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status_absensi = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status_absensi = 'Alpha' THEN 1 ELSE 0 END) as alpha,
                SUM(CASE WHEN status_absensi = 'Kerja Proyek' THEN 1 ELSE 0 END) as kerja_proyek
            FROM absensi
            WHERE id_karyawan = ? AND tanggal BETWEEN ? AND ?
        ");
        $stmt->execute([$id_karyawan, $start_date, $end_date]);
        $res = $stmt->fetch();
        
        return [
            'hadir' => $res['hadir'] ?? 0,
            'setengah_hari' => $res['setengah_hari'] ?? 0,
            'izin' => $res['izin'] ?? 0,
            'sakit' => $res['sakit'] ?? 0,
            'alpha' => $res['alpha'] ?? 0,
            'kerja_proyek' => $res['kerja_proyek'] ?? 0
        ];
    }
    
    // For dashboard attendance chart (last 15 days trend)
    public function getTrendLast15Days($id_divisi = null) {
        if ($id_divisi) {
            $stmt = $this->db->prepare("
                SELECT a.tanggal,
                    SUM(CASE WHEN a.status_absensi = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN a.status_absensi = 'Setengah Hari' THEN 1 ELSE 0 END) as setengah_hari,
                    SUM(CASE WHEN a.status_absensi = 'Izin' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN a.status_absensi = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN a.status_absensi = 'Alpha' THEN 1 ELSE 0 END) as alpha,
                    SUM(CASE WHEN a.status_absensi = 'Kerja Proyek' THEN 1 ELSE 0 END) as kerja_proyek
                FROM absensi a
                JOIN karyawan k ON a.id_karyawan = k.id
                WHERE k.id_divisi = ?
                GROUP BY a.tanggal
                ORDER BY a.tanggal DESC
                LIMIT 15
            ");
            $stmt->execute([$id_divisi]);
        } else {
            $stmt = $this->db->query("
                SELECT tanggal,
                    SUM(CASE WHEN status_absensi = 'Hadir' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN status_absensi = 'Setengah Hari' THEN 1 ELSE 0 END) as setengah_hari,
                    SUM(CASE WHEN status_absensi = 'Izin' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN status_absensi = 'Sakit' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN status_absensi = 'Alpha' THEN 1 ELSE 0 END) as alpha,
                    SUM(CASE WHEN status_absensi = 'Kerja Proyek' THEN 1 ELSE 0 END) as kerja_proyek
                FROM absensi
                GROUP BY tanggal
                ORDER BY tanggal DESC
                LIMIT 15
            ");
        }
        $results = $stmt->fetchAll();
        return array_reverse($results); // return chronological order
    }

    // Get attendance list report
    public function getReport($filters = []) {
        $sql = "SELECT a.*, k.nik, k.nama_lengkap, j.nama_jabatan, d.nama_divisi 
                FROM absensi a
                JOIN karyawan k ON a.id_karyawan = k.id
                JOIN jabatan j ON k.id_jabatan = j.id
                JOIN divisi d ON k.id_divisi = d.id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $where[] = "a.tanggal BETWEEN ? AND ?";
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
        } elseif (!empty($filters['tanggal'])) {
            $where[] = "a.tanggal = ?";
            $params[] = $filters['tanggal'];
        }
        
        if (!empty($filters['id_karyawan'])) {
            $where[] = "a.id_karyawan = ?";
            $params[] = $filters['id_karyawan'];
        }
        
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin_divisi') {
            $where[] = "k.id_divisi = ?";
            $params[] = $_SESSION['id_divisi'];
        } elseif (!empty($filters['id_divisi'])) {
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
        
        $sql .= " ORDER BY a.tanggal DESC, k.nik ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Ambil absensi satu karyawan untuk tanggal tertentu (untuk tampilan pegawai).
     * Return array dengan 1 row jika ada, atau array kosong jika belum diabsen.
     */
    public function getByDateAndKaryawan($tanggal, $id_karyawan) {
        $sql = "SELECT k.id as id_karyawan, k.nik, k.nama_lengkap, j.nama_jabatan, d.nama_divisi,
                       a.id as id_absensi, a.status_absensi, a.keterangan, a.jam_absen
                FROM karyawan k
                JOIN jabatan j ON k.id_jabatan = j.id
                JOIN divisi d ON k.id_divisi = d.id
                LEFT JOIN absensi a ON k.id = a.id_karyawan AND a.tanggal = ?
                WHERE k.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tanggal, $id_karyawan]);
        return $stmt->fetchAll();
    }

    /**
     * Ambil riwayat absensi satu karyawan dengan filter tanggal (untuk rekap pegawai).
     */
    public function getHistoryByKaryawan($id_karyawan, $bulan = null, $tahun = null) {
        $sql    = "SELECT a.*, k.nik, k.nama_lengkap, j.nama_jabatan, d.nama_divisi
                   FROM absensi a
                   JOIN karyawan k ON a.id_karyawan = k.id
                   JOIN jabatan j ON k.id_jabatan = j.id
                   JOIN divisi d ON k.id_divisi = d.id
                   WHERE a.id_karyawan = ?";
        $params = [$id_karyawan];

        if ($bulan && $tahun) {
            $sql   .= " AND MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?";
            $params[] = $bulan;
            $params[] = $tahun;
        }
        $sql .= " ORDER BY a.tanggal DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Update satu record absensi berdasarkan ID.
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE absensi SET status_absensi = ?, keterangan = ? WHERE id = ?"
        );
        return $stmt->execute([
            $data['status_absensi'],
            $data['keterangan'] ?? null,
            $id
        ]);
    }

    /**
     * Hapus satu record absensi berdasarkan ID.
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM absensi WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
