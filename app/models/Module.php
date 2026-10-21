<?php
class Module extends Database {
    protected $table = 'modules';

    public function getAllActive() {
        $this->query("SELECT * FROM {$this->table} WHERE status = 'active'");
        return $this->resultSet();
    }

    public function getBySlug($slug) {
        $this->query("SELECT * FROM {$this->table} WHERE slug = :slug AND status = 'active'");
        $this->bind(':slug', $slug);
        return $this->single();
    }

    public function getById($id) {
        $this->query("SELECT * FROM {$this->table} WHERE id = :id");
        $this->bind(':id', $id);
        return $this->single();
    }
}