<?php
// app/models/Sale.php
require_once __DIR__ . '/InventoryLog.php'; 

class Sale {
    private $db;
    private $inventoryLogModel; 

    public function __construct() {
        $this->db = new Database();
        $this->inventoryLogModel = new InventoryLog(); 
    }

    /**
     * Retrieves all sales with client name.
     * @return array
     */
    public function getAllSales() {
        $this->db->query("
            SELECT s.*, c.name AS client_name
            FROM sales s
            LEFT JOIN clients c ON s.client_id = c.id
            ORDER BY s.sale_date DESC
        ");
        return $this->db->fetchAll();
    }

    /**
     * Retrieves a single sale by ID.
     * @param int $id
     * @return object|false
     */
    public function findById($id) {
        $this->db->query('SELECT * FROM sales WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * Retrieves a single sales record by ID with client details.
     * @param int $id
     * @return object|false
     */
    public function findByIdWithClient($id) {
        $this->db->query("
            SELECT s.*, c.name AS client_name, c.company_name AS client_company_name, c.phone AS client_phone, c.address AS client_address
            FROM sales s
            LEFT JOIN clients c ON s.client_id = c.id
            WHERE s.id = :id
        ");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * Creates a new sale record and its items in a transaction.
     * @param array $data
     * @return int|false The ID of the new sale or false on failure.
     */
    public function createSale($data) {
        $this->db->beginTransaction();
        try {
            // ایجاد رکورد فروش
            $this->db->query('INSERT INTO sales (client_id, sale_date, subtotal, vat_rate, vat_amount, total_amount, status) VALUES (:client_id, :sale_date, :subtotal, :vat_rate, :vat_amount, :total_amount, :status)');
            $this->db->bind(':client_id', $data['client_id']);
            $this->db->bind(':sale_date', $data['sale_date']);
            $this->db->bind(':subtotal', $data['subtotal']);
            $this->db->bind(':vat_rate', $data['vat_rate']);
            $this->db->bind(':vat_amount', $data['vat_amount']);
            $this->db->bind(':total_amount', $data['total_amount']);
            $this->db->bind(':status', $data['status']);
            $this->db->execute();
            $saleId = $this->db->lastInsertId();

            // درج آیتم‌های فروش و کاهش موجودی
            foreach ($data['items'] as $item) {
                // درج آیتم
                $this->db->query('INSERT INTO sale_items (sale_id, product_id, quantity, price) VALUES (:sale_id, :product_id, :quantity, :price)');
                $this->db->bind(':sale_id', $saleId);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':price', $item['price']);
                $this->db->execute();

                // کاهش موجودی محصول در جدول products
                $this->db->query('UPDATE products SET inventory = inventory - :quantity WHERE id = :product_id');
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->execute();
                
                // ایجاد لاگ خروجی در جدول inventory_logs
                $logData = [
                    'warehouse_id' => $data['warehouse_id'], 
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'log_type' => 'out',
                    'source_id' => $saleId,
                    'source_type' => 'sale', // مقدار ENUM صحیح
                    'user_id' => $data['user_id']
                ];
                $this->inventoryLogModel->createLog($logData);
            }

            $this->db->commit();
            return $saleId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Sale creation failed: " . $e->getMessage()); 
            return false;
        }
    }

    /**
     * Retrieves all items for a given sales ID.
     * @param int $saleId
     * @return array
     */
     public function getItemsBySaleId($saleId) {
        $this->db->query("
            SELECT si.*, p.name AS product_name, u.name AS product_unit
            FROM sale_items si
            LEFT JOIN products p ON si.product_id = p.id
            LEFT JOIN units u ON p.unit_id = u.id
            WHERE si.sale_id = :sale_id
        ");
        $this->db->bind(':sale_id', $saleId);
        return $this->db->fetchAll();
    }
    
    /**
     * Updates the status of a sales record.
     * @param int $id
     * @param string $status
     * @return bool
     */
   public function updateStatus($id, $status) {
        $this->db->query('UPDATE sales SET status = :status WHERE id = :id');
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Deletes a sale and its items in a transaction.
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $this->db->beginTransaction();
        try {
            $items = $this->getItemsBySaleId($id);

            // افزایش موجودی محصول (برگشت موجودی)
            foreach ($items as $item) {
                $this->db->query('UPDATE products SET inventory = inventory + :quantity WHERE id = :product_id');
                $this->db->bind(':quantity', $item->quantity);
                $this->db->bind(':product_id', $item->product_id);
                $this->db->execute();
            }

            // حذف آیتم‌ها
            $this->db->query('DELETE FROM sale_items WHERE sale_id = :id');
            $this->db->bind(':id', $id);
            $this->db->execute();

            // حذف سند فروش
            $this->db->query('DELETE FROM sales WHERE id = :id');
            $this->db->bind(':id', $id);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Sale deletion failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteSale($id) {
    try {
        $this->db->beginTransaction();

        // ۱. دریافت آیتم‌های فاکتور فروش
        $this->db->query("SELECT * FROM sale_items WHERE sale_id = :id");
        $this->db->bind(':id', $id);
        $items = $this->db->fetchAll() ?? [];

        // ۲. برگشت موجودی کالاها به انبار (ورود مجدد به انبار بابت ابطال فروش)
        foreach ($items as $item) {
            $this->db->query("INSERT INTO inventory_logs (warehouse_id, product_id, quantity, log_type, source_id, source_type, user_id) 
                              VALUES (:warehouse_id, :product_id, :quantity, 'in', :source_id, 'sale', :user_id)");
            $this->db->bind(':warehouse_id', $item->warehouse_id ?? 1);
            $this->db->bind(':product_id', $item->product_id);
            $this->db->bind(':quantity', $item->quantity);
            $this->db->bind(':source_id', $id);
            $this->db->bind(':user_id', $_SESSION['user_id'] ?? 1);
            $this->db->execute();
        }

        // ۳. حذف اسناد حسابداری مرتبط با این فاکتور فروش
        $this->db->query("DELETE FROM journal_vouchers WHERE description LIKE :desc");
        $this->db->bind(':desc', '%فاکتور شماره ' . $id . '%');
        $this->db->execute();

        // ۴. حذف آیتم‌های فاکتور فروش
        $this->db->query("DELETE FROM sale_items WHERE sale_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();

        // ۵. حذف رکورد اصلی فاکتور فروش
        $this->db->query("DELETE FROM sales WHERE id = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->execute();

        $this->db->commit();
        return $result;
    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("Error deleting sale " . $id . ": " . $e->getMessage());
        return false;
    }
    }
}