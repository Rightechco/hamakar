<?php
// app/models/MarketingLead.php

require_once __DIR__ . '/../core/Database.php';

class MarketingLead {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // ================== GOOGLE MAPS ==================
    public function getMapsLeads() {
        $this->db->query("SELECT * FROM Maps_data ORDER BY id DESC");
        return $this->db->fetchAll();
    }

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

    public function deleteMapsLead($id) {
        $this->db->query("DELETE FROM Maps_data WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    // ================== NIAZROOZ ==================
    public function getNiazroozLeads() {
        $this->db->query("SELECT * FROM niazerooz_data ORDER BY id DESC");
        return $this->db->fetchAll();
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

    public function deleteNiazroozLead($id) {
        $this->db->query("DELETE FROM niazerooz_data WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // ================== NESHAN ==================
    public function getNeshanLeads() {
        $this->db->query("SELECT * FROM neshan_data ORDER BY id DESC");
        return $this->db->fetchAll();
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

    public function deleteNeshanLead($id) {
        $this->db->query("DELETE FROM neshan_data WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // ================== DIVAR (NEW) ==================
    public function getDivarLeads() {
        $this->db->query("SELECT * FROM divar_data ORDER BY id DESC");
        return $this->db->fetchAll();
    }

    public function getDivarLeadsPaginated($page_number, $records_per_page = 20) {
        $offset = ($page_number - 1) * $records_per_page;
        $this->db->query("SELECT * FROM divar_data ORDER BY id DESC LIMIT :records_per_page OFFSET :offset");
        $this->db->bind(':records_per_page', $records_per_page);
        $this->db->bind(':offset', $offset);
        return $this->db->fetchAll();
    }

    public function countDivarLeads() {
        $this->db->query("SELECT COUNT(*) as count FROM divar_data");
        $result = $this->db->fetch();
        return $result->count ?? 0;
    }

    public function deleteDivarLead($id) {
        $this->db->query("DELETE FROM divar_data WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // ================== SHEYPOOR (NEW) ==================
    public function getSheypoorLeads() {
        $this->db->query("SELECT * FROM sheypoor_data ORDER BY id DESC");
        return $this->db->fetchAll();
    }

    public function getSheypoorLeadsPaginated($page_number, $records_per_page = 20) {
        $offset = ($page_number - 1) * $records_per_page;
        $this->db->query("SELECT * FROM sheypoor_data ORDER BY id DESC LIMIT :records_per_page OFFSET :offset");
        $this->db->bind(':records_per_page', $records_per_page);
        $this->db->bind(':offset', $offset);
        return $this->db->fetchAll();
    }

    public function countSheypoorLeads() {
        $this->db->query("SELECT COUNT(*) as count FROM sheypoor_data");
        $result = $this->db->fetch();
        return $result->count ?? 0;
    }

    public function deleteSheypoorLead($id) {
        $this->db->query("DELETE FROM sheypoor_data WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}