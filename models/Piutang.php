<?php
/**
 * Piutang Model
 */

require_once __DIR__ . '/BaseModel.php';

class Piutang extends BaseModel {
    
    public function getAll($filters = []) {
        $sql = "SELECT p.*, k.nik, k.nama_lengkap, j.nama_jabatan, d.nama_divisi 
                FROM piutang p
                JOIN karyawan k ON p.id_karyawan = k.id
                JOIN jabatan j ON k.id_jabatan = j.id
                JOIN divisi d ON k.id_divisi = d.id";
        
        $where = [];
        $params = [];
        
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $where[] = "p.tanggal BETWEEN ? AND ?";
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
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
        
        if (isset($filters['status_piutang']) && $filters['status_piutang'] !== '') {
            $where[] = "p.status_piutang = ?";
            $params[] = $filters['status_piutang'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(k.nama_lengkap LIKE ? OR k.nik LIKE ?)";
            $params[] = "%" . $filters['search'] . "%";
            $params[] = "%" . $filters['search'] . "%";
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY p.tanggal DESC, k.nik ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, k.nama_lengkap, k.nik
            FROM piutang p
            JOIN karyawan k ON p.id_karyawan = k.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO piutang (id_karyawan, tanggal, nominal, keterangan, status_piutang) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['id_karyawan'],
            $data['tanggal'],
            $data['nominal'],
            $data['keterangan'],
            $data['status_piutang'] ?? 'Belum Lunas'
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE piutang 
            SET id_karyawan = ?, tanggal = ?, nominal = ?, keterangan = ?, status_piutang = ? 
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['id_karyawan'],
            $data['tanggal'],
            $data['nominal'],
            $data['keterangan'],
            $data['status_piutang'],
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM piutang WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    // Get total unpaid receivables for an employee in a period
    public function getUnpaidSumByPeriod($id_karyawan, $start_date, $end_date) {
        $stmt = $this->db->prepare("
            SELECT SUM(nominal) as total 
            FROM piutang 
            WHERE id_karyawan = ? 
              AND tanggal BETWEEN ? AND ? 
              AND status_piutang = 'Belum Lunas'
        ");
        $stmt->execute([$id_karyawan, $start_date, $end_date]);
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }
    
    // Get list of unpaid receivables in period
    public function getUnpaidListByPeriod($id_karyawan, $start_date, $end_date) {
        $stmt = $this->db->prepare("
            SELECT * FROM piutang 
            WHERE id_karyawan = ? 
              AND tanggal BETWEEN ? AND ? 
              AND status_piutang = 'Belum Lunas'
        ");
        $stmt->execute([$id_karyawan, $start_date, $end_date]);
        return $stmt->fetchAll();
    }
    
    // Link and mark as Lunas when payroll is processed
    public function settlePiutangInPayroll($id_karyawan, $start_date, $end_date, $id_penggajian) {
        $stmt = $this->db->prepare("
            UPDATE piutang 
            SET status_piutang = 'Lunas', id_penggajian = ? 
            WHERE id_karyawan = ? 
              AND tanggal BETWEEN ? AND ? 
              AND status_piutang = 'Belum Lunas'
        ");
        return $stmt->execute([$id_penggajian, $id_karyawan, $start_date, $end_date]);
    }
    
    // Reset piutang state if payroll is deleted
    public function resetSettleByPayroll($id_penggajian) {
        $stmt = $this->db->prepare("
            UPDATE piutang 
            SET status_piutang = 'Belum Lunas', id_penggajian = NULL 
            WHERE id_penggajian = ?
        ");
        return $stmt->execute([$id_penggajian]);
    }
    
    // Sum active/unpaid receivables for dashboard
    public function getSumActiveReceivables() {
        $stmt = $this->db->query("
            SELECT SUM(nominal) as total 
            FROM piutang 
            WHERE status_piutang = 'Belum Lunas'
        ");
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }
}
