<?php
// app/models/ProductionOrder.php - دستور تولید و محاسبه بهای تمام شده واقعی

class ProductionOrder {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `production_orders` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `order_number` VARCHAR(50) NOT NULL,
            `bom_id` INT NOT NULL,
            `planned_qty` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
            `actual_qty` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `raw_warehouse_id` INT NULL, -- انبار خروج مواد اولیه
            `finished_warehouse_id` INT NULL, -- انبار ورود کالای ساخته شده
            `start_date` VARCHAR(20) NULL,
            `end_date` VARCHAR(20) NULL,
            `actual_labor_cost` BIGINT NOT NULL DEFAULT 0,
            `actual_overhead_cost` BIGINT NOT NULL DEFAULT 0,
            `total_material_cost` BIGINT NOT NULL DEFAULT 0,
            `total_cost` BIGINT NOT NULL DEFAULT 0,
            `unit_cost` BIGINT NOT NULL DEFAULT 0,
            `status` ENUM('planned', 'in_progress', 'completed', 'canceled') DEFAULT 'planned',
            `voucher_id` INT NULL,
            `notes` TEXT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->query($sql);
        $this->db->execute();
    }

    public function getAll() {
        $this->db->query("SELECT po.*, b.title as bom_title, p.name as product_name,
                                 w1.name as raw_wh_name, w2.name as fin_wh_name
                          FROM production_orders po
                          LEFT JOIN boms b ON po.bom_id = b.id
                          LEFT JOIN products p ON b.product_id = p.id
                          LEFT JOIN warehouses w1 ON po.raw_warehouse_id = w1.id
                          LEFT JOIN warehouses w2 ON po.finished_warehouse_id = w2.id
                          ORDER BY po.id DESC");
        return $this->db->fetchAll();
    }

    public function getById($id) {
        $this->db->query("SELECT po.*, b.title as bom_title, p.name as product_name, p.id as product_id 
                          FROM production_orders po
                          LEFT JOIN boms b ON po.bom_id = b.id
                          LEFT JOIN products p ON b.product_id = p.id
                          WHERE po.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    public function create($data) {
        $orderNumber = 'PRD-' . date('Ymd') . '-' . rand(100, 999);
        $this->db->query("INSERT INTO production_orders 
            (order_number, bom_id, planned_qty, raw_warehouse_id, finished_warehouse_id, start_date, notes, status)
            VALUES (:num, :bom_id, :qty, :raw_wh, :fin_wh, :start_date, :notes, 'planned')");
        
        $this->db->bind(':num', $orderNumber);
        $this->db->bind(':bom_id', $data['bom_id']);
        $this->db->bind(':qty', $data['planned_qty']);
        $this->db->bind(':raw_wh', $data['raw_warehouse_id'] ?? null);
        $this->db->bind(':fin_wh', $data['finished_warehouse_id'] ?? null);
        $this->db->bind(':start_date', $data['start_date'] ?? date('Y/m/d'));
        $this->db->bind(':notes', $data['notes'] ?? '');

        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    /**
     * تکمیل سفارش تولید:
     * ۱. کسر خودکار مواد اولیه از انبار بر اساس اقلام BOM
     * ۲. اضافه کردن کالای نهایی به انبار محصولات
     * ۳. محاسبه بهای تمام شده نهایی
     */
    public function completeOrder($orderId, $actualQty, $laborCost = 0, $overheadCost = 0) {
        $order = $this->getById($orderId);
        if (!$order) return false;

        $bomModel = new Bom();
        $bomItems = $bomModel->getItems($order->bom_id);

        $totalMaterialCost = 0;
        foreach ($bomItems as $item) {
            $usedQty = $item->quantity * $actualQty * (1 + ($item->scrap_percent / 100));
            $materialPrice = $item->raw_price ?? 0;
            $totalMaterialCost += ($usedQty * $materialPrice);

            // کسر از انبار مواد اولیه در جدول inventory_logs در صورت وجود
            if ($order->raw_warehouse_id) {
                $this->db->query("INSERT INTO inventory_logs (warehouse_id, product_id, quantity, type, description, created_at)
                                  VALUES (:wh, :prd, :qty, 'out', :desc, NOW())");
                $this->db->bind(':wh', $order->raw_warehouse_id);
                $this->db->bind(':prd', $item->raw_material_id);
                $this->db->bind(':qty', $usedQty);
                $this->db->bind(':desc', "مصرف مواد در سفارش تولید شماره {$order->order_number}");
                $this->db->execute();
            }
        }

        $totalCost = $totalMaterialCost + $laborCost + $overheadCost;
        $unitCost = ($actualQty > 0) ? round($totalCost / $actualQty) : 0;

        // افزایش موجودی انبار محصول نهایی
        if ($order->finished_warehouse_id && $order->product_id) {
            $this->db->query("INSERT INTO inventory_logs (warehouse_id, product_id, quantity, type, description, created_at)
                              VALUES (:wh, :prd, :qty, 'in', :desc, NOW())");
            $this->db->bind(':wh', $order->finished_warehouse_id);
            $this->db->bind(':prd', $order->product_id);
            $this->db->bind(':qty', $actualQty);
            $this->db->bind(':desc', "ورود کالای ساخته شده از سفارش تولید شماره {$order->order_number}");
            $this->db->execute();
        }

        // بروزرسانی سفارش تولید
        $this->db->query("UPDATE production_orders 
                          SET actual_qty = :actual_qty,
                              actual_labor_cost = :labor,
                              actual_overhead_cost = :overhead,
                              total_material_cost = :mat_cost,
                              total_cost = :total_cost,
                              unit_cost = :unit_cost,
                              status = 'completed',
                              end_date = :end_date
                          WHERE id = :id");
        $this->db->bind(':actual_qty', $actualQty);
        $this->db->bind(':labor', $laborCost);
        $this->db->bind(':overhead', $overheadCost);
        $this->db->bind(':mat_cost', $totalMaterialCost);
        $this->db->bind(':total_cost', $totalCost);
        $this->db->bind(':unit_cost', $unitCost);
        $this->db->bind(':end_date', date('Y/m/d'));
        $this->db->bind(':id', $orderId);

        return $this->db->execute();
    }
}
