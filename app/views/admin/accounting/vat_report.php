<style>
    .fin-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1.5rem; }
    .fin-card-header { padding: 1.25rem 1.5rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .fin-card-title { font-weight: 800; color: #0f172a; font-size: 1rem; margin: 0; }
    .btn-fin-primary { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #fff; border: none; border-radius: 12px; padding: 0.6rem 1.25rem; font-weight: 700; }
    .btn-fin-action { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.45rem 0.9rem; font-size: 0.825rem; font-weight: 700; transition: all 0.2s; }
    .btn-fin-action:hover { background: #e2e8f0; color: #0f172a; }

    @media print {
        .no-print, #sidebar, .top-header-bar, #chat-toggler, #chat-window { display: none !important; }
        body { background: white !important; }
        .fin-card { border: 1px solid #ccc !important; box-shadow: none !important; }
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">گزارش مالیات بر ارزش افزوده (VAT)</h1>
        <p class="text-muted small mb-0">محاسبه و تجمیع اعتبارات و بدهی‌های مالیاتی دوره</p>
    </div>
    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-print text-indigo-600"></i>
            <span>چاپ اظهارنامه</span>
        </button>
        <button onclick="exportTableToExcel('vat-report-area', 'گزارش-ارزش-افزوده')" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-file-excel text-success"></i>
            <span>خروجی اکسل</span>
        </button>
    </div>
</div>

<div class="fin-card no-print">
    <div class="fin-card-header"><h6 class="fin-card-title"><i class="fas fa-calendar-alt text-indigo-500" style="color:#6366f1;"></i> انتخاب دوره گزارش</h6></div>
    <div class="card-body p-4">
        <form method="POST" action="">
            <div class="row g-3 align-items-end">
                <div class="col-md-5"><label class="form-label fw-bold text-secondary small">از تاریخ:</label><input type="text" name="start_date" class="form-control persian-datepicker rounded-3" value="<?php echo $startDateJalali ?? ''; ?>" autocomplete="off" required></div>
                <div class="col-md-5"><label class="form-label fw-bold text-secondary small">تا تاریخ:</label><input type="text" name="end_date" class="form-control persian-datepicker rounded-3" value="<?php echo $endDateJalali ?? ''; ?>" autocomplete="off" required></div>
                <div class="col-md-2"><button type="submit" class="btn btn-fin-primary w-100 py-2.5">تهیه گزارش</button></div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($reportData)): ?>
<div class="fin-card" id="vat-report-area">
    <div class="fin-card-header"><h6 class="fin-card-title">خلاصه گزارش VAT برای دوره <?php echo htmlspecialchars($startDateJalali); ?> تا <?php echo htmlspecialchars($endDateJalali); ?></h6></div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light">
                    <span class="small text-muted d-block mb-1">کل مالیات فروش (بستانکاری مالیاتی)</span>
                    <span class="fs-5 fw-bold font-monospace text-primary"><?php echo number_format($reportData['sales_vat']); ?> تومان</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light">
                    <span class="small text-muted d-block mb-1">کل مالیات خرید (اعتبار مالیاتی)</span>
                    <span class="fs-5 fw-bold font-monospace text-info">(<?php echo number_format($reportData['purchase_vat']); ?>) تومان</span>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="p-4 rounded-3 text-center text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    <span class="small opacity-75 d-block mb-1">مبلغ خالص قابل پرداخت / استرداد</span>
                    <span class="fs-3 fw-bold font-monospace">
                        <?php 
                            $payable_vat = $reportData['payable_vat'];
                            echo number_format(abs($payable_vat));
                            echo $payable_vat >= 0 ? ' تومان (قابل پرداخت)' : ' تومان (قابل استرداد)';
                        ?>
                    </span>
                </div>
            </div>
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