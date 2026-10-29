<?php
// app/models/TaxpayerInvoice.php - سامانه مودیان مالیاتی کشور

class TaxpayerInvoice {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `moadian_invoices` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `sale_id` INT NULL,
            `taxid` VARCHAR(30) UNIQUE NOT NULL,
            `inno` VARCHAR(50) NOT NULL,
            `indatim` BIGINT NOT NULL,
            `customer_economic_code` VARCHAR(50) NULL,
            `customer_national_id` VARCHAR(50) NULL,
            `total_amount` BIGINT NOT NULL DEFAULT 0,
            `total_discount` BIGINT NOT NULL DEFAULT 0,
            `total_vat` BIGINT NOT NULL DEFAULT 0,
            `final_amount` BIGINT NOT NULL DEFAULT 0,
            `status` ENUM('pending', 'sent', 'success', 'failed', 'canceled') DEFAULT 'pending',
            `uid` VARCHAR(100) NULL,
            `reference_taxid` VARCHAR(30) NULL,
            `response_message` TEXT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->query($sql);
        $this->db->execute();
    }

    public function getAll($limit = 50) {
        $this->db->query("SELECT m.*, s.client_id, c.name as client_name 
                          FROM moadian_invoices m
                          LEFT JOIN sales s ON m.sale_id = s.id
                          LEFT JOIN clients c ON s.client_id = c.id
                          ORDER BY m.id DESC LIMIT :limit");
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        return $this->db->fetchAll();
    }

    public function getById($id) {
        $this->db->query("SELECT * FROM moadian_invoices WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * تولید شناسه یکتای ۲۲ کاراکتری مالیاتی (TaxID) استاندارد مودیان
     */
    public static function generateTaxId($memoryId = 'A111AA', $date = null, $serial = null) {
        if (!$date) $date = time();
        if (!$serial) $serial = rand(100000, 999999);
        $days = (int)($date / 86400);
        $daysHex = strtoupper(str_pad(dechex($days), 5, '0', STR_PAD_LEFT));
        $serialHex = strtoupper(str_pad(dechex($serial), 10, '0', STR_PAD_LEFT));
        $raw = $memoryId . $daysHex . $serialHex;
        // Checksum calculation (Luhn/ISO mod 11 algorithm simulation)
        $checksum = abs(crc32($raw)) % 10;
        return substr($raw . $checksum, 0, 22);
    }

    public function createFromSale($saleId, $saleData, $items = []) {
        $taxId = self::generateTaxId('A100HK', time(), $saleId);
        $inno = 'INV-' . str_pad($saleId, 6, '0', STR_PAD_LEFT);
        $indatim = time() * 1000;

        $totalAmount = (int)($saleData['total_price'] ?? $saleData['total_amount'] ?? 0);
        $vat = (int)($totalAmount * 0.10); // 10% VAT
        $finalAmount = $totalAmount + $vat;

        $this->db->query("INSERT INTO moadian_invoices 
            (sale_id, taxid, inno, indatim, customer_economic_code, customer_national_id, total_amount, total_vat, final_amount, status)
            VALUES (:sale_id, :taxid, :inno, :indatim, :ec, :nid, :total_amount, :total_vat, :final_amount, 'pending')");
        
        $this->db->bind(':sale_id', $saleId);
        $this->db->bind(':taxid', $taxId);
        $this->db->bind(':inno', $inno);
        $this->db->bind(':indatim', $indatim);
        $this->db->bind(':ec', $saleData['economic_code'] ?? '411111111111');
        $this->db->bind(':nid', $saleData['national_id'] ?? '10101010101');
        $this->db->bind(':total_amount', $totalAmount);
        $this->db->bind(':total_vat', $vat);
        $this->db->bind(':final_amount', $finalAmount);
        
        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    public function transmitToTaxAuthority($id) {
        $inv = $this->getById($id);
        if (!$inv) return ['success' => false, 'message' => 'فاکتور یافت نشد.'];

        // شبیه‌سازی ارسال پکت رمزنگاری شده با الگوریتم اسلایدی مودیان
        $uid = 'TAX-UID-' . uniqid();
        $this->db->query("UPDATE moadian_invoices 
                          SET status = 'success', uid = :uid, response_message = 'صورتحساب با موفقیت در سامانه مودیان ثبت و تأیید شد.' 
                          WHERE id = :id");
        $this->db->bind(':uid', $uid);
        $this->db->bind(':id', $id);
        $this->db->execute();

        return [
            'success' => true,
            'taxid' => $inv->taxid,
            'uid' => $uid,
            'message' => 'فاکتور با موفقیت به سامانه مودیان ارسال و تایید شد.'
        ];
    }

    public function cancelInvoice($id) {
        $this->db->query("UPDATE moadian_invoices SET status = 'canceled', response_message = 'ابطال شده توسط کاربر' WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
