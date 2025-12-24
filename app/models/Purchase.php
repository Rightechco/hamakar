<?php
// app/models/Purchase.php
require_once __DIR__ . '/InventoryLog.php';

class Purchase {
    private $db;
    private $inventoryLogModel;

    public function __construct() {
        $this->db = new Database();
        $this->inventoryLogModel = new InventoryLog();
    }

    /**
     * Retrieves all purchases with vendor (client) name.
     * @return array
     */
    public function getAllPurchases() {
        $this->db->query("
            SELECT p.*, c.name AS vendor_name
            FROM purchases p
            LEFT JOIN clients c ON p.vendor_id = c.id
            ORDER BY p.purchase_date DESC
        ");
        return $this->db->fetchAll();
    }

    /**
     * Retrieves a single purchase by ID.
     * @param int $id
     * @return object|false
     */
    public function findById($id) {
        $this->db->query('SELECT * FROM purchases WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * Retrieves a single purchase by ID with vendor (client) details.
     * @param int $id
     * @return object|false
     */
    public function findByIdWithVendor($id) {
        $this->db->query("
            SELECT p.*, c.name AS vendor_name, c.company_name AS vendor_company_name, c.phone AS vendor_phone, c.address AS vendor_address
            FROM purchases p
            LEFT JOIN clients c ON p.vendor_id = c.id
            WHERE p.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * Creates a new purchase record and its items in a transaction.
     * @param array $data
     * @return int|false The ID of the new purchase or false on failure.
     */
   public function createPurchase($data) {
        $this->db->beginTransaction();
        try {
            // ایجاد رکورد خرید
            $this->db->query('INSERT INTO purchases (vendor_id, purchase_date, subtotal, vat_rate, vat_amount, total_amount, status) VALUES (:vendor_id, :purchase_date, :subtotal, :vat_rate, :vat_amount, :total_amount, :status)');
            $this->db->bind(':vendor_id', $data['vendor_id']);
            $this->db->bind(':purchase_date', $data['purchase_date']);
            $this->db->bind(':subtotal', $data['subtotal']);
            $this->db->bind(':vat_rate', $data['vat_rate']);
            $this->db->bind(':vat_amount', $data['vat_amount']);
            $this->db->bind(':total_amount', $data['total_amount']);
            $this->db->bind(':status', $data['status']);
            $this->db->execute();
            $purchaseId = $this->db->lastInsertId();

            // درج آیتم‌های خرید و افزایش موجودی
            foreach ($data['items'] as $item) {
                // درج آیتم
                $this->db->query('INSERT INTO purchase_items (purchase_id, product_id, quantity, price) VALUES (:purchase_id, :product_id, :quantity, :price)');
                $this->db->bind(':purchase_id', $purchaseId);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':price', $item['price']);
                $this->db->execute();

                // افزایش موجودی محصول در جدول products
                $this->db->query('UPDATE products SET inventory = inventory + :quantity WHERE id = :product_id');
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->execute();
                
                // ایجاد لاگ ورودی در جدول inventory_logs
                $logData = [
                    'warehouse_id' => $data['warehouse_id'], 
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'log_type' => 'in',
                    'source_id' => $purchaseId,
                    'source_type' => 'purchase', // مقدار ENUM صحیح
                    'user_id' => $data['user_id']
                ];
                $this->inventoryLogModel->createLog($logData);
            }
            $this->db->commit();
            return $purchaseId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Purchase creation failed: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Retrieves all items for a given purchase ID.
     * @param int $purchaseId
     * @return array
     */
    public function getItemsByPurchaseId($purchaseId) {
        $this->db->query("
            SELECT pi.*, p.name AS product_name, u.name AS product_unit
            FROM purchase_items pi
            LEFT JOIN products p ON pi.product_id = p.id
            LEFT JOIN units u ON p.unit_id = u.id
            WHERE pi.purchase_id = :purchase_id
        ");
        $this->db->bind(':purchase_id', $purchaseId);
        return $this->db->fetchAll();
    }

    /**
     * Updates the status of a purchase record.
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status) {
        $this->db->query('UPDATE purchases SET status = :status WHERE id = :id');
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}