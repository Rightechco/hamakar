<?php
// app/views/shared/sidebar.php - نسخه نهایی و مدرن با دیوایدر و منوهای کشویی

global $auth;
$currentTitle = $title ?? '';

// آرایه‌های مورد نیاز برای فعال‌سازی لینک‌ها
$user_management_titles = ['Manage Users', 'Create User', 'Edit User', 'مدیریت کاربران'];
$client_management_titles = ['مدیریت مشتریان', 'افزودن مشتری جدید', 'ویرایش مشتری', 'مشاهده لاگ ها'];
$contract_management_titles = ['مدیریت قراردادها', 'ایجاد قرارداد جدید', 'ویرایش قرارداد', 'مشاهده قرارداد'];
$invoice_management_titles = ['مدیریت فاکتورها', 'صدور فاکتور جدید', 'ویرایش فاکتور', 'مشاهده فاکتور'];
$project_titles = ['مدیریت پروژه‌ها', 'ایجاد پروژه جدید', 'مشاهده پروژه', 'ویرایش پروژه'];
$inventory_titles = ['مدیریت محصولات', 'افزودن محصول جدید', 'ویرایش محصول', 'مدیریت خریدها', 'ثبت خرید جدید', 'مدیریت فروش‌ها', 'ثبت فروش جدید']; // ✅ عناوین جدید
$accounting_titles = ['کدینگ حسابداری', 'ثبت سند جدید', 'مدیریت هزینه‌ها', 'گزارشات مالی', 'دفتر کل حساب', 'دفتر روزنامه', 'گزارش مالیات بر ارزش افزوده', 'بستن سال مالی', 'دفتر تفصیلی', 'بودجه‌بندی', 'گزارش بودجه', 'مغایرت‌گیری بانکی', 'مدیریت دارایی‌های ثابت', 'اجرای عملیات استهلاک', 'صورت جریان وجوه نقد', 'آنالیز هزینه‌ها', 'گزارش سود و زیان']; // ✅ عنوان جدید
$marketing_titles = ['استخراج بانک اطلاعات', 'بازاریابی پیامکی', 'تحقیقات بازاریابی و جمع‌آوری لید'];
$messaging_titles = ['مدیریت اطلاعیه‌ها', 'ایجاد اطلاعیه جدید', 'ویرایش اطلاعیه', 'مدیریت تیکت‌ها', 'مشاهده تیکت', 'پیام‌ها'];
$hr_titles = ['مدیریت حقوق و دستمزد', 'صدور فیش حقوقی جدید', 'مشاهده فیش حقوقی', 'تنظیمات سالانه حقوق و دستمزد', 'حضور و غیاب', 'مدیریت مرخصی‌ها', 'نیازسنجی آموزش', 'آموزش کارکنان'];
$system_settings_titles = ['System Settings', 'مدیریت کلیدهای API'];

