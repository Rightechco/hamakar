<?php
// app/views/admin/moadian/print.php - چاپ استاندارد صورتحساب الکترونیکی سازمان امور مالیاتی کشور
$invoice = $invoice ?? null;
$settings = $settings ?? null;
$packet = $packet ?? null;

$header = $packet['header'] ?? [];
$body = $packet['body'] ?? [];
$payments = $packet['payments'] ?? [];
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'صورتحساب رسمی الکترونیکی مودیان'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.003/Vazirmatn-font-face.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Vazirmatn', -apple-system, sans-serif;
        }
        body {
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 20px;
            font-size: 11px;
        }
        .invoice-paper {
            max-width: 900px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #0f172a;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .invoice-header-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .tax-title {
            text-align: center;
        }
        .tax-title h3 {
            margin: 0 0 4px 0;
            font-size: 16px;
            font-weight: 800;
        }
        .tax-title h4 {
            margin: 0;
            font-size: 13px;
            color: #475569;
        }
        .meta-box {
            font-size: 10px;
            line-height: 1.6;
        }
        .taxid-bar {
            background: #f8fafc;
            border: 1px dashed #64748b;
            padding: 6px 10px;
            font-family: monospace;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            text-align: center;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .section-box {
            border: 1px solid #0f172a;
            margin-bottom: 10px;
        }
        .section-title {
            background: #0f172a;
            color: #ffffff;
            padding: 3px 8px;
            font-weight: bold;
            font-size: 10px;
        }
        .section-content {
            padding: 8px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
        }
        .info-cell {
            font-size: 10px;
        }
        .info-cell strong {
            color: #475569;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.items-table th, table.items-table td {
            border: 1px solid #0f172a;
            padding: 5px 4px;
            text-align: center;
            font-size: 9.5px;
        }
        table.items-table th {
            background: #e2e8f0;
            font-weight: bold;
        }
        .totals-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            border: 1px solid #0f172a;
            margin-bottom: 10px;
        }
        .totals-right {
            padding: 8px;
            border-left: 1px solid #0f172a;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .totals-left {
            padding: 8px;
            line-height: 1.8;
            font-size: 10px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            text-align: center;
            margin-top: 15px;
            padding-top: 25px;
            font-size: 11px;
            font-weight: bold;
        }
        .print-btn-bar {
            max-width: 900px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .print-btn-bar {
                display: none !important;
            }
            .invoice-paper {
                box-shadow: none;
                border: 2px solid #000;
                margin: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <div>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=moadian_invoices" style="text-decoration:none; color:#475569; font-weight:bold;">
                <i class="fas fa-arrow-right me-1"></i> بازگشت به کارپوشه
            </a>
        </div>
        <div style="display:flex; gap:10px;">
            <button onclick="window.print()" style="background:#4f46e5; color:#fff; border:none; padding:8px 18px; border-radius:8px; cursor:pointer; font-weight:bold;">
                <i class="fas fa-print me-1"></i> چاپ فاکتور رسمی
            </button>
        </div>
    </div>

    <div class="invoice-paper">
        <!-- Top Header -->
        <div class="invoice-header-box">
            <div style="width:140px; text-align:center;">
                <i class="fas fa-balance-scale" style="font-size:32px; color:#0f172a;"></i>
                <div style="font-size:8px; font-weight:bold; margin-top:4px;">سازمان امور مالیاتی کشور</div>
            </div>
            <div class="tax-title">
                <h3>صورتحساب الکترونیکی فروش کالا و خدمات</h3>
                <h4>(الگوی <?php echo (int)($invoice->pattern_type ?? 1); ?> - موضوع: <?php echo ($invoice->subject_type === 'revocation' ? 'ابطالی' : ($invoice->subject_type === 'correction' ? 'اصلاحی' : 'اصلی')); ?>)</h4>
            </div>
            <div class="meta-box" style="width:180px;">
                <div><strong>شماره صورتحساب:</strong> <?php echo htmlspecialchars($invoice->inno); ?></div>
                <div><strong>تاریخ و ساعت صدور:</strong> <?php echo date('Y/m/d H:i', (int)($invoice->indatim / 1000)); ?></div>
                <div><strong>کد رهگیری کارپوشه:</strong> <?php echo htmlspecialchars($invoice->uid ?: 'در صف استعلام'); ?></div>
            </div>
        </div>

        <!-- TaxID Bar -->
        <div class="taxid-bar">
            <span>شماره منحصر به فرد مالیاتی (TaxID):</span>
            <span style="font-size:14px; letter-spacing:1.5px;"><?php echo htmlspecialchars($invoice->taxid); ?></span>
            <span>الگوریتم ISO Mod 11</span>
        </div>

        <!-- Seller Info -->
        <div class="section-box">
            <div class="section-title">مشخصات فروشنده</div>
            <div class="section-content">
                <div class="info-cell"><strong>نام شخص حقیقی/حقوقی:</strong> <?php echo htmlspecialchars($settings->taxpayer_name ?? 'سامانه یکپارچه هماکار'); ?></div>
                <div class="info-cell"><strong>شناسه ملی:</strong> <?php echo htmlspecialchars($settings->national_id ?? '14009156321'); ?></div>
                <div class="info-cell"><strong>شماره اقتصادی:</strong> <?php echo htmlspecialchars($settings->economic_code ?? '14009156321'); ?></div>
                <div class="info-cell"><strong>شناسه حافظه مالیاتی:</strong> <?php echo htmlspecialchars($settings->memory_id ?? 'A100HK'); ?></div>
                <div class="info-cell" style="grid-column: span 3;"><strong>نشانی:</strong> تبریز، بلوار آزادی، جنب شهرداری منطقه ۳، پلاک ۴۱</div>
                <div class="info-cell"><strong>تلفن:</strong> ۰۴۱۳۴۴۰۱۱۷۹</div>
            </div>
        </div>

        <!-- Buyer Info -->
        <div class="section-box">
            <div class="section-title">مشخصات خریدار</div>
            <div class="section-content">
                <div class="info-cell"><strong>نام شخص حقیقی/حقوقی:</strong> <?php echo htmlspecialchars($invoice->client_name ?? 'مشتری آزاد'); ?></div>
                <div class="info-cell"><strong>شناسه / شماره ملی:</strong> <?php echo htmlspecialchars($invoice->customer_national_id ?: '---'); ?></div>
                <div class="info-cell"><strong>شماره اقتصادی خریدار:</strong> <?php echo htmlspecialchars($invoice->customer_economic_code ?: '---'); ?></div>
                <div class="info-cell"><strong>شماره تماس:</strong> <?php echo htmlspecialchars($invoice->client_phone ?: '---'); ?></div>
                <div class="info-cell" style="grid-column: span 4;"><strong>نشانی کامل:</strong> <?php echo htmlspecialchars($invoice->client_address ?: 'ثبت شده در سامانه'); ?></div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:30px;">ردیف</th>
                    <th style="width:110px;">شناسه کالا / خدمت</th>
                    <th>شرح کالا یا خدمت</th>
                    <th style="width:40px;">تعداد</th>
                    <th style="width:50px;">واحد</th>
                    <th style="width:90px;">مبلغ واحد (تومان)</th>
                    <th style="width:70px;">تخفیف</th>
                    <th style="width:90px;">مبلغ پس از تخفیف</th>
                    <th style="width:50px;">نرخ مالیات</th>
                    <th style="width:80px;">مبلغ مالیات (۱۰٪)</th>
                    <th style="width:100px;">جمع کل (تومان)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($body)): ?>
                    <?php foreach ($body as $idx => $item): ?>
                        <tr>
                            <td><?php echo $idx + 1; ?></td>
                            <td style="font-family:monospace;"><?php echo htmlspecialchars($item['sstid']); ?></td>
                            <td style="text-align:right; padding-right:6px;"><?php echo htmlspecialchars($item['sstt']); ?></td>
                            <td><?php echo number_format($item['am']); ?></td>
                            <td>عدد/بسته</td>
                            <td><?php echo number_format($item['fee']); ?></td>
                            <td><?php echo number_format($item['dis'] ?? 0); ?></td>
                            <td><?php echo number_format($item['adis']); ?></td>
                            <td><?php echo $item['vra']; ?>%</td>
                            <td><?php echo number_format($item['vam']); ?></td>
                            <td style="font-weight:bold;"><?php echo number_format($item['tsstam']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td>۱</td>
                        <td style="font-family:monospace;">2710000138241</td>
                        <td style="text-align:right; padding-right:6px;">کالا و خدمات مندرج در قرارداد</td>
                        <td>۱</td>
                        <td>بسته</td>
                        <td><?php echo number_format($invoice->total_amount); ?></td>
                        <td>۰</td>
                        <td><?php echo number_format($invoice->total_amount); ?></td>
                        <td>۱۰٪</td>
                        <td><?php echo number_format($invoice->total_vat); ?></td>
                        <td style="font-weight:bold;"><?php echo number_format($invoice->final_amount); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Totals Grid -->
        <div class="totals-grid">
            <div class="totals-right">
                <div>
                    <strong>مبلغ کل به حروف:</strong>
                    <span style="font-size:11px; font-weight:bold;">
                        <?php echo number_format($invoice->final_amount); ?> تومان
                    </span>
                </div>
                <div style="font-size:9.5px; color:#475569; margin-top:8px;">
                    روش تسویه: نقدی | این صورتحساب بر اساس ماده ۱ قانون پایانه‌های فروشگاهی و سامانه مودیان به‌صورت معتبر صادر شده است.
                </div>
            </div>
            <div class="totals-left">
                <div class="totals-row">
                    <span>مجموع ناخالص:</span>
                    <span><?php echo number_format($invoice->total_amount); ?> تومان</span>
                </div>
                <div class="totals-row">
                    <span>تخفیف کلی:</span>
                    <span>۰ تومان</span>
                </div>
                <div class="totals-row">
                    <span>مالیات بر ارزش افزوده (۱۰٪):</span>
                    <span><?php echo number_format($invoice->total_vat); ?> تومان</span>
                </div>
                <div class="totals-row" style="border-top:1px solid #0f172a; font-weight:bold; font-size:11px; margin-top:4px; padding-top:4px;">
                    <span>مبلغ قابل پرداخت نهایی:</span>
                    <span><?php echo number_format($invoice->final_amount); ?> تومان</span>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div>مهر و امضای فروشنده</div>
            <div>مهر و امضای خریدار</div>
        </div>
    </div>

</body>
</html>
