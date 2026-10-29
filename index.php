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
require_once __DIR__ . '/app/lib/numberToWords.php';


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
require_once __DIR__ . '/app/models/Account.php';
require_once __DIR__ . '/app/models/JournalVoucher.php';
require_once __DIR__ . '/app/models/JournalEntry.php';
require_once __DIR__ . '/app/models/Expense.php';
require_once __DIR__ . '/app/models/FinancialReport.php';
require_once __DIR__ . '/app/models/Budget.php';
require_once __DIR__ . '/app/models/FixedAsset.php';
require_once __DIR__ . '/app/models/ClientLog.php';
require_once __DIR__ . '/app/models/ClientContact.php';
require_once __DIR__ . '/app/models/Announcement.php';
require_once __DIR__ . '/app/models/TrainingNeed.php';
require_once __DIR__ . '/app/models/TrainingCourse.php';
require_once __DIR__ . '/app/models/SkillAssessment.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/models/MarketingLead.php';
require_once __DIR__ . '/app/models/Product.php'; 
require_once __DIR__ . '/app/models/Purchase.php'; 
require_once __DIR__ . '/app/models/Sale.php';     
require_once __DIR__ . '/app/models/Category.php';
require_once __DIR__ . '/app/models/Reconciliation.php';
require_once __DIR__ . '/app/models/FixedAsset.php';
require_once __DIR__ . '/app/models/Unit.php';
require_once __DIR__ . '/app/models/Warehouse.php';
require_once __DIR__ . '/app/models/InventoryLog.php';
require_once __DIR__ . '/app/models/Service.php'; // ✅ اضافه شدن مدل Service
require_once __DIR__ . '/app/models/InvoiceItem.php'; // ✅ اضافه شدن مدل InvoiceItem
require_once __DIR__ . '/app/models/Payroll.php'; // ✅ اضافه شدن مدل Payroll
require_once __DIR__ . '/app/models/PayrollSetting.php';

require_once __DIR__ . '/app/models/TaxpayerInvoice.php';
require_once __DIR__ . '/app/models/Cheque.php';
require_once __DIR__ . '/app/models/Bom.php';
require_once __DIR__ . '/app/models/ProductionOrder.php';
require_once __DIR__ . '/app/models/PurchaseRequisition.php';
require_once __DIR__ . '/app/models/ContractGuarantee.php';
require_once __DIR__ . '/app/models/WorkflowRule.php';
require_once __DIR__ . '/app/lib/AiAnalyticsService.php';
 // ✅ اضافه شدن مدل PayrollSetting


function getStatusInPersian($status) {
    switch ($status) {
        case 'todo':
            return 'برای انجام';
        case 'in_progress':
            return 'در حال انجام';
        case 'review':
            return 'در حال بررسی';
        case 'done':
            return 'انجام شده';
        default:
            return $status;
    }
}

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
require_once __DIR__ . '/app/controllers/ServiceController.php';
require_once __DIR__ . '/app/controllers/InventoryController.php';


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


// تشخیص محیط دمو
$isDemoHost = (strpos($_SERVER['HTTP_HOST'] ?? '', 'demo') !== false) || isset($_GET['demo']) || isset($_GET['demo_login']);

// در محیط دمو صفحه اول لاگین حذف شده و ورود خودکار انجام می‌پذیرد
if ($isDemoHost && !$auth->check()) {
    $auth->autoLoginDemo();
}

