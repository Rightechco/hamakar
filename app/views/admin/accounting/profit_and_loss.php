<style>
    .fin-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1.5rem; }
    .fin-card-header { padding: 1.25rem 1.5rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .fin-card-title { font-weight: 800; color: #0f172a; font-size: 1rem; margin: 0; }
    .btn-fin-primary { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #fff; border: none; border-radius: 12px; padding: 0.6rem 1.25rem; font-weight: 700; }
    .btn-fin-action { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.45rem 0.9rem; font-size: 0.825rem; font-weight: 700; transition: all 0.2s; }
    .btn-fin-action:hover { background: #e2e8f0; color: #0f172a; }
    .table-pnl { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-pnl th { background: #f8fafc; color: #0f172a; font-weight: 800; font-size: 0.9rem; padding: 1rem; border-bottom: 1px solid #e2e8f0; }
    .table-pnl td { padding: 0.85rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; }

    @media print {
        .no-print, #sidebar, .top-header-bar, #chat-toggler, #chat-window { display: none !important; }
        body { background: white !important; }
        .fin-card { border: 1px solid #ccc !important; box-shadow: none !important; }
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1"><?php echo sanitize($title); ?></h1>
        <p class="text-muted small mb-0">صورت سود و زیان جامع عملکرد مالی دوره</p>
    </div>
    <!-- دکمه‌های اکشن -->
    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-print text-indigo-600"></i>
            <span>چاپ صورت مالی</span>
        </button>
        <button onclick="exportTableToExcel('pnl-report-area', 'گزارش-سود-و-زیان')" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-file-excel text-success"></i>
            <span>خروجی اکسل</span>
        </button>
    </div>
</div>

<div class="fin-card no-print">
    <div class="fin-card-header"><h6 class="fin-card-title"><i class="fas fa-filter text-indigo-500" style="color:#6366f1;"></i> انتخاب دوره مالی</h6></div>
    <div class="card-body p-4">
        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=profitAndLossReport" method="POST">
            <div class="row g-3 align-items-end">
                <div class="col-md-5"><label for="start_date" class="form-label fw-bold text-secondary small">از تاریخ:</label><input type="text" id="start_date" name="start_date" class="form-control persian-datepicker rounded-3" value="<?php echo htmlspecialchars($startDateJalali ?? ''); ?>" autocomplete="off" required></div>
                <div class="col-md-5"><label for="end_date" class="form-label fw-bold text-secondary small">تا تاریخ:</label><input type="text" id="end_date" name="end_date" class="form-control persian-datepicker rounded-3" value="<?php echo htmlspecialchars($endDateJalali ?? ''); ?>" autocomplete="off" required></div>
                <div class="col-md-2"><button type="submit" class="btn btn-fin-primary w-100 py-2.5">نمایش گزارش</button></div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($reportData)): ?>
    <div class="fin-card" id="pnl-report-area">
        <div class="card-body p-0">
            <div class="table-responsive mb-4">
                <table class="table-pnl">
                    <thead><tr><th colspan="2" class="text-success"><i class="fas fa-plus-circle me-1"></i> درآمدها</th></tr></thead>
                    <tbody>
                        <?php $totalIncome = 0; ?>
                        <?php if (!empty($reportData['incomes'])): ?>
                            <?php foreach ($reportData['incomes'] as $income): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($income->name); ?></td>
                                    <td class="text-end font-monospace"><?php echo number_format($income->balance); ?></td>
                                </tr>
                                <?php $totalIncome += $income->balance; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="2" class="text-center text-muted">هیچ درآمدی در این دوره ثبت نشده است.</td></tr>
                        <?php endif; ?>
                        <tr class="table-success fw-bold">
                            <td>کل درآمدها</td>
                            <td class="text-end font-monospace fs-6"><?php echo number_format($totalIncome); ?> تومان</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row g-3 p-3 bg-light m-2 rounded-3">
                <div class="col-md-6">
                    <div class="p-3 bg-white border rounded-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-muted small">بهای تمام‌شده (COGS)</span>
                        <span class="font-monospace fw-bold text-danger"><?php echo number_format($reportData['totalCogs'] ?? 0); ?></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-white border rounded-3 d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-muted small">سود ناخالص</span>
                        <span class="font-monospace fw-bold text-primary fs-5"><?php echo number_format($reportData['grossProfit']); ?></span>
                    </div>
                </div>
            </div>

            <div class="table-responsive my-4">
                <table class="table-pnl">
                    <thead><tr><th colspan="2" class="text-danger"><i class="fas fa-minus-circle me-1"></i> هزینه‌های عملیاتی</th></tr></thead>
                    <tbody>
                        <?php $totalExpenses = 0; ?>
                        <?php if (!empty($reportData['expenses'])): ?>
                            <?php foreach ($reportData['expenses'] as $expense): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($expense->name); ?></td>
                                    <td class="text-end font-monospace"><?php echo number_format($expense->balance); ?></td>
                                </tr>
                                <?php $totalExpenses += $expense->balance; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="2" class="text-center text-muted">هیچ هزینه‌ای در این دوره ثبت نشده است.</td></tr>
                        <?php endif; ?>
                        <tr class="table-danger fw-bold">
                            <td>کل هزینه‌ها</td>
                            <td class="text-end font-monospace fs-6"><?php echo number_format($totalExpenses); ?> تومان</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 text-center bg-indigo-600 text-white rounded-bottom-4" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
                <span class="d-block small text-white-50 mb-1 fw-bold">سود خالص دوره</span>
                <span class="fs-2 font-monospace fw-bold"><?php echo number_format($reportData['netProfit']); ?> تومان</span>
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