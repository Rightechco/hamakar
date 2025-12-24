<h1 class="mb-4">مغایرت‌گیری بانکی</h1>

<?php if (!isset($deposits)): // اگر هنوز مغایرت‌گیری شروع نشده، فرم اولیه را نمایش بده ?>

<div class="card shadow">
    <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">شروع عملیات مغایرت‌گیری</h6></div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row align-items-end">
                <div class="col-md-4"><label class="form-label">انتخاب حساب بانکی:</label><select name="account_id" class="form-select" required><option value="">یک حساب را انتخاب کنید...</option><?php foreach($bankAccounts as $account): ?><option value="<?php echo $account->id; ?>"><?php echo htmlspecialchars($account->name); ?></option><?php endforeach; ?></select></div>
                <div class="col-md-4"><label class="form-label">تاریخ پایان دوره صورت‌حساب:</label><input type="text" name="statement_date" class="form-control persian-datepicker" required></div>
                <div class="col-md-4"><label class="form-label">مانده طبق صورت‌حساب بانک:</label><input type="number" name="statement_balance" class="form-control" required></div>
            </div>
            <button type="submit" name="start_reconciliation" class="btn btn-primary mt-4">شروع مغایرت‌گیری</button>
        </form>
    </div>
</div>

<?php else: // در غیر این صورت، صفحه اصلی مغایرت‌گیری را نمایش بده ?>

<form action="index.php?page=admin&action=process_reconciliation" method="POST">
    <input type="hidden" id="statement_balance_hidden" value="<?php echo $statementBalance; ?>">
    <input type="hidden" id="book_balance_hidden" value="<?php echo $bookBalance; ?>">
    <input type="hidden" name="account_id" value="<?php echo $selectedAccountId; ?>">
    <input type="hidden" name="statement_date" value="<?php echo $statementDate; ?>">
    <input type="hidden" name="statement_balance" value="<?php echo $statementBalance; ?>">

    <div class="card shadow mb-4">
        <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">خلاصه مغایرت‌گیری تا تاریخ <?php echo $statementDateJalali; ?></h6></div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-3"><div class="fs-6 text-muted">مانده طبق دفتر</div><div class="fs-5 fw-bold" id="book-balance-display"></div></div>
                <div class="col-md-3"><div class="fs-6 text-muted">جمع موارد تیک‌خورده</div><div class="fs-5 fw-bold text-primary" id="cleared-balance-display"></div></div>
                <div class="col-md-3"><div class="fs-6 text-muted">مانده طبق صورت‌حساب</div><div class="fs-5 fw-bold"><?php echo number_format($statementBalance); ?></div></div>
                <div class="col-md-3"><div class="fs-6 text-muted">اختلاف</div><div class="fs-4 fw-bolder text-danger" id="difference-display"></div></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow"><div class="card-header"><h6 class="m-0">واریزها (Debits)</h6></div><div class="card-body p-2"><table class="table table-sm table-hover"><tbody><?php foreach($deposits as $tx): ?><tr><td><input type="checkbox" class="form-check-input reconcile-checkbox" name="entry_ids[]" value="<?php echo $tx->id; ?>" data-amount="<?php echo $tx->debit; ?>"></td><td><?php echo jdate('y/m/d', strtotime($tx->voucher_date)); ?></td><td><?php echo htmlspecialchars($tx->description); ?></td><td class="text-end text-success"><?php echo number_format($tx->debit); ?></td></tr><?php endforeach; ?></tbody></table></div></div>
        </div>
        <div class="col-md-6">
            <div class="card shadow"><div class="card-header"><h6 class="m-0">برداشت‌ها (Credits)</h6></div><div class="card-body p-2"><table class="table table-sm table-hover"><tbody><?php foreach($payments as $tx): ?><tr><td><input type="checkbox" class="form-check-input reconcile-checkbox" name="entry_ids[]" value="<?php echo $tx->id; ?>" data-amount="<?php echo -$tx->credit; ?>"></td><td><?php echo jdate('y/m/d', strtotime($tx->voucher_date)); ?></td><td><?php echo htmlspecialchars($tx->description); ?></td><td class="text-end text-danger">(<?php echo number_format($tx->credit); ?>)</td></tr><?php endforeach; ?></tbody></table></div></div>
        </div>
    </div>
    
    <div class="text-center mt-4">
        <button type="submit" id="finish-reconciliation-btn" class="btn btn-success btn-lg" disabled>ثبت نهایی مغایرت و بستن عملیات</button>
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
        checkboxes.forEach(cb => {
            if (cb.checked) {
                clearedAmount += parseFloat(cb.dataset.amount);
            }
        });

        // فرمول استاندارد: مانده تعدیل شده دفتر باید برابر با مانده بانک باشد
        // مانده تعدیل شده دفتر = مانده دفتر - واریزهای باز + برداشت‌های باز
        let unclearedAmount = 0;
        checkboxes.forEach(cb => {
            if (!cb.checked) {
                unclearedAmount += parseFloat(cb.dataset.amount);
            }
        });
        
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
    calculateTotals(); // محاسبه اولیه
});
</script>

<?php endif; ?>