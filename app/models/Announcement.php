<?php
// app/models/Announcement.php

class Announcement {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($data) {
        $this->db->query('INSERT INTO announcements (title, body, target_roles) VALUES (:title, :body, :target_roles)');
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':body', $data['body']);
        $this->db->bind(':target_roles', $data['target_roles']);
        return $this->db->execute();
    }
    
    public function update($id, $data) {
        $this->db->query('UPDATE announcements SET title = :title, body = :body, target_roles = :target_roles WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':body', $data['body']);
        $this->db->bind(':target_roles', $data['target_roles']);
        return $this->db->execute();
    }
    
    public function delete($id) {
        $this->db->query('DELETE FROM announcements WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function getAll() {
        $this->db->query('SELECT * FROM announcements ORDER BY created_at DESC');
        return $this->db->fetchAll();
    }
    
    public function findById($id) {
        $this->db->query('SELECT * FROM announcements WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    
    /**
     * دریافت اطلاعیه‌های مرتبط با نقش کاربر
     */
    public function getAnnouncementsForRole($role) {
        $this->db->query("SELECT * FROM announcements WHERE target_roles = 'all' OR FIND_IN_SET(:role, target_roles) ORDER BY created_at DESC LIMIT 5");
        $this->db->bind(':role', $role);
        return $this->db->fetchAll();
    }
}