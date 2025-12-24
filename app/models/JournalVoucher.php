<?php
class JournalVoucher {
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function create($data) {
        $this->db->query('INSERT INTO journal_vouchers (voucher_date, description, created_by_user_id) VALUES (:voucher_date, :description, :created_by_user_id)');
        $this->db->bind(':voucher_date', $data['voucher_date']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':created_by_user_id', $data['user_id']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    // app/models/JournalVoucher.php

public function delete($id) {
    $this->db->beginTransaction();
    try {
        // ابتدا تمام ردیف‌های (entries) مربوط به این سند را حذف کن
        $this->db->query('DELETE FROM journal_entries WHERE voucher_id = :id');
        $this->db->bind(':id', $id);
        $this->db->execute();

        // سپس خود سند اصلی (voucher) را حذف کن
        $this->db->query('DELETE FROM journal_vouchers WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->execute();

        // اگر همه چیز موفق بود، تراکنش را تایید نهایی کن
        return $this->db->commit();
    } catch (PDOException $e) {
        // در صورت بروز خطا، تمام تغییرات را به حالت اول بازگردان
        $this->db->rollBack();
        error_log("Voucher Deletion Failed: " . $e->getMessage());
        return false;
    }
}

public function reverse($voucherId, $userId) {
    // ۱. پیدا کردن سند اصلی و ردیف‌های آن
    $this->db->query("SELECT * FROM journal_vouchers WHERE id = :id");
    $this->db->bind(':id', $voucherId);
    $originalVoucher = $this->db->fetch();

    $this->db->query("SELECT * FROM journal_entries WHERE voucher_id = :id");
    $this->db->bind(':id', $voucherId);
    $originalEntries = $this->db->fetchAll();

    if (!$originalVoucher || empty($originalEntries)) {
        return false;
    }

    // ۲. ایجاد یک سند جدید (سند معکوس)
    $newVoucherData = [
        'voucher_date' => $originalVoucher->voucher_date,
        'description' => 'سند معکوس برای سند شماره ' . $voucherId . ' - ' . $originalVoucher->description,
        'user_id' => $userId
    ];
    $newVoucherId = $this->create($newVoucherData);

    if ($newVoucherId) {
        // ۳. ثبت ردیف‌های معکوس (جابجایی بدهکار و بستانکار)
        $entryModel = new JournalEntry();
        foreach ($originalEntries as $entry) {
            $reversedEntry = [
                'account_id' => $entry->account_id,
                'debit' => $entry->credit, // جای بدهکار و بستانکار عوض می‌شود
                'credit' => $entry->debit,
                'entity_type' => $entry->entity_type,
                'entity_id' => $entry->entity_id,
            ];
            $entryModel->create($newVoucherId, $reversedEntry);
        }
        return true;
    }
    return false;
}
}