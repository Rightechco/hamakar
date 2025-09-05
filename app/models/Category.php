<?php
// app/models/Category.php

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * یک دسته‌بندی جدید ایجاد می‌کند.
     * @param array $data شامل name, description
     * @return bool
     */
    public function create($data) {
        $this->db->query("INSERT INTO categories (name, description) VALUES (:name, :description)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? '');
        return $this->db->execute();
    }

    /**
     * تمام دسته‌بندی‌ها را برمی‌گرداند.
     * @return array
     */
    public function getAll() {
        $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        return $this->db->fetchAll();
    }
    
    /**
     * یک دسته‌بندی را بر اساس شناسه پیدا می‌کند.
     * @param int $id
     * @return object|false
     */
    public function findById($id) {
        $this->db->query("SELECT * FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    
    /**
     * اطلاعات یک دسته‌بندی را به‌روزرسانی می‌کند.
     * @param int $id
     * @param array $data شامل name, description
     * @return bool
     */
    public function update($id, $data) {
        $this->db->query("UPDATE categories SET name = :name, description = :description, updated_at = CURRENT_TIMESTAMP WHERE id = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * یک دسته‌بندی را حذف می‌کند.
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        // ابتدا بررسی می‌کند که آیا این دسته‌بندی در جایی استفاده شده است یا خیر
        // این کار از خطاهای کلید خارجی جلوگیری می‌کند.
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
            // اگر دسته‌بندی در جایی استفاده شده باشد، از حذف آن جلوگیری می‌کند.
            return false;
        }

        $this->db->query("DELETE FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}