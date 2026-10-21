<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/profit_loss.php - گزارش سود و زیان
$reportData = $reportData ?? $data['reportData'] ?? [];
$startDateJalali = $startDateJalali ?? $data['startDateJalali'] ?? '';
$endDateJalali = $endDateJalali ?? $data['endDateJalali'] ?? '';
?>

<style>
    .pnl-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
    }
    .pnl-row {
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-line-chart text-emerald-600" style="color:#059669;"></i>
            <span>گزارش سود و زیان دوره مالی</span>
        </h3>
        <p class="text-muted small mb-0">محاسبه درآمدهای عملیاتی، هزینه‌های جاری و سود یا زیان خالص سازمان</p>
    </div>
</div>

<div class="card pnl-card mb-4 p-4">
    <form action="index.php?page=admin&action=profitAndLossReport" method="POST" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-bold text-dark">از تاریخ:</label>
            <input type="text" name="start_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $startDateJalali; ?>" required>
        </div>
        <div class="col-md-5">
            <label class="form-label small fw-bold text-dark">تا تاریخ:</label>
            <input type="text" name="end_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $endDateJalali; ?>" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-emerald w-100 rounded-3 fw-bold" style="background:#059669; color:#fff;">محاسبه سود/زیان</button>
        </div>
    </form>
</div>

<div class="card pnl-card overflow-hidden">
    <div class="card-header bg-white border-bottom p-3.5">
        <h6 class="m-0 fw-bold text-dark">صورت سود و زیان انباشته</h6>
    </div>

    <div class="card-body p-0">
        <div class="pnl-row bg-light fw-bold text-dark">
            <span>۱. درآمدهای عملیاتی و فروش</span>
            <span class="font-monospace text-emerald-600" style="color:#059669;"><?php echo number_format($reportData['total_income'] ?? 0); ?> تومان</span>
        </div>
        <div class="pnl-row bg-light fw-bold text-dark">
            <span>۲. هزینه‌های عمومی و اداری</span>
            <span class="font-monospace text-rose-600" style="color:#e11d48;"><?php echo number_format($reportData['total_expense'] ?? 0); ?> تومان</span>
        </div>
        <div class="pnl-row p-4 text-center justify-content-center bg-indigo-50" style="background:#e0e7ff;">
            <div class="fs-5 fw-extrabold text-dark">
                سود (زیان) خالص دوره: 
                <span class="font-monospace ms-2 text-indigo-600" style="color:#4338ca;">
                    <?php echo number_format(($reportData['total_income'] ?? 0) - ($reportData['total_expense'] ?? 0)); ?> تومان
                </span>
            </div>
        </div>
    </div>
</div>