<?php
// app/models/Product.php
require_once __DIR__ . '/InventoryLog.php';
require_once __DIR__ . '/Unit.php';
require_once __DIR__ . '/Warehouse.php';

class Product {
    private $db;
    private $inventoryLogModel;
    private $unitModel;
    private $warehouseModel;

    public function __construct() {
        $this->db = new Database();
        $this->inventoryLogModel = new InventoryLog(); 
        $this->unitModel = new Unit();
        $this->warehouseModel = new Warehouse();
    }

    public function create($data) {
        $this->db->beginTransaction();
        try {
            // ... (کد درج محصول) ...
            $this->db->query('INSERT INTO products (name, sku, category_id, purchase_price, sale_price, description, unit_id, image_path, warehouse_id, inventory) VALUES (:name, :sku, :category_id, :purchase_price, :sale_price, :description, :unit_id, :image_path, :warehouse_id, :inventory)');
            $this->db->bind(':name', $data['name']);
            $this->db->bind(':sku', $data['sku']);
            $this->db->bind(':category_id', $data['category_id'] ?? null);
            $this->db->bind(':purchase_price', $data['purchase_price']);
            $this->db->bind(':sale_price', $data['sale_price']);
            $this->db->bind(':description', $data['description'] ?? null);
            $this->db->bind(':unit_id', $data['unit_id'] ?? null);
            $this->db->bind(':image_path', $data['image_path'] ?? null);
            $this->db->bind(':warehouse_id', $data['warehouse_id']);
            $this->db->bind(':inventory', $data['initial_inventory']);
            $this->db->execute();
            $productId = $this->db->lastInsertId();

            if ($data['initial_inventory'] > 0) {
                 $logData = [
                    'warehouse_id' => $data['warehouse_id'],
                    'product_id' => $productId,
                    'quantity' => $data['initial_inventory'],
                    'log_type' => 'in',
                    'source_id' => $productId,
                    'source_type' => 'stock', // ✅ اصلاح مقدار به یک کلمه کوتاه (stock)
                    'user_id' => 1
                ];
                $this->inventoryLogModel->createLog($logData);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Product creation failed: " . $e->getMessage());
            return false;
        }
    }

    // ... (ادامه متدهای Product)
    
    public function update($id, $data) {
        $this->db->query('UPDATE products SET name = :name, sku = :sku, category_id = :category_id, purchase_price = :purchase_price, sale_price = :sale_price, description = :description, unit_id = :unit_id, image_path = :image_path, warehouse_id = :warehouse_id WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':category_id', $data['category_id'] ?? null);
        $this->db->bind(':purchase_price', $data['purchase_price']);
        $this->db->bind(':sale_price', $data['sale_price']);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':unit_id', $data['unit_id'] ?? null);
        $this->db->bind(':image_path', $data['image_path'] ?? null);
        $this->db->bind(':warehouse_id', $data['warehouse_id']);
        return $this->db->execute();
    }
    
    public function findById($id) {
        $this->db->query('SELECT * FROM products WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    
    public function getAll() {
        $this->db->query("
            SELECT 
                p.*, 
                c.name as category_name, 
                u.name as unit_name
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN units u ON p.unit_id = u.id 
            ORDER BY p.name
        ");
        return $this->db->fetchAll();
    }
    
    public function getFilteredProducts($filters = []) {
        $sql = "
            SELECT 
                p.*, 
                c.name as category_name, 
                u.name as unit_name
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            LEFT JOIN units u ON p.unit_id = u.id 
            WHERE 1=1
        ";

        if (!empty($filters['search'])) {
            $sql .= " AND (p.name LIKE :search OR p.sku LIKE :search)";
        }
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
        }
        $sql .= " ORDER BY p.name ASC";

        $this->db->query($sql);
        if (!empty($filters['search'])) {
            $this->db->bind(':search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['category_id'])) {
            $this->db->bind(':category_id', $filters['category_id']);
        }
        
        return $this->db->fetchAll();
    }

    public function updateInventory($productId, $quantity) {
        $this->db->query('UPDATE products SET inventory = inventory + :quantity WHERE id = :product_id');
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':product_id', $productId);
        return $this->db->execute();
    }
    
    public function delete($id) {
        $this->db->query('DELETE FROM products WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function getInventoryLogsByProduct($productId) {
        return $this->inventoryLogModel->getLogsByProduct($productId);
    }
}
