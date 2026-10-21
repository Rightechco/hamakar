<?php
// app/views/client/dashboard.php - نسخه اولترا پرمیوم راست‌چین (RTL) و کاملاً ایزوله
$stats = $stats ?? null;
$user = $user ?? null;
$license = $license ?? null;
?>

<!-- فراخوانی مستقیم CSS جهت تضمین راست‌چین بودن و استایل پرمیوم -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" />

<style>
    /* تنظیمات پایه راست‌چین و فونت */
    .client-dashboard-wrapper {
        direction: rtl !important;
        text-align: right !important;
        font-family: 'Vazirmatn', sans-serif !important;
        width: 100%;
        box-sizing: border-box;
    }

    /* کارت‌های KPI گرید اختصاصی */
    .kpi-grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }

    .premium-kpi-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        padding: 1.35rem 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .premium-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 4px;
        background: transparent;
        transition: background 0.3s ease;
    }

    .premium-kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px -5px rgba(99, 102, 241, 0.12);
        border-color: rgba(99, 102, 241, 0.2);
    }

    .premium-kpi-card.card-contracts:hover::before { background: linear-gradient(90deg, #6366f1, #818cf8); }
    .premium-kpi-card.card-invoices:hover::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .premium-kpi-card.card-unpaid:hover::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .premium-kpi-card.card-debt:hover::before { background: linear-gradient(90deg, #f43f5e, #fb7185); }

    .kpi-icon-wrapper {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .icon-bg-indigo { background: #e0e7ff; color: #4338ca; }
    .icon-bg-emerald { background: #d1fae5; color: #059669; }
    .icon-bg-amber { background: #fef3c7; color: #b45309; }
    .icon-bg-rose { background: #ffe4e6; color: #e11d48; }

    .kpi-title {
        font-size: 0.825rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 0.35rem;
    }

    .kpi-value {
        font-size: 1.75rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
        font-family: monospace, 'Vazirmatn';
    }

    /* بنر وضعیت لایسنس */
    .license-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 22px;
        color: #ffffff;
        padding: 1.75rem;
        margin-bottom: 1.75rem;
        box-shadow: 0 15px 30px -5px rgba(15, 23, 42, 0.2);
    }

    /* شبکه میانبرهای سریع */
    .shortcut-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }

    .quick-shortcut-box {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 18px;
        padding: 1.25rem 1rem;
        text-align: center;
        text-decoration: none !important;
        color: #0f172a;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.6rem;
        box-shadow: 0 4px 12px -2px rgba(0,0,0,0.03);
    }

    .quick-shortcut-box:hover {
        background: #4f46e5;
        color: #ffffff !important;
        border-color: #4f46e5;
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(79, 70, 229, 0.3);
    }

    .quick-shortcut-box i {
        font-size: 1.6rem;
    }

    .quick-shortcut-box:hover i {
        color: #ffffff !important;
    }

    .pulse-green-live {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse-green-anim 2s infinite;
    }

    @keyframes pulse-green-anim {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
</style>

<div class="client-dashboard-wrapper">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 class="h3 fw-extrabold text-dark mb-0">خوش آمدید، <?php echo htmlspecialchars($user->name ?? 'ماهرخ رضوانی'); ?> 👋</h1>
                <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 small fw-bold d-inline-flex align-items-center gap-2" style="background:#e0e7ff; color:#4338ca;">
                    <span class="pulse-green-live"></span>
                    پنل آنلاین کلاینت
                </span>
            </div>
            <p class="text-muted small mb-0">مرور سریع خلاصه‌ی وضعیت حساب، فاکتورها، قراردادها و لایسنس فعال شما</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo APP_URL; ?>/index.php?page=client&action=create_ticket" class="btn btn-primary rounded-3 px-3.5 py-2.5 fw-bold d-inline-flex align-items-center gap-2 shadow-sm text-white text-decoration-none" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); border:none;">
                <i class="fas fa-plus-circle"></i>
                <span>ثبت تیکت جدید</span>
            </a>
        </div>
    </div>

    <!-- بنر لایسنس فعال -->
    <?php if (isset($license)): ?>
        <div class="license-banner">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-white bg-opacity-10 p-3 text-white d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="fas fa-robot fs-3 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 fs-6">لایسنس ربات هوشمند شما فعال است</h5>
                        <span class="small text-white-50">کد اختصاصی: <code class="text-warning font-monospace fw-bold ms-1" style="color:#fde047 !important;"><?php echo htmlspecialchars($license->license_key ?? '---'); ?></code></span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-start text-md-end">
                        <span class="d-block small text-white-50">تاریخ انقضا:</span>
                        <span class="fw-bold font-monospace" style="color: #34d399;"><?php echo !empty($license->expires_at) ? jdate('Y/m/d', strtotime($license->expires_at)) : '---'; ?></span>
                    </div>
                    <a href="<?php echo APP_URL; ?>/index.php?page=client&action=my_licenses" class="btn btn-light btn-sm rounded-3 px-3 fw-bold text-dark text-decoration-none">مدیریت لایسنس</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- کارت‌های KPI چهارگانه به صورت شبکه هم‌تراز -->
    <div class="kpi-grid-container">
        <!-- تعداد کل قراردادها -->
        <div class="premium-kpi-card card-contracts">
            <div>
                <div class="kpi-title">تعداد کل قراردادها</div>
                <div class="kpi-value"><?php echo number_format($stats['contracts_count'] ?? 0); ?></div>
            </div>
            <div class="kpi-icon-wrapper icon-bg-indigo">
                <i class="fas fa-file-signature"></i>
            </div>
        </div>

        <!-- تعداد کل فاکتورها -->
        <div class="premium-kpi-card card-invoices">
            <div>
                <div class="kpi-title">تعداد کل فاکتورها</div>
                <div class="kpi-value"><?php echo number_format($stats['invoices_count'] ?? 0); ?></div>
            </div>
            <div class="kpi-icon-wrapper icon-bg-emerald">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>

        <!-- فاکتورهای معوق -->
        <div class="premium-kpi-card card-unpaid">
            <div>
                <div class="kpi-title">فاکتورهای معوق</div>
                <div class="kpi-value text-warning"><?php echo number_format($stats['unpaid_invoices_count'] ?? 0); ?></div>
            </div>
            <div class="kpi-icon-wrapper icon-bg-amber">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
        </div>

        <!-- مبلغ کل بدهی -->
        <div class="premium-kpi-card card-debt">
            <div>
                <div class="kpi-title">مبلغ کل بدهی</div>
                <div class="kpi-value text-danger" style="font-size: 1.4rem;">
                    <?php echo number_format($stats['total_unpaid_amount'] ?? 0); ?>
                    <small class="fs-7 text-muted font-normal" style="font-size:0.75rem;">تومان</small>
                </div>
            </div>
            <div class="kpi-icon-wrapper icon-bg-rose">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>

    <!-- میانبرهای دسترسی سریع -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
            <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fas fa-compass text-indigo-600" style="color:#6366f1;"></i>
                <span>میانبرهای دسترسی سریع خدمات</span>
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="shortcut-grid">
                <a href="<?php echo APP_URL; ?>/index.php?page=client&action=my_invoices" class="quick-shortcut-box">
                    <i class="fas fa-receipt text-indigo-600" style="color:#6366f1;"></i>
                    <span class="fw-bold">فاکتورها و پرداخت</span>
                </a>
                <a href="<?php echo APP_URL; ?>/index.php?page=client&action=my_contracts" class="quick-shortcut-box">
                    <i class="fas fa-file-contract text-emerald-600" style="color:#10b981;"></i>
                    <span class="fw-bold">قراردادهای من</span>
                </a>
                <a href="<?php echo APP_URL; ?>/index.php?page=client&action=my_projects" class="quick-shortcut-box">
                    <i class="fas fa-diagram-project text-amber-600" style="color:#f59e0b;"></i>
                    <span class="fw-bold">پروژه‌های در حال انجام</span>
                </a>
                <a href="<?php echo APP_URL; ?>/index.php?page=client&action=my_tickets" class="quick-shortcut-box">
                    <i class="fas fa-headset text-sky-600" style="color:#0284c7;"></i>
                    <span class="fw-bold">پشتیبانی و تیکت‌ها</span>
                </a>
            </div>
        </div>
    </div>
</div>