<?php
// app/views/client/my_invoices.php - نسخه اولترا پرمیوم
$invoices = $invoices ?? [];

// محاسبه مجموع پرداخت‌های معوق جهت نمایش در هدر
$totalUnpaid = 0;
$unpaidCount = 0;
foreach ($invoices as $inv) {
    if (in_array($inv->status ?? '', ['pending', 'overdue', 'partial'])) {
        $totalUnpaid += ($inv->total_amount ?? 0) - ($inv->paid_amount ?? 0);
        $unpaidCount++;
    }
}
?>

<style>
    .invoices-card-header {
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
        padding: 0.4rem 0.85rem;
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.2s ease;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .btn-action-view:hover {
        background: #4f46e5;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    .btn-action-pay {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 10px;
        padding: 0.4rem 0.85rem;
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.2s ease;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
    }

    .btn-action-pay:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">فاکتورها و صورتحساب‌های من</h1>
        <p class="text-muted small mb-0">مدیریت مالی، پیگیری بدهی‌ها و پرداخت آنلاین فاکتورهای صادرشده با درگاه شتاب</p>
    </div>

    <div class="d-flex align-items-center gap-2 no-print">
        <?php if ($totalUnpaid > 0): ?>
            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2 fw-bold font-monospace">
                <i class="fas fa-wallet me-1"></i> مجموع بدهی: <?php echo number_format($totalUnpaid); ?> تومان
            </span>
        <?php endif; ?>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
            <i class="fas fa-print"></i>
            <span>چاپ لیست</span>
        </button>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="invoices-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fas fa-receipt text-indigo-600" style="color: #4f46e5;"></i>
            <span>فهرست کل فاکتورهای رسمی و پیش‌فاکتورها</span>
        </h6>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold" style="background:#e0e7ff; color:#4338ca;">
                کل فاکتورها: <?php echo count($invoices); ?>
            </span>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">#</th>
                        <th>شماره فاکتور</th>
                        <th>قرارداد / بابت</th>
                        <th class="text-center">تاریخ صدور</th>
                        <th class="text-center">سررسید</th>
                        <th>مبلغ کل</th>
                        <th>پرداخت‌شده</th>
                        <th class="text-center">وضعیت</th>
                        <th class="text-center no-print" style="width: 170px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($invoices)): ?>
                        <?php foreach ($invoices as $invoice): ?>
                            <?php 
                                $totalAmt = (float)($invoice->total_amount ?? 0);
                                $paidAmt = (float)($invoice->paid_amount ?? 0);
                                $remainingAmt = $totalAmt - $paidAmt;
                                $status = $invoice->status ?? 'pending';

                                $statusBadge = 'bg-secondary';
                                $statusLabel = 'نامشخص';

                                if ($status === 'paid') {
                                    $statusBadge = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                    $statusLabel = 'تسویه شده';
                                } elseif ($status === 'pending') {
                                    $statusBadge = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                                    $statusLabel = 'در انتظار پرداخت';
                                } elseif ($status === 'partial') {
                                    $statusBadge = 'bg-info bg-opacity-10 text-info border border-info border-opacity-25';
                                    $statusLabel = 'پرداخت جزیی';
                                } elseif ($status === 'overdue') {
                                    $statusBadge = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                    $statusLabel = 'سررسید گذشته';
                                } elseif ($status === 'canceled') {
                                    $statusBadge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
                                    $statusLabel = 'باطل‌شده';
                                }
                            ?>
                            <tr>
                                <td class="text-center font-monospace text-muted">#<?php echo sanitize($invoice->id); ?></td>
                                <td class="fw-bold text-dark font-monospace">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 bg-light p-2 text-indigo-600 border" style="color: #4f46e5;">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <span><?php echo sanitize($invoice->invoice_number); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark small fw-bold">
                                        <?php echo sanitize($invoice->contract_title ?? $invoice->description ?? 'خدمات کلی'); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark font-monospace border">
                                        <?php echo sanitize($invoice->issue_date_jalali ?? '---'); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark font-monospace border">
                                        <?php echo sanitize($invoice->due_date_jalali ?? '---'); ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-dark font-monospace">
                                    <?php echo number_format($totalAmt); ?> <small class="text-muted font-normal" style="font-size: 0.75rem;">تومان</small>
                                </td>
                                <td class="fw-bold font-monospace text-success">
                                    <?php echo number_format($paidAmt); ?> <small class="text-muted font-normal" style="font-size: 0.75rem;">تومان</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold <?php echo $statusBadge; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=client&action=view_invoice&id=<?php echo $invoice->id; ?>" class="btn-action-view" title="مشاهده فاکتور">
                                            <i class="fas fa-eye"></i>
                                            <span>نمایش</span>
                                        </a>

                                        <?php if (!in_array($status, ['paid', 'canceled']) && $remainingAmt > 0): ?>
                                            <a href="<?php echo APP_URL; ?>/index.php?page=client&action=invoice_pay&id=<?php echo $invoice->id; ?>" class="btn-action-pay" title="پرداخت آنلاین فاکتور">
                                                <i class="fas fa-credit-card"></i>
                                                <span>پرداخت</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fs-2 mb-2 d-block opacity-40"></i>
                                هنوز هیچ فاکتور یا صورتحسابی برای حساب شما صادر نشده است.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>