<h1 class="mb-4">مدیریت بودجه هزینه‌ها</h1>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">ثبت یا ویرایش بودجه</h6></div>
            <div class="card-body">
                <form action="index.php?page=admin&action=budgeting" method="post">
                    <div class="mb-3">
                        <label class="form-label">برای سال:</label>
                        <input type="number" name="period_year" class="form-control" value="<?php echo $currentYear; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">برای ماه:</label>
                        <select name="period_month" class="form-select" required>
                            <?php for($m=1; $m<=12; $m++): ?>
                                <option value="<?php echo $m; ?>"><?php echo jdate('F', mktime(0,0,0,$m,1)); ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">برای حساب هزینه:</label>
                        <select name="account_id" class="form-select" required>
                            <option value="">انتخاب کنید...</option>
                            <?php foreach($expenseAccounts as $account): ?>
                                <option value="<?php echo $account->id; ?>"><?php echo htmlspecialchars($account->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مبلغ بودجه (تومان):</label>
                        <input type="number" name="budget_amount" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">ذخیره بودجه</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">بودجه‌های ثبت‌شده برای سال <?php echo $currentYear; ?></h6></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead><tr><th>ماه</th><th>حساب هزینه</th><th>مبلغ بودجه</th><th>عملیات</th></tr></thead>
                        <tbody>
                            <?php foreach($budgets as $budget): ?>
                            <tr>
                                <td><?php echo jdate('F', mktime(0,0,0,$budget->period_month,1)); ?></td>
                                <td><?php echo htmlspecialchars($budget->account_name); ?></td>
                                <td><?php echo number_format($budget->budget_amount); ?> تومان</td>
                                <td>
                                    <form action="index.php?page=admin&action=delete_budget&id=<?php echo $budget->id; ?>" method="post" onsubmit="return confirm('آیا از حذف این بودجه مطمئن هستید؟');">
                                        <button type="submit" class="btn btn-danger btn-sm py-0 px-1">حذف</button>
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