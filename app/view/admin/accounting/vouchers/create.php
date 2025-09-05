<h1 class="mb-4">ثبت سند حسابداری جدید</h1>

<form action="index.php?page=admin&action=store_voucher" method="post">
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">اطلاعات سند</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">تاریخ سند</label>
                    <input type="text" name="voucher_date" class="form-control persian-datepicker" required>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">شرح کلی سند</label>
                    <input type="text" name="description" class="form-control" required>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mt-4">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">آرتیکل‌های سند (بدهکار/بستانکار)</h6>
        </div>
        <div class="card-body">
            <table class="table" id="journal-entries-table">
                <thead>
                    <tr>
                        <th>سرفصل حساب</th>
                        <th>بدهکار</th>
                        <th>بستانکار</th>
                        <th><button type="button" class="btn btn-success btn-sm" id="add-entry-row">+</button></th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
                <tfoot>
                    <tr>
                        <th class="text-end">جمع کل:</th>
                        <th id="total-debit">0</th>
                        <th id="total-credit">0</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            <div id="balance-error" class="alert alert-danger" style="display: none;">ترازنامه سند متعادل نیست (جمع بدهکار و بستانکار باید برابر باشد).</div>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary mt-4">ثبت نهایی سند</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 0;
    const tableBody = document.querySelector('#journal-entries-table tbody');
    const addRowBtn = document.getElementById('add-entry-row');
    const totalDebitEl = document.getElementById('total-debit');
    const totalCreditEl = document.getElementById('total-credit');
    const balanceError = document.getElementById('balance-error');

    function addRow() {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="entries[${rowIndex}][account_id]" class="form-select" required>
                    <option value="">انتخاب کنید...</option>
                    <?php foreach($accounts as $account) echo '<option value="'.$account->id.'">'.htmlspecialchars($account->name).' ('.$account->code.')</option>'; ?>
                </select>
            </td>
            <td><input type="number" name="entries[${rowIndex}][debit]" class="form-control debit-input" value="0"></td>
            <td><input type="number" name="entries[${rowIndex}][credit]" class="form-control credit-input" value="0"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-row">x</button></td>
        `;
        tableBody.appendChild(newRow);
        rowIndex++;
        updateEventListeners();
    }

    function calculateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;
        document.querySelectorAll('.debit-input').forEach(input => totalDebit += parseFloat(input.value) || 0);
        document.querySelectorAll('.credit-input').forEach(input => totalCredit += parseFloat(input.value) || 0);
        
        totalDebitEl.textContent = totalDebit.toLocaleString();
        totalCreditEl.textContent = totalCredit.toLocaleString();
        
        balanceError.style.display = (totalDebit !== totalCredit || totalDebit === 0) ? 'block' : 'none';
    }

    function updateEventListeners() {
        document.querySelectorAll('.remove-row').forEach(btn => {
            btn.onclick = (e) => { e.target.closest('tr').remove(); calculateTotals(); };
        });
        document.querySelectorAll('.debit-input, .credit-input').forEach(input => {
            input.oninput = calculateTotals;
        });
    }

    addRowBtn.addEventListener('click', addRow);
    addRow(); // افزودن یک ردیف در ابتدا
});
</script>