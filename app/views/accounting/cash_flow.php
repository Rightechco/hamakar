<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/cash_flow.php - صورت جریان وجوه نقد
$reportData = $reportData ?? $data['reportData'] ?? [];
?>

<style>
    .cashflow-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-money-bill-transfer text-emerald-600" style="color:#059669;"></i>
            <span>صورت جریان وجوه نقد</span>
        </h3>
        <p class="text-muted small mb-0">تحلیل جریان ورود و خروج نقدینگی در فعالیت‌های عملیاتی و سرمایه‌گذاری</p>
    </div>
</div>

<div class="card cashflow-card mb-4 p-4">
    <form action="index.php?page=admin&action=cash_flow_report" method="POST" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-bold text-dark">از تاریخ:</label>
            <input type="text" name="start_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $startDateJalali ?? ''; ?>" required>
        </div>
        <div class="col-md-5">
            <label class="form-label small fw-bold text-dark">تا تاریخ:</label>
            <input type="text" name="end_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $endDateJalali ?? ''; ?>" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-emerald text-white w-100 rounded-3 fw-bold" style="background:#059669;">محاسبه جریان</button>
        </div>
    </form>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card cashflow-card p-4 text-center">
            <span class="text-muted small fw-bold mb-1 d-block">مجموع ورودی نقدینگی</span>
            <h4 class="fw-black text-emerald-600 font-monospace mb-0" style="color:#059669;"><?php echo number_format($reportData['total_inflow'] ?? 0); ?> تومان</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card cashflow-card p-4 text-center">
            <span class="text-muted small fw-bold mb-1 d-block">مجموع خروجی نقدینگی</span>
            <h4 class="fw-black text-rose-600 font-monospace mb-0" style="color:#e11d48;"><?php echo number_format($reportData['total_outflow'] ?? 0); ?> تومان</h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card cashflow-card p-4 text-center">
            <span class="text-muted small fw-bold mb-1 d-block">خالص جریان نقدینگی</span>
            <h4 class="fw-black text-indigo-600 font-monospace mb-0" style="color:#4338ca;"><?php echo number_format($reportData['net_cash_flow'] ?? 0); ?> تومان</h4>
        </div>
    </div>
</div>