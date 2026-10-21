<?php
class Plan {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // دریافت همه پلن‌ها
    public function getPlans() {
        $this->db->query("SELECT * FROM plans ORDER BY is_featured DESC, price ASC");
        return $this->db->fetchAll();
    }

    // دریافت پلن‌های فعال
    public function getActivePlans() {
        $this->db->query("SELECT * FROM plans WHERE is_active = 1 ORDER BY is_featured DESC, price ASC");
        return $this->db->fetchAll();
    }

    // دریافت یک پلن خاص
    public function getPlanById($id) {
        $this->db->query("SELECT * FROM plans WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}