<?php
// app/views/admin/inventory/warehouses/index.php
$warehouses = $warehouses ?? [];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
    }

    body, .warehouses-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .bg-soft-primary { background-color: #e0e7ff; }
    .text-primary-custom { color: #4338ca !important; }
    .bg-soft-info { background-color: #e0f2fe; }
    .text-info-custom { color: #0369a1 !important; }
    .bg-soft-warning { background-color: #fef3c7; }
    .text-warning { color: #b45309 !important; }
    .bg-soft-danger { background-color: #fee2e2; }
    .text-danger { color: #b91c1c !important; }

    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
    }

    /* Floating Modern Table Styling */
    .table-ultra-warehouse {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-ultra-warehouse tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-ultra-warehouse tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra-warehouse td, .table-ultra-warehouse th {
        padding: 1rem 1.25rem;
        border: none;
        vertical-align: middle;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .action-btn:hover {
        transform: scale(1.08);
    }
</style>

<div class="warehouses-wrapper py-3">
    <!-- Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">مدیریت انبارها</h3>
            <p class="text-muted small mb-0">لیست تمامی انبارهای تعریف‌شده، موقعیت‌های مکانی و گزارش موجودی آنها</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=create_warehouse" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-medium">
            <i class="fas fa-plus me-1"></i> افزودن انبار جدید
        </a>
    </div>

    <?php echo FlashMessage::display(); ?>

    <!-- Table Container -->
    <div class="ultra-card p-4">
        <div class="table-responsive">
            <table class="table table-ultra-warehouse align-middle" id="dataTable">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>عنوان انبار</th>
                        <th>موقعیت / آدرس انبار</th>
                        <th>تاریخ ایجاد</th>
                        <th class="text-end">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($warehouses)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-warehouse fa-3x mb-3 text-light"></i>
                                <p class="mb-0">هیچ انباری یافت نشد.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-soft-primary text-primary-custom px-3 py-2 rounded-pill fs-6 fw-bold">
                                        <i class="fas fa-boxes me-1.5"></i>
                                        <?php echo htmlspecialchars($warehouse->name); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark fw-medium fs-6">
                                        <?php echo htmlspecialchars($warehouse->location ?: '---'); ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    <?php echo jdate('Y/m/d', strtotime($warehouse->created_at)); ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=warehouse_report&warehouse_id=<?php echo $warehouse->id; ?>" class="action-btn bg-soft-info text-info-custom" title="گزارش موجودی انبار">
                                            <i class="fas fa-chart-pie"></i>
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=edit_warehouse&id=<?php echo $warehouse->id; ?>" class="action-btn bg-soft-warning text-warning" title="ویرایش انبار">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=inventory&action=delete_warehouse" method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $warehouse->id; ?>">
                                            <button type="submit" class="action-btn bg-soft-danger text-danger border-0" title="حذف" onclick="return confirm('آیا از حذف این انبار مطمئن هستید؟');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>