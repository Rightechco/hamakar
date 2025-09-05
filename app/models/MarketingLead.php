<?php
// app/models/MarketingLead.php

require_once __DIR__ . '/../core/Database.php';

class MarketingLead {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getMapsLeads() {
        $this->db->query("SELECT * FROM Maps_data ORDER BY id DESC");
        return $this->db->fetchAll();
    }
    
    public function getNiazroozLeads() {
        $this->db->query("SELECT * FROM niazerooz_data ORDER BY id DESC");
        return $this->db->fetchAll();
    }

    // ✅ متدهای جدید برای نشان
    public function getNeshanLeads() {
        $this->db->query("SELECT * FROM neshan_data ORDER BY id DESC");
        return $this->db->fetchAll();
    }
    
    public function deleteMapsLead($id) {
        $this->db->query("DELETE FROM Maps_data WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function deleteNiazroozLead($id) {
        $this->db->query("DELETE FROM niazerooz_data WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // ✅ متد حذف لید نشان
    public function deleteNeshanLead($id) {
        $this->db->query("DELETE FROM neshan_data WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // ✅ متدهای جدید با قابلیت صفحه بندی
    public function getMapsLeadsPaginated($page_number, $records_per_page = 20) {
        $offset = ($page_number - 1) * $records_per_page;
        $this->db->query("SELECT * FROM Maps_data ORDER BY id DESC LIMIT :records_per_page OFFSET :offset");
        $this->db->bind(':records_per_page', $records_per_page);
        $this->db->bind(':offset', $offset);
        return $this->db->fetchAll();
    }

    public function countMapsLeads() {
        $this->db->query("SELECT COUNT(*) as count FROM Maps_data");
        $result = $this->db->fetch();
        return $result->count ?? 0;
    }
    
    public function getNiazroozLeadsPaginated($page_number, $records_per_page = 20) {
        $offset = ($page_number - 1) * $records_per_page;
        $this->db->query("SELECT * FROM niazerooz_data ORDER BY id DESC LIMIT :records_per_page OFFSET :offset");
        $this->db->bind(':records_per_page', $records_per_page);
        $this->db->bind(':offset', $offset);
        return $this->db->fetchAll();
    }
    
    public function countNiazroozLeads() {
        $this->db->query("SELECT COUNT(*) as count FROM niazerooz_data");
        $result = $this->db->fetch();
        return $result->count ?? 0;
    }

    public function getNeshanLeadsPaginated($page_number, $records_per_page = 20) {
        $offset = ($page_number - 1) * $records_per_page;
        $this->db->query("SELECT * FROM neshan_data ORDER BY id DESC LIMIT :records_per_page OFFSET :offset");
        $this->db->bind(':records_per_page', $records_per_page);
        $this->db->bind(':offset', $offset);
        return $this->db->fetchAll();
    }
    
    public function countNeshanLeads() {
        $this->db->query("SELECT COUNT(*) as count FROM neshan_data");
        $result = $this->db->fetch();
        return $result->count ?? 0;
    }

}