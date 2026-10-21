<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/financial_reports.php - گزارشات مالی اصلی و ترازنامه
$trialBalance = $trialBalance ?? $data['trialBalance'] ?? [];
$balanceSheet = $balanceSheet ?? $data['balanceSheet'] ?? [];
?>

<style>
    .report-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
    }
    .table-report th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-report td {
        padding: 0.9rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-chart-pie text-indigo-600" style="color:#6366f1;"></i>
            <span>گزارشات مالی اصلی و ترازنامه</span>
        </h3>
        <p class="text-muted small mb-0">مشاهده تراز آزمایشی، ترازنامه و ساختار دارایی‌ها و تعهدات سازمان</p>
    </div>
</div>

<div class="card report-card mb-4 p-4">
    <form action="index.php?page=admin&action=financial_reports" method="POST" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-bold text-dark">از تاریخ:</label>
            <input type="text" name="start_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $startDateJalali ?? ''; ?>" required>
        </div>
        <div class="col-md-5">
            <label class="form-label small fw-bold text-dark">تا تاریخ:</label>
            <input type="text" name="end_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $endDateJalali ?? ''; ?>" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100 rounded-3 fw-bold">محاسبه تراز</button>
        </div>
    </form>
</div>

<div class="card report-card overflow-hidden mb-4">
    <div class="card-header bg-white border-bottom p-3.5">
        <h6 class="m-0 fw-bold text-dark">تراز آزمایشی حساب‌ها</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-report w-100">
                <thead>
                    <tr>
                        <th class="px-4">کد حساب</th>
                        <th>نام حساب</th>
                        <th class="text-center">گردش بدهکار</th>
                        <th class="text-center">گردش بستانکار</th>
                        <th class="text-center">مانده بدهکار</th>
                        <th class="text-center">مانده بستانکار</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($trialBalance)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">اطلاعات تراز آزمایشی موجود نیست.</td></tr>
                    <?php else: ?>
                        <?php foreach ($trialBalance as $tb): ?>
                            <tr>
                                <td class="px-4 font-monospace fw-bold text-indigo-600"><?php echo sanitize($tb->code); ?></td>
                                <td class="fw-bold text-dark"><?php echo sanitize($tb->name); ?></td>
                                <td class="text-center font-monospace"><?php echo number_format($tb->debit_sum ?? 0); ?></td>
                                <td class="text-center font-monospace"><?php echo number_format($tb->credit_sum ?? 0); ?></td>
                                <td class="text-center font-monospace fw-bold text-emerald-600"><?php echo number_format($tb->debit_balance ?? 0); ?></td>
                                <td class="text-center font-monospace fw-bold text-indigo-600"><?php echo number_format($tb->credit_balance ?? 0); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>