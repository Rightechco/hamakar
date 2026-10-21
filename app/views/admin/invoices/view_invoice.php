<?php
// app/views/admin/invoices/view_invoice.php

$invoice = $invoice ?? null;
$items = $items ?? [];

$status_map = [
    'paid' => ['text' => 'پرداخت شده', 'bg' => '#dcfce7', 'color' => '#15803d'],
    'pending' => ['text' => 'در انتظار پرداخت', 'bg' => '#fef3c7', 'color' => '#b45309'],
    'partial' => ['text' => 'پرداخت جزئی', 'bg' => '#e0f2fe', 'color' => '#0369a1'],
    'overdue' => ['text' => 'سررسید گذشته', 'bg' => '#fee2e2', 'color' => '#b91c1c'],
    'canceled' => ['text' => 'لغو شده', 'bg' => '#f1f5f9', 'color' => '#475569'],
];
$current_status = $status_map[$invoice->status ?? ''] ?? ['text' => 'نامشخص', 'bg' => '#f1f5f9', 'color' => '#475569'];

if (!$invoice) {
    echo '<div class="alert alert-danger border-0 shadow-sm rounded-3">فاکتور مورد نظر یافت نشد.</div>';
    return;
}

$sellerInfo = [
    'name' => 'شرکت رایان تکرو',
    'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
    'address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه ۳',
    'economic_code' => '۱۴۰۰۹۱۵۶۳۲۱',
    'registration_number' => '۵۵۲۰۰',
    'logo_path' => APP_URL . '/assets/img/company_logo.png',
];

$issueDateJalali = !empty($invoice->issue_date) ? jdate('Y/m/d', strtotime($invoice->issue_date)) : '---';
$dueDateJalali = !empty($invoice->due_date) ? jdate('Y/m/d', strtotime($invoice->due_date)) : '---';

