<?php
// app/view/admin/inventory/services/create_edit.php
$service = $service ?? null;
$categories = $categories ?? [];
?>

<h1 class="h3 mb-4 text-gray-800"><?php echo $service ? 'ویرایش خدمت' : 'افزودن خدمت جدید'; ?></h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <?php FlashMessage::display(); ?>
        <form action="<?php echo APP_URL; ?>/index.php?page=services&action=<?php echo $service ? 'update&id=' . $service->id : 'store'; ?>" method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">نام خدمت</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($service->name ?? ''); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">قیمت</label>
                        <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($service->price ?? 0); ?>" required min="0">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">دسته‌بندی</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">بدون دسته‌بندی</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category->id; ?>" <?php echo ($service && $service->category_id == $category->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">توضیحات</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($service->description ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $service ? 'بروزرسانی' : 'ذخیره'; ?></button>
            <a href="<?php echo APP_URL; ?>/index.php?page=services&action=index" class="btn btn-secondary">بازگشت</a>
        </form>
    </div>
</div>