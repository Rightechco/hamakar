<style>
    .fin-card { 
        background: #ffffff; 
        border-radius: 20px; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); 
        overflow: hidden; 
        margin-bottom: 1.5rem; 
    }
    .fin-card-header { 
        padding: 1.25rem 1.5rem; 
        background: #ffffff; 
        border-bottom: 1px solid #f1f5f9; 
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .fin-card-title { 
        font-weight: 800; 
        color: #0f172a; 
        font-size: 1rem; 
        margin: 0; 
        display: flex; 
        align-items: center; 
        gap: 0.5rem; 
    }
    
    .btn-fin-primary { 
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); 
        color: #fff; 
        border: none; 
        border-radius: 12px; 
        padding: 0.65rem 1.5rem; 
        font-weight: 700; 
        transition: all 0.2s; 
    }
    .btn-fin-primary:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 20px rgba(99,102,241,0.35); 
        color: #fff; 
    }

    .btn-fin-action { 
        background: #f1f5f9; 
        color: #334155; 
        border: 1px solid #cbd5e1; 
        border-radius: 10px; 
        padding: 0.45rem 0.9rem; 
        font-size: 0.825rem; 
        font-weight: 700; 
        transition: all 0.2s; 
    }
    .btn-fin-action:hover { 
        background: #e2e8f0; 
        color: #0f172a; 
    }

    .form-control-premium {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.65rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    .form-control-premium:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .table-premium { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-premium th { background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.8rem; padding: 1rem; border-bottom: 1px solid #e2e8f0; }
    .table-premium td { padding: 0.85rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; color: #1e293b; vertical-align: middle; }
    .table-premium tr:hover td { background-color: #f8fafc; }

    .table-sub-report { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-sub-report td, .table-sub-report th { padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }

    @media print {
        .no-print, #sidebar, .top-header-bar, #chat-toggler, #chat-window { display: none !important; }
        body { background: white !important; }
        .fin-card { border: 1px solid #ccc !important; box-shadow: none !important; margin-bottom: 1rem !important; }
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">گزارشات مالی اصلی</h1>
        <p class="text-muted small mb-0">صورت‌های مالی اساسی شامل تراز آزمایشی، سود و زیان و ترازنامه سیستم</p>
    </div>
    <!-- دکمه‌های اکشن چاپ و اکسل -->
    <?php if (isset($trialBalance)): ?>
    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-print text-indigo-600"></i>
            <span>چاپ کامل صورت‌های مالی</span>
        </button>
        <button onclick="exportTableToExcel('all-financial-reports-area', 'صورت‌های-مالی-اصلی')" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-file-excel text-success"></i>
            <span>خروجی اکسل</span>
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- فرم فیلتر گزارش -->
<div class="fin-card no-print">
    <div class="fin-card-header">
        <h6 class="fin-card-title">
            <i class="fas fa-calendar-check text-indigo-500" style="color:#6366f1;"></i>
            <span>انتخاب دوره گزارش مالی</span>
        </h6>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-bold text-secondary small">از تاریخ:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted"><i class="far fa-calendar-alt"></i></span>
                        <input type="text" name="start_date" class="form-control form-control-premium border-start-0 rounded-end-3 persian-datepicker" value="<?php echo $startDateJalali ?? ''; ?>" autocomplete="off" required>
                    </div>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold text-secondary small">تا تاریخ:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted"><i class="far fa-calendar-alt"></i></span>
                        <input type="text" name="end_date" class="form-control form-control-premium border-start-0 rounded-end-3 persian-datepicker" value="<?php echo $endDateJalali ?? ''; ?>" autocomplete="off" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-fin-primary w-100 py-2.5">تهیه گزارش</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($trialBalance)): ?>
<div id="all-financial-reports-area">

    <!-- ۱. تراز آزمایشی -->
    <div class="fin-card">
        <div class="fin-card-header">
            <h6 class="fin-card-title">
                <i class="fas fa-balance-scale text-indigo-500" style="color:#6366f1;"></i>
                <span>تراز آزمایشی حساب‌ها</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table-premium w-100">
                    <thead>
                        <tr>
                            <th style="width: 140px;">کد حساب</th>
                            <th>نام حساب</th>
                            <th class="text-center">جمع بدهکار (تومان)</th>
                            <th class="text-center">جمع بستانکار (تومان)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $totalDebit = 0; $totalCredit = 0;
                            foreach ($trialBalance as $row): 
                            $totalDebit += $row->total_debit;
                            $totalCredit += $row->total_credit;
                        ?>
                        <tr>
                            <td><span class="badge bg-slate-100 text-slate-700 font-monospace border"><?php echo htmlspecialchars($row->code); ?></span></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($row->name); ?></td>
                            <td class="text-center font-monospace text-success fw-bold"><?php echo number_format($row->total_debit, 0); ?></td>
                            <td class="text-center font-monospace text-danger fw-bold"><?php echo number_format($row->total_credit, 0); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="bg-light fw-bold fs-6">
                            <td colspan="2" class="text-end pe-3">جمع کل:</td>
                            <td class="text-center font-monospace text-success"><?php echo number_format($totalDebit, 0); ?></td>
                            <td class="text-center font-monospace text-danger"><?php echo number_format($totalCredit, 0); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ۲. صورت سود و زیان -->
    <?php if (isset($profitAndLoss)): ?>
    <div class="fin-card">
        <div class="fin-card-header">
            <div>
                <h6 class="fin-card-title">
                    <i class="fas fa-chart-line text-emerald-500" style="color:#10b981;"></i>
                    <span>صورت سود و زیان</span>
                </h6>
                <small class="text-muted">برای دوره <?php echo htmlspecialchars($startDateJalali); ?> تا <?php echo htmlspecialchars($endDateJalali); ?></small>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="fw-bold text-success mb-3"><i class="fas fa-plus-circle me-1"></i> درآمدها</h6>
                    <table class="table-sub-report mb-0">
                        <tbody>
                            <?php foreach ($profitAndLoss['incomes'] as $income): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($income->name); ?></td>
                                    <td class="text-end font-monospace text-success fw-bold"><?php echo number_format($income->balance, 0); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-success">
                                <th class="text-end">جمع درآمدها:</th>
                                <th class="text-end font-monospace"><?php echo number_format($profitAndLoss['totalIncome'], 0); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="col-md-6">
                    <h6 class="fw-bold text-danger mb-3"><i class="fas fa-minus-circle me-1"></i> هزینه‌ها</h6>
                    <table class="table-sub-report mb-0">
                        <tbody>
                            <?php foreach ($profitAndLoss['expenses'] as $expense): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($expense->name); ?></td>
                                    <td class="text-end font-monospace text-danger fw-bold">(<?php echo number_format($expense->balance, 0); ?>)</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-danger">
                                <th class="text-end">جمع هزینه‌ها:</th>
                                <th class="text-end font-monospace">(<?php echo number_format($profitAndLoss['totalExpenses'], 0); ?>)</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <hr class="my-4 border-slate-200">

            <div class="d-flex justify-content-center">
                <?php $netProfit = $profitAndLoss['netProfit']; $isProfit = $netProfit >= 0; ?>
                <div class="p-3 px-5 rounded-4 text-white text-center shadow-sm <?php echo $isProfit ? 'bg-success' : 'bg-danger'; ?>" style="<?php echo $isProfit ? 'background: linear-gradient(135deg, #10b981 0%, #047857 100%);' : 'background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);'; ?>">
                    <span class="d-block small opacity-75 mb-1 fw-bold"><?php echo $isProfit ? 'سود خالص دوره' : 'زیان خالص دوره'; ?></span>
                    <span class="fs-3 font-monospace fw-bold"><?php echo number_format(abs($netProfit), 0); ?> تومان</span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ۳. ترازنامه (صورت وضعیت مالی) -->
    <?php if (isset($balanceSheet)): ?>
    <div class="fin-card">
        <div class="fin-card-header">
            <div>
                <h6 class="fin-card-title">
                    <i class="fas fa-file-invoice text-sky-500" style="color:#0284c7;"></i>
                    <span>ترازنامه (صورت وضعیت مالی)</span>
                </h6>
                <small class="text-muted">در تاریخ <?php echo htmlspecialchars($endDateJalali); ?></small>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <!-- دارایی‌ها -->
                <div class="col-md-6 border-end border-slate-200">
                    <h6 class="fw-bold text-primary mb-3 text-center"><i class="fas fa-vault me-1"></i> دارایی‌ها</h6>
                    <table class="table-sub-report mb-3">
                        <tbody>
                            <?php foreach($balanceSheet['assets'] as $asset): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($asset->name); ?></td>
                                    <td class="text-end font-monospace fw-bold"><?php echo number_format($asset->balance, 0); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="p-3 bg-indigo-50 rounded-3 d-flex justify-content-between align-items-center fw-bold text-indigo-900">
                        <span>جمع کل دارایی‌ها:</span>
                        <span class="font-monospace fs-6"><?php echo number_format($balanceSheet['totalAssets'], 0); ?> تومان</span>
                    </div>
                </div>

                <!-- بدهی‌ها و حقوق صاحبان سهام -->
                <div class="col-md-6">
                    <h6 class="fw-bold text-slate-800 mb-3 text-center"><i class="fas fa-scale-balanced me-1"></i> بدهی‌ها و حقوق صاحبان سهام</h6>
                    
                    <!-- بدهی‌ها -->
                    <span class="small fw-bold text-muted mb-2 d-block">بدهی‌ها:</span>
                    <table class="table-sub-report mb-2">
                        <tbody>
                            <?php foreach($balanceSheet['liabilities'] as $liability): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($liability->name); ?></td>
                                    <td class="text-end font-monospace text-danger"><?php echo number_format($liability->balance, 0); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th class="text-end">جمع بدهی‌ها:</th>
                                <th class="text-end font-monospace text-danger"><?php echo number_format($balanceSheet['totalLiabilities'], 0); ?></th>
                            </tr>
                        </tfoot>
                    </table>

                    <!-- حقوق صاحبان سهام -->
                    <span class="small fw-bold text-muted mt-4 mb-2 d-block">حقوق صاحبان سهام:</span>
                    <table class="table-sub-report mb-3">
                        <tbody>
                            <?php foreach($balanceSheet['equity'] as $eq): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($eq->name); ?></td>
                                    <td class="text-end font-monospace"><?php echo number_format($eq->balance, 0); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td>سود (زیان) انباشته دوره</td>
                                <td class="text-end font-monospace fw-bold"><?php echo number_format($balanceSheet['netProfitForPeriod'], 0); ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <th class="text-end">جمع حقوق صاحبان سهام:</th>
                                <?php $totalEquityAndProfit = $balanceSheet['totalEquity'] + $balanceSheet['netProfitForPeriod']; ?>
                                <th class="text-end font-monospace"><?php echo number_format($totalEquityAndProfit, 0); ?></th>
                            </tr>
                        </tfoot>
                    </table>

                    <?php $totalLiabilitiesAndEquity = $balanceSheet['totalLiabilities'] + $totalEquityAndProfit; ?>
                    <?php $isBalanced = round($balanceSheet['totalAssets']) == round($totalLiabilitiesAndEquity); ?>
                    <div class="p-3 rounded-3 d-flex justify-content-between align-items-center fw-bold <?php echo $isBalanced ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : 'bg-rose-50 text-rose-900 border border-rose-200'; ?>" style="<?php echo $isBalanced ? 'background-color:#ecfdf5; color:#065f46;' : 'background-color:#fff1f2; color:#9f1239;'; ?>">
                        <span>جمع بدهی‌ها و حقوق سهام:</span>
                        <span class="font-monospace fs-6"><?php echo number_format($totalLiabilitiesAndEquity, 0); ?> تومان</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
<?php endif; ?>

<!-- اسکریپت استخراج اکسل و تقویم شمسی -->
<script>
function exportTableToExcel(tableID, filename = ''){
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    filename = filename?filename+'.xls':'excel_data.xls';
    downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff' + tableHTML], { type: dataType });
        navigator.msSaveOrOpenBlob(blob, filename);
    }else{
        downloadLink.href = 'data:' + dataType + ', ' + '\ufeff' + tableHTML;
        downloadLink.download = filename;
        downloadLink.click();
    }
}

(function loadPersianDatePicker() {
    function initPicker() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.pDatepicker) {
            $('.persian-datepicker').pDatepicker({
                format: 'YYYY/MM/DD',
                autoClose: true,
                initialValue: false,
                observer: true,
                initialValueType: 'persian'
            });
        } else { setTimeout(initPicker, 100); }
    }
    if (!document.getElementById('p-datepicker-css')) {
        var css = document.createElement('link'); css.id = 'p-datepicker-css'; css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css';
        document.head.appendChild(css);
    }
    if (typeof $ === 'undefined') {
        var jq = document.createElement('script'); jq.src = 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'; jq.onload = loadPDate; document.head.appendChild(jq);
    } else { loadPDate(); }
    function loadPDate() {
        if (typeof window.persianDate === 'undefined') {
            var pd = document.createElement('script'); pd.src = 'https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js'; pd.onload = loadPDatePicker; document.head.appendChild(pd);
        } else { loadPDatePicker(); }
    }
    function loadPDatePicker() {
        if (typeof $.fn.pDatepicker === 'undefined') {
            var pdp = document.createElement('script'); pdp.src = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js'; pdp.onload = initPicker; document.head.appendChild(pdp);
        } else { initPicker(); }
    }
})();
</script>