// منطق ریدایرکت‌های اولیه
if ($page === 'home') {
    if (!$auth->check()) {
        if ($isDemoHost) {
            $auth->autoLoginDemo();
            redirect(APP_URL . '/index.php?page=admin&action=dashboard');
        } else {
            redirect(APP_URL . '/index.php?page=login&action=show');
        }
    } else {
        if ($auth->isAdmin()) {
            redirect(APP_URL . '/index.php?page=admin&action=dashboard');
        } elseif ($auth->isEmployee()) {
            redirect(APP_URL . '/index.php?page=employee&action=dashboard');
        } else {
            redirect(APP_URL . '/index.php?page=client&action=dashboard');
        }
    }
} elseif ($page === 'login' && $action === 'show' && $isDemoHost) {
    // حذف کامل صفحه لاگین در ساب‌دومین دمو و هدایت آنی به میز کار
    if (!$auth->check()) {
        $auth->autoLoginDemo();
    }
    redirect(APP_URL . '/index.php?page=admin&action=dashboard');
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
$hasParam = false;


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

            // --- ماژول‌های سازمانی جدید ---
            case 'moadian_invoices': $targetMethod = 'moadianInvoices'; break;
            case 'send_to_moadian': $targetMethod = 'sendToMoadian'; break;
            
            case 'treasury_cheques': $targetMethod = 'treasuryCheques'; break;
            case 'create_cheque': $targetMethod = 'createCheque'; break;
            case 'store_cheque': $targetMethod = 'storeCheque'; break;
            case 'change_cheque_status': $targetMethod = 'changeChequeStatus'; break;
            case 'treasury_rasgiri': $targetMethod = 'treasuryRasgiri'; break;
            
            case 'boms': $targetMethod = 'boms'; break;
            case 'create_bom': $targetMethod = 'createBom'; break;
            case 'store_bom': $targetMethod = 'storeBom'; break;
            case 'production_orders': $targetMethod = 'productionOrders'; break;
            case 'create_production_order': $targetMethod = 'createProductionOrder'; break;
            case 'store_production_order': $targetMethod = 'storeProductionOrder'; break;
            case 'complete_production_order': $targetMethod = 'completeProductionOrder'; break;
            
            case 'procurement_requisitions': $targetMethod = 'procurementRequisitions'; break;
            case 'create_requisition': $targetMethod = 'createRequisition'; break;
            case 'store_requisition': $targetMethod = 'storeRequisition'; break;
            case 'approve_requisition': $targetMethod = 'approveRequisition'; break;
            
            case 'contract_guarantees': $targetMethod = 'contractGuarantees'; break;
            case 'store_guarantee': $targetMethod = 'storeGuarantee'; break;
            case 'update_guarantee_status': $targetMethod = 'updateGuaranteeStatus'; break;
            
            case 'ai_analytics': $targetMethod = 'aiAnalytics'; break;
            
            case 'workflow_rules': $targetMethod = 'workflowRules'; break;
            case 'store_workflow_rule': $targetMethod = 'storeWorkflowRule'; break;
            case 'toggle_workflow_rule': $targetMethod = 'toggleWorkflowRule'; break;

            case 'profitAndLossReport': $targetMethod = 'profitAndLossReport'; break;

            // Users
            case 'users': $targetMethod = 'users'; break;
            case 'users_create': $targetMethod = 'createUser'; break;
            case 'users_store': $targetMethod = 'storeUser'; break;
            case 'users_edit': 
                $targetMethod = 'editUser';
                $hasParam = true;
                break;
            case 'users_update': 
                $targetMethod = 'updateUser';
                $hasParam = true;
                break;
            case 'users_delete': 
                $targetMethod = 'deleteUser'; 
                $hasParam = true;
                break;
            case 'get_payroll_data': $targetMethod = 'getAutomatedPayrollData'; break;
            case 'exportUsersToExcel': $targetMethod = 'exportUsersToExcel'; break;
            case 'showImportUsersForm': $targetMethod = 'showImportUsersForm'; break;
            case 'importUsers': $targetMethod = 'importUsers'; break;

            // Clients
            case 'clients': $targetMethod = 'clients'; break;
            case 'clients_create': $targetMethod = 'createClient'; break;
            case 'clients_store': $targetMethod = 'storeClient'; break;
            case 'clients_edit': 
                $targetMethod = 'editClient';
                $hasParam = true;
                break;
            case 'clients_update': 
                $targetMethod = 'updateClient'; 
                $hasParam = true;
                break;
            case 'clients_delete': 
                $targetMethod = 'deleteClient'; 
                $hasParam = true;
                break;
            case 'clients_logs': 
                $targetMethod = 'viewClientLogs';
                $hasParam = true;
                break;
            case 'clients_store_log': $targetMethod = 'storeClientLog'; break;
            case 'updateClientLog': $targetMethod = 'updateClientLog'; break;
            case 'deleteClientLog': $targetMethod = 'deleteClientLog'; break;
            case 'deleteLogAttachment': $targetMethod = 'deleteLogAttachment'; break;
            case 'import_clients': $targetMethod = 'importClients'; break;
            
            // Contracts
            case 'contracts': $targetMethod = 'contracts'; break;
            case 'contracts_create': $targetMethod = 'createContract'; break;
            case 'contracts_store': $targetMethod = 'storeContract'; break;
            case 'contracts_edit': 
                $targetMethod = 'editContract';
                $hasParam = true;
                break;
            case 'contracts_update': 
                $targetMethod = 'updateContract';
                $hasParam = true;
                break;
            case 'contracts_delete': 
                $targetMethod = 'deleteContract'; 
                $hasParam = true;
                break;
            case 'view_contract': 
                $targetMethod = 'viewContract';
                $hasParam = true;
                break;
            case 'send_contract_reminder': 
                $targetMethod = 'sendContractReminder'; 
                $hasParam = true;
                break;
            case 'export_contracts_to_excel': $targetMethod = 'exportContractsToExcel'; break;
            
            // Invoices
            case 'invoices': $targetMethod = 'invoices'; break;
            case 'invoices_create': $targetMethod = 'createInvoice'; break;
            case 'invoices_store': $targetMethod = 'storeInvoice'; break;
            
            case 'editInvoice':
                $targetMethod = 'editInvoice';
                $hasParam = true;
                break;
            case 'invoices_update': 
                $targetMethod = 'updateInvoice'; 
                $hasParam = true;
                break;
            case 'invoices_delete': 
                $targetMethod = 'deleteInvoice'; 
                $hasParam = true;
                break;
            case 'view_invoice': 
                $targetMethod = 'viewInvoice'; 
                $hasParam = true;
                break;
                case 'invoices_update_status':
                $targetMethod = 'updateInvoiceStatus';
                $hasParam = true;
                break;
            case 'send_invoice_reminder': 
                $targetMethod = 'sendInvoiceReminder'; 
                $hasParam = true;
                break;
            case 'invoice_add_payment': $targetMethod = 'addPaymentToInvoice'; break;
            case 'export_invoices_to_excel': $targetMethod = 'exportInvoicesToExcel'; break;
            // Projects
            case 'projects': $targetMethod = 'projects'; break;
            case 'projects_create': $targetMethod = 'createProject'; break;
            case 'projects_store': $targetMethod = 'storeProject'; break;
            case 'projects_edit': 
                $targetMethod = 'editProject';
                $hasParam = true;
                break;
            case 'projects_update': 
                $targetMethod = 'updateProject'; 
                $hasParam = true;
                break;
            case 'projects_delete': 
                $targetMethod = 'deleteProject'; 
                $hasParam = true;
                break;
            case 'view_project': 
                $targetMethod = 'viewProject';
                $hasParam = true;
                break;
            case 'projects_add_member': $targetMethod = 'addProjectMember'; break;
            case 'projects_create_task': 
                $targetMethod = 'createTask';
                $hasParam = true;
                break;
            // Tickets
            case 'tickets': $targetMethod = 'tickets'; break;
            case 'view_ticket': 
                $targetMethod = 'viewTicket';
                $hasParam = true;
                break;
            case 'store_ticket_reply': 
                $targetMethod = 'storeTicketReply';
                $hasParam = true;
                break;
            case 'projects_add_member': $targetMethod = 'projects_add_member'; break;
            case 'addChecklistItem': $targetMethod = 'addChecklistItem'; break;
            
            // Reports
            case 'reports': $targetMethod = 'reports'; break;
            case 'detailed_reports': $targetMethod = 'detailedReports'; break;
            // HR
            case 'attendance_report': $targetMethod = 'attendanceReport'; break;
            case 'manage_leave_requests': $targetMethod = 'manageLeaveRequests'; break;
            case 'process_leave_request': 
                $targetMethod = 'processLeaveRequest';
                $hasParam = true;
                break;
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
            case 'delete_voucher': 
                $targetMethod = 'deleteVoucher';
                $hasParam = true;
                break;
            case 'subledger_report': $targetMethod = 'subledgerReport'; break;
            case 'reverse_voucher': 
                $targetMethod = 'reverseVoucher';
                $hasParam = true;
                break;
            case 'budgeting': $targetMethod = 'budgeting'; break;
            case 'delete_budget': 
                $targetMethod = 'deleteBudget';
                $hasParam = true;
                break;
            case 'budget_report': $targetMethod = 'budgetReport'; break;
            case 'bank_reconciliation': $targetMethod = 'bankReconciliation'; break;
            case 'process_reconciliation': $targetMethod = 'processReconciliation'; break; 
            case 'fixed_assets': $targetMethod = 'manageFixedAssets'; break;
            case 'store_fixed_asset': $targetMethod = 'storeFixedAsset'; break;
            case 'delete_fixed_asset': 
                $targetMethod = 'deleteFixedAsset';
                $hasParam = true;
                break;
            case 'run_depreciation_form': $targetMethod = 'showDepreciationForm'; break;
            case 'run_depreciation': $targetMethod = 'runDepreciation'; break;
            case 'cash_flow_report': $targetMethod = 'cashFlowReport'; break;
            case 'expense_analysis_report': $targetMethod = 'expenseAnalysisReport'; break;

            // اطلاعیه‌ها
            case 'announcements_index': $targetMethod = 'announcementsIndex'; break; 
            case 'announcements_create': $targetMethod = 'announcementsCreate'; break;
            case 'announcements_store': $targetMethod = 'announcementsStore'; break;
            case 'announcements_edit': 
                $targetMethod = 'announcementsEdit';
                $hasParam = true;
                break;
            case 'announcements_update': 
                $targetMethod = 'announcementsUpdate';
                $hasParam = true;
                break;
            case 'announcements_delete': 
                $targetMethod = 'announcementsDelete';
                $hasParam = true;
                break;

            
            // مدیریت نیازسنجی آموزشی
            case 'training_needs': $targetMethod = 'trainingNeeds'; break;
            case 'view_training_need': 
                $targetMethod = 'viewTrainingNeed';
                $hasParam = true;
                break;
            case 'process_training_need': 
                $targetMethod = 'processTrainingNeed';
                $hasParam = true;
                break;
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
            case 'deleteMapLead': 
                $targetMethod = 'deleteMapLead';
                $hasParam = true;
                break;
            case 'deleteNiazroozLead': 
                $targetMethod = 'deleteNiazroozLead';
                $hasParam = true;
                break;
            case 'deleteNeshanLead': 
                $targetMethod = 'deleteNeshanLead';
                $hasParam = true;
                break;
            
            // Payroll
            case 'employee_list': $targetMethod = 'employeeList'; break;
            case 'employee_performance_report': 
                $targetMethod = 'employeePerformanceReport'; 
                $hasParam = true;
                break;
            case 'payrolls': $targetMethod = 'payrolls'; break;
            case 'create_payroll': $targetMethod = 'createPayroll'; break;
            case 'store_payroll': $targetMethod = 'storePayroll'; break;
            case 'view_payslip': 
                $targetMethod = 'viewPayslip';
                $hasParam = true;
                break;
            case 'payrolls_delete': 
                $targetMethod = 'deletePayroll';
                $hasParam = true;
                break;
            case 'payroll_settings': $targetMethod = 'payrollSettings'; break;
            case 'store_payroll_settings': $targetMethod = 'storePayrollSettings'; break;
        }
        break;

    case 'employee':
        $controller = new EmployeeController();
        switch ($action) {
            case 'addChecklistItem': $targetMethod = 'addChecklistItem'; break;
            case 'toggleChecklistItem': $targetMethod = 'toggleChecklistItem'; break;
            case 'deleteChecklistItem': $targetMethod = 'deleteChecklistItem'; break;
            case 'dashboard': $targetMethod = 'dashboard'; break;

            // --- ماژول‌های سازمانی جدید ---
            case 'moadian_invoices': $targetMethod = 'moadianInvoices'; break;
            case 'send_to_moadian': $targetMethod = 'sendToMoadian'; break;
            
            case 'treasury_cheques': $targetMethod = 'treasuryCheques'; break;
            case 'create_cheque': $targetMethod = 'createCheque'; break;
            case 'store_cheque': $targetMethod = 'storeCheque'; break;
            case 'change_cheque_status': $targetMethod = 'changeChequeStatus'; break;
            case 'treasury_rasgiri': $targetMethod = 'treasuryRasgiri'; break;
            
            case 'boms': $targetMethod = 'boms'; break;
            case 'create_bom': $targetMethod = 'createBom'; break;
            case 'store_bom': $targetMethod = 'storeBom'; break;
            case 'production_orders': $targetMethod = 'productionOrders'; break;
            case 'create_production_order': $targetMethod = 'createProductionOrder'; break;
            case 'store_production_order': $targetMethod = 'storeProductionOrder'; break;
            case 'complete_production_order': $targetMethod = 'completeProductionOrder'; break;
            
            case 'procurement_requisitions': $targetMethod = 'procurementRequisitions'; break;
            case 'create_requisition': $targetMethod = 'createRequisition'; break;
            case 'store_requisition': $targetMethod = 'storeRequisition'; break;
            case 'approve_requisition': $targetMethod = 'approveRequisition'; break;
            
            case 'contract_guarantees': $targetMethod = 'contractGuarantees'; break;
            case 'store_guarantee': $targetMethod = 'storeGuarantee'; break;
            case 'update_guarantee_status': $targetMethod = 'updateGuaranteeStatus'; break;
            
            case 'ai_analytics': $targetMethod = 'aiAnalytics'; break;
            
            case 'workflow_rules': $targetMethod = 'workflowRules'; break;
            case 'store_workflow_rule': $targetMethod = 'storeWorkflowRule'; break;
            case 'toggle_workflow_rule': $targetMethod = 'toggleWorkflowRule'; break;

            case 'process_clocking': $targetMethod = 'processClocking'; break;
            case 'leave_requests': $targetMethod = 'leaveRequests'; break;
            case 'submit_leave_request': $targetMethod = 'submitLeaveRequest'; break;
            case 'my_projects': $targetMethod = 'myProjects'; break;
            case 'update_task_status': $targetMethod = 'updateTaskStatus'; break;
            case 'my_payslips': $targetMethod = 'myPayslips'; break;
            case 'view_my_payslip': 
                $targetMethod = 'viewMyPayslip';
                $hasParam = true;
                break;
            case 'announcements_index': $targetMethod = 'announcementsIndex'; break;
            case 'show_training_needs_form': $targetMethod = 'showTrainingNeedsForm'; break;
            case 'submit_training_need': $targetMethod = 'submitTrainingNeed'; break;
            case 'show_peer_assessment_form': $targetMethod = 'showPeerAssessmentForm'; break;
            case 'submit_peer_assessment': $targetMethod = 'submitPeerAssessment'; break;
        }
        break;
        
    case 'client':
        $controller = new ClientController();
        switch ($action) {
            case 'dashboard': $targetMethod = 'dashboard'; break;

            // --- ماژول‌های سازمانی جدید ---
            case 'moadian_invoices': $targetMethod = 'moadianInvoices'; break;
            case 'send_to_moadian': $targetMethod = 'sendToMoadian'; break;
            
            case 'treasury_cheques': $targetMethod = 'treasuryCheques'; break;
            case 'create_cheque': $targetMethod = 'createCheque'; break;
            case 'store_cheque': $targetMethod = 'storeCheque'; break;
            case 'change_cheque_status': $targetMethod = 'changeChequeStatus'; break;
            case 'treasury_rasgiri': $targetMethod = 'treasuryRasgiri'; break;
            
            case 'boms': $targetMethod = 'boms'; break;
            case 'create_bom': $targetMethod = 'createBom'; break;
            case 'store_bom': $targetMethod = 'storeBom'; break;
            case 'production_orders': $targetMethod = 'productionOrders'; break;
            case 'create_production_order': $targetMethod = 'createProductionOrder'; break;
            case 'store_production_order': $targetMethod = 'storeProductionOrder'; break;
            case 'complete_production_order': $targetMethod = 'completeProductionOrder'; break;
            
            case 'procurement_requisitions': $targetMethod = 'procurementRequisitions'; break;
            case 'create_requisition': $targetMethod = 'createRequisition'; break;
            case 'store_requisition': $targetMethod = 'storeRequisition'; break;
            case 'approve_requisition': $targetMethod = 'approveRequisition'; break;
            
            case 'contract_guarantees': $targetMethod = 'contractGuarantees'; break;
            case 'store_guarantee': $targetMethod = 'storeGuarantee'; break;
            case 'update_guarantee_status': $targetMethod = 'updateGuaranteeStatus'; break;
            
            case 'ai_analytics': $targetMethod = 'aiAnalytics'; break;
            
            case 'workflow_rules': $targetMethod = 'workflowRules'; break;
            case 'store_workflow_rule': $targetMethod = 'storeWorkflowRule'; break;
            case 'toggle_workflow_rule': $targetMethod = 'toggleWorkflowRule'; break;

            case 'my_contracts': $targetMethod = 'myContracts'; break;
            case 'view_contract': 
                $targetMethod = 'viewContract';
                $hasParam = true;
                break;
            case 'my_invoices': $targetMethod = 'myInvoices'; break;

            case 'statement': $targetMethod = 'statement'; break;
            case 'submit_payment_proof': $targetMethod = 'submitPaymentProof'; break;

            case 'invoice_view': 
                $targetMethod = 'viewInvoice'; 
                $hasParam = true;
                break;
            case 'my_projects': $targetMethod = 'myProjects'; break;
            case 'my_tickets': $targetMethod = 'myTickets'; break;
            case 'create_ticket': $targetMethod = 'createTicket'; break;
            case 'store_ticket': $targetMethod = 'storeTicket'; break;
            case 'view_invoice': 
                $targetMethod = 'viewInvoice';
                $hasParam = true;
                break;
            case 'invoice_pay': $targetMethod = 'payInvoice'; break;
            case 'view_ticket': 
                $targetMethod = 'viewTicket';
                $hasParam = true;
                break;
            case 'store_ticket_reply': 
                $targetMethod = 'storeTicketReply';
                $hasParam = true;
                break;
            case 'announcements_index': $targetMethod = 'announcementsIndex'; break;
        }
        break;

    case 'payment':
        $controller = new PaymentController();
        switch($action) {
            case 'request': $targetMethod = 'request'; break;
            case 'callback': $targetMethod = 'callback'; break;
        }
        break;

    case 'ajax':
        $controller = new AjaxController();
        switch($action) {
            case 'get_users': $targetMethod = 'getUsers'; break;
            case 'send_message': $targetMethod = 'sendMessage'; break;
            case 'fetch_messages': $targetMethod = 'fetchMessages'; break;
            case 'number_to_words': $targetMethod = 'numberToWords'; break;
        }
        break;
    
    // تعریف کنترلرهای جدید
    case 'products':
        $controller = new ProductController();
        switch($action) {
            case 'index': $targetMethod = 'index'; break;
            case 'create': $targetMethod = 'create'; break;
            case 'store': $targetMethod = 'store'; break;
            case 'edit': 
                $targetMethod = 'edit';
                $hasParam = true;
                break;
            case 'update': 
                $targetMethod = 'update';
                $hasParam = true;
                break;
            case 'delete': 
                $targetMethod = 'delete';
                $hasParam = true;
                break;
            case 'product_flow_report':
                $targetMethod = 'productFlowReport';
                $hasParam = true;
                break;
        }
        break;
        
    case 'purchases':
    $controller = new PurchasesController();
    switch($action) {
        case 'index': $targetMethod = 'index'; break;
        case 'create': $targetMethod = 'create'; break;
        case 'store': $targetMethod = 'store'; break;
        case 'view':  
            $targetMethod = 'view';  
            $hasParam = true;
            break;
        case 'updateStatus':  
            $targetMethod = 'updateStatus';  
            $hasParam = true;
            break;
        case 'edit': // ✅ اضافه شدن مسیر edit
            $targetMethod = 'edit';
            $hasParam = true;
            break;
        case 'update': // ✅ اضافه شدن مسیر update
            $targetMethod = 'update';
            $hasParam = true;
            break;
        case 'delete': // ✅ اضافه شدن مسیر delete
            $targetMethod = 'delete';
            $hasParam = true;
            break;
    }
    break;
        
    case 'sales':
        $controller = new SalesController();
        switch($action) {
            case 'index': $targetMethod = 'index'; break;
            case 'create': $targetMethod = 'create'; break;
            case 'store': $targetMethod = 'store'; break;
            case 'view': 
                $targetMethod = 'view';
                $hasParam = true;
                break;
            case 'updateStatus': 
                $targetMethod = 'updateStatus';
                $hasParam = true;
                break;
        }
        break;
    
    
    case 'services':
        $controller = new ServiceController();
        switch($action) {
            case 'index': $targetMethod = 'index'; break;
            case 'create': $targetMethod = 'create'; break;
            case 'store': $targetMethod = 'store'; break;
            case 'edit': 
                $targetMethod = 'edit'; 
                $hasParam = true;
                break;
            case 'update': 
                $targetMethod = 'update'; 
                $hasParam = true;
                break;
            case 'delete': 
                $targetMethod = 'delete'; 
                $hasParam = true;
                break;
        }
        break;

     case 'categories':
        $controller = new CategoryController();
        switch($action) {
            case 'index': $targetMethod = 'index'; break;
            case 'create': $targetMethod = 'create'; break;
            case 'store': $targetMethod = 'store'; break;
            case 'edit': 
                $targetMethod = 'edit';
                $hasParam = true;
                break;
            case 'update': 
                $targetMethod = 'update';
                $hasParam = true;
                break;
            case 'delete': 
                $targetMethod = 'delete';
                $hasParam = true;
                break;
        }
        break;
        
        //انبارها و واحدها 
    case 'inventory':
        require_once __DIR__ . '/app/controllers/InventoryController.php';
        $controller = new InventoryController();
        $id = sanitize($_GET['id'] ?? null);
        switch ($action) {
            // مسیرهای مربوط به انبارها
            case 'warehouses': $targetMethod = 'warehouses'; break;
            case 'create_warehouse': $targetMethod = 'createWarehouse'; break;
            case 'store_warehouse': $targetMethod = 'storeWarehouse'; break;
            case 'edit_warehouse': 
                $targetMethod = 'editWarehouse';
                $hasParam = true;
                break;
            case 'update_warehouse': 
                $targetMethod = 'updateWarehouse';
                $hasParam = true;
                break;
            case 'delete_warehouse':
                $targetMethod = 'deleteWarehouse';
                $hasParam = true;
                break;

            // مسیرهای مربوط به واحدها
            case 'units': $targetMethod = 'units'; break;
            case 'create_unit': $targetMethod = 'createUnit'; break;
            case 'store_unit': $targetMethod = 'storeUnit'; break;
            case 'edit_unit':
                $targetMethod = 'editUnit';
                $hasParam = true;
                break;
            case 'update_unit':
                $targetMethod = 'updateUnit';
                $hasParam = true;
                break;
            case 'delete_unit':
                $targetMethod = 'deleteUnit';
                $hasParam = true;
                break;

            // مسیرهای جدید گزارشات و رسیدها
            case 'warehouse_report': $targetMethod = 'warehouseReport'; break;
            case 'product_flow_report': 
                $targetMethod = 'productFlowReport';
                $hasParam = true;
                break;
            case 'print_purchase_receipt': 
                $targetMethod = 'printPurchaseReceipt';
                $hasParam = true;
                break;
            case 'print_sale_receipt': 
                $targetMethod = 'printSaleReceipt';
                $hasParam = true;
                break;
        }
        break;
        
    default:
        http_response_code(404);
        view('errors/404', ['layout' => 'guest_layout', 'title' => 'صفحه یافت نشد']);
        exit();
}
// فراخوانی متد کنترلر
if ($controller && method_exists($controller, $targetMethod)) {
    if ($hasParam) {
        // مطمئن شوید که $id وجود دارد تا خطا رخ ندهد
        if ($id !== null) {
            call_user_func([$controller, $targetMethod], $id);
        } else {
            // اگر متد نیاز به id دارد اما id ارسال نشده، به صفحه 404 برود
            http_response_code(404);
            view('errors/404', ['layout' => 'guest_layout', 'title' => 'صفحه یافت نشد']);
            exit();
        }
    } else {
        call_user_func([$controller, $targetMethod]);
    }
} else {
    // در صورت عدم یافتن متد، به صفحه 404 هدایت کن
    http_response_code(404);
    view('errors/404', ['layout' => 'guest_layout', 'title' => 'صفحه یافت نشد']);
    exit();
}