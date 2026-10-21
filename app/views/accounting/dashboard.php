<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/admin/accounting/dashboard.php - داشبورد تخصصی و اولترا پرمیوم حسابداری

$totalIncome = $totalIncome ?? $data['totalIncome'] ?? 0;
$totalExpenses = $totalExpenses ?? $data['totalExpenses'] ?? 0;
$netProfit = $totalIncome - $totalExpenses;
$vouchersCount = $vouchersCount ?? $data['vouchersCount'] ?? 0;
$accountsReceivable = $accountsReceivable ?? $data['accountsReceivable'] ?? 0;
$bankBalance = $bankBalance ?? $data['bankBalance'] ?? 0;
$recentVouchers = $recentVouchers ?? $data['recentVouchers'] ?? [];
$upcomingInvoices = $upcomingInvoices ?? $data['upcomingInvoices'] ?? [];
?>

<style>
    .acc-kpi-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        padding: 1.5rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .acc-kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 35px -5px rgba(99, 102, 241, 0.12);
        border-color: #c7d2fe;
    }

    .kpi-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .quick-action-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1.25rem 1rem;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        color: #1e293b;
    }

    .quick-action-card:hover {
        background: #f8fafc;
        border-color: #6366f1;
        transform: translateY(-3px);
        box-shadow: 0 12px 25px -5px rgba(99, 102, 241, 0.15);
        color: #4338ca;
    }

    .quick-action-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        transition: all 0.3s ease;
    }

    .quick-action-card:hover .quick-action-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .table-acc-dashboard {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-acc-dashboard th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-acc-dashboard td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
        vertical-align: middle;
    }

    .table-acc-dashboard tr:hover td {
        background-color: #f8fafc;
    }

    .deadline-item {
        padding: 0.85rem 1rem;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    .deadline-item:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-calculator text-indigo-600" style="color:#6366f1;"></i>
            <span>داشبورد مدیریت امور مالی و حسابداری</span>
        </h3>
        <p class="text-muted small mb-0">مرور سریع شاخص‌های مالی، آخرین اسناد حسابداری، نقدینگی و وضعیت تسویه‌ها</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white border text-dark shadow-sm rounded-pill px-3 py-2 fw-bold font-monospace">
            <i class="far fa-calendar-alt text-indigo-600 me-1" style="color:#6366f1;"></i>
            <?php echo function_exists('jdate') ? jdate('Y/m/d') : date('Y-m-d'); ?>
        </span>
        <a href="index.php?page=admin&action=create_voucher_form" class="btn btn-primary rounded-pill px-4 py-2.5 shadow-sm fw-bold d-inline-flex align-items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>ثبت سند جدید</span>
        </a>
    </div>
</div>

<!-- کارت‌های آمار و شاخص‌های کلیدی مالی -->
<div class="row g-3 mb-4">
    <!-- درآمد ماه جاری -->
    <div class="col-xl-3 col-md-6">
        <div class="acc-kpi-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">درآمد کل دوره</span>
                <h4 class="fw-black text-emerald-600 font-monospace mb-0" style="color:#059669;">
                    <?php echo number_format($totalIncome); ?> <span class="fs-7 text-muted fw-normal">تومان</span>
                </h4>
            </div>
            <div class="kpi-icon-box bg-emerald-50 text-emerald-600" style="background:#d1fae5; color:#059669;">
                <i class="fas fa-arrow-trend-up"></i>
            </div>
        </div>
    </div>

    <!-- هزینه‌های ماه جاری -->
    <div class="col-xl-3 col-md-6">
        <div class="acc-kpi-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">هزینه‌های کل دوره</span>
                <h4 class="fw-black text-rose-600 font-monospace mb-0" style="color:#e11d48;">
                    <?php echo number_format($totalExpenses); ?> <span class="fs-7 text-muted fw-normal">تومان</span>
                </h4>
            </div>
            <div class="kpi-icon-box bg-rose-50 text-rose-600" style="background:#ffe4e6; color:#e11d48;">
                <i class="fas fa-arrow-trend-down"></i>
            </div>
        </div>
    </div>

    <!-- سود / زیان خالص -->
    <div class="col-xl-3 col-md-6">
        <div class="acc-kpi-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">سود (زیان) خالص</span>
                <h4 class="fw-black font-monospace mb-0 <?php echo $netProfit >= 0 ? 'text-indigo-600' : 'text-danger'; ?>" style="<?php echo $netProfit >= 0 ? 'color:#4338ca;' : ''; ?>">
                    <?php echo number_format($netProfit); ?> <span class="fs-7 text-muted fw-normal">تومان</span>
                </h4>
            </div>
            <div class="kpi-icon-box bg-indigo-50 text-indigo-600" style="background:#e0e7ff; color:#4338ca;">
                <i class="fas fa-scale-balanced"></i>
            </div>
        </div>
    </div>

    <!-- مطالبات از مشتریان -->
    <div class="col-xl-3 col-md-6">
        <div class="acc-kpi-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">مطالبات (حساب‌های دریافتنی)</span>
                <h4 class="fw-black text-amber-600 font-monospace mb-0" style="color:#d97706;">
                    <?php echo number_format($accountsReceivable); ?> <span class="fs-7 text-muted fw-normal">تومان</span>
                </h4>
            </div>
            <div class="kpi-icon-box bg-amber-50 text-amber-600" style="background:#fef3c7; color:#d97706;">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
        </div>
    </div>
</div>

<!-- میانبرهای دسترسی سریع به عملیات حسابداری -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <a href="index.php?page=admin&action=create_voucher_form" class="quick-action-card">
            <div class="quick-action-icon bg-indigo-50 text-indigo-600" style="background:#e0e7ff; color:#4338ca;">
                <i class="fas fa-file-signature"></i>
            </div>
            <span class="fw-bold small">ثبت سند جدید</span>
        </a>
    </div>

    <div class="col-6 col-md-4 col-lg-2">
        <a href="index.php?page=admin&action=accounting_expenses" class="quick-action-card">
            <div class="quick-action-icon bg-rose-50 text-rose-600" style="background:#ffe4e6; color:#e11d48;">
                <i class="fas fa-receipt"></i>
            </div>
            <span class="fw-bold small">ثبت هزینه</span>
        </a>
    </div>

    <div class="col-6 col-md-4 col-lg-2">
        <a href="index.php?page=admin&action=general_journal" class="quick-action-card">
            <div class="quick-action-icon bg-sky-50 text-sky-600" style="background:#e0f2fe; color:#0284c7;">
                <i class="fas fa-book"></i>
            </div>
            <span class="fw-bold small">دفتر روزنامه</span>
        </a>
    </div>

    <div class="col-6 col-md-4 col-lg-2">
        <a href="index.php?page=admin&action=account_ledger" class="quick-action-card">
            <div class="quick-action-icon bg-teal-50 text-teal-600" style="background:#ccfbf1; color:#0d9488;">
                <i class="fas fa-book-open"></i>
            </div>
            <span class="fw-bold small">دفتر کل</span>
        </a>
    </div>

    <div class="col-6 col-md-4 col-lg-2">
        <a href="index.php?page=admin&action=profitAndLossReport" class="quick-action-card">
            <div class="quick-action-icon bg-emerald-50 text-emerald-600" style="background:#d1fae5; color:#059669;">
                <i class="fas fa-chart-line"></i>
            </div>
            <span class="fw-bold small">گزارش سود و زیان</span>
        </a>
    </div>

    <div class="col-6 col-md-4 col-lg-2">
        <a href="index.php?page=admin&action=vat_report" class="quick-action-card">
            <div class="quick-action-icon bg-purple-50 text-purple-600" style="background:#f3e8ff; color:#7e22ce;">
                <i class="fas fa-percent"></i>
            </div>
            <span class="fw-bold small">گزارش ارزش افزوده</span>
        </a>
    </div>
</div>

<!-- جدول آخرین اسناد مالی و باکس یادآوری‌ها -->
<div class="row g-4 mb-4">
    <!-- جدول آخرین اسناد ثبت‌شده -->
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white h-100">
            <div class="card-header bg-white border-bottom border-light p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-list-check text-indigo-600" style="color:#6366f1;"></i>
                    <span>آخرین اسناد حسابداری ثبت‌شده</span>
                </h6>
                <a href="index.php?page=admin&action=general_journal" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold text-indigo-600" style="color:#4338ca;">
                    <span>مشاهده دفتر روزنامه</span>
                    <i class="fas fa-arrow-left ms-1"></i>
                </a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-acc-dashboard">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 80px;">شماره</th>
                                <th class="text-center" style="width: 110px;">تاریخ</th>
                                <th>شرح عمومی سند / بابت</th>
                                <th class="text-center" style="width: 140px;">بدهکار / بستانکار</th>
                                <th class="text-end px-4" style="width: 90px;">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentVouchers)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fs-2 mb-2 d-block opacity-40"></i>
                                        هنوز هیچ سند حسابداری در این دوره ثبت نشده است.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentVouchers as $voucher): ?>
                                    <tr>
                                        <td class="text-center font-monospace fw-bold text-indigo-600" style="color:#4338ca;">
                                            #<?php echo sanitize($voucher->voucher_number ?? $voucher->id); ?>
                                        </td>
                                        <td class="text-center font-monospace small text-muted">
                                            <?php echo function_exists('jdate') ? jdate('Y/m/d', strtotime($voucher->voucher_date)) : $voucher->voucher_date; ?>
                                        </td>
                                        <td class="fw-bold text-dark">
                                            <?php echo sanitize($voucher->description); ?>
                                        </td>
                                        <td class="text-center font-monospace fw-bold text-dark">
                                            <?php echo number_format($voucher->total_debit ?? $voucher->amount ?? 0); ?> <span class="small text-muted fw-normal">تومان</span>
                                        </td>
                                        <td class="text-end px-4">
                                            <a href="index.php?page=admin&action=general_journal" class="btn btn-sm btn-light border text-primary rounded-circle" style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center;" title="مشاهده">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- باکس سررسید فاکتورها و یادآوری‌های مالیاتی -->
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2 border-bottom pb-3">
                <i class="fas fa-bell text-warning"></i>
                <span>یادآوری‌ها و سررسیدهای مالی</span>
            </h6>

            <div class="d-flex flex-column gap-1">
                <div class="deadline-item d-flex align-items-center justify-content-between">
                    <div>
                        <strong class="d-block text-dark small fw-bold mb-0.5">ارسال اظهارنامه ارزش افزوده</strong>
                        <span class="text-muted small">تکالیف قانونی دوره جاری</span>
                    </div>
                    <span class="badge bg-rose-50 text-rose-700 border border-rose-200 rounded-pill px-2.5 py-1 font-monospace" style="background:#ffe4e6; color:#e11d48;">
                        سررسید نزدیک
                    </span>
                </div>

                <div class="deadline-item d-flex align-items-center justify-content-between">
                    <div>
                        <strong class="d-block text-dark small fw-bold mb-0.5">محاسبه و صدور لیست حقوق</strong>
                        <span class="text-muted small">پایان ماه جاری</span>
                    </div>
                    <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-2.5 py-1 font-monospace" style="background:#e0e7ff; color:#4338ca;">
                        تا انتهای ماه
                    </span>
                </div>

                <div class="deadline-item d-flex align-items-center justify-content-between">
                    <div>
                        <strong class="d-block text-dark small fw-bold mb-0.5">مغایرت‌گیری بانک‌ها</strong>
                        <span class="text-muted small">تطبیق تراکنش‌های هفتگی</span>
                    </div>
                    <span class="badge bg-amber-50 text-amber-700 border border-amber-200 rounded-pill px-2.5 py-1 font-monospace" style="background:#fef3c7; color:#b45309;">
                        هفتگی
                    </span>
                </div>
            </div>

            <!-- لینک سریع گزارشات -->
            <div class="mt-4 pt-3 border-top">
                <a href="index.php?page=admin&action=financial_reports" class="btn btn-outline-primary w-100 rounded-pill py-2.5 fw-bold d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-chart-pie"></i>
                    <span>مشاهده گزارشات جامع مالی</span>
                </a>
            </div>
        </div>
    </div>
</div>