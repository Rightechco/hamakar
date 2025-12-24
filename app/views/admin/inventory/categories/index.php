<?php
// app/views/admin/inventory/categories/index.php - نسخه نهایی و اصلاح شده
global $auth;
$categories = $categories ?? [];
$filters = $filters ?? [];

// Helper function to get the category type in Persian
function getCategoryTypeInPersian($type) {
    return ($type == 'product') ? 'محصول' : 'خدمات';
}

?>

<style>
    .category-card {
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .action-buttons .btn {
        margin-right: 5px;
    }
    .add-new-btn {
        margin-bottom: 20px;
    }
</style>

<h1 class="h3 mb-4 text-gray-800">مدیریت دسته‌بندی‌ها</h1>
<?php FlashMessage::display(); ?>

<div class="card shadow mb-4 category-card">
    <div class="card-header py-3 d-flex flex-column flex-md-row align-items-md-center justify-content-md-between">
        <h6 class="m-0 font-weight-bold text-primary">لیست دسته‌بندی‌ها</h6>
        <div class="mt-2 mt-md-0">
            <a href="<?php echo APP_URL; ?>/index.php?page=categories&action=create" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> افزودن دسته‌بندی جدید
            </a>
        </div>
    </div>
    <div class="card-body">
        <form id="filter-form" class="mb-4 d-flex flex-column flex-md-row align-items-md-center gap-3" action="<?php echo APP_URL; ?>/index.php" method="GET">
            <input type="hidden" name="page" value="categories">
            <input type="hidden" name="action" value="index">
            <div class="flex-grow-1">
                <label for="type-filter" class="form-label mb-0">نوع دسته‌بندی:</label>
                <select class="form-select form-select-sm" id="type-filter" name="type">
                    <option value="">همه</option>
                    <option value="product" <?php echo (isset($filters['type']) && $filters['type'] == 'product') ? 'selected' : ''; ?>>محصول</option>
                    <option value="service" <?php echo (isset($filters['type']) && $filters['type'] == 'service') ? 'selected' : ''; ?>>خدمات</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-secondary btn-sm">اعمال فیلتر</button>
                <a href="<?php echo APP_URL; ?>/index.php?page=categories&action=index" class="btn btn-outline-secondary btn-sm">پاک کردن فیلتر</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نام دسته‌بندی</th>
                        <th>نوع</th>
                        <th>توضیحات</th>
                        <th>تاریخ ایجاد</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php $counter = 1; ?>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo $counter++; ?></td>
                                <td><?php echo htmlspecialchars($category->name ?? ''); ?></td>
                                <td><?php echo getCategoryTypeInPersian($category->type ?? 'product'); ?></td>
                                <td><?php echo htmlspecialchars($category->description ?? ''); ?></td>
                                <td>
                                    <?php
                                        echo ($category->created_at ?? '') ?
                                             jdate('Y/m/d H:i', strtotime($category->created_at)) :
                                             'نامشخص';
                                    ?>
                                </td>
                                <td class="text-center action-buttons">
                                    <a href="<?php echo APP_URL; ?>/index.php?page=categories&action=edit&id=<?php echo htmlspecialchars($category->id ?? ''); ?>" class="btn btn-warning btn-sm" title="ویرایش">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo APP_URL; ?>/index.php?page=categories&action=delete&id=<?php echo htmlspecialchars($category->id ?? ''); ?>" method="POST" class="d-inline-block">
                                        <button type="submit" class="btn btn-danger btn-sm" title="حذف" onclick="return confirm('آیا از حذف این دسته‌بندی اطمینان دارید؟');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">هیچ دسته‌بندی‌ای یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>