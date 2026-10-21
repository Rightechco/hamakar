<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/expense_analysis.php - آنالیز هزینه‌ها
$reportData = $reportData ?? $data['reportData'] ?? [];
?>

<style>
    .analysis-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-chart-pie text-rose-600" style="color:#e11d48;"></i>
            <span>آنالیز و تحلیل سهم هزینه‌ها</span>
        </h3>
        <p class="text-muted small mb-0">بررسی سهم درصدی مرکز هزینه‌ها و شناسایی نقاط تمرکز خروج منابع مالی</p>
    </div>
</div>

<div class="card analysis-card mb-4 p-4">
    <form action="index.php?page=admin&action=expense_analysis_report" method="POST" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-bold text-dark">از تاریخ:</label>
            <input type="text" name="start_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $startDateJalali ?? ''; ?>" required>
        </div>
        <div class="col-md-5">
            <label class="form-label small fw-bold text-dark">تا تاریخ:</label>
            <input type="text" name="end_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $endDateJalali ?? ''; ?>" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-rose text-white w-100 rounded-3 fw-bold" style="background:#e11d48;">تحلیل هزینه‌ها</button>
        </div>
    </form>
</div>