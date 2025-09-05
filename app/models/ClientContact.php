<?php
// app/models/ClientContact.php

class ClientContact {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * یک رابط جدید را در جدول client_contacts ثبت می‌کند.
     * @param array $data شامل client_id, user_id, position
     * @return bool
     */
    public function create($data) {
        $this->db->query('INSERT INTO client_contacts (client_id, user_id, position) VALUES (:client_id, :user_id, :position)');
        $this->db->bind(':client_id', $data['client_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':position', $data['position']);
        return $this->db->execute();
    }

    /**
     * تمام رابطین یک مشتری خاص را به همراه اطلاعات کاربری آنها برمی‌گرداند.
     * @param int $clientId شناسه مشتری
     * @return array
     */
    public function getContactsByClientId($clientId) {
        $this->db->query('SELECT cc.*, u.name as user_name, u.mobile_number, u.email FROM client_contacts cc JOIN users u ON cc.user_id = u.id WHERE cc.client_id = :client_id');
        $this->db->bind(':client_id', $clientId);
        return $this->db->fetchAll();
    }
    
    /**
     * ✅ متد جدید: حذف تمام رابطین یک مشتری بر اساس شناسه مشتری.
     * @param int $clientId شناسه مشتری
     * @return bool
     */
    public function deleteByClientId($clientId) {
        $this->db->query('DELETE FROM client_contacts WHERE client_id = :client_id');
        $this->db->bind(':client_id', $clientId);
        return $this->db->execute();
    }

    /**
     * یک رابط را بر اساس شناسه حذف می‌کند.
     * @param int $id شناسه رابط
     * @return bool
     */
    public function delete($id) {
        $this->db->query('DELETE FROM client_contacts WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}