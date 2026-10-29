<?php
// app/models/ContractGuarantee.php - مدیریت تضامین، چک‌های ضمانت و ضمانت‌نامه‌های بانکی قراردادها

class ContractGuarantee {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `contract_guarantees` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `contract_id` INT NOT NULL,
            `type` ENUM('bank_guarantee', 'promissory_note', 'cheque', 'cash') NOT NULL,
            `guarantee_number` VARCHAR(100) NOT NULL,
            `issuer_bank` VARCHAR(100) NULL,
            `amount` BIGINT NOT NULL DEFAULT 0,
            `issue_date` VARCHAR(20) NOT NULL,
            `expiry_date` VARCHAR(20) NOT NULL,
            `purpose` ENUM('tender', 'advance_payment', 'performance', 'retention') DEFAULT 'performance', -- حسن انجام کار / پیش پرداخت / مناقصه
            `status` ENUM('active', 'released', 'extended', 'confiscated') DEFAULT 'active',
            `notes` TEXT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->query($sql);
        $this->db->execute();
    }

    public function getAll($contractId = null) {
        $sql = "SELECT cg.*, c.title as contract_title, cl.name as client_name
                FROM contract_guarantees cg
                LEFT JOIN contracts c ON cg.contract_id = c.id
                LEFT JOIN clients cl ON c.client_id = cl.id
                WHERE 1=1";
        
        if ($contractId) $sql .= " AND cg.contract_id = :cid";
        $sql .= " ORDER BY cg.expiry_date ASC";

        $this->db->query($sql);
        if ($contractId) $this->db->bind(':cid', $contractId);
        return $this->db->resultSet();
    }

    public function create($data) {
        $this->db->query("INSERT INTO contract_guarantees 
            (contract_id, type, guarantee_number, issuer_bank, amount, issue_date, expiry_date, purpose, notes, status)
            VALUES (:cid, :type, :num, :bank, :amount, :idate, :edate, :purpose, :notes, 'active')");
        
        $this->db->bind(':cid', $data['contract_id']);
        $this->db->bind(':type', $data['type']);
        $this->db->bind(':num', $data['guarantee_number']);
        $this->db->bind(':bank', $data['issuer_bank'] ?? '');
        $this->db->bind(':amount', (int)$data['amount']);
        $this->db->bind(':idate', $data['issue_date']);
        $this->db->bind(':edate', $data['expiry_date']);
        $this->db->bind(':purpose', $data['purpose'] ?? 'performance');
        $this->db->bind(':notes', $data['notes'] ?? '');

        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    public function updateStatus($id, $status) {
        $this->db->query("UPDATE contract_guarantees SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
