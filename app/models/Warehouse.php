<?php
// app/models/Warehouse.php
class Warehouse {
    private $db;
    public function __construct() {
        $this->db = new Database();
    }
    public function getAll() {
        $this->db->query("SELECT * FROM warehouses ORDER BY name ASC");
        return $this->db->fetchAll();
    }
    public function create($data) {
        $this->db->query("INSERT INTO warehouses (name, location) VALUES (:name, :location)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':location', $data['location']);
        return $this->db->execute();
    }

    // ✅ متد جدید: یافتن یک انبار بر اساس شناسه
    public function findById($id) {
        $this->db->query("SELECT * FROM warehouses WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    
    // ✅ متد جدید: به‌روزرسانی یک انبار
    public function update($id, $data) {
        $this->db->query("UPDATE warehouses SET name = :name, location = :location WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':location', $data['location']);
        return $this->db->execute();
    }
    
    // ✅ متد جدید: حذف یک انبار
    public function delete($id) {
        $this->db->query("DELETE FROM warehouses WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}