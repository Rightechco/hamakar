<?php
// app/models/Account.php

class Account {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function create($data) {
        $this->db->query('INSERT INTO accounts (parent_id, code, name, type) VALUES (:parent_id, :code, :name, :type)');
        $this->db->bind(':parent_id', $data['parent_id'] ?: null);
        $this->db->bind(':code', $data['code']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':type', $data['type']);
        return $this->db->execute();
    }

    public function getAll() {
        $this->db->query('SELECT * FROM accounts ORDER BY code ASC');
        return $this->db->fetchAll();
    }

    // این متد حساب‌ها را به صورت درختی برای نمایش بهتر برمی‌گرداند
    public function getAllHierarchical() {
        $accounts = $this->getAll();
        $nested = [];
        $lookup = [];

        foreach ($accounts as $account) {
            $lookup[$account->id] = $account;
            $account->children = [];
        }

        foreach ($accounts as $account) {
            if ($account->parent_id) {
                if(isset($lookup[$account->parent_id])) {
                   $lookup[$account->parent_id]->children[] = $account;
                }
            } else {
                $nested[] = $account;
            }
        }
        return $nested;
    }
    public function getAccountsByType($type) {
    $this->db->query('SELECT * FROM accounts WHERE type = :type ORDER BY code');
    $this->db->bind(':type', $type);
    return $this->db->fetchAll();
}

public function findByCode($code) {
    $this->db->query('SELECT * FROM accounts WHERE code = :code');
    $this->db->bind(':code', $code);
    return $this->db->fetch();
}

public function getAccountLedger($accountId, $startDate, $endDate) {
    // ۱. محاسبه مانده اولیه (مانده از قبل)
    $this->db->query("
        SELECT SUM(je.debit - je.credit) as opening_balance
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        WHERE je.account_id = :account_id AND jv.voucher_date < :start_date
    ");
    $this->db->bind(':account_id', $accountId);
    $this->db->bind(':start_date', $startDate);
    $openingBalanceRow = $this->db->fetch();
    $openingBalance = $openingBalanceRow ? (float)$openingBalanceRow->opening_balance : 0;

    // ۲. دریافت تراکنش‌های دوره انتخاب شده
    $this->db->query("
        SELECT jv.voucher_date, jv.description, je.debit, je.credit
        FROM journal_entries je
        JOIN journal_vouchers jv ON je.voucher_id = jv.id
        WHERE je.account_id = :account_id AND jv.voucher_date BETWEEN :start_date AND :end_date
        ORDER BY jv.voucher_date, jv.id
    ");
    $this->db->bind(':account_id', $accountId);
    $this->db->bind(':start_date', $startDate);
    $this->db->bind(':end_date', $endDate);
    $transactions = $this->db->fetchAll();

    return [
        'opening_balance' => $openingBalance,
        'transactions' => $transactions
    ];
}

public function findById($id) {
    $this->db->query('SELECT * FROM accounts WHERE id = :id');
    $this->db->bind(':id', $id);
    return $this->db->fetch();
}
}