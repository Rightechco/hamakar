<?php
// app/models/Category.php

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }
    
    // ✅ جدید: متد برای یافتن دسته‌بندی بر اساس نام
    public function findByName($name) {
        $this->db->query("SELECT * FROM categories WHERE name = :name");
        $this->db->bind(':name', $name);
        return $this->db->fetch();
    }

    public function create($data) {
        // ✅ بررسی وجود نام تکراری قبل از ایجاد
        if ($this->findByName($data['name'])) {
            return false; // بازگشت false در صورت تکراری بودن نام
        }

        $this->db->query("INSERT INTO categories (name, type, description) VALUES (:name, :type, :description)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type'] ?? 'product');
        $this->db->bind(':description', $data['description'] ?? '');
        return $this->db->execute();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        return $this->db->fetchAll();
    }
    
    public function getFilteredCategories($filters) {
        $sql = "SELECT * FROM categories WHERE 1=1";
        
        if (!empty($filters['search'])) {
            $sql .= " AND name LIKE :search";
        }
        if (!empty($filters['type'])) {
            $sql .= " AND type = :type";
        }
        
        $sql .= " ORDER BY name ASC";
        $this->db->query($sql);
        
        if (!empty($filters['search'])) {
            $this->db->bind(':search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['type'])) {
            $this->db->bind(':type', $filters['type']);
        }
        
        return $this->db->fetchAll();
    }
    
    public function getByType($type) {
        $this->db->query("SELECT * FROM categories WHERE type = :type ORDER BY name ASC");
        $this->db->bind(':type', $type);
        return $this->db->fetchAll();
    }
    
    public function findById($id) {
        $this->db->query("SELECT * FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    
    public function update($id, $data) {
        // ✅ بررسی وجود نام تکراری برای دسته‌بندی‌های دیگر
        $existingCategory = $this->findByName($data['name']);
        if ($existingCategory && $existingCategory->id != $id) {
            return false; // بازگشت false در صورت تکراری بودن نام
        }

        $this->db->query("UPDATE categories SET name = :name, type = :type, description = :description, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type'] ?? 'product');
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("SELECT COUNT(*) as count FROM products WHERE category_id = :id");
        $this->db->bind(':id', $id);
        $productCount = $this->db->fetch()->count;
        
        $this->db->query("SELECT COUNT(*) as count FROM contracts WHERE category_id = :id");
        $this->db->bind(':id', $id);
        $contractCount = $this->db->fetch()->count;
        
        $this->db->query("SELECT COUNT(*) as count FROM invoices WHERE category_id = :id");
        $this->db->bind(':id', $id);
        $invoiceCount = $this->db->fetch()->count;
        
        if ($productCount > 0 || $contractCount > 0 || $invoiceCount > 0) {
            return false;
        }

        $this->db->query("DELETE FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}