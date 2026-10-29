<?php
// app/views/admin/manufacturing/orders.php - دستورات تولید و بهای تمام‌شده
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-industry text-primary me-2"></i>دستورات تولید و بهای تمام‌شده
            </h3>
            <span class="text-muted small">ردیابی چرخه تولید، صدور حواله مصرف انبار، ثبت ورود محصول نهایی و بهای تمام‌شده واحد</span>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?page=admin&action=boms" class="btn btn-outline-secondary rounded-3 px-3">
                <i class="fas fa-sitemap me-1"></i> فرمول‌های ساخت (BOM)
            </a>
            <a href="index.php?page=admin&action=create_production_order" class="btn btn-primary rounded-3 px-3 shadow-sm">
                <i class="fas fa-plus me-1"></i> صدور دستور تولید جدید
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th>شماره دستور</th>
                        <th>فرمول ساخت (BOM)</th>
                        <th>محصول نهایی</th>
                        <th>تیراژ مصوب / واقعی</th>
                        <th>بهای تمام‌شده واحد</th>
                        <th>جمع کل هزینه</th>
                        <th>وضعیت</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">دستور تولیدی ثبت نشده است.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $ord): ?>
                            <tr>
                                <td class="font-monospace fw-bold"><?php echo htmlspecialchars($ord->order_number); ?></td>
                                <td><?php echo htmlspecialchars($ord->bom_title); ?></td>
                                <td><?php echo htmlspecialchars($ord->product_name); ?></td>
                                <td class="font-monospace">
                                    <?php echo $ord->planned_qty; ?> / <strong><?php echo $ord->actual_qty; ?></strong>
                                </td>
                                <td class="font-monospace text-primary fw-bold">
                                    <?php echo ($ord->unit_cost > 0) ? number_format($ord->unit_cost) . ' تومان' : '-'; ?>
                                </td>
                                <td class="font-monospace">
                                    <?php echo ($ord->total_cost > 0) ? number_format($ord->total_cost) . ' تومان' : '-'; ?>
                                </td>
                                <td>
                                    <?php if ($ord->status === 'completed'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">تکمیل شده</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2">در جریان ساخت</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($ord->status !== 'completed'): ?>
                                        <button class="btn btn-sm btn-success rounded-3 px-2 py-1" onclick="openCompleteModal(<?php echo $ord->id; ?>, <?php echo $ord->planned_qty; ?>)">
                                            <i class="fas fa-check me-1"></i> تکمیل و ثبت به انبار
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="fas fa-circle-check text-success"></i> انبار شد</span>
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

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="completeForm" method="POST" class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">تکمیل تولید و محاسبه بهای تمام‌شده</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small">تیراژ واقعی تولید شده:</label>
                    <input type="number" step="0.01" name="actual_qty" id="modalActualQty" required class="form-control rounded-3 font-monospace">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">هزینه دستمزد مستقیم واقعی (تومان):</label>
                    <input type="number" name="actual_labor_cost" value="0" class="form-control rounded-3 font-monospace">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">هزینه سربار واقعی خط تولید (تومان):</label>
                    <input type="number" name="actual_overhead_cost" value="0" class="form-control rounded-3 font-monospace">
                </div>
                <small class="text-muted">با ثبت این فرم، مواد اولیه طبق BOM از انبار مبدا کسر و محصول نهایی به انبار مقصد اضافه می‌گردد.</small>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">انصراف</button>
                <button type="submit" class="btn btn-success rounded-3 px-4">تکمیل نهایی و سند انبار</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCompleteModal(id, plannedQty) {
    document.getElementById('completeForm').action = 'index.php?page=admin&action=complete_production_order&id=' + id;
    document.getElementById('modalActualQty').value = plannedQty;
    new bootstrap.Modal(document.getElementById('completeModal')).show();
}
</script>
