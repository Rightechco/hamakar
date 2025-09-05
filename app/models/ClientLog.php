<?php
// app/models/ClientLog.php

class ClientLog {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * لاگ‌های یک مشتری را به همراه نام کاربر ثبت کننده و نام رابط (در صورت وجود) برمی‌گرداند.
     */
    public function getLogsByClientId($clientId) {
        $this->db->query('SELECT cl.*, u.name as user_name, cu.name as contact_name
                          FROM client_logs cl 
                          JOIN users u ON cl.user_id = u.id 
                          LEFT JOIN users cu ON cl.contact_user_id = cu.id
                          WHERE cl.client_id = :client_id 
                          ORDER BY cl.log_date DESC');
        $this->db->bind(':client_id', $clientId);
        return $this->db->fetchAll();
    }

    public function create($data) {
        $this->db->query('INSERT INTO client_logs (client_id, log_type, description, log_date, user_id, contact_user_id) 
                          VALUES (:client_id, :log_type, :description, :log_date, :user_id, :contact_user_id)');
        $this->db->bind(':client_id', $data['client_id']);
        $this->db->bind(':log_type', $data['log_type']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':log_date', $data['log_date']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':contact_user_id', $data['contact_user_id']);
        return $this->db->execute();
    }
}
