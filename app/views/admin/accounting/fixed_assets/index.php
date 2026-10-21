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

    .btn-fin-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.55rem 1.25rem;
        font-weight: 700;
        transition: all 0.2s;
        text-decoration: none;
    }
    .btn-fin-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245,158,11,0.35);
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
        <h1 class="h3 fw-bold text-dark mb-1">مدیریت دارایی‌های ثابت</h1>
        <p class="text-muted small mb-0">ثبت، شناسایی و کنترل دارایی‌های سرمایه‌ای و محاسبات ارزش استهلاک</p>
    </div>
</div>

<div class="row g-4">
    <!-- فرم ثبت دارایی جدید -->
    <div class="col-lg-4">
        <div class="fin-card">
            <div class="fin-card-header">
                <h6 class="fin-card-title">
                    <i class="fas fa-boxes-packing text-indigo-500" style="color:#6366f1;"></i>
                    <span>افزودن دارایی جدید</span>
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="index.php?page=admin&action=store_fixed_asset" method="post">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">نام دارایی:</label>
                        <input type="text" name="asset_name" class="form-control form-control-premium" placeholder="مثال: سرور مرکزی شبکه" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">کد دارایی:</label>
                        <input type="text" name="asset_code" class="form-control form-control-premium font-monospace" placeholder="مثال: AST-1002">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">تاریخ خرید:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                                <i class="far fa-calendar-alt"></i>
                            </span>
                            <input type="text" name="purchase_date" class="form-control form-control-premium border-start-0 rounded-end-3 persian-datepicker" placeholder="YYYY/MM/DD" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">بهای تمام شده (تومان):</label>
                            <input type="number" name="purchase_cost" class="form-control form-control-premium font-monospace" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">ارزش اسقاط (تومان):</label>
                            <input type="number" name="salvage_value" class="form-control form-control-premium font-monospace" value="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">عمر مفید (سال):</label>
                        <input type="number" name="useful_life_years" class="form-control form-control-premium font-monospace" placeholder="مثال: 5" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">حساب دارایی:</label>
                        <select name="asset_account_id" class="form-select form-select-premium" required>
                            <option value="">انتخاب حساب دارایی...</option>
                            <?php foreach($assetAccounts as $acc): ?>
                                <option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">حساب هزینه استهلاک:</label>
                        <select name="expense_account_id" class="form-select form-select-premium" required>
                            <option value="">انتخاب حساب هزینه...</option>
                            <?php foreach($expenseAccounts as $acc): ?>
                                <option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small">حساب استهلاک انباشته:</label>
                        <select name="accumulated_depreciation_account_id" class="form-select form-select-premium" required>
                            <option value="">انتخاب استهلاک انباشته...</option>
                            <?php foreach($assetAccounts as $acc): ?>
                                <option value="<?php echo $acc->id; ?>"><?php echo htmlspecialchars($acc->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-fin-primary w-100 py-2.5 d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-plus-circle"></i>
                        <span>ثبت و ذخیره دارایی</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- جدول لیست دارایی‌های ثبت‌شده -->
    <div class="col-lg-8">
        <div class="fin-card">
            <div class="fin-card-header">
                <h6 class="fin-card-title">
                    <i class="fas fa-cubes text-indigo-500" style="color:#6366f1;"></i>
                    <span>لیست دارایی‌های ثبت‌شده</span>
                </h6>
                <a href="index.php?page=admin&action=run_depreciation_form" class="btn btn-fin-warning d-inline-flex align-items-center gap-2">
                    <i class="fas fa-cogs"></i>
                    <span>اجرای عملیات استهلاک</span>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-premium w-100">
                        <thead>
                            <tr>
                                <th>نام دارایی</th>
                                <th>کد شناسایی</th>
                                <th>تاریخ خرید</th>
                                <th>مبلغ (تومان)</th>
                                <th class="text-center">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($fixedAssets)): ?>
                                <?php foreach($fixedAssets as $asset): ?>
                                    <tr>
                                        <td class="fw-bold"><?php echo htmlspecialchars($asset->asset_name); ?></td>
                                        <td><span class="badge bg-slate-100 text-slate-700 font-monospace border"><?php echo htmlspecialchars($asset->asset_code ?? '-'); ?></span></td>
                                        <td><span class="badge bg-light text-dark font-monospace"><?php echo jdate('Y/m/d', strtotime($asset->purchase_date)); ?></span></td>
                                        <td class="font-monospace fw-bold text-indigo-600"><?php echo number_format($asset->purchase_cost); ?></td>
                                        <td class="text-center">
                                            <form action="index.php?page=admin&action=delete_fixed_asset&id=<?php echo $asset->id; ?>" method="post" onsubmit="return confirm('آیا از حذف این دارایی مطمئن هستید؟');" class="d-inline">
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
                                        <i class="fas fa-boxes-stacked fs-2 mb-2 d-block opacity-50"></i>
                                        هیچ دارایی ثابتی تاکنون ثبت نشده است.
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