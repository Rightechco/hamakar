<?php
// app/models/Payment.php

class Payment {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllPaymentsByInvoiceId($invoiceId) {
        $this->db->query('SELECT * FROM payments WHERE invoice_id = :invoice_id ORDER BY payment_date DESC');
        $this->db->bind(':invoice_id', $invoiceId);
        return $this->db->fetchAll();
    }

    public function create($data) {
        $this->db->query('INSERT INTO payments (invoice_id, amount, payment_date, method, transaction_id, notes) 
                          VALUES (:invoice_id, :amount, :payment_date, :method, :transaction_id, :notes)');
        $this->db->bind(':invoice_id', $data['invoice_id']);
        $this->db->bind(':amount', $data['amount']);
        $this->db->bind(':payment_date', $data['payment_date']);
        $this->db->bind(':method', $data['method']);
        $this->db->bind(':transaction_id', $data['transaction_id']);
        $this->db->bind(':notes', $data['notes']);
        return $this->db->execute();
    }

    // می توانید متدهای update و delete برای پرداخت ها را نیز اضافه کنید.
}