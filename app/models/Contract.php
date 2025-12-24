<?php
// app/models/Contract.php

class Contract {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllContracts() {
        $this->db->query("SELECT contracts.*, clients.name as client_name FROM contracts JOIN clients ON contracts.client_id = clients.id ORDER BY contracts.created_at DESC");
        return $this->db->fetchAll();
    }

    public function findById($id) {
        $this->db->query('SELECT * FROM contracts WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    public function getContractsByClientId($clientId) {
        $this->db->query('SELECT id, client_id, title, service_type, total_amount, start_date, end_date, renewal_type, next_renewal_date, status FROM contracts WHERE client_id = :client_id ORDER BY created_at DESC');
        $this->db->bind(':client_id', $clientId);
        return $this->db->fetchAll();
    }
    
    public function countAll() {
        $this->db->query('SELECT COUNT(*) as count FROM contracts');
        $result = $this->db->fetch();
        return $result ? $result->count : 0;
    }

    public function create($data) {
        $this->db->query('INSERT INTO contracts (client_id, title, service_type, category_id, description, total_amount, start_date, end_date, renewal_type, next_renewal_date, status) VALUES (:client_id, :title, :service_type, :category_id, :description, :total_amount, :start_date, :end_date, :renewal_type, :next_renewal_date, :status)');
        $this->db->bind(':client_id', $data['client_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':service_type', $data['service_type']);
        $this->db->bind(':category_id', $data['category_id'] ?? null);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':renewal_type', $data['renewal_type']);
        $this->db->bind(':next_renewal_date', $data['next_renewal_date']);
        $this->db->bind(':status', $data['status']);
        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    public function update($id, $data) {
        $sql = 'UPDATE contracts SET client_id = :client_id, title = :title, service_type = :service_type, category_id = :category_id, description = :description, total_amount = :total_amount, start_date = :start_date, end_date = :end_date, renewal_type = :renewal_type, next_renewal_date = :next_renewal_date, status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':client_id', $data['client_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':service_type', $data['service_type']);
        $this->db->bind(':category_id', $data['category_id'] ?? null);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':renewal_type', $data['renewal_type']);
        $this->db->bind(':next_renewal_date', $data['next_renewal_date']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function delete($id) {
        $this->db->query('DELETE FROM contracts WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * getContractsForRenewalReminder
     * قراردادهایی را پیدا می کند که نیاز به یادآوری تمدید دارند.
     * @param string $daysBefore چند روز قبل از سررسید (مثلاً 7، 1، 0).
     * @return array
     */
    public function getContractsForRenewalReminder($daysBefore) {
        $today = date('Y-m-d');
        $targetDate = date('Y-m-d', strtotime("+$daysBefore days"));

        $this->db->query("
            SELECT 
                c.id, c.title, c.total_amount, c.next_renewal_date, c.renewal_type,
                cl.name as client_name, cl.phone as client_phone, cl.email as client_email,
                c.last_reminder_1week_sent_at, c.last_reminder_1day_sent_at, c.last_invoice_sent_at
            FROM contracts c
            JOIN clients cl ON c.client_id = cl.id
            WHERE c.renewal_type != 'none' 
            AND c.next_renewal_date = :target_date
            AND c.status = 'active' -- فقط قراردادهای فعال
        ");
        $this->db->bind(':target_date', $targetDate);
        return $this->db->fetchAll();
    }

    /**
     * updateReminderTimestamp
     * زمان ارسال آخرین یادآوری را برای یک قرارداد به روز می کند.
     * @param int $contractId شناسه قرارداد.
     * @param string $column نام ستون (last_reminder_1week_sent_at, last_reminder_1day_sent_at, last_invoice_sent_at).
     * @return bool
     */
    public function updateReminderTimestamp($contractId, $column) {
        $this->db->query("UPDATE contracts SET {$column} = NOW() WHERE id = :id");
        $this->db->bind(':id', $contractId);
        return $this->db->execute();
    }
    
public function getDueContractsForReminder($targetDate, $reminderType) {
    $column_map = [
        '7day' => 'reminder_7day_sent_at',
        '1day' => 'reminder_1day_sent_at',
        'today' => 'invoice_sent_at'
    ];
    $sent_column = $column_map[$reminderType];

    $sql = "
        SELECT c.id, c.title, c.total_amount, c.next_renewal_date,
               cl.name as client_name, cl.phone as client_phone
        FROM contracts c
        JOIN clients cl ON c.client_id = cl.id
        WHERE c.status = 'active'
          AND c.renewal_type != 'none'
          AND c.next_renewal_date = :target_date
          AND c.{$sent_column} IS NULL
    ";
    
    $this->db->query($sql);
    $this->db->bind(':target_date', $targetDate);
    return $this->db->fetchAll();
}

/**
 * زمان ارسال یادآوری را برای یک قرارداد ثبت می‌کند.
 * @param int $contractId شناسه قرارداد
 * @param string $reminderType نوع یادآوری ('7day', '1day', 'today')
 * @return bool
 */
public function markReminderAsSent($contractId, $reminderType) {
    $column_map = [
        '7day' => 'reminder_7day_sent_at',
        '1day' => 'reminder_1day_sent_at',
        'today' => 'invoice_sent_at'
    ];
    $sent_column = $column_map[$reminderType];

    $this->db->query("UPDATE contracts SET {$sent_column} = NOW() WHERE id = :id");
    $this->db->bind(':id', $contractId);
    return $this->db->execute();
}
public function getActiveMonthlyContracts() {
    $this->db->query("SELECT * FROM contracts WHERE billing_cycle = 'monthly' AND status = 'active' AND auto_reminder_status = 'active'");
    return $this->db->fetchAll();
}

/**
 * به‌روزرسانی تاریخ آخرین یادآوری ارسال شده
 */
public function updateLastReminderDate($contractId, $renewalDate) {
    $this->db->query("UPDATE contracts SET last_reminder_sent_for = :renewal_date WHERE id = :id");
    $this->db->bind(':renewal_date', $renewalDate);
    $this->db->bind(':id', $contractId);
    return $this->db->execute();
}

/**
 * تمدید تاریخ سررسید قرارداد برای ماه بعد
 */
public function renewMonthlyContract($contractId, $newRenewalDate) {
    // با این کار، فیلد last_reminder_sent_for برای دوره جدید خالی می‌شود
    $this->db->query("UPDATE contracts SET next_renewal_date = :new_date, last_reminder_sent_for = NULL WHERE id = :id");
    $this->db->bind(':new_date', $newRenewalDate);
    $this->db->bind(':id', $contractId);
    return $this->db->execute();
}
public function getFilteredContracts($filters) {
        $sql = "SELECT c.*, cl.name as client_name, cat.name as category_name FROM contracts c JOIN clients cl ON c.client_id = cl.id LEFT JOIN categories cat ON c.category_id = cat.id WHERE 1=1";

        if (!empty($filters['client_id'])) {
            $sql .= " AND c.client_id = :client_id";
        }
        if (!empty($filters['status'])) {
            $sql .= " AND c.status = :status";
        }
        if (!empty($filters['category_id'])) {
            $sql .= " AND c.category_id = :category_id";
        }
        // ... other filters
        $sql .= " ORDER BY c.created_at DESC";
        $this->db->query($sql);
        
        // Bind parameters
        if (!empty($filters['client_id'])) {
            $this->db->bind(':client_id', $filters['client_id']);
        }
        if (!empty($filters['status'])) {
            $this->db->bind(':status', $filters['status']);
        }
        if (!empty($filters['category_id'])) {
            $this->db->bind(':category_id', $filters['category_id']);
        }

        return $this->db->fetchAll();
    }
}