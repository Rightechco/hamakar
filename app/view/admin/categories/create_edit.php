<?php
// app/views/admin/categories/create_edit.php
global $auth;
$category = $category ?? null;
$pageTitle = $category ? 'ویرایش دسته‌بندی: ' . htmlspecialchars($category->name) : 'افزودن دسته‌بندی جدید';
$formAction = $category ? APP_URL . '/index.php?page=categories&action=update&id=' . $category->id : APP_URL . '/index.php?page=categories&action=store';
?>

<h1 class="mb-4"><?php echo $pageTitle; ?></h1>

<div class="card shadow-sm">
    <div class="card-header bg-white p-3">
        <h5 class="mb-0"><?php echo $pageTitle; ?></h5>
    </div>
    <div class="card-body">
        <?php echo FlashMessage::get('message'); ?>
        <form action="<?php echo $formAction; ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">نام دسته‌بندی</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($category->name ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">توضیحات (اختیاری)</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($category->description ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-success">ذخیره تغییرات</button>
            <a href="<?php echo APP_URL; ?>/index.php?page=categories&action=index" class="btn btn-secondary">بازگشت</a>
        </form>
    </div>
</div>