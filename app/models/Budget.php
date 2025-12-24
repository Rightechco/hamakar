<?php
// app/models/Budget.php

class Budget {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * بودجه را برای یک حساب در یک دوره مشخص ذخیره یا به‌روزرسانی می‌کند.
     */
    public function saveOrUpdate($data) {
        // ابتدا بررسی می‌کنیم آیا برای این دوره بودجه‌ای ثبت شده یا خیر
        $this->db->query('SELECT id FROM budgets WHERE account_id = :account_id AND period_year = :period_year AND period_month = :period_month');
        $this->db->bind(':account_id', $data['account_id']);
        $this->db->bind(':period_year', $data['period_year']);
        $this->db->bind(':period_month', $data['period_month']);
        $existing = $this->db->fetch();

        if ($existing) {
            // به‌روزرسانی
            $this->db->query('UPDATE budgets SET budget_amount = :budget_amount WHERE id = :id');
            $this->db->bind(':budget_amount', $data['budget_amount']);
            $this->db->bind(':id', $existing->id);
        } else {
            // ایجاد
            $this->db->query('INSERT INTO budgets (account_id, period_year, period_month, budget_amount) VALUES (:account_id, :period_year, :period_month, :budget_amount)');
            $this->db->bind(':account_id', $data['account_id']);
            $this->db->bind(':period_year', $data['period_year']);
            $this->db->bind(':period_month', $data['period_month']);
            $this->db->bind(':budget_amount', $data['budget_amount']);
        }
        return $this->db->execute();
    }

    /**
     * تمام بودجه‌های ثبت‌شده برای یک سال خاص را برمی‌گرداند.
     */
    public function getBudgetsByYear($year) {
        $this->db->query('SELECT b.*, a.name as account_name FROM budgets b JOIN accounts a ON b.account_id = a.id WHERE b.period_year = :year ORDER BY b.period_month, a.name');
        $this->db->bind(':year', $year);
        return $this->db->fetchAll();
    }

    /**
     * یک ردیف بودجه را حذف می‌کند.
     */
    public function delete($id) {
        $this->db->query('DELETE FROM budgets WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}