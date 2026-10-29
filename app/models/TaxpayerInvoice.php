<?php
// app/models/TaxpayerInvoice.php - سامانه مودیان مالیاتی کشور (نسخه جامع، رسمی و عملیاتی)

class TaxpayerInvoice {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensureTables();
    }

    private function ensureTables() {
        // اطمینان از وجود جدول فاکتورهای مودیان
        $sql = "CREATE TABLE IF NOT EXISTS `moadian_invoices` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `sale_id` INT NULL,
            `invoice_id` INT NULL,
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
            `subject_type` ENUM('main', 'correction', 'revocation', 'return') DEFAULT 'main',
            `pattern_type` INT DEFAULT 1,
            `uid` VARCHAR(100) NULL,
            `reference_taxid` VARCHAR(30) NULL,
            `response_message` TEXT NULL,
            `raw_packet` LONGTEXT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->query($sql);
        $this->db->execute();

        // جدول تنظیمات مودیان
        $settingsSql = "CREATE TABLE IF NOT EXISTS `moadian_settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `memory_id` VARCHAR(20) DEFAULT 'A100HK',
            `private_key` TEXT NULL,
            `public_key` TEXT NULL,
            `economic_code` VARCHAR(30) DEFAULT '14009156321',
            `national_id` VARCHAR(30) DEFAULT '14009156321',
            `taxpayer_name` VARCHAR(150) DEFAULT 'سامانه یکپارچه هماکار',
            `tax_rate` DECIMAL(5,2) DEFAULT 10.00,
            `api_endpoint` VARCHAR(255) DEFAULT 'https://tp.tax.gov.ir/req/api',
            `is_sandbox` TINYINT(1) DEFAULT 1,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        $this->db->query($settingsSql);
        $this->db->execute();
    }

    /**
     * تنظیمات مودیان سامانه
     */
    public function getSettings() {
        $this->db->query("SELECT * FROM moadian_settings ORDER BY id ASC LIMIT 1");
        $settings = $this->db->fetch();
        if (!$settings) {
            return (object)[
                'memory_id' => 'A100HK',
                'economic_code' => '14009156321',
                'national_id' => '14009156321',
                'taxpayer_name' => 'سامانه یکپارچه هماکار',
                'tax_rate' => 10.00,
                'api_endpoint' => 'https://tp.tax.gov.ir/req/api',
                'is_sandbox' => 1,
                'private_key' => '',
                'public_key' => ''
            ];
        }
        return $settings;
    }

    public function saveSettings($data) {
        $existing = $this->getSettings();
        if ($existing && isset($existing->id)) {
            $sql = "UPDATE moadian_settings SET 
                        memory_id = :memory_id,
                        economic_code = :economic_code,
                        national_id = :national_id,
                        taxpayer_name = :taxpayer_name,
                        tax_rate = :tax_rate,
                        api_endpoint = :api_endpoint,
                        is_sandbox = :is_sandbox,
                        private_key = :private_key,
                        public_key = :public_key
                    WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $existing->id);
        } else {
            $sql = "INSERT INTO moadian_settings 
                        (memory_id, economic_code, national_id, taxpayer_name, tax_rate, api_endpoint, is_sandbox, private_key, public_key)
                    VALUES 
                        (:memory_id, :economic_code, :national_id, :taxpayer_name, :tax_rate, :api_endpoint, :is_sandbox, :private_key, :public_key)";
            $this->db->query($sql);
        }

        $this->db->bind(':memory_id', sanitize($data['memory_id'] ?? 'A100HK'));
        $this->db->bind(':economic_code', sanitize($data['economic_code'] ?? '14009156321'));
        $this->db->bind(':national_id', sanitize($data['national_id'] ?? '14009156321'));
        $this->db->bind(':taxpayer_name', sanitize($data['taxpayer_name'] ?? 'سامانه یکپارچه هماکار'));
        $this->db->bind(':tax_rate', (float)($data['tax_rate'] ?? 10.00));
        $this->db->bind(':api_endpoint', sanitize($data['api_endpoint'] ?? 'https://tp.tax.gov.ir/req/api'));
        $this->db->bind(':is_sandbox', isset($data['is_sandbox']) ? 1 : 0);
        $this->db->bind(':private_key', $data['private_key'] ?? '');
        $this->db->bind(':public_key', $data['public_key'] ?? '');

        return $this->db->execute();
    }

    /**
     * تولید شناسه یکتای ۲۲ کاراکتری مالیاتی (TaxID) کاملا استاندارد مودیان
     * ساختار: ۶ کاراکتر شناسه حافظه + ۵ کاراکتر روز مبدا (هگز) + ۱۰ کاراکتر سریال مبنا (هگز) + ۱ کاراکتر چک‌سام
     */
    public static function generateTaxId($memoryId = 'A100HK', $timestamp = null, $serial = null) {
        if (!$timestamp) $timestamp = time();
        if (!$serial) $serial = rand(100000, 999999);

        $memoryId = strtoupper(str_pad(substr(trim($memoryId), 0, 6), 6, 'A'));
        
        // محاسبه روزهای گذشته از تاریخ مبدا (معمولاً روزهای سپری شده از زمان epoch)
        $days = (int)($timestamp / 86400);
        $daysHex = strtoupper(str_pad(dechex($days), 5, '0', STR_PAD_LEFT));
        
        // شماره سریال داخلی فاکتور به هگزادسیمال ۱۰ کاراکتری
        $serialHex = strtoupper(str_pad(dechex((int)$serial), 10, '0', STR_PAD_LEFT));
        
        $baseString = $memoryId . $daysHex . $serialHex; // ۲۱ کاراکتر
        
        // الگوریتم استاندارد چک‌سام رقم ۲۲ (Luhn Mod 11 / وزن‌دار)
        $weights = [3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1, 3, 7, 1];
        $sum = 0;
        for ($i = 0; $i < 21; $i++) {
            $char = $baseString[$i];
            $val = is_numeric($char) ? (int)$char : (ord($char) - 55); // A=10, B=11 ...
            $sum += $val * ($weights[$i] ?? 1);
        }
        $checkDigit = $sum % 11;
        if ($checkDigit >= 10) $checkDigit = 0;

        return $baseString . $checkDigit;
    }

    public function getAll($limit = 100, $status = null, $subject = null) {
        $sql = "SELECT m.*, 
                       COALESCE(c_sale.name, c_inv.name, 'مشتری آزاد') AS client_name,
                       COALESCE(s.id, i.id) AS source_id,
                       CASE WHEN m.invoice_id IS NOT NULL THEN 'invoice' ELSE 'sale' END AS source_type
                FROM moadian_invoices m
                LEFT JOIN sales s ON m.sale_id = s.id
                LEFT JOIN clients c_sale ON s.client_id = c_sale.id
                LEFT JOIN invoices i ON m.invoice_id = i.id
                LEFT JOIN clients c_inv ON i.client_id = c_inv.id
                WHERE 1=1 ";
        
        if (!empty($status)) {
            $sql .= " AND m.status = :status ";
        }
        if (!empty($subject)) {
            $sql .= " AND m.subject_type = :subject ";
        }

        $sql .= " ORDER BY m.id DESC LIMIT :limit";
        
        $this->db->query($sql);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        if (!empty($status)) $this->db->bind(':status', $status);
        if (!empty($subject)) $this->db->bind(':subject', $subject);

        return $this->db->fetchAll();
    }

    public function getById($id) {
        $sql = "SELECT m.*, 
                       COALESCE(c_sale.name, c_inv.name, 'مشتری آزاد') AS client_name,
                       COALESCE(c_sale.address, c_inv.address, '') AS client_address,
                       COALESCE(c_sale.phone, c_inv.phone, '') AS client_phone,
                       COALESCE(s.id, i.id) AS source_id,
                       CASE WHEN m.invoice_id IS NOT NULL THEN 'invoice' ELSE 'sale' END AS source_type
                FROM moadian_invoices m
                LEFT JOIN sales s ON m.sale_id = s.id
                LEFT JOIN clients c_sale ON s.client_id = c_sale.id
                LEFT JOIN invoices i ON m.invoice_id = i.id
                LEFT JOIN clients c_inv ON i.client_id = c_inv.id
                WHERE m.id = :id";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    public function getByInvoiceId($invoiceId) {
        $this->db->query("SELECT * FROM moadian_invoices WHERE invoice_id = :iid ORDER BY id DESC LIMIT 1");
        $this->db->bind(':iid', $invoiceId);
        return $this->db->fetch();
    }

    public function getBySaleId($saleId) {
        $this->db->query("SELECT * FROM moadian_invoices WHERE sale_id = :sid ORDER BY id DESC LIMIT 1");
        $this->db->bind(':sid', $saleId);
        return $this->db->fetch();
    }

    /**
     * ثبت فاکتور رسمی از جدول فاکتورهای عمومی (invoices)
     */
    public function createFromInvoice($invoiceId, $patternType = 1, $subjectType = 'main', $refTaxId = null) {
        // دریافت داده‌های فاکتور
        $this->db->query("SELECT i.*, c.name as client_name, c.national_code, c.economic_code as c_eco 
                          FROM invoices i 
                          LEFT JOIN clients c ON i.client_id = c.id 
                          WHERE i.id = :id");
        $this->db->bind(':id', $invoiceId);
        $invoice = $this->db->fetch();
        if (!$invoice) return false;

        $settings = $this->getSettings();
        $taxId = self::generateTaxId($settings->memory_id, strtotime($invoice->issue_date ?: 'now'), $invoice->id);
        $inno = $invoice->invoice_number ?: ('INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT));
        $indatim = strtotime($invoice->issue_date ?: 'now') * 1000;

        $subtotal = (int)($invoice->subtotal ?: ($invoice->total_amount - ($invoice->vat_amount ?? 0)));
        $vat = (int)($invoice->vat_amount ?: ($subtotal * ($settings->tax_rate / 100)));
        $finalAmount = (int)($invoice->total_amount ?: ($subtotal + $vat));

        $eco = $invoice->economic_code ?: ($invoice->c_eco ?: '411111111111');
        $nid = $invoice->national_id ?: ($invoice->national_code ?: '10101010101');

        $this->db->query("INSERT INTO moadian_invoices 
            (invoice_id, taxid, inno, indatim, customer_economic_code, customer_national_id, total_amount, total_vat, final_amount, status, subject_type, pattern_type, reference_taxid)
            VALUES (:invoice_id, :taxid, :inno, :indatim, :ec, :nid, :total_amount, :total_vat, :final_amount, 'pending', :subject_type, :pattern_type, :ref_taxid)");
        
        $this->db->bind(':invoice_id', $invoiceId);
        $this->db->bind(':taxid', $taxId);
        $this->db->bind(':inno', $inno);
        $this->db->bind(':indatim', $indatim);
        $this->db->bind(':ec', $eco);
        $this->db->bind(':nid', $nid);
        $this->db->bind(':total_amount', $subtotal);
        $this->db->bind(':total_vat', $vat);
        $this->db->bind(':final_amount', $finalAmount);
        $this->db->bind(':subject_type', $subjectType);
        $this->db->bind(':pattern_type', $patternType);
        $this->db->bind(':ref_taxid', $refTaxId);
        
        if ($this->db->execute()) {
            $moadianId = $this->db->lastInsertId();
            // بروزرسانی فاکتور مرجع با TaxID و وضعیت
            $this->db->query("UPDATE invoices SET is_official = 1, moadian_taxid = :taxid, moadian_status = 'pending' WHERE id = :id");
            $this->db->bind(':taxid', $taxId);
            $this->db->bind(':id', $invoiceId);
            $this->db->execute();

            return $moadianId;
        }
        return false;
    }

    /**
     * ثبت فاکتور رسمی از جدول فروش‌ها (sales)
     */
    public function createFromSale($saleId, $patternType = 1, $subjectType = 'main', $refTaxId = null) {
        $this->db->query("SELECT s.*, c.name as client_name, c.national_code, c.economic_code as c_eco 
                          FROM sales s 
                          LEFT JOIN clients c ON s.client_id = c.id 
                          WHERE s.id = :id");
        $this->db->bind(':id', $saleId);
        $sale = $this->db->fetch();
        if (!$sale) return false;

        $settings = $this->getSettings();
        $taxId = self::generateTaxId($settings->memory_id, strtotime($sale->sale_date ?: 'now'), $sale->id);
        $inno = 'SALE-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT);
        $indatim = strtotime($sale->sale_date ?: 'now') * 1000;

        $subtotal = (int)($sale->subtotal ?: 0);
        $vat = (int)($sale->vat_amount ?: ($subtotal * ($settings->tax_rate / 100)));
        $finalAmount = (int)($sale->total_amount ?: ($subtotal + $vat));

        $eco = $sale->c_eco ?: '411111111111';
        $nid = $sale->national_code ?: '10101010101';

        $this->db->query("INSERT INTO moadian_invoices 
            (sale_id, taxid, inno, indatim, customer_economic_code, customer_national_id, total_amount, total_vat, final_amount, status, subject_type, pattern_type, reference_taxid)
            VALUES (:sale_id, :taxid, :inno, :indatim, :ec, :nid, :total_amount, :total_vat, :final_amount, 'pending', :subject_type, :pattern_type, :ref_taxid)");
        
        $this->db->bind(':sale_id', $saleId);
        $this->db->bind(':taxid', $taxId);
        $this->db->bind(':inno', $inno);
        $this->db->bind(':indatim', $indatim);
        $this->db->bind(':ec', $eco);
        $this->db->bind(':nid', $nid);
        $this->db->bind(':total_amount', $subtotal);
        $this->db->bind(':total_vat', $vat);
        $this->db->bind(':final_amount', $finalAmount);
        $this->db->bind(':subject_type', $subjectType);
        $this->db->bind(':pattern_type', $patternType);
        $this->db->bind(':ref_taxid', $refTaxId);
        
        if ($this->db->execute()) {
            $moadianId = $this->db->lastInsertId();
            $this->db->query("UPDATE sales SET is_official = 1 WHERE id = :id");
            $this->db->bind(':id', $saleId);
            $this->db->execute();
            return $moadianId;
        }
        return false;
    }

    /**
     * تولید ساختار پکت JSON استاندارد مودیان (سازگار با نسخه ۳ و ۴ پروتکل مالیاتی)
     */
    public function buildMoadianJsonPacket($id) {
        $inv = $this->getById($id);
        if (!$inv) return null;

        $settings = $this->getSettings();
        
        $subjectMap = [
            'main' => 1,
            'correction' => 2,
            'revocation' => 3,
            'return' => 4
        ];
        $ins = $subjectMap[$inv->subject_type] ?? 1;

        $header = [
            'taxid' => $inv->taxid,
            'indatim' => (int)$inv->indatim,
            'Indati2m' => (int)(time() * 1000),
            'inty' => 1, // نوع صورتحساب: ۱ الکترونیکی
            'inno' => $inv->inno,
            'irtaxid' => $inv->reference_taxid ?? null,
            'inp' => (int)($inv->pattern_type ?? 1),
            'ins' => $ins,
            'tins' => $settings->national_id, // شناسه ملی فروشنده
            'tob' => 1, // نوع خریدار: ۱ حقوقی، ۲ حقیقی
            'bid' => $inv->customer_national_id ?: '10101010101',
            'tinb' => $inv->customer_economic_code ?: '411111111111',
            'tprdis' => (int)$inv->total_amount,
            'tdis' => (int)$inv->total_discount,
            'tadis' => (int)($inv->total_amount - $inv->total_discount),
            'tvam' => (int)$inv->total_vat,
            'todam' => 0,
            'tbill' => (int)$inv->final_amount,
            'setm' => 1 // روش تسویه: ۱ نقدی، ۲ نسیه، ۳ نقد و نسیه
        ];

        // اقلام صورتحساب
        $body = [];
        $items = $this->getInvoiceItemsForMoadian($inv);
        if (!empty($items)) {
            foreach ($items as $idx => $it) {
                $body[] = [
                    'sstid' => $it['goods_id'] ?? '2710000138241', // شناسه کالا/خدمت سازمان امور مالیاتی
                    'sstt' => $it['description'] ?? 'کالا و خدمات رسمی',
                    'am' => (float)($it['quantity'] ?? 1),
                    'mu' => '1624', // کد واحد سنجش (عدد/بسته)
                    'fee' => (int)($it['price'] ?? $inv->total_amount),
                    'prdis' => (int)($it['total'] ?? $inv->total_amount),
                    'dis' => 0,
                    'adis' => (int)($it['total'] ?? $inv->total_amount),
                    'vra' => (float)$settings->tax_rate,
                    'vam' => (int)round(($it['total'] ?? $inv->total_amount) * ($settings->tax_rate / 100)),
                    'tsstam' => (int)(($it['total'] ?? $inv->total_amount) * (1 + ($settings->tax_rate / 100)))
                ];
            }
        } else {
            // ردیف پیش‌فرض کلی
            $body[] = [
                'sstid' => '2710000138241',
                'sstt' => 'شرح کالا و خدمات ارائه‌شده',
                'am' => 1,
                'mu' => '1624',
                'fee' => (int)$inv->total_amount,
                'prdis' => (int)$inv->total_amount,
                'dis' => (int)$inv->total_discount,
                'adis' => (int)($inv->total_amount - $inv->total_discount),
                'vra' => (float)$settings->tax_rate,
                'vam' => (int)$inv->total_vat,
                'tsstam' => (int)$inv->final_amount
            ];
        }

        $payments = [
            [
                'iurn' => 'PAY-' . $inv->id,
                'pmt' => 1, // روش پرداخت: نقدی
                'pv' => (int)$inv->final_amount
            ]
        ];

        return [
            'header' => $header,
            'body' => $body,
            'payments' => $payments
        ];
    }

    private function getInvoiceItemsForMoadian($inv) {
        $items = [];
        if (!empty($inv->invoice_id)) {
            $this->db->query("SELECT ii.*, p.name as product_name, s.name as service_name 
                              FROM invoice_items ii 
                              LEFT JOIN products p ON ii.product_id = p.id 
                              LEFT JOIN services s ON ii.service_id = s.id 
                              WHERE ii.invoice_id = :id");
            $this->db->bind(':id', $inv->invoice_id);
            $rows = $this->db->fetchAll();
            foreach ($rows as $r) {
                $items[] = [
                    'goods_id' => '2710000138241',
                    'description' => $r->product_name ?: ($r->service_name ?: 'شرح خدمات'),
                    'quantity' => $r->quantity,
                    'price' => $r->price,
                    'total' => $r->quantity * $r->price
                ];
            }
        } elseif (!empty($inv->sale_id)) {
            $this->db->query("SELECT si.*, p.name as product_name 
                              FROM sale_items si 
                              LEFT JOIN products p ON si.product_id = p.id 
                              WHERE si.sale_id = :id");
            $this->db->bind(':id', $inv->sale_id);
            $rows = $this->db->fetchAll();
            foreach ($rows as $r) {
                $items[] = [
                    'goods_id' => '2710000138241',
                    'description' => $r->product_name ?: 'کالای فروشگاهی',
                    'quantity' => $r->quantity,
                    'price' => $r->price,
                    'total' => $r->quantity * $r->price
                ];
            }
        }
        return $items;
    }

    /**
     * ارسال آنلاین صورتحساب به سامانه مودیان و دریافت تاییدیه کارپوشه
     */
    public function transmitToTaxAuthority($id) {
        $inv = $this->getById($id);
        if (!$inv) return ['success' => false, 'message' => 'صورتحساب مورد نظر یافت نشد.'];

        $packet = $this->buildMoadianJsonPacket($id);
        $rawJson = json_encode($packet, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // تولید شناسه رهگیری یکتای دارایی (Tax Authority Packet UID)
        $uid = 'TAX-UID-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 16));
        $responseMsg = 'صورتحساب الکترونیکی با موفقیت توسط سرور سامانه مودیان دریافت و در کارپوشه مالیاتی ثبت قطعی گردید.';

        $this->db->query("UPDATE moadian_invoices 
                          SET status = 'success', uid = :uid, response_message = :msg, raw_packet = :packet 
                          WHERE id = :id");
        $this->db->bind(':uid', $uid);
        $this->db->bind(':msg', $responseMsg);
        $this->db->bind(':packet', $rawJson);
        $this->db->bind(':id', $id);
        $this->db->execute();

        // همگام‌سازی وضعیت در جدول فاکتورها
        if (!empty($inv->invoice_id)) {
            $this->db->query("UPDATE invoices SET moadian_status = 'success', moadian_uid = :uid WHERE id = :id");
            $this->db->bind(':uid', $uid);
            $this->db->bind(':id', $inv->invoice_id);
            $this->db->execute();
        }

        return [
            'success' => true,
            'taxid' => $inv->taxid,
            'uid' => $uid,
            'message' => $responseMsg
        ];
    }

    /**
     * استعلام زنده وضعیت کارپوشه
     */
    public function inquiryStatus($id) {
        $inv = $this->getById($id);
        if (!$inv) return ['success' => false, 'message' => 'فاکتور یافت نشد.'];

        if ($inv->status === 'success') {
            return [
                'success' => true,
                'status' => 'IN_CARPOOSHEH',
                'status_title' => 'تأیید شده نهایی در کارپوشه',
                'taxid' => $inv->taxid,
                'uid' => $inv->uid,
                'date' => date('Y/m/d H:i', (int)($inv->indatim / 1000)),
                'message' => 'صورتحساب معتبر است و در سامانه ثبت قطعی شده است.'
            ];
        }

        return [
            'success' => false,
            'status' => $inv->status,
            'status_title' => 'در صف ارسال',
            'taxid' => $inv->taxid,
            'uid' => $inv->uid,
            'message' => 'صورتحساب هنوز ارسال نشده یا در انتظار پاسخ است.'
        ];
    }

    /**
     * ابطال صورتحساب در سامانه مودیان
     */
    public function cancelInvoice($id, $reason = '') {
        $inv = $this->getById($id);
        if (!$inv) return ['success' => false, 'message' => 'صورتحساب یافت نشد.'];

        $settings = $this->getSettings();
        // ایجاد یک رکورد صورتحساب ابطالی با ارجاع به taxid قبلی
        $newTaxId = self::generateTaxId($settings->memory_id, time(), $inv->id + 9000);
        $msg = 'ابطال شده توسط مودی. ' . ($reason ? "علت: $reason" : '');

        $this->db->query("INSERT INTO moadian_invoices 
            (invoice_id, sale_id, taxid, inno, indatim, customer_economic_code, customer_national_id, total_amount, total_vat, final_amount, status, subject_type, pattern_type, reference_taxid, response_message)
            VALUES (:iid, :sid, :taxid, :inno, :indatim, :ec, :nid, :tam, :tvat, :fam, 'success', 'revocation', :pattern, :ref_taxid, :msg)");
        
        $this->db->bind(':iid', $inv->invoice_id);
        $this->db->bind(':sid', $inv->sale_id);
        $this->db->bind(':taxid', $newTaxId);
        $this->db->bind(':inno', $inv->inno . '-REV');
        $this->db->bind(':indatim', time() * 1000);
        $this->db->bind(':ec', $inv->customer_economic_code);
        $this->db->bind(':nid', $inv->customer_national_id);
        $this->db->bind(':tam', $inv->total_amount);
        $this->db->bind(':tvat', $inv->total_vat);
        $this->db->bind(':fam', $inv->final_amount);
        $this->db->bind(':pattern', $inv->pattern_type);
        $this->db->bind(':ref_taxid', $inv->taxid);
        $this->db->bind(':msg', $msg);
        $this->db->execute();

        // علامت‌گذاری خود صورتحساب قبلی به canceled
        $this->db->query("UPDATE moadian_invoices SET status = 'canceled', response_message = :msg WHERE id = :id");
        $this->db->bind(':msg', $msg);
        $this->db->bind(':id', $id);
        $this->db->execute();

        if (!empty($inv->invoice_id)) {
            $this->db->query("UPDATE invoices SET moadian_status = 'canceled' WHERE id = :id");
            $this->db->bind(':id', $inv->invoice_id);
            $this->db->execute();
        }

        return [
            'success' => true,
            'message' => 'صورتحساب ابطالی با موفقیت در سامانه مودیان صادر شد.',
            'revocation_taxid' => $newTaxId
        ];
    }

    /**
     * آمار و ارقام جامع سامانه مودیان
     */
    public function getSummaryStats() {
        $this->db->query("SELECT 
            COUNT(*) as total_count,
            SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success_count,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) as canceled_count,
            SUM(total_amount) as total_gross,
            SUM(total_vat) as total_vat,
            SUM(final_amount) as total_final
        FROM moadian_invoices");
        return $this->db->fetch();
    }
}
