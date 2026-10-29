<?php
// app/views/admin/manufacturing/create_order.php - صدور دستور تولید
?>
<div class="container py-3" style="max-width:860px;">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-plus-circle text-primary me-2"></i>صدور دستور تولید جدید
            </h5>
            <a href="index.php?page=admin&action=production_orders" class="btn btn-sm btn-outline-secondary rounded-3">
                <i class="fas fa-arrow-right me-1"></i> بازگشت
            </a>
        </div>
        <div class="card-body p-4">
            <form action="index.php?page=admin&action=store_production_order" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">فرمول ساخت مبنا (BOM):</label>
                        <select name="bom_id" required class="form-select rounded-3">
                            <option value="">-- انتخاب فرمول ساخت کالا --</option>
                            <?php foreach ($boms as $b): ?>
                                <option value="<?php echo $b->id; ?>" <?php echo (isset($_GET['bom_id']) && $_GET['bom_id'] == $b->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($b->title . ' (' . ($b->product_name ?? '') . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">تیراژ برنامه‌ریزی‌شده جهت تولید:</label>
                        <input type="number" step="0.01" name="planned_qty" value="10" required class="form-control rounded-3 font-monospace">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">انبار مبدا (خروج مواد اولیه):</label>
                        <select name="raw_warehouse_id" required class="form-select rounded-3">
                            <option value="">-- انتخاب انبار مواد اولیه --</option>
                            <?php foreach ($warehouses as $w): ?>
                                <option value="<?php echo $w->id; ?>"><?php echo htmlspecialchars($w->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">انبار مقصد (ورود محصول تولیدی):</label>
                        <select name="finished_warehouse_id" required class="form-select rounded-3">
                            <option value="">-- انتخاب انبار محصول نهایی --</option>
                            <?php foreach ($warehouses as $w): ?>
                                <option value="<?php echo $w->id; ?>"><?php echo htmlspecialchars($w->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">تاریخ شروع فرآیند:</label>
                        <input type="text" name="start_date" value="<?php echo date('Y/m/d'); ?>" class="form-control rounded-3 persian-datepicker">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">توضیحات و شیفت کاری:</label>
                        <input type="text" name="notes" class="form-control rounded-3" placeholder="یادداشت‌های خط تولید">
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">
                        <i class="fas fa-check me-1"></i> صدور دستور تولید
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
