<h1 class="mb-4">مدیریت دارایی‌های ثابت</h1>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">افزودن دارایی جدید</h6></div>
            <div class="card-body">
                <form action="index.php?page=admin&action=store_fixed_asset" method="post">
                    <div class="mb-2"><label>نام دارایی:</label><input type="text" name="asset_name" class="form-control" required></div>
                    <div class="mb-2"><label>کد دارایی:</label><input type="text" name="asset_code" class="form-control"></div>
                    <div class="mb-2"><label>تاریخ خرید:</label><input type="text" name="purchase_date" class="form-control persian-datepicker" required></div>
                    <div class="mb-2"><label>بهای تمام شده:</label><input type="number" name="purchase_cost" class="form-control" required></div>
                    <div class="mb-2"><label>ارزش اسقاط:</label><input type="number" name="salvage_value" class="form-control" value="0" required></div>
                    <div class="mb-2"><label>عمر مفید (سال):</label><input type="number" name="useful_life_years" class="form-control" required></div>
                    <div class="mb-2"><label>حساب دارایی:</label><select name="asset_account_id" class="form-select" required><?php foreach($assetAccounts as $acc) echo "<option value='{$acc->id}'>{$acc->name}</option>"; ?></select></div>
                    <div class="mb-2"><label>حساب هزینه استهلاک:</label><select name="expense_account_id" class="form-select" required><?php foreach($expenseAccounts as $acc) echo "<option value='{$acc->id}'>{$acc->name}</option>"; ?></select></div>
                    <div class="mb-2"><label>حساب استهلاک انباشته:</label><select name="accumulated_depreciation_account_id" class="form-select" required><?php foreach($assetAccounts as $acc) echo "<option value='{$acc->id}'>{$acc->name}</option>"; ?></select></div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">ثبت دارایی</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow">
 <div class="card-header d-flex justify-content-between align-items-center">
    <h6 class="m-0 font-weight-bold text-primary">لیست دارایی‌های ثبت‌شده</h6>
    <a href="index.php?page=admin&action=run_depreciation_form" class="btn btn-warning btn-sm">
        <i class="fas fa-cogs"></i> اجرای عملیات استهلاک
    </a>
</div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead><tr><th>نام</th><th>کد</th><th>تاریخ خرید</th><th>مبلغ</th><th>عملیات</th></tr></thead>
                    <tbody>
                        <?php foreach($fixedAssets as $asset): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($asset->asset_name); ?></td>
                            <td><?php echo htmlspecialchars($asset->asset_code); ?></td>
                            <td><?php echo jdate('Y/m/d', strtotime($asset->purchase_date)); ?></td>
                            <td><?php echo number_format($asset->purchase_cost); ?></td>
                            <td>
                                <form action="index.php?page=admin&action=delete_fixed_asset&id=<?php echo $asset->id; ?>" method="post" onsubmit="return confirm('آیا مطمئن هستید؟');"><button type="submit" class="btn btn-danger btn-sm py-0 px-1">حذف</button></form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>