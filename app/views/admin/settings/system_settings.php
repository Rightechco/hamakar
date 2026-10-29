<?php
// app/views/admin/settings/system_settings.php - تنظیمات یکپارچه و پیشرفته سامانه هماکار
$settings = $settings ?? [];
?>

<style>
.settings-card {
    border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    background: #ffffff;
}
.nav-pills-settings .nav-link {
    border-radius: 12px;
    padding: 10px 18px;
    font-weight: 600;
    color: #64748b;
    border: 1px solid transparent;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}
.nav-pills-settings .nav-link.active {
    background: #4f46e5;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
}
.nav-pills-settings .nav-link:hover:not(.active) {
    background: #f8fafc;
    color: #1e293b;
}
.form-label {
    font-size: 0.825rem;
    font-weight: 700;
    color: #475569;
    margin-bottom: 6px;
}
.form-control, .form-select {
    border-radius: 10px;
    border: 1px solid #cbd5e1;
    font-size: 0.875rem;
    padding: 8px 12px;
}
.form-control:focus, .form-select:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}
</style>

<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#0f172a;">
                <i class="fas fa-sliders-h text-primary me-2"></i>تنظیمات جامع سامانه هماکار
            </h3>
            <span class="text-muted small">مدیریت هویت سازمانی، پارامترهای مالی، کارکرد آفلاین سیستم و سیاست‌های نگهداری</span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=backups" class="btn btn-outline-primary rounded-3 px-3 shadow-sm">
                <i class="fas fa-database me-1"></i> مدیریت پشتیبان‌گیری
            </a>
            <button type="submit" form="settings-form" class="btn btn-primary rounded-3 px-4 shadow-sm">
                <i class="fas fa-save me-1"></i> ذخیره تغییرات
            </button>
        </div>
    </div>

    <?php FlashMessage::display(); ?>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills nav-pills-settings mb-4 gap-2 bg-white p-2 rounded-4 border shadow-sm">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-company">
                <i class="fas fa-building me-1"></i> هویت و مشخصات شرکت
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-financial">
                <i class="fas fa-receipt me-1"></i> تنظیمات مالی و فاکتور
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-offline">
                <i class="fas fa-laptop me-1"></i> کارکرد آفلاین و نصب کامپیوتر
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-system">
                <i class="fas fa-cogs me-1"></i> پیکربندی عمومی سامانه
            </button>
        </li>
    </ul>

    <!-- Form Container -->
    <form id="settings-form" method="POST" action="<?php echo APP_URL; ?>/index.php?page=admin&action=save_system_settings">
        <div class="tab-content">
            <!-- 1. COMPANY TAB -->
            <div class="tab-pane fade show active" id="tab-company">
                <div class="settings-card p-4">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="fas fa-id-card text-primary me-2"></i>مشخصات ثبتی و هویتی شرکت
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">نام رسمی شخص حقوقی / شرکت:</label>
                            <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($settings['company_name'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نام تجاری / برند سازمانی:</label>
                            <input type="text" name="company_brand" class="form-control" value="<?php echo htmlspecialchars($settings['company_brand'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">شماره ثبت شرکت:</label>
                            <input type="text" name="company_registration_no" class="form-control font-monospace" value="<?php echo htmlspecialchars($settings['company_registration_no'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">کد اقتصادی (Economic Code):</label>
                            <input type="text" name="company_economic_code" class="form-control font-monospace" value="<?php echo htmlspecialchars($settings['company_economic_code'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">شناسه ملی / کد ملی:</label>
                            <input type="text" name="company_national_id" class="form-control font-monospace" value="<?php echo htmlspecialchars($settings['company_national_id'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">تلفن تماس سازمانی:</label>
                            <input type="text" name="company_phone" class="form-control font-monospace" value="<?php echo htmlspecialchars($settings['company_phone'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ایمیل رسمی شرکت:</label>
                            <input type="email" name="company_email" class="form-control font-monospace" value="<?php echo htmlspecialchars($settings['company_email'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">کد پستی ۱۰ رقمی:</label>
                            <input type="text" name="company_postal_code" class="form-control font-monospace" value="<?php echo htmlspecialchars($settings['company_postal_code'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">نشانی کامل دفتر مرکزی:</label>
                            <textarea name="company_address" rows="2" class="form-control"><?php echo htmlspecialchars($settings['company_address'] ?? ''); ?></textarea>
                            <small class="text-muted">این آدرس به عنوان نشانی فروشنده در سربرگ فاکتورهای رسمی و صورتحساب‌های مودیان درج می‌گردد.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. FINANCIAL TAB -->
            <div class="tab-pane fade" id="tab-financial">
                <div class="settings-card p-4">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="fas fa-coins text-success me-2"></i>تنظیمات مالی و پارامترهای صدور فاکتور
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">واحد پول رسمی سامانه:</label>
                            <select name="default_currency" class="form-select">
                                <option value="IRT" <?php echo (($settings['default_currency'] ?? 'IRT') === 'IRT') ? 'selected' : ''; ?>>تومان (پیش‌فرض)</option>
                                <option value="IRR" <?php echo (($settings['default_currency'] ?? '') === 'IRR') ? 'selected' : ''; ?>>ریال</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">نرخ پیش‌فرض مالیات بر ارزش افزوده (درصد):</label>
                            <input type="number" step="0.1" name="default_vat_rate" class="form-control font-monospace" value="<?php echo htmlspecialchars($settings['default_vat_rate'] ?? '10.00'); ?>" required>
                            <small class="text-muted">مطابق قانون مالیات بر ارزش افزوده کشور (۱۰ درصد)</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">پیشوند شماره‌گذاری فاکتورها:</label>
                            <input type="text" name="invoice_prefix" class="form-control font-monospace" value="<?php echo htmlspecialchars($settings['invoice_prefix'] ?? 'INV-'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">روش تسویه پیش‌فرض:</label>
                            <select name="default_payment_terms" class="form-select">
                                <option value="cash" <?php echo (($settings['default_payment_terms'] ?? 'cash') === 'cash') ? 'selected' : ''; ?>>نقدی</option>
                                <option value="credit" <?php echo (($settings['default_payment_terms'] ?? '') === 'credit') ? 'selected' : ''; ?>>نسیه / اعتباری</option>
                                <option value="mixed" <?php echo (($settings['default_payment_terms'] ?? '') === 'mixed') ? 'selected' : ''; ?>>نقد و نسیه</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. OFFLINE & DESKTOP TAB -->
            <div class="tab-pane fade" id="tab-offline">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="settings-card p-4">
                            <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                                <i class="fas fa-laptop-code text-info me-2"></i>کارکرد کاملاً آفلاین و نصب دسکتاپ
                            </h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-check form-switch p-0 d-flex justify-content-between align-items-center bg-light p-3 rounded-3 border">
                                        <div>
                                            <label class="form-check-label fw-bold d-block text-dark" for="offline_mode_enabled">
                                                فعال‌سازی موتور کارکرد آفلاین (IndexedDB Engine)
                                            </label>
                                            <small class="text-muted">در صورت قطعی اینترنت، تمام فرم‌ها باز شده و فاکتورها در حافظه محلی کامپیوتر ذخیره می‌گردند.</small>
                                        </div>
                                        <input class="form-check-input ms-0 me-3" style="width:2.5em; height:1.3em;" type="checkbox" name="offline_mode_enabled" id="offline_mode_enabled" value="1" <?php echo (($settings['offline_mode_enabled'] ?? '1') === '1') ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">دوره تناوب همگام‌سازی خودکار (Auto-Sync Interval):</label>
                                    <div class="input-group">
                                        <input type="number" name="offline_sync_interval" class="form-control font-monospace" value="<?php echo htmlspecialchars($settings['offline_sync_interval'] ?? '60'); ?>" min="10" max="3600">
                                        <span class="input-group-text small">ثانیه</span>
                                    </div>
                                    <small class="text-muted">زمان بررسی اتصال اینترنت و ارسال صف فاکتورها به سرور ابری</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">حداکثر رکوردهای کش محلی سیستم:</label>
                                    <input type="number" name="offline_max_cached_rows" class="form-control font-monospace" value="<?php echo htmlspecialchars($settings['offline_max_cached_rows'] ?? '500'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="settings-card p-4 bg-light border-0">
                            <h6 class="fw-bold text-dark mb-2">
                                <i class="fab fa-windows text-primary me-2"></i>نصب نرم‌افزار روی ویندوز
                            </h6>
                            <p class="small text-muted mb-3">
                                هماکار با بهره‌گیری از فناوری پیشرفته PWA می‌تواند مستقیماً بدون نیاز به مرورگر، مانند یک نرم‌افزار نصبی ویندوز روی سیستم کامپیوتری شما نصب شود.
                            </p>
                            <button type="button" class="btn btn-outline-primary w-100 rounded-3 mb-2" onclick="window.installHamakarDesktopApp()">
                                <i class="fas fa-download me-1"></i> نصب نرم‌افزار روی کامپیوتر
                            </button>
                            <div class="alert alert-info rounded-3 p-2 small mb-0">
                                <i class="fas fa-check-circle me-1"></i> پس از نصب، آیکون هماکار روی دسکتاپ و منوی استارت ویندوز قرار می‌گیرد.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. SYSTEM GENERAL TAB -->
            <div class="tab-pane fade" id="tab-system">
                <div class="settings-card p-4">
                    <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">
                        <i class="fas fa-sliders-h text-secondary me-2"></i>پیکربندی عمومی و دسترسی
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">عنوان عمومی سامانه در نوار عنوان مرورگر:</label>
                            <input type="text" name="system_title" class="form-control" value="<?php echo htmlspecialchars($settings['system_title'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">مدت زمان اعتبار نشست کاربران (Session Lifetime):</label>
                            <div class="input-group">
                                <input type="number" name="session_lifetime_minutes" class="form-control font-monospace" value="<?php echo htmlspecialchars($settings['session_lifetime_minutes'] ?? '120'); ?>" min="15" max="1440">
                                <span class="input-group-text small">دقیقه</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch p-0 d-flex justify-content-between align-items-center bg-light p-3 rounded-3 border">
                                <div>
                                    <label class="form-check-label fw-bold d-block text-danger" for="maintenance_mode">
                                        حالت تعمیرات و بروزرسانی سامانه (Maintenance Mode)
                                    </label>
                                    <small class="text-muted">در صورت فعال بودن، تنها مدیران ارشد سیستم امکان دسترسی خواهند داشت.</small>
                                </div>
                                <input class="form-check-input ms-0 me-3" style="width:2.5em; height:1.3em;" type="checkbox" name="maintenance_mode" id="maintenance_mode" value="1" <?php echo (($settings['maintenance_mode'] ?? '0') === '1') ? 'checked' : ''; ?>>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
