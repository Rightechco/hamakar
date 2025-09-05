<?php
// app/models/Sale.php

class Sale {
    private $db;

    public function __construct() {
        $this->db = new Database();
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
     * Retrieves a single sale by ID with client details.
     * @param int $id
     * @return object|false
     */
    public function findByIdWithClient($id) {
        $this->db->query("
            SELECT s.*, c.name AS client_name, c.phone AS client_phone, c.email AS client_email, c.address AS client_address
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
            // Create the main sales invoice
            $this->db->query('INSERT INTO sales (client_id, sale_date, total_amount, status) VALUES (:client_id, :sale_date, :total_amount, :status)');
            $this->db->bind(':client_id', $data['client_id']);
            $this->db->bind(':sale_date', $data['sale_date']);
            $this->db->bind(':total_amount', $data['total_amount']);
            $this->db->bind(':status', $data['status']);
            $this->db->execute();
            $saleId = $this->db->lastInsertId();

            // Insert sale items
            foreach ($data['items'] as $item) {
                $this->db->query('INSERT INTO sale_items (sale_id, product_id, quantity, price) VALUES (:sale_id, :product_id, :quantity, :price)');
                $this->db->bind(':sale_id', $saleId);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':price', $item['price']);
                $this->db->execute();
                
                // Update product inventory (decrement)
                $this->db->query('UPDATE products SET inventory = inventory - :quantity WHERE id = :product_id');
                $this->db->bind(':quantity', $item['quantity']);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->execute();
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
     * Retrieves all items for a given sale ID.
     * @param int $saleId
     * @return array
     */
    public function getItemsBySaleId($saleId) {
        $this->db->query("
            SELECT si.*, p.name AS product_name, p.unit AS product_unit, p.purchase_price AS product_purchase_price
            FROM sale_items si
            LEFT JOIN products p ON si.product_id = p.id
            WHERE si.sale_id = :sale_id
        ");
        $this->db->bind(':sale_id', $saleId);
        return $this->db->fetchAll();
    }
    
    /**
     * Updates the status of a sale record.
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
}