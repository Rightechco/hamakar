<?php
// app/views/shared/sidebar.php - نسخه اولترا پرمیوم، کاملاً منطبق بر ساختار سیستم و دسترسی‌های جدید حسابداری

global $auth;
$currentTitle = $title ?? '';

// =========================================================
// آرایه‌های فعال‌سازی منوها (Active State Logic)
// =========================================================
$user_management_titles = ['Manage Users', 'Create User', 'Edit User', 'مدیریت کاربران'];
$client_management_titles = ['مدیریت مشتریان', 'افزودن مشتری جدید', 'ویرایش مشتری', 'مشاهده لاگ ها'];
$contract_management_titles = ['مدیریت قراردادها', 'ایجاد قرارداد جدید', 'ویرایش قرارداد', 'مشاهده قرارداد'];
$invoice_management_titles = ['مدیریت فاکتورها', 'صدور فاکتور جدید', 'ویرایش فاکتور', 'مشاهده فاکتور'];
$project_titles = ['مدیریت پروژه‌ها', 'ایجاد پروژه جدید', 'مشاهده پروژه', 'ویرایش پروژه', 'پروژه‌های من', 'وظایف من', 'مدیریت پروژه‌ها و وظایف من'];
$products_services_titles = ['مدیریت محصولات', 'افزودن محصول جدید', 'ویرایش محصول', 'مدیریت خدمات', 'افزودن خدمت جدید', 'ویرایش خدمت', 'مدیریت دسته‌بندی‌ها', 'افزودن دسته‌بندی جدید', 'ویرایش دسته‌بندی'];
$inventory_titles = ['لیست انبارها', 'افزودن انبار جدید', 'واحدها', 'گزارش موجودی', 'ورود/خروج کالا', 'مدیریت خریدها', 'ثبت خرید جدید', 'مدیریت فروش‌ها', 'ثبت فروش جدید'];
$accounting_titles = ['داشبورد مدیریت امور مالی و حسابداری', 'داشبورد حسابداری', 'کدینگ حسابداری', 'ثبت سند جدید', 'مدیریت هزینه‌ها', 'گزارشات مالی', 'دفتر کل حساب', 'دفتر کل', 'دفتر روزنامه', 'گزارش مالیات بر ارزش افزوده', 'بستن سال مالی', 'دفتر تفصیلی', 'بودجه‌بندی', 'گزارش بودجه', 'مغایرت‌گیری بانکی', 'مدیریت دارایی‌های ثابت', 'اجرای عملیات استهلاک', 'صورت جریان وجوه نقد', 'آنالیز هزینه‌ها', 'گزارش سود و زیان'];
$marketing_titles = ['استخراج بانک اطلاعات', 'بازاریابی پیامکی', 'تحقیقات بازاریابی و جمع‌آوری لید'];
$messaging_titles = ['مدیریت اطلاعیه‌ها', 'اطلاعیه‌ها و اخبار سیستم', 'ایجاد اطلاعیه جدید', 'ویرایش اطلاعیه', 'مدیریت تیکت‌ها', 'مشاهده تیکت', 'پیام‌ها'];
$hr_titles = ['مدیریت حقوق و دستمزد', 'صدور فیش حقوقی جدید', 'فیش‌های حقوقی من', 'مشاهده فیش حقوقی', 'تنظیمات سالانه حقوق و دستمزد', 'حضور و غیاب', 'مدیریت مرخصی‌ها', 'مدیریت درخواست‌های مرخصی', 'نیازسنجی آموزش', 'نیازسنجی و ارتقای مهارت‌های فردی', 'آموزش کارکنان', 'لیست کارمندان و عملکرد', 'گزارش عملکرد', 'آزمون خودارزیابی عملکرد', 'ارزیابی عملکرد همکار'];

