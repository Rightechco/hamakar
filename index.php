<?php
// public/index.php - نسخه نهایی و کامل با تمام مسیرها

// فعال‌سازی نمایش خطا برای دیباگ
ini_set('display_errors', 1);
error_reporting(E_ALL);

// تنظیم منطقه زمانی پیش‌فرض برای کل برنامه
date_default_timezone_set('Asia/Tehran');

// شروع سشن
session_start();

// بارگذاری فایل‌های پیکربندی و اصلی
require_once __DIR__ . '/app/config/app.php';
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/config/sms.php';
require_once __DIR__ . '/app/config/payment.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/lib/JalaliDate.php';


// بارگذاری کلاس‌های هسته
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Auth.php';
require_once __DIR__ . '/app/core/Validator.php';
require_once __DIR__ . '/app/core/Helpers.php';
require_once __DIR__ . '/app/core/FlashMessage.php';

// بارگذاری مدل‌ها
require_once __DIR__ . '/app/models/User.php';
require_once __DIR__ . '/app/models/Client.php'; 
require_once __DIR__ . '/app/models/Contract.php';
require_once __DIR__ . '/app/models/Invoice.php'; 
require_once __DIR__ . '/app/models/Payment.php'; 
require_once __DIR__ . '/app/models/Project.php'; 
require_once __DIR__ . '/app/models/Task.php'; 
require_once __DIR__ . '/app/models/Report.php';
require_once __DIR__ . '/app/models/Ticket.php';
require_once __DIR__ . '/app/models/TicketReply.php';
require_once __DIR__ . '/app/models/Attendance.php';
require_once __DIR__ . '/app/models/LeaveRequest.php';
require_once __DIR__ . '/app/models/Chat.php';
require_once __DIR__ . '/app/models/Payroll.php';
require_once __DIR__ . '/app/models/PayrollSetting.php';
require_once __DIR__ . '/app/models/ClientLog.php';
require_once __DIR__ . '/app/models/Announcement.php';
require_once __DIR__ . '/app/models/ClientContact.php';
require_once __DIR__ . '/app/models/TrainingNeed.php';   
require_once __DIR__ . '/app/models/TrainingCourse.php'; 
require_once __DIR__ . '/app/models/SkillAssessment.php';
require_once __DIR__ . '/app/models/MarketingLead.php';
require_once __DIR__ . '/app/models/Product.php'; 
require_once __DIR__ . '/app/models/Purchase.php'; 
require_once __DIR__ . '/app/models/Sale.php';     
require_once __DIR__ . '/app/models/Category.php';
require_once __DIR__ . '/app/models/Reconciliation.php';
require_once __DIR__ . '/app/models/FixedAsset.php';
require_once __DIR__ . '/app/models/Budget.php';
require_once __DIR__ . '/app/models/Expense.php';
require_once __DIR__ . '/app/models/FinancialReport.php';


// بارگذاری کنترلرها و کتابخانه‌ها
require_once __DIR__ . '/app/controllers/HomeController.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/AdminController.php';
require_once __DIR__ . '/app/controllers/EmployeeController.php';
require_once __DIR__ . '/app/controllers/ClientController.php'; 
require_once __DIR__ . '/app/controllers/PaymentController.php';
require_once __DIR__ . '/app/controllers/AjaxController.php';
require_once __DIR__ . '/app/lib/JalaliDate.php';
require_once __DIR__ . '/app/lib/Occasions.php';
require_once __DIR__ . '/app/controllers/ProductController.php';  
require_once __DIR__ . '/app/controllers/PurchasesController.php'; 
require_once __DIR__ . '/app/controllers/SalesController.php';     
require_once __DIR__ . '/app/controllers/CategoryController.php';


// مقداردهی اولیه سیستم احراز هویت
$auth = new Auth();

// ردیابی فعالیت کاربر
if ($auth->check()) {
    $userActivityModel = new User();
    $userActivityModel->updateLastActivity($auth->user()->id);
}

