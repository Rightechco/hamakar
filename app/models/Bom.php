<?php
// app/models/Bom.php - فرمول ساخت کالا (Bill of Materials)

class Bom {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `boms` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `product_id` INT NOT NULL, -- کالای ساخته شده
            `title` VARCHAR(255) NOT NULL,
            `standard_qty` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
            `estimated_labor_cost` BIGINT NOT NULL DEFAULT 0,
            `estimated_overhead_cost` BIGINT NOT NULL DEFAULT 0,
            `notes` TEXT NULL,
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->query($sql);
        $this->db->execute();

        $sql2 = "CREATE TABLE IF NOT EXISTS `bom_items` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `bom_id` INT NOT NULL,
            `raw_material_id` INT NOT NULL, -- کالای مصرفی/ماده اولیه
            `quantity` DECIMAL(10,4) NOT NULL DEFAULT 1.0000,
            `scrap_percent` DECIMAL(5,2) NOT NULL DEFAULT 0.00, -- درصد ضایعات استاندارد
            `unit_id` INT NULL,
            `notes` VARCHAR(255) NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->query($sql2);
        $this->db->execute();
    }

    public function getAll() {
        $this->db->query("SELECT b.*, p.name as product_name, p.code as product_code 
                          FROM boms b
                          LEFT JOIN products p ON b.product_id = p.id
                          ORDER BY b.id DESC");
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query("SELECT b.*, p.name as product_name, p.code as product_code 
                          FROM boms b
                          LEFT JOIN products p ON b.product_id = p.id
                          WHERE b.id = :id");
        $this->db->bind(':id', $id);
        $bom = $this->db->single();
        if ($bom) {
            $bom->items = $this->getItems($id);
        }
        return $bom;
    }

    public function getItems($bomId) {
        $this->db->query("SELECT bi.*, p.name as raw_material_name, p.price as raw_price, u.name as unit_name
                          FROM bom_items bi
                          LEFT JOIN products p ON bi.raw_material_id = p.id
                          LEFT JOIN units u ON bi.unit_id = u.id
                          WHERE bi.bom_id = :bom_id");
        $this->db->bind(':bom_id', $bomId);
        return $this->db->resultSet();
    }

    public function create($data, $items = []) {
        $this->db->query("INSERT INTO boms (product_id, title, standard_qty, estimated_labor_cost, estimated_overhead_cost, notes)
                          VALUES (:product_id, :title, :standard_qty, :labor, :overhead, :notes)");
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':standard_qty', $data['standard_qty'] ?? 1);
        $this->db->bind(':labor', $data['estimated_labor_cost'] ?? 0);
        $this->db->bind(':overhead', $data['estimated_overhead_cost'] ?? 0);
        $this->db->bind(':notes', $data['notes'] ?? '');

        if (!$this->db->execute()) return false;
        $bomId = $this->db->lastInsertId();

        foreach ($items as $item) {
            if (!empty($item['raw_material_id']) && !empty($item['quantity'])) {
                $this->db->query("INSERT INTO bom_items (bom_id, raw_material_id, quantity, scrap_percent, unit_id, notes)
                                  VALUES (:bom_id, :raw_id, :qty, :scrap, :unit_id, :notes)");
                $this->db->bind(':bom_id', $bomId);
                $this->db->bind(':raw_id', $item['raw_material_id']);
                $this->db->bind(':qty', $item['quantity']);
                $this->db->bind(':scrap', $item['scrap_percent'] ?? 0);
                $this->db->bind(':unit_id', $item['unit_id'] ?? null);
                $this->db->bind(':notes', $item['notes'] ?? '');
                $this->db->execute();
            }
        }
        return $bomId;
    }
}
