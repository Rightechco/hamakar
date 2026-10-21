<?php
// app/views/admin/inventory/reports/warehouse_report.php
$warehouses = $warehouses ?? [];
$reportData = $reportData ?? [];

// محاسبه مجموع کل موجودی و ارزش موجودی در صورت وجود
$totalItems = count($reportData);
$totalStock = 0;
$totalValue = 0;

foreach ($reportData as $item) {
    $qty = $item->current_inventory_calculated ?? 0;
    $price = $item->purchase_price ?? 0;
    $totalStock += $qty;
    $totalValue += ($qty * $price);
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --border-color: #e2e8f0;
        --surface-bg: #ffffff;
    }

    .report-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    /* Filter Box */
    .filter-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
    }

    .form-select-ultra {
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 0.65rem 1rem;
        font-size: 0.95rem;
        transition: all 0.2s;
    }

    .form-select-ultra:focus {
        border-color: var(--primary-indigo);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
    }

    .btn-ultra-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 0.65rem 1.8rem;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: all 0.2s;
    }

    .btn-ultra-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        color: #ffffff;
    }

    /* Table Styles */
    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .table-ultra-warehouse {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-ultra-warehouse th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        padding: 16px 18px;
        border-bottom: 1px solid var(--border-color);
    }

    .table-ultra-warehouse td {
        padding: 16px 18px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.925rem;
    }

    .table-ultra-warehouse tbody tr:hover {
        background-color: #f8fafc;
    }

    .sku-tag {
        font-family: monospace;
        background: #f1f5f9;
        color: #475569;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 0.85rem;
    }
</style>

<div class="report-wrapper py-3">
    <!-- Title -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">گزارش جامع موجودی انبارها</h3>
            <p class="text-muted small mb-0">مشاهده برمیزان موجودی کالاها و قیمت‌گذاری‌ها به تفکیک انبار</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-medium no-print">
            <i class="fas fa-print me-1"></i> چاپ گزارش
        </button>
    </div>

    <!-- Filter Form -->
    <div class="filter-card mb-4 no-print">
        <form action="<?php echo APP_URL; ?>/index.php" method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="inventory">
            <input type="hidden" name="action" value="warehouse_report">
            
            <div class="col-md-6 col-lg-4">
                <label for="warehouse_id" class="form-label fw-bold small text-dark mb-2">
                    <i class="fas fa-warehouse me-1 text-primary"></i> انتخاب انبار مورد نظر:
                </label>
                <select name="warehouse_id" id="warehouse_id" class="form-select form-select-ultra">
                    <option value="">نمایش همه انبارها</option>
                    <?php foreach ($warehouses as $warehouse): ?>
                        <option value="<?php echo $warehouse->id; ?>" <?php echo (isset($selectedWarehouseId) && $selectedWarehouseId == $warehouse->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($warehouse->name ?? ''); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6 col-lg-3">
                <button type="submit" class="btn btn-ultra-primary w-100">
                    <i class="fas fa-filter me-1"></i> اعمال فیلتر
                </button>
            </div>
        </form>
    </div>

    <?php if (isset($reportData) && !empty($reportData)): ?>
        <!-- Quick Stats Banner -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 bg-white border rounded-4 shadow-sm d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">تعداد تنوع کالا</span>
                        <h5 class="fw-bold text-dark mb-0 mt-1"><?php echo number_format($totalItems); ?> قلم</h5>
                    </div>
                    <div class="badge bg-soft-primary text-primary p-3 rounded-3"><i class="fas fa-boxes fa-lg"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-white border rounded-4 shadow-sm d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">مجموع کل موجودی‌ها</span>
                        <h5 class="fw-bold text-primary mb-0 mt-1"><?php echo number_format($totalStock); ?></h5>
                    </div>
                    <div class="badge bg-soft-success text-success p-3 rounded-3"><i class="fas fa-layer-group fa-lg"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-white border rounded-4 shadow-sm d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small">ارزش تقریبی خریدموجودی</span>
                        <h5 class="fw-bold text-dark mb-0 mt-1"><?php echo number_format($totalValue); ?> تومان</h5>
                    </div>
                    <div class="badge bg-soft-warning text-warning p-3 rounded-3"><i class="fas fa-coins fa-lg"></i></div>
                </div>
            </div>
        </div>

        <!-- Inventory Table -->
        <div class="ultra-card p-4">
            <div class="table-responsive">
                <table class="table-ultra-warehouse align-middle">
                    <thead>
                        <tr>
                            <th>نام محصول</th>
                            <th>شناسه کالا (SKU)</th>
                            <th class="text-center">موجودی فعلی</th>
                            <th class="text-center">واحد سنجش</th>
                            <th class="text-end">قیمت خرید (میانگین)</th>
                            <th class="text-end">قیمت فروش</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $item): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($item->product_name ?? ''); ?></td>
                                <td><span class="sku-tag"><?php echo htmlspecialchars($item->sku ?? '---'); ?></span></td>
                                <td class="text-center">
                                    <span class="badge bg-soft-primary text-primary fs-6 px-3 py-1.5 rounded-pill fw-bold">
                                        <?php echo number_format($item->current_inventory_calculated ?? 0); ?>
                                    </span>
                                </td>
                                <td class="text-center text-muted small"><?php echo htmlspecialchars($item->unit_name ?? 'عدد'); ?></td>
                                <td class="text-end text-muted"><?php echo number_format($item->purchase_price ?? 0); ?> تومان</td>
                                <td class="text-end fw-bold text-dark"><?php echo number_format($item->sale_price ?? 0); ?> تومان</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif (isset($selectedWarehouseId) && !empty($selectedWarehouseId)): ?>
        <div class="ultra-card p-5 text-center text-muted">
            <i class="fas fa-box-open fa-4x mb-3 text-light"></i>
            <h5 class="fw-bold text-dark">هیچ کالایی یافت نشد</h5>
            <p class="mb-0">در انبار انتخاب شده در حال حاضر هیچ موجودی یا کالایی ثبت نگردیده است.</p>
        </div>
    <?php endif; ?>
</div>