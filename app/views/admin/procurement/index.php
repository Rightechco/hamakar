<?php
// app/views/admin/procurement/index.php - مدیریت تدارکات و درخواست‌های خرید
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-truck-ramp-box text-primary me-2"></i>مدیریت تدارکات و درخواست‌های خرید
            </h3>
            <span class="text-muted small">فرآیند درخواست خرید از انبارها و دپارتمان‌ها، تأیید مالی و صدور فاکتور خرید</span>
        </div>
        <a href="index.php?page=admin&action=create_requisition" class="btn btn-primary rounded-3 px-3 shadow-sm">
            <i class="fas fa-plus me-1"></i> ثبت درخواست خرید جدید
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th>شماره درخواست</th>
                        <th>درخواست‌دهنده</th>
                        <th>کالا / قطعه موردنیاز</th>
                        <th>تعداد درخواستی</th>
                        <th>انبار تحویل‌گیرنده</th>
                        <th>اولویت</th>
                        <th>تاریخ نیاز</th>
                        <th>وضعیت</th>
                        <th class="text-center">اقدام مدیر</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requisitions)): ?>
                        <tr><td colspan="9" class="text-center py-4 text-muted">هیچ درخواست خریدی ثبت نشده است.</td></tr>
                    <?php else: ?>
                        <?php foreach ($requisitions as $r): ?>
                            <tr>
                                <td class="font-monospace fw-bold"><?php echo htmlspecialchars($r->req_number); ?></td>
                                <td><?php echo htmlspecialchars($r->requester_name ?? 'پرسنل'); ?></td>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($r->product_name); ?></td>
                                <td class="font-monospace"><?php echo $r->quantity; ?></td>
                                <td><?php echo htmlspecialchars($r->warehouse_name ?? 'انبار مرکزی'); ?></td>
                                <td>
                                    <?php if ($r->priority === 'urgent'): ?>
                                        <span class="badge bg-danger text-danger bg-opacity-10 border border-danger border-opacity-25 rounded-pill px-2">فوری و بحرانی</span>
                                    <?php elseif ($r->priority === 'high'): ?>
                                        <span class="badge bg-warning text-warning bg-opacity-10 border border-warning border-opacity-25 rounded-pill px-2">اولویت بالا</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-secondary border rounded-pill px-2">عادی</span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-monospace"><?php echo htmlspecialchars($r->needed_date); ?></td>
                                <td>
                                    <?php if ($r->status === 'approved'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">تأیید شده برای خرید</span>
                                    <?php elseif ($r->status === 'pending'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2">در انتظار تأیید</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2"><?php echo $r->status; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($r->status === 'pending'): ?>
                                        <a href="index.php?page=admin&action=approve_requisition&id=<?php echo $r->id; ?>&status=approved" class="btn btn-sm btn-success rounded-3 px-2 py-1">
                                            <i class="fas fa-check me-1"></i> تأیید
                                        </a>
                                        <a href="index.php?page=admin&action=approve_requisition&id=<?php echo $r->id; ?>&status=rejected" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1">
                                            رد
                                        </a>
                                    <?php else: ?>
                                        <a href="index.php?page=purchases&action=create" class="btn btn-sm btn-outline-primary rounded-3 px-2 py-1">
                                            <i class="fas fa-cart-shopping me-1"></i> ثبت فاکتور خرید
                                        </a>
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
