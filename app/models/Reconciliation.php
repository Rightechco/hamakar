<?php
// app/models/Reconciliation.php

class Reconciliation {
    private $db;
    public function __construct() { $this->db = new Database(); }

    /**
     * یک رکورد مغایرت‌گیری جدید ایجاد کرده و شناسه آن را برمی‌گرداند.
     */
    public function create($data) {
        $this->db->query('INSERT INTO reconciliations (account_id, statement_date, statement_balance, reconciled_by_user_id) VALUES (:account_id, :statement_date, :statement_balance, :user_id)');
        $this->db->bind(':account_id', $data['account_id']);
        $this->db->bind(':statement_date', $data['statement_date']);
        $this->db->bind(':statement_balance', $data['statement_balance']);
        $this->db->bind(':user_id', $data['user_id']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
}