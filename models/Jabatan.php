<?php
/**
 * Jabatan Model
 */

require_once __DIR__ . '/BaseModel.php';

class Jabatan extends BaseModel {
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM jabatan ORDER BY nama_jabatan ASC");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM jabatan WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($nama_jabatan) {
        $stmt = $this->db->prepare("INSERT INTO jabatan (nama_jabatan) VALUES (?)");
        return $stmt->execute([$nama_jabatan]);
    }
    
    public function update($id, $nama_jabatan) {
        $stmt = $this->db->prepare("UPDATE jabatan SET nama_jabatan = ? WHERE id = ?");
        return $stmt->execute([$nama_jabatan, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM jabatan WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM jabatan");
        $res = $stmt->fetch();
        return $res['total'] ?? 0;
    }
}
