<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/expenses.php - مدیریت هزینه‌ها و ثبت فاکتور خرید
$expenseAccounts = $expense_accounts ?? $data['expense_accounts'] ?? [];
$paymentAccounts = $payment_accounts ?? $data['payment_accounts'] ?? [];
$expenses = $expenses ?? $data['expenses'] ?? [];
?>

<style>
    .expense-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
    }
    .form-control-expense {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding: 0.8rem 1rem;
        font-size: 0.9rem;
    }
    .form-control-expense:focus {
        border-color: #e11d48;
        box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.15);
    }
    .table-expense th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-expense td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-receipt text-rose-600" style="color:#e11d48;"></i>
            <span>مدیریت و ثبت هزینه‌ها</span>
        </h3>
        <p class="text-muted small mb-0">ثبت فاکتورهای هزینه و صدور خودکار سند اسناد پرداختی</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- فرم ثبت هزینه جدید -->
    <div class="col-lg-5">
        <div class="card expense-card p-4">
            <h6 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                <i class="fas fa-plus-circle text-rose-600" style="color:#e11d48;"></i>
                <span>ثبت هزینه جدید</span>
            </h6>

            <form action="index.php?page=admin&action=store_expense" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">تاریخ ثبت: <span class="text-danger">*</span></label>
                    <input type="text" name="expense_date" class="form-control form-control-expense font-monospace text-center" value="<?php echo function_exists('jdate') ? jdate('Y/m/d') : date('Y-m-d'); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">حساب هزینه: <span class="text-danger">*</span></label>
                    <select name="expense_account_id" class="form-select form-control-expense" required>
                        <option value="">-- انتخاب سرفصل هزینه --</option>
                        <?php foreach ($expenseAccounts as $acc): ?>
                            <option value="<?php echo $acc->id; ?>">[<?php echo sanitize($acc->code); ?>] <?php echo sanitize($acc->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">مبلغ هزینه (تومان): <span class="text-danger">*</span></label>
                    <input type="number" name="amount" class="form-control form-control-expense font-monospace fw-bold text-end" min="1" placeholder="0" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">پرداخت از حساب (دارایی/بانک): <span class="text-danger">*</span></label>
                    <select name="payment_account_id" class="form-select form-control-expense" required>
                        <option value="">-- انتخاب بانک / صندوق --</option>
                        <?php foreach ($paymentAccounts as $acc): ?>
                            <option value="<?php echo $acc->id; ?>">[<?php echo sanitize($acc->code); ?>] <?php echo sanitize($acc->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small text-dark">طرف حساب / دریافت کننده:</label>
                    <input type="text" name="vendor" class="form-control form-control-expense" placeholder="نام شخص یا فروشنده...">
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-dark">شرح هزینه: <span class="text-danger">*</span></label>
                    <textarea name="description" class="form-control form-control-expense" rows="3" placeholder="توضیحات تکمیلی بابت هزینه..." required></textarea>
                </div>

                <button type="submit" class="btn btn-danger w-100 rounded-pill py-2.5 fw-bold shadow-sm" style="background:#e11d48; border:none;">
                    <i class="fas fa-check-circle me-1"></i> ثبت هزینه و صدور سند
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card expense-card overflow-hidden h-100">
            <div class="card-header bg-white border-bottom p-3.5 d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-list text-rose-600" style="color:#e11d48;"></i>
                    <span>آخرین هزینه‌های ثبت‌شده</span>
                </h6>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-expense w-100">
                        <thead>
                            <tr>
                                <th class="text-center">تاریخ</th>
                                <th>شرح / بابت</th>
                                <th class="text-center">مبلغ</th>
                                <th class="text-center">طرف حساب</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($expenses)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-receipt fs-2 mb-2 d-block opacity-40"></i>
                                        هنوز هیچ هزینه‌ای در سیستم ثبت نشده است.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($expenses as $exp): ?>
                                    <tr>
                                        <td class="text-center font-monospace small text-muted"><?php echo function_exists('jdate') ? jdate('Y/m/d', strtotime($exp->expense_date)) : $exp->expense_date; ?></td>
                                        <td class="fw-bold text-dark"><?php echo sanitize($exp->description); ?></td>
                                        <td class="text-center font-monospace fw-bold text-rose-600" style="color:#e11d48;"><?php echo number_format($exp->amount); ?> تومان</td>
                                        <td class="text-center small text-muted"><?php echo sanitize($exp->vendor ?? '---'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>