// تعیین اینکه کدام منوهای اصلی باید باز باشند
$isClientsActive = in_array($currentTitle, array_merge($client_management_titles, $contract_management_titles, $invoice_management_titles));
$isInventoryActive = in_array($currentTitle, $inventory_titles); // ✅ متغیر جدید
$isAccountingActive = in_array($currentTitle, $accounting_titles);
$isMarketingActive = in_array($currentTitle, $marketing_titles);
$isMessagingActive = in_array($currentTitle, $messaging_titles);
$isHRActive = in_array($currentTitle, $hr_titles);
$isProjectActive = in_array($currentTitle, $project_titles);
$isUserManagementActive = in_array($currentTitle, $user_management_titles);
$isSystemSettingsActive = in_array($currentTitle, $system_settings_titles);
$isTrainingActive = ($currentTitle === 'نیازسنجی آموزشی' || $currentTitle === 'بررسی نیازسنجی'); 
$isTrainingCoursesActive = ($currentTitle === 'مدیریت دوره‌های آموزشی'); 
$isAssessmentActive = ($currentTitle === 'مدیریت آزمون‌های مهارتی');
?>
<style>
    /* Modern Styles for Project View */
    .project-header-card {
        background: linear-gradient(to right, #4e73df, #6610f2);
        color: white;
        border-radius: 15px;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .project-header-card h1 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    .project-header-card .badge {
        font-size: 1rem;
        padding: 0.5em 1em;
        border-radius: 20px;
        background-color: rgba(255, 255, 255, 0.2);
    }
    .project-header-card .progress-bar {
        background-color: #28a745;
        border-radius: 50px;
    }
    .project-details-card, .task-board-card, .members-card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: none;
    }
    .task-board-card .card-body {
        padding: 1rem;
    }
    .task-column-header {
        border-radius: 10px;
        padding: 10px;
        color: white;
        margin-bottom: 1rem;
    }
    .task-column-header.bg-light {
        background-color: #f1f3f5 !important;
        color: #212529 !important;
    }
    .task-column-header.bg-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }
    .task-column-header.bg-info {
        background-color: #0dcaf0 !important;
        color: #212529 !important;
    }
    .task-column-header.bg-success {
        background-color: #198754 !important;
        color: #fff !important;
    }
    .task-item {
        background-color: white;
        border-radius: 8px;
        margin-bottom: 10px;
        padding: 15px;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .task-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .task-title {
        font-weight: 600;
        margin-bottom: 5px;
    }
    .task-meta {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 8px;
    }
    .task-action-buttons {
        opacity: 0;
        transition: opacity 0.2s;
    }
    .task-item:hover .task-action-buttons {
        opacity: 1;
    }
</style>

<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <a href="index.php?page=admin&action=dashboard">
            <i class="fas fa-chart-line"></i>
            <span>سامانه مدیریت</span>
        </a>
    </div>
    
    <ul class="list-unstyled sidebar-nav">
        
        <?php if ($auth->hasRole(['admin', 'accountant', 'accountant_viewer'])): ?>

            <li class="nav-item"><a class="nav-link <?php echo ($currentTitle === 'Admin Dashboard') ? 'active' : ''; ?>" href="index.php?page=admin&action=dashboard"><i class="fas fa-fw fa-tachometer-alt"></i><span>داشبورد</span></a></li>

            <?php if ($auth->hasRole(['admin'])): ?>
            <li class="nav-item"><a class="nav-link <?php echo $isUserManagementActive ? 'active' : ''; ?>" href="index.php?page=admin&action=users"><i class="fas fa-fw fa-user-shield"></i><span>کاربران</span></a></li>
            <?php endif; ?>

            <li class="nav-item">
    <a class="nav-link collapsed <?php echo $isClientsActive ? 'active' : ''; ?>" href="#collapseClients" data-bs-toggle="collapse" aria-expanded="<?php echo $isClientsActive ? 'true' : 'false'; ?>">
        <i class="fas fa-fw fa-users"></i><span>مشتریان</span>
    </a>
    <div id="collapseClients" class="collapse <?php echo $isClientsActive ? 'show' : ''; ?>">
        <ul class="sidebar-submenu">
            <li><a class="submenu-item" href="index.php?page=admin&action=clients">لیست مشتریان</a></li>
            <li><a class="submenu-item" href="index.php?page=admin&action=contracts">قراردادها</a></li>
            <li><a class="submenu-item" href="index.php?page=admin&action=invoices">فاکتورها</a></li>
            <li><a class="submenu-item" href="index.php?page=categories&action=index">مدیریت دسته‌بندی‌ها</a></li> </ul>
    </div>
</li>
            
            <li class="nav-item">
                <a class="nav-link collapsed <?php echo $isInventoryActive ? 'active' : ''; ?>" href="#collapseInventory" data-bs-toggle="collapse" aria-expanded="<?php echo $isInventoryActive ? 'true' : 'false'; ?>">
                    <i class="fas fa-fw fa-boxes"></i><span>انبارداری و محصولات</span>
                </a>
                <div id="collapseInventory" class="collapse <?php echo $isInventoryActive ? 'show' : ''; ?>">
                    <ul class="sidebar-submenu">
                        <li><a class="submenu-item" href="index.php?page=products&action=index">محصولات</a></li>
                        <li><a class="submenu-item" href="index.php?page=sales&action=index">فروش‌ها</a></li>
                        <li><a class="submenu-item" href="index.php?page=purchases&action=index">خریدها</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item"><a class="nav-link <?php echo $isProjectActive ? 'active' : ''; ?>" href="index.php?page=admin&action=projects"><i class="fas fa-fw fa-project-diagram"></i><span>پروژه‌ها</span></a></li>
            
            <li class="nav-item">
                <a class="nav-link collapsed <?php echo $isAccountingActive ? 'active' : ''; ?>" href="#collapseAccounting" data-bs-toggle="collapse" aria-expanded="<?php echo $isAccountingActive ? 'true' : 'false'; ?>">
                    <i class="fas fa-fw fa-calculator"></i><span>حسابداری</span>
                </a>
                <div id="collapseAccounting" class="collapse <?php echo $isAccountingActive ? 'show' : ''; ?>">
                    <ul class="sidebar-submenu">
                        <?php if ($auth->hasRole(['admin', 'accountant'])): ?>
                            <li><a class="submenu-item" href="index.php?page=admin&action=accounting_accounts">کدینگ حسابداری</a></li>
                            <li><a class="submenu-item" href="index.php?page=admin&action=create_voucher_form">ثبت سند</a></li>
                            <li><a class="submenu-item" href="index.php?page=admin&action=accounting_expenses">مدیریت هزینه‌ها</a></li>
                            <li><a class="submenu-item" href="index.php?page=admin&action=bank_reconciliation">مغایرت‌گیری بانکی</a></li>
                            <li><a class="submenu-item" href="index.php?page=admin&action=fixed_assets">دارایی‌های ثابت</a></li>
                            <li><a class="submenu-item" href="index.php?page=admin&action=budgeting">بودجه‌بندی</a></li>
                            <hr class="dropdown-divider">
                        <?php endif; ?>
                        <li><a class="submenu-item" href="index.php?page=admin&action=general_journal">دفتر روزنامه</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=account_ledger">دفتر کل</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=subledger_report">دفتر تفصیلی</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=financial_reports">گزارشات مالی اصلی</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=profitAndLossReport">گزارش سود و زیان</a></li> <li><a class="submenu-item" href="index.php?page=admin&action=vat_report">گزارش ارزش افزوده</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=budget_report">گزارش بودجه</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=expense_analysis_report">آنالیز هزینه‌ها</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=cash_flow_report">صورت جریان وجوه نقد</a></li>
                        <?php if ($auth->hasRole(['admin'])): ?>
                            <hr class="dropdown-divider">
                            <li><a class="submenu-item" href="index.php?page=admin&action=close_fiscal_year">بستن سال مالی</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>

            <?php if ($auth->hasRole(['admin'])): ?>
            <li class="nav-item">
                <a class="nav-link collapsed <?php echo $isMarketingActive ? 'active' : ''; ?>" href="#collapseMarketing" data-bs-toggle="collapse" aria-expanded="<?php echo $isMarketingActive ? 'true' : 'false'; ?>">
                    <i class="fas fa-fw fa-bullhorn"></i><span>بازاریابی</span>
                </a>
                <div id="collapseMarketing" class="collapse <?php echo $isMarketingActive ? 'show' : ''; ?>">
                    <ul class="sidebar-submenu">
                        <li><a class="submenu-item" href="index.php?page=admin&action=showMarketingForm">تحقیقات بازاریابی و جمع‌آوری لید</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=sms_panel">بازاریابی پیامکی</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed <?php echo $isMessagingActive ? 'active' : ''; ?>" href="#collapseMessaging" data-bs-toggle="collapse" aria-expanded="<?php echo $isMessagingActive ? 'true' : 'false'; ?>">
                    <i class="fas fa-fw fa-comment-alt"></i><span>مرکز پیام</span>
                </a>
                <div id="collapseMessaging" class="collapse <?php echo $isMessagingActive ? 'show' : ''; ?>">
                    <ul class="sidebar-submenu">
                        <li><a class="submenu-item" href="index.php?page=admin&action=announcements_index">اطلاعیه‌ها</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=tickets">تیکت و پشتیبانی</a></li>
                        <li><a class="submenu-item" href="#">پیام‌ها (چت)</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed <?php echo $isHRActive || $isTrainingActive || $isTrainingCoursesActive || $isAssessmentActive ? 'active' : ''; ?>" href="#collapseHR" data-bs-toggle="collapse" aria-expanded="<?php echo $isHRActive || $isTrainingActive || $isTrainingCoursesActive || $isAssessmentActive ? 'true' : 'false'; ?>">
                    <i class="fas fa-fw fa-users-cog"></i><span>منابع انسانی</span>
                </a>
                <div id="collapseHR" class="collapse <?php echo $isHRActive || $isTrainingActive || $isTrainingCoursesActive || $isAssessmentActive ? 'show' : ''; ?>">
                    <ul class="sidebar-submenu">
                        <li><a class="submenu-item" href="index.php?page=admin&action=payrolls">حقوق و دستمزد</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=payroll_settings">تنظیمات حقوق</a></li>
                        <hr class="dropdown-divider">
                        <li><a class="submenu-item" href="index.php?page=admin&action=attendance_report">حضور و غیاب</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=manage_leave_requests">مدیریت مرخصی‌ها</a></li>
                        <hr class="dropdown-divider">
                        <li><a class="submenu-item" href="index.php?page=admin&action=training_needs">نیازسنجی آموزش</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=manage_training_courses">مدیریت دوره‌ها</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=manage_skill_assessments">مدیریت آزمون‌ها</a></li>
                        <li><a class="submenu-item" href="index.php?page=admin&action=training_needs_analysis">گزارش تحلیلی آموزش</a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed <?php echo $isSystemSettingsActive ? 'active' : ''; ?>" href="#collapseSettings" data-bs-toggle="collapse" aria-expanded="<?php echo $isSystemSettingsActive ? 'true' : 'false'; ?>">
                    <i class="fas fa-fw fa-cogs"></i><span>تنظیمات سیستم</span>
                </a>
                <div id="collapseSettings" class="collapse <?php echo $isSystemSettingsActive ? 'show' : ''; ?>">
                    <ul class="sidebar-submenu">
                        <li><a class="submenu-item" href="index.php?page=admin&action=showApiSettings">مدیریت کلیدهای API</a></li>
                        <li><a class="submenu-item" href="#">تنظیمات عمومی</a></li>
                        <li><a class="submenu-item" href="#">نقش‌ها و دسترسی‌ها</a></li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

        <?php elseif ($auth->hasRole(['employee'])): ?>
            <li class="nav-item"><a class="nav-link" href="index.php?page=employee&action=dashboard"><i class="fas fa-fw fa-tachometer-alt"></i><span>داشبورد</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?page=employee&action=my_projects"><i class="fas fa-fw fa-tasks"></i><span>پروژه‌ها و وظایف من</span></a></li>
            
            <li class="nav-item">
                <a class="nav-link collapsed" href="#collapseEmployeeMessaging" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="fas fa-fw fa-comment-alt"></i><span>مرکز پیام</span>
                </a>
                <div id="collapseEmployeeMessaging" class="collapse">
                    <ul class="sidebar-submenu">
                        <li><a class="submenu-item" href="index.php?page=employee&action=announcements_index">اطلاعیه‌ها</a></li>
                        <li><a class="submenu-item" href="#">تیکت و پشتیبانی</a></li>
                        <li><a class="submenu-item" href="#">پیام‌ها (چت)</a></li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#collapseEmployeeHR" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="fas fa-fw fa-users-cog"></i><span>منابع انسانی</span>
                </a>
                <div id="collapseEmployeeHR" class="collapse">
                    <ul class="sidebar-submenu">
                        <li><a class="submenu-item" href="index.php?page=employee&action=my_payslips">فیش‌های حقوقی من</a></li>
                        <li><a class="submenu-item" href="index.php?page=employee&action=leave_requests">درخواست مرخصی</a></li>
                        <li><a class="submenu-item" href="index.php?page=employee&action=show_training_needs_form">نیازسنجی آموزش</a></li>
                        <li><a class="submenu-item" href="#">ارزیابی همکاران</a></li>
                    </ul>
                </div>
            </li>

        <?php elseif ($auth->hasRole(['client'])): ?>
            <li class="nav-item"><a class="nav-link" href="index.php?page=client&action=dashboard"><i class="fas fa-fw fa-tachometer-alt"></i><span>داشبورد</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?page=client&action=my_contracts"><i class="fas fa-fw fa-file-signature"></i><span>قراردادهای من</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?page=client&action=my_invoices"><i class="fas fa-fw fa-file-invoice"></i><span>فاکتورهای من</span></a></li>
            <li class="nav-item"><a class="nav-link" href="index.php?page=client&action=my_projects"><i class="fas fa-fw fa-project-diagram"></i><span>پروژه‌های من</span></a></li>
            
            <li class="nav-item">
                <a class="nav-link collapsed" href="#collapseClientMessaging" data-bs-toggle="collapse" aria-expanded="false">
                    <i class="fas fa-fw fa-comment-alt"></i><span>مرکز پیام</span>
                </a>
                <div id="collapseClientMessaging" class="collapse">
                    <ul class="sidebar-submenu">
                        <li><a class="submenu-item" href="index.php?page=client&action=announcements_index">اطلاعیه‌ها</a></li>
                        <li><a class="submenu-item" href="index.php?page=client&action=my_tickets">تیکت و پشتیبانی</a></li>
                    </ul>
                </div>
            </li>
        <?php endif; ?>

        <li class="nav-item mt-auto">
            <a class="nav-link" href="index.php?page=logout">
                <i class="fas fa-fw fa-sign-out-alt"></i>
                <span>خروج</span>
            </a>
        </li>
    </ul>
</nav>