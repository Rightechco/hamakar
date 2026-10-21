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

    @media print {
        .no-print, #sidebar, .top-header-bar, #chat-toggler, #chat-window { display: none !important; }
        body { background: white !important; }
        .fin-card { border: 1px solid #ccc !important; box-shadow: none !important; margin-bottom: 1rem !important; }
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">گزارش حضور و غیاب کارمندان</h1>
        <p class="text-muted small mb-0">تردد، ساعات ورود/خروج و محاسبه مجموع کارکرد پرسنل</p>
    </div>
    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-print text-indigo-600"></i>
            <span>چاپ گزارش</span>
        </button>
        <button onclick="exportTableToExcel('attendance-report-area', 'حضور-و-غیاب')" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-file-excel text-success"></i>
            <span>خروجی اکسل</span>
        </button>
    </div>
</div>

<div class="fin-card no-print">
    <div class="fin-card-header">
        <h6 class="fin-card-title">
            <i class="fas fa-filter text-indigo-500" style="color:#6366f1;"></i>
            <span>فیلتر گزارش تردد</span>
        </h6>
    </div>
    <div class="card-body p-4">
        <form method="POST">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-bold text-secondary small">از تاریخ:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted"><i class="far fa-calendar-alt"></i></span>
                        <input type="text" name="start_date" class="form-control form-control-premium border-start-0 rounded-end-3 persian-datepicker" value="<?php echo $startDateJalali ?? ''; ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold text-secondary small">تا تاریخ:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted"><i class="far fa-calendar-alt"></i></span>
                        <input type="text" name="end_date" class="form-control form-control-premium border-start-0 rounded-end-3 persian-datepicker" value="<?php echo $endDateJalali ?? ''; ?>" autocomplete="off">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-fin-primary w-100 py-2.5">اعمال فیلتر</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="fin-card" id="attendance-report-area">
    <div class="fin-card-header">
        <h6 class="fin-card-title">
            <i class="fas fa-user-clock text-indigo-500" style="color:#6366f1;"></i>
            <span>جدول ثبت ترددها</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium w-100">
                <thead>
                    <tr>
                        <th>نام و خانوادگی کارمند</th>
                        <th class="text-center">تاریخ</th>
                        <th class="text-center">ساعت ورود</th>
                        <th class="text-center">ساعت خروج</th>
                        <th class="text-center">مجموع ساعت (دقیقه)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($records)): ?>
                        <?php foreach($records as $rec): ?>
                        <tr>
                            <td class="fw-bold text-slate-800"><?php echo sanitize($rec->employee_name); ?></td>
                            <td class="text-center"><span class="badge bg-light text-dark font-monospace"><?php echo jdate('Y/m/d', strtotime($rec->clock_in)); ?></span></td>
                            <td class="text-center font-monospace text-emerald-600 fw-bold" style="color:#059669;"><?php echo jdate('H:i', strtotime($rec->clock_in)); ?></td>
                            <td class="text-center font-monospace text-rose-600 fw-bold" style="color:#e11d48;"><?php echo $rec->clock_out ? jdate('H:i', strtotime($rec->clock_out)) : '---'; ?></td>
                            <td class="text-center"><span class="badge bg-indigo-50 text-indigo-700 font-monospace border px-3 py-1.5" style="background:#e0e7ff; color:#4338ca;"><?php echo $rec->total_duration ?? '---'; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-user-slash fs-2 mb-2 d-block opacity-50"></i>
                                هیچ رکوردی برای نمایش تردد پیدا نشد.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

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