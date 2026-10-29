<?php
// app/models/WorkflowRule.php - موتور اتوماسیون گردش کار و قوانین شرطی

class WorkflowRule {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `workflow_rules` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `event_trigger` VARCHAR(50) NOT NULL, -- invoice_overdue, cheque_due_soon, low_stock, new_lead
            `condition_field` VARCHAR(50) NULL,
            `condition_operator` VARCHAR(10) NULL, -- >, <, =, contains
            `condition_value` VARCHAR(100) NULL,
            `action_type` VARCHAR(50) NOT NULL, -- send_sms, create_task, send_notification
            `action_payload` TEXT NULL,
            `is_active` TINYINT(1) DEFAULT 1,
            `last_triggered_at` DATETIME NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->query($sql);
        $this->db->execute();
    }

    public function getAll() {
        $this->db->query("SELECT * FROM workflow_rules ORDER BY id DESC");
        return $this->db->resultSet();
    }

    public function create($data) {
        $this->db->query("INSERT INTO workflow_rules 
            (title, event_trigger, condition_field, condition_operator, condition_value, action_type, action_payload, is_active)
            VALUES (:title, :trigger, :field, :op, :val, :action, :payload, 1)");
        
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':trigger', $data['event_trigger']);
        $this->db->bind(':field', $data['condition_field'] ?? '');
        $this->db->bind(':op', $data['condition_operator'] ?? '=');
        $this->db->bind(':val', $data['condition_value'] ?? '');
        $this->db->bind(':action', $data['action_type']);
        $this->db->bind(':payload', $data['action_payload'] ?? '');

        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    public function toggle($id) {
        $this->db->query("UPDATE workflow_rules SET is_active = NOT is_active WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
