<?php
// app/view/admin/inventory/services/create_edit.php
$service = $service ?? null;
$categories = $categories ?? [];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 18px;
        --radius-md: 10px;
        --shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    body, .premium-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .premium-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
    }

    .premium-card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
    }

    .form-control, .form-select {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        padding: 0.65rem 0.95rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .btn-gradient-primary {
        background: var(--primary-gradient);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        padding: 0.65rem 1.6rem;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: all 0.2s ease;
    }

    .btn-gradient-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        color: #fff;
    }
</style>

<div class="premium-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><?php echo $service ? 'ویرایش اطلاعات خدمت' : 'افزودن خدمت جدید'; ?></h3>
            <p class="text-muted small mb-0">مشخصات خدمت، دسته‌بندی و تعرفه مربوطه را تنظیم نمایید</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=services&action=index" class="btn btn-outline-secondary btn-sm px-4 rounded-pill fw-medium">
            <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست
        </a>
    </div>

    <!-- Form Container -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="premium-card">
                <div class="premium-card-header d-flex align-items-center">
                    <i class="fas fa-concierge-bell text-primary fs-5 me-2"></i>
                    <h5 class="mb-0 fw-bold fs-6 text-dark"><?php echo $service ? 'فرم ویرایش خدمت' : 'فرم تعریف خدمت جدید'; ?></h5>
                </div>
                <div class="card-body p-4">
                    <?php FlashMessage::display(); ?>

                    <form action="<?php echo APP_URL; ?>/index.php?page=services&action=<?php echo $service ? 'update&id=' . $service->id : 'store'; ?>" method="POST">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-medium small text-dark">نام خدمت <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($service->name ?? ''); ?>" placeholder="عنوان خدمت را وارد کنید..." required>
                            </div>
                            <div class="col-md-6">
                                <label for="price" class="form-label fw-medium small text-dark">قیمت / تعرفه (تومان) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($service->price ?? 0); ?>" required min="0" placeholder="0">
                                    <span class="input-group-text bg-light text-muted small">تومان</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-medium small text-dark">دسته‌بندی مربوطه</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">بدون دسته‌بندی (عمومی)</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category->id; ?>" <?php echo ($service && $service->category_id == $category->id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium small text-dark">توضیحات و جزئیات خدمت</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="توضیحات تکمیلی یا جزئیات ارائه این خدمت را شرح دهید..."><?php echo htmlspecialchars($service->description ?? ''); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="<?php echo APP_URL; ?>/index.php?page=services&action=index" class="btn btn-light px-4 rounded-2">انصراف</a>
                            <button type="submit" class="btn btn-gradient-primary px-4">
                                <i class="fas fa-check me-1"></i> <?php echo $service ? 'بروزرسانی خدمت' : 'ذخیره خدمت جدید'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>