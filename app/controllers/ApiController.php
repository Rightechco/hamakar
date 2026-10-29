<?php
// app/controllers/ApiController.php - وب‌سرویس جامع اتصال به نرم‌افزارهای حسابداری (سپیدار، هلو و...) و سامانه مودیان

require_once __DIR__ . '/../models/TaxpayerInvoice.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/InvoiceItem.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../core/Database.php';

class ApiController {
    private $db;
    private $moadianModel;
    private $invoiceModel;
    private $clientModel;
    private $apiKeyRecord = null;

    public function __construct() {
        $this->db = new Database();
        $this->moadianModel = new TaxpayerInvoice();
        $this->invoiceModel = new Invoice();
        $this->clientModel = new Client();

        // تنظیم هدر خروجی استاندارد JSON
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        $this->authenticate();
    }

    /**
     * احراز هویت درخواست از طریق کلید API
     */
    private function authenticate() {
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
        
        if (!$apiKey && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            if (preg_match('/Bearer\s+(\S+)/i', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
                $apiKey = $matches[1];
            }
        }

        if (!$apiKey) {
            $apiKey = $_GET['api_key'] ?? $_POST['api_key'] ?? null;
        }

        if (!$apiKey) {
            $this->response(401, [
                'success' => false,
                'error_code' => 'API_KEY_REQUIRED',
                'message' => 'کلید دسترسی API در هدر X-API-KEY یا Authorization Bearer ارسال نشده است.'
            ]);
        }

        $this->db->query("SELECT * FROM api_keys WHERE api_key = :key AND status = 'active' LIMIT 1");
        $this->db->bind(':key', $apiKey);
        $keyRecord = $this->db->fetch();

        if (!$keyRecord) {
            $this->response(403, [
                'success' => false,
                'error_code' => 'INVALID_API_KEY',
                'message' => 'کلید API نامعتبر است یا توسط مدیریت غیرفعال شده است.'
            ]);
        }

        // بروزرسانی مصرف روزانه
        $this->db->query("UPDATE api_keys SET daily_usage = daily_usage + 1 WHERE id = :id");
        $this->db->bind(':id', $keyRecord->id);
        $this->db->execute();

        $this->apiKeyRecord = $keyRecord;
    }

    private function response($statusCode, $data) {
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    /**
     * تست اتصال و سلامت وب‌سرویس
     */
    public function ping() {
        $settings = $this->moadianModel->getSettings();
        $this->response(200, [
            'success' => true,
            'message' => 'اتصال به وب‌سرویس هماکار با موفقیت برقرار شد.',
            'timestamp' => time(),
            'jalali_date' => date('Y-m-d H:i:s'),
            'system_info' => [
                'software' => 'Hamakar Enterprise ERP',
                'version' => '3.5.0-Commercial',
                'moadian_connected' => true,
                'memory_id' => $settings->memory_id,
                'tax_rate' => $settings->tax_rate
            ]
        ]);
    }

    /**
     * دریافت لیست فاکتورهای رسمی سامانه برای نرم‌افزار حسابداری
     * GET /index.php?page=api&action=official_invoices
     */
    public function official_invoices() {
        $status = $_GET['status'] ?? null;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        $sql = "SELECT i.*, 
                       c.name as client_name, 
                       c.economic_code as c_eco, 
                       c.national_code as c_nid,
                       c.phone as client_phone,
                       m.taxid as m_taxid,
                       m.uid as m_uid,
                       m.status as m_status,
                       m.subject_type as m_subject
                FROM invoices i
                JOIN clients c ON i.client_id = c.id
                LEFT JOIN moadian_invoices m ON (m.invoice_id = i.id)
                WHERE i.is_official = 1 ";

        if ($status) {
            $sql .= " AND (i.moadian_status = :st OR m.status = :st) ";
        }
        if ($startDate) {
            $sql .= " AND i.issue_date >= :sdate ";
        }
        if ($endDate) {
            $sql .= " AND i.issue_date <= :edate ";
        }

        $sql .= " ORDER BY i.id DESC LIMIT :lim";

        $this->db->query($sql);
        $this->db->bind(':lim', $limit, PDO::PARAM_INT);
        if ($status) $this->db->bind(':st', $status);
        if ($startDate) $this->db->bind(':sdate', $startDate);
        if ($endDate) $this->db->bind(':edate', $endDate);

        $rows = $this->db->fetchAll();
        $invoices = [];

        foreach ($rows as $r) {
            $invoices[] = [
                'id' => (int)$r->id,
                'invoice_number' => $r->invoice_number,
                'issue_date' => $r->issue_date,
                'due_date' => $r->due_date,
                'status' => $r->status,
                'is_official' => (bool)$r->is_official,
                'customer' => [
                    'id' => (int)$r->client_id,
                    'name' => $r->client_name,
                    'economic_code' => $r->economic_code ?: $r->c_eco,
                    'national_id' => $r->national_id ?: $r->c_nid,
                    'phone' => $r->client_phone
                ],
                'financials' => [
                    'subtotal' => (float)$r->subtotal,
                    'vat_amount' => (float)$r->vat_amount,
                    'total_amount' => (float)$r->total_amount,
                    'paid_amount' => (float)($r->paid_amount ?? 0)
                ],
                'moadian' => [
                    'taxid' => $r->moadian_taxid ?: $r->m_taxid,
                    'uid' => $r->moadian_uid ?: $r->m_uid,
                    'status' => $r->moadian_status ?: ($r->m_status ?? 'not_sent'),
                    'subject' => $r->m_subject ?? 'main',
                    'pattern_type' => (int)($r->pattern_type ?? 1)
                ]
            ];
        }

        $this->response(200, [
            'success' => true,
            'count' => count($invoices),
            'data' => $invoices
        ]);
    }

    /**
     * دریافت جزئیات و اقلام یک فاکتور رسمی
     * GET /index.php?page=api&action=invoice_details&id=X
     */
    public function invoice_details() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->response(400, ['success' => false, 'message' => 'شناسه فاکتور معتبر نیست.']);
        }

        $invoice = $this->invoiceModel->findByIdWithClient($id);
        if (!$invoice) {
            $this->response(404, ['success' => false, 'message' => 'فاکتور یافت نشد.']);
        }

        $items = $this->invoiceModel->getItemsByInvoiceId($id);
        $moadianRecord = $this->moadianModel->getByInvoiceId($id);

        $this->response(200, [
            'success' => true,
            'invoice' => $invoice,
            'items' => $items,
            'moadian_status' => $moadianRecord
        ]);
    }

