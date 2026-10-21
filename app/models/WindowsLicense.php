<?php
class WindowsLicense extends Database {
    protected $table = 'windows_licenses';

    public function getAvailableLicense() {
        // دریافت اولین لایسنس فروش نرفته
        $this->query("SELECT * FROM {$this->table} WHERE is_sold = 0 LIMIT 1");
        return $this->single();
    }

    public function markAsSold($id, $userId) {
        $this->query("UPDATE {$this->table} SET is_sold = 1, user_id = :uid, sold_at = NOW() WHERE id = :id");
        $this->bind(':uid', $userId);
        $this->bind(':id', $id);
        return $this->execute();
    }
}