// بخش‌های لایسنس و پلن
$license_titles = ['مدیریت لایسنس‌ها', 'صدور لایسنس جدید', 'لیست لایسنس‌ها']; 
$plan_titles = ['مدیریت پلن‌های فروش', 'تعریف پلن جدید', 'تعریف طرح جدید'];
$system_settings_titles = ['System Settings', 'مدیریت کلیدهای API'];

// بررسی وضعیت فعال بودن منوها
$isClientsActive = in_array($currentTitle, array_merge($client_management_titles, $contract_management_titles, $invoice_management_titles));
$isProductsServicesActive = in_array($currentTitle, $products_services_titles);
$isInventoryActive = in_array($currentTitle, $inventory_titles);
$isAccountingActive = in_array($currentTitle, $accounting_titles);
$isMarketingActive = in_array($currentTitle, $marketing_titles);
$isMessagingActive = in_array($currentTitle, $messaging_titles);
$isProjectActive = in_array($currentTitle, $project_titles);
$isUserManagementActive = in_array($currentTitle, $user_management_titles);
$isSystemSettingsActive = in_array($currentTitle, $system_settings_titles);

$isLicenseActive = in_array($currentTitle, $license_titles); 
$isPlansActive = in_array($currentTitle, $plan_titles);
$isHRActive = in_array($currentTitle, $hr_titles); 
?>

<style>
    .premium-sidebar {
        width: 270px;
        min-width: 270px;
        background: #0f172a;
        color: #94a3b8;
        display: flex;
        flex-direction: column;
        height: 100vh;
        position: sticky;
        top: 0;
        z-index: 1040;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 1px solid rgba(255, 255, 255, 0.05);
    }
    .sidebar-header-box {
        height: 65px;
        padding: 0 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #020617;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    .sidebar-menu-wrapper {
        flex: 1;
        overflow-y: auto;
        padding: 1rem 0.75rem;
    }
    .sidebar-menu-wrapper::-webkit-scrollbar { width: 4px; }
    .sidebar-menu-wrapper::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

    .nav-btn-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0.7rem 0.9rem;
        color: #94a3b8;
        border-radius: 10px;
        text-decoration: none !important;
        font-size: 0.825rem;
        font-weight: 600;
        transition: all 0.2s ease;
        background: transparent;
        border: none;
        cursor: pointer;
        margin-bottom: 2px;
    }
    .nav-btn-link:hover, .nav-btn-link.active {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.06);
    }
    .nav-btn-link.active-root {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }
    .nav-btn-link i.icon-main {
        width: 22px;
        font-size: 1rem;
        margin-left: 8px;
        text-align: center;
    }
    
    .acc-submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.35s cubic-bezier(0, 1, 0, 1);
        padding-right: 1.25rem;
    }
    .acc-submenu.open {
        max-height: 1200px;
        transition: max-height 0.35s cubic-bezier(1, 0, 1, 0);
    }
    .acc-submenu a {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 500;
        text-decoration: none !important;
        border-radius: 6px;
        transition: all 0.2s;
    }
    .acc-submenu a:hover, .acc-submenu a.active {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.04);
    }
    .chevron-rot {
        transition: transform 0.25s ease;
        font-size: 0.65rem;
    }
    .chevron-rot.down { transform: rotate(-180deg); }

    @media (max-width: 768px) {
        .premium-sidebar {
            position: fixed;
            right: 0;
            top: 0;
            transform: translateX(100%);
        }
        .premium-sidebar.mobile-open {
            transform: translateX(0);
        }
    }
</style>