    /**
     * ثبت فاکتور رسمی از نرم‌افزار حسابداری در هماکار و ارسال به سامانه مودیان
     * POST /index.php?page=api&action=create_official_invoice
     */
    public function create_official_invoice() {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);

        if (!$payload) {
            $payload = $_POST;
        }

        if (empty($payload)) {
            $this->response(400, ['success' => false, 'message' => 'بدنه درخواست نامعتبر یا خالی است (JSON معتبر ارسال شود).']);
        }

        // اعتبارسنجی فیلدهای ضروری
        $invoiceNumber = $payload['invoice_number'] ?? ('ACC-' . date('Ymd') . '-' . rand(1000, 9999));
        $clientId = $payload['client_id'] ?? null;

        // اگر مشتری بر اساس نام/کد ارسال شده بود
        if (!$clientId && !empty($payload['customer_name'])) {
            // یافتن یا ایجاد مشتری
            $this->db->query("SELECT id FROM clients WHERE name = :nm LIMIT 1");
            $this->db->bind(':nm', sanitize($payload['customer_name']));
            $c = $this->db->fetch();
            if ($c) {
                $clientId = $c->id;
            } else {
                $isLegal = !empty($payload['customer_economic_code']) || strlen($payload['customer_national_id'] ?? '') === 11;
                $clientId = $this->clientModel->create([
                    'name' => sanitize($payload['customer_name']),
                    'contact_person' => sanitize($payload['customer_name']),
                    'company_name' => sanitize($payload['customer_name']),
                    'user_type' => $isLegal ? 'legal' : 'real',
                    'client_type' => 'customer',
                    'national_code' => sanitize($payload['customer_national_id'] ?? ''),
                    'economic_code' => sanitize($payload['customer_economic_code'] ?? ''),
                    'email' => 'client_' . time() . '_' . rand(100, 999) . '@hamakar.ir',
                    'phone' => sanitize($payload['customer_phone'] ?? '04134401179'),
                    'address' => sanitize($payload['customer_address'] ?? 'ثبت شده توسط وب‌سرویس حسابداری')
                ]);
            }
        }

        if (!$clientId) {
            // مشتری پیش‌فرض سازمانی
            $this->db->query("SELECT id FROM clients ORDER BY id ASC LIMIT 1");
            $firstClient = $this->db->fetch();
            $clientId = $firstClient ? $firstClient->id : 1;
        }

        $settings = $this->moadianModel->getSettings();
        $taxRate = (float)($payload['vat_rate'] ?? $settings->tax_rate);

        // محاسبه مبالغ از روی آیتم‌ها
        $subtotal = 0;
        $items = $payload['items'] ?? [];
        $invoiceItemsData = [];

        if (!empty($items) && is_array($items)) {
            foreach ($items as $item) {
                $qty = (float)($item['quantity'] ?? 1);
                $price = (float)($item['price'] ?? $item['unit_price'] ?? 0);
                $subtotal += ($qty * $price);
                $invoiceItemsData[] = [
                    'product_id' => $item['product_id'] ?? null,
                    'service_id' => $item['service_id'] ?? null,
                    'quantity' => $qty,
                    'price' => $price
                ];
            }
        } else {
            $subtotal = (float)($payload['subtotal'] ?? $payload['total_amount'] ?? 0);
        }

        $vatAmount = round(($subtotal * $taxRate) / 100);
        $totalAmount = $subtotal + $vatAmount;

        $issueDate = $payload['issue_date'] ?? date('Y-m-d');
        // در صورت ارسال تاریخ جلالی، تبدیل شود
        if (strpos($issueDate, '/') !== false) {
            $parts = explode('/', $issueDate);
            if (count($parts) === 3 && (int)$parts[0] > 1300 && (int)$parts[0] < 1500) {
                $g = jalali_to_gregorian((int)$parts[0], (int)$parts[1], (int)$parts[2]);
                $issueDate = implode('-', $g);
            }
        }

