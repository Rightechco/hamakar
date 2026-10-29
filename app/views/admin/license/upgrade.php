<?php
// app/views/admin/license/upgrade.php - صفحه جذاب ارتقای پلن و فعال‌سازی ماژول اختصاصی
$moduleKey = $moduleKey ?? '';
$moduleInfo = $moduleInfo ?? null;
$plans = $plans ?? [];
$activeLicense = $activeLicense ?? null;
?>

<div class="container py-4 text-center" style="max-width: 860px;">
    <div class="card border-0 rounded-4 shadow-sm p-4 p-md-5 bg-white mb-4">
        <div class="mb-4">
            <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width: 84px; height: 84px;">
                <i class="fas fa-lock fa-2x"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">این بخش نیاز به ارتقای پلن لایسنس دارد</h3>
            <p class="text-muted small max-w-600 mx-auto">
                ماژول <strong><?php echo htmlspecialchars($moduleInfo['name'] ?? 'انتخابی'); ?></strong> در پلن فعلی لایسنس شما فعال نمی‌باشد. شما می‌توانید با ارتقای پلن یا فعال‌سازی تک‌ماژول به تمامی قابلیت‌های آن دسترسی پیدا کنید.
            </p>
        </div>

        <?php if ($moduleInfo): ?>
            <div class="p-3 rounded-3 bg-light border mb-4 text-start">
                <div class="d-flex align-items-center gap-2 fw-bold text-dark mb-1">
                    <i class="<?php echo $moduleInfo['icon']; ?> text-primary"></i>
                    <span>قابلیت‌های ماژول <?php echo htmlspecialchars($moduleInfo['name']); ?>:</span>
                </div>
                <p class="small text-muted mb-0"><?php echo htmlspecialchars($moduleInfo['desc']); ?></p>
            </div>
        <?php endif; ?>

        <div class="row g-3 justify-content-center mb-4">
            <div class="col-md-6">
                <div class="p-4 rounded-4 border bg-white h-100 text-center">
                    <i class="fas fa-headset fa-2x text-primary mb-2"></i>
                    <h6 class="fw-bold text-dark mb-1">تماس با واحد فروش و پشتیبانی</h6>
                    <p class="small text-muted mb-3">جهت دریافت مشاوره رایگان و فعال‌سازی فوری لایسنس با کارشناسان ما تماس بگیرید.</p>
                    <a href="tel:04134401179" class="btn btn-outline-primary rounded-pill px-4 fw-bold">
                        <i class="fas fa-phone me-1"></i> ۰۴۱-۳۴۴۰۱۱۷۹
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 rounded-4 border bg-white h-100 text-center">
                    <i class="fas fa-certificate fa-2x text-success mb-2"></i>
                    <h6 class="fw-bold text-dark mb-1">لایسنس جدید دریافت کرده‌اید؟</h6>
                    <p class="small text-muted mb-3">کد لایسنس اختصاصی خود را در بخش فعال‌سازی وارد نموده تا ماژول بلافاصله باز شود.</p>
                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=licensing" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-key me-1"></i> فعال‌سازی لایسنس
                    </a>
                </div>
            </div>
        </div>

        <div class="border-top pt-3">
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=dashboard" class="small text-muted text-decoration-none">
                <i class="fas fa-arrow-right me-1"></i> بازگشت به پیشخوان مدیریت
            </a>
        </div>
    </div>
</div>
