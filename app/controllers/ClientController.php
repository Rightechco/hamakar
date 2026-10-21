<?php
// app/controllers/ClientController.php - نسخه بهینه‌شده و کامل

require_once __DIR__ . '/../models/Payment.php';

class ClientController extends Controller {
    private $auth;
    private $user;
    private $db;
    private $clientModel;
    private $contractModel;
    private $invoiceModel;
    private $projectModel;
    private $ticketModel;
    private $ticketReplyModel;
    private $paymentModel;

    public function __construct() {
        // اتصال به دیتابیس
        $this->db = new Database();

        // احراز هویت و محدودسازی به نقش client
        $this->auth = new Auth();
        $this->auth->restrict(['client']);
        $this->user = $this->auth->user();
        
        // نمونه‌سازی مدل‌ها
        $this->clientModel = new Client();
        $this->contractModel = new Contract();
        $this->invoiceModel = new Invoice();
        $this->projectModel = new Project();
        $this->ticketModel = new Ticket();
        $this->ticketReplyModel = new TicketReply();
        $this->paymentModel = new Payment();
    }

    // =========================================================
    // 1. داشبورد + بررسی انقضای لایسنس
    // =========================================================
    // =========================================================
    // 1. داشبورد اصلی کلاینت
    // =========================================================
    public function dashboard() {
        $userId = $this->user->id;
        
        $this->db->query("SELECT * FROM user_licenses WHERE user_id = :uid ORDER BY expires_at DESC LIMIT 1");
        $this->db->bind(':uid', $userId);
        
        $license = method_exists($this->db, 'single') ? $this->db->single() : (method_exists($this->db, 'fetch') ? $this->db->fetch() : null);

        $client = $this->clientModel->findByUserId($userId);
        
        $stats = [
            'contracts_count' => 0,
            'invoices_count' => 0,
            'unpaid_invoices_count' => 0,
            'total_unpaid_amount' => 0
        ];

        if ($client) {
            $contracts = $this->contractModel->getContractsByClientId($client->id) ?? [];
            $invoices = $this->invoiceModel->getInvoicesByClientId($client->id) ?? [];

            $unpaid_invoices = array_filter($invoices, function($inv) {
                return in_array($inv->status, ['pending', 'overdue']);
            });

            $stats = [
                'contracts_count' => count($contracts),
                'invoices_count' => count($invoices),
                'unpaid_invoices_count' => count($unpaid_invoices),
                'total_unpaid_amount' => array_sum(array_column($unpaid_invoices, 'total_amount'))
            ];
        }

        // ارسال لایوت client_layout جهت فراخوانی سایدبار و نوبار
        $this->view('client/dashboard', [
            'layout' => 'client_layout',
            'title' => 'داشبورد مشتری',
            'stats' => $stats,
            'license' => $license,
            'user' => $this->user
        ]);
    }
    // =========================================================
    // 2. تنظیمات هوش مصنوعی (منشی هوشمند)
    // =========================================================
    
    public function ai_settings() {
        $userId = $this->user->id;
        $this->db->query("SELECT * FROM business_profiles WHERE user_id = :uid");
        $this->db->bind(':uid', $userId);
        $profile = $this->db->single();
        
        $this->view('client/ai_settings', [
            'layout' => 'admin_layout',
            'title' => 'تنظیمات منشی هوشمند',
            'profile' => $profile
        ]);
    }

    public function storeAiProfile() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $this->user->id;
            
            $business_name = sanitize(trim($_POST['business_name'] ?? ''));
            $description = sanitize(trim($_POST['description'] ?? ''));
            $products = sanitize(trim($_POST['products'] ?? ''));
            $faq = sanitize(trim($_POST['faq'] ?? ''));

            // بررسی وجود پروفایل قبلی
            $this->db->query("SELECT id FROM business_profiles WHERE user_id = :uid");
            $this->db->bind(':uid', $userId);
            $exists = $this->db->single();
            
            if ($exists) {
                $sql = "UPDATE business_profiles SET business_name = :b, description = :d, products_list = :p, faq_list = :f WHERE user_id = :uid";
            } else {
                $sql = "INSERT INTO business_profiles (user_id, business_name, description, products_list, faq_list, is_active) VALUES (:uid, :b, :d, :p, :f, 1)";
            }
            
            $this->db->query($sql);
            $this->db->bind(':uid', $userId);
            $this->db->bind(':b', $business_name);
            $this->db->bind(':d', $description);
            $this->db->bind(':p', $products);
            $this->db->bind(':f', $faq);
            
            if ($this->db->execute()) {
                FlashMessage::set('اطلاعات منشی هوشمند با موفقیت ذخیره شد.', 'success');
            } else {
                FlashMessage::set('خطا در ذخیره اطلاعات.', 'danger');
            }
            
