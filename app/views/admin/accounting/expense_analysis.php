<style>
    .fin-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1.5rem; }
    .fin-card-header { padding: 1.25rem 1.5rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .fin-card-title { font-weight: 800; color: #0f172a; font-size: 1rem; margin: 0; }
    .btn-fin-primary { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #fff; border: none; border-radius: 12px; padding: 0.6rem 1.25rem; font-weight: 700; }
    .btn-fin-action { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.45rem 0.9rem; font-size: 0.825rem; font-weight: 700; transition: all 0.2s; }
    .btn-fin-action:hover { background: #e2e8f0; color: #0f172a; }
    .table-premium th { background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.8rem; padding: 1rem; }
    .table-premium td { padding: 0.9rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; color: #1e293b; }

    @media print {
        .no-print, #sidebar, .top-header-bar, #chat-toggler, #chat-window { display: none !important; }
        body { background: white !important; }
        .fin-card { border: 1px solid #ccc !important; box-shadow: none !important; }
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">گزارش آنالیز هزینه‌ها</h1>
        <p class="text-muted small mb-0">تفکیک و تحلیل هزینه‌های کل در بازه زمانی انتخابی</p>
    </div>
    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-print text-indigo-600"></i>
            <span>چاپ گزارش</span>
        </button>
        <button onclick="exportTableToExcel('expense-analysis-report-area', 'آنالیز-هزینه‌ها')" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-file-excel text-success"></i>
            <span>خروجی اکسل</span>
        </button>
    </div>
</div>

<div class="fin-card no-print">
    <div class="fin-card-header"><h6 class="fin-card-title"><i class="fas fa-filter text-indigo-500" style="color:#6366f1;"></i> فیلتر گزارش</h6></div>
    <div class="card-body p-4">
        <form method="POST" action="">
            <div class="row g-3 align-items-end">
                <div class="col-md-5"><label class="form-label fw-bold text-secondary small">از تاریخ:</label><input type="text" name="start_date" class="form-control persian-datepicker rounded-3" value="<?php echo $startDateJalali ?? ''; ?>" autocomplete="off" required></div>
                <div class="col-md-5"><label class="form-label fw-bold text-secondary small">تا تاریخ:</label><input type="text" name="end_date" class="form-control persian-datepicker rounded-3" value="<?php echo $endDateJalali ?? ''; ?>" autocomplete="off" required></div>
                <div class="col-md-2"><button type="submit" class="btn btn-fin-primary w-100 py-2.5">نمایش گزارش</button></div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($reportData)): ?>
<div class="fin-card" id="expense-analysis-report-area">
    <div class="fin-card-header"><h6 class="fin-card-title">تجزیه و تحلیل هزینه‌ها برای دوره <?php echo htmlspecialchars($startDateJalali); ?> تا <?php echo htmlspecialchars($endDateJalali); ?></h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium w-100">
                <thead>
                    <tr>
                        <th>شرح هزینه</th>
                        <th class="text-center">مبلغ کل هزینه (تومان)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reportData)): ?>
                        <tr><td colspan="2" class="text-center text-muted py-4">هیچ هزینه‌ای در این دوره یافت نشد.</td></tr>
                    <?php else: ?>
                        <?php $totalExpenses = 0; foreach($reportData as $row): $totalExpenses += $row->total_expense; ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($row->account_name); ?></td>
                            <td class="text-center font-monospace fw-bold text-danger"><?php echo number_format($row->total_expense); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($reportData)): ?>
                <tfoot>
                    <tr class="bg-light fw-bold fs-6">
                        <td class="text-end">جمع کل هزینه‌ها:</td>
                        <td class="text-center font-monospace text-danger"><?php echo number_format($totalExpenses); ?> تومان</td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

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
            $('.persian-datepicker').pDatepicker({ format: 'YYYY/MM/DD', autoClose: true, initialValue: false, observer: true, initialValueType: 'persian' });
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