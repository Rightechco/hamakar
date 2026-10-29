<?php
// app/views/admin/manufacturing/create_bom.php - ایجاد فرمول ساخت BOM
?>
<div class="container py-3" style="max-width:960px;">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-sitemap text-primary me-2"></i>تعریف فرمول ساخت جدید (BOM)
            </h5>
            <a href="index.php?page=admin&action=boms" class="btn btn-sm btn-outline-secondary rounded-3">
                <i class="fas fa-arrow-right me-1"></i> بازگشت
            </a>
        </div>
        <div class="card-body p-4">
            <form action="index.php?page=admin&action=store_bom" method="POST">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">عنوان فرمول ساخت:</label>
                        <input type="text" name="title" required class="form-control rounded-3" placeholder="مثال: فرمول ساخت موتور سه فاز ۱۵KW">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">کالای نهایی حاصل از تولید:</label>
                        <select name="product_id" required class="form-select rounded-3">
                            <option value="">-- انتخاب محصول نهایی --</option>
                            <?php foreach ($products as $p): ?>
                                <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-secondary">تیراژ مبنا (تعداد استاندارد):</label>
                        <input type="number" step="0.01" name="standard_qty" value="1" required class="form-control rounded-3 font-monospace">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-secondary">برآورد دستمزد مستقیم (تومان):</label>
                        <input type="number" name="estimated_labor_cost" value="0" class="form-control rounded-3 font-monospace">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-secondary">برآورد سربار ساخت (تومان):</label>
                        <input type="number" name="estimated_overhead_cost" value="0" class="form-control rounded-3 font-monospace">
                    </div>
                </div>

                <!-- Raw Materials Table -->
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-boxes-stacked text-primary me-1"></i> مواد اولیه و قطعات تشکیل‌دهنده (عوامل تولید)
                </h6>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered align-middle" id="bomItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>ماده اولیه / قطعه مصرفی</th>
                                <th style="width:160px;">مقدار مصرف</th>
                                <th style="width:140px;">ضایعات (٪)</th>
                                <th style="width:50px;">حذف</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <select name="raw_materials[]" class="form-select rounded-3">
                                        <option value="">-- انتخاب قطعه / ماده اولیه --</option>
                                        <?php foreach ($products as $p): ?>
                                            <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" step="0.0001" name="raw_quantities[]" value="1" class="form-control rounded-3 font-monospace"></td>
                                <td><input type="number" step="0.01" name="scrap_percents[]" value="0" class="form-control rounded-3 font-monospace"></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 mb-4" onclick="addBomRow()">
                    <i class="fas fa-plus me-1"></i> افزودن ردیف ماده اولیه جدید
                </button>

                <div class="pt-3 border-top text-end">
                    <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">
                        <i class="fas fa-check me-1"></i> ذخیره فرمول ساخت BOM
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addBomRow() {
    const tbody = document.querySelector('#bomItemsTable tbody');
    const firstRow = tbody.querySelector('tr');
    const clone = firstRow.cloneNode(true);
    clone.querySelectorAll('input').forEach(i => i.value = '1');
    tbody.appendChild(clone);
}
</script>
