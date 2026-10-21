<?php
// app/views/admin/inventory/units/create_edit.php
// View for creating or editing a new product unit

$unit = $unit ?? null;
$isEdit = $unit !== null;
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

    .form-control {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        padding: 0.65rem 0.95rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
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
            <h3 class="fw-bold mb-1"><?php echo $isEdit ? 'ویرایش واحد سنجش' : 'افزودن واحد سنجش جدید'; ?></h3>
            <p class="text-muted small mb-0">تعریف واحدهای اندازهگیری کالا (مانند: کیلوگرم، عدد، بسته و...)</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=units" class="btn btn-outline-secondary btn-sm px-4 rounded-pill fw-medium">
            <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست
        </a>
    </div>

    <!-- Form Area -->
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="premium-card">
                <div class="premium-card-header d-flex align-items-center">
                    <i class="fas fa-balance-scale text-primary fs-5 me-2"></i>
                    <h5 class="mb-0 fw-bold fs-6 text-dark"><?php echo $isEdit ? 'فرم ویرایش واحد' : 'فرم ثبت واحد جدید'; ?></h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo APP_URL; ?>/index.php?page=inventory&action=<?php echo $isEdit ? 'update_unit&id=' . $unit->id : 'store_unit'; ?>" method="POST">
                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium small text-dark">نام واحد سنجش <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $isEdit ? htmlspecialchars($unit->name) : ''; ?>" placeholder="مثال: کیلوگرم، گرم، عدد، کارتن..." required autofocus>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=units" class="btn btn-light px-4 rounded-2">انصراف</a>
                            <button type="submit" class="btn btn-gradient-primary px-4">
                                <i class="fas fa-check me-1"></i> <?php echo $isEdit ? 'ذخیره تغییرات' : 'ثبت واحد جدید'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>