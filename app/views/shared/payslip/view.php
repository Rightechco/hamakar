<?php // فایل: app/views/shared/payslip/view.php - نسخه حرفه‌ای و رسپانسیو ?>

<style>
    :root {
        --payslip-font: 'Vazirmatn', sans-serif;
        --payslip-primary-color: #3a3f51;
        --payslip-secondary-color: #4e73df;
        --payslip-border-color: #e3e6f0;
        --payslip-bg-light: #f8f9fc;
    }
    body {
        background-color: #f1f1f1; /* رنگ پس زمینه بیرون از فیش */
    }
    .payslip-container {
        font-family: var(--payslip-font);
        max-width: 900px;
        margin: 30px auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        overflow: hidden; /* برای حفظ border-radius در هدر */
    }
    .payslip-header {
        background-color: var(--payslip-primary-color);
        color: #fff;
        padding: 20px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .payslip-header h3 { margin: 0; font-weight: 700; }
    .payslip-header .logo { max-height: 50px; filter: brightness(0) invert(1); }
    .payslip-body { padding: 30px; }
    .employee-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        background-color: var(--payslip-bg-light);
        border: 1px solid var(--payslip-border-color);
        padding: 20px;
        border-radius: 6px;
        margin-bottom: 30px;
    }
    .detail-item { font-size: 14px; }
    .detail-item strong { color: var(--payslip-primary-color); }
    .breakdown-section {
        border-top: 2px solid var(--payslip-border-color);
        padding-top: 20px;
    }
    .breakdown-table { font-size: 14px; }
    .breakdown-table th {
        background-color: var(--payslip-bg-light);
        font-weight: 600;
        padding: 12px;
    }
    .breakdown-table td { padding: 10px 12px; vertical-align: middle; }
    .breakdown-table .amount { font-weight: 500; text-align: right; }
    .totals-section {
        margin-top: 20px;
        padding: 20px;
        background-color: var(--payslip-bg-light);
        border-radius: 6px;
    }
    .total-row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 15px; }
    .total-row.net-pay {
        font-size: 1.3rem;
        font-weight: bold;
        color: #155724;
        background-color: #d4edda;
        margin-top: 10px;
        padding: 15px;
        border-radius: 6px;
    }
    .payslip-footer {
        display: flex;
        justify-content: space-around;
        margin-top: 50px;
        padding-top: 20px;
        border-top: 1px dashed #ccc;
    }
    .signature-box { text-align: center; }
    .print-button-container { text-align: center; margin: 20px 0; }

    /* استایل‌های رسپانسیو و چاپ */
    @media (max-width: 768px) {
        .payslip-body { padding: 15px; }
        .employee-details { padding: 15px; }
        .payslip-header { flex-direction: column; text-align: center; gap: 15px; }
    }
    @media print {
        body { background-color: #fff !important; }
        .payslip-container {
            margin: 0;
            box-shadow: none;
            border: none;
            max-width: 100%;
            border-radius: 0;
        }
        .main-content, .card {
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }
        .print-button-container, .navbar, #sidebar {
            display: none !important;
        }
        body > .wrapper { display: block !important; }
    }
</style>


<div class="payslip-container printable-area">
    <div class="payslip-header">
        <div>
            <h3>فیش حقوقی</h3>
            <span class="opacity-75"><?php echo htmlspecialchars($companyInfo['name']); ?></span>
        </div>
        <?php if (!empty($companyInfo['logo_path'])): ?>
            <img src="<?php echo $companyInfo['logo_path']; ?>" alt="لوگو" class="logo">
        <?php endif; ?>
    </div>

    <div class="payslip-body">
        <h5 class="text-center mb-4">دوره: <?php echo jdate_words(['mm' => $payroll->pay_period_month])['mm'] . ' سال ' . $payroll->pay_period_year; ?></h5>
        
        <div class="employee-details">
            <div class="detail-item"><strong>کارمند:</strong> <?php echo htmlspecialchars($payroll->user->name); ?></div>
            <div class="detail-item"><strong>پست سازمانی:</strong> <?php echo htmlspecialchars($payroll->user->organizational_position ?? '-'); ?></div>
            <div class="detail-item"><strong>کد ملی:</strong> <?php echo htmlspecialchars($payroll->user->national_id_code ?? '-'); ?></div>
            <div class="detail-item"><strong>تاریخ صدور:</strong> <?php echo jdate('Y/m/d', strtotime($payroll->created_at)); ?></div>
        </div>

        <div class="row breakdown-section">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h6 class="text-success mb-3">۱. حقوق و مزایا</h6>
                <table class="table table-sm table-bordered breakdown-table">
                    <thead><tr><th>شرح</th><th class="text-end">مبلغ (تومان)</th></tr></thead>
                    <tbody>
                        <?php // ✅ سینتکس fn به function تغییر کرد
                        $earnings = array_filter($payroll->items, function($item) { return $item->item_type == 'earning'; }); 
                        ?>
                        <?php foreach($earnings as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item->description); ?></td>
                            <td class="amount"><?php echo number_format($item->amount); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-6">
                <h6 class="text-danger mb-3">۲. کسورات</h6>
                <table class="table table-sm table-bordered breakdown-table">
                    <thead><tr><th>شرح</th><th class="text-end">مبلغ (تومان)</th></tr></thead>
                    <tbody>
                        <?php // ✅ سینتکس fn به function تغییر کرد
                        $deductions = array_filter($payroll->items, function($item) { return $item->item_type == 'deduction'; }); 
                        ?>
                        <?php if (empty($deductions)): ?>
                            <tr><td colspan="2" class="text-center text-muted p-4">بدون کسورات</td></tr>
                        <?php else: ?>
                            <?php foreach($deductions as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item->description); ?></td>
                                <td class="amount"><?php echo number_format($item->amount); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="totals-section">
            <div class="total-row"><span>جمع حقوق و مزایا:</span> <strong><?php echo number_format($payroll->gross_earnings); ?> تومان</strong></div>
            <div class="total-row"><span>جمع کسورات:</span> <strong><?php echo number_format($payroll->total_deductions); ?> تومان</strong></div>
            <div class="total-row net-pay"><span>خالص پرداختی:</span> <span><?php echo number_format($payroll->net_pay); ?> تومان</span></div>
            <div class="text-muted mt-2 small"><strong>به حروف: </strong> <?php echo convert_number_to_words($payroll->net_pay); ?> تومان</div>
        </div>
        
        <div class="payslip-footer">
            <div class="signature-box"><p>مهر و امضاء امور مالی</p><br><p>....................</p></div>
            <div class="signature-box"><p>امضاء دریافت کننده</p><br><p>....................</p></div>
        </div>
    </div>
</div>

<div class="print-button-container">
    <button onclick="window.print();" class="btn btn-primary"><i class="fas fa-print me-2"></i>چاپ فیش</button>
</div>