<aside id="sidebar" class="premium-sidebar">
    <!-- Header -->
    <div class="sidebar-header-box">
        <?php if (isset($auth) && $auth->isEmployee()): ?>
            <a href="index.php?page=employee&action=dashboard" class="d-flex align-items-center gap-2 text-white text-decoration-none">
                <div class="rounded-3 p-2 text-white d-flex align-items-center justify-content-center" style="background:#6366f1; width:34px; height:34px;">
                    <i class="fas fa-briefcase"></i>
                </div>
                <span class="fw-bold fs-6">پنل کارمندان</span>
            </a>
        <?php elseif (isset($auth) && $auth->isClient()): ?>
            <a href="index.php?page=client&action=dashboard" class="d-flex align-items-center gap-2 text-white text-decoration-none">
                <div class="rounded-3 p-2 text-white d-flex align-items-center justify-content-center" style="background:#0ea5e9; width:34px; height:34px;">
                    <i class="fas fa-user-tie"></i>
                </div>
                <span class="fw-bold fs-6">پرتال مشتریان</span>
            </a>
        <?php else: ?>
            <a href="index.php?page=admin&action=dashboard" class="d-flex align-items-center gap-2 text-white text-decoration-none">
                <div class="rounded-3 p-2 text-white d-flex align-items-center justify-content-center" style="background:#4f46e5; width:34px; height:34px;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="fw-bold fs-6">سامانه مدیریت</span>
            </a>
        <?php endif; ?>

        <button type="button" id="close-sidebar-btn" class="btn btn-sm text-white-50 d-md-none p-0">
            <i class="fas fa-times fs-5"></i>
        </button>
    </div>

    <!-- Menu Items -->
    <div class="sidebar-menu-wrapper">
        <?php if (isset($auth) && $auth->hasRole(['admin', 'accountant', 'accountant_viewer'])): ?>

            <!-- Dashboard -->
            <a href="index.php?page=admin&action=dashboard" class="nav-btn-link <?php echo ($currentTitle === 'Admin Dashboard' || $currentTitle === 'داشبورد مدیریتی هماکار') ? 'active-root' : ''; ?>">
                <div>
                    <i class="fas fa-tachometer-alt icon-main text-indigo-400"></i>
                    <span>داشبورد اصلی</span>
                </div>
            </a>

            <?php if ($auth->hasRole(['admin'])): ?>
            <!-- محصولات و فروش -->
            <div class="acc-group">
                <button type="button" class="nav-btn-link acc-trigger <?php echo $isPlansActive ? 'active' : ''; ?>">
                    <div>
                        <i class="fas fa-gem icon-main text-info"></i>
                        <span>محصولات و فروش</span>
                    </div>
                    <i class="fas fa-chevron-down chevron-rot <?php echo $isPlansActive ? 'down' : ''; ?>"></i>
                </button>
                <div class="acc-submenu <?php echo $isPlansActive ? 'open' : ''; ?>">
                    <a href="index.php?page=admin&action=plans"><i class="fas fa-minus text-muted"></i> مدیریت پلن‌ها</a>
                    <a href="index.php?page=admin&action=create_plan"><i class="fas fa-minus text-muted"></i> افزودن پلن جدید</a>
                </div>
            </div>

            <!-- مشترکین (ربات) -->
            <div class="acc-group">
                <button type="button" class="nav-btn-link acc-trigger <?php echo $isLicenseActive ? 'active' : ''; ?>">
                    <div>
                        <i class="fas fa-robot icon-main text-warning"></i>
                        <span>مشترکین (ربات)</span>
                    </div>
                    <i class="fas fa-chevron-down chevron-rot <?php echo $isLicenseActive ? 'down' : ''; ?>"></i>
                </button>
                <div class="acc-submenu <?php echo $isLicenseActive ? 'open' : ''; ?>">
                    <a href="index.php?page=admin&action=licenses"><i class="fas fa-minus text-muted"></i> لیست لایسنس‌ها</a>
                    <a href="index.php?page=admin&action=create_license_form"><i class="fas fa-minus text-muted"></i> صدور لایسنس جدید</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- مشتریان -->
            <div class="acc-group">
                <button type="button" class="nav-btn-link acc-trigger <?php echo $isClientsActive ? 'active' : ''; ?>">
                    <div>
                        <i class="fas fa-users icon-main text-primary"></i>
                        <span>مشتریان</span>
                    </div>
                    <i class="fas fa-chevron-down chevron-rot <?php echo $isClientsActive ? 'down' : ''; ?>"></i>
                </button>
                <div class="acc-submenu <?php echo $isClientsActive ? 'open' : ''; ?>">
                    <a href="index.php?page=admin&action=clients"><i class="fas fa-minus text-muted"></i> لیست مشتریان</a>
                    <a href="index.php?page=admin&action=contracts"><i class="fas fa-minus text-muted"></i> قراردادها</a>
                    <a href="index.php?page=admin&action=invoices"><i class="fas fa-minus text-muted"></i> فاکتورها</a>
                </div>
            </div>

            <!-- محصولات و خدمات -->
            <div class="acc-group">
                <button type="button" class="nav-btn-link acc-trigger <?php echo $isProductsServicesActive ? 'active' : ''; ?>">
                    <div>
                        <i class="fas fa-cubes icon-main text-success"></i>
                        <span>محصولات و خدمات</span>
                    </div>
                    <i class="fas fa-chevron-down chevron-rot <?php echo $isProductsServicesActive ? 'down' : ''; ?>"></i>
                </button>
                <div class="acc-submenu <?php echo $isProductsServicesActive ? 'open' : ''; ?>">
                    <a href="index.php?page=products&action=index"><i class="fas fa-minus text-muted"></i> لیست محصولات</a>
                    <a href="index.php?page=services&action=index"><i class="fas fa-minus text-muted"></i> لیست خدمات</a>
                    <a href="index.php?page=categories&action=index"><i class="fas fa-minus text-muted"></i> مدیریت دسته‌بندی‌ها</a>
                </div>
            </div>

            <!-- انبار -->
            <div class="acc-group">
                <button type="button" class="nav-btn-link acc-trigger <?php echo $isInventoryActive ? 'active' : ''; ?>">
                    <div>
                        <i class="fas fa-warehouse icon-main text-purple"></i>
                        <span>انبار</span>
                    </div>
                    <i class="fas fa-chevron-down chevron-rot <?php echo $isInventoryActive ? 'down' : ''; ?>"></i>
                </button>
                <div class="acc-submenu <?php echo $isInventoryActive ? 'open' : ''; ?>">
                    <a href="index.php?page=inventory&action=warehouses"><i class="fas fa-minus text-muted"></i> لیست انبارها</a>
                    <a href="index.php?page=inventory&action=create_warehouse"><i class="fas fa-minus text-muted"></i> افزودن انبار جدید</a>
                    <a href="index.php?page=inventory&action=units"><i class="fas fa-minus text-muted"></i> واحدها</a>
                    <div class="my-1 border-top border-secondary opacity-25"></div>
                    <a href="index.php?page=sales&action=index"><i class="fas fa-minus text-muted"></i> مدیریت فروش‌ها</a>
                    <a href="index.php?page=purchases&action=index"><i class="fas fa-minus text-muted"></i> مدیریت خریدها</a>
                </div>
            </div>

            <!-- پروژه‌ها -->
            <a href="index.php?page=admin&action=projects" class="nav-btn-link <?php echo $isProjectActive ? 'active-root' : ''; ?>">
                <div>
                    <i class="fas fa-project-diagram icon-main text-danger"></i>
                    <span>پروژه‌ها</span>
                </div>
            </a>

            <!-- حسابداری و امور مالی -->
            <div class="acc-group">
                <button type="button" class="nav-btn-link acc-trigger <?php echo $isAccountingActive ? 'active' : ''; ?>">
                    <div>
                        <i class="fas fa-calculator icon-main text-teal"></i>
                        <span>حسابداری و امور مالی</span>
                    </div>
                    <i class="fas fa-chevron-down chevron-rot <?php echo $isAccountingActive ? 'down' : ''; ?>"></i>
                </button>
                <div class="acc-submenu <?php echo $isAccountingActive ? 'open' : ''; ?>">
                    <a href="index.php?page=admin&action=accounting_dashboard" class="<?php echo ($currentTitle === 'داشبورد مدیریت امور مالی و حسابداری' || $currentTitle === 'داشبورد حسابداری') ? 'active' : ''; ?>">
                        <i class="fas fa-minus text-muted"></i> داشبورد مالی
                    </a>
                    <?php if ($auth->hasRole(['admin', 'accountant'])): ?>
                        <a href="index.php?page=admin&action=accounting_accounts"><i class="fas fa-minus text-muted"></i> کدینگ حسابداری</a>
                        <a href="index.php?page=admin&action=create_voucher_form"><i class="fas fa-minus text-muted"></i> ثبت سند دوبل</a>
                        <a href="index.php?page=admin&action=accounting_expenses"><i class="fas fa-minus text-muted"></i> مدیریت هزینه‌ها</a>
                        <a href="index.php?page=admin&action=bank_reconciliation"><i class="fas fa-minus text-muted"></i> مغایرت‌گیری بانکی</a>
                        <a href="index.php?page=admin&action=fixed_assets"><i class="fas fa-minus text-muted"></i> دارایی‌های ثابت</a>
                        <a href="index.php?page=admin&action=budgeting"><i class="fas fa-minus text-muted"></i> بودجه‌بندی</a>
                        <div class="my-1 border-top border-secondary opacity-25"></div>
                    <?php endif; ?>
                    <a href="index.php?page=admin&action=general_journal"><i class="fas fa-minus text-muted"></i> دفتر روزنامه</a>
                    <a href="index.php?page=admin&action=account_ledger"><i class="fas fa-minus text-muted"></i> دفتر کل</a>
                    <a href="index.php?page=admin&action=subledger_report"><i class="fas fa-minus text-muted"></i> دفتر تفصیلی</a>
                    <a href="index.php?page=admin&action=financial_reports"><i class="fas fa-minus text-muted"></i> گزارشات مالی اصلی</a>
                    <a href="index.php?page=admin&action=profitAndLossReport"><i class="fas fa-minus text-muted"></i> گزارش سود و زیان</a>
                    <a href="index.php?page=admin&action=vat_report"><i class="fas fa-minus text-muted"></i> گزارش ارزش افزوده</a>
                    <a href="index.php?page=admin&action=budget_report"><i class="fas fa-minus text-muted"></i> گزارش بودجه</a>
                    <a href="index.php?page=admin&action=expense_analysis_report"><i class="fas fa-minus text-muted"></i> آنالیز هزینه‌ها</a>
                    <a href="index.php?page=admin&action=cash_flow_report"><i class="fas fa-minus text-muted"></i> صورت جریان وجوه نقد</a>
                    <?php if ($auth->hasRole(['admin'])): ?>
                        <div class="my-1 border-top border-secondary opacity-25"></div>
                        <a href="index.php?page=admin&action=close_fiscal_year"><i class="fas fa-minus text-muted"></i> بستن سال مالی</a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($auth->hasRole(['admin'])): ?>
            <!-- بازاریابی -->
            <div class="acc-group">
                <button type="button" class="nav-btn-link acc-trigger <?php echo $isMarketingActive ? 'active' : ''; ?>">
                    <div>
                        <i class="fas fa-bullhorn icon-main text-warning"></i>
                        <span>بازاریابی</span>
                    </div>
                    <i class="fas fa-chevron-down chevron-rot <?php echo $isMarketingActive ? 'down' : ''; ?>"></i>
                </button>
                <div class="acc-submenu <?php echo $isMarketingActive ? 'open' : ''; ?>">
                    <a href="index.php?page=admin&action=showMarketingForm"><i class="fas fa-minus text-muted"></i> تحقیقات بازاریابی و جمع‌آوری لید</a>
                    <a href="index.php?page=admin&action=sms_panel"><i class="fas fa-minus text-muted"></i> بازاریابی پیامکی</a>
                </div>
            </div>

            <!-- مرکز پیام -->
            <div class="acc-group">
                <button type="button" class="nav-btn-link acc-trigger <?php echo $isMessagingActive ? 'active' : ''; ?>">
                    <div>
                        <i class="fas fa-comment-alt icon-main text-info"></i>
                        <span>مرکز پیام</span>
                    </div>
                    <i class="fas fa-chevron-down chevron-rot <?php echo $isMessagingActive ? 'down' : ''; ?>"></i>
                </button>
                <div class="acc-submenu <?php echo $isMessagingActive ? 'open' : ''; ?>">
                    <a href="index.php?page=admin&action=announcements_index"><i class="fas fa-minus text-muted"></i> اطلاعیه‌ها</a>
                    <a href="index.php?page=admin&action=tickets"><i class="fas fa-minus text-muted"></i> تیکت و پشتیبانی</a>
                </div>
            </div>

            <!-- منابع انسانی -->
            <div class="acc-group">
                <button type="button" class="nav-btn-link acc-trigger <?php echo $isHRActive ? 'active' : ''; ?>">
                    <div>
                        <i class="fas fa-users-cog icon-main text-danger"></i>
                        <span>منابع انسانی</span>
                    </div>
                    <i class="fas fa-chevron-down chevron-rot <?php echo $isHRActive ? 'down' : ''; ?>"></i>
                </button>
                <div class="acc-submenu <?php echo $isHRActive ? 'open' : ''; ?>">
                    <?php if ($auth->hasRole(['admin'])): ?>
                        <a href="index.php?page=admin&action=users"><i class="fas fa-minus text-muted"></i> مدیریت کاربران و دسترسی</a>
                        <a href="index.php?page=admin&action=employee_list"><i class="fas fa-minus text-muted"></i> لیست کارمندان و عملکرد</a>
                    <?php endif; ?>
                    <div class="my-1 border-top border-secondary opacity-25"></div>
                    <a href="index.php?page=admin&action=payrolls"><i class="fas fa-minus text-muted"></i> حقوق و دستمزد</a>
                    <a href="index.php?page=admin&action=payroll_settings"><i class="fas fa-minus text-muted"></i> تنظیمات حقوق</a>
                    <div class="my-1 border-top border-secondary opacity-25"></div>
                    <a href="index.php?page=admin&action=attendance_report"><i class="fas fa-minus text-muted"></i> حضور و غیاب</a>
                    <a href="index.php?page=admin&action=manage_leave_requests"><i class="fas fa-minus text-muted"></i> مدیریت مرخصی‌ها</a>
                    <div class="my-1 border-top border-secondary opacity-25"></div>
                    <a href="index.php?page=admin&action=training_needs"><i class="fas fa-minus text-muted"></i> نیازسنجی آموزش</a>
                    <a href="index.php?page=admin&action=manage_training_courses"><i class="fas fa-minus text-muted"></i> مدیریت دوره‌ها</a>
                    <a href="index.php?page=admin&action=manage_skill_assessments"><i class="fas fa-minus text-muted"></i> مدیریت آزمون‌ها</a>
                    <a href="index.php?page=admin&action=training_needs_analysis"><i class="fas fa-minus text-muted"></i> گزارش تحلیلی آموزش</a>
                </div>
            </div>

            <!-- تنظیمات سیستم -->
            <div class="acc-group">
                <button type="button" class="nav-btn-link acc-trigger <?php echo $isSystemSettingsActive ? 'active' : ''; ?>">
                    <div>
                        <i class="fas fa-cogs icon-main text-secondary"></i>
                        <span>تنظیمات سیستم</span>
                    </div>
                    <i class="fas fa-chevron-down chevron-rot <?php echo $isSystemSettingsActive ? 'down' : ''; ?>"></i>
                </button>
                <div class="acc-submenu <?php echo $isSystemSettingsActive ? 'open' : ''; ?>">
                    <a href="index.php?page=admin&action=showApiSettings"><i class="fas fa-minus text-muted"></i> مدیریت کلیدهای API</a>
                </div>
            </div>
            <?php endif; ?>

        <?php elseif (isset($auth) && $auth->hasRole(['employee'])): ?>
            
            <!-- ---------------------------------------------------- -->
            <!-- پنل کارمندان (EMPLOYEE PANEL MENU) -->
            <!-- ---------------------------------------------------- -->
            
            <!-- داشبورد و ثبت ورود/خروج -->
            <a href="index.php?page=employee&action=dashboard" class="nav-btn-link <?php echo ($currentTitle === 'داشبورد' || $currentTitle === 'Employee Dashboard' || strpos($currentTitle, 'خوش آمدید') !== false) ? 'active-root' : ''; ?>">
                <div>
                    <i class="fas fa-tachometer-alt icon-main text-indigo-400"></i>
                    <span>داشبورد و حضور و غیاب</span>
                </div>
            </a>

            <div class="text-uppercase small fw-bold text-muted px-3 mt-3 mb-2" style="font-size: 0.675rem; letter-spacing: 0.5px;">امور کاری و پروژه‌ها</div>

            <!-- پروژه‌ها و تسک‌های من -->
            <a href="index.php?page=employee&action=my_projects" class="nav-btn-link <?php echo $isProjectActive ? 'active-root' : ''; ?>">
                <div>
                    <i class="fas fa-diagram-project icon-main text-success"></i>
                    <span>پروژه‌ها و چک‌لیست‌ها</span>
                </div>
            </a>

            <div class="text-uppercase small fw-bold text-muted px-3 mt-3 mb-2" style="font-size: 0.675rem; letter-spacing: 0.5px;">امور پرسنلی و مالی</div>

            <!-- آکاردئون کارکرد و منابع انسانی کارمند -->
            <div class="acc-group">
                <button type="button" class="nav-btn-link acc-trigger <?php echo $isHRActive ? 'active' : ''; ?>">
                    <div>
                        <i class="fas fa-user-clock icon-main text-info"></i>
                        <span>امور پرسنلی و ارزیابی</span>
                    </div>
                    <i class="fas fa-chevron-down chevron-rot <?php echo $isHRActive ? 'down' : ''; ?>"></i>
                </button>
                <div class="acc-submenu <?php echo $isHRActive ? 'open' : ''; ?>">
                    <a href="index.php?page=employee&action=leave_requests" class="<?php echo ($currentTitle === 'مدیریت مرخصی‌ها' || $currentTitle === 'مدیریت درخواست‌های مرخصی') ? 'active' : ''; ?>">
                        <i class="fas fa-minus text-muted"></i> درخواست‌های مرخصی من
                    </a>
                    <a href="index.php?page=employee&action=my_payslips" class="<?php echo ($currentTitle === 'فیش‌های حقوقی من') ? 'active' : ''; ?>">
                        <i class="fas fa-minus text-muted"></i> فیش‌های حقوقی من
                    </a>
                    <div class="my-1 border-top border-secondary opacity-25"></div>
                    <a href="index.php?page=employee&action=show_training_needs_form" class="<?php echo ($currentTitle === 'نیازسنجی و ارتقای مهارت‌های فردی') ? 'active' : ''; ?>">
                        <i class="fas fa-minus text-muted"></i> نیازسنجی آموزشی
                    </a>
                    <a href="index.php?page=employee&action=show_self_assessment_form" class="<?php echo ($currentTitle === 'آزمون خودارزیابی عملکرد') ? 'active' : ''; ?>">
                        <i class="fas fa-minus text-muted"></i> خودارزیابی سالانه
                    </a>
                    <a href="index.php?page=employee&action=peer_assessment" class="<?php echo ($currentTitle === 'ارزیابی عملکرد همکار') ? 'active' : ''; ?>">
                        <i class="fas fa-minus text-muted"></i> ارزیابی همکاران (۳۶۰)
                    </a>
                </div>
            </div>

            <div class="text-uppercase small fw-bold text-muted px-3 mt-3 mb-2" style="font-size: 0.675rem; letter-spacing: 0.5px;">اطلاع‌رسانی</div>

            <!-- مرکز اطلاع‌رسانی و اطلاعیه‌ها -->
            <a href="index.php?page=employee&action=announcements_index" class="nav-btn-link <?php echo $isMessagingActive ? 'active-root' : ''; ?>">
                <div>
                    <i class="fas fa-bullhorn icon-main text-warning"></i>
                    <span>اطلاعیه‌های سازمان</span>
                </div>
            </a>

        <?php elseif (isset($auth) && $auth->hasRole(['client'])): ?>

            <!-- ---------------------------------------------------- -->
            <!-- پنل مشتریان (CLIENT PANEL MENU) -->
            <!-- ---------------------------------------------------- -->

            <a href="index.php?page=client&action=dashboard" class="nav-btn-link <?php echo ($currentTitle === 'داشبورد مشتری') ? 'active-root' : ''; ?>">
                <div><i class="fas fa-tachometer-alt icon-main text-indigo-400"></i><span>داشبورد</span></div>
            </a>
            <a href="index.php?page=client&action=my_licenses" class="nav-btn-link">
                <div><i class="fas fa-robot icon-main text-warning"></i><span>ربات‌های من (لایسنس)</span></div>
            </a>
            <a href="index.php?page=client&action=my_contracts" class="nav-btn-link">
                <div><i class="fas fa-file-signature icon-main text-info"></i><span>قراردادهای من</span></div>
            </a>
            <a href="index.php?page=client&action=my_invoices" class="nav-btn-link">
                <div><i class="fas fa-file-invoice-dollar icon-main text-success"></i><span>فاکتورهای من</span></div>
            </a>
            <a href="index.php?page=client&action=my_projects" class="nav-btn-link">
                <div><i class="fas fa-project-diagram icon-main text-danger"></i><span>پروژه‌های من</span></div>
            </a>
            
            <div class="acc-group">
                <button type="button" class="nav-btn-link acc-trigger <?php echo $isMessagingActive ? 'active' : ''; ?>">
                    <div><i class="fas fa-comment-alt icon-main text-primary"></i><span>مرکز پیام</span></div>
                    <i class="fas fa-chevron-down chevron-rot <?php echo $isMessagingActive ? 'down' : ''; ?>"></i>
                </button>
                <div class="acc-submenu <?php echo $isMessagingActive ? 'open' : ''; ?>">
                    <a href="index.php?page=client&action=announcements_index"><i class="fas fa-minus text-muted"></i> اطلاعیه‌ها</a>
                    <a href="index.php?page=client&action=my_tickets"><i class="fas fa-minus text-muted"></i> تیکت و پشتیبانی</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="pt-3 mt-3 border-top border-secondary opacity-25"></div>
        <a href="index.php?page=logout" class="nav-btn-link text-danger">
            <div><i class="fas fa-sign-out-alt icon-main"></i><span>خروج از حساب</span></div>
        </a>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const triggers = document.querySelectorAll('.acc-trigger');
        triggers.forEach(trig => {
            trig.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = this.nextElementSibling;
                const chevron = this.querySelector('.chevron-rot');
                
                submenu.classList.toggle('open');
                if(chevron) chevron.classList.toggle('down');
            });
        });

        // بسته شدن سایدبار در حالت موبایل
        const closeBtn = document.getElementById('close-sidebar-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) sidebar.classList.remove('mobile-open');
            });
        }
    });
</script>