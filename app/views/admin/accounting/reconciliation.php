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
        <h1 class="h3 fw-bold text-dark mb-1">مغایرت‌گیری بانکی</h1>
        <p class="text-muted small mb-0">تطبیق تراکنش‌های دفتر با صورت‌حساب رسمی بانک</p>
    </div>
    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-print text-indigo-600"></i>
            <span>چاپ گزارش</span>
        </button>
        <button onclick="exportTableToExcel('reconciliation-report-area', 'مغایرت-گیری-بانکی')" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-file-excel text-success"></i>
            <span>خروجی اکسل</span>
        </button>
    </div>
</div>

<?php if (!isset($deposits)): ?>
<div class="fin-card no-print">
    <div class="fin-card-header"><h6 class="fin-card-title"><i class="fas fa-university text-indigo-500" style="color:#6366f1;"></i> شروع عملیات مغایرت‌گیری</h6></div>
    <div class="card-body p-4">
        <form method="POST" action="">
            <div class="row g-3 align-items-end">
                <div class="col-md-4"><label class="form-label fw-bold text-secondary small">انتخاب حساب بانکی:</label><select name="account_id" class="form-select rounded-3" required><option value="">یک حساب را انتخاب کنید...</option><?php foreach($bankAccounts as $account): ?><option value="<?php echo $account->id; ?>"><?php echo htmlspecialchars($account->name); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-4"><label class="form-label fw-bold text-secondary small">تاریخ پایان دوره صورت‌حساب:</label><input type="text" name="statement_date" class="form-control persian-datepicker rounded-3" autocomplete="off" required></div>
                <div class="col-md-4"><label class="form-label fw-bold text-secondary small">مانده طبق صورت‌حساب بانک:</label><input type="number" name="statement_balance" class="form-control rounded-3" required></div>
            </div>
            <button type="submit" name="start_reconciliation" class="btn btn-fin-primary mt-4 px-4 py-2.5">شروع مغایرت‌گیری</button>
        </form>
    </div>
</div>
<?php else: ?>
<form action="index.php?page=admin&action=process_reconciliation" method="POST" id="reconciliation-report-area">
    <input type="hidden" id="statement_balance_hidden" value="<?php echo $statementBalance; ?>">
    <input type="hidden" id="book_balance_hidden" value="<?php echo $bookBalance; ?>">
    <input type="hidden" name="account_id" value="<?php echo $selectedAccountId; ?>">
    <input type="hidden" name="statement_date" value="<?php echo $statementDate; ?>">
    <input type="hidden" name="statement_balance" value="<?php echo $statementBalance; ?>">

    <div class="fin-card mb-4">
        <div class="fin-card-header"><h6 class="fin-card-title">خلاصه مغایرت‌گیری تا تاریخ <?php echo $statementDateJalali; ?></h6></div>
        <div class="card-body p-4">
            <div class="row text-center g-3">
                <div class="col-md-3"><div class="p-3 bg-light rounded-3"><div class="small text-muted mb-1">مانده طبق دفتر</div><div class="fs-5 fw-bold font-monospace" id="book-balance-display"></div></div></div>
                <div class="col-md-3"><div class="p-3 bg-indigo-50 rounded-3"><div class="small text-indigo-600 mb-1">جمع موارد تیک‌خورده</div><div class="fs-5 fw-bold font-monospace text-indigo-700" id="cleared-balance-display"></div></div></div>
                <div class="col-md-3"><div class="p-3 bg-light rounded-3"><div class="small text-muted mb-1">مانده طبق صورت‌حساب</div><div class="fs-5 fw-bold font-monospace"><?php echo number_format($statementBalance); ?></div></div></div>
                <div class="col-md-3"><div class="p-3 bg-rose-50 rounded-3"><div class="small text-rose-600 mb-1">اختلاف</div><div class="fs-4 fw-bolder font-monospace text-danger" id="difference-display"></div></div></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="fin-card"><div class="fin-card-header"><h6 class="fin-card-title text-success"><i class="fas fa-plus-circle me-1"></i> واریزها (Debits)</h6></div><div class="card-body p-0"><table class="table table-hover table-sm mb-0"><tbody><?php foreach($deposits as $tx): ?><tr><td class="ps-3"><input type="checkbox" class="form-check-input reconcile-checkbox" name="entry_ids[]" value="<?php echo $tx->id; ?>" data-amount="<?php echo $tx->debit; ?>"></td><td class="small"><?php echo jdate('y/m/d', strtotime($tx->voucher_date)); ?></td><td class="small"><?php echo htmlspecialchars($tx->description); ?></td><td class="text-end pe-3 font-monospace text-success fw-bold"><?php echo number_format($tx->debit); ?></td></tr><?php endforeach; ?></tbody></table></div></div>
        </div>
        <div class="col-md-6">
            <div class="fin-card"><div class="fin-card-header"><h6 class="fin-card-title text-danger"><i class="fas fa-minus-circle me-1"></i> برداشت‌ها (Credits)</h6></div><div class="card-body p-0"><table class="table table-hover table-sm mb-0"><tbody><?php foreach($payments as $tx): ?><tr><td class="ps-3"><input type="checkbox" class="form-check-input reconcile-checkbox" name="entry_ids[]" value="<?php echo $tx->id; ?>" data-amount="<?php echo -$tx->credit; ?>"></td><td class="small"><?php echo jdate('y/m/d', strtotime($tx->voucher_date)); ?></td><td class="small"><?php echo htmlspecialchars($tx->description); ?></td><td class="text-end pe-3 font-monospace text-danger fw-bold">(<?php echo number_format($tx->credit); ?>)</td></tr><?php endforeach; ?></tbody></table></div></div>
        </div>
    </div>
    
    <div class="text-center mt-4 no-print">
        <button type="submit" id="finish-reconciliation-btn" class="btn btn-success btn-lg rounded-3 px-5 py-3 fw-bold" disabled>ثبت نهایی مغایرت و بستن عملیات</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statementBalance = parseFloat(document.getElementById('statement_balance_hidden').value);
    const bookBalance = parseFloat(document.getElementById('book_balance_hidden').value);
    const checkboxes = document.querySelectorAll('.reconcile-checkbox');
    const clearedDisplay = document.getElementById('cleared-balance-display');
    const differenceDisplay = document.getElementById('difference-display');
    const bookDisplay = document.getElementById('book-balance-display');
    const finishBtn = document.getElementById('finish-reconciliation-btn');

    bookDisplay.textContent = bookBalance.toLocaleString('fa-IR');

    function calculateTotals() {
        let clearedAmount = 0;
        checkboxes.forEach(cb => { if (cb.checked) clearedAmount += parseFloat(cb.dataset.amount); });
        let unclearedAmount = 0;
        checkboxes.forEach(cb => { if (!cb.checked) unclearedAmount += parseFloat(cb.dataset.amount); });
        
        let adjustedBookBalance = bookBalance - unclearedAmount;
        const difference = adjustedBookBalance - statementBalance;
        
        clearedDisplay.textContent = clearedAmount.toLocaleString('fa-IR');
        differenceDisplay.textContent = difference.toLocaleString('fa-IR');

        if (Math.abs(difference) < 0.01) {
            differenceDisplay.classList.replace('text-danger', 'text-success');
            finishBtn.disabled = false;
        } else {
            differenceDisplay.classList.replace('text-success', 'text-danger');
            finishBtn.disabled = true;
        }
    }

    checkboxes.forEach(cb => cb.addEventListener('change', calculateTotals));
    calculateTotals();
});
</script>
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