<?php
// app/models/PurchaseItem.php

class PurchaseItem {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Retrieves all items for a given purchase ID.
     * @param int $purchaseId
     * @return array
     */
    public function getItemsByPurchaseId($purchaseId) {
        $this->db->query("
            SELECT pi.*, p.name AS product_name
            FROM purchase_items pi
            LEFT JOIN products p ON pi.product_id = p.id
            WHERE pi.purchase_id = :purchase_id
        ");
        $this->db->bind(':purchase_id', $purchaseId);
        return $this->db->fetchAll();
    }
    
    /**
     * Creates a new purchase item.
     * @param array $data
     * @return bool
     */
    public function create($data) {
        $this->db->query('INSERT INTO purchase_items (purchase_id, product_id, quantity, price) VALUES (:purchase_id, :product_id, :quantity, :price)');
        $this->db->bind(':purchase_id', $data['purchase_id']);
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':price', $data['price']);
        return $this->db->execute();
    }
    
    /**
     * Deletes all items for a given purchase ID.
     * @param int $purchaseId
     * @return bool
     */
    public function deleteByPurchaseId($purchaseId) {
        $this->db->query('DELETE FROM purchase_items WHERE purchase_id = :purchase_id');
        $this->db->bind(':purchase_id', $purchaseId);
        return $this->db->execute();
    }
}