// سیستم مسیریابی (Router)
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// منطق ریدایرکت‌های اولیه
if ($page === 'home' && !$auth->check()) {
    redirect(APP_URL . '/index.php?page=login&action=show');
} elseif ($page === 'login' && $auth->check()) {
    if ($auth->isAdmin()) {
        redirect(APP_URL . '/index.php?page=admin&action=dashboard');
    } elseif ($auth->isEmployee()) {
        redirect(APP_URL . '/index.php?page=employee&action=dashboard');
    } else {
        redirect(APP_URL . '/index.php?page=client&action=dashboard');
    }
}

// اجرای مسیرها
$controller = null;
$targetMethod = null;

switch ($page) {
    case 'home':
        $controller = new HomeController();
        $targetMethod = 'index';
        break;
    case 'login':
        $controller = new AuthController();
        $targetMethod = ($action === 'show') ? 'showLogin' : 'login';
        break;
    case 'logout':
        $controller = new AuthController();
        $targetMethod = 'logout';
        break;

    case 'admin':
        $controller = new AdminController();
        switch ($action) {
            case 'addChecklistItem': $targetMethod = 'addChecklistItem'; break;
            case 'toggleChecklistItem': $targetMethod = 'toggleChecklistItem'; break;
            case 'deleteChecklistItem': $targetMethod = 'deleteChecklistItem'; break;
            case 'showApiSettings': $targetMethod = 'showApiSettings'; break;
            case 'storeApiKey': $targetMethod = 'storeApiKey'; break;
            case 'deleteApiKey': $targetMethod = 'deleteApiKey'; break;
            
            case 'showMarketingForm': $targetMethod = 'showMarketingForm'; break;
            case 'processMarketingSearch': $targetMethod = 'processMarketingSearch'; break;
            case 'deleteMapLead': $targetMethod = 'deleteMapLead'; break;
            case 'deleteNiazroozLead': $targetMethod = 'deleteNiazroozLead'; break;
            case 'deleteNeshanLead': $targetMethod = 'deleteNeshanLead'; break;
            
            case 'financial_reports': $targetMethod = 'financialReports'; break;
            case 'export_clients': $targetMethod = 'exportClients'; break;
            case 'show_import_form': $targetMethod = 'showImportForm'; break;
            case 'import_clients': $targetMethod = 'importClients'; break;
            case 'dashboard': $targetMethod = 'dashboard'; break;
            case 'profitAndLossReport': $targetMethod = 'profitAndLossReport'; break;

            // Users
            case 'users': $targetMethod = 'users'; break;
            case 'users_create': $targetMethod = 'createUser'; break;
            case 'users_store': $targetMethod = 'storeUser'; break;
            case 'users_edit': $targetMethod = 'editUser'; break;
            case 'users_update': $targetMethod = 'updateUser'; break;
            case 'users_delete': $targetMethod = 'deleteUser'; break;
            case 'get_payroll_data': $targetMethod = 'getAutomatedPayrollData'; break;

            // Clients
            case 'clients': $targetMethod = 'clients'; break;
            case 'clients_create': $targetMethod = 'createClient'; break;
            case 'clients_store': $targetMethod = 'storeClient'; break;
            case 'clients_edit': $targetMethod = 'editClient'; break;
            case 'clients_update': $targetMethod = 'updateClient'; break;
            case 'clients_delete': $targetMethod = 'deleteClient'; break;
            case 'clients_logs': $targetMethod = 'viewClientLogs'; break;
            case 'clients_store_log': $targetMethod = 'storeClientLog'; break;
            
            // Contracts
            case 'contracts': $targetMethod = 'contracts'; break;
            case 'contracts_create': $targetMethod = 'createContract'; break;
            case 'contracts_store': $targetMethod = 'storeContract'; break;
            case 'contracts_edit': $targetMethod = 'editContract'; break;
            case 'contracts_update': $targetMethod = 'updateContract'; break;
            case 'contracts_delete': $targetMethod = 'deleteContract'; break;
            case 'view_contract': $targetMethod = 'viewContract'; break;
            case 'send_contract_reminder': $targetMethod = 'sendContractReminder'; break;
            // Invoices
            case 'invoices': $targetMethod = 'invoices'; break;
            case 'invoices_create': $targetMethod = 'createInvoice'; break;
            case 'invoices_store': $targetMethod = 'storeInvoice'; break;
            case 'invoices_edit': $targetMethod = 'editInvoice'; break;
            case 'invoices_update': $targetMethod = 'updateInvoice'; break;
            case 'invoices_delete': $targetMethod = 'deleteInvoice'; break;
            case 'view_invoice': $targetMethod = 'viewInvoice'; break;
            case 'send_invoice_reminder': $targetMethod = 'sendInvoiceReminder'; break;
            case 'invoice_add_payment': $targetMethod = 'addPaymentToInvoice'; break;
            // Projects
            case 'projects': $targetMethod = 'projects'; break;
            case 'projects_create': $targetMethod = 'createProject'; break;
            case 'projects_store': $targetMethod = 'storeProject'; break;
            case 'projects_edit': $targetMethod = 'editProject'; break;
            case 'projects_update': $targetMethod = 'updateProject'; break;
            case 'projects_delete': $targetMethod = 'deleteProject'; break;
            case 'view_project': $targetMethod = 'viewProject'; break;
            case 'projects_add_member': $targetMethod = 'addProjectMember'; break;
            case 'projects_create_task': $targetMethod = 'createTask'; break;
            // Tickets
            case 'tickets': $targetMethod = 'tickets'; break;
            case 'view_ticket': $targetMethod = 'viewTicket'; break;
            case 'store_ticket_reply': $targetMethod = 'storeTicketReply'; break;
            case 'projects_add_member':
            $targetMethod = 'projects_add_member';
            break;
            
            
            // Reports
            case 'reports': $targetMethod = 'reports'; break;
            case 'detailed_reports': $targetMethod = 'detailedReports'; break;
            // HR
            case 'attendance_report': $targetMethod = 'attendanceReport'; break;
            case 'manage_leave_requests': $targetMethod = 'manageLeaveRequests'; break;
            case 'process_leave_request': $targetMethod = 'processLeaveRequest'; break;
            // SMS
            case 'sms_panel': $targetMethod = 'smsPanel'; break;
            case 'send_custom_bulk_sms': $targetMethod = 'sendCustomBulkSms'; break;
            case 'send_occasional_sms': $targetMethod = 'sendOccasionalSms'; break;
            case 'send_selected_occasion_sms': $targetMethod = 'sendSelectedOccasionSms'; break;
            
            // مسیرهای ماژول حسابداری
            case 'accounting_accounts': $targetMethod = 'accountingAccounts'; break;
            case 'create_voucher_form': $targetMethod = 'createVoucherForm'; break;
            case 'create_account_form': $targetMethod = 'createAccountForm'; break;
            case 'store_account': $targetMethod = 'storeAccount'; break;
            case 'store_voucher': $targetMethod = 'storeVoucher'; break;
            case 'accounting_expenses': $targetMethod = 'accountingExpenses'; break;
            case 'store_expense': $targetMethod = 'storeExpense'; break;
            case 'account_ledger': $targetMethod = 'accountLedger'; break;
            case 'general_journal': $targetMethod = 'generalJournal'; break;
            case 'vat_report': $targetMethod = 'vatReport'; break; 
            case 'close_fiscal_year': $targetMethod = 'closeFiscalYear'; break;
            case 'delete_voucher': $targetMethod = 'deleteVoucher'; break;
            case 'subledger_report': $targetMethod = 'subledgerReport'; break;
            case 'reverse_voucher': $targetMethod = 'reverseVoucher'; break;
            case 'budgeting': $targetMethod = 'budgeting'; break;
            case 'delete_budget': $targetMethod = 'deleteBudget'; break;
            case 'budget_report': $targetMethod = 'budgetReport'; break;
            case 'bank_reconciliation': $targetMethod = 'bankReconciliation'; break;
            case 'process_reconciliation': $targetMethod = 'processReconciliation'; break; 
            case 'fixed_assets': $targetMethod = 'manageFixedAssets'; break;
            case 'store_fixed_asset': $targetMethod = 'storeFixedAsset'; break;
            case 'delete_fixed_asset': $targetMethod = 'deleteFixedAsset'; break;
            case 'run_depreciation_form': $targetMethod = 'showDepreciationForm'; break;
            case 'run_depreciation': $targetMethod = 'runDepreciation'; break;
            case 'cash_flow_report': $targetMethod = 'cashFlowReport'; break;
            case 'expense_analysis_report': $targetMethod = 'expenseAnalysisReport'; break;

            // اطلاعیه‌ها
            case 'announcements_index': $targetMethod = 'announcementsIndex'; break; 
            case 'announcements_create': $targetMethod = 'announcementsCreate'; break;
            case 'announcements_store': $targetMethod = 'announcementsStore'; break;
            case 'announcements_edit': $targetMethod = 'announcementsEdit'; break;
            case 'announcements_update': $targetMethod = 'announcementsUpdate'; break;
            case 'announcements_delete': $targetMethod = 'announcementsDelete'; break;
            
            // مدیریت نیازسنجی آموزشی
            case 'training_needs': $targetMethod = 'trainingNeeds'; break;
            case 'view_training_need': $targetMethod = 'viewTrainingNeed'; break;
            case 'process_training_need': $targetMethod = 'processTrainingNeed'; break;
            case 'manage_training_courses': $targetMethod = 'manageTrainingCourses'; break;
            case 'store_training_course': $targetMethod = 'storeTrainingCourse'; break;
            case 'training_needs_analysis': $targetMethod = 'trainingNeedsAnalysis'; break;
            
            // مدیریت آزمون مهارتی
            case 'manage_skill_assessments': $targetMethod = 'manageSkillAssessments'; break;
            case 'send_peer_assessment_request': $targetMethod = 'sendPeerAssessmentRequest'; break;
            case 'show_self_assessment_form': $targetMethod = 'showSelfAssessmentForm'; break;
            case 'submit_self_assessment': $targetMethod = 'submitSelfAssessment'; break;
            case 'show_peer_assessment_form': $targetMethod = 'showPeerAssessmentForm'; break;
            case 'submit_peer_assessment': $targetMethod = 'submitPeerAssessment'; break;
            
              // مسیرهای جدید برای تحقیقات بازاریابی
            case 'showMarketingForm': $targetMethod = 'showMarketingForm'; break;
            case 'deleteMapLead': $targetMethod = 'deleteMapLead'; break;
            case 'deleteNiazroozLead': $targetMethod = 'deleteNiazroozLead'; break;
            case 'deleteNeshanLead': $targetMethod = 'deleteNeshanLead'; break;
            
            // Payroll
            case 'payrolls': $targetMethod = 'payrolls'; break;
            case 'create_payroll': $targetMethod = 'createPayroll'; break;
            case 'store_payroll': $targetMethod = 'storePayroll'; break;
            case 'view_payslip': $targetMethod = 'viewPayslip'; break;
            case 'payrolls_delete': $targetMethod = 'deletePayroll'; break;
            case 'payroll_settings': $targetMethod = 'payrollSettings'; break;
            case 'store_payroll_settings': $targetMethod = 'storePayrollSettings'; break;
            default: $targetMethod = 'dashboard'; break;
        }
        break;

    case 'employee':
        $controller = new EmployeeController();
        switch ($action) {
            case 'addChecklistItem': $targetMethod = 'addChecklistItem'; break;
            case 'toggleChecklistItem': $targetMethod = 'toggleChecklistItem'; break;
            case 'deleteChecklistItem': $targetMethod = 'deleteChecklistItem'; break;
            case 'dashboard': $targetMethod = 'dashboard'; break;
            case 'process_clocking': $targetMethod = 'processClocking'; break;
            case 'leave_requests': $targetMethod = 'leaveRequests'; break;
            case 'submit_leave_request': $targetMethod = 'submitLeaveRequest'; break;
            case 'my_projects': $targetMethod = 'myProjects'; break;
            case 'update_task_status': $targetMethod = 'updateTaskStatus'; break;
            case 'my_payslips': $targetMethod = 'myPayslips'; break;
            case 'view_my_payslip': $targetMethod = 'viewMyPayslip'; break;
            case 'announcements_index': $targetMethod = 'announcementsIndex'; break;
            case 'show_training_needs_form': $targetMethod = 'showTrainingNeedsForm'; break;
            case 'submit_training_need': $targetMethod = 'submitTrainingNeed'; break;
            case 'toggleChecklistItem':
            $targetMethod = 'toggleChecklistItem';
            break;
            case 'show_peer_assessment_form': $targetMethod = 'showPeerAssessmentForm'; break;
            case 'submit_peer_assessment': $targetMethod = 'submitPeerAssessment'; break;
            default: $targetMethod = 'dashboard'; break; 
        }
        break;
        
    case 'client':
        $controller = new ClientController();
        switch ($action) {
            case 'dashboard': $targetMethod = 'dashboard'; break;
            case 'my_contracts': $targetMethod = 'myContracts'; break;
            case 'view_contract': $targetMethod = 'viewContract'; break;
            case 'my_invoices': $targetMethod = 'myInvoices'; break;
            case 'invoice_view': $targetMethod = 'viewInvoice'; break;
            case 'my_projects': $targetMethod = 'myProjects'; break;
            case 'my_tickets': $targetMethod = 'myTickets'; break;
            case 'create_ticket': $targetMethod = 'createTicket'; break;
            case 'store_ticket': $targetMethod = 'storeTicket'; break;
            case 'view_invoice': $targetMethod = 'viewInvoice'; break;
            case 'invoice_pay': $targetMethod = 'payInvoice'; break;
            case 'view_ticket': $targetMethod = 'viewTicket'; break;
            case 'store_ticket_reply': $targetMethod = 'storeTicketReply'; break;
            case 'announcements_index': $targetMethod = 'announcementsIndex'; break;
            default: $targetMethod = 'dashboard'; break;
        }
        break;

    case 'payment':
        $controller = new PaymentController();
        switch($action) {
            case 'request': $targetMethod = 'request'; break;
            case 'callback': $targetMethod = 'callback'; break;
            default: $targetMethod = null; break;
        }
        break;

    case 'ajax':
        $controller = new AjaxController();
        switch($action) {
            case 'get_users': $targetMethod = 'getUsers'; break;
            case 'send_message': $targetMethod = 'sendMessage'; break;
            case 'fetch_messages': $targetMethod = 'fetchMessages'; break;
            default: $targetMethod = null; break; 
        }
        break;
    
    // تعریف کنترلرهای جدید
    case 'products':
        $controller = new ProductController();
        switch($action) {
            case 'index': $targetMethod = 'index'; break;
            case 'create': $targetMethod = 'create'; break;
            case 'store': $targetMethod = 'store'; break;
            case 'edit': $targetMethod = 'edit'; break;
            case 'update': $targetMethod = 'update'; break;
            case 'delete': $targetMethod = 'delete'; break;
            default: $targetMethod = 'index'; break;
        }
        break;
        
    case 'purchases':
    $controller = new PurchasesController();
    switch($action) {
        case 'index': $targetMethod = 'index'; break;
        case 'create': $targetMethod = 'create'; break;
        case 'store': $targetMethod = 'store'; break;
        case 'view': $targetMethod = 'view'; break; 
        case 'updateStatus': $targetMethod = 'updateStatus'; break;
        default: $targetMethod = 'index'; break;
    }
    break;
        
    case 'sales':
    $controller = new SalesController();
    switch($action) {
        case 'index': $targetMethod = 'index'; break;
        case 'create': $targetMethod = 'create'; break;
        case 'store': $targetMethod = 'store'; break;
        case 'view': $targetMethod = 'view'; break;
        case 'updateStatus': $targetMethod = 'updateStatus'; break;
        default: $targetMethod = 'index'; break;
    }
    break;

    case 'categories':
        $controller = new CategoryController();
        switch($action) {
            case 'index': $targetMethod = 'index'; break;
            case 'store': $targetMethod = 'store'; break;
            case 'edit': $targetMethod = 'edit'; break;
            case 'update': $targetMethod = 'update'; break;
            case 'delete': $targetMethod = 'delete'; break;
            default: $targetMethod = 'index'; break;
        }
        break;
        
    default:
        http_response_code(404);
        view('errors/404', ['layout' => 'guest_layout', 'title' => 'صفحه یافت نشد']);
        exit();
}

// فراخوانی متد کنترلر
if ($controller && method_exists($controller, $targetMethod)) {
    call_user_func_array([$controller, $targetMethod], $id !== null ? [$id] : []);
} else {
    // در صورت عدم یافتن متد، به صفحه 404 هدایت کن، نه داشبورد
    http_response_code(404);
    view('errors/404', ['layout' => 'guest_layout', 'title' => 'صفحه یافت نشد']);
    exit();
}