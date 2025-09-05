<?php
// app/models/Product.php

class Product {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function create($data) {
        $this->db->query('INSERT INTO products (name, sku, category_id, purchase_price, sale_price, description, unit, image_path) VALUES (:name, :sku, :category_id, :purchase_price, :sale_price, :description, :unit, :image_path)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':category_id', $data['category_id'] ?? null);
        $this->db->bind(':purchase_price', $data['purchase_price']);
        $this->db->bind(':sale_price', $data['sale_price']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':unit', $data['unit'] ?? null);
        $this->db->bind(':image_path', $data['image_path'] ?? null);
        return $this->db->execute();
    }

    public function update($id, $data) {
        $this->db->query('UPDATE products SET name = :name, sku = :sku, category_id = :category_id, purchase_price = :purchase_price, sale_price = :sale_price, description = :description, unit = :unit, image_path = :image_path WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':category_id', $data['category_id'] ?? null);
        $this->db->bind(':purchase_price', $data['purchase_price']);
        $this->db->bind(':sale_price', $data['sale_price']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':unit', $data['unit'] ?? null);
        $this->db->bind(':image_path', $data['image_path'] ?? null);
        return $this->db->execute();
    }
    
    public function findById($id) {
        $this->db->query('SELECT * FROM products WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    
    public function getAll() {
        $this->db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.name");
        return $this->db->fetchAll();
    }

    public function getFilteredProducts($filters = []) {
        $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.sku LIKE :search)";
        }
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
        }
        $sql .= " ORDER BY p.name ASC";

        $this->db->query($sql);
        if (!empty($filters['search'])) {
            $this->db->bind(':search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['category_id'])) {
            $this->db->bind(':category_id', $filters['category_id']);
        }
        
        return $this->db->fetchAll();
    }

    public function updateInventory($productId, $quantity) {
        $this->db->query('UPDATE products SET inventory = inventory + :quantity WHERE id = :product_id');
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':product_id', $productId);
        return $this->db->execute();
    }
    
    public function delete($id) {
        $this->db->query('DELETE FROM products WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}