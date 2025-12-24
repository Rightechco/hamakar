<?php
// app/views/admin/invoices/view_invoice.php

// بارگذاری کلاس JalaliDate (اگرچه ممکن است در index.php باشد، برای اطمینان در ویوهای مهم قرار داده می‌شود)
// اگر این خط باعث خطای "Class already defined" شد، آن را حذف کنید.
// require_once __DIR__ . '/../../lib/JalaliDate.php';

// آماده‌سازی متغیرها برای استفاده در ویو
$invoice = $invoice ?? null;
$items = $items ?? [];

$status_map = [
    'paid' => ['text' => 'پرداخت شده', 'color' => 'success'],
    'pending' => ['text' => 'در انتظار پرداخت', 'color' => 'warning'],
    'partial' => ['text' => 'پرداخت جزئی', 'color' => 'info'],
    'overdue' => ['text' => 'سررسید گذشته', 'color' => 'danger'],
    'canceled' => ['text' => 'لغو شده', 'color' => 'secondary'],
];
$current_status = $status_map[$invoice->status] ?? ['text' => 'نامشخص', 'color' => 'secondary'];

if (!$invoice) {
    echo '<div class="alert alert-danger">فاکتور مورد نظر یافت نشد.</div>';
    return;
}

$sellerInfo = [
    'name' => 'شرکت رایان تکرو',
    'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
    'address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه ۳',
    'economic_code' => '۱۴۰۰۹۱۵۶۳۲۱',
    'registration_number' => '۵۵۲۰۰',
    'logo_path' => APP_URL . '/assets/img/company_logo.png', // فرض بر وجود مسیر لوگو
];

$issueDateJalali = $invoice->issue_date ? jdate('Y/m/d', strtotime($invoice->issue_date)) : '---';
$dueDateJalali = $invoice->due_date ? jdate('Y/m/d', strtotime($invoice->due_date)) : '---';

// بازیابی مبالغ از مدل (استفاده از مقادیر صفر پیش‌فرض برای جلوگیری از خطای PHP)
$subtotal = $invoice->subtotal ?? 0;
$vatRate = $invoice->vat_rate ?? 0;
$vatAmount = $invoice->vat_amount ?? 0;
$totalAmount = $invoice->total_amount ?? 0;
?>

<style>
    @font-face {
        font-family: 'B Homa';
        src: url('<?php echo APP_URL; ?>/assets/fonts/BHoma.eot');
        src: url('<?php echo APP_URL; ?>/assets/fonts/BHoma.eot?#iefix') format('embedded-opentype'),
        url('<?php echo APP_URL; ?>/assets/fonts/BHoma.woff2') format('woff2'),
        url('<?php echo APP_URL; ?>/assets/fonts/BHoma.woff') format('woff'),
        url('<?php echo APP_URL; ?>/assets/fonts/BHoma.ttf') format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    @import url('https://cdn.jsdelivr.net/font-face/vazir-fd/vazir-fd.css');
    body, h1, h2, h3, h4, h5, h6, p, a, span, table {
        font-family: 'Vazir FD', 'B Homa', Tahoma, sans-serif !important;
        color: #333;
    }
    .invoice-card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 20px;
    }
    .invoice-main-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .invoice-main-header .title {
        font-size: 1.5rem;
        font-weight: bold;
    }
    .invoice-meta {
        text-align: left;
    }
    .invoice-meta p {
        margin: 0;
        font-size: 0.9rem;
    }
    .invoice-split-box {
        display: flex;
        border: 1px solid #000;
        margin-bottom: 20px;
    }
    .box-left, .box-right {
        flex: 1;
        padding: 10px;
        position: relative;
    }
    .box-left {
        border-left: 1px solid #000;
        text-align: right;
    }
    .box-right {
        text-align: right;
    }
    .box-vertical-title {
        position: absolute;
        top: 50%;
        left: -15px;
        transform: rotate(90deg) translate(-50%, 0);
        transform-origin: 0 0;
        font-size: 14px;
        font-weight: bold;
        background-color: #000;
        color: #fff;
        padding: 5px 10px;
    }
    .box-title {
        font-size: 1rem;
        font-weight: bold;
        color: #000;
        margin-bottom: 5px;
        padding-right: 20px;
    }
    .box-content {
        font-size: 0.8rem;
    }
    .table-invoice {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }
    .table-invoice thead th {
        background-color: #000;
        color: #fff;
        font-weight: normal;
        padding: 8px;
        border: 1px solid #000;
    }
    .table-invoice tbody td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .table-invoice tfoot td {
        background-color: #f8f9fa;
        font-weight: bold;
        border: 1px solid #ddd;
        padding: 8px;
    }
    .invoice-summary-minimal {
        width: 40%;
        margin-right: auto;
    }
    .invoice-summary-minimal .summary-item {
        display: flex;
        justify-content: space-between;
    }
    .invoice-summary-minimal .summary-item.total {
        background-color: #f0f8ff;
        padding: 8px;
        font-size: 1.1rem;
    }
    .invoice-footer-minimal {
        margin-top: 30px;
        border-top: 1px dashed #ddd;
        padding-top: 20px;
    }
    .no-print {
        display: block;
    }
    @media print {
        @page {
            size: A5 landscape;
            margin: 0;
        }
        body {
            font-size: 10px !important;
            background-color: #fff !important;
            margin: 0;
        }
        .container {
            width: 100% !important;
            padding: 10mm !important;
        }
        .no-print {
            display: none !important;
        }
        .invoice-card {
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            border: none !important;
        }
        .invoice-header-minimal {
            flex-direction: row;
            border-bottom: 1px solid #000;
        }
        .invoice-header-minimal .logo {
            max-width: 80px;
        }
        .invoice-header-minimal .invoice-title {
            font-size: 1.5rem;
        }
        .invoice-split-box {
            border: 1px solid #000;
        }
        .box-title {
            font-size: 0.9rem;
        }
        .box-vertical-title {
            font-size: 12px;
            padding: 3px 8px;
        }
        .table-invoice th, .table-invoice td {
            font-size: 8px;
            padding: 4px;
        }
        .invoice-summary-minimal {
            width: 100%;
        }
    }
