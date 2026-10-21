<?php
// app/views/admin/inventory/products/create_edit.php - نسخه اولترا پرمیوم و مدرن
global $auth;
$product =$product ?? null;
$categories =$categories ?? [];
$units =$units ?? [];
$warehouses =$warehouses ?? [];
$pageTitle =$product ? 'ویرایش مشخصات محصول' : 'افزودن محصول جدید به انبار';
?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1"><?php echo $pageTitle; ?></h1>
        <p class="text-muted small mb-0">تعریف قیمت‌ها، تخصیص انبار، واحد سنجش و بارگذاری تصویر کالا</p>
    </div>
    <a href="<?php echo APP_URL; ?>/index.php?page=products&action=index" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
        <i class="fas fa-arrow-right"></i>
        <span>بازگشت به لیست محصولات</span>
    </a>
</div>

<?php if (class_exists('FlashMessage')) { echo FlashMessage::get('message'); } ?>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
        <i class="fas fa-box text-primary"></i>
        <h6 class="m-0 fw-bold text-dark">فرم اطلاعات کالا</h6>
    </div>
    <div class="card-body p-4">
        <form action="<?php echo APP_URL; ?>/index.php?page=products&action=<?php echo $product ? 'update&id=' . $product->id : 'store'; ?>" method="POST" enctype="multipart/form-data">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-bold text-secondary small">نام محصول:</label>
                    <input type="text" class="form-control rounded-3" id="name" name="name" value="<?php echo htmlspecialchars($product->name ?? ''); ?>" placeholder="نام تجاری یا مدل کالا" required>
                </div>
                
                <div class="col-md-6">
                    <label for="sku" class="form-label fw-bold text-secondary small">کد کالا (SKU):</label>
                    <input type="text" class="form-control rounded-3 font-monospace" id="sku" name="sku" value="<?php echo htmlspecialchars($product->sku ?? ''); ?>" placeholder="PRD-001" required>
                </div>

                <div class="col-md-6">
                    <label for="purchase_price" class="form-label fw-bold text-secondary small">قیمت خرید (تومان):</label>
                    <input type="number" class="form-control rounded-3 font-monospace" id="purchase_price" name="purchase_price" value="<?php echo htmlspecialchars($product->purchase_price ?? ''); ?>" required min="0">
                </div>

                <div class="col-md-6">
                    <label for="sale_price" class="form-label fw-bold text-secondary small">قیمت فروش (تومان):</label>
                    <input type="number" class="form-control rounded-3 font-monospace" id="sale_price" name="sale_price" value="<?php echo htmlspecialchars($product->sale_price ?? ''); ?>" required min="0">
                </div>

                <div class="col-md-4">
                    <label for="category_id" class="form-label fw-bold text-secondary small">دسته‌بندی:</label>
                    <select class="form-select rounded-3" id="category_id" name="category_id">
                        <option value="">-- بدون دسته‌بندی --</option>
                        <?php foreach($categories as$category): ?>
                            <option value="<?php echo $category->id; ?>" <?php echo ($product && $product->category_id ==$category->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="unit_id" class="form-label fw-bold text-secondary small">واحد اندازه‌گیری:</label>
                    <select class="form-select rounded-3" id="unit_id" name="unit_id" required>
                        <option value="">انتخاب واحد...</option>
                        <?php foreach($units as$unit): ?>
                            <option value="<?php echo $unit->id; ?>" <?php echo ($product && $product->unit_id ==$unit->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($unit->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="warehouse_id" class="form-label fw-bold text-secondary small">انبار پیش‌فرض:</label>
                    <select class="form-select rounded-3" id="warehouse_id" name="warehouse_id" required>
                        <option value="">انتخاب انبار...</option>
                        <?php foreach($warehouses as$warehouse): ?>
                            <option value="<?php echo $warehouse->id; ?>" <?php echo ($product && (isset($product->warehouse_id) && $product->warehouse_id ==$warehouse->id)) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($warehouse->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="initial_inventory" class="form-label fw-bold text-secondary small">موجودی اولیه:</label>
                    <input type="number" class="form-control rounded-3 font-monospace" id="initial_inventory" name="initial_inventory" value="<?php echo htmlspecialchars($product->inventory ?? 0); ?>" min="0" <?php echo $product ? 'readonly' : ''; ?> required>
                    <?php if ($product): ?>
                        <small class="text-muted d-block mt-1">موجودی اولیه پس از ثبت قابل تغییر مستقیم نیست و از طریق اسناد انبارداری مدیریت می‌شود.</small>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="product_image" class="form-label fw-bold text-secondary small">تصویر محصول:</label>
                    <input type="file" class="form-control rounded-3" id="product_image" name="product_image" accept="image/*">
                </div>

                <?php if ($product && !empty($product->image_path)): ?>
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-secondary small">تصویر فعلی کالا:</label>
                        <div>
                            <img src="<?php echo APP_URL; ?>/public/uploads/products/<?php echo htmlspecialchars($product->image_path); ?>" alt="Product Image" class="rounded-3 border object-fit-cover shadow-sm" style="width: 100px; height: 100px;">
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-md-12">
                    <label for="description" class="form-label fw-bold text-secondary small">توضیحات و مشخصات تکمیلی:</label>
                    <textarea class="form-control rounded-3" id="description" name="description" rows="4" placeholder="جزئیات فنی، کاربرد یا مشخصات کالا..."><?php echo htmlspecialchars($product->description ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="d-flex align-items-center justify-content-end gap-2">
                <a href="<?php echo APP_URL; ?>/index.php?page=products&action=index" class="btn btn-light border btn-sm rounded-3 px-3">انصراف</a>
                <button type="submit" class="btn btn-success btn-sm rounded-3 px-4 d-inline-flex align-items-center gap-1">
                    <i class="fas fa-check-circle"></i>
                    <span>ذخیره اطلاعات محصول</span>
                </button>
            </div>
        </form>
    </div>
</div>