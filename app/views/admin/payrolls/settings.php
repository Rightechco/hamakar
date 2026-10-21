<?php
// app/views/admin/payrolls/settings.php
$title = $title ?? 'تنظیمات حقوق و دستمزد';
$settings = $settings ?? [];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 18px;
        --radius-md: 10px;
        --shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    body, .payroll-settings-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .premium-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        margin-bottom: 1.5rem;
    }

    .premium-card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
    }

    .form-control {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        padding: 0.65rem 0.95rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .btn-gradient-primary {
        background: var(--primary-gradient);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        padding: 0.75rem 1.8rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: all 0.2s ease;
    }

    .btn-gradient-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        color: #fff;
    }

    /* Floating Table Styling */
    .table-ultra-settings {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-ultra-settings tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 12px;
        transition: transform 0.2s;
    }

    .table-ultra-settings tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra-settings td, .table-ultra-settings th {
        padding: 1rem 1.1rem;
        border: none;
        vertical-align: middle;
    }

    .bg-soft-primary { background-color: #e0e7ff; color: #4338ca !important; }
</style>

<div class="payroll-settings-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($title); ?></h3> 
            <p class="text-muted small mb-0">تنظیم بخشنامه‌ها، حقوق پایه، حق مسکن، پایه سنوات و ضرایب پاداش/جریمه وزارت کار</p>
        </div>
    </div>

    <!-- Info Banner -->
    <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4 d-flex align-items-center p-3">
        <i class="fas fa-info-circle fa-2x me-3 text-primary"></i>
        <div class="small">
            در این بخش مقادیر سالانه حقوق و دستمزد را بر اساس دستورالعمل‌های قانون کار وارد کنید. سیستم از این مقادیر برای محاسبات خودکار فیش‌های حقوقی استفاده خواهد کرد. 
        </div>
    </div>

    <!-- Form Settings Card -->
    <div class="premium-card">
        <div class="premium-card-header d-flex align-items-center">
            <i class="fas fa-sliders-h text-primary fs-5 me-2"></i>
            <h5 class="mb-0 fw-bold fs-6 text-dark">ثبت تنظیمات سالانه جدید</h5>
        </div>
        <div class="card-body p-4">
            <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=store_payroll_settings" method="POST"> 
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label for="year" class="form-label fw-medium small text-dark">سال اعمال تنظیمات <span class="text-danger">*</span></label> 
                        <input type="number" class="form-control fw-bold" name="year" value="<?php echo jdate('Y'); ?>" required> 
                    </div>
                    <div class="col-md-3">
                        <label for="base_salary_monthly" class="form-label fw-medium small text-dark">حقوق پایه ماهانه (تومان) <span class="text-danger">*</span></label> 
                        <input type="number" class="form-control" name="base_salary_monthly" placeholder="مبلغ به تومان" required> 
                    </div>
                    <div class="col-md-3">
                        <label for="work_days_in_month" class="form-label fw-medium small text-dark">روزهای کاری ماه <span class="text-danger">*</span></label> 
                        <input type="number" class="form-control" name="work_days_in_month" value="30" required> 
                    </div>
                    <div class="col-md-3">
                        <label for="housing_allowance" class="form-label fw-medium small text-dark">حق مسکن (ماهانه) <span class="text-danger">*</span></label> 
                        <input type="number" class="form-control" name="housing_allowance" placeholder="مبلغ به تومان" required> 
                    </div>
                    <div class="col-md-3">
                        <label for="family_allowance" class="form-label fw-medium small text-dark">حق اولاد (هر فرزند) <span class="text-danger">*</span></label> 
                        <input type="number" class="form-control" name="family_allowance" placeholder="مبلغ به تومان" required> 
                    </div>
                    <div class="col-md-3">
                        <label for="seniority_per_year" class="form-label fw-medium small text-dark">پایه سنوات (روزانه) <span class="text-danger">*</span></label> 
                        <input type="number" class="form-control" name="seniority_per_year" placeholder="مبلغ به تومان" required> 
                    </div>
                    <div class="col-md-3">
                        <label for="bonus_rate_percentage" class="form-label fw-medium small text-dark">درصد پاداش عملکرد (%)</label> 
                        <input type="number" step="0.01" class="form-control" name="bonus_rate_percentage" placeholder="مثلا 10" value="0"> 
                    </div>
                    <div class="col-md-3">
                        <label for="penalty_rate_percentage" class="form-label fw-medium small text-dark">درصد جریمه عملکرد (%)</label> 
                        <input type="number" step="0.01" class="form-control" name="penalty_rate_percentage" placeholder="مثلا 5" value="0"> 
                    </div>
                </div>
                
                <div class="d-flex justify-content-end pt-3 border-top">
                    <button type="submit" class="btn btn-gradient-primary"> 
                        <i class="fas fa-save me-1.5"></i> ذخیره تنظیمات سال 
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Settings Table -->
    <div class="premium-card">
        <div class="premium-card-header d-flex align-items-center">
            <i class="fas fa-history text-primary fs-5 me-2"></i>
            <h5 class="mb-0 fw-bold fs-6 text-dark">تاریخچه تنظیمات ذخیره‌شده</h5> 
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-ultra-settings align-middle">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th>سال مالی</th> 
                            <th>حقوق پایه (تومان)</th> 
                            <th class="text-center">روزهای کاری</th> 
                            <th>حق مسکن (تومان)</th> 
                            <th>حق اولاد (تومان)</th> 
                            <th>پایه سنوات (روزانه)</th> 
                            <th class="text-center">پاداش (%)</th> 
                            <th class="text-center">جریمه (%)</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($settings)): ?> 
                            <?php foreach($settings as $setting): ?> 
                            <tr>
                                <td>
                                    <span class="badge bg-soft-primary px-3 py-1.5 rounded-pill fw-bold">
                                        <?php echo htmlspecialchars($setting->setting_year); ?> 
                                    </span>
                                </td>
                                <td class="fw-bold text-dark"><?php echo number_format($setting->base_salary_monthly); ?></td> 
                                <td class="text-center fw-medium"><?php echo htmlspecialchars($setting->work_days_in_month); ?></td> 
                                <td class="text-muted"><?php echo number_format($setting->housing_allowance); ?></td> 
                                <td class="text-muted"><?php echo number_format($setting->family_allowance); ?></td> 
                                <td class="text-muted"><?php echo number_format($setting->seniority_per_year); ?></td> 
                                <td class="text-center text-success fw-bold"><?php echo htmlspecialchars($setting->bonus_rate_percentage); ?>%</td> 
                                <td class="text-center text-danger fw-bold"><?php echo htmlspecialchars($setting->penalty_rate_percentage); ?>%</td> 
                            </tr>
                            <?php endforeach; ?> 
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-cog fa-3x mb-3 text-light"></i>
                                    <p class="mb-0">هیچ تنظیماتی یافت نشد.</p> 
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>