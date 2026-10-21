<?php
// public/index.php - نسخه اولترا پرمیوم، نهایی و اصلاح‌شده

// -------------------------------------------------------------------------
// 0. سیستم عیب‌یابی اضطراری (Emergency Debugger)
// -------------------------------------------------------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        echo "<div style='background:#fdeded; color:#5f2120; padding:20px; border:2px solid red; font-family:sans-serif; direction:ltr; text-align:left;'>";
        echo "<h2>⛔ Fatal Error (500) Detected</h2>";
        echo "<strong>Message:</strong> " . $error['message'] . "<br>";
        echo "<strong>File:</strong> " . $error['file'] . "<br>";
        echo "<strong>Line:</strong> " . $error['line'];
        echo "</div>";
    }
});

// -------------------------------------------------------------------------
// 1. تنظیمات اولیه (API vs Web)
// -------------------------------------------------------------------------
if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false || (isset($_GET['page']) && $_GET['page'] === 'api')) {
    ini_set('display_errors', 0); 
    error_reporting(0);
}

// تنظیم منطقه زمانی
date_default_timezone_set('Asia/Tehran');

// شروع سشن
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------------------------
// 2. بارگذاری فایل‌های پیکربندی و کتابخانه‌ها
// -------------------------------------------------------------------------
require_once __DIR__ . '/app/config/app.php';
require_once __DIR__ . '/app/config/database.php';
if (file_exists(__DIR__ . '/app/config/sms.php')) require_once __DIR__ . '/app/config/sms.php';
if (file_exists(__DIR__ . '/app/config/payment.php')) require_once __DIR__ . '/app/config/payment.php';
if (file_exists(__DIR__ . '/vendor/autoload.php')) require_once __DIR__ . '/vendor/autoload.php';

// بارگذاری هسته سیستم
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Auth.php';

if (file_exists(__DIR__ . '/app/core/Controller.php')) {
    require_once __DIR__ . '/app/core/Controller.php';
} elseif (file_exists(__DIR__ . '/app/libraries/Controller.php')) {
    require_once __DIR__ . '/app/libraries/Controller.php';
} else {
    die("Error: Base Controller class not found in app/core or app/libraries.");
}

require_once __DIR__ . '/app/core/Validator.php';
require_once __DIR__ . '/app/core/Helpers.php';
require_once __DIR__ . '/app/core/FlashMessage.php';
if (file_exists(__DIR__ . '/app/lib/numberToWords.php')) require_once __DIR__ . '/app/lib/numberToWords.php';
if (file_exists(__DIR__ . '/app/lib/JalaliDate.php')) require_once __DIR__ . '/app/lib/JalaliDate.php';
if (file_exists(__DIR__ . '/app/lib/Occasions.php')) require_once __DIR__ . '/app/lib/Occasions.php';

// -------------------------------------------------------------------------
// 3. بارگذاری مدل‌ها (هوشمند)
// -------------------------------------------------------------------------
$models_path = __DIR__ . '/app/models/';
if (is_dir($models_path)) {
    foreach (glob($models_path . "*.php") as $filename) {
        require_once $filename;
    }
}

if (!function_exists('getStatusInPersian')) {
    function getStatusInPersian($status) {
        switch ($status) {
            case 'todo': return 'برای انجام';
            case 'in_progress': return 'در حال انجام';
            case 'review': return 'در حال بررسی';
            case 'done': return 'انجام شده';
            default: return $status;
        }
    }
}

// -------------------------------------------------------------------------
// 4. بارگذاری کنترلرها (هوشمند)
// -------------------------------------------------------------------------
$controllers_path = __DIR__ . '/app/controllers/';
if (is_dir($controllers_path)) {
    foreach (glob($controllers_path . "*.php") as $filename) {
        require_once $filename;
    }
}

// -------------------------------------------------------------------------
// 5. بررسی لاگین و مقداردهی اولیه
// -------------------------------------------------------------------------
$auth = new Auth();
if ($auth->check()) {
    if (class_exists('User')) {
        $userActivityModel = new User();
        if (method_exists($userActivityModel, 'updateLastActivity')) {
            $userActivityModel->updateLastActivity($auth->user()->id);
        }
    }
}

// دریافت پارامترهای مسیریابی
$page   = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';
$id     = $_GET['id'] ?? null;

