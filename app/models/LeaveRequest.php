<?php
// app/models/LeaveRequest.php

class LeaveRequest {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function create($data) {
        $this->db->query('INSERT INTO leave_requests (user_id, leave_type, start_date, end_date, reason) VALUES (:user_id, :leave_type, :start_date, :end_date, :reason)');
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':leave_type', $data['leave_type']);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':reason', $data['reason']);
        return $this->db->execute();
    }

    public function getForUser($userId) {
        $this->db->query('SELECT * FROM leave_requests WHERE user_id = :user_id ORDER BY created_at DESC');
        $this->db->bind(':user_id', $userId);
        return $this->db->fetchAll();
    }

    public function getAllPending() {
        $this->db->query("SELECT lr.*, u.name as employee_name FROM leave_requests lr JOIN users u ON lr.user_id = u.id WHERE lr.status = 'pending' ORDER BY lr.created_at ASC");
        return $this->db->fetchAll();
    }
    
    public function findById($id) {
        $this->db->query('SELECT * FROM leave_requests WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    public function processRequest($id, $status, $adminNotes, $adminId) {
        $this->db->query('UPDATE leave_requests SET status = :status, admin_notes = :admin_notes, reviewed_by_user_id = :admin_id WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        $this->db->bind(':admin_notes', $adminNotes);
        $this->db->bind(':admin_id', $adminId);
        return $this->db->execute();
    }
}