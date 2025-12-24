<?php
// app/views/admin/inventory/reports/warehouse_report.php
$warehouses = $warehouses ?? [];
$reportData = $reportData ?? [];
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">گزارش موجودی انبار</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">فیلتر گزارش</h6>
    </div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/index.php?page=inventory&action=warehouse_report" method="GET">
            <input type="hidden" name="page" value="inventory">
            <input type="hidden" name="action" value="warehouse_report">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="warehouse_id">انتخاب انبار:</label>
                    <select name="warehouse_id" id="warehouse_id" class="form-control">
                        <option value="">همه انبارها</option>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <option value="<?php echo $warehouse->id; ?>" <?php echo (isset($selectedWarehouseId) && $selectedWarehouseId == $warehouse->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($warehouse->name ?? ''); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">مشاهده گزارش</button>
        </form>
    </div>
</div>

<?php if (isset($reportData) && !empty($reportData)): ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">جزئیات موجودی</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>نام محصول</th>
                        <th>کد کالا</th>
                        <th>موجودی</th>
                        <th>واحد</th>
                        <th>قیمت خرید (میانگین)</th>
                        <th>قیمت فروش</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reportData as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item->product_name ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($item->sku ?? ''); ?></td>
                            <!-- استفاده از نام فیلد محاسبه شده جدید -->
                            <td><?php echo number_format($item->current_inventory_calculated ?? 0); ?></td>
                            <td><?php echo htmlspecialchars($item->unit_name ?? ''); ?></td>
                            <td><?php echo number_format($item->purchase_price ?? 0); ?></td>
                            <td><?php echo number_format($item->sale_price ?? 0); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php elseif (isset($selectedWarehouseId) && !empty($selectedWarehouseId)): ?>
<div class="alert alert-info text-center">
    هیچ محصولی در این انبار یافت نشد.
</div>
<?php endif; ?>
