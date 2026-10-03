<?php
/**
 * Lembur Model
 */

require_once __DIR__ . '/BaseModel.php';

class Lembur extends BaseModel {
    
    public function getAll($filters = []) {
        $sql = "SELECT l.*, k.nik, k.nama_lengkap, j.nama_jabatan, d.nama_divisi 
                FROM lembur l
                JOIN karyawan k ON l.id_karyawan = k.id
                JOIN jabatan j ON k.id_jabatan = j.id
                JOIN divisi d ON k.id_divisi = d.id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $where[] = "l.tanggal BETWEEN ? AND ?";
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
        }
        
        if (!empty($filters['id_karyawan'])) {
            $where[] = "l.id_karyawan = ?";
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
        
        $sql .= " ORDER BY l.tanggal DESC, k.nik ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT l.*, k.nama_lengkap, k.nik, k.tarif_lembur as rate_karyawan
            FROM lembur l
            JOIN karyawan k ON l.id_karyawan = k.id
            WHERE l.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO lembur (id_karyawan, tanggal, jumlah_jam, tarif_lembur, total_lembur, keterangan) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['id_karyawan'],
            $data['tanggal'],
            $data['jumlah_jam'],
            $data['tarif_lembur'],
            $data['total_lembur'],
            $data['keterangan']
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE lembur 
            SET id_karyawan = ?, tanggal = ?, jumlah_jam = ?, tarif_lembur = ?, total_lembur = ?, keterangan = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['id_karyawan'],
            $data['tanggal'],
            $data['jumlah_jam'],
            $data['tarif_lembur'],
            $data['total_lembur'],
            $data['keterangan'],
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM lembur WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getSumByPeriod($id_karyawan, $start_date, $end_date) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(jumlah_jam) as total_jam,
                SUM(total_lembur) as total_nominal
            FROM lembur
            WHERE id_karyawan = ? AND tanggal BETWEEN ? AND ?
        ");
        $stmt->execute([$id_karyawan, $start_date, $end_date]);
        $res = $stmt->fetch();
        return [
            'total_jam' => $res['total_jam'] ?? 0,
            'total_nominal' => $res['total_nominal'] ?? 0
        ];
    }
    
    public function getSumActivePeriod($start_date, $end_date, $id_divisi = null) {
        if ($id_divisi) {
            $stmt = $this->db->prepare("
                SELECT SUM(l.total_lembur) as total
                FROM lembur l
                JOIN karyawan k ON l.id_karyawan = k.id
                WHERE (l.tanggal BETWEEN ? AND ?) AND k.id_divisi = ?
            ");
            $stmt->execute([$start_date, $end_date, $id_divisi]);
        } else {
            $stmt = $this->db->prepare("
                SELECT SUM(total_lembur) as total
                FROM lembur
                WHERE tanggal BETWEEN ? AND ?
            ");
            $stmt->execute([$start_date, $end_date]);
        }
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }
}
