<style>
    .fin-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1.5rem; }
    .fin-card-header { padding: 1.25rem 1.5rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .fin-card-title { font-weight: 800; color: #0f172a; font-size: 1rem; margin: 0; }
    .btn-fin-primary { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #fff; border: none; border-radius: 12px; padding: 0.6rem 1.25rem; font-weight: 700; }
    .btn-fin-action { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.45rem 0.9rem; font-size: 0.825rem; font-weight: 700; transition: all 0.2s; }
    .btn-fin-action:hover { background: #e2e8f0; color: #0f172a; }
    .voucher-box { border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 1rem; }

    @media print {
        .no-print, #sidebar, .top-header-bar, #chat-toggler, #chat-window { display: none !important; }
        body { background: white !important; }
        .fin-card, .voucher-box { border: 1px solid #ccc !important; box-shadow: none !important; }
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">گزارش دفتر روزنامه</h1>
        <p class="text-muted small mb-0">مشاهده اسناد حسابداری ثبت‌شده به ترتیب زمان</p>
    </div>
    <!-- دکمه‌های اکشن چاپ و خروجی -->
    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-print text-indigo-600"></i>
            <span>چاپ گزارش</span>
        </button>
        <button onclick="exportTableToExcel('journal-table-data', 'دفتر-روزنامه')" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
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
                <div class="col-md-2"><button type="submit" class="btn btn-fin-primary w-100 py-2.5">نمایش</button></div>
            </div>
        </form>
    </div>
</div>

<div id="journal-table-data">
<?php if (isset($journalData) && !empty($journalData)): ?>
    <?php foreach($journalData as $voucher): ?>
        <div class="voucher-box bg-white shadow-sm">
            <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-indigo-600 px-3 py-2 fs-6" style="background:#6366f1;">سند #<?php echo $voucher->id; ?></span>
                    <span class="small fw-bold text-secondary"><i class="far fa-calendar me-1"></i> <?php echo jdate('Y/m/d', strtotime($voucher->voucher_date)); ?></span>
                    <span class="small text-muted"><i class="far fa-comment me-1"></i> <?php echo htmlspecialchars($voucher->description); ?></span>
                </div>
                <div class="d-flex align-items-center gap-1 no-print">
                    <form action="index.php?page=admin&action=reverse_voucher&id=<?php echo $voucher->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('آیا از صدور سند معکوس مطمئن هستید؟');">
                        <button type="submit" class="btn btn-sm btn-outline-warning rounded-2 px-2" title="سند معکوس"><i class="fas fa-undo"></i></button>
                    </form>
                    <form action="index.php?page=admin&action=delete_voucher&id=<?php echo $voucher->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف کامل این سند مطمئن هستید؟');">
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="حذف سند"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-borderless mb-0 align-middle">
                    <thead class="border-bottom text-muted small">
                        <tr>
                            <th class="ps-3" style="width: 50%;">نام حساب</th>
                            <th class="text-center" style="width: 25%;">بدهکار</th>
                            <th class="text-center" style="width: 25%;">بستانکار</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($voucher->entries as $entry): ?>
                            <tr class="border-bottom-faint">
                                <td class="ps-3 fw-bold small"><?php echo htmlspecialchars($entry->account_name . ' (' . $entry->account_code . ')'); ?></td>
                                <td class="text-center font-monospace text-success fw-bold"><?php echo $entry->debit > 0 ? number_format($entry->debit) : '-'; ?></td>
                                <td class="text-center font-monospace text-danger fw-bold"><?php echo $entry->credit > 0 ? number_format($entry->credit) : '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
<?php elseif(isset($journalData)): ?>
    <div class="alert alert-info rounded-3 p-3">هیچ سندی در بازه زمانی انتخاب شده یافت نشد.</div>
<?php endif; ?>
</div>

<script>
// تابع استخراج جدول به فایل اکسل
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