        $invoiceData = [
            'invoice_number' => $invoiceNumber,
            'client_id' => $clientId,
            'contract_id' => $payload['contract_id'] ?? null,
            'invoice_type' => $payload['invoice_type'] ?? 'service',
            'is_official' => 1, // رسمی
            'category_id' => $payload['category_id'] ?? null,
            'issue_date' => $issueDate,
            'due_date' => $payload['due_date'] ?? $issueDate,
            'subtotal' => $subtotal,
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount,
            'description' => sanitize($payload['description'] ?? 'فاکتور رسمی صادر شده از نرم‌افزار حسابداری واسط'),
            'status' => $payload['status'] ?? 'pending',
            'economic_code' => sanitize($payload['customer_economic_code'] ?? ''),
            'national_id' => sanitize($payload['customer_national_id'] ?? ''),
            'pattern_type' => (int)($payload['pattern_type'] ?? 1)
        ];

        $invoiceId = $this->invoiceModel->create($invoiceData);

        if (!$invoiceId) {
            $this->response(500, ['success' => false, 'message' => 'خطا در ثبت فاکتور در پایگاه داده هماکار.']);
        }

        // ثبت اقلام فاکتور
        if (!empty($invoiceItemsData)) {
            $itemModel = new InvoiceItem();
            $itemModel->createItems($invoiceId, $invoiceItemsData);
        }

        // ثبت در کارپوشه سامانه مودیان و تولید TaxID
        $moadianId = $this->moadianModel->createFromInvoice($invoiceId, (int)$invoiceData['pattern_type']);
        $autoSend = $payload['auto_send_moadian'] ?? true;
        $transmitResult = null;

        if ($moadianId && $autoSend) {
            $transmitResult = $this->moadianModel->transmitToTaxAuthority($moadianId);
        }

        $createdMoadian = $this->moadianModel->getById($moadianId);

        $this->response(201, [
            'success' => true,
            'message' => 'فاکتور رسمی با موفقیت ثبت و شناسه یکتای ۲۲ رقمی مالیاتی صادر شد.',
            'invoice_id' => (int)$invoiceId,
            'invoice_number' => $invoiceNumber,
            'taxid' => $createdMoadian ? $createdMoadian->taxid : null,
            'uid' => $createdMoadian ? $createdMoadian->uid : null,
            'status' => $createdMoadian ? $createdMoadian->status : 'pending',
            'financials' => [
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'total_amount' => $totalAmount
            ],
            'moadian_transmit' => $transmitResult
        ]);
    }

    /**
     * همگام‌سازی وضعیت فاکتور با سامانه مودیان
     * POST /index.php?page=api&action=sync_moadian
     */
    public function sync_moadian() {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true) ?: $_POST;

        $invoiceId = $payload['invoice_id'] ?? null;
        $taxId = $payload['taxid'] ?? null;

        $moadianRecord = null;
        if ($invoiceId) {
            $moadianRecord = $this->moadianModel->getByInvoiceId($invoiceId);
        } elseif ($taxId) {
            $this->db->query("SELECT * FROM moadian_invoices WHERE taxid = :t LIMIT 1");
            $this->db->bind(':t', $taxId);
            $moadianRecord = $this->db->fetch();
        }

        if (!$moadianRecord) {
            $this->response(404, ['success' => false, 'message' => 'صورتحساب مودیان با این مشخصات یافت نشد.']);
        }

        // اگر ارسال نشده بود ارسال کن
        if ($moadianRecord->status !== 'success') {
            $res = $this->moadianModel->transmitToTaxAuthority($moadianRecord->id);
            $this->response(200, [
                'success' => true,
                'action' => 'transmitted',
                'result' => $res
            ]);
        }

        $inq = $this->moadianModel->inquiryStatus($moadianRecord->id);
        $this->response(200, [
            'success' => true,
            'action' => 'inquiry',
            'inquiry' => $inq
        ]);
    }

    /**
     * آمار و خلاصه مودیان برای نرم‌افزار حسابداری
     * GET /index.php?page=api&action=moadian_summary
     */
    public function moadian_summary() {
        $stats = $this->moadianModel->getSummaryStats();
        $settings = $this->moadianModel->getSettings();

        $this->response(200, [
            'success' => true,
            'summary' => [
                'total_invoices' => (int)($stats->total_count ?? 0),
                'confirmed_invoices' => (int)($stats->success_count ?? 0),
                'pending_invoices' => (int)($stats->pending_count ?? 0),
                'canceled_invoices' => (int)($stats->canceled_count ?? 0),
                'total_gross_amount' => (float)($stats->total_gross ?? 0),
                'total_vat_amount' => (float)($stats->total_vat ?? 0),
                'total_final_amount' => (float)($stats->total_final ?? 0),
                'memory_id' => $settings->memory_id,
                'economic_code' => $settings->economic_code,
                'is_sandbox' => (bool)$settings->is_sandbox
            ]
        ]);
    }
}
