<?php
// app/views/admin/categories/index.php - مدیریت دسته بندی ها (نسخه نهایی با گرافیک بهتر)
// این فایل برای نمایش، افزودن، ویرایش و حذف دسته بندی ها استفاده می شود.

// مطمئن شوید که تابع sanitize و FlashMessage در دسترس هستند.
// در اکثر فریم ورک ها این ها به صورت سراسری تعریف می شوند.
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
    /* Style for the Add New button */
    .add-new-btn {
        margin-bottom: 20px;
    }
</style>

<h1 class="h3 mb-4 text-gray-800">مدیریت دسته‌بندی‌ها</h1>

<?php FlashMessage::display(); // نمایش پیام های موفقیت/خطا ?>

<div class="d-flex justify-content-end add-new-btn">
    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=createCategory" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> افزودن دسته‌بندی جدید
    </a>
</div>

<div class="card shadow mb-4 category-card">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">لیست دسته‌بندی‌ها</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نام دسته‌بندی</th>
                        <th>توضیحات</th>
                        <th>تاریخ ایجاد</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>نام دسته‌بندی</th>
                        <th>توضیحات</th>
                        <th>تاریخ ایجاد</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php $counter = 1; ?>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo $counter++; ?></td>
                                <td><?php echo htmlspecialchars($category->name ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($category->description ?? ''); ?></td>
                                <td>
                                    <?php 
                                        echo ($category->created_at ?? '') ? 
                                             jdate('Y/m/d H:i', strtotime($category->created_at)) : 
                                             'نامشخص'; 
                                    ?>
                                </td>
                                <td class="text-center action-buttons">
                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=editCategory&id=<?php echo htmlspecialchars($category->id ?? ''); ?>" class="btn btn-warning btn-sm" title="ویرایش">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteCategory&id=<?php echo htmlspecialchars($category->id ?? ''); ?>" class="btn btn-danger btn-sm" title="حذف" onclick="return confirm('آیا از حذف این دسته‌بندی اطمینان دارید؟');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">هیچ دسته‌بندی‌ای یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // این قسمت برای فعال سازی DataTable (در صورت استفاده) می تواند به یک فایل JS جدا منتقل شود.
    // $(document).ready(function() {
    //     $('#dataTable').DataTable({
    //         "language": {
    //             "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Persian.json"
    //         }
    //     });
    // });
</script>