<?php
// app/views/employee/payslips/index.php - نسخه اولترا پرمیوم و شاهکار
// نمایش لیست فیش‌های حقوقی صادرشده، خالص پرداختی و امکان دریافت نسخه چاپی
$payslips = $payslips ?? [];

$totalPayslips = count($payslips);
$totalNetPaid = 0;

foreach ($payslips as $ps) {
    $totalNetPaid += (float)($ps->net_pay ?? 0);
}
?>

<style>
    .payslip-card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .table-premium {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-premium th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-premium td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
        vertical-align: middle;
    }

    .table-premium tr:hover td {
        background-color: #f8fafc;
    }

    .btn-action-view {
        background: #e0e7ff;
        color: #4338ca !important;
        border: 1px solid #c7d2fe;
        border-radius: 10px;
        padding: 0.45rem 0.95rem;
        font-size: 0.825rem;
        font-weight: 700;
        transition: all 0.25s ease;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .btn-action-view:hover {
        background: #4f46e5;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1"><?php echo sanitize($title ?? 'فیش‌های حقوقی من'); ?></h1>
        <p class="text-muted small mb-0">مشاهده سوابق دریافت حقوق، جزئیات کسورات و مزایا و دریافت نسخه چاپی فیش</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
            <i class="fas fa-print"></i>
            <span>چاپ لیست</span>
        </button>
    </div>
</div>

<!-- کارت‌های آمار و خلاصه دریافتی -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">تعداد فیش‌های صادرشده</span>
                <h4 class="fw-black text-dark font-monospace mb-0"><?php echo number_format($totalPayslips); ?> <span class="fs-6 fw-normal text-muted">دوره</span></h4>
            </div>
            <div class="rounded-3 p-3 text-indigo-600" style="background:#e0e7ff; color:#4338ca;">
                <i class="fas fa-file-invoice-dollar fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">مجموع دریافتی کل سوابق</span>
                <h4 class="fw-black text-emerald-600 font-monospace mb-0" style="color:#059669;"><?php echo number_format($totalNetPaid); ?> <span class="fs-6 fw-normal text-muted">تومان</span></h4>
            </div>
            <div class="rounded-3 p-3 text-emerald-600" style="background:#d1fae5; color:#059669;">
                <i class="fas fa-wallet fs-4"></i>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
    <div class="payslip-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fas fa-receipt text-indigo-600" style="color:#4f46e5;"></i>
            <span>لیست سوابق فیش حقوقی</span>
        </h6>
        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold" style="background:#e0e7ff; color:#4338ca;">
            تعداد: <?php echo $totalPayslips; ?>
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 70px;">#</th>
                        <th>دوره پرداخت (سال / ماه)</th>
                        <th class="text-center">خالص پرداختی</th>
                        <th class="text-center">تاریخ صدور</th>
                        <th class="text-center no-print" style="width: 140px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payslips)): ?>
                        <?php foreach ($payslips as $index => $payslip): ?>
                            <?php
                                $monthWord = function_exists('jdate_words') ? jdate_words(['mm' => $payslip->pay_period_month])['mm'] : $payslip->pay_period_month;
                            ?>
                            <tr>
                                <td class="text-center font-monospace text-muted"><?php echo $index + 1; ?></td>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 bg-light p-2 text-indigo-600 border" style="color:#4f46e5;">
                                            <i class="far fa-calendar-check"></i>
                                        </div>
                                        <span><?php echo sanitize($payslip->pay_period_year) . ' / ' . sanitize($monthWord); ?></span>
                                    </div>
                                </td>
                                <td class="text-center font-monospace fw-black text-emerald-600 fs-6" style="color:#059669;">
                                    <?php echo number_format($payslip->net_pay); ?> <span class="fs-7 fw-normal text-muted">تومان</span>
                                </td>
                                <td class="text-center font-monospace small text-muted">
                                    <?php echo function_exists('jdate') ? jdate('Y/m/d', strtotime($payslip->created_at)) : sanitize($payslip->created_at); ?>
                                </td>
                                <td class="text-center no-print">
                                    <a href="<?php echo APP_URL; ?>/index.php?page=employee&action=view_my_payslip&id=<?php echo $payslip->id; ?>" class="btn-action-view" target="_blank">
                                        <i class="fas fa-print"></i>
                                        <span>مشاهده و چاپ</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-file-invoice-dollar fs-2 mb-2 d-block opacity-40"></i>
                                هنوز هیچ فیش حقوقی برای شما صادر نشده است.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>