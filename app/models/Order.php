<?php
class Order extends Database {
    protected $table = 'orders';

    public function createOrder($userId, $type, $itemId, $amount) {
        $this->query("INSERT INTO {$this->table} (user_id, type, item_id, amount, status) VALUES (:uid, :type, :iid, :amount, 'pending')");
        $this->bind(':uid', $userId);
        $this->bind(':type', $type);
        $this->bind(':iid', $itemId);
        $this->bind(':amount', $amount);
        
        if ($this->execute()) {
            return $this->dbh->lastInsertId();
        }
        return false;
    }

    public function markAsPaid($orderId) {
        $this->query("UPDATE {$this->table} SET status = 'paid' WHERE id = :id");
        $this->bind(':id', $orderId);
        return $this->execute();
    }
}