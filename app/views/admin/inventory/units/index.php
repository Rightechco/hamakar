<?php
// app/views/admin/inventory/units/index.php
$units = $units ?? [];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
    }

    body, .units-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .bg-soft-primary { background-color: #e0e7ff; }
    .text-primary-custom { color: #4338ca !important; }
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
    .table-ultra-units {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-ultra-units tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-ultra-units tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra-units td, .table-ultra-units th {
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

<div class="units-wrapper py-3">
    <!-- Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">واحدهای سنجش کالا</h3>
            <p class="text-muted small mb-0">مدیریت لیست واحدها، وزن‌ها و مقادیر سنجش محصولات انبار</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=create_unit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-medium">
            <i class="fas fa-plus me-1"></i> افزودن واحد جدید
        </a>
    </div>

    <?php echo FlashMessage::display(); ?>

    <!-- Table Container -->
    <div class="ultra-card p-4">
        <div class="table-responsive">
            <table class="table table-ultra-units align-middle">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>عنوان واحد سنجش</th>
                        <th>تاریخ ایجاد</th>
                        <th class="text-end">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($units)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="fas fa-balance-scale fa-3x mb-3 text-light"></i>
                                <p class="mb-0">هیچ واحد سنجشی ثبت نشده است.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($units as $unit): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-soft-primary text-primary-custom px-3 py-2 rounded-pill fs-6 fw-bold">
                                        <i class="fas fa-ruler-combined me-1"></i>
                                        <?php echo htmlspecialchars($unit->name); ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    <?php echo jdate('Y/m/d', strtotime($unit->created_at)); ?>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=edit_unit&id=<?php echo $unit->id; ?>" class="action-btn bg-soft-warning text-warning" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=inventory&action=delete_unit" method="POST" class="d-inline">
                                            <input type="hidden" name="id" value="<?php echo $unit->id; ?>">
                                            <button type="submit" class="action-btn bg-soft-danger text-danger border-0" title="حذف" onclick="return confirm('آیا از حذف این واحد مطمئن هستید؟');">
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