            redirect('index.php?page=client&action=ai_settings');
        }
    }

    // =========================================================
    // 3. بخش‌های مالی و قراردادها
    // =========================================================

    public function myContracts() {
        $client = $this->clientModel->findByUserId($this->user->id);
        $contracts = $client ? ($this->contractModel->getContractsByClientId($client->id) ?? []) : [];

        foreach ($contracts as $contract) {
            $contract->start_date_jalali = (!empty($contract->start_date)) ? jdate('Y/m/d', strtotime($contract->start_date)) : '---';
            $contract->end_date_jalali = (!empty($contract->end_date)) ? jdate('Y/m/d', strtotime($contract->end_date)) : '---';
        }

        $this->view('client/my_contracts', [
            'layout' => 'admin_layout',
            'title' => 'قراردادهای من',
            'contracts' => $contracts
        ]);
    }

    public function myInvoices() {
        $client = $this->clientModel->findByUserId($this->user->id);
        $invoices = $client ? ($this->invoiceModel->getInvoicesByClientId($client->id) ?? []) : [];

        foreach ($invoices as $invoice) {
            $invoice->issue_date_jalali = (!empty($invoice->issue_date)) ? jdate('Y/m/d', strtotime($invoice->issue_date)) : '---';
            $invoice->due_date_jalali = (!empty($invoice->due_date)) ? jdate('Y/m/d', strtotime($invoice->due_date)) : '---';
        }

        $this->view('client/my_invoices', [
            'layout' => 'admin_layout',
            'title' => 'فاکتورهای من',
            'invoices' => $invoices
        ]);
    }

    public function myProjects() {
        $client = $this->clientModel->findByUserId($this->user->id);
        $projects = $client ? ($this->projectModel->getProjectsByClientId($client->id) ?? []) : [];

        foreach ($projects as $project) {
            $project->start_date_jalali = (!empty($project->start_date)) ? jdate('Y/m/d', strtotime($project->start_date)) : '---';
        }

        $this->view('client/my_projects', [
            'layout' => 'admin_layout',
            'title' => 'پروژه‌های من',
            'projects' => $projects
        ]);
    }

    public function my_licenses() {
        // تغییر ORDER BY L.created_at به ORDER BY L.id DESC جهت جلوگیری از خطای 1054
        $this->db->query("SELECT L.*, P.title as plan_title 
                          FROM user_licenses L 
                          LEFT JOIN plans P ON L.plan_id = P.id 
                          WHERE L.user_id = :uid ORDER BY L.id DESC");
        $this->db->bind(':uid', $this->user->id);
        
        $licenses = method_exists($this->db, 'resultSet') ? $this->db->resultSet() : $this->db->fetchAll();

        $this->view('client/my_licenses', [
            'layout' => 'client_layout',
            'title' => 'لایسنس‌های من',
            'licenses' => $licenses
        ]);
    }

    // =========================================================
    // 4. نمایش جزئیات (مشاهده فاکتور و قرارداد)
    // =========================================================

    public function viewContract($id) {
        $contract = $this->contractModel->findById($id);
        $client = $this->clientModel->findByUserId($this->user->id);

        if (!$contract || !$client || $contract->client_id != $client->id) {
            FlashMessage::set('قرارداد یافت نشد یا دسترسی ندارید.', 'danger');
            redirect('index.php?page=client&action=my_contracts');
            return;
        }
        
        $contract->start_date_jalali = !empty($contract->start_date) ? jdate('Y/m/d', strtotime($contract->start_date)) : '---';

        $this->view('admin/contracts/view_contract', [
            'layout' => 'guest_layout',
            'title' => 'جزئیات قرارداد',
            'contract' => $contract,
            'customer' => $client,
            'companyInfo' => $this->getCompanyInfo()
        ]);
    }

    public function viewInvoice($id) {
        $invoice = $this->invoiceModel->findById($id);
        $client = $this->clientModel->findByUserId($this->user->id);

        if (!$invoice || !$client || $invoice->client_id != $client->id) {
            FlashMessage::set('فاکتور یافت نشد.', 'danger');
            redirect('index.php?page=client&action=my_invoices');
            return;
        }
        
        $payments = $this->paymentModel->getAllPaymentsByInvoiceId($id) ?? [];
        $invoice->issue_date_jalali = !empty($invoice->issue_date) ? jdate('Y/m/d', strtotime($invoice->issue_date)) : '---';

        $this->view('admin/invoices/view_invoice', [
            'layout' => 'guest_layout',
            'title' => 'مشاهده فاکتور',
            'invoice' => $invoice,
            'payments' => $payments,
            'companyInfo' => $this->getCompanyInfo()
        ]);
    }
    
    private function getCompanyInfo() {
        return [
            'name' => 'شرکت رایان تکرو',
            'address' => 'تبریز، بلوار آزادی',
            'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
            'email' => 'info@rayantakro.com',
            'logo_path' => defined('APP_URL') ? APP_URL . '/assets/img/logo.png' : '/assets/img/logo.png',
        ];
    }

    public function payInvoice($id) {
        $invoice = $this->invoiceModel->findById($id);
        $client = $this->clientModel->findByUserId($this->user->id);

        if (!$invoice || !$client || $invoice->client_id != $client->id) {
            FlashMessage::set('دسترسی غیرمجاز.', 'danger');
            redirect('index.php?page=client&action=my_invoices');
            return;
        }

        redirect('index.php?page=payment&action=request&invoice_id=' . $invoice->id);
    }

    // =========================================================
    // 5. سیستم تیکتینگ
    // =========================================================

    public function myTickets() {
        $tickets = $this->ticketModel->getAllForClient($this->user->id) ?? [];
        
        foreach ($tickets as $t) {
            $t->created_at_jalali = !empty($t->created_at) ? jdate('Y/m/d H:i', strtotime($t->created_at)) : '---';
        }

        $this->view('client/my_tickets', [
            'layout' => 'admin_layout',
            'title' => 'تیکت‌های من',
            'tickets' => $tickets
        ]);
    }

    public function createTicket() {
        $this->view('client/create_ticket', [
            'layout' => 'admin_layout',
            'title' => 'ارسال تیکت جدید'
        ]);
    }

    public function storeTicket() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subject = sanitize(trim($_POST['subject'] ?? ''));
            $department = sanitize(trim($_POST['department'] ?? ''));
            $priority = sanitize(trim($_POST['priority'] ?? ''));
            $body = sanitize(trim($_POST['body'] ?? ''));

            if (empty($subject) || empty($body)) {
                FlashMessage::set('لطفاً عنوان و متن تیکت را وارد کنید.', 'danger');
                redirect('index.php?page=client&action=createTicket');
                return;
            }

            $ticketId = $this->ticketModel->create([
                'user_id' => $this->user->id,
                'subject' => $subject,
                'department' => $department,
                'priority' => $priority
            ]);
            
            if ($ticketId) {
                $this->ticketReplyModel->create([
                    'ticket_id' => $ticketId, 
                    'user_id' => $this->user->id, 
                    'body' => $body
                ]);
                FlashMessage::set('تیکت با موفقیت ایجاد شد.', 'success');
                redirect('index.php?page=client&action=view_ticket&id=' . $ticketId);
            } else {
                FlashMessage::set('خطا در ایجاد تیکت.', 'danger');
                redirect('index.php?page=client&action=createTicket');
            }
        }
    }

    public function viewTicket($id) {
        $ticket = $this->ticketModel->findByIdWithDetails($id);
        
        if (!$ticket || $ticket->user_id != $this->user->id) {
            FlashMessage::set('تیکت یافت نشد یا دسترسی ندارید.', 'danger');
            redirect('index.php?page=client&action=my_tickets');
            return;
        }

        $replies = $this->ticketReplyModel->getRepliesByTicketId($id) ?? [];
        foreach ($replies as $r) {
            $r->created_at_jalali = !empty($r->created_at) ? jdate('Y/m/d H:i', strtotime($r->created_at)) : '---';
        }
        
        $this->view('shared/tickets/view', [
            'layout' => 'admin_layout',
            'title' => 'مشاهده تیکت #' . $ticket->id,
            'ticket' => $ticket,
            'replies' => $replies
        ]);
    }

    public function storeTicketReply($ticketId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $body = sanitize(trim($_POST['body'] ?? ''));
            
            if (empty($body)) {
                FlashMessage::set('متن پاسخ نمی‌تواند خالی باشد.', 'danger');
                redirect('index.php?page=client&action=view_ticket&id=' . $ticketId);
                return;
            }

            $ticket = $this->ticketModel->findById($ticketId);
            if (!$ticket || $ticket->user_id != $this->user->id) {
                redirect('index.php?page=client&action=my_tickets');
                return;
            }

            $replyId = $this->ticketReplyModel->create([
                'ticket_id' => $ticketId,
                'user_id' => $this->user->id,
                'body' => $body
            ]);

            if ($replyId) {
                $this->ticketModel->updateStatus($ticketId, 'customer_reply');
                FlashMessage::set('پاسخ شما با موفقیت ثبت شد.', 'success');
            } else {
                FlashMessage::set('خطا در ثبت پاسخ.', 'danger');
            }
            
            redirect('index.php?page=client&action=view_ticket&id=' . $ticketId);
        }
    }
    // =========================================================
    // 6. اطلاعیه‌ها و اخبار
    // =========================================================
    // =========================================================
    // اطلاعیه‌ها و اخبار سیستم
    // =========================================================
    public function announcements_index() {
        $announcements = [];
        try {
            $this->db->query("SELECT * FROM announcements WHERE is_active = 1 ORDER BY created_at DESC");
            if (method_exists($this->db, 'resultSet')) {
                $announcements = $this->db->resultSet() ?? [];
            } elseif (method_exists($this->db, 'fetchAll')) {
                $announcements = $this->db->fetchAll() ?? [];
            }
        } catch (Throwable $e) {
            $announcements = [];
        }

        if (!empty($announcements)) {
            foreach ($announcements as $item) {
                $item->created_at_jalali = (!empty($item->created_at) && function_exists('jdate')) 
                    ? jdate('Y/m/d H:i', strtotime($item->created_at)) 
                    : ($item->created_at ?? '---');
            }
        }

        $this->view('client/announcements_index', [
            'layout' => 'client_layout',
            'title' => 'اطلاعیه‌ها و اخبار سیستم',
            'announcements' => $announcements
        ]);
    }
}