<?php
// app/models/InventoryLog.php

class InventoryLog {
    private $db;
    public function __construct() {
        $this->db = new Database();
    }
    
    public function createLog($data) {
        $this->db->query("INSERT INTO inventory_logs (warehouse_id, product_id, quantity, log_type, source_id, source_type, user_id) VALUES (:warehouse_id, :product_id, :quantity, :log_type, :source_id, :source_type, :user_id)");
        $this->db->bind(':warehouse_id', $data['warehouse_id']);
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':log_type', $data['log_type']);
        $this->db->bind(':source_id', $data['source_id']);
        $this->db->bind(':source_type', $data['source_type']);
        $this->db->bind(':user_id', $data['user_id']);
        return $this->db->execute();
    }
    
    /**
     * Retrieves all inventory movements for a specific product, including related details.
     * @param int $productId
     * @return array
     */
    public function getLogsByProduct($productId) {
        $this->db->query("
            SELECT 
                il.*, 
                w.name as warehouse_name, 
                u.name as user_name,
                
                -- دریافت نام مشتری یا فروشنده مرتبط برای نمایش در گزارش
                CASE 
                    WHEN il.source_type = 'sale' THEN (SELECT name FROM clients WHERE id = s.client_id)
                    WHEN il.source_type = 'purchase' THEN (SELECT name FROM clients WHERE id = p.vendor_id)
                    ELSE 'موجودی اولیه'
                END AS related_entity_name
                
            FROM inventory_logs il
            LEFT JOIN warehouses w ON il.warehouse_id = w.id
            LEFT JOIN users u ON il.user_id = u.id
            LEFT JOIN sales s ON il.source_id = s.id AND il.source_type = 'sale'
            LEFT JOIN purchases p ON il.source_id = p.id AND il.source_type = 'purchase'
            
            WHERE il.product_id = :product_id
            ORDER BY il.created_at DESC
        ");
        $this->db->bind(':product_id', $productId);
        return $this->db->fetchAll();
    }

    /**
     * Retrieves a detailed inventory report for a specific warehouse.
     * * NOTE: This method relies on the 'inventory' column in the products table being initialized
     * or updated by product creation/update/delete.
     * The original code snippet was complex, simplified here to ensure products are linked to logs.
     * * @param int $warehouseId
     * @return array
     */
    public function getDetailedReportByWarehouse($warehouseId) {
        // اگر شناسه انبار مشخص شده، محصولات آن انبار را با موجودی محاسبه شده نشان بده
        $sql = "
            SELECT
                p.id,
                p.name as product_name,
                p.sku,
                p.sale_price,
                p.purchase_price,
                u.name as unit_name,
                
                -- محاسبه موجودی فعلی بر اساس جمع کل ورودی‌ها و خروجی‌ها
                COALESCE(
                    SUM(CASE WHEN il.log_type = 'in' THEN il.quantity ELSE 0 END) - 
                    SUM(CASE WHEN il.log_type = 'out' THEN il.quantity ELSE 0 END)
                , 0) AS current_inventory_calculated
                
            FROM products p
            LEFT JOIN units u ON p.unit_id = u.id
            LEFT JOIN inventory_logs il ON p.id = il.product_id AND il.warehouse_id = :warehouse_id
            
            WHERE p.warehouse_id = :warehouse_id_filter OR il.warehouse_id = :warehouse_id_log
            
            GROUP BY p.id
            ORDER BY p.name ASC
        ";
        $this->db->query($sql);
        $this->db->bind(':warehouse_id', $warehouseId);
        $this->db->bind(':warehouse_id_filter', $warehouseId);
        $this->db->bind(':warehouse_id_log', $warehouseId);

        return $this->db->fetchAll();
    }
}