// تشخیص درخواست API
$request_uri = $_SERVER['REQUEST_URI'];
if (strpos($request_uri, '/api/') !== false) {
    $page = 'api';
    if (strpos($request_uri, 'check_license') !== false) $action = 'check_license';
    elseif (strpos($request_uri, 'update_usage') !== false) $action = 'update_usage';
}

// منطق صفحه اصلی (لندینگ vs داشبورد)
if ($page === 'home' && (!isset($_GET['action']) || $_GET['action'] === 'index')) {
    if ($auth->check()) {
        if ($auth->isAdmin()) redirect(APP_URL . '/index.php?page=admin&action=dashboard');
        elseif ($auth->isEmployee()) redirect(APP_URL . '/index.php?page=employee&action=dashboard');
        else redirect(APP_URL . '/index.php?page=client&action=dashboard');
    } else {
        $action = 'landing';
    }
}

// صفحات عمومی
$public_pages = ['home', 'login', 'api', 'payment', 'ajax', 'register'];

// ریدایرکت امنیتی
if (!in_array($page, $public_pages) && !$auth->check()) {
    redirect(APP_URL . '/index.php?page=login&action=show');
} 
elseif ($page === 'login' && $auth->check()) {
    if ($auth->isAdmin()) redirect(APP_URL . '/index.php?page=admin&action=dashboard');
    elseif ($auth->isEmployee()) redirect(APP_URL . '/index.php?page=employee&action=dashboard');
    else redirect(APP_URL . '/index.php?page=client&action=dashboard');
}

// -------------------------------------------------------------------------
// 6. سوئیچ اصلی مسیردهی
// -------------------------------------------------------------------------
$controller = null;
$targetMethod = null;
$hasParam = false;

