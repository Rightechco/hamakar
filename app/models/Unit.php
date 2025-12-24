<?php
// app/models/Unit.php
class Unit {
    private $db;
    public function __construct() {
        $this->db = new Database();
    }
    public function getAll() {
        $this->db->query("SELECT * FROM units ORDER BY name ASC");
        return $this->db->fetchAll();
    }
    public function findById($id) {
        $this->db->query("SELECT * FROM units WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    public function findByName($name) {
        $this->db->query("SELECT * FROM units WHERE name = :name");
        $this->db->bind(':name', $name);
        return $this->db->fetch();
    }
    public function create($data) {
        if ($this->findByName($data['name'])) return false;
        $this->db->query("INSERT INTO units (name) VALUES (:name)");
        $this->db->bind(':name', $data['name']);
        return $this->db->execute();
    }
    
    // ✅ متد جدید: به‌روزرسانی یک واحد
    public function update($id, $data) {
        $this->db->query("UPDATE units SET name = :name WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        return $this->db->execute();
    }
    
    // ✅ متد جدید: حذف یک واحد
    public function delete($id) {
        $this->db->query("DELETE FROM units WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}