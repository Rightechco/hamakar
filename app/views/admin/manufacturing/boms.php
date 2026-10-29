<?php
// app/views/admin/manufacturing/boms.php - فرمول‌های ساخت کالا (BOM)
?>
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-sitemap text-primary me-2"></i>فرمول‌های ساخت محصول (BOM)
            </h3>
            <span class="text-muted small">تعریف درخت مواد اولیه، ضرایب ضایعات، برآورد دستمزد و سربار تولید</span>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?page=admin&action=production_orders" class="btn btn-outline-primary rounded-3 px-3">
                <i class="fas fa-industry me-1"></i> دستورات تولید
            </a>
            <a href="index.php?page=admin&action=create_bom" class="btn btn-primary rounded-3 px-3 shadow-sm">
                <i class="fas fa-plus me-1"></i> تعریف BOM جدید
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th>عنوان فرمول ساخت</th>
                        <th>محصول نهایی</th>
                        <th>تیراژ استاندارد</th>
                        <th>برآورد دستمزد مستقیم</th>
                        <th>برآورد سربار</th>
                        <th>وضعیت</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($boms)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-industry fa-2x mb-2 d-block"></i>
                                هیچ فرمول ساختی تعریف نشده است. جهت محاسبه بهای تمام‌شده و کسر خودکار مواد از انبار، اولین فرمول BOM را بسازید.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($boms as $b): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($b->title); ?></td>
                                <td><?php echo htmlspecialchars($b->product_name ?? 'محصول نامشخص'); ?></td>
                                <td class="font-monospace"><?php echo $b->standard_qty; ?> عدد</td>
                                <td class="font-monospace"><?php echo number_format($b->estimated_labor_cost); ?> تومان</td>
                                <td class="font-monospace"><?php echo number_format($b->estimated_overhead_cost); ?> تومان</td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2">فعال</span>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?page=admin&action=create_production_order&bom_id=<?php echo $b->id; ?>" class="btn btn-sm btn-primary rounded-3 px-2 py-1" title="صدور دستور تولید">
                                        <i class="fas fa-play me-1"></i> صدور دستور تولید
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
