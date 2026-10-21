<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/budgeting.php - بودجه‌بندی و برنامه مالی
$expenseAccounts = $expenseAccounts ?? $data['expenseAccounts'] ?? [];
$budgets = $budgets ?? $data['budgets'] ?? [];
?>

<style>
    .budget-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-wallet text-indigo-600" style="color:#6366f1;"></i>
            <span>بودجه‌بندی هزینه‌ها</span>
        </h3>
        <p class="text-muted small mb-0">تخصیص سقف اعتبار بودجه به سرفصل هزینه‌های عملیاتی سالانه و ماهانه</p>
    </div>
</div>

<div class="card budget-card p-4 mb-4">
    <form action="index.php?page=admin&action=budgeting" method="POST" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-dark">سرفصل هزینه:</label>
            <select name="account_id" class="form-select rounded-3" required>
                <option value="">-- انتخاب سرفصل --</option>
                <?php foreach ($expenseAccounts as $acc): ?>
                    <option value="<?php echo $acc->id; ?>">[<?php echo sanitize($acc->code); ?>] <?php echo sanitize($acc->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold text-dark">سال:</label>
            <input type="text" name="period_year" class="form-control rounded-3 font-monospace text-center" value="<?php echo function_exists('jdate') ? jdate('Y') : date('Y'); ?>" required>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold text-dark">ماه:</label>
            <input type="number" name="period_month" class="form-control rounded-3 font-monospace text-center" min="1" max="12" value="1" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-dark">مبلغ بودجه مصوب (تومان):</label>
            <input type="number" name="budget_amount" class="form-control rounded-3 font-monospace" placeholder="0" required>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold">ثبت</button>
        </div>
    </form>
</div>