</style>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h1 class="h3 text-gray-800">مشاهده فاکتور</h1>
        <div class="d-flex gap-2">
            <a href="javascript:window.print()" class="btn btn-primary btn-sm">
                <i class="fas fa-print"></i> چاپ
            </a>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=editInvoice&id=<?php echo htmlspecialchars($invoice->id); ?>" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> ویرایش
            </a>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> بازگشت
            </a>
        </div>
    </div>
    
    <?php FlashMessage::display(); ?>

    <div class="invoice-card">
        <div class="invoice-main-header">
            <div class="d-flex align-items-center gap-3">
                <img src="<?php echo htmlspecialchars($sellerInfo['logo_path'] ?? ''); ?>" alt="Company Logo" class="logo">
                <h5 class="title">فاکتور فروش</h5>
            </div>
            <div class="invoice-meta text-left">
                <p>شماره: <?php echo htmlspecialchars($invoice->invoice_number); ?></p>
                <p>تاریخ: <?php echo htmlspecialchars($issueDateJalali); ?></p>
                <p>وضعیت: <span class="badge bg-<?php echo htmlspecialchars($current_status['color']); ?>"><?php echo htmlspecialchars($current_status['text']); ?></span></p>
            </div>
        </div>

        <div class="invoice-split-box">
            <div class="box-right">
                <h6 class="box-title">مشخصات خریدار</h6>
                <div class="box-content">
                    <p class="mb-1"><strong>نام:</strong> <?php echo htmlspecialchars($invoice->client_name ?? '---'); ?></p>
                    <p class="mb-1"><strong>تلفن:</strong> <?php echo htmlspecialchars($invoice->client_phone ?? '---'); ?></p>
                    <p class="mb-1"><strong>آدرس:</strong> <?php echo htmlspecialchars($invoice->client_address ?? '---'); ?></p>
                </div>
            </div>
            <div class="box-left">
                <h6 class="box-title">مشخصات فروشنده</h6>
                <div class="box-content">
                    <p class="mb-1"><strong>نام:</strong> <?php echo htmlspecialchars($sellerInfo['name']); ?></p>
                    <p class="mb-1"><strong>تلفن:</strong> <?php echo htmlspecialchars($sellerInfo['phone']); ?></p>
                    <p class="mb-1"><strong>آدرس:</strong> <?php echo htmlspecialchars($sellerInfo['address']); ?></p>
                </div>
                <div class="box-vertical-title">فروشنده</div>
            </div>
        </div>
        
        <p class="mb-4"><strong>توضیحات فاکتور:</strong> <?php echo htmlspecialchars($invoice->description ?? '---'); ?></p>

        <div class="table-responsive mt-4">
    <table class="table table-bordered table-hover table-invoice">
        <thead class="bg-light">
            <tr>
                <th>شرح</th>
                <th>واحد</th>
                <th>مقدار</th>
                <th>مبلغ واحد (تومان)</th>
                <th>مبلغ کل (تومان)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (is_array($items) && !empty($items)): ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item->product_name ?? $item->service_name ?? '---'); ?></td>
                        <td><?php echo htmlspecialchars($item->product_unit ?? '---'); ?></td>
                        <td><?php echo number_format($item->quantity); ?></td>
                        <td><?php echo number_format($item->price); ?></td>
                        <td><?php echo number_format($item->quantity * $item->price); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">هیچ آیتمی در این فاکتور یافت نشد.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

        <div class="row justify-content-end">
            <div class="col-md-6">
                <div class="invoice-total-summary">
                    <div class="summary-item">
                        <span class="summary-label">جمع کل (بدون مالیات):</span>
                        <span class="summary-value"><?php echo number_format($subtotal); ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">مالیات بر ارزش افزوده (VAT) (<?php echo number_format($vatRate); ?>%):</span>
                        <span class="summary-value"><?php echo number_format($vatAmount); ?></span>
                    </div>
                    <div class="total-summary">
                        <div class="total-label">مبلغ قابل پرداخت:</div>
                        <div class="total-value"><?php echo number_format($totalAmount); ?> تومان</div>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-center mt-3" style="font-size: 0.9rem;"><strong>مبلغ به حروف:</strong> <?php echo numberToWords($totalAmount); ?></p>
        
        <div class="row invoice-footer-minimal">
            <div class="col-6 text-center">
                <p><strong>امضاء خریدار</strong></p>
            </div>
            <div class="col-6 text-center">
                <p><strong>امضاء فروشنده</strong></p>
            </div>
        </div>

        <footer class="text-center text-muted mt-4" style="font-size: 0.8rem;">
            <p>از اعتماد شما به شرکت رایان تکرو سپاسگزاریم.</p>
        </footer>
    </div>
    
    <div class="card p-4 mt-4 no-print">
        <h5 class="mb-3">تغییر وضعیت فاکتور</h5>
        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices_update_status&id=<?php echo htmlspecialchars($invoice->id); ?>" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">وضعیت جدید:</label>
                    <select class="form-select" id="status" name="status" required>
                        <?php foreach ($status_map as $key => $status_data): ?>
                            <option value="<?php echo $key; ?>" <?php echo ($invoice->status == $key) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status_data['text']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end mb-3">
                    <button type="submit" class="btn btn-primary">به‌روزرسانی وضعیت</button>
                </div>
            </div>
        </form>
    </div>

</div>