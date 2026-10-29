<?php
// app/views/admin/treasury/rasgiri.php - ماشین حساب راس‌گیری وزنی چک‌ها و فاکتورها
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-calculator text-primary me-2"></i>ماشین‌حساب راس‌گیری وزنی اسناد
            </h3>
            <span class="text-muted small">محاسبه دقیق میانگین وزنی تاریخ سررسید چک‌ها جهت تسویه عادلانه و محاسبه سود/خسارت تأخیر</span>
        </div>
        <a href="index.php?page=admin&action=treasury_cheques" class="btn btn-outline-secondary rounded-3">
            <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست چک‌ها
        </a>
    </div>

    <div class="row g-4">
        <!-- Select Cheques Form -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size:1rem;">انتخاب چک‌های دریافتی برای راس‌گیری</h5>
                </div>
                <div class="card-body p-3">
                    <form action="index.php?page=admin&action=treasury_rasgiri" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">تاریخ مبدا محاسبات (روز پایه):</label>
                            <input type="text" name="base_date" value="<?php echo date('Y/m/d'); ?>" class="form-control rounded-3 persian-datepicker w-50">
                        </div>

                        <div class="table-responsive mb-3" style="max-height:380px;">
                            <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width:40px;"><input type="checkbox" id="checkAll" onclick="document.querySelectorAll('.ch-select').forEach(c => c.checked = this.checked)"></th>
                                        <th>صاحب حساب</th>
                                        <th>بانک</th>
                                        <th>مبلغ (تومان)</th>
                                        <th>سررسید</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($cheques)): ?>
                                        <tr><td colspan="5" class="text-center text-muted py-3">چک در جریان وصولی موجود نیست.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($cheques as $ch): ?>
                                            <tr>
                                                <td><input type="checkbox" name="selected_cheques[]" value="<?php echo $ch->id; ?>" class="ch-select"></td>
                                                <td><?php echo htmlspecialchars($ch->drawer_name ?: ($ch->client_name ?? '-')); ?></td>
                                                <td><?php echo htmlspecialchars($ch->bank_name); ?></td>
                                                <td class="font-monospace fw-bold"><?php echo number_format($ch->amount); ?></td>
                                                <td class="font-monospace"><?php echo htmlspecialchars($ch->due_date); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm w-100">
                            <i class="fas fa-play me-1"></i> محاسبه آنی راس چک‌های انتخابی
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Result Box -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size:1rem;">نتیجه محاسبات راس‌گیری</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <?php if ($rasResult): ?>
                        <div class="rounded-4 p-4 mb-3" style="background:#eef2ff; border:1px solid #c7d2fe;">
                            <span class="text-muted d-block small mb-1">تاریخ راس وزنی نهایی:</span>
                            <h2 class="fw-bold text-primary mb-0 font-monospace" style="letter-spacing:1px;">
                                <?php echo $rasResult['ras_date']; ?>
                            </h2>
                            <span class="badge bg-primary rounded-pill px-3 py-1 mt-2">
                                میانگین <?php echo $rasResult['average_days']; ?> روز از تاریخ مبدا
                            </span>
                        </div>

                        <div class="d-flex justify-content-between p-2 border-bottom">
                            <span class="text-muted small">تعداد چک‌ها:</span>
                            <strong class="font-monospace"><?php echo $rasResult['items_count']; ?> فقره</strong>
                        </div>
                        <div class="d-flex justify-content-between p-2 border-bottom">
                            <span class="text-muted small">مجموع مبالغ:</span>
                            <strong class="font-monospace text-dark"><?php echo number_format($rasResult['total_amount']); ?> تومان</strong>
                        </div>
                        <div class="alert alert-success mt-3 small text-start rounded-3 mb-0">
                            <i class="fas fa-check-circle me-1"></i>
                            در صورت تسویه نقدی یا صدور فاکتور متناظر، سررسید تراز مالی برابر با <strong><?php echo $rasResult['ras_date']; ?></strong> می‌باشد.
                        </div>
                    <?php else: ?>
                        <div class="py-5 text-muted">
                            <i class="fas fa-scale-balanced fa-3x mb-3 text-secondary text-opacity-50"></i>
                            <p class="mb-0">چک‌های مورد نظر را از جدول سمت راست تیک زده و دکمه محاسبه را بزنید تا تاریخ راس، میانگین روزها و ارزش وزنی نمایش داده شود.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
