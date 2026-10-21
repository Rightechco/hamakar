<style>
    .fin-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1.5rem; }
    .fin-card-header { padding: 1.25rem 1.5rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .fin-card-title { font-weight: 800; color: #0f172a; font-size: 1rem; margin: 0; }
    .btn-fin-primary { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #fff; border: none; border-radius: 12px; padding: 0.6rem 1.25rem; font-weight: 700; }
    .btn-fin-action { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.45rem 0.9rem; font-size: 0.825rem; font-weight: 700; transition: all 0.2s; }
    .btn-fin-action:hover { background: #e2e8f0; color: #0f172a; }
    .table-premium { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-premium th { background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.8rem; padding: 1rem; border-bottom: 1px solid #e2e8f0; }
    .table-premium td { padding: 0.85rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; color: #1e293b; }

    @media print {
        .no-print, #sidebar, .top-header-bar, #chat-toggler, #chat-window { display: none !important; }
        body { background: white !important; }
        .fin-card { border: 1px solid #ccc !important; box-shadow: none !important; }
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">گزارش دفتر تفصیلی</h1>
        <p class="text-muted small mb-0">مشاهده تفکیکی ریز حساب‌ها بر اساس اشخاص و طرف حساب‌ها</p>
    </div>
    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-print text-indigo-600"></i>
            <span>چاپ گزارش</span>
        </button>
        <button onclick="exportTableToExcel('subledger-report-area', 'دفتر-تفصیلی')" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
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
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary small">انتخاب حساب کل:</label>
                    <select name="account_id" class="form-select rounded-3" required>
                        <option value="">یک حساب را انتخاب کنید...</option>
                        <?php foreach($allAccounts as $account): ?>
                            <option value="<?php echo $account->id; ?>" <?php echo (isset($selectedAccountId) && $selectedAccountId == $account->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($account->name . ' (' . $account->code . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-secondary small">انتخاب مشتری:</label>
                    <select name="entity_id" class="form-select rounded-3" required>
                        <option value="">یک مشتری را انتخاب کنید...</option>
                        <?php foreach($allClients as $client): ?>
                            <option value="<?php echo $client->id; ?>" <?php echo (isset($selectedEntityId) && $selectedEntityId == $client->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($client->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2"><label class="form-label fw-bold text-secondary small">از تاریخ:</label><input type="text" name="start_date" class="form-control persian-datepicker rounded-3" value="<?php echo $startDateJalali ?? ''; ?>" autocomplete="off" required></div>
                <div class="col-md-2"><label class="form-label fw-bold text-secondary small">تا تاریخ:</label><input type="text" name="end_date" class="form-control persian-datepicker rounded-3" value="<?php echo $endDateJalali ?? ''; ?>" autocomplete="off" required></div>
                <div class="col-md-2"><button type="submit" class="btn btn-fin-primary w-100 py-2.5">نمایش</button></div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($ledgerData)): ?>
<div class="fin-card" id="subledger-report-area">
    <div class="fin-card-header"><h6 class="fin-card-title">گردش حساب تفصیلی: <?php echo htmlspecialchars($selectedAccountName . ' - ' . $selectedEntityName); ?></h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium w-100">
                <thead>
                    <tr>
                        <th>تاریخ</th>
                        <th>شرح</th>
                        <th class="text-center">بدهکار</th>
                        <th class="text-center">بستانکار</th>
                        <th class="text-center">مانده</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-light">
                        <td colspan="4" class="fw-bold">مانده از قبل</td>
                        <td class="text-center fw-bold font-monospace"><?php echo number_format($ledgerData['opening_balance']); ?></td>
                    </tr>
                    <?php 
                        $runningBalance = $ledgerData['opening_balance'];
                        foreach ($ledgerData['transactions'] as $tx): 
                        $runningBalance += ($tx->debit - $tx->credit);
                    ?>
                    <tr>
                        <td><?php echo jdate('Y/m/d', strtotime($tx->voucher_date)); ?></td>
                        <td><?php echo htmlspecialchars($tx->description); ?></td>
                        <td class="text-center font-monospace text-success fw-bold"><?php echo $tx->debit > 0 ? number_format($tx->debit) : '-'; ?></td>
                        <td class="text-center font-monospace text-danger fw-bold"><?php echo $tx->credit > 0 ? number_format($tx->credit) : '-'; ?></td>
                        <td class="text-center font-monospace fw-bold"><?php echo number_format($runningBalance); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="bg-light fw-bold fs-6">
                        <td colspan="4" class="text-end">مانده نهایی:</td>
                        <td class="text-center font-monospace text-indigo-600"><?php echo number_format($runningBalance); ?></td>
                    </tr>
                </tfoot>
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