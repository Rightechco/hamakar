<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/fixed_assets.php - مدیریت دارایی‌های ثابت
$fixedAssets = $fixedAssets ?? $data['fixedAssets'] ?? [];
$assetAccounts = $assetAccounts ?? $data['assetAccounts'] ?? [];
$expenseAccounts = $expenseAccounts ?? $data['expenseAccounts'] ?? [];
?>

<style>
    .asset-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-building-columns text-teal-600" style="color:#0d9488;"></i>
            <span>مدیریت دارایی‌های ثابت و استهلاک</span>
        </h3>
        <p class="text-muted small mb-0">ثبت اموال، محاسبه استهلاک انباشته و ارزش دفتری تجهیزات سازمان</p>
    </div>

    <a href="index.php?page=admin&action=run_depreciation_form" class="btn btn-teal text-white rounded-pill px-4 fw-bold" style="background:#0d9488;">
        <i class="fas fa-calculator me-1"></i> اجرای عملیات استهلاک
    </a>
</div>

<div class="card asset-card p-4 mb-4">
    <h6 class="fw-bold text-dark mb-3">افزودن دارایی ثابت جدید</h6>
    <form action="index.php?page=admin&action=store_fixed_asset" method="POST" class="row g-3">
        <div class="col-md-3">
            <label class="form-label small fw-bold text-dark">نام دارایی:</label>
            <input type="text" name="asset_name" class="form-control rounded-3" placeholder="مثال: سرور اداری..." required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-dark">کد پلاک / اموال:</label>
            <input type="text" name="asset_code" class="form-control rounded-3 font-monospace" placeholder="FA-1001" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-dark">قیمت خرید (تومان):</label>
            <input type="number" name="purchase_cost" class="form-control rounded-3 font-monospace" placeholder="0" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-dark">عمر مفید (سال):</label>
            <input type="number" name="useful_life_years" class="form-control rounded-3 font-monospace" value="5" required>
        </div>
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">ذخیره دارایی</button>
        </div>
    </form>
</div>