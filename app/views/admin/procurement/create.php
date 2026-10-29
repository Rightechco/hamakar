<?php
// app/views/admin/procurement/create.php - ثبت درخواست خرید
?>
<div class="container py-3" style="max-width:860px;">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-plus-circle text-primary me-2"></i>ثبت فرم درخواست خرید کالا
            </h5>
            <a href="index.php?page=admin&action=procurement_requisitions" class="btn btn-sm btn-outline-secondary rounded-3">
                <i class="fas fa-arrow-right me-1"></i> بازگشت
            </a>
        </div>
        <div class="card-body p-4">
            <form action="index.php?page=admin&action=store_requisition" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">کالای درخواستی:</label>
                        <select name="product_id" required class="form-select rounded-3">
                            <option value="">-- انتخاب کالا / ماده اولیه --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">تعداد یا مقدار درخواستی:</label>
                        <input type="number" step="0.01" name="quantity" value="1" required class="form-control rounded-3 font-monospace">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">انبار تحویل‌گیرنده:</label>
                        <select name="warehouse_id" required class="form-select rounded-3">
                            <option value="">-- انتخاب انبار مقصد --</option>
                            <?php foreach ($warehouses as $w): ?>
                                <option value="<?php echo $w->id; ?>"><?php echo htmlspecialchars($w->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">اولویت نیاز:</label>
                        <select name="priority" class="form-select rounded-3">
                            <option value="normal">عادی</option>
                            <option value="high">بالا</option>
                            <option value="urgent">فوری و بحرانی (خط متوقف)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">تاریخ مورد نیاز:</label>
                        <input type="text" name="needed_date" value="<?php echo date('Y/m/d'); ?>" class="form-control rounded-3 persian-datepicker">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">تخمین قیمت واحد (تومان):</label>
                        <input type="number" name="estimated_price" value="0" class="form-control rounded-3 font-monospace">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small text-secondary">علت درخواست / شرح مصرف:</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="توضیحاتی درباره علت نیاز به خرید این کالا..."></textarea>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">
                        <i class="fas fa-check me-1"></i> ثبت و ارسال به کارتابل مدیر تدارکات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
