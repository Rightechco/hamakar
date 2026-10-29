<?php
// app/views/client/statement.php - صورت‌حساب مالی تفصیلی مشتری در پورتال اختصاصی
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-file-invoice text-primary me-2"></i>صورت‌حساب مالی و گردش حساب
            </h3>
            <span class="text-muted small">مشاهده تاریخچه تمامی فاکتورها، پرداختی‌ها، وضعیت بدهکاری/بستانکاری و دانلود اسناد رسمی</span>
        </div>
        <button class="btn btn-primary rounded-3 px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
            <i class="fas fa-receipt me-1"></i> ثبت فیش واریزی جدید
        </button>
    </div>

    <!-- Balance Summary -->
    <?php
        $totalInvoices = 0;
        if (!empty($invoices)) {
            foreach ($invoices as $inv) {
                $totalInvoices += $inv->total_amount ?? 0;
            }
        }
        $totalPayments = 0;
        if (!empty($payments)) {
            foreach ($payments as $p) {
                $totalPayments += $p->amount ?? 0;
            }
        }
        $balance = $totalInvoices - $totalPayments;
    ?>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block">مجموع خریدها و فاکتورها</small>
                <h4 class="fw-bold mb-0 font-monospace text-dark"><?php echo number_format($totalInvoices); ?> <small style="font-size:0.75rem;">تومان</small></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <small class="text-muted d-block">مجموع مبالغ واریز شده</small>
                <h4 class="fw-bold mb-0 font-monospace text-success"><?php echo number_format($totalPayments); ?> <small style="font-size:0.75rem;">تومان</small></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-4 border-<?php echo ($balance > 0) ? 'danger' : 'success'; ?>">
                <small class="text-muted d-block">مانده حساب نهایی</small>
                <h4 class="fw-bold mb-0 font-monospace text-<?php echo ($balance > 0) ? 'danger' : 'success'; ?>">
                    <?php echo number_format(abs($balance)); ?> <small style="font-size:0.75rem;">تومان (<?php echo ($balance > 0) ? 'بدهکار' : 'تسویه/بستانکار'; ?>)</small>
                </h4>
            </div>
        </div>
    </div>

    <!-- Invoices List -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="fw-bold mb-0 text-dark">ریز فاکتورهای رسمی و صادره</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th>شماره فاکتور</th>
                        <th>تاریخ صدور</th>
                        <th>سررسید</th>
                        <th>مبلغ کل</th>
                        <th>پرداخت شده</th>
                        <th>وضعیت</th>
                        <th class="text-center">چاپ رسمی</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <tr><td colspan="7" class="text-center py-3 text-muted">فاکتوری یافت نشد.</td></tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td class="font-monospace fw-bold"><?php echo htmlspecialchars($inv->invoice_number ?? $inv->id); ?></td>
                                <td class="font-monospace"><?php echo htmlspecialchars($inv->issue_date ?? '-'); ?></td>
                                <td class="font-monospace"><?php echo htmlspecialchars($inv->due_date ?? '-'); ?></td>
                                <td class="font-monospace fw-bold"><?php echo number_format($inv->total_amount ?? 0); ?> تومان</td>
                                <td class="font-monospace text-success"><?php echo number_format($inv->paid_amount ?? 0); ?> تومان</td>
                                <td>
                                    <?php if (($inv->status ?? '') === 'paid'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">پرداخت کامل</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2">معوق / نیمه‌پرداخت</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?page=client&action=view_invoice&id=<?php echo $inv->id; ?>" class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1">
                                        <i class="fas fa-print me-1"></i> مشاهده و پرینت
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Upload Payment Proof -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="index.php?page=client&action=submit_payment_proof" method="POST" enctype="multipart/form-data" class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">ثبت فیش واریزی یا شماره پیگیری</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">مبلغ واریزی (تومان):</label>
                    <input type="number" name="amount" required class="form-control rounded-3 font-monospace">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">شماره پیگیری / ارجاع بانکی:</label>
                    <input type="text" name="tracking_code" required class="form-control rounded-3 font-monospace" placeholder="شماره ارجاع واریز">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">آپلود عکس فیش واریز:</label>
                    <input type="file" name="receipt_file" class="form-control rounded-3" accept="image/*,.pdf">
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">انصراف</button>
                <button type="submit" class="btn btn-primary rounded-3 px-4">ثبت فیش جهت تأیید مالی</button>
            </div>
        </form>
    </div>
</div>
