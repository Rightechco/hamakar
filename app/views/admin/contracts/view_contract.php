<?php
// app/views/admin/contracts/view_contract.php
// برگه رسمی اولترا پرمیوم قرارداد به همراه خلاصه مالی، مالیات و چاپ PDF

$companyName = $companyInfo['name'] ?? 'شرکت رایان تکرو';
$customerName = $customer->name ?? 'نامشخص';
$contractTitle = $contract->title ?? 'قرارداد خدمات';

$totalAmount = (float)($contract->total_amount ?? 0);
$prepaymentAmount = (float)($contract->prepayment_amount ?? 0);
$remainingAmount = $totalAmount - $prepaymentAmount;
$vatRate = (float)($contract->vat_rate ?? 0);

$startDate = !empty($contract->start_date) ? jdate('Y/m/d', strtotime($contract->start_date)) : '---';
$endDate = !empty($contract->end_date) ? jdate('Y/m/d', strtotime($contract->end_date)) : 'نامحدود';
$finalDueDate = !empty($contract->final_payment_due_date) ? jdate('Y/m/d', strtotime($contract->final_payment_due_date)) : 'طبق توافق';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "برگه رسمی قرارداد - " . sanitize($contractTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        :root {
            --brand-color: #0f172a;
            --accent-color: #3730a3;
            --border-color: #cbd5e1;
        }

        body { 
            font-family: 'Vazirmatn', sans-serif; 
            background-color: #f8fafc; 
            color: #1e293b; 
            line-height: 2.1; 
            font-size: 13.5px; 
            margin: 0; 
            padding: 30px 0;
            -webkit-print-color-adjust: exact;
        }

        .action-bar {
            max-width: 850px;
            margin: 0 auto 20px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
        }

        .btn-print {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            color: white;
            border: none;
            padding: 11px 26px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Vazirmatn';
            transition: all 0.2s;
        }
        .btn-print:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(79, 70, 229, 0.4); }

        .contract-page { 
            max-width: 850px; 
            margin: 0 auto; 
            padding: 55px 65px; 
            background-color: #ffffff; 
            border-radius: 24px; 
            box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05); 
            border: 1px solid var(--border-color);
        }

        .contract-header { 
            text-align: center; 
            border-bottom: 2px solid #f1f5f9; 
            padding-bottom: 20px; 
            margin-bottom: 25px; 
        }
        .contract-header h1 { font-size: 17px; color: #64748b; margin: 0 0 6px 0; letter-spacing: 2px; }
        .contract-header h2 { font-size: 21px; color: var(--brand-color); margin: 0; font-weight: 800; }

        .financial-summary-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 25px; 
            font-size: 12px; 
        }
        .financial-summary-table th, .financial-summary-table td { 
            border: 1px solid #cbd5e1; 
            padding: 9px 10px; 
            text-align: center; 
        }
        .financial-summary-table th { 
            background-color: #f1f5f9; 
            color: #334155; 
            font-weight: 800; 
        }

        .parties-section { 
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px; 
            margin-bottom: 25px;
        }
        .party-card { 
            background-color: #f8fafc; 
            padding: 16px; 
            border-radius: 14px; 
            border: 1px solid #e2e8f0; 
        }
        .party-card h4 { 
            margin: 0 0 8px 0; 
            color: var(--accent-color); 
            font-size: 13.5px;
            font-weight: 800;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 6px;
        }
        .party-card p { margin: 4px 0; font-size: 12.5px; color: #334155; }

        .clause-item { margin-bottom: 20px; }
        .clause-title { 
            font-size: 14.5px; 
            font-weight: 800; 
            color: var(--brand-color); 
            margin-bottom: 5px; 
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .clause-text { text-align: justify; color: #334155; margin: 0; padding-right: 12px; }

        .signature-section { 
            margin-top: 50px; 
            display: flex; 
            justify-content: space-between; 
            padding-top: 25px; 
            border-top: 2px dashed #cbd5e1; 
        }
        .signature-box { width: 45%; text-align: center; }
        .signature-box span { font-weight: 800; color: var(--brand-color); display: block; margin-bottom: 65px; }

        @media print { 
            body { background: none; padding: 0; } 
            .contract-page { margin: 0; padding: 0; box-shadow: none; border-radius: 0; border: none; } 
            .action-bar { display: none !important; } 
        }
    </style>
</head>
<body>

    <div class="action-bar">
        <span style="font-weight: 700; color: #64748b; font-size: 0.9rem;"><i class="fas fa-shield-alt text-indigo-600"></i> سند رسمی قرارداد حقوقی</span>
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i>
            <span>چاپ و دریافت نسخه PDF</span>
        </button>
    </div>

    <div class="contract-page">
        <header class="contract-header">
            <h1>بسمه تعالی</h1>
            <h2>قرارداد <?php echo sanitize($contractTitle); ?></h2>
        </header>

        <!-- جدول خلاصه وضعیت مالی و زمان‌بندی -->
        <table class="financial-summary-table">
            <thead>
                <tr>
                    <th>تاریخ شروع</th>
                    <th>تاریخ پایان</th>
                    <th>مبلغ کل</th>
                    <th>پیش‌پرداخت</th>
                    <th>مبلغ باقی‌مانده</th>
                    <th>مالیات</th>
                    <th>سررسید تسویه</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong><?php echo $startDate; ?></strong></td>
                    <td><strong><?php echo $endDate; ?></strong></td>
                    <td><strong><?php echo number_format($totalAmount); ?> تومان</strong></td>
                    <td><strong><?php echo number_format($prepaymentAmount); ?> تومان</strong></td>
                    <td><strong style="color: #3730a3;"><?php echo number_format($remainingAmount); ?> تومان</strong></td>
                    <td><strong><?php echo $vatRate; ?>٪</strong></td>
                    <td><strong><?php echo $finalDueDate; ?></strong></td>
                </tr>
            </tbody>
        </table>

        <!-- مشخصات طرفین -->
        <section class="parties-section">
            <div class="party-card">
                <h4>طرف اول (مجری):</h4>
                <p>شرکت: <strong><?php echo sanitize($companyName); ?></strong></p>
                <p>نشانی: <?php echo sanitize($companyInfo['address'] ?? 'ثبت نشده'); ?></p>
                <p>تلفن: <?php echo sanitize($companyInfo['phone'] ?? 'ثبت نشده'); ?></p>
            </div>
            <div class="party-card">
                <h4>طرف دوم (کارفرما):</h4>
                <p>آقا / خانم / شرکت: <strong><?php echo sanitize($customerName); ?></strong></p>
                <p>نشانی: <?php echo sanitize($customer->address ?? 'ثبت نشده'); ?></p>
                <p>تلفن: <?php echo sanitize($customer->phone ?? 'ثبت نشده'); ?></p>
            </div>
        </section>

        <p style="text-align: justify; margin-bottom: 22px; font-weight: 500; font-size: 13px;">
            این قرارداد بر اساس ماده ۱۰ قانون مدنی و قوانین جاری کشور در تاریخ <strong><?php echo $startDate; ?></strong> فی‌مابین طرفین فوق منعقد گردیده و کلیه مفاد و مواد ذیل برای طرفین لازم‌الاجرا می‌باشد.
        </p>

        <!-- مواد و بندهای داینامیک ثبت‌شده -->
        <section class="clauses-section">
            <?php if (!empty($clauses)): ?>
                <?php foreach ($clauses as $clause): ?>
                    <div class="clause-item">
                        <div class="clause-title">
                            <i class="fas fa-bookmark" style="color:#3730a3; font-size:11px;"></i>
                            <span><?php echo sanitize($clause->title); ?></span>
                        </div>
                        <p class="clause-text"><?php echo nl2br(sanitize($clause->content)); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($contract->description)): ?>
                <div class="clause-item">
                    <div class="clause-title">
                        <i class="fas fa-info-circle" style="color:#3730a3; font-size:11px;"></i>
                        <span>توضیحات و شرایط اختصاصی متمم:</span>
                    </div>
                    <p class="clause-text"><?php echo nl2br(sanitize($contract->description)); ?></p>
                </div>
            <?php endif; ?>
        </section>

        <!-- محل امضا -->
        <section class="signature-section">
            <div class="signature-box">
                <span>مهر و امضای طرف اول (مجری)</span>
                <p>شرکت <?php echo sanitize($companyName); ?></p>
            </div>
            <div class="signature-box">
                <span>امضای طرف دوم (کارفرما)</span>
                <p><?php echo sanitize($customerName); ?></p>
            </div>
        </section>
    </div>

</body>
</html>