switch ($page) {
    
    // --- API ---
    case 'api':
        if (ob_get_length()) ob_clean();
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        
        if (class_exists('ApiController')) {
            $controller = new ApiController();
            switch ($action) {
                case 'check_license': $targetMethod = 'check_license'; break;
                case 'update_usage': $targetMethod = 'update_usage'; break;
                default: echo json_encode(['status' => false, 'message' => 'Invalid API Action']); exit;
            }
        } else {
            echo json_encode(['status' => false, 'message' => 'API Controller Not Found']); exit;
        }
        break;

    // --- عمومی ---
    case 'home':
        $controller = new HomeController();
        $targetMethod = 'index'; 
        break;

    // --- احراز هویت ---
    case 'login':
        $controller = new AuthController();
        $targetMethod = ($action === 'show') ? 'showLogin' : 'login';
        break;
    case 'logout':
        $controller = new AuthController();
        $targetMethod = 'logout';
        break;
    case 'register':
        $controller = new AuthController();
        $targetMethod = 'register';
        break;

    // --- پنل مدیریت (ADMIN) ---
    case 'admin':
        $controller = new AdminController();
        switch ($action) {
            case 'dashboard': 
            case 'index': 
                $targetMethod = 'dashboard'; 
                break;

            case 'plans':       $targetMethod = 'plans'; break;
            case 'create_plan': $targetMethod = 'create_plan'; break;
            case 'store_plan':  $targetMethod = 'store_plan'; break;
            case 'delete_plan': $targetMethod = 'delete_plan'; $hasParam = true; break;
            case 'edit_plan':   $targetMethod = 'edit_plan'; $hasParam = true; break;
            case 'update_plan': $targetMethod = 'update_plan'; $hasParam = true; break;

            case 'licenses':            $targetMethod = 'licenses'; break;
            case 'create_license_form': $targetMethod = 'create_license_form'; break;
            case 'store_license':       $targetMethod = 'store_license'; break;
            case 'delete_license':      $targetMethod = 'delete_license'; $hasParam = true; break;

            case 'users':           $targetMethod = 'users'; break;
            case 'users_create':    $targetMethod = 'createUser'; break;
            case 'users_store':     $targetMethod = 'storeUser'; break;
            case 'users_edit':      $targetMethod = 'editUser'; $hasParam = true; break;
            case 'users_update':    $targetMethod = 'updateUser'; $hasParam = true; break;
            case 'users_delete':    $targetMethod = 'deleteUser'; $hasParam = true; break;
            case 'get_payroll_data': $targetMethod = 'getAutomatedPayrollData'; break;
            case 'exportUsersToExcel': $targetMethod = 'exportUsersToExcel'; break;
            case 'showImportUsersForm': $targetMethod = 'showImportUsersForm'; break;
            case 'importUsers':     $targetMethod = 'importUsers'; break;

            case 'clients':         $targetMethod = 'clients'; break;
            case 'clients_create':  $targetMethod = 'createClient'; break;
            case 'clients_store':   $targetMethod = 'storeClient'; break;
            case 'clients_edit':    $targetMethod = 'editClient'; $hasParam = true; break;
            case 'clients_update':  $targetMethod = 'updateClient'; $hasParam = true; break;
            case 'clients_delete':  $targetMethod = 'deleteClient'; $hasParam = true; break;
            case 'clients_logs':    $targetMethod = 'viewClientLogs'; $hasParam = true; break;
            case 'clients_store_log': $targetMethod = 'storeClientLog'; break;
            case 'updateClientLog': $targetMethod = 'updateClientLog'; break;
            case 'deleteClientLog': $targetMethod = 'deleteClientLog'; break;
            case 'deleteLogAttachment': $targetMethod = 'deleteLogAttachment'; break;
            case 'export_clients':  $targetMethod = 'exportClients'; break;
            case 'show_import_form': $targetMethod = 'showImportForm'; break;
            case 'import_clients':  $targetMethod = 'importClients'; break;

            case 'contracts':           $targetMethod = 'contracts'; break;
            case 'contracts_create':    $targetMethod = 'createContract'; break;
            case 'contracts_store':     $targetMethod = 'storeContract'; break;
            case 'contracts_edit':      $targetMethod = 'editContract'; $hasParam = true; break;
            case 'contracts_update':    $targetMethod = 'updateContract'; $hasParam = true; break;
            case 'contracts_delete':    $targetMethod = 'deleteContract'; $hasParam = true; break;
            case 'view_contract':       $targetMethod = 'viewContract'; $hasParam = true; break;
            case 'get_contract_templates_ajax': $targetMethod = 'getContractTemplatesAjax'; break; // ✅ مسیر اصلاح‌شده برای فراخوانی ایجکس مواد قرارداد
            case 'send_contract_reminder': $targetMethod = 'sendContractReminder'; $hasParam = true; break;
            case 'export_contracts_to_excel': $targetMethod = 'exportContractsToExcel'; break;
            
            case 'invoices':            $targetMethod = 'invoices'; break;
            case 'invoices_create':     $targetMethod = 'createInvoice'; break;
            case 'invoices_store':      $targetMethod = 'storeInvoice'; break;
            case 'editInvoice':         $targetMethod = 'editInvoice'; $hasParam = true; break;
            case 'invoices_update':     $targetMethod = 'updateInvoice'; $hasParam = true; break;
            case 'invoices_delete':     $targetMethod = 'deleteInvoice'; $hasParam = true; break;
            case 'view_invoice':        $targetMethod = 'viewInvoice'; $hasParam = true; break;
            case 'invoices_update_status': $targetMethod = 'updateInvoiceStatus'; $hasParam = true; break;
            case 'send_invoice_reminder': $targetMethod = 'sendInvoiceReminder'; $hasParam = true; break;
            case 'invoice_add_payment': $targetMethod = 'addPaymentToInvoice'; break;
            case 'export_invoices_to_excel': $targetMethod = 'exportInvoicesToExcel'; break;

            case 'projects':            $targetMethod = 'projects'; break;
            case 'projects_create':     $targetMethod = 'createProject'; break;
            case 'projects_store':      $targetMethod = 'storeProject'; break;
            case 'projects_edit':       $targetMethod = 'editProject'; $hasParam = true; break;
            case 'projects_update':     $targetMethod = 'updateProject'; $hasParam = true; break;
            case 'projects_delete':     $targetMethod = 'deleteProject'; $hasParam = true; break;
            case 'view_project':        $targetMethod = 'viewProject'; $hasParam = true; break;
            case 'projects_add_member': $targetMethod = 'addProjectMember'; break;
            case 'projects_create_task': $targetMethod = 'createTask'; $hasParam = true; break;
            
            case 'addChecklistItem':    $targetMethod = 'addChecklistItem'; break;
            case 'toggleChecklistItem': $targetMethod = 'toggleChecklistItem'; break;
            case 'deleteChecklistItem': $targetMethod = 'deleteChecklistItem'; break;

            case 'accounting_accounts': $targetMethod = 'accountingAccounts'; break;
            case 'create_voucher_form': $targetMethod = 'createVoucherForm'; break;
            case 'create_account_form': $targetMethod = 'createAccountForm'; break;
            case 'store_account':       $targetMethod = 'storeAccount'; break;
            case 'store_voucher':       $targetMethod = 'storeVoucher'; break;
            case 'accounting_expenses': $targetMethod = 'accountingExpenses'; break;
            case 'store_expense':       $targetMethod = 'storeExpense'; break;
            case 'account_ledger':      $targetMethod = 'accountLedger'; break;
            case 'general_journal':     $targetMethod = 'generalJournal'; break;
            case 'vat_report':          $targetMethod = 'vatReport'; break; 
            case 'close_fiscal_year':   $targetMethod = 'closeFiscalYear'; break;
            case 'delete_voucher':      $targetMethod = 'deleteVoucher'; $hasParam = true; break;
            case 'subledger_report':    $targetMethod = 'subledgerReport'; break;
            case 'reverse_voucher':     $targetMethod = 'reverseVoucher'; $hasParam = true; break;
            case 'budgeting':           $targetMethod = 'budgeting'; break;
            case 'delete_budget':       $targetMethod = 'deleteBudget'; $hasParam = true; break;
            case 'budget_report':       $targetMethod = 'budgetReport'; break;
            case 'bank_reconciliation': $targetMethod = 'bankReconciliation'; break;
            case 'process_reconciliation': $targetMethod = 'processReconciliation'; break; 
            case 'fixed_assets':        $targetMethod = 'manageFixedAssets'; break;
            case 'store_fixed_asset':   $targetMethod = 'storeFixedAsset'; break;
            case 'delete_fixed_asset':  $targetMethod = 'deleteFixedAsset'; $hasParam = true; break;
            case 'run_depreciation_form': $targetMethod = 'showDepreciationForm'; break;
            case 'run_depreciation':    $targetMethod = 'runDepreciation'; break;
            case 'cash_flow_report':    $targetMethod = 'cashFlowReport'; break;
            case 'expense_analysis_report': $targetMethod = 'expenseAnalysisReport'; break;
            case 'financial_reports':   $targetMethod = 'financialReports'; break;
            case 'profitAndLossReport': $targetMethod = 'profitAndLossReport'; break;

            case 'announcements_index': $targetMethod = 'announcementsIndex'; break; 
            case 'announcements_create': $targetMethod = 'announcementsCreate'; break;
            case 'announcements_store': $targetMethod = 'announcementsStore'; break;
            case 'announcements_edit':  $targetMethod = 'announcementsEdit'; $hasParam = true; break;
            case 'announcements_update': $targetMethod = 'announcementsUpdate'; $hasParam = true; break;
            case 'announcements_delete': $targetMethod = 'announcementsDelete'; $hasParam = true; break;
            case 'training_needs':      $targetMethod = 'trainingNeeds'; break;
            case 'view_training_need':  $targetMethod = 'viewTrainingNeed'; $hasParam = true; break;
            case 'process_training_need': $targetMethod = 'processTrainingNeed'; $hasParam = true; break;
            case 'manage_training_courses': $targetMethod = 'manageTrainingCourses'; break;
            case 'store_training_course': $targetMethod = 'storeTrainingCourse'; break;
            case 'training_needs_analysis': $targetMethod = 'trainingNeedsAnalysis'; break;
            case 'manage_skill_assessments': $targetMethod = 'manageSkillAssessments'; break;
            case 'send_peer_assessment_request': $targetMethod = 'sendPeerAssessmentRequest'; break;
            case 'show_self_assessment_form': $targetMethod = 'showSelfAssessmentForm'; break;
            case 'submit_self_assessment': $targetMethod = 'submitSelfAssessment'; break;
            case 'show_peer_assessment_form': $targetMethod = 'showPeerAssessmentForm'; break;
            case 'submit_peer_assessment': $targetMethod = 'submitPeerAssessment'; break;
            case 'employee_list':       $targetMethod = 'employeeList'; break;
            case 'employee_performance_report': $targetMethod = 'employeePerformanceReport'; $hasParam = true; break;
            case 'payrolls':            $targetMethod = 'payrolls'; break;
            case 'create_payroll':      $targetMethod = 'createPayroll'; break;
            case 'store_payroll':       $targetMethod = 'storePayroll'; break;
            case 'view_payslip':        $targetMethod = 'viewPayslip'; $hasParam = true; break;
            case 'payrolls_delete':     $targetMethod = 'deletePayroll'; $hasParam = true; break;
            case 'payroll_settings':    $targetMethod = 'payrollSettings'; break;
            case 'store_payroll_settings': $targetMethod = 'storePayrollSettings'; break;
            case 'attendance_report':   $targetMethod = 'attendanceReport'; break;
            case 'manage_leave_requests': $targetMethod = 'manageLeaveRequests'; break;
            case 'process_leave_request': $targetMethod = 'processLeaveRequest'; $hasParam = true; break;

            case 'showMarketingForm':   $targetMethod = 'showMarketingForm'; break;
            case 'processMarketingSearch': $targetMethod = 'processMarketingSearch'; break;
            case 'deleteMapLead':       $targetMethod = 'deleteMapLead'; $hasParam = true; break;
            case 'deleteNiazroozLead':  $targetMethod = 'deleteNiazroozLead'; $hasParam = true; break;
            case 'deleteNeshanLead':    $targetMethod = 'deleteNeshanLead'; $hasParam = true; break;
            case 'sms_panel':           $targetMethod = 'smsPanel'; break;
            case 'send_custom_bulk_sms': $targetMethod = 'sendCustomBulkSms'; break;
            case 'send_occasional_sms': $targetMethod = 'sendOccasionalSms'; break;
            case 'send_selected_occasion_sms': $targetMethod = 'sendSelectedOccasionSms'; break;
            case 'showApiSettings':     $targetMethod = 'showApiSettings'; break;
            case 'storeApiKey':         $targetMethod = 'storeApiKey'; break;
            case 'deleteApiKey':        $targetMethod = 'deleteApiKey'; break;

            case 'reports':             $targetMethod = 'reports'; break;
            case 'detailed_reports':    $targetMethod = 'detailedReports'; break;
            case 'tickets':             $targetMethod = 'tickets'; break;
            case 'view_ticket':         $targetMethod = 'viewTicket'; $hasParam = true; break;
            case 'store_ticket_reply':  $targetMethod = 'storeTicketReply'; $hasParam = true; break;

            default: $targetMethod = 'dashboard'; break;
        }
        break;
        
    // --- پنل کارمند (EMPLOYEE) ---
    case 'employee':
        $controller = new EmployeeController();
        switch ($action) {
            case 'dashboard':            $targetMethod = 'dashboard'; break;
            case 'process_clocking':     $targetMethod = 'processClocking'; break;
            case 'my_attendance':        $targetMethod = 'dashboard'; break;
            
            // مرخصی‌ها
            case 'leave_requests':       $targetMethod = 'leaveRequests'; break;
            case 'submit_leave_request': $targetMethod = 'submitLeaveRequest'; break;
            
            // پروژه‌ها و وظایف
            case 'my_projects':          $targetMethod = 'myProjects'; break;
            case 'project_detail':       $targetMethod = 'projectDetail'; $hasParam = true; break;
            case 'update_task_status':   $targetMethod = 'updateTaskStatus'; break;
            case 'toggleChecklistItem':  $targetMethod = 'toggleChecklistItem'; break;
            case 'addChecklistItem':     $targetMethod = 'addChecklistItem'; break;
            case 'deleteChecklistItem':  $targetMethod = 'deleteChecklistItem'; break;
            
            // فیش حقوقی
            case 'my_payslips':          $targetMethod = 'myPayslips'; break;
            case 'view_my_payslip':      $targetMethod = 'viewMyPayslip'; $hasParam = true; break;
            
            // نیازسنجی آموزشی و ارزیابی ۳۶۰ درجه
            case 'show_training_needs_form': $targetMethod = 'showTrainingNeedsForm'; break;
            case 'submit_training_need':     $targetMethod = 'submitTrainingNeed'; break;
            case 'show_self_assessment_form': $targetMethod = 'showSelfAssessmentForm'; break;
            case 'submit_self_assessment':   $targetMethod = 'submitSelfAssessment'; break;
            case 'show_peer_assessment_form':
            case 'peer_assessment':
                $targetMethod = 'showPeerAssessmentForm'; 
                break;
            case 'submit_peer_assessment':   $targetMethod = 'submitPeerAssessment'; break;
            
            // اطلاعیه‌ها
            case 'announcements_index':  $targetMethod = 'announcementsIndex'; break;
            
            default:                     $targetMethod = 'dashboard'; break;
        }
        break;
        
    // --- پنل مشتری (CLIENT) ---
    case 'client':
        $controller = new ClientController();
        switch ($action) {
            case 'dashboard':            $targetMethod = 'dashboard'; break;
            case 'ai_settings':          $targetMethod = 'ai_settings'; break;
            case 'storeAiProfile':       $targetMethod = 'storeAiProfile'; break;
            case 'my_contracts':         $targetMethod = 'myContracts'; break;
            case 'my_invoices':          $targetMethod = 'myInvoices'; break;
            case 'my_projects':          $targetMethod = 'myProjects'; break;
            case 'my_licenses':          $targetMethod = 'my_licenses'; break;
            case 'my_tickets':           $targetMethod = 'myTickets'; break;
            case 'create_ticket':        $targetMethod = 'createTicket'; break;
            case 'storeTicket':          $targetMethod = 'storeTicket'; break;
            case 'view_ticket':          $targetMethod = 'viewTicket'; $hasParam = true; break;
            case 'invoice_view':         $targetMethod = 'viewInvoice'; $hasParam = true; break;
            case 'invoice_pay':          $targetMethod = 'payInvoice'; $hasParam = true; break;
            case 'announcements_index':  
            case 'announcementsIndex':   $targetMethod = 'announcements_index'; break;
            default:                     $targetMethod = 'dashboard'; break;
        }
        break;
    
    // --- پرداخت ---
    case 'payment':
        $controller = new PaymentController();
        switch($action) {
            case 'request': $targetMethod = 'request'; break;
            case 'callback': $targetMethod = 'callback'; break;
            case 'checkout': $targetMethod = 'checkout'; $hasParam = true; break;
            case 'verify': $targetMethod = 'verify'; break;
        }
        break;

    // --- ایجکس ---
    case 'ajax':
        $controller = new AjaxController();
        switch($action) {
            case 'get_users': $targetMethod = 'getUsers'; break;
            case 'send_message': $targetMethod = 'sendMessage'; break;
            case 'fetch_messages': $targetMethod = 'fetchMessages'; break;
            case 'number_to_words': $targetMethod = 'numberToWords'; break;
        }
        break;

    // --- محصولات ---
    case 'products':
        $controller = new ProductController();
        switch($action) {
            case 'index': $targetMethod = 'index'; break;
            case 'create': $targetMethod = 'create'; break;
            case 'store': $targetMethod = 'store'; break;
            case 'edit': $targetMethod = 'edit'; $hasParam = true; break;
            case 'update': $targetMethod = 'update'; $hasParam = true; break;
            case 'delete': $targetMethod = 'delete'; $hasParam = true; break;
            case 'product_flow_report': $targetMethod = 'productFlowReport'; $hasParam = true; break;
        }
        break;

    // --- خریدها ---
    case 'purchases':
        $controller = new PurchasesController();
        switch($action) {
            case 'index': $targetMethod = 'index'; break;
            case 'create': $targetMethod = 'create'; break;
            case 'store': $targetMethod = 'store'; break;
            case 'view': $targetMethod = 'view'; $hasParam = true; break;
            case 'updateStatus': $targetMethod = 'updateStatus'; $hasParam = true; break;
            case 'edit': $targetMethod = 'edit'; $hasParam = true; break;
            case 'update': $targetMethod = 'update'; $hasParam = true; break;
            case 'delete': $targetMethod = 'delete'; $hasParam = true; break;
        }
        break;

    // --- فروش‌ها ---
    case 'sales':
        $controller = new SalesController();
        switch($action) {
            case 'index': $targetMethod = 'index'; break;
            case 'create': $targetMethod = 'create'; break;
            case 'store': $targetMethod = 'store'; break;
            case 'view': $targetMethod = 'view'; $hasParam = true; break;
            case 'updateStatus': $targetMethod = 'updateStatus'; $hasParam = true; break;
            case 'delete': $targetMethod = 'delete'; $hasParam = true; break; // ✅ افزوده شد
        }
        break;

    // --- خدمات ---
    case 'services':
        $controller = new ServiceController();
        switch($action) {
            case 'index': $targetMethod = 'index'; break;
            case 'create': $targetMethod = 'create'; break;
            case 'store': $targetMethod = 'store'; break;
            case 'edit': $targetMethod = 'edit'; $hasParam = true; break;
            case 'update': $targetMethod = 'update'; $hasParam = true; break;
            case 'delete': $targetMethod = 'delete'; $hasParam = true; break;
        }
        break;

    // --- دسته‌بندی‌ها ---
    case 'categories':
        $controller = new CategoryController();
        switch($action) {
            case 'index': $targetMethod = 'index'; break;
            case 'create': $targetMethod = 'create'; break;
            case 'store': $targetMethod = 'store'; break;
            case 'edit': $targetMethod = 'edit'; $hasParam = true; break;
            case 'update': $targetMethod = 'update'; $hasParam = true; break;
            case 'delete': $targetMethod = 'delete'; $hasParam = true; break;
        }
        break;

    // --- انبارداری ---
    case 'inventory':
        if (file_exists(__DIR__ . '/app/controllers/InventoryController.php')) {
            require_once __DIR__ . '/app/controllers/InventoryController.php';
            $controller = new InventoryController();
            $idParam = $_GET['id'] ?? null;
            $id = (function_exists('sanitize') && $idParam) ? sanitize($idParam) : $idParam;

            switch ($action) {
                case 'warehouses': $targetMethod = 'warehouses'; break;
                case 'create_warehouse': $targetMethod = 'createWarehouse'; break;
                case 'store_warehouse': $targetMethod = 'storeWarehouse'; break;
                case 'edit_warehouse': $targetMethod = 'editWarehouse'; $hasParam = true; break;
                case 'update_warehouse': $targetMethod = 'updateWarehouse'; $hasParam = true; break;
                case 'delete_warehouse': $targetMethod = 'deleteWarehouse'; $hasParam = true; break;
                case 'units': $targetMethod = 'units'; break;
                case 'create_unit': $targetMethod = 'createUnit'; break;
                case 'store_unit': $targetMethod = 'storeUnit'; break;
                case 'edit_unit': $targetMethod = 'editUnit'; $hasParam = true; break;
                case 'update_unit': $targetMethod = 'updateUnit'; $hasParam = true; break;
                case 'delete_unit': $targetMethod = 'deleteUnit'; $hasParam = true; break;
                case 'warehouse_report': $targetMethod = 'warehouseReport'; break;
                case 'product_flow_report': $targetMethod = 'productFlowReport'; $hasParam = true; break;
                case 'print_purchase_receipt': $targetMethod = 'printPurchaseReceipt'; $hasParam = true; break;
                case 'print_sale_receipt': $targetMethod = 'printSaleReceipt'; $hasParam = true; break;
            }
        }
        break;

    default:
        http_response_code(404);
        if (file_exists(__DIR__ . '/app/views/errors/404.php')) {
            require_once __DIR__ . '/app/views/errors/404.php';
        } else {
            echo "404 - Page Not Found";
        }
        exit();
}

