<?php
// app/models/SystemSetting.php - مدل جامع مدیریت تنظیمات سامانه

class SystemSetting {
    private $db;
    private static $cachedSettings = null;

    public function __construct() {
        $this->db = new Database();
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `system_settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `setting_key` VARCHAR(100) UNIQUE NOT NULL,
            `setting_value` TEXT NULL,
            `setting_group` VARCHAR(50) DEFAULT 'general',
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->query($sql);
        $this->db->execute();
    }

    /**
     * دریافت تمام تنظیمات به صورت آرایه کلید => مقدار
     */
    public function getAll() {
        if (self::$cachedSettings !== null) {
            return self::$cachedSettings;
        }

        $this->db->query("SELECT setting_key, setting_value FROM system_settings");
        $rows = $this->db->fetchAll();
        $settings = [];
        foreach ($rows as $r) {
            $settings[$r->setting_key] = $r->setting_value;
        }

        // مقادیر پیش‌فرض هوشمند
        $defaults = [
            'company_name' => 'سامانه یکپارچه هماکار',
            'company_brand' => 'هماکار ERP',
            'company_phone' => '۰۴۱۳۴۴۰۱۱۷۹',
            'company_email' => 'info@hamakar.ir',
            'company_address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه ۳، پلاک ۴۱',
            'company_economic_code' => '۱۴۰۰۹۱۵۶۳۲۱',
            'company_national_id' => '۱۴۰۰۹۱۵۶۳۲۱',
            'company_registration_no' => '۵۵۲۰۰',
            'company_postal_code' => '۵۱۶۶۶۱۴۵۷۸',
            'default_currency' => 'IRT',
            'default_vat_rate' => '10.00',
            'invoice_prefix' => 'INV-',
            'default_payment_terms' => 'cash',
            'offline_mode_enabled' => '1',
            'offline_sync_interval' => '60',
            'offline_max_cached_rows' => '500',
            'system_title' => 'سامانه یکپارچه هماکار | نسل نوین نرم‌افزار سازمانی',
            'maintenance_mode' => '0',
            'session_lifetime_minutes' => '120'
        ];

        self::$cachedSettings = array_merge($defaults, $settings);
        return self::$cachedSettings;
    }

    /**
     * دریافت تنظیمات دسته‌بندی شده بر اساس گروه
     */
    public function getAllGrouped() {
        $this->db->query("SELECT * FROM system_settings ORDER BY setting_group, id ASC");
        $rows = $this->db->fetchAll();
        $grouped = [];
        foreach ($rows as $r) {
            $grouped[$r->setting_group][$r->setting_key] = $r->setting_value;
        }
        return $grouped;
    }

    /**
     * دریافت یک مقدار با کلید
     */
    public function get($key, $default = null) {
        $all = $this->getAll();
        return $all[$key] ?? $default;
    }

    /**
     * تنظیم یک کلید مشخص
     */
    public function set($key, $value, $group = 'general') {
        $sql = "INSERT INTO system_settings (setting_key, setting_value, setting_group) 
                VALUES (:k, :v, :g) 
                ON DUPLICATE KEY UPDATE setting_value = :v2, setting_group = :g2";
        $this->db->query($sql);
        $this->db->bind(':k', $key);
        $this->db->bind(':v', $value);
        $this->db->bind(':g', $group);
        $this->db->bind(':v2', $value);
        $this->db->bind(':g2', $group);
        self::$cachedSettings = null;
        return $this->db->execute();
    }

    /**
     * ذخیره دسته‌ای تنظیمات از روی فرم
     */
    public function saveBulk($data) {
        $groupMap = [
            'company_name' => 'company',
            'company_brand' => 'company',
            'company_phone' => 'company',
            'company_email' => 'company',
            'company_address' => 'company',
            'company_economic_code' => 'company',
            'company_national_id' => 'company',
            'company_registration_no' => 'company',
            'company_postal_code' => 'company',
            'default_currency' => 'financial',
            'default_vat_rate' => 'financial',
            'invoice_prefix' => 'financial',
            'default_payment_terms' => 'financial',
            'offline_mode_enabled' => 'offline',
            'offline_sync_interval' => 'offline',
            'offline_max_cached_rows' => 'offline',
            'system_title' => 'system',
            'maintenance_mode' => 'system',
            'session_lifetime_minutes' => 'system'
        ];

        foreach ($data as $key => $val) {
            if ($key === 'page' || $key === 'action' || $key === 'csrf_token') continue;
            $group = $groupMap[$key] ?? 'general';
            $this->set($key, sanitize($val), $group);
        }
        self::$cachedSettings = null;
        return true;
    }

    /**
     * اطلاعات شرکت جهت چاپ در فاکتورها و گزارشات
     */
    public function getCompanyInfo() {
        $s = $this->getAll();
        return (object)[
            'name' => $s['company_name'] ?? 'سامانه یکپارچه هماکار',
            'brand' => $s['company_brand'] ?? 'هماکار ERP',
            'phone' => $s['company_phone'] ?? '۰۴۱۳۴۴۰۱۱۷۹',
            'email' => $s['company_email'] ?? 'info@hamakar.ir',
            'address' => $s['company_address'] ?? 'تبریز، بلوار آزادی، جنب شهرداری منطقه ۳، پلاک ۴۱',
            'economic_code' => $s['company_economic_code'] ?? '۱۴۰۰۹۱۵۶۳۲۱',
            'national_id' => $s['company_national_id'] ?? '۱۴۰۰۹۱۵۶۳۲۱',
            'registration_no' => $s['company_registration_no'] ?? '۵۵۲۰۰',
            'postal_code' => $s['company_postal_code'] ?? '۵۱۶۶۶۱۴۵۷۸',
        ];
    }
}
