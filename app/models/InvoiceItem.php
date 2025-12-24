<?php
// app/models/InvoiceItem.php
// ✅ این مدل برای مدیریت آیتم‌های فاکتور لازم است
class InvoiceItem {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }
    
    // متد برای ثبت یک آیتم فاکتور
    public function createItem($data) {
        $sql = "INSERT INTO invoice_items (invoice_id, product_id, service_id, quantity, price) 
                VALUES (:invoice_id, :product_id, :service_id, :quantity, :price)";
        
        $this->db->query($sql);
        
        $this->db->bind(':invoice_id', $data['invoice_id']);
        $this->db->bind(':product_id', $data['product_id'] ?? null);
        $this->db->bind(':service_id', $data['service_id'] ?? null);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':price', $data['price']);
        
        return $this->db->execute();
    }

    // متد برای ثبت چندین آیتم فاکتور
    public function createItems($invoiceId, $items) {
        $success = true;
        foreach ($items as $item) {
            $data = [
                'invoice_id' => $invoiceId,
                'product_id' => $item['product_id'] ?? null,
                'service_id' => $item['service_id'] ?? null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ];
            if (!$this->createItem($data)) {
                $success = false;
            }
        }
        return $success;
    }

    // ✅ متد جدید برای دریافت آیتم‌های فاکتور
     public function getItemsByInvoiceId($invoiceId) {
        $this->db->query("
            SELECT 
                ii.*,
                p.name AS product_name,
                s.name AS service_name,
                u.name AS product_unit
            FROM invoice_items ii
            LEFT JOIN products p ON ii.product_id = p.id
            LEFT JOIN services s ON ii.service_id = s.id
            LEFT JOIN units u ON p.unit_id = u.id  -- ✅ این JOIN اکنون با ساختار جدید پایگاه داده کار می‌کند
            WHERE ii.invoice_id = :invoice_id
        ");
        $this->db->bind(':invoice_id', $invoiceId);
        return $this->db->fetchAll();
    }
}