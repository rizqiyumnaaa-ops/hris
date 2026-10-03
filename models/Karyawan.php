<?php
/**
 * Karyawan Model
 */

require_once __DIR__ . '/BaseModel.php';

class Karyawan extends BaseModel {
    
    public function getAll($filters = []) {
        $sql = "SELECT k.*, j.nama_jabatan, d.nama_divisi 
                FROM karyawan k
                JOIN jabatan j ON k.id_jabatan = j.id
                JOIN divisi d ON k.id_divisi = d.id";
        
        $where = [];
        $params = [];
        
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
        
        if (isset($filters['status_aktif']) && $filters['status_aktif'] !== '') {
            $where[] = "k.status_aktif = ?";
            $params[] = $filters['status_aktif'];
        }
        
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        
        $sql .= " ORDER BY k.nik ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT k.*, j.nama_jabatan, d.nama_divisi 
            FROM karyawan k
            JOIN jabatan j ON k.id_jabatan = j.id
            JOIN divisi d ON k.id_divisi = d.id
            WHERE k.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByNik($nik) {
        $stmt = $this->db->prepare("SELECT * FROM karyawan WHERE nik = ?");
        $stmt->execute([$nik]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $sql = "INSERT INTO karyawan 
                (nik, nama_lengkap, foto, id_jabatan, id_divisi, no_hp, alamat, tanggal_masuk, status_kerja, status_aktif, gaji_pokok, tarif_lembur) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nik'],
            $data['nama_lengkap'],
            $data['foto'],
            $data['id_jabatan'],
            $data['id_divisi'],
            $data['no_hp'],
            $data['alamat'],
            $data['tanggal_masuk'],
            $data['status_kerja'],
            $data['status_aktif'] ?? 1,
            $data['gaji_pokok'],
            $data['tarif_lembur']
        ]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE karyawan SET 
                nama_lengkap = ?, 
                id_jabatan = ?, 
                id_divisi = ?, 
                no_hp = ?, 
                alamat = ?, 
                tanggal_masuk = ?, 
                status_kerja = ?, 
                status_aktif = ?, 
                gaji_pokok = ?, 
                tarif_lembur = ?";
        
        $params = [
            $data['nama_lengkap'],
            $data['id_jabatan'],
            $data['id_divisi'],
            $data['no_hp'],
            $data['alamat'],
            $data['tanggal_masuk'],
            $data['status_kerja'],
            $data['status_aktif'],
            $data['gaji_pokok'],
            $data['tarif_lembur']
        ];
        
        if (isset($data['foto'])) {
            $sql .= ", foto = ?";
            $params[] = $data['foto'];
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM karyawan WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function toggleStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE karyawan SET status_aktif = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
    
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM karyawan");
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }
    
    public function countAllActive($id_divisi = null) {
        if ($id_divisi) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM karyawan WHERE status_aktif = 1 AND id_divisi = ?");
            $stmt->execute([$id_divisi]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM karyawan WHERE status_aktif = 1");
        }
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }
}
