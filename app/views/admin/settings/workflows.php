<?php
// app/views/admin/settings/workflows.php - موتور اتوماسیون گردش کار و قوانین شرطی
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-network-wired text-primary me-2"></i>موتور اتوماسیون گردش کار و قوانین شرطی
            </h3>
            <span class="text-muted small">تعریف سناریوهای خودکار سازی (Trigger & Action) برای ارسال پیامک، ایجاد وظایف و اطلاع‌رسانی</span>
        </div>
        <button class="btn btn-primary rounded-3 px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#workflowModal">
            <i class="fas fa-plus me-1"></i> تعریف قانون جدید
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th>عنوان قانون</th>
                        <th>رویداد محرک (Event Trigger)</th>
                        <th>شرط اجرایی</th>
                        <th>اقدام خودکار (Action)</th>
                        <th>وضعیت</th>
                        <th class="text-center">فعال / غیرفعال</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rules)): ?>
                        <tr><td colspan="6" class="text-center py-4 text-muted">هیچ قانون اتوماسیونی تعریف نشده است.</td></tr>
                    <?php else: ?>
                        <?php foreach ($rules as $r): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($r->title); ?></td>
                                <td>
                                    <?php
                                        $triggers = [
                                            'invoice_overdue' => 'سررسید فاکتور و معوق شدن',
                                            'cheque_due_soon' => '۳ روز مانده به سررسید چک',
                                            'low_stock' => 'رسیدن موجودی کالا به نقطه سفارش',
                                            'new_lead' => 'ثبت سرنخ یا مشتری جدید در CRM'
                                        ];
                                        echo $triggers[$r->event_trigger] ?? $r->event_trigger;
                                    ?>
                                </td>
                                <td class="font-monospace text-muted">
                                    <?php echo htmlspecialchars(($r->condition_field ?: 'پیش‌فرض') . ' ' . $r->condition_operator . ' ' . $r->condition_value); ?>
                                </td>
                                <td>
                                    <?php
                                        $actions = [
                                            'send_sms' => '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2"><i class="fas fa-comment-sms me-1"></i> ارسال پیامک خودکار</span>',
                                            'create_task' => '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2"><i class="fas fa-list-check me-1"></i> ایجاد تسک خودکار در پروژه</span>',
                                            'send_notification' => '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2"><i class="fas fa-bell me-1"></i> اعلان سیستمی</span>'
                                        ];
                                        echo $actions[$r->action_type] ?? $r->action_type;
                                    ?>
                                </td>
                                <td>
                                    <?php if ($r->is_active): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">فعال</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2">غیرفعال</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?page=admin&action=toggle_workflow_rule&id=<?php echo $r->id; ?>" class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1">
                                        <?php echo $r->is_active ? 'غیرفعال‌سازی' : 'فعال‌سازی'; ?>
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

<!-- Modal Workflow Rule -->
<div class="modal fade" id="workflowModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="index.php?page=admin&action=store_workflow_rule" method="POST" class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">تعریف سناریوی اتوماسیون گردش کار</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">عنوان سناریو:</label>
                    <input type="text" name="title" required class="form-control rounded-3" placeholder="مثال: یادآوری پیامکی چک ۳ روز قبل از سررسید">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">رویداد محرک (اگر این اتفاق افتاد...):</label>
                    <select name="event_trigger" class="form-select rounded-3">
                        <option value="cheque_due_soon">۳ روز مانده به موعد چک دریافتی</option>
                        <option value="invoice_overdue">فاکتور مشتری معوق شد و پرداخت نگردید</option>
                        <option value="low_stock">موجودی فیزیکی کالایی زیر نقطه سفارش رفت</option>
                        <option value="new_lead">سرنخ یا مشتری جدید در CRM تعریف شد</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">اقدام اتوماتیک (آنگاه این کار را انجام بده):</label>
                    <select name="action_type" class="form-select rounded-3">
                        <option value="send_sms">ارسال پیامک با خط خدماتی به مشتری / مدیر</option>
                        <option value="create_task">ایجاد خودکار وظیفه (Task) برای کارشناس پیگیری</option>
                        <option value="send_notification">ارسال نوتیفیکیشن هشدار در پنل و اپ Winbo</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">متن پیام یا شرح اقدام:</label>
                    <textarea name="action_payload" class="form-control rounded-3" rows="2" placeholder="متن پیامک یا شرح وظیفه..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">انصراف</button>
                <button type="submit" class="btn btn-primary rounded-3 px-4">ایجاد و فعال‌سازی قانون</button>
            </div>
        </form>
    </div>
</div>
