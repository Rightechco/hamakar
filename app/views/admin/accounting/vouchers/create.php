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
        padding: 0.75rem 1.75rem; 
        font-weight: 700; 
        transition: all 0.2s; 
    }
    .btn-fin-primary:hover:not(:disabled) { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 20px rgba(99,102,241,0.35); 
        color: #fff; 
    }
    .btn-fin-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .form-control-premium, .form-select-premium {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.65rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    .form-control-premium:focus, .form-select-premium:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .table-premium { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-premium th { background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.8rem; padding: 1rem; border-bottom: 1px solid #e2e8f0; }
    .table-premium td { padding: 0.75rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    .balance-status-badge {
        padding: 0.75rem 1.25rem;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.25s ease;
    }
    .balance-status-badge.balanced {
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .balance-status-badge.unbalanced {
        background-color: #fff1f2;
        color: #be123c;
        border: 1px solid #fecdd3;
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">ثبت سند حسابداری جدید</h1>
        <p class="text-muted small mb-0">صدور و ثبت بدهکار/بستانکار آرتیکل‌های مالی در دفتر روزنامه</p>
    </div>
</div>

<form action="index.php?page=admin&action=store_voucher" method="post" id="voucher-form">
    <!-- header card -->
    <div class="fin-card">
        <div class="fin-card-header">
            <h6 class="fin-card-title">
                <i class="fas fa-file-invoice text-indigo-500" style="color:#6366f1;"></i>
                <span>اطلاعات سربرگ سند</span>
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-secondary small">تاریخ ثبت سند:</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                            <i class="far fa-calendar-alt"></i>
                        </span>
                        <input type="text" name="voucher_date" class="form-control form-control-premium border-start-0 rounded-end-3 persian-datepicker" placeholder="YYYY/MM/DD" autocomplete="off" required>
                    </div>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold text-secondary small">شرح کلی و بابت سند:</label>
                    <input type="text" name="description" class="form-control form-control-premium" placeholder="مثال: بابت ثبت هزینه‌ها و واریزی‌های ماه جاری" required>
                </div>
            </div>
        </div>
    </div>

    <!-- entries table card -->
    <div class="fin-card">
        <div class="fin-card-header">
            <h6 class="fin-card-title">
                <i class="fas fa-list-ol text-indigo-500" style="color:#6366f1;"></i>
                <span>آرتیکل‌های سند (ردیف‌های بدهکار / بستانکار)</span>
            </h6>
            <button type="button" class="btn btn-sm btn-indigo px-3 py-2 rounded-3 text-white fw-bold d-inline-flex align-items-center gap-1.5" id="add-entry-row" style="background:#6366f1;">
                <i class="fas fa-plus"></i>
                <span>افزودن ردیف جدید</span>
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table-premium w-100" id="journal-entries-table">
                    <thead>
                        <tr>
                            <th style="min-width: 250px;">سرفصل حساب</th>
                            <th style="width: 220px;" class="text-center">مبلغ بدهکار (تومان)</th>
                            <th style="width: 220px;" class="text-center">مبلغ بستانکار (تومان)</th>
                            <th style="width: 60px;" class="text-center">حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ردیف‌ها با JS افزوده می‌شوند -->
                    </tbody>
                    <tfoot>
                        <tr class="bg-light fw-bold fs-6">
                            <td class="text-end pe-3 fw-bold">جمع کل آرتیکل‌ها:</td>
                            <td class="text-center font-monospace text-success fw-bold" id="total-debit">0</td>
                            <td class="text-center font-monospace text-danger fw-bold" id="total-credit">0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- وضعیت تراز سند -->
            <div class="p-3 bg-slate-50 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div id="balance-status-indicator" class="balance-status-badge unbalanced">
                    <i class="fas fa-exclamation-circle" id="balance-icon"></i>
                    <span id="balance-text">سند متعادل نیست (جمع بدهکار و بستانکار باید برابر و بزرگتر از صفر باشد)</span>
                </div>
                <div class="text-muted small">
                    مابه التفاوت: <span class="font-monospace fw-bold" id="balance-diff">0</span> تومان
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end gap-3 mb-5">
        <a href="index.php?page=admin&action=general_journal" class="btn btn-light border rounded-3 px-4 py-2.5 font-bold text-slate-600">انصراف</a>
        <button type="submit" class="btn btn-fin-primary d-inline-flex align-items-center gap-2" id="submit-btn" disabled>
            <i class="fas fa-check-circle fs-6"></i>
            <span>ثبت نهایی سند حسابداری</span>
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 0;
    const tableBody = document.querySelector('#journal-entries-table tbody');
    const addRowBtn = document.getElementById('add-entry-row');
    const totalDebitEl = document.getElementById('total-debit');
    const totalCreditEl = document.getElementById('total-credit');
    const balanceStatusIndicator = document.getElementById('balance-status-indicator');
    const balanceText = document.getElementById('balance-text');
    const balanceIcon = document.getElementById('balance-icon');
    const balanceDiff = document.getElementById('balance-diff');
    const submitBtn = document.getElementById('submit-btn');

    function addRow() {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select name="entries[${rowIndex}][account_id]" class="form-select form-select-premium" required>
                    <option value="">انتخاب حساب...</option>
                    <?php foreach($accounts as $account): ?>
                        <option value="<?php echo $account->id; ?>"><?php echo htmlspecialchars($account->name) . ' (' . htmlspecialchars($account->code) . ')'; ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="number" name="entries[${rowIndex}][debit]" class="form-control form-control-premium text-center font-monospace debit-input text-success fw-bold" value="0" min="0"></td>
            <td><input type="number" name="entries[${rowIndex}][credit]" class="form-control form-control-premium text-center font-monospace credit-input text-danger fw-bold" value="0" min="0"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1 remove-row" title="حذف ردیف"><i class="fas fa-times"></i></button></td>
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
        
        totalDebitEl.textContent = totalDebit.toLocaleString('fa-IR');
        totalCreditEl.textContent = totalCredit.toLocaleString('fa-IR');
        
        const diff = Math.abs(totalDebit - totalCredit);
        balanceDiff.textContent = diff.toLocaleString('fa-IR');

        const isBalanced = (totalDebit === totalCredit && totalDebit > 0);

        if (isBalanced) {
            balanceStatusIndicator.className = 'balance-status-badge balanced';
            balanceText.textContent = 'سند کاملاً تراز و آماده ثبت است.';
            balanceIcon.className = 'fas fa-check-circle';
            submitBtn.disabled = false;
        } else {
            balanceStatusIndicator.className = 'balance-status-badge unbalanced';
            balanceText.textContent = 'سند متعادل نیست (جمع بدهکار و بستانکار باید برابر باشد)';
            balanceIcon.className = 'fas fa-exclamation-circle';
            submitBtn.disabled = true;
        }
    }

    function updateEventListeners() {
        document.querySelectorAll('.remove-row').forEach(btn => {
            btn.onclick = (e) => { 
                if (document.querySelectorAll('#journal-entries-table tbody tr').length > 1) {
                    e.target.closest('tr').remove(); 
                    calculateTotals(); 
                }
            };
        });
        document.querySelectorAll('.debit-input, .credit-input').forEach(input => {
            input.oninput = calculateTotals;
        });
    }

    addRowBtn.addEventListener('click', addRow);
    addRow(); // افزودن حداقل یک ردیف در ابتدا
    addRow(); // افزودن ردیف دوم برای سهولت بدهکار/بستانکار
});

// اسکریپت تضمینی لودر تقویم شمسی
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