<?php
// app/models/FixedAsset.php

class FixedAsset {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        // با جدول حساب‌ها join می‌زنیم تا نام حساب را داشته باشیم
        $this->db->query('SELECT fa.*, a.name as asset_account_name 
                          FROM fixed_assets fa 
                          JOIN accounts a ON fa.asset_account_id = a.id 
                          ORDER BY fa.purchase_date DESC');
        return $this->db->fetchAll();
    }

    public function create($data) {
        $sql = 'INSERT INTO fixed_assets (asset_name, asset_code, purchase_date, purchase_cost, salvage_value, useful_life_years, asset_account_id, expense_account_id, accumulated_depreciation_account_id) 
                VALUES (:asset_name, :asset_code, :purchase_date, :purchase_cost, :salvage_value, :useful_life_years, :asset_account_id, :expense_account_id, :accumulated_depreciation_account_id)';
        
        $this->db->query($sql);
        $this->db->bind(':asset_name', $data['asset_name']);
        $this->db->bind(':asset_code', $data['asset_code']);
        $this->db->bind(':purchase_date', $data['purchase_date']);
        $this->db->bind(':purchase_cost', $data['purchase_cost']);
        $this->db->bind(':salvage_value', $data['salvage_value']);
        $this->db->bind(':useful_life_years', $data['useful_life_years']);
        $this->db->bind(':asset_account_id', $data['asset_account_id']);
        $this->db->bind(':expense_account_id', $data['expense_account_id']);
        $this->db->bind(':accumulated_depreciation_account_id', $data['accumulated_depreciation_account_id']);
        
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query('DELETE FROM fixed_assets WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}