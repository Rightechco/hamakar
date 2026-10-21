<?php
// app/views/admin/inventory/categories/index.php
global $auth;
$categories = $categories ?? [];
$filters = $filters ?? [];

function getCategoryTypeInPersian($type) {
    return ($type == 'product') ? 'محصول' : 'خدمات';
}
?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">مدیریت دسته‌بندی‌ها</h1>
        <p class="text-muted small mb-0">تعریف، دسته‌بندی و ساماندهی محصولات و خدمات شرکت</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1 no-print">
            <i class="fas fa-print"></i>
            <span>چاپ</span>
        </button>
        <a href="<?php echo APP_URL; ?>/index.php?page=categories&action=create" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
            <i class="fas fa-plus"></i>
            <span>افزودن دسته‌بندی جدید</span>
        </a>
    </div>
</div>

<?php if (class_exists('FlashMessage')) { FlashMessage::display(); } ?>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2 no-print">
        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fas fa-filter text-primary"></i>
            <span>فیلتر دسته‌بندی‌ها</span>
        </h6>
    </div>
    <div class="card-body p-3 bg-light no-print border-bottom">
        <form id="filter-form" class="row g-3 align-items-end" action="<?php echo APP_URL; ?>" method="GET">
            <input type="hidden" name="page" value="categories">
            <input type="hidden" name="action" value="index">
            <div class="col-md-4">
                <label for="type-filter" class="form-label text-muted small fw-bold mb-1">نوع دسته‌بندی:</label>
                <select class="form-select form-select-sm rounded-3" id="type-filter" name="type">
                    <option value="">همه موارد</option>
                    <option value="product" <?php echo (isset($filters['type']) && $filters['type'] == 'product') ? 'selected' : ''; ?>>محصول</option>
                    <option value="service" <?php echo (isset($filters['type']) && $filters['type'] == 'service') ? 'selected' : ''; ?>>خدمات</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3">اعمال فیلتر</button>
                <a href="<?php echo APP_URL; ?>/index.php?page=categories&action=index" class="btn btn-outline-secondary btn-sm rounded-3 px-3">بازنشانی</a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small">
                    <tr>
                        <th class="ps-3" style="width: 60px;">#</th>
                        <th>نام دسته‌بندی</th>
                        <th class="text-center">نوع</th>
                        <th>توضیحات</th>
                        <th class="text-center">تاریخ ایجاد</th>
                        <th class="text-center no-print" style="width: 110px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php $counter = 1; ?>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td class="ps-3 font-monospace fw-bold text-muted"><?php echo $counter++; ?></td>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($category->name ?? ''); ?></td>
                                <td class="text-center">
                                    <?php $isProduct = ($category->type ?? 'product') === 'product'; ?>
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold fs-7 <?php echo $isProduct ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle'; ?>">
                                        <?php echo getCategoryTypeInPersian($category->type ?? 'product'); ?>
                                    </span>
                                </td>
                                <td class="small text-secondary"><?php echo htmlspecialchars($category->description ?? '---'); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark font-monospace border">
                                        <?php
                                            echo ($category->created_at ?? '') ?
                                                 jdate('Y/m/d H:i', strtotime($category->created_at)) :
                                                 'نامشخص';
                                        ?>
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=categories&action=edit&id=<?php echo htmlspecialchars($category->id ?? ''); ?>" class="btn btn-outline-warning" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=categories&action=delete&id=<?php echo htmlspecialchars($category->id ?? ''); ?>" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این دسته‌بندی اطمینان دارید؟');">
                                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-layer-group fs-2 mb-2 d-block opacity-50"></i>
                                هیچ دسته‌بندی‌ای یافت نشد.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>