<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/vat_report.php - گزارش مالیات بر ارزش افزوده
$reportData = $reportData ?? $data['reportData'] ?? [];
?>

<style>
    .vat-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-percent text-purple-600" style="color:#7e22ce;"></i>
            <span>گزارش مالیات بر ارزش افزوده (VAT)</span>
        </h3>
        <p class="text-muted small mb-0">محاسبه مالیات و عوارض ارزش افزوده خرید و فروش برای اظهارنامه فصلی</p>
    </div>
</div>

<div class="card vat-card mb-4 p-4">
    <form action="index.php?page=admin&action=vat_report" method="POST" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-bold text-dark">از تاریخ:</label>
            <input type="text" name="start_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $startDateJalali ?? ''; ?>" required>
        </div>
        <div class="col-md-5">
            <label class="form-label small fw-bold text-dark">تا تاریخ:</label>
            <input type="text" name="end_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $endDateJalali ?? ''; ?>" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-purple w-100 rounded-3 fw-bold" style="background:#7e22ce; color:#fff;">محاسبه ارزش افزوده</button>
        </div>
    </form>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card vat-card p-4">
            <h6 class="fw-bold text-dark mb-3">مالیات و عوارض اعتبار خرید (نهاده)</h6>
            <div class="font-monospace fs-4 fw-black text-indigo-600"><?php echo number_format($reportData['purchase_vat'] ?? 0); ?> تومان</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card vat-card p-4">
            <h6 class="fw-bold text-dark mb-3">مالیات و عوارض خروجی فروش</h6>
            <div class="font-monospace fs-4 fw-black text-rose-600"><?php echo number_format($reportData['sales_vat'] ?? 0); ?> تومان</div>
        </div>
    </div>
</div>