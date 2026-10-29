<?php
// app/models/Cheque.php - مدیریت چک‌های صیادی، خزانه‌داری و راس‌گیری

class Cheque {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `cheques` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `type` ENUM('received', 'paid') NOT NULL, -- دریافتی یا پرداختی
            `cheque_number` VARCHAR(50) NOT NULL,
            `sayad_id` VARCHAR(16) NOT NULL, -- شناسه ۱۶ رقمی صیادی
            `bank_name` VARCHAR(100) NOT NULL,
            `branch` VARCHAR(100) NULL,
            `amount` BIGINT NOT NULL DEFAULT 0,
            `issue_date` VARCHAR(20) NOT NULL,
            `due_date` VARCHAR(20) NOT NULL,
            `client_id` INT NULL, -- طرف حساب
            `drawer_name` VARCHAR(150) NULL, -- صاحب حساب / صادرکننده
            `drawer_national_id` VARCHAR(20) NULL,
            `status` ENUM('pending', 'deposited', 'cleared', 'bounced', 'transferred') DEFAULT 'pending',
            `transferred_to` VARCHAR(150) NULL,
            `voucher_id` INT NULL, -- شماره سند مالی مرتبط
            `notes` TEXT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->query($sql);
        $this->db->execute();
    }

    public function getAll($type = null, $status = null) {
        $sql = "SELECT c.*, cl.name as client_name 
                FROM cheques c 
                LEFT JOIN clients cl ON c.client_id = cl.id 
                WHERE 1=1";
        
        if ($type) $sql .= " AND c.type = :type";
        if ($status) $sql .= " AND c.status = :status";
        $sql .= " ORDER BY c.due_date ASC";

        $this->db->query($sql);
        if ($type) $this->db->bind(':type', $type);
        if ($status) $this->db->bind(':status', $status);
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query("SELECT * FROM cheques WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function create($data) {
        $this->db->query("INSERT INTO cheques 
            (type, cheque_number, sayad_id, bank_name, branch, amount, issue_date, due_date, client_id, drawer_name, drawer_national_id, status, notes)
            VALUES (:type, :cheque_number, :sayad_id, :bank_name, :branch, :amount, :issue_date, :due_date, :client_id, :drawer_name, :drawer_national_id, 'pending', :notes)");
        
        $this->db->bind(':type', $data['type'] ?? 'received');
        $this->db->bind(':cheque_number', $data['cheque_number']);
        $this->db->bind(':sayad_id', $data['sayad_id']);
        $this->db->bind(':bank_name', $data['bank_name']);
        $this->db->bind(':branch', $data['branch'] ?? '');
        $this->db->bind(':amount', (int)$data['amount']);
        $this->db->bind(':issue_date', $data['issue_date']);
        $this->db->bind(':due_date', $data['due_date']);
        $this->db->bind(':client_id', !empty($data['client_id']) ? $data['client_id'] : null);
        $this->db->bind(':drawer_name', $data['drawer_name'] ?? '');
        $this->db->bind(':drawer_national_id', $data['drawer_national_id'] ?? '');
        $this->db->bind(':notes', $data['notes'] ?? '');

        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    public function updateStatus($id, $newStatus, $transferredTo = null) {
        $this->db->query("UPDATE cheques SET status = :status, transferred_to = :transferred_to WHERE id = :id");
        $this->db->bind(':status', $newStatus);
        $this->db->bind(':transferred_to', $transferredTo);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * محاسبه راس‌گیری وزنی ریاضی چک‌ها و فاکتورها
     * @param array $items [['amount' => 1000000, 'date' => '1403/08/15'], ...]
     * @param string $baseDate تاریخ مبدا
     */
    public static function calculateRas($items, $baseDate = null) {
        if (empty($items)) return null;

        $totalAmount = 0;
        $weightedDaysSum = 0;

        // تبدیل ساده تاریخ شمسی به تایم‌استمپ روز
        $baseTimestamp = $baseDate ? strtotime(str_replace('/', '-', $baseDate)) : time();

        foreach ($items as $item) {
            $amount = (float)($item['amount'] ?? 0);
            $date = $item['date'] ?? date('Y-m-d');
            $itemTimestamp = strtotime(str_replace('/', '-', $date));
            $daysDiff = round(($itemTimestamp - $baseTimestamp) / 86400);

            $totalAmount += $amount;
            $weightedDaysSum += ($amount * $daysDiff);
        }

        if ($totalAmount <= 0) return null;

        $averageDays = round($weightedDaysSum / $totalAmount);
        $rasTimestamp = $baseTimestamp + ($averageDays * 86400);

        return [
            'total_amount' => $totalAmount,
            'average_days' => $averageDays,
            'ras_date' => date('Y/m/d', $rasTimestamp),
            'items_count' => count($items)
        ];
    }
}
