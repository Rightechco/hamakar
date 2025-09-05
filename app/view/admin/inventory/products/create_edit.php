<?php
// app/views/admin/inventory/products/create_edit.php
global $auth;
$product = $product ?? null;
$categories = $categories ?? [];
?>

<h1 class="mb-4"><?php echo $product ? 'ویرایش محصول' : 'افزودن محصول جدید'; ?></h1>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white p-3">
        <h5 class="mb-0">فرم محصول</h5>
    </div>
    <div class="card-body">
        <?php echo FlashMessage::get('message'); ?>
        <form action="<?php echo APP_URL; ?>/index.php?page=products&action=<?php echo $product ? 'update&id=' . $product->id : 'store'; ?>" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">نام محصول</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product->name ?? ''); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="sku" class="form-label">کد کالا (SKU)</label>
                    <input type="text" class="form-control" id="sku" name="sku" value="<?php echo htmlspecialchars($product->sku ?? ''); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="purchase_price" class="form-label">قیمت خرید</label>
                    <input type="number" class="form-control" id="purchase_price" name="purchase_price" value="<?php echo htmlspecialchars($product->purchase_price ?? ''); ?>" required min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="sale_price" class="form-label">قیمت فروش</label>
                    <input type="number" class="form-control" id="sale_price" name="sale_price" value="<?php echo htmlspecialchars($product->sale_price ?? ''); ?>" required min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="category_id" class="form-label">دسته‌بندی:</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">-- بدون دسته‌بندی --</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category->id; ?>" <?php echo ($product && $product->category_id == $category->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="unit" class="form-label">واحد</label>
                    <input type="text" class="form-control" id="unit" name="unit" value="<?php echo htmlspecialchars($product->unit ?? ''); ?>" placeholder="مثال: عدد، کارتن، کیلوگرم">
                </div>
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">توضیحات</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($product->description ?? ''); ?></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="product_image" class="form-label">تصویر محصول</label>
                    <input type="file" class="form-control" id="product_image" name="product_image">
                </div>
                <?php if ($product && !empty($product->image_path)): ?>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">تصویر فعلی</label>
                        <div>
                            <img src="<?php echo APP_URL; ?>/public/uploads/products/<?php echo htmlspecialchars($product->image_path); ?>" alt="Product Image" style="max-width: 150px; border-radius: 5px;">
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-success mt-3">ذخیره</button>
            <a href="<?php echo APP_URL; ?>/index.php?page=products&action=index" class="btn btn-secondary mt-3">بازگشت</a>
        </form>
    </div>
</div>