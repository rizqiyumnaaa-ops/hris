<?php
/**
 * Potongan Lainnya Model
 */

require_once __DIR__ . '/BaseModel.php';

class PotonganLainnya extends BaseModel {
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM potongan_lainnya ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    public function getActive() {
        $stmt = $this->db->query("SELECT * FROM potongan_lainnya WHERE status_aktif = 1");
        return $stmt->fetchAll();
    }
    
    public function getActiveSum() {
        $stmt = $this->db->query("SELECT SUM(nominal) as total FROM potongan_lainnya WHERE status_aktif = 1");
        $res = $stmt->fetch();
        return (float)($res['total'] ?? 0.00);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM potongan_lainnya WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO potongan_lainnya (nama_potongan, nominal, status_aktif) VALUES (?, ?, ?)");
        return $stmt->execute([
            $data['nama_potongan'],
            $data['nominal'],
            $data['status_aktif'] ?? 1
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE potongan_lainnya SET nama_potongan = ?, nominal = ?, status_aktif = ? WHERE id = ?");
        return $stmt->execute([
            $data['nama_potongan'],
            $data['nominal'],
            $data['status_aktif'],
            $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM potongan_lainnya WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function toggleStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE potongan_lainnya SET status_aktif = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
    
    public function getEmployeeIdsForPotongan($id_potongan) {
        $stmt = $this->db->prepare("SELECT id_karyawan FROM karyawan_potongan_lainnya WHERE id_potongan = ?");
        $stmt->execute([$id_potongan]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    public function saveEmployeeMappings($id_potongan, $employee_ids) {
        $this->db->beginTransaction();
        try {
            // Hapus semua pemetaan lama untuk potongan ini
            $stmtDel = $this->db->prepare("DELETE FROM karyawan_potongan_lainnya WHERE id_potongan = ?");
            $stmtDel->execute([$id_potongan]);
            
            // Masukkan pemetaan baru jika ada yang dipilih
            if (!empty($employee_ids)) {
                $stmtIns = $this->db->prepare("INSERT INTO karyawan_potongan_lainnya (id_potongan, id_karyawan) VALUES (?, ?)");
                foreach ($employee_ids as $emp_id) {
                    $stmtIns->execute([$id_potongan, (int)$emp_id]);
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function getActiveForEmployee($id_karyawan) {
        $stmt = $this->db->prepare("
            SELECT pl.* FROM potongan_lainnya pl
            JOIN karyawan_potongan_lainnya kpl ON pl.id = kpl.id_potongan
            WHERE kpl.id_karyawan = ? AND pl.status_aktif = 1
        ");
        $stmt->execute([$id_karyawan]);
        return $stmt->fetchAll();
    }
    
    public function getActiveSumForEmployee($id_karyawan) {
        $stmt = $this->db->prepare("
            SELECT SUM(pl.nominal) as total FROM potongan_lainnya pl
            JOIN karyawan_potongan_lainnya kpl ON pl.id = kpl.id_potongan
            WHERE kpl.id_karyawan = ? AND pl.status_aktif = 1
        ");
        $stmt->execute([$id_karyawan]);
        $res = $stmt->fetch();
        return (float)($res['total'] ?? 0.00);
    }
}
