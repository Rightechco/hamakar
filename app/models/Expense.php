<?php
class Expense {
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function create($data) {
        $this->db->query('INSERT INTO expenses (expense_date, description, amount, vendor, expense_account_id, payment_account_id) VALUES (:expense_date, :description, :amount, :vendor, :expense_account_id, :payment_account_id)');
        $this->db->bind(':expense_date', $data['expense_date']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':vendor', $data['vendor']);
        $this->db->bind(':expense_account_id', $data['expense_account_id']);
        $this->db->bind(':payment_account_id', $data['payment_account_id']);
        
        return $this->db->execute();
    }
}