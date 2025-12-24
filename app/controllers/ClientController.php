<?php
// app/controllers/ClientController.php - نسخه نهایی و کامل

// بارگذاری مدل Payment در ابتدای فایل برای اطمینان از دسترس بودن کلاس
require_once __DIR__ . '/../models/Payment.php';

class ClientController {
    private $auth;
    private $user;
    private $clientModel;
    private $contractModel;
    private $invoiceModel;
    private $projectModel;
    private $ticketModel;
    private $ticketReplyModel;
    private $paymentModel; // ✅ اضافه شدن مدل Payment

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['client']);
        
        $this->user = $this->auth->user();
        
        // نمونه‌سازی تمام مدل‌های مورد نیاز
        $this->clientModel = new Client();
        $this->contractModel = new Contract();
        $this->invoiceModel = new Invoice();
        $this->projectModel = new Project();
        $this->ticketModel = new Ticket();
        $this->ticketReplyModel = new TicketReply();
        $this->paymentModel = new Payment(); // ✅ نمونه‌سازی مدل Payment
    }

    public function dashboard() {
        $client = $this->clientModel->findByUserId($this->user->id);
        if (!$client) {
            // ✅ توصیه: اینجا هم از 'client_layout' استفاده کنید اگر برای مشتری مناسب‌تر است
            view('client/dashboard', ['layout' => 'admin_layout', 'title' => 'داشبورد مشتری', 'stats' => null]);
            return;
        }

        $contracts = $this->contractModel->getContractsByClientId($client->id);
        $invoices = $this->invoiceModel->getInvoicesByClientId($client->id);

        $unpaid_invoices = array_filter($invoices, function($inv) {
            return in_array($inv->status, ['pending', 'overdue']);
        });

        $stats = [
            'contracts_count' => count($contracts),
            'invoices_count' => count($invoices),
            'unpaid_invoices_count' => count($unpaid_invoices),
            'total_unpaid_amount' => array_sum(array_column($unpaid_invoices, 'total_amount'))
        ];

        // ✅ توصیه: اینجا هم از 'client_layout' استفاده کنید اگر برای مشتری مناسب‌تر است
        view('client/dashboard', [
            'layout' => 'admin_layout',
            'title' => 'داشبورد مشتری',
            'stats' => $stats
        ]);
    }
    
    public function myContracts() {
        $client = $this->clientModel->findByUserId($this->user->id);
        $contracts = $client ? $this->contractModel->getContractsByClientId($client->id) : [];

        // ✅ تبدیل تاریخ‌ها در کنترلر
        foreach ($contracts as $contract) {
            if (!empty($contract->start_date) && $contract->start_date != '0000-00-00') {
                $contract->start_date_jalali = jdate('Y/m/d', strtotime($contract->start_date));
            } else {
                $contract->start_date_jalali = '---';
            }
            if (!empty($contract->end_date) && $contract->end_date != '0000-00-00') {
                $contract->end_date_jalali = jdate('Y/m/d', strtotime($contract->end_date));
            } else {
                $contract->end_date_jalali = '---';
            }
            if (!empty($contract->next_renewal_date) && $contract->next_renewal_date != '0000-00-00') {
                $contract->next_renewal_date_jalali = jdate('Y/m/d', strtotime($contract->next_renewal_date));
            } else {
                $contract->next_renewal_date_jalali = '---';
            }
        }

        // ✅ توصیه: اینجا هم از 'client_layout' استفاده کنید
        view('client/my_contracts', [
            'layout' => 'admin_layout',
            'title' => 'قراردادهای من',
            'contracts' => $contracts
        ]);
    }

    public function myInvoices() {
        // ✅ حذف require_once JalaliDate از اینجا
        
        $client = $this->clientModel->findByUserId($this->user->id);
        $invoices = $client ? $this->invoiceModel->getInvoicesByClientId($client->id) : [];

        // ✅ تبدیل تاریخ‌ها در کنترلر
        foreach ($invoices as $invoice) {
            if (!empty($invoice->issue_date) && $invoice->issue_date != '0000-00-00') {
                $invoice->issue_date_jalali = jdate('Y/m/d', strtotime($invoice->issue_date));
            } else {
                $invoice->issue_date_jalali = '---';
            }
            if (!empty($invoice->due_date) && $invoice->due_date != '0000-00-00') {
                $invoice->due_date_jalali = jdate('Y/m/d', strtotime($invoice->due_date));
            } else {
                $invoice->due_date_jalali = '---';
            }
        }

        // ✅ توصیه: اینجا هم از 'client_layout' استفاده کنید
        view('client/my_invoices', [
            'layout' => 'admin_layout',
            'title' => 'فاکتورهای من',
            'invoices' => $invoices
        ]);
    }

    /**
     * ✅ متد جدید برای نمایش پروژه‌های مشتری
     */
    public function myProjects() {
        $client = $this->clientModel->findByUserId($this->user->id);
        $projects = $client ? $this->projectModel->getProjectsByClientId($client->id) : [];

        // ✅ تبدیل تاریخ‌ها در کنترلر
        foreach ($projects as $project) {
            if (!empty($project->start_date) && $project->start_date != '0000-00-00') {
                $project->start_date_jalali = jdate('Y/m/d', strtotime($project->start_date));
            } else {
                $project->start_date_jalali = '---';
            }
            if (!empty($project->due_date) && $project->due_date != '0000-00-00') {
                $project->due_date_jalali = jdate('Y/m/d', strtotime($project->due_date));
            } else {
                $project->due_date_jalali = '---';
            }
        }

        // ✅ توصیه: اینجا هم از 'client_layout' استفاده کنید
        view('client/my_projects', [
            'layout' => 'admin_layout',
            'title' => 'پروژه‌های من',
            'projects' => $projects
        ]);
    }

    public function myTickets() {
        // ✅ نام متد به getAllForClient تغییر یافت (اگر این متد در مدل Ticket وجود دارد)
        // اگر این متد فقط بر اساس user_id کار می‌کند و نیازی به client_id نیست، می‌توانید client = $this->clientModel->findByUserId($this->user->id); را حذف کنید.
        $tickets = $this->ticketModel->getAllForClient($this->user->id);

        // ✅ تبدیل تاریخ‌ها در کنترلر
        foreach ($tickets as $ticket) {
            if (!empty($ticket->created_at) && $ticket->created_at != '0000-00-00') {
                $ticket->created_at_jalali = jdate('Y/m/d H:i', strtotime($ticket->created_at));
            } else {
                $ticket->created_at_jalali = '---';
            }
        }

        // ✅ توصیه: اینجا هم از 'client_layout' استفاده کنید
        view('client/my_tickets', [
            'layout' => 'admin_layout',
            'title' => 'تیکت‌های من',
            'tickets' => $tickets
        ]);
    }

    // ✅ این متد جدید را اضافه کنید
    public function createTicket() {
        // ✅ توصیه: اینجا هم از 'client_layout' استفاده کنید
        view('client/create_ticket', [
            'layout' => 'admin_layout',
            'title' => 'ایجاد تیکت جدید'
        ]);
    }

    public function viewContract($id) {
        $contract = $this->contractModel->findById($id);
        $client = $this->clientModel->findByUserId($this->auth->user()->id);

        // ✅ بررسی دسترسی: مطمئن شوید قرارداد متعلق به مشتری فعلی است
        if (!$contract || !$client || $contract->client_id != $client->id) {
            FlashMessage::set('message', 'قرارداد مورد نظر یافت نشد یا شما به آن دسترسی ندارید.', 'error');
            redirect(APP_URL . '/index.php?page=client&action=my_contracts');
            return;
        }

        // ✅ تبدیل تاریخ‌ها در کنترلر برای ویو قرارداد
        $contract->start_date_jalali = (!empty($contract->start_date) && $contract->start_date != '0000-00-00') ? jdate('Y/m/d', strtotime($contract->start_date)) : '---';
        $contract->end_date_jalali = (!empty($contract->end_date) && $contract->end_date != '0000-00-00') ? jdate('Y/m/d', strtotime($contract->end_date)) : '---';
        $contract->final_payment_due_date_jalali = (!empty($contract->final_payment_due_date) && $contract->final_payment_due_date != '0000-00-00') ? jdate('Y/m/d', strtotime($contract->final_payment_due_date)) : '---';
        $contract->next_renewal_date_jalali = (!empty($contract->next_renewal_date) && $contract->next_renewal_date != '0000-00-00') ? jdate('Y/m/d', strtotime($contract->next_renewal_date)) : '---';


        // ✅ اطلاعات شرکت باید از جای معتبرتری (مثلا کانفیگ) بیایند، نه ثابت در اینجا
        view('admin/contracts/view_contract', [ // ✅ این ویو مشترک است و معمولاً بدون سایدبار پنل است
            'layout' => 'guest_layout',
            'title' => 'جزئیات قرارداد',
            'contract' => $contract,
            'customer' => $client,
            'companyInfo' => [
                'name' => 'شرکت رایان تکرو', // ✅ اطلاعات به‌روز شده
                'address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه 3',
                'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
                'email' => 'info@rayantakro.com',
                'logo_path' => APP_URL . '/assets/img/company_logo.png',
                'signature_path' => APP_URL . '/assets/img/company_signature.png',
                'seal_path' => APP_URL . '/assets/img/company_seal.png',
            ]
        ]);
    }

    public function viewInvoice($id) {
        $invoice = $this->invoiceModel->findById($id);
        $client = $this->clientModel->findByUserId($this->auth->user()->id); // استفاده از user()->id

        if (!$invoice || !$client || $invoice->client_id != $client->id) {
            FlashMessage::set('message', 'فاکتور مورد نظر یافت نشد یا شما به آن دسترسی ندارید.', 'error');
            redirect(APP_URL . '/index.php?page=client&action=my_invoices');
            return;
        }
        $payments = $this->paymentModel->getAllPaymentsByInvoiceId($id);

        // ✅ تبدیل تاریخ‌ها در کنترلر برای ویو فاکتور
        $invoice->issue_date_jalali = (!empty($invoice->issue_date) && $invoice->issue_date != '0000-00-00') ? jdate('Y/m/d', strtotime($invoice->issue_date)) : '---';
        $invoice->due_date_jalali = (!empty($invoice->due_date) && $invoice->due_date != '0000-00-00') ? jdate('Y/m/d', strtotime($invoice->due_date)) : '---';

        // ✅ اطلاعات شرکت باید از جای معتبرتری (مثلا کانفیگ) بیایند
        view('admin/invoices/view_invoice', [ // ✅ این ویو مشترک است و معمولاً بدون سایدبار پنل است
            'layout' => 'guest_layout',
            'title' => 'جزئیات فاکتور',
            'invoice' => $invoice,
            'payments' => $payments,
            'companyInfo' => [
                'name' => 'شرکت رایان تکرو', // ✅ اطلاعات به‌روز شده
                'address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه 3',
                'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
                'email' => 'info@rayantakro.com',
                'logo_path' => APP_URL . '/assets/img/company_logo.png',
                'signature_path' => APP_URL . '/assets/img/company_signature.png',
                'seal_path' => APP_URL . '/assets/img/company_seal.png',
            ]
        ]);
    }

      public function payInvoice($id) {
        $invoice = $this->invoiceModel->findById($id); // فاکتور را پیدا کن

        // بررسی‌های اولیه (امنیت و وضعیت فاکتور)
        if (!$invoice) {
            FlashMessage::set('message', 'فاکتور مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=client&action=my_invoices');
            return;
        }

        $client = $this->clientModel->findByUserId($this->auth->user()->id); //
        if (!$client || $invoice->client_id != $client->id) { //
            FlashMessage::set('message', 'شما به این فاکتور دسترسی ندارید.', 'error');
            redirect(APP_URL . '/index.php?page=client&action=my_invoices');
            return;
        }

        $remainingAmount = ($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0); //

        if ($remainingAmount <= 0 || $invoice->status == 'paid' || $invoice->status == 'canceled') { //
            FlashMessage::set('message', 'این فاکتور قبلاً به صورت کامل پرداخت شده است یا قابل پرداخت نیست.', 'info');
            redirect(APP_URL . '/index.php?page=client&action=invoice_view&id=' . $id);
            return;
        }
        
        // ✅✅✅ تغییر اصلی در اینجا: هدایت به PaymentController برای شروع فرآیند پرداخت ✅✅✅
        // توجه: PaymentController::request انتظار invoice_id را در $_GET می‌کشد
        redirect(APP_URL . '/index.php?page=payment&action=request&invoice_id=' . $invoice->id);
    }


    public function storeTicket() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $this->auth->user();
            $ticketId = $this->ticketModel->create([
                'user_id' => $user->id,
                'subject' => sanitize($_POST['subject']),
                'department' => sanitize($_POST['department']),
                'priority' => sanitize($_POST['priority'])
            ]);
            if ($ticketId) {
                // ✅ مطمئن شوید که body تیکت را نیز به عنوان اولین پاسخ ذخیره می‌کنید
                $this->ticketReplyModel->create(['ticket_id' => $ticketId, 'user_id' => $user->id, 'body' => sanitize($_POST['body'])]);
                FlashMessage::set('message', 'تیکت شما با موفقیت ایجاد شد.');
                redirect(APP_URL . '/index.php?page=client&action=view_ticket&id=' . $ticketId);
            } else {
                FlashMessage::set('message', 'خطا در ایجاد تیکت.', 'error');
                redirect_back();
            }
        }
    }
    
    // ✅ متد برای مشاهده تیکت (اگر در ClientController باشد)
    public function viewTicket($id) {
        $ticket = $this->ticketModel->findByIdWithDetails($id); // فرض بر وجود این متد در Ticket Model
        
        // ✅ بررسی دسترسی: مطمئن شوید تیکت متعلق به کاربر فعلی است
        if (!$ticket || $ticket->user_id != $this->user->id) {
            FlashMessage::set('message', 'تیکت مورد نظر یافت نشد یا شما به آن دسترسی ندارید.', 'error');
            redirect(APP_URL . '/index.php?page=client&action=my_tickets');
            return;
        }

        $replies = $this->ticketReplyModel->getRepliesByTicketId($id);

        // ✅ تبدیل تاریخ‌ها برای نمایش در ویو تیکت
        foreach ($replies as $reply) {
            if (!empty($reply->created_at) && $reply->created_at != '0000-00-00') {
                $reply->created_at_jalali = jdate('Y/m/d H:i', strtotime($reply->created_at));
            } else {
                $reply->created_at_jalali = '---';
            }
        }
        
        view('shared/tickets/view', [ // ✅ این ویو می‌تواند مشترک باشد
            'layout' => 'admin_layout', // ✅ استفاده از layout مشتری
            'title' => 'مشاهده تیکت: ' . sanitize($ticket->subject),
            'ticket' => $ticket,
            'replies' => $replies
        ]);
    }

    // ✅ متد برای ارسال پاسخ تیکت (اگر در ClientController باشد)
    public function storeTicketReply($ticketId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['body'])) {
            $user = $this->auth->user();

            // ✅ بررسی دسترسی مجدد برای اطمینان از اینکه کاربر اجازه پاسخ به این تیکت را دارد
            $ticket = $this->ticketModel->findById($ticketId);
            if (!$ticket || $ticket->user_id != $user->id) {
                FlashMessage::set('message', 'شما اجازه پاسخ به این تیکت را ندارید.', 'error');
                redirect(APP_URL . '/index.php?page=client&action=my_tickets');
                return;
            }

            $replyId = $this->ticketReplyModel->create([
                'ticket_id' => $ticketId,
                'user_id' => $user->id,
                'body' => $_POST['body'] // محتوای HTML از ویرایشگر
            ]);

            if ($replyId) {
                // منطق آپلود فایل (اگر نیاز دارید)
                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                    $uploadDir = 'uploads/attachments/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileName = uniqid() . '-' . basename($_FILES['attachment']['name']);
                    $uploadFile = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadFile)) {
                        $this->ticketReplyModel->createAttachment($replyId, $_FILES['attachment']['name'], $uploadFile, $_FILES['attachment']['size']);
                    }
                }
                
                // به‌روزرسانی وضعیت تیکت به 'answered' یا 'customer_reply'
                $this->ticketModel->updateStatus($ticketId, 'customer_reply'); // یا 'answered'
                FlashMessage::set('message', 'پاسخ شما با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت پاسخ.', 'error');
            }
            redirect(APP_URL . '/index.php?page=client&action=view_ticket&id=' . $ticketId);
        } else {
            FlashMessage::set('message', 'متن پاسخ نمی‌تواند خالی باشد.', 'error');
            redirect(APP_URL . '/index.php?page=client&action=view_ticket&id=' . $ticketId);
        }
    }

    public function create($data) {
        $this->db->query('INSERT INTO client_contacts (client_id, user_id, position) VALUES (:client_id, :user_id, :position)');
        $this->db->bind(':client_id', $data['client_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':position', $data['position']);
        return $this->db->execute();
    }

    public function getContactsByClientId($clientId) {
        $this->db->query('SELECT cc.*, u.name as user_name, u.mobile_number, u.email FROM client_contacts cc JOIN users u ON cc.user_id = u.id WHERE cc.client_id = :client_id');
        $this->db->bind(':client_id', $clientId);
        return $this->db->fetchAll();
    }
}