// -------------------------------------------------------------------------
// 7. اجرای نهایی متد
// -------------------------------------------------------------------------
if ($controller && method_exists($controller, $targetMethod)) {
    try {
        if ($hasParam) {
            if ($id !== null) {
                call_user_func([$controller, $targetMethod], $id);
            } else {
                if ($page === 'api') {
                    echo json_encode(['status' => false, 'message' => 'Missing ID Parameter']);
                } else {
                    http_response_code(404);
                    if (file_exists(__DIR__.'/app/views/errors/404.php')) {
                        require_once __DIR__.'/app/views/errors/404.php';
                    } else {
                        echo "404 - Missing ID Parameter";
                    }
                }
                exit();
            }
        } else {
            call_user_func([$controller, $targetMethod]);
        }
        exit(); // توقف کامل اجرا پس از فراخوانی موفق جهت جلوگیری از چسبیدن کدهای ۴۰۴
    } catch (Error $e) {
        echo "<div style='background:#fdeded;color:red;padding:20px;border:1px solid red;'>";
        echo "<h3>Runtime Error</h3>";
        echo "Message: " . $e->getMessage() . "<br>";
        echo "File: " . $e->getFile() . " on line " . $e->getLine();
        echo "</div>";
        exit();
    }
} else {
    if ($page !== 'inventory' && $page !== 'api') {
        http_response_code(404);
        if (file_exists(__DIR__.'/app/views/errors/404.php')) {
            require_once __DIR__.'/app/views/errors/404.php';
        } else {
            echo "404 - Controller Method Not Found";
        }
    }
    exit();
}