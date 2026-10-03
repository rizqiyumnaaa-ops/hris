<?php
/**
 * User Model
 */

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {

    public function getByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAll() {
        $stmt = $this->db->query(
            "SELECT u.id, u.username, u.nama_lengkap, u.foto, u.role, u.karyawan_id, u.id_divisi, u.created_at,
                    k.nama_lengkap AS nama_karyawan, k.nik, d.nama_divisi
             FROM users u
             LEFT JOIN karyawan k ON u.karyawan_id = k.id
             LEFT JOIN divisi d ON u.id_divisi = d.id
             ORDER BY u.role ASC, u.id ASC"
        );
        return $stmt->fetchAll();
    }

    public function getAllAdmins() {
        $stmt = $this->db->query(
            "SELECT id, username, nama_lengkap, foto, role, id_divisi, created_at FROM users WHERE role IN ('admin_utama', 'admin_divisi') ORDER BY id ASC"
        );
        return $stmt->fetchAll();
    }

    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, password, nama_lengkap, foto, role, id_divisi, karyawan_id) VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['nama_lengkap'],
            $data['foto'] ?? null,
            $data['role'] ?? 'admin_divisi',
            $data['id_divisi'] ?? null,
            $data['karyawan_id'] ?? null,
        ]);
    }

    public function updateProfile($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE users SET username = ?, nama_lengkap = ? WHERE id = ?"
        );
        return $stmt->execute([$data['username'], $data['nama_lengkap'], $id]);
    }

    public function updatePhoto($id, $foto) {
        $stmt = $this->db->prepare("UPDATE users SET foto = ? WHERE id = ?");
        return $stmt->execute([$foto, $id]);
    }

    public function updatePassword($id, $new_password) {
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    public function isUsernameTaken($username, $exclude_id = null) {
        if ($exclude_id) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $exclude_id]);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
        }
        return $stmt->fetch() !== false;
    }

    public function isKaryawanLinked($karyawan_id, $exclude_id = null) {
        if ($exclude_id) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE karyawan_id = ? AND id != ?");
            $stmt->execute([$karyawan_id, $exclude_id]);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE karyawan_id = ?");
            $stmt->execute([$karyawan_id]);
        }
        return $stmt->fetch() !== false;
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