$subtotal = $invoice->subtotal ?? 0;
$vatRate = $invoice->vat_rate ?? 0;
$vatAmount = $invoice->vat_amount ?? 0;
$totalAmount = $invoice->total_amount ?? 0;
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
    }

    body, .view-invoice-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .invoice-card-ultra {
        max-width: 880px;
        margin: 0 auto;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        padding: 40px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 16px;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .info-box-ultra {
        background: #f8fafc;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #f1f5f9;
        height: 100%;
    }

    .table-ultra-invoice {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-ultra-invoice th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-ultra-invoice td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
    }

    @media print {
        .no-print { display: none !important; }
        body { background: #fff !important; }
        .invoice-card-ultra { border: none !important; box-shadow: none !important; width: 100% !important; max-width: 100% !important; padding: 0 !important; }
    }
</style>

<div class="view-invoice-wrapper py-3">
    <!-- Action Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print mx-auto" style="max-width: 880px;">
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices" class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-medium">
            <i class="fas fa-arrow-right me-1"></i> بازگشت
        </a>

        <div class="d-flex align-items-center gap-2">
            <a href="javascript:window.print()" class="btn btn-primary rounded-pill px-4 btn-sm fw-medium shadow-sm">
                <i class="fas fa-print me-1"></i> چاپ فاکتور
            </a>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=editInvoice&id=<?php echo htmlspecialchars($invoice->id); ?>" class="btn btn-warning rounded-pill px-4 btn-sm fw-medium text-dark">
                <i class="fas fa-edit me-1"></i> ویرایش
            </a>
        </div>
    </div>
    
    <?php FlashMessage::display(); ?>

    <!-- Printable Invoice Sheet -->
    <div class="invoice-card-ultra">
        <div class="d-flex justify-content-between align-items-center pb-4 mb-4 border-bottom">
            <div class="d-flex align-items-center gap-3">
                <img src="<?php echo htmlspecialchars($sellerInfo['logo_path'] ?? ''); ?>" alt="Logo" style="max-height: 55px;" class="rounded-2">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">فاکتور رسمی فروش</h4>
                    <span class="status-pill" style="background-color: <?php echo $current_status['bg']; ?>; color: <?php echo $current_status['color']; ?>;">
                        <?php echo htmlspecialchars($current_status['text']); ?>
                    </span>
                </div>
            </div>
            <div class="text-end text-muted small">
                <div class="mb-1">شماره فاکتور: <strong class="text-dark">#<?php echo htmlspecialchars($invoice->invoice_number); ?></strong></div>
                <div class="mb-1">تاریخ صدور: <strong class="text-dark"><?php echo htmlspecialchars($issueDateJalali); ?></strong></div>
                <div>تاریخ سررسید: <strong class="text-dark"><?php echo htmlspecialchars($dueDateJalali); ?></strong></div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="info-box-ultra">
                    <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-user-check me-2"></i>مشخصات خریدار</h6>
                    <div class="small lh-lg text-muted">
                        <div>نام مشتری: <strong class="text-dark"><?php echo htmlspecialchars($invoice->client_name ?? '---'); ?></strong></div>
                        <div>شماره تماس: <strong class="text-dark"><?php echo htmlspecialchars($invoice->client_phone ?? '---'); ?></strong></div>
                        <div>نشانی: <strong class="text-dark"><?php echo htmlspecialchars($invoice->client_address ?? '---'); ?></strong></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box-ultra">
                    <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-building me-2"></i>مشخصات فروشنده</h6>
                    <div class="small lh-lg text-muted">
                        <div>نام شرکت: <strong class="text-dark"><?php echo htmlspecialchars($sellerInfo['name']); ?></strong></div>
                        <div>کد اقتصادی: <strong class="text-dark"><?php echo htmlspecialchars($sellerInfo['economic_code']); ?></strong></div>
                        <div>تلفن: <strong class="text-dark"><?php echo htmlspecialchars($sellerInfo['phone']); ?></strong></div>
                        <div>نشانی: <strong class="text-dark"><?php echo htmlspecialchars($sellerInfo['address']); ?></strong></div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($invoice->description)): ?>
            <div class="p-3 bg-light rounded-3 mb-4 small text-muted">
                <strong class="text-dark me-1">توضیحات فاکتور:</strong> <?php echo htmlspecialchars($invoice->description); ?>
            </div>
        <?php endif; ?>

        <!-- Invoice Items Table -->
        <div class="table-responsive mb-4">
            <table class="table table-ultra-invoice align-middle">
                <thead>
                    <tr>
                        <th>شرح کالا / خدمت</th>
                        <th class="text-center">واحد</th>
                        <th class="text-center">مقدار / تعداد</th>
                        <th class="text-end">قیمت واحد (تومان)</th>
                        <th class="text-end">مبلغ کل (تومان)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (is_array($items) && !empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($item->product_name ?? $item->service_name ?? '---'); ?></td>
                                <td class="text-center text-muted small"><?php echo htmlspecialchars($item->product_unit ?? 'عدد'); ?></td>
                                <td class="text-center fw-medium"><?php echo number_format($item->quantity); ?></td>
                                <td class="text-end"><?php echo number_format($item->price); ?></td>
                                <td class="text-end fw-bold text-dark"><?php echo number_format($item->quantity * $item->price); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">هیچ آیتمی در این فاکتور یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Summary Calculation Box -->
        <div class="row justify-content-end">
            <div class="col-md-6">
                <div class="bg-light p-3 rounded-3">
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>جمع کل بدون مالیات:</span>
                        <span class="fw-medium text-dark"><?php echo number_format($subtotal); ?> تومان</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>مالیات بر ارزش افزوده (VAT <?php echo number_format($vatRate); ?>%):</span>
                        <span class="fw-medium text-dark"><?php echo number_format($vatAmount); ?> تومان</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2 text-primary fs-5">
                        <span>مبلغ قابل پرداخت:</span>
                        <span><?php echo number_format($totalAmount); ?> تومان</span>
                    </div>
                </div>
            </div>
        </div>

        <?php if (function_exists('numberToWords')): ?>
            <p class="text-center mt-3 small text-muted"><strong>مبلغ به حروف:</strong> <?php echo numberToWords($totalAmount); ?></p>
        <?php endif; ?>

        <!-- Signatures -->
        <div class="row text-center mt-5 pt-4 text-muted border-top">
            <div class="col-6">
                <p class="mb-4 small fw-medium">مهر و امضاء خریدار</p>
                <div style="border-bottom: 1px dashed #cbd5e1; width: 60%; margin: 0 auto;"></div>
            </div>
            <div class="col-6">
                <p class="mb-4 small fw-medium">مهر و امضاء فروشنده</p>
                <div style="border-bottom: 1px dashed #cbd5e1; width: 60%; margin: 0 auto;"></div>
            </div>
        </div>

        <footer class="text-center text-muted mt-4 small pt-3">
            <p class="mb-0">از اعتماد شما به شرکت رایان تکرو سپاسگزاریم.</p>
        </footer>
    </div>
    
    <!-- Status Update Panel -->
    <div class="ultra-card p-4 mt-4 no-print mx-auto" style="max-width: 880px;">
        <h6 class="fw-bold mb-3 text-dark"><i class="fas fa-sync-alt me-2 text-primary"></i>تغییر وضعیت فاکتور</h6>
        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices_update_status&id=<?php echo htmlspecialchars($invoice->id); ?>" method="POST">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="status" class="form-label small text-muted">انتخاب وضعیت جدید:</label>
                    <select class="form-select" id="status" name="status" required>
                        <?php foreach ($status_map as $key => $status_data): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($invoice->status == $key) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status_data['text']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">به‌روزرسانی وضعیت</button>
                </div>
            </div>
        </form>
    </div>
</div>