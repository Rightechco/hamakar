<?php
// app/views/admin/inventory/receipts/purchase_receipt.php
$purchase = $purchase ?? null;
$items = $items ?? [];

// Helper badge status function
function getReceiptStatusBadge($status) {
    switch (strtolower($status)) {
        case 'completed':
        case 'paid':
        case 'تکمیل شده':
            return ['bg' => '#dcfce7', 'color' => '#15803d', 'label' => 'پرداخت شده / تکمیل'];
        case 'pending':
        case 'در انتظار':
            return ['bg' => '#fef3c7', 'color' => '#b45309', 'label' => 'در انتظار پرداخت'];
        case 'canceled':
        case 'لغو شده':
            return ['bg' => '#fee2e2', 'color' => '#b91c1c', 'label' => 'لغو شده'];
        default:
            return ['bg' => '#f1f5f9', 'color' => '#475569', 'label' => $status ?: 'تایید شده'];
    }
}

$statusBadge = getReceiptStatusBadge($purchase->status ?? '');
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --dark-slate: #0f172a;
        --border-color: #e2e8f0;
        --bg-light: #f8fafc;
    }

    .ultra-receipt-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        max-width: 900px;
        margin: 0 auto;
        direction: rtl;
    }

    .receipt-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.02);
        overflow: hidden;
    }

    .receipt-header-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        padding: 32px 40px;
        position: relative;
    }

    .receipt-header-banner::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        left: 0;
        height: 4px;
        background: linear-gradient(90deg, #6366f1 0%, #4f46e5 50%, #06b6d4 100%);
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 16px;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 600;
        gap: 6px;
    }

    .info-card-box {
        background: var(--bg-light);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 20px;
        height: 100%;
        transition: border-color 0.2s;
    }

    .info-card-box:hover {
        border-color: #cbd5e1;
    }

    .table-ultra-receipt {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-ultra-receipt th {
        background-color: var(--bg-light);
        color: #475569;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border-color);
    }

    .table-ultra-receipt td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 0.925rem;
    }

    .table-ultra-receipt tbody tr:last-child td {
        border-bottom: none;
    }

    .summary-box {
        background: var(--bg-light);
        border-radius: 14px;
        padding: 20px 24px;
        border: 1px solid var(--border-color);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 0.9rem;
        color: #64748b;
    }

    .summary-row.final-total {
        border-top: 2px dashed #cbd5e1;
        margin-top: 10px;
        padding-top: 14px;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--primary-indigo);
    }

    .signature-area {
        margin-top: 50px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    @media print {
        .no-print { display: none !important; }
        body { background: #fff !important; }
        .receipt-card { border: none !important; box-shadow: none !important; }
        .ultra-receipt-wrapper { max-width: 100% !important; margin: 0 !important; }
    }
</style>

<div class="ultra-receipt-wrapper py-4">
    <!-- Actions Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-medium">
            <i class="fas fa-arrow-right me-1"></i> بازگشت
        </a>
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 btn-sm fw-medium shadow-sm">
            <i class="fas fa-print me-1"></i> چاپ / دریافت PDF
        </button>
    </div>

    <!-- Main Receipt Document -->
    <div class="receipt-card">
        <!-- Top Banner Header -->
        <div class="receipt-header-banner">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-white bg-opacity-10 text-white fw-normal px-3 py-1 rounded-pill mb-2 small">فاکتور / رسید ورود به انبار</span>
                    <h3 class="fw-bold mb-1">رسید رسمی خرید</h3>
                    <p class="text-white-50 small mb-0">شماره پیگیری / رسید: <strong class="text-white">#<?php echo htmlspecialchars($purchase->id ?? '---'); ?></strong></p>
                </div>
                <div class="text-end">
                    <img src="<?php echo APP_URL; ?>/assets/img/company_logo.png" alt="لوگوی شرکت" class="img-fluid rounded-3 bg-white p-2" style="max-height: 70px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                </div>
            </div>
        </div>

        <div class="p-4 p-md-5">
            <!-- Information Grid -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="info-card-box">
                        <div class="d-flex align-items-center mb-3 text-primary">
                            <i class="fas fa-truck-loading me-2 fs-5"></i>
                            <h6 class="fw-bold mb-0 text-dark">مشخصات تامین‌کننده / فروشنده</h6>
                        </div>
                        <div class="small lh-lg">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">نام فروشنده:</span>
                                <strong class="text-dark"><?php echo htmlspecialchars($purchase->vendor_name ?? '---'); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">شماره تماس:</span>
                                <span class="text-dark dir-ltr"><?php echo htmlspecialchars($purchase->vendor_phone ?? '---'); ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">نشانی:</span>
                                <span class="text-dark text-truncate" style="max-width: 220px;"><?php echo htmlspecialchars($purchase->vendor_address ?? '---'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="info-card-box">
                        <div class="d-flex align-items-center mb-3 text-primary">
                            <i class="fas fa-file-invoice me-2 fs-5"></i>
                            <h6 class="fw-bold mb-0 text-dark">اطلاعات سند خرید</h6>
                        </div>
                        <div class="small lh-lg">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted">تاریخ ثبت خرید:</span>
                                <strong class="text-dark"><?php echo jdate('Y/m/d', strtotime($purchase->purchase_date ?? date('Y-m-d'))); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">وضعیت فاکتور:</span>
                                <span class="status-pill" style="background-color: <?php echo $statusBadge['bg']; ?>; color: <?php echo $statusBadge['color']; ?>;">
                                    <i class="fas fa-circle fs-6 opacity-75"></i> <?php echo htmlspecialchars($statusBadge['label']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="border rounded-4 overflow-hidden mb-4">
                <div class="table-responsive">
                    <table class="table-ultra-receipt align-middle">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>نام و شرح کالا</th>
                                <th class="text-center">تعداد / مقدار</th>
                                <th class="text-center">واحد سنجش</th>
                                <th class="text-end">قیمت واحد (تومان)</th>
                                <th class="text-end">قیمت کل (تومان)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $grandTotal = 0; $counter = 1; ?>
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $item): ?>
                                    <?php $itemTotal = $item->quantity * $item->price; $grandTotal += $itemTotal; ?>
                                    <tr>
                                        <td class="text-center text-muted small"><?php echo $counter++; ?></td>
                                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($item->product_name); ?></td>
                                        <td class="text-center fw-medium"><?php echo number_format($item->quantity); ?></td>
                                        <td class="text-center text-muted small"><?php echo htmlspecialchars($item->product_unit ?? 'عدد'); ?></td>
                                        <td class="text-end"><?php echo number_format($item->price); ?></td>
                                        <td class="text-end fw-bold text-dark"><?php echo number_format($itemTotal); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">هیچ آیتمی در این رسید ثبت نشده است.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total Calculation Section -->
            <div class="row justify-content-end">
                <div class="col-md-6 col-lg-5">
                    <div class="summary-box">
                        <div class="summary-row">
                            <span>جمع اقلام فاکتور:</span>
                            <span class="fw-bold text-dark"><?php echo number_format($grandTotal); ?> تومان</span>
                        </div>
                        <?php if (isset($purchase->vat_amount) && $purchase->vat_amount > 0): ?>
                            <div class="summary-row">
                                <span>مالیات بر ارزش افزوده:</span>
                                <span class="fw-bold text-dark"><?php echo number_format($purchase->vat_amount); ?> تومان</span>
                            </div>
                        <?php endif; ?>
                        <div class="summary-row final-total">
                            <span>مبلغ نهایی قابل پرداخت:</span>
                            <span><?php echo number_format($purchase->total_amount ?? $grandTotal); ?> تومان</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signatures Section -->
            <div class="row text-center signature-area">
                <div class="col-6">
                    <p class="small text-muted fw-medium mb-4">مهر و امضاء انباردار / خریدار</p>
                    <div style="border-bottom: 1px dashed #cbd5e1; width: 50%; margin: 0 auto;"></div>
                </div>
                <div class="col-6">
                    <p class="small text-muted fw-medium mb-4">مهر و امضاء تامین‌کننده / تحویل‌دهنده</p>
                    <div style="border-bottom: 1px dashed #cbd5e1; width: 50%; margin: 0 auto;"></div>
                </div>
            </div>
        </div>
    </div>
</div>