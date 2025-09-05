<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo htmlspecialchars($title); ?></h6>
    </div>
    <div class="card-body">
        <p>در این بخش مقادیر سالانه حقوق و دستمزد را بر اساس دستورالعمل‌های وزارت کار وارد کنید. سیستم از این مقادیر برای محاسبات خودکار استفاده خواهد کرد.</p>
        
        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=store_payroll_settings" method="POST">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="year" class="form-label">سال اعمال تنظیمات <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="year" value="<?php echo jdate('Y'); ?>" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="base_salary_monthly" class="form-label">حقوق پایه ماهانه <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="base_salary_monthly" placeholder="مبلغ به تومان" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="work_days_in_month" class="form-label">روزهای کاری ماه <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="work_days_in_month" value="30" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="housing_allowance" class="form-label">حق مسکن (ماهانه) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="housing_allowance" placeholder="مبلغ به تومان" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="family_allowance" class="form-label">حق اولاد (به ازای هر فرزند) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="family_allowance" placeholder="مبلغ به تومان" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="seniority_per_year" class="form-label">پایه سنوات (روزانه) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="seniority_per_year" placeholder="مبلغ به تومان" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">ذخیره تنظیمات سال</button>
        </form>
        
        <hr>
        
        <h5 class="mt-4">تنظیمات ذخیره شده</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>سال</th>
                        <th>حقوق پایه</th>
                        <th>روزهای کاری</th>
                        <th>حق مسکن</th>
                        <th>حق اولاد</th>
                        <th>پایه سنوات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($settings)): ?>
                        <?php foreach($settings as $setting): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($setting->setting_year); ?></td>
                            <td><?php echo number_format($setting->base_salary_monthly); ?></td>
                            <td><?php echo htmlspecialchars($setting->work_days_in_month); ?></td>
                            <td><?php echo number_format($setting->housing_allowance); ?></td>
                            <td><?php echo number_format($setting->family_allowance); ?></td>
                            <td><?php echo number_format($setting->seniority_per_year); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">هیچ تنظیماتی یافت نشد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>