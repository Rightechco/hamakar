<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/create_voucher.php - ثبت سند حسابداری دوبل
$accounts = $accounts ?? $data['accounts'] ?? [];
?>

<style>
    .voucher-card {
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        background: #ffffff;
        box-shadow: 0 10px 30px -5px rgba(0,0,0,0.03);
    }
    .voucher-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0.9rem 1rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .voucher-table td {
        padding: 0.75rem 0.5rem;
        vertical-align: middle;
    }
    .input-voucher-num {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.65rem 0.9rem;
        font-size: 0.9rem;
    }
    .input-voucher-num:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }
    .btn-row-add {
        background: #e0e7ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        border-radius: 12px;
        font-weight: 700;
        padding: 0.5rem 1.25rem;
        transition: all 0.2s ease;
    }
    .btn-row-add:hover {
        background: #6366f1;
        color: #ffffff;
    }
    .balance-status-box {
        border-radius: 16px;
        padding: 1rem 1.25rem;
        font-weight: 800;
    }
    .balance-matched {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .balance-unmatched {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-file-signature text-indigo-600" style="color:#6366f1;"></i>
            <span>ثبت سند حسابداری جدید (دوبل)</span>
        </h3>
        <p class="text-muted small mb-0">ثبت تراکنش‌های بدهکار و بستانکار سیستم با کنترل تراز آنلاین</p>
    </div>

    <a href="index.php?page=admin&action=general_journal" class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-bold">
        <i class="fas fa-arrow-left me-1"></i> مشاهده دفتر روزنامه
    </a>
</div>

<div class="card voucher-card border-0 mb-4">
    <div class="card-body p-4 p-md-5">
        <form action="index.php?page=admin&action=store_voucher" method="POST" id="voucherForm">
            
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark small">تاریخ سند: <span class="text-danger">*</span></label>
                    <input type="text" name="voucher_date" class="form-control input-voucher-num font-monospace text-center fw-bold" value="<?php echo function_exists('jdate') ? jdate('Y/m/d') : date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label fw-bold text-dark small">شرح عمومی / بابت سند: <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control input-voucher-num fw-bold" placeholder="مثال: بابت ثبت فاکتور شماره ۱۲۴ یا تسویه حساب..." required>
                </div>
            </div>

            <div class="table-responsive mb-3">
                <table class="table voucher-table" id="entriesTable">
                    <thead>
                        <tr>
                            <th style="width: 32%;">حساب حسابداری</th>
                            <th>شرح آرتیکل (جزئیات)</th>
                            <th style="width: 18%;" class="text-center">بدهکار (تومان)</th>
                            <th style="width: 18%;" class="text-center">بستانکار (تومان)</th>
                            <th style="width: 50px;" class="text-center">حذف</th>
                        </tr>
                    </thead>
                    <tbody id="entriesTbody">
                        <!-- ردیف اول -->
                        <tr class="entry-row">
                            <td>
                                <select name="entries[0][account_id]" class="form-select input-voucher-num select-account" required>
                                    <option value="">-- انتخاب حساب --</option>
                                    <?php foreach ($accounts as $acc): ?>
                                        <option value="<?php echo $acc->id; ?>">[<?php echo sanitize($acc->code); ?>] <?php echo sanitize($acc->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="entries[0][description]" class="form-control input-voucher-num" placeholder="شرح ردیف..."></td>
                            <td><input type="number" name="entries[0][debit]" class="form-control input-voucher-num font-monospace text-end input-debit" value="0" min="0" step="any"></td>
                            <td><input type="number" name="entries[0][credit]" class="form-control input-voucher-num font-monospace text-end input-credit" value="0" min="0" step="any"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm text-danger remove-row-btn"><i class="fas fa-trash"></i></button></td>
                        </tr>
                        <!-- ردیف دوم -->
                        <tr class="entry-row">
                            <td>
                                <select name="entries[1][account_id]" class="form-select input-voucher-num select-account" required>
                                    <option value="">-- انتخاب حساب --</option>
                                    <?php foreach ($accounts as $acc): ?>
                                        <option value="<?php echo $acc->id; ?>">[<?php echo sanitize($acc->code); ?>] <?php echo sanitize($acc->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="text" name="entries[1][description]" class="form-control input-voucher-num" placeholder="شرح ردیف..."></td>
                            <td><input type="number" name="entries[1][debit]" class="form-control input-voucher-num font-monospace text-end input-debit" value="0" min="0" step="any"></td>
                            <td><input type="number" name="entries[1][credit]" class="form-control input-voucher-num font-monospace text-end input-credit" value="0" min="0" step="any"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm text-danger remove-row-btn"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <button type="button" class="btn btn-row-add" id="addEntryBtn">
                    <i class="fas fa-plus me-1"></i> افزودن ردیف آرتیکل
                </button>

                <div class="d-flex gap-4 font-monospace fs-6">
                    <div>جمع بدهکار: <strong id="totalDebitText" class="text-indigo-600">0</strong> تومان</div>
                    <div>جمع بستانکار: <strong id="totalCreditText" class="text-indigo-600">0</strong> تومان</div>
                </div>
            </div>

            <div class="balance-status-box mb-4 text-center balance-matched" id="balanceBox">
                <i class="fas fa-scale-balanced me-2"></i>
                <span id="balanceText">سند تراز می‌باشد.</span>
            </div>

            <div class="d-flex justify-content-end pt-3 border-top">
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm" id="submitVoucherBtn">
                    <i class="fas fa-check-circle me-1"></i> ذخیره و ثبت نهایی سند
                </button>
            </div>

        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let rowIndex = 2;
    const tbody = document.getElementById('entriesTbody');
    const addBtn = document.getElementById('addEntryBtn');
    const totalDebitText = document.getElementById('totalDebitText');
    const totalCreditText = document.getElementById('totalCreditText');
    const balanceBox = document.getElementById('balanceBox');
    const balanceText = document.getElementById('balanceText');
    const submitBtn = document.getElementById('submitVoucherBtn');

    function calculateTotals() {
        let debits = 0;
        let credits = 0;

        document.querySelectorAll('.input-debit').forEach(i => debits += parseFloat(i.value) || 0);
        document.querySelectorAll('.input-credit').forEach(i => credits += parseFloat(i.value) || 0);

        totalDebitText.textContent = debits.toLocaleString();
        totalCreditText.textContent = credits.toLocaleString();

        const diff = Math.abs(debits - credits);

        if (debits > 0 && diff < 0.01) {
            balanceBox.className = 'balance-status-box mb-4 text-center balance-matched';
            balanceText.textContent = 'سند تراز می‌باشد.';
            submitBtn.disabled = false;
        } else {
            balanceBox.className = 'balance-status-box mb-4 text-center balance-unmatched';
            balanceText.textContent = `سند ناتراز است! اختلاف: ${diff.toLocaleString()} تومان`;
            submitBtn.disabled = true;
        }
    }

    addBtn.addEventListener('click', function() {
        const tr = document.createElement('tr');
        tr.className = 'entry-row';
        tr.innerHTML = `
            <td>
                <select name="entries[${rowIndex}][account_id]" class="form-select input-voucher-num select-account" required>
                    <option value="">-- انتخاب حساب --</option>
                    <?php foreach ($accounts as $acc): ?>
                        <option value="<?php echo $acc->id; ?>">[<?php echo sanitize($acc->code); ?>] <?php echo sanitize($acc->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td><input type="text" name="entries[${rowIndex}][description]" class="form-control input-voucher-num" placeholder="شرح ردیف..."></td>
            <td><input type="number" name="entries[${rowIndex}][debit]" class="form-control input-voucher-num font-monospace text-end input-debit" value="0" min="0" step="any"></td>
            <td><input type="number" name="entries[${rowIndex}][credit]" class="form-control input-voucher-num font-monospace text-end input-credit" value="0" min="0" step="any"></td>
            <td class="text-center"><button type="button" class="btn btn-sm text-danger remove-row-btn"><i class="fas fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
        calculateTotals();
    });

    tbody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row-btn')) {
            if (document.querySelectorAll('.entry-row').length > 2) {
                e.target.closest('.entry-row').remove();
                calculateTotals();
            }
        }
    });

    tbody.addEventListener('input', calculateTotals);
    calculateTotals();
});
</script>