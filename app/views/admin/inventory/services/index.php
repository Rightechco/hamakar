<?php
// app/view/admin/inventory/services/index.php
$services = $services ?? [];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
    }

    body, .services-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .bg-soft-primary { background-color: #e0e7ff; }
    .text-primary-custom { color: #4338ca !important; }
    .bg-soft-secondary { background-color: #f1f5f9; }
    .text-secondary { color: #475569 !important; }
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

    .table-ultra-service {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-ultra-service tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-ultra-service tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra-service td, .table-ultra-service th {
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

<div class="services-wrapper py-3">
    <!-- Header & Controls -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">مدیریت خدمات</h3>
            <p class="text-muted small mb-0">لیست تمامی خدمات قابل ارائه، تعرفه‌ها و دسته‌بندی‌های مربوطه</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=services&action=create" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-medium">
            <i class="fas fa-plus me-1"></i> افزودن خدمت جدید
        </a>
    </div>

    <?php FlashMessage::display(); ?>

    <!-- Table Container Card -->
    <div class="ultra-card p-4">
        <div class="table-responsive">
            <table class="table table-ultra-service align-middle">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th style="width: 80px;">شناسه</th>
                        <th>عنوان خدمت</th>
                        <th>قیمت / تعرفه (تومان)</th>
                        <th>دسته‌بندی</th>
                        <th class="text-end">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($services)): ?>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td class="fw-bold text-secondary">#<?php echo htmlspecialchars($service->id); ?></td>
                                <td>
                                    <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($service->name); ?></div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark fs-6"><?php echo htmlspecialchars(number_format($service->price)); ?></span>
                                    <span class="text-muted small ms-1">تومان</span>
                                </td>
                                <td>
                                    <span class="badge bg-soft-primary text-primary-custom px-3 py-2 rounded-pill fw-medium">
                                        <i class="fas fa-folder me-1"></i><?php echo htmlspecialchars($service->category_name ?? 'بدون دسته‌بندی'); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=services&action=edit&id=<?php echo $service->id; ?>" class="action-btn bg-soft-warning text-warning" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=services&action=delete&id=<?php echo $service->id; ?>" method="POST" class="d-inline">
                                            <button type="submit" class="action-btn bg-soft-danger text-danger border-0" title="حذف" onclick="return confirm('آیا از حذف این خدمت اطمینان دارید؟');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-concierge-bell fa-3x mb-3 text-light"></i>
                                <p class="mb-0">هیچ خدمتی تعریف نشده است.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>