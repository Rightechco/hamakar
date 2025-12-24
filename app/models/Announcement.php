<?php
// app/models/Announcement.php

class Announcement {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function create($data) {
        $this->db->query('INSERT INTO announcements (title, body, target_roles, project_id) VALUES (:title, :body, :target_roles, :project_id)');
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':body', $data['body']);
        $this->db->bind(':target_roles', $data['target_roles']);
        $this->db->bind(':project_id', $data['project_id'] ?? null); // ✅ اضافه شدن
        return $this->db->execute();
    }
    
    public function update($id, $data) {
        $this->db->query('UPDATE announcements SET title = :title, body = :body, target_roles = :target_roles, project_id = :project_id WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':body', $data['body']);
        $this->db->bind(':target_roles', $data['target_roles']);
        $this->db->bind(':project_id', $data['project_id'] ?? null); // ✅ اضافه شدن
        return $this->db->execute();
    }
    
    public function delete($id) {
        $this->db->query('DELETE FROM announcements WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function getAll() {
        $this->db->query('SELECT a.*, p.name as project_name FROM announcements a LEFT JOIN projects p ON a.project_id = p.id ORDER BY created_at DESC');
        return $this->db->fetchAll();
    }
    
    public function findById($id) {
        $this->db->query('SELECT a.*, p.name as project_name FROM announcements a LEFT JOIN projects p ON a.project_id = p.id WHERE a.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    
    /**
     * ✅ متد جدید: دریافت اطلاعیه‌های مربوط به یک پروژه
     * @param int $projectId
     * @return array
     */
    public function getAnnouncementsForProject($projectId) {
        $this->db->query("SELECT * FROM announcements WHERE project_id = :project_id OR target_roles = 'all' ORDER BY created_at DESC");
        $this->db->bind(':project_id', $projectId);
        return $this->db->fetchAll();
    }

    /**
     * دریافت اطلاعیه‌های عمومی مرتبط با نقش کاربر
     */
    public function getAnnouncementsForRole($role) {
        $this->db->query("SELECT * FROM announcements WHERE project_id IS NULL AND (target_roles = 'all' OR FIND_IN_SET(:role, target_roles)) ORDER BY created_at DESC LIMIT 5");
        $this->db->bind(':role', $role);
        return $this->db->fetchAll();
    }
}