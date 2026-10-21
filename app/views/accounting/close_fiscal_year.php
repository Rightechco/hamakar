<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/close_fiscal_year.php - بستن سال مالی
?>

<style>
    .closing-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-lock text-danger"></i>
            <span>بستن سال مالی و صدور سند اختتامیه</span>
        </h3>
        <p class="text-muted small mb-0">بستن حساب‌های موقت (درآمد و هزینه) و انتقال مانده سود/زیان به سود انباشته</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card closing-card p-4 p-md-5">
            <div class="alert alert-warning border-0 rounded-4 p-4 mb-4">
                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-exclamation-triangle me-2 text-warning"></i> هشدار مهم قبل از بستن سال مالی:</h6>
                <p class="small text-muted mb-0 lh-lg">
                    با اجرای این عملیات، تمامی حساب‌های موقت سود و زیانی بسته شده و سند اختتامیه دوره صادر می‌گردد. لطفاً از صحت تمامی اسناد و عدم وجود سند معوق اطمینان حاصل فرمایید.
                </p>
            </div>

            <form action="index.php?page=admin&action=close_fiscal_year" method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small">تاریخ پایان سال مالی (سند اختتامیه): <span class="text-danger">*</span></label>
                    <input type="text" name="end_date" class="form-control rounded-3 font-monospace text-center fw-bold fs-5" value="<?php echo function_exists('jdate') ? jdate('Y/12/29') : date('Y-12-31'); ?>" required>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-danger rounded-pill px-5 py-2.5 fw-bold shadow-sm" onclick="return confirm('آیا از بستن سال مالی و صدور سند اختتامیه اطمینان قطعی دارید؟');">
                        <i class="fas fa-lock me-1"></i> تایید نهایی و بستن سال مالی
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>