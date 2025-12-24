<?php
// app/models/Service.php

class Service {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $this->db->query("SELECT s.*, c.name as category_name FROM services s LEFT JOIN categories c ON s.category_id = c.id ORDER BY s.name ASC");
        return $this->db->fetchAll();
    }
    
    public function findById($id) {
        $this->db->query("SELECT * FROM services WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    public function create($data) {
        $this->db->query("INSERT INTO services (name, price, description, category_id) VALUES (:name, :price, :description, :category_id)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':category_id', $data['category_id'] ?? null);
        return $this->db->execute();
    }

    public function update($id, $data) {
        $this->db->query("UPDATE services SET name = :name, price = :price, description = :description, category_id = :category_id WHERE id = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':category_id', $data['category_id'] ?? null);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query("DELETE FROM services WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}