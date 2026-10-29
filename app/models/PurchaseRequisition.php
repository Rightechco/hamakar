<?php
// app/models/PurchaseRequisition.php - درخواست خرید و فرآیند تدارکات سازمانی

class PurchaseRequisition {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `purchase_requisitions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `req_number` VARCHAR(50) NOT NULL,
            `requester_id` INT NULL,
            `product_id` INT NOT NULL,
            `warehouse_id` INT NULL,
            `quantity` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
            `estimated_price` BIGINT NOT NULL DEFAULT 0,
            `needed_date` VARCHAR(20) NULL,
            `priority` ENUM('normal', 'high', 'urgent') DEFAULT 'normal',
            `status` ENUM('pending', 'approved', 'purchased', 'rejected') DEFAULT 'pending',
            `approved_by` INT NULL,
            `notes` TEXT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->query($sql);
        $this->db->execute();
    }

    public function getAll($status = null) {
        $sql = "SELECT pr.*, p.name as product_name, p.sku as product_code,
                       u.name as requester_name, w.name as warehouse_name
                FROM purchase_requisitions pr
                LEFT JOIN products p ON pr.product_id = p.id
                LEFT JOIN users u ON pr.requester_id = u.id
                LEFT JOIN warehouses w ON pr.warehouse_id = w.id
                WHERE 1=1";
        
        if ($status) $sql .= " AND pr.status = :status";
        $sql .= " ORDER BY pr.id DESC";

        $this->db->query($sql);
        if ($status) $this->db->bind(':status', $status);
        return $this->db->fetchAll();
    }

    public function create($data) {
        $reqNum = 'REQ-' . date('Ymd') . '-' . rand(100, 999);
        $this->db->query("INSERT INTO purchase_requisitions 
            (req_number, requester_id, product_id, warehouse_id, quantity, estimated_price, needed_date, priority, notes, status)
            VALUES (:req_number, :requester_id, :product_id, :warehouse_id, :quantity, :price, :needed_date, :priority, :notes, 'pending')");
        
        $this->db->bind(':req_number', $reqNum);
        $this->db->bind(':requester_id', $data['requester_id'] ?? null);
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':warehouse_id', $data['warehouse_id'] ?? null);
        $this->db->bind(':quantity', $data['quantity'] ?? 1);
        $this->db->bind(':price', $data['estimated_price'] ?? 0);
        $this->db->bind(':needed_date', $data['needed_date'] ?? date('Y/m/d'));
        $this->db->bind(':priority', $data['priority'] ?? 'normal');
        $this->db->bind(':notes', $data['notes'] ?? '');

        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    public function updateStatus($id, $status, $approvedBy = null) {
        $this->db->query("UPDATE purchase_requisitions SET status = :status, approved_by = :app WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':app', $approvedBy);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
