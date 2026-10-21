<?php
// app/views/client/my_licenses.php - نسخه اولترا پرمیوم
$licenses = $licenses ?? [];

$totalLicenses = count($licenses);
$activeLicensesCount = 0;
$expiredLicensesCount = 0;

foreach ($licenses as $lic) {
    $expiresAt = !empty($lic->expires_at) ? strtotime($lic->expires_at) : 0;
    $status = $lic->status ?? 'active';

    if ($status === 'active' && $expiresAt > time()) {
        $activeLicensesCount++;
    } else {
        $expiredLicensesCount++;
    }
}
?>

<style>
    .licenses-card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .license-box-card {
        background: #ffffff;
        border-radius: 22px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
        padding: 1.5rem;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }

    .license-box-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 35px -5px rgba(99, 102, 241, 0.15);
        border-color: #a5b4fc;
    }

    .license-key-container {
        background: #0f172a;
        color: #fde047;
        border-radius: 14px;
        padding: 0.85rem 1.1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-family: monospace, 'Vazirmatn';
        font-weight: 700;
        letter-spacing: 1px;
        font-size: 0.95rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-copy-key {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 0.35rem 0.65rem;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-copy-key:hover {
        background: rgba(255, 255, 255, 0.3);
        color: #ffffff;
    }

    .btn-renew-license {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 12px;
        padding: 0.65rem 1.25rem;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 6px 18px rgba(99, 102, 241, 0.25);
        transition: all 0.25s ease;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .btn-renew-license:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(99, 102, 241, 0.35);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">ربات‌های من و لایسنس‌ها</h1>
        <p class="text-muted small mb-0">مدیریت لایسنس‌های فعال ربات‌ها، تمدید اشتراک و بررسی تاریخ انقضا</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo APP_URL; ?>/index.php?page=client&action=dashboard" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
            <i class="fas fa-arrow-right"></i>
            <span>بازگشت به داشبورد</span>
        </a>
    </div>
</div>

<!-- کارت‌های خلاصه وضعیت لایسنس‌ها -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">کل لایسنس‌های صادر شده</span>
                <h4 class="fw-black text-dark font-monospace mb-0"><?php echo number_format($totalLicenses); ?></h4>
            </div>
            <div class="rounded-3 p-3 text-indigo-600" style="background:#e0e7ff; color:#4338ca;">
                <i class="fas fa-robot fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">لایسنس‌های فعال</span>
                <h4 class="fw-black text-emerald-600 font-monospace mb-0" style="color:#059669;"><?php echo number_format($activeLicensesCount); ?></h4>
            </div>
            <div class="rounded-3 p-3 text-emerald-600" style="background:#d1fae5; color:#059669;">
                <i class="fas fa-circle-check fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">منقضی شده / نیازمند تمدید</span>
                <h4 class="fw-black text-danger font-monospace mb-0"><?php echo number_format($expiredLicensesCount); ?></h4>
            </div>
            <div class="rounded-3 p-3 text-danger" style="background:#ffe4e6; color:#e11d48;">
                <i class="fas fa-clock-rotate-left fs-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- شبکه گرید کارت‌های لایسنس -->
<div class="row g-4 mb-4">
    <?php if (!empty($licenses)): ?>
        <?php foreach ($licenses as $license): ?>
            <?php
                $expiresAt = !empty($license->expires_at) ? strtotime($license->expires_at) : 0;
                $isExpired = ($expiresAt < time());
                $status = $license->status ?? 'active';

                $remainingDays = 0;
                if ($expiresAt > time()) {
                    $remainingDays = floor(($expiresAt - time()) / (60 * 60 * 24));
                }

                $badgeClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                $statusText = 'فعال';

                if ($isExpired || $status === 'expired') {
                    $badgeClass = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                    $statusText = 'منقضی شده';
                } elseif ($status === 'suspended') {
                    $badgeClass = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                    $statusText = 'معلق شده';
                }
            ?>
            <div class="col-lg-6">
                <div class="license-box-card">
                    <div>
                        <!-- هدر لایسنس -->
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 p-2 text-warning bg-dark border border-secondary" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-robot fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0"><?php echo sanitize($license->plan_title ?? 'پلن اشتراک عمومی'); ?></h6>
                                    <span class="small text-muted font-monospace">شناسه: #LIC-<?php echo sanitize($license->id); ?></span>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold <?php echo $badgeClass; ?>">
                                <?php echo $statusText; ?>
                            </span>
                        </div>

                        <!-- کد لایسنس و دکمه کپی -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">کد اختصاصی لایسنس (License Key):</label>
                            <div class="license-key-container">
                                <span id="key-text-<?php echo $license->id; ?>"><?php echo sanitize($license->license_key); ?></span>
                                <button type="button" class="btn-copy-key" onclick="copyLicenseKey('key-text-<?php echo $license->id; ?>')" title="کپی کد لایسنس">
                                    <i class="far fa-copy me-1"></i> کپی
                                </button>
                            </div>
                        </div>

                        <!-- جزییات زمان و اعتبار -->
                        <div class="row g-2 mb-3 font-monospace small">
                            <div class="col-6">
                                <div class="bg-light p-2.5 rounded-3 border text-center">
                                    <span class="d-block text-muted align-top" style="font-size:0.75rem;">تاریخ فعال‌سازی:</span>
                                    <strong class="text-dark"><?php echo !empty($license->created_at) ? sanitize(jdate('Y/m/d', strtotime($license->created_at))) : '---'; ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light p-2.5 rounded-3 border text-center">
                                    <span class="d-block text-muted align-top" style="font-size:0.75rem;">تاریخ انقضا:</span>
                                    <strong class="<?php echo $isExpired ? 'text-danger' : 'text-dark'; ?>"><?php echo !empty($license->expires_at) ? sanitize(jdate('Y/m/d', strtotime($license->expires_at))) : '---'; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- فوتر و دکمه‌های اکشن -->
                    <div class="border-top pt-3 mt-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <?php if (!$isExpired && $remainingDays > 0): ?>
                                <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold font-monospace" style="background:#e0e7ff; color:#4338ca;">
                                    <i class="fas fa-hourglass-half me-1"></i> <?php echo $remainingDays; ?> روز باقی‌مانده
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1.5 fw-bold font-monospace">
                                    <i class="fas fa-exclamation-triangle me-1"></i> نیازمند تمدید اشتراک
                                </span>
                            <?php endif; ?>
                        </div>

                        <a href="<?php echo APP_URL; ?>/index.php?page=client&action=dashboard" class="btn-renew-license">
                            <i class="fas fa-bolt"></i>
                            <span>تمدید آنلاین اشتراک</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <div class="card shadow-sm border-0 rounded-4 p-5">
                <i class="fas fa-robot fs-1 text-muted mb-3 opacity-40"></i>
                <h6 class="fw-bold text-dark mb-1">هنوز هیچ لایسنسی برای شما صادر نشده است.</h6>
                <p class="text-muted small mb-0">پس از خرید یا فعال‌سازی اشتراک، اطلاعات لایسنس ربات شما در این صفحه قرار می‌گیرد.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function copyLicenseKey(elementId) {
    const textToCopy = document.getElementById(elementId).innerText;
    navigator.clipboard.writeText(textToCopy).then(() => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'کد لایسنس با موفقیت کپی شد',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            alert('کد لایسنس کپی شد!');
        }
    });
}
</script>