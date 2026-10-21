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
        padding: 0.65rem 1.5rem; 
        font-weight: 700; 
        transition: all 0.2s; 
    }
    .btn-fin-primary:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 20px rgba(99,102,241,0.35); 
        color: #fff; 
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
    .table-premium td { padding: 0.85rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; color: #1e293b; vertical-align: middle; }
    .table-premium tr:hover td { background-color: #f8fafc; }
</style>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">مدیریت هزینه‌ها</h1>
        <p class="text-muted small mb-0">ثبت، پیگیری و دسته‌بندی هزینه‌های جاری سیستم</p>
    </div>
</div>

<div class="row g-4">
    <!-- فرم ثبت هزینه جدید -->
    <div class="col-lg-4">
        <div class="fin-card">
            <div class="fin-card-header">
                <h6 class="fin-card-title">
                    <i class="fas fa-receipt text-indigo-500" style="color:#6366f1;"></i>
                    <span>ثبت هزینه جدید</span>
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="index.php?page=admin&action=store_expense" method="post">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">تاریخ پرداخت:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                            <input type="text" name="expense_date" class="form-control form-control-premium border-start-0 rounded-end-3 persian-datepicker" placeholder="YYYY/MM/DD" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">مبلغ هزینه (تومان):</label>
                        <input type="number" name="amount" class="form-control form-control-premium font-monospace" placeholder="مثال: 500000" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">شرح و بابت:</label>
                        <input type="text" name="description" class="form-control form-control-premium" placeholder="مثال: خرید ملزومات اداری" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">سرفصل هزینه:</label>
                        <select name="expense_account_id" class="form-select form-select-premium" required>
                            <option value="">انتخاب حساب هزینه...</option>
                            <?php if (isset($expenseAccounts)): ?>
                                <?php foreach($expenseAccounts as $acc): ?>
                                    <option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->name); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small">پرداخت از حساب (بانک/صندوق):</label>
                        <select name="payment_account_id" class="form-select form-select-premium" required>
                            <option value="">انتخاب حساب پرداخت‌کننده...</option>
                            <?php if (isset($paymentAccounts)): ?>
                                <?php foreach($paymentAccounts as $pAcc): ?>
                                    <option value="<?php echo $pAcc->id; ?>"><?php echo htmlspecialchars($pAcc->name); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-fin-primary w-100 py-2.5 d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <span>ثبت و ذخیره هزینه</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- جدول لیست هزینه‌ها -->
    <div class="col-lg-8">
        <div class="fin-card">
            <div class="fin-card-header">
                <h6 class="fin-card-title">
                    <i class="fas fa-list-ul text-indigo-500" style="color:#6366f1;"></i>
                    <span>لیست هزینه‌های ثبت‌شده</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-premium w-100">
                        <thead>
                            <tr>
                                <th>تاریخ</th>
                                <th>شرح هزینه</th>
                                <th>سرفصل هزینه</th>
                                <th>مبلغ (تومان)</th>
                                <th class="text-center">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($expenses)): ?>
                                <?php foreach($expenses as $exp): ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark font-monospace"><?php echo jdate('Y/m/d', strtotime($exp->expense_date)); ?></span></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($exp->description); ?></td>
                                        <td><span class="text-muted small"><?php echo htmlspecialchars($exp->expense_account_name ?? '-'); ?></span></td>
                                        <td class="font-monospace fw-bold text-danger"><?php echo number_format($exp->amount); ?></td>
                                        <td class="text-center">
                                            <form action="index.php?page=admin&action=delete_expense&id=<?php echo $exp->id; ?>" method="post" onsubmit="return confirm('آیا از حذف این هزینه مطمئن هستید؟');" class="d-inline">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1" title="حذف">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fas fa-receipt fs-2 mb-2 d-block opacity-50"></i>
                                        هیچ هزینه‌ای تاکنون ثبت نشده است.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- اسکریپت تضمینی لودر تقویم شمسی -->
<script>
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