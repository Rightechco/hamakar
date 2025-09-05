<?php
// app/views/admin/invoices/view_invoice.php
// نسخه نهایی با طراحی حرفه‌ای

// آماده‌سازی متغیرها برای استفاده در ویو
$status_map = [
    'paid' => ['text' => 'پرداخت شده', 'color' => '#198754'],
    'pending' => ['text' => 'در انتظار پرداخت', 'color' => '#ffc107'],
    'overdue' => ['text' => 'سررسید گذشته', 'color' => '#dc3545'],
    'canceled' => ['text' => 'لغو شده', 'color' => '#6c757d'],
];
$current_status = $status_map[$invoice->status] ?? ['text' => sanitize($invoice->status), 'color' => '#6c757d'];

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاکتور شماره <?php echo sanitize($invoice->invoice_number); ?></title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-gray: #f8f9fa;
            --border-color: #dee2e6;
            --font-family-sans-serif: 'Vazirmatn', sans-serif;
            --body-color: #212529;
        }
        body {
            font-family: var(--font-family-sans-serif);
            background-color: #e9ecef;
            color: var(--body-color);
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .invoice-wrapper {
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 35px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        .invoice-header {
            padding: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            background: var(--light-gray);
            border-bottom: 3px solid var(--primary-color);
        }
        .invoice-header .logo { max-width: 100px; }
        .invoice-header .title-section { text-align: left; }
        .invoice-header .title-section h1 { margin: 0; font-size: 42px; color: #343a40; }
        .invoice-header .title-section .invoice-status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            color: #fff;
            background-color: <?php echo $current_status['color']; ?>;
            margin-top: 10px;
        }
        .invoice-details { padding: 40px; display: flex; justify-content: space-between; }
        .invoice-details .info-box { line-height: 1.8; }
        .invoice-details .info-box h4 { margin-top: 0; font-size: 16px; color: var(--secondary-color); }
        .invoice-details .info-box strong { color: var(--body-color); }
        .invoice-items { padding: 0 40px; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table thead { background-color: #212529; color: #fff; }
        .items-table th { padding: 15px; text-align: right; font-weight: 600; }
        .items-table tbody tr:nth-child(even) { background-color: var(--light-gray); }
        .items-table td { padding: 15px; border-bottom: 1px solid var(--border-color); }
        .invoice-summary { padding: 40px; }
        .invoice-summary .totals { float: left; width: 45%; }
        .totals table { width: 100%; }
        .totals td { padding: 10px 0; }
        .totals .label { color: var(--secondary-color); }
        .totals .grand-total { border-top: 2px solid var(--primary-color); padding-top: 15px; margin-top: 10px; }
        .totals .grand-total .label, .totals .grand-total .value { font-size: 20px; font-weight: bold; color: var(--primary-color); }
        .invoice-footer { padding: 40px; text-align: center; color: var(--secondary-color); font-size: 12px; }
        .actions-bar { padding: 20px 40px; background-color: var(--light-gray); text-align: center; }
        .actions-bar .btn {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            font-size: 16px;
            font-family: var(--font-family-sans-serif);
            border: none;
            cursor: pointer;
            margin: 0 5px;
        }
        .btn-print { background-color: var(--secondary-color); }
        .btn-pay { background-color: var(--success-color); }

        @media print {
            body { background: #fff; padding: 0; }
            .invoice-wrapper { margin: 0; box-shadow: none; border-radius: 0; }
            .actions-bar { display: none; }
        }
    </style>
</head>
<body>
    <div class="invoice-wrapper">
        <div class="actions-bar">
            <button class="btn btn-print" onclick="window.print()">چاپ فاکتور</button>
            <?php if (($invoice->status == 'pending' || $invoice->status == 'overdue') && isset($is_client_view)): ?>
                <a href="<?php echo APP_URL; ?>/index.php?page=payment&action=request&invoice_id=<?php echo $invoice->id; ?>" class="btn btn-pay">پرداخت آنلاین</a>
            <?php endif; ?>
        </div>

        <header class="invoice-header">
            <img src="<?php echo sanitize($companyInfo['logo_path']); ?>" alt="لوگوی شرکت" class="logo">
            <div class="title-section">
                <h1>فاکتور</h1>
                <span class="invoice-status"><?php echo $current_status['text']; ?></span>
            </div>
        </header>

        <section class="invoice-details">
            <div class="info-box">
                <h4>از طرف:</h4>
                <p><strong><?php echo sanitize($companyInfo['name']); ?></strong></p>
                <p><?php echo sanitize($companyInfo['address']); ?></p>
                <p>تلفن: <?php echo sanitize($companyInfo['phone']); ?></p>
            </div>
            <div class="info-box" style="text-align: left;">
                <h4>برای:</h4>
    <p><strong><?php echo sanitize($invoice->client_name ?? 'نامشخص'); ?></strong></p>
    <p><?php echo sanitize($invoice->client_address ?? 'آدرس ثبت نشده است'); ?></p>
    <p>تلفن: <?php echo sanitize($invoice->client_phone ?? '-'); ?></p>
</div>
            <div class="info-box" style="text-align: left;">
                <h4>جزئیات فاکتور:</h4>
                <p>شماره: <strong>#<?php echo sanitize($invoice->invoice_number); ?></strong></p>
<p class="mb-0"><strong>تاریخ صدور:</strong> <?php echo sanitize($invoice->issue_date_jalali); ?></p>
<p class="mb-0"><strong>تاریخ سررسید:</strong> <?php echo sanitize($invoice->due_date_jalali); ?></p>
            </div>
        </section>

        <section class="invoice-items">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th>شرح خدمات</th>
                        <th style="width: 15%;">تعداد</th>
                        <th style="width: 20%;">مبلغ (تومان)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>۱</td>
                        <td>
                            <strong><?php echo sanitize($invoice->contract_title ?? 'خدمات عمومی'); ?></strong>
                            <p style="font-size: 12px; color: #6c757d;"><?php echo nl2br(sanitize($invoice->description)); ?></p>
                        </td>
                        <td>۱</td>
                        <td><?php echo number_format($invoice->total_amount); ?></td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="invoice-summary">
            <div class="totals">
                <table>
                    <tr>
                        <td class="label">جمع جزء:</td>
                        <td class="value" style="text-align: left;"><?php echo number_format($invoice->total_amount); ?></td>
                    </tr>
                    <tr>
                        <td class="label">مالیات (۰٪):</td>
                        <td class="value" style="text-align: left;">۰</td>
                    </tr>
                    <tr class="grand-total">
                        <td class="label">مبلغ قابل پرداخت:</td>
                        <td class="value" style="text-align: left;"><?php echo number_format($invoice->total_amount); ?> تومان</td>
                    </tr>
                </table>
            </div>
            <div style="clear: both;"></div>
        </section>

        <footer class="invoice-footer">
            <p>از اعتماد شما به شرکت رایان تکرو سپاسگزاریم.</p>
        </footer>
    </div>
</body>
</html>