<style>
    .fin-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1.5rem; }
    .fin-card-header { padding: 1.25rem 1.5rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; }
    .fin-card-title { font-weight: 800; color: #0f172a; font-size: 1rem; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    .btn-fin-primary { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #fff; border: none; border-radius: 12px; padding: 0.6rem 1.25rem; font-weight: 700; transition: all 0.2s; }
    .btn-fin-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(99,102,241,0.35); color: #fff; }
    .table-premium th { background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.8rem; padding: 1rem; border-bottom: 1px solid #e2e8f0; }
    .table-premium td { padding: 0.85rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; color: #1e293b; vertical-align: middle; }
</style>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">مدیریت بودجه هزینه‌ها</h1>
        <p class="text-muted small mb-0">تعریف و تخصیص سقف اعتبار مالی برای حساب‌های هزینه</p>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="fin-card">
            <div class="fin-card-header"><h6 class="fin-card-title"><i class="fas fa-plus-circle text-indigo-500" style="color:#6366f1;"></i> ثبت یا ویرایش بودجه</h6></div>
            <div class="card-body p-4">
                <form action="index.php?page=admin&action=budgeting" method="post">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">برای سال:</label>
                        <input type="number" name="period_year" class="form-control rounded-3" value="<?php echo $currentYear; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">برای ماه:</label>
                        <select name="period_month" class="form-select rounded-3" required>
                            <?php for($m=1; $m<=12; $m++): ?>
                                <option value="<?php echo $m; ?>"><?php echo jdate('F', mktime(0,0,0,$m,1)); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary small">برای حساب هزینه:</label>
                        <select name="account_id" class="form-select rounded-3" required>
                            <option value="">انتخاب کنید...</option>
                            <?php foreach($expenseAccounts as $account): ?>
                                <option value="<?php echo $account->id; ?>"><?php echo htmlspecialchars($account->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small">مبلغ بودجه (تومان):</label>
                        <input type="number" name="budget_amount" class="form-control rounded-3" required>
                    </div>
                    <button type="submit" class="btn btn-fin-primary w-100 py-2.5">ذخیره بودجه</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="fin-card">
            <div class="fin-card-header"><h6 class="fin-card-title"><i class="fas fa-list text-indigo-500" style="color:#6366f1;"></i> بودجه‌های ثبت‌شده برای سال <?php echo $currentYear; ?></h6></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-premium w-100">
                        <thead><tr><th>ماه</th><th>حساب هزینه</th><th>مبلغ بودجه</th><th class="text-center">عملیات</th></tr></thead>
                        <tbody>
                            <?php foreach($budgets as $budget): ?>
                            <tr>
                                <td><span class="badge bg-light text-dark fw-bold"><?php echo jdate('F', mktime(0,0,0,$budget->period_month,1)); ?></span></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($budget->account_name); ?></td>
                                <td class="font-monospace text-indigo-600 fw-bold"><?php echo number_format($budget->budget_amount); ?> تومان</td>
                                <td class="text-center">
                                    <form action="index.php?page=admin&action=delete_budget&id=<?php echo $budget->id; ?>" method="post" onsubmit="return confirm('آیا از حذف این بودجه مطمئن هستید؟');">
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1"><i class="fas fa-trash"></i> حذف</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>