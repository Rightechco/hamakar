<?php
// app/views/employee/client_invoices.php - نسخه اولترا پرمیوم
$invoices = $invoices ?? [];

$totalInvoices = count($invoices);
$totalAmount = 0;
$paidAmount = 0;
$unpaidAmount = 0;

foreach ($invoices as $inv) {
    $amount = (float)($inv->total_amount ?? $inv->amount ?? 0);
    $status = $inv->status ?? '';
    
    $totalAmount += $amount;
    if ($status === 'paid') {
        $paidAmount += $amount;
    } else {
        $unpaidAmount += $amount;
    }
}
?>

<style>
    .invoice-card-header {
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
        padding: 0.45rem 0.9rem;
        font-size: 0.825rem;
        font-weight: 700;
        transition: all 0.2s ease;
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
        <h1 class="h3 fw-bold text-dark mb-1">فاکتورها و صورت‌حساب‌ها</h1>
        <p class="text-muted small mb-0">مشاهده کلیه فاکتورهای صادر شده، وضعیت پرداخت و گزارش مالی پروژه</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
            <i class="fas fa-print"></i>
            <span>چاپ گزارش</span>
        </button>
    </div>
</div>

<!-- کارت‌های خلاصه وضعیت مالی -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">مبلغ کل فاکتورها</span>
                <h4 class="fw-black text-dark font-monospace mb-0"><?php echo number_format($totalAmount); ?> <span class="fs-6 fw-normal text-muted">تومان</span></h4>
            </div>
            <div class="rounded-3 p-3 text-indigo-600" style="background:#e0e7ff; color:#4338ca;">
                <i class="fas fa-file-invoice-dollar fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">مجموع پرداخت‌شده</span>
                <h4 class="fw-black text-emerald-600 font-monospace mb-0" style="color:#059669;"><?php echo number_format($paidAmount); ?> <span class="fs-6 fw-normal text-muted">تومان</span></h4>
            </div>
            <div class="rounded-3 p-3 text-emerald-600" style="background:#d1fae5; color:#059669;">
                <i class="fas fa-circle-check fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">مانده بدهی / معوق</span>
                <h4 class="fw-black text-danger font-monospace mb-0"><?php echo number_format($unpaidAmount); ?> <span class="fs-6 fw-normal text-muted">تومان</span></h4>
            </div>
            <div class="rounded-3 p-3 text-danger" style="background:#ffe4e6; color:#e11d48;">
                <i class="fas fa-clock-rotate-left fs-4"></i>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="invoice-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fas fa-receipt text-indigo-600" style="color:#4f46e5;"></i>
            <span>لیست کامل فاکتورها</span>
        </h6>
        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold" style="background:#e0e7ff; color:#4338ca;">
            تعداد: <?php echo $totalInvoices; ?>
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80px;">شماره</th>
                        <th>عنوان / مشتری</th>
                        <th class="text-center">مبلغ کل</th>
                        <th class="text-center">تاریخ صدور</th>
                        <th class="text-center">سررسید</th>
                        <th class="text-center">وضعیت</th>
                        <th class="text-center no-print" style="width: 110px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($invoices)): ?>
                        <?php foreach ($invoices as $inv): ?>
                            <?php
                                $status = $inv->status ?? 'pending';
                                $statusBadge = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                                $statusLabel = 'در انتظار پرداخت';

                                if ($status === 'paid') {
                                    $statusBadge = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                    $statusLabel = 'پرداخت شده';
                                } elseif ($status === 'overdue') {
                                    $statusBadge = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                    $statusLabel = 'سررسید گذشته';
                                } elseif ($status === 'canceled') {
                                    $statusBadge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
                                    $statusLabel = 'لغو شده';
                                }
                            ?>
                            <tr>
                                <td class="text-center font-monospace text-muted">#<?php echo sanitize($inv->invoice_number ?? $inv->id); ?></td>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 bg-light p-2 text-indigo-600 border" style="color:#4f46e5;">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>
                                        <span><?php echo sanitize($inv->title ?? $inv->client_name ?? 'فاکتور خدمات'); ?></span>
                                    </div>
                                </td>
                                <td class="text-center font-monospace fw-bold text-dark">
                                    <?php echo number_format((float)($inv->total_amount ?? $inv->amount ?? 0)); ?> <span class="small text-muted fw-normal">تومان</span>
                                </td>
                                <td class="text-center font-monospace small text-muted">
                                    <?php echo sanitize($inv->issue_date_jalali ?? $inv->created_at ?? '---'); ?>
                                </td>
                                <td class="text-center font-monospace small text-muted">
                                    <?php echo sanitize($inv->due_date_jalali ?? $inv->due_date ?? '---'); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold <?php echo $statusBadge; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <a href="<?php echo APP_URL; ?>/index.php?page=employee&action=view_invoice&id=<?php echo $inv->id; ?>" class="btn-action-view">
                                        <i class="fas fa-eye"></i>
                                        <span>مشاهده</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-file-invoice-dollar fs-2 mb-2 d-block opacity-40"></i>
                                هنوز هیچ فاکتوری صادر نشده است.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>