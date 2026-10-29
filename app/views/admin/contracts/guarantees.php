<?php
// app/views/admin/contracts/guarantees.php - مدیریت تضامین، چک‌های امانت و ضمانت‌نامه‌های بانکی قراردادها
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-shield-halved text-primary me-2"></i>مدیریت تضامین و ضمانت‌نامه‌های قراردادها
            </h3>
            <span class="text-muted small">ردیابی ضمانت‌نامه‌های بانکی حسن انجام کار، پیش‌پرداخت، سفته‌ها و اعلام هشدار سررسید تمدید</span>
        </div>
        <button class="btn btn-primary rounded-3 px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#guaranteeModal">
            <i class="fas fa-plus me-1"></i> ثبت ضمانت‌نامه جدید
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th>شماره ضمانت‌نامه / سند</th>
                        <th>قرارداد متصل</th>
                        <th>طرف قرارداد</th>
                        <th>نوع ضمانت</th>
                        <th>بانک صادرکننده</th>
                        <th>مبلغ ضمانت (تومان)</th>
                        <th>تاریخ سررسید انقضا</th>
                        <th>موضوع ضمانت</th>
                        <th>وضعیت</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($guarantees)): ?>
                        <tr><td colspan="10" class="text-center py-4 text-muted">هیچ ضمانت‌نامه‌ای ثبت نشده است.</td></tr>
                    <?php else: ?>
                        <?php foreach ($guarantees as $g): ?>
                            <tr>
                                <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($g->guarantee_number); ?></td>
                                <td><?php echo htmlspecialchars($g->contract_title ?? 'قرارداد نامشخص'); ?></td>
                                <td><?php echo htmlspecialchars($g->client_name ?? '-'); ?></td>
                                <td>
                                    <?php 
                                        $typeNames = [
                                            'bank_guarantee' => 'ضمانت‌نامه بانکی',
                                            'promissory_note' => 'سفته تضامین',
                                            'cheque' => 'چک ضمانت',
                                            'cash' => 'سپرده نقدی'
                                        ];
                                        echo $typeNames[$g->type] ?? $g->type;
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($g->issuer_bank ?: '-'); ?></td>
                                <td class="font-monospace fw-bold text-primary"><?php echo number_format($g->amount); ?></td>
                                <td class="font-monospace text-danger fw-bold"><?php echo htmlspecialchars($g->expiry_date); ?></td>
                                <td>
                                    <?php
                                        $purposes = [
                                            'performance' => 'حسن انجام تعهدات',
                                            'advance_payment' => 'پیش‌پرداخت',
                                            'tender' => 'شرکت در مناقصه',
                                            'retention' => 'حسن انجام کار (کسورات)'
                                        ];
                                        echo $purposes[$g->purpose] ?? $g->purpose;
                                    ?>
                                </td>
                                <td>
                                    <?php if ($g->status === 'active'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">معتبر و فعال</span>
                                    <?php elseif ($g->status === 'released'): ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2">آزاد و عودت شده</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border rounded-pill px-2">ضبط شده</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($g->status === 'active'): ?>
                                        <a href="index.php?page=admin&action=update_guarantee_status&id=<?php echo $g->id; ?>&status=released" class="btn btn-sm btn-outline-success rounded-3 px-2 py-1" onclick="return confirm('آیا از آزادسازی و عودت این ضمانت‌نامه اطمینان دارید؟')">
                                            <i class="fas fa-unlock me-1"></i> آزادسازی
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create Guarantee -->
<div class="modal fade" id="guaranteeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="index.php?page=admin&action=store_guarantee" method="POST" class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">ثبت ضمانت‌نامه یا تضامین قرارداد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">قرارداد مربوطه:</label>
                    <select name="contract_id" required class="form-select rounded-3">
                        <option value="">-- انتخاب قرارداد --</option>
                        <?php foreach ($contracts as $cnt): ?>
                            <option value="<?php echo $cnt->id; ?>"><?php echo htmlspecialchars($cnt->title); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">نوع سند ضمانت:</label>
                    <select name="type" class="form-select rounded-3">
                        <option value="bank_guarantee">ضمانت‌نامه رسمی بانکی</option>
                        <option value="promissory_note">سفته امانی حسن انجام کار</option>
                        <option value="cheque">چک ضمانت صیادی</option>
                        <option value="cash">سپرده نقدی مسدودی</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">شماره ضمانت‌نامه / شماره سند:</label>
                    <input type="text" name="guarantee_number" required class="form-control rounded-3 font-monospace">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">بانک / مرجع صادرکننده:</label>
                    <input type="text" name="issuer_bank" class="form-control rounded-3" placeholder="مثال: بانک تجارت شعبه مرکزی">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">مبلغ تضمین (تومان):</label>
                    <input type="number" name="amount" required class="form-control rounded-3 font-monospace">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">تاریخ صدور:</label>
                        <input type="text" name="issue_date" value="<?php echo date('Y/m/d'); ?>" class="form-control rounded-3 persian-datepicker">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">تاریخ انقضا:</label>
                        <input type="text" name="expiry_date" required class="form-control rounded-3 persian-datepicker">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">موضوع ضمانت:</label>
                    <select name="purpose" class="form-select rounded-3">
                        <option value="performance">حسن انجام تعهدات</option>
                        <option value="advance_payment">پیش‌پرداخت</option>
                        <option value="tender">شرکت در مناقصه</option>
                        <option value="retention">کسورات حسن انجام کار</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">انصراف</button>
                <button type="submit" class="btn btn-primary rounded-3 px-4">ثبت ضمانت‌نامه</button>
            </div>
        </form>
    </div>
</div>
