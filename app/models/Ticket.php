<?php
// app/models/Ticket.php
class Ticket {
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function create($data) {
        $this->db->query('INSERT INTO tickets (user_id, subject, department, priority, status) VALUES (:user_id, :subject, :department, :priority, "open")');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':subject', $data['subject']);
        $this->db->bind(':department', $data['department']);
        $this->db->bind(':priority', $data['priority']);
        if ($this->db->execute()) { return $this->db->lastInsertId(); }
        return false;
    }

    public function findByIdWithDetails($id) {
        $this->db->query('SELECT t.*, u.name as client_name FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    
    public function getAllForAdmin() {
        $this->db->query('SELECT t.*, u.name as client_name FROM tickets t JOIN users u ON t.user_id = u.id ORDER BY t.updated_at DESC');
        return $this->db->fetchAll();
    }

    public function getAllForClient($userId) {
        $this->db->query('SELECT * FROM tickets WHERE user_id = :user_id ORDER BY updated_at DESC');
        $this->db->bind(':user_id', $userId);
        return $this->db->fetchAll();
    }
    
    public function updateStatus($id, $status) {
        $this->db->query('UPDATE tickets SET status = :status, updated_at = NOW() WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }
}