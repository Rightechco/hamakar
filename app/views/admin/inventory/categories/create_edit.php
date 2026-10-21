<?php
// app/views/admin/inventory/categories/create_edit.php
global $auth;
$category = $category ?? null;
$pageTitle = $category ? 'ویرایش دسته‌بندی: ' . htmlspecialchars($category->name) : 'افزودن دسته‌بندی جدید';
$buttonText = $category ? 'ذخیره تغییرات' : 'ثبت دسته‌بندی جدید';
$formAction = $category ? APP_URL . '/index.php?page=categories&action=update&id=' . $category->id : APP_URL . '/index.php?page=categories&action=store';
?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1"><?php echo $pageTitle; ?></h1>
        <p class="text-muted small mb-0">تعریف و مدیریت دسته‌بندی محصولات و خدمات سیستم</p>
    </div>
    <a href="<?php echo APP_URL; ?>/index.php?page=categories&action=index" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
        <i class="fas fa-arrow-right"></i>
        <span>بازگشت به لیست</span>
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
        <i class="fas fa-layer-group text-primary"></i>
        <h6 class="m-0 fw-bold text-dark"><?php echo $pageTitle; ?></h6>
    </div>
    <div class="card-body p-4">
        <?php if (class_exists('FlashMessage')) { FlashMessage::display(); } ?>
        
        <form action="<?php echo $formAction; ?>" method="POST">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-bold text-secondary small">نام دسته‌بندی:</label>
                    <input type="text" class="form-control rounded-3" id="name" name="name" value="<?php echo htmlspecialchars($category->name ?? ''); ?>" placeholder="مثال: قطعات سخت‌افزاری یا خدمات پشتیبانی" required>
                </div>

                <div class="col-md-6">
                    <label for="type" class="form-label fw-bold text-secondary small">نوع دسته‌بندی:</label>
                    <select class="form-select rounded-3" id="type" name="type" required>
                        <option value="product" <?php echo ($category && $category->type == 'product') ? 'selected' : ''; ?>>محصول</option>
                        <option value="service" <?php echo ($category && $category->type == 'service') ? 'selected' : ''; ?>>خدمات</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="description" class="form-label fw-bold text-secondary small">توضیحات (اختیاری):</label>
                    <textarea class="form-control rounded-3" id="description" name="description" rows="4" placeholder="خلاصه توضیحات کاربردی این دسته‌بندی..."><?php echo htmlspecialchars($category->description ?? ''); ?></textarea>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-end gap-2">
                <a href="<?php echo APP_URL; ?>/index.php?page=categories&action=index" class="btn btn-light border btn-sm rounded-3 px-3">انصراف</a>
                <button type="submit" class="btn btn-success btn-sm rounded-3 px-4 d-inline-flex align-items-center gap-1">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo $buttonText; ?></span>
                </button>
            </div>
        </form>
    </div>
</div>