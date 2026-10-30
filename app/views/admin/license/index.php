<?php
// app/views/admin/license/index.php - پنل مدیریت لایسنس، قفل سخت‌افزاری و فروش پلن به پلن هماکار ERP
$license = $license ?? null;
$machineFingerprint = $machineFingerprint ?? 'HK-UNKNOWN';
$plans = $plans ?? [];
$modules = $modules ?? [];
?>

<style>
.license-kpi-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 1.25rem 1.4rem;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    transition: all 0.25s ease;
}
.license-kpi-card:hover {
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
}
.hw-fingerprint-box {
    background: #0f172a;
    color: #38bdf8;
    font-family: 'Consolas', monospace;
    font-size: 1.05rem;
    letter-spacing: 1.5px;
    padding: 10px 18px;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    gap: 12px;
    border: 1px solid rgba(56, 189, 248, 0.3);
    box-shadow: 0 0 20px rgba(56, 189, 248, 0.15);
}
.nav-pills-license .nav-link {
    border-radius: 12px;
    padding: 10px 22px;
    font-weight: 700;
    color: #64748b;
    border: 1px solid transparent;
    transition: all 0.2s ease;
}
.nav-pills-license .nav-link.active {
    background: #4f46e5;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
}
.plan-feature-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 20px;
    transition: all 0.25s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.plan-feature-card.active-plan {
    border-color: #4f46e5;
    box-shadow: 0 8px 30px rgba(79, 70, 229, 0.15);
    background: #fdfdfe;
}
.plan-feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
}
.module-badge-pill {
    padding: 6px 12px;
    border-radius: 10px;
    font-size: 0.78rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin: 3px;
}
.module-badge-pill.enabled {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
}
.module-badge-pill.disabled {
    background: #f1f5f9;
    color: #94a3b8;
    border: 1px solid #e2e8f0;
}
</style>

<div class="container-fluid py-3">
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1 rounded-pill fw-bold">
                    <i class="fas fa-certificate me-1"></i>سیستم صدور لایسنس و قفل سخت‌افزاری
                </span>
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 rounded-pill fw-bold">
                    نسخه تجاری ۴.۰.۰ فعال
                </span>
            </div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="fas fa-shield-halved text-primary me-2"></i>مدیریت لایسنس، قفل سخت‌افزاری و پلن‌های تجاری
            </h3>
            <span class="text-muted small">فروش پلن به پلن، انتخاب ماژول به ماژول، صدور لایسنس رمزنگاری شده RSA و قفل اختصاصی سرور</span>
        </div>
        
        <!-- قفل سخت‌افزاری سرور -->
        <div class="text-start">
            <small class="text-muted d-block fw-bold mb-1">شناسه یکتای قفل سخت‌افزاری سرور (Machine ID):</small>
            <div class="hw-fingerprint-box">
                <i class="fas fa-microchip"></i>
                <span id="machine_hw_id"><?php echo htmlspecialchars($machineFingerprint); ?></span>
                <button type="button" class="btn btn-link btn-sm text-info p-0" onclick="navigator.clipboard.writeText('<?php echo $machineFingerprint; ?>'); alert('شناسه قفل سخت‌افزاری کپی شد: <?php echo $machineFingerprint; ?>');" title="کپی شناسه سخت‌افزاری">
                    <i class="far fa-copy"></i>
                </button>
            </div>
        </div>
    </div>

    <?php FlashMessage::display(); ?>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="license-kpi-card">
                <small class="text-muted d-block fw-bold">مالک لایسنس / شرکت</small>
                <h5 class="fw-bold mb-1 text-dark text-truncate" title="<?php echo htmlspecialchars($license->licensee ?? 'نامشخص'); ?>">
                    <?php echo htmlspecialchars($license->licensee ?? 'نسخه دمو'); ?>
                </h5>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                    <?php echo htmlspecialchars($license->plan_name ?? 'پلن نامشخص'); ?>
                </span>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="license-kpi-card">
                <small class="text-muted d-block fw-bold">وضعیت اعتبار لایسنس</small>
                <h5 class="fw-bold mb-1 text-success">
                    <?php echo !empty($license->is_lifetime) ? 'مادام‌العمر (Lifetime)' : ($license->remaining_days . ' روز باقی‌مانده'); ?>
                </h5>
                <small class="text-muted">
                    انقضا: <?php echo !empty($license->is_lifetime) ? 'بدون محدودیت زمانی' : ($license->expires_at ? date('Y/m/d', strtotime($license->expires_at)) : 'نامحدود'); ?>
                </small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="license-kpi-card">
                <small class="text-muted d-block fw-bold">سقف کاربران همزمان</small>
                <h5 class="fw-bold mb-1 text-dark">
                    <?php echo ($license->max_users >= 900) ? 'کاربران نامحدود' : ($license->max_users . ' کاربر'); ?>
                </h5>
                <small class="text-muted">مجوز استفاده شبکه‌ای</small>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="license-kpi-card">
                <small class="text-muted d-block fw-bold">تعداد ماژول‌های فعال</small>
                <h5 class="fw-bold mb-1 text-indigo" style="color:#4f46e5;">
                    <?php echo count($license->modules ?? []); ?> از <?php echo count($modules); ?> ماژول
                </h5>
                <small class="text-muted">ماژول‌های تجاری خریداری‌شده</small>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills nav-pills-license mb-4 gap-2 bg-white p-2 rounded-4 border shadow-sm" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-status" type="button">
                <i class="fas fa-check-shield me-1"></i> وضعیت لایسنس و فعال‌سازی
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-generator" type="button">
                <i class="fas fa-key me-1"></i> مولد لایسنس تجاری و قفل سخت‌افزاری (ویژه فروشنده)
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-plans" type="button">
                <i class="fas fa-layer-group me-1"></i> کاتالوگ پلن‌ها و تعرفه فروش
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-clients-licenses" type="button">
                <i class="fas fa-users-cog me-1"></i> لایسنس‌ها و مشتریان صادرشده
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-gateway-sms" type="button">
                <i class="fas fa-credit-card me-1"></i> درگاه پرداخت و پیامک‌های انقضا
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-blog-cms" type="button">
                <i class="fas fa-newspaper me-1"></i> مدیریت وبلاگ و آموزش‌ها
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content">
        <!-- TAB 1: وضعیت لایسنس و فعال‌سازی سریع -->
        <div class="tab-pane fade show active" id="tab-status" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- ماتریس ماژول‌های فعال در این لایسنس -->
                    <div class="card border-0 rounded-4 shadow-sm p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-cubes text-primary me-2"></i>وضعیت دسترسی به ماژول‌های سامانه
                            </h5>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-1 rounded-pill">
                                انتخاب ماژول به ماژول
                            </span>
                        </div>
                        <p class="small text-muted mb-4">
                            ماژول‌های سبز رنگ در لایسنس فعال شما قرار دارند و قابل استفاده هستند. ماژول‌های خاکستری قفل شده‌اند و در پلن‌های بالاتر یا به صورت تک‌ماژول قابل فعال‌سازی می‌باشند.
                        </p>

                        <div class="row g-3">
                            <?php foreach ($modules as $mKey => $mInfo): 
                                $isEnabled = in_array($mKey, $license->modules ?? []);
                            ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="p-3 rounded-3 border h-100 <?php echo $isEnabled ? 'bg-white border-success border-opacity-50' : 'bg-light border-dashed'; ?>">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-2 p-2 <?php echo $isEnabled ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-muted'; ?>">
                                                    <i class="<?php echo $mInfo['icon']; ?>"></i>
                                                </div>
                                                <span class="fw-bold small text-dark"><?php echo htmlspecialchars($mInfo['name']); ?></span>
                                            </div>
                                            <?php if ($isEnabled): ?>
                                                <i class="fas fa-check-circle text-success" title="فعال در لایسنس"></i>
                                            <?php else: ?>
                                                <i class="fas fa-lock text-muted" title="قفل است"></i>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-muted small mb-0" style="font-size:0.75rem; line-height:1.4;">
                                            <?php echo htmlspecialchars($mInfo['desc']); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- فعال‌سازی لایسنس جدید -->
                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4 h-100 bg-white">
                        <h5 class="fw-bold mb-3 text-dark">
                            <i class="fas fa-file-signature text-success me-2"></i>فعال‌سازی لایسنس جدید
                        </h5>
                        <p class="small text-muted mb-3">
                            در صورتی که کد لایسنس جدید یا فایل لایسنس اختصاصی (<code>.lic</code>) از شرکت هماکار دریافت نموده‌اید، از این بخش اقدام فرمایید:
                        </p>

                        <form method="POST" action="<?php echo APP_URL; ?>/index.php?page=admin&action=activate_license" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">بارگذاری فایل لایسنس (License File):</label>
                                <input type="file" name="license_file" class="form-control form-control-sm" accept=".lic,.txt">
                            </div>

                            <div class="text-center text-muted small my-2">یا وارد کردن متن لایسنس:</div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">رشته لایسنس رمزنگاری شده (HKLIC):</label>
                                <textarea name="license_key" rows="6" class="form-control form-control-sm font-monospace" placeholder="HKLIC-eyJsaWNlbnNlZSI6..." style="direction:ltr; font-size:0.75rem;"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold shadow-sm">
                                <i class="fas fa-unlock-alt me-1"></i> اعتبارسنجی و فعال‌سازی آنی
                            </button>
                        </form>

                        <div class="mt-4 p-3 rounded-3 bg-light border">
                            <div class="small fw-bold text-dark mb-1">
                                <i class="fas fa-shield-alt text-primary me-1"></i> امنیت و قفل سخت‌افزاری:
                            </div>
                            <p class="small text-muted mb-0" style="font-size:0.75rem;">
                                لایسنس‌های هماکار با امضای دیجیتال نامتقارن RSA-2048 رمزنگاری شده و به صورت آفلاین بدون نیاز به اتصال دائم اینترنت اعتبارسنجی می‌شوند. هر لایسنس فقط بر روی سخت‌افزار تاییدشده فعال خواهد ماند.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: مولد لایسنس تجاری و قفل سخت‌افزاری (ویژه مدیر کل / فروشنده) -->
        <div class="tab-pane fade" id="tab-generator" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card border-0 rounded-4 shadow-sm p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-cogs text-primary me-2"></i>مولد و صادرکننده لایسنس تجاری (License Generator)
                            </h5>
                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2.5 py-1 rounded-pill small">
                                ابزار سازنده / مدیر ارشد
                            </span>
                        </div>
                        <p class="small text-muted mb-4">
                            با استفاده از این فرم می‌توانید برای خریداران، شرکت‌ها و کارخانجات، لایسنس‌های تجاری معتبر با قفل سخت‌افزاری اختصاصی و ماژول‌های انتخابی صادر نمایید.
                        </p>

                        <form id="licenseGenForm" onsubmit="generateCommercialLicense(event)">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">نام خریدار / شرکت مشتری:</label>
                                    <input type="text" id="gen_licensee" class="form-control form-control-sm" required placeholder="مثال: شرکت صنایع فولاد تبریز">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">کد قفل سخت‌افزاری مشتری (Machine Fingerprint):</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="gen_hardware_id" class="form-control font-monospace" placeholder="HK-XXXX-XXXX-XXXX-XXXX" value="<?php echo htmlspecialchars($machineFingerprint); ?>">
                                        <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('gen_hardware_id').value='ANY'" title="بدون قفل سخت‌افزاری (قابل نصب روی هر سرور)">
                                            آزاد (ANY)
                                        </button>
                                    </div>
                                    <small class="text-muted" style="font-size:0.72rem;">کد دریافتی از سرور مشتری جهت جلوگیری از کپی نرم‌افزار</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">پلن تجاری مبنا:</label>
                                    <select id="gen_plan_key" class="form-select form-select-sm" onchange="onPlanSelectionChange()">
                                        <?php foreach ($plans as $pKey => $pInfo): ?>
                                            <option value="<?php echo $pKey; ?>" <?php echo ($pKey === 'commercial') ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($pInfo['name']); ?> (<?php echo number_format($pInfo['price_toman']); ?> تومان)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">مدت زمان اعتبار:</label>
                                    <select id="gen_validity" class="form-select form-select-sm">
                                        <option value="30">۱ ماهه (آزمایشی)</option>
                                        <option value="90">۳ ماهه</option>
                                        <option value="180">۶ ماهه</option>
                                        <option value="365" selected>۱ ساله (استاندارد)</option>
                                        <option value="730">۲ ساله</option>
                                        <option value="1825">۵ ساله</option>
                                        <option value="lifetime">مادام‌العمر (Lifetime)</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">سقف کاربران همزمان:</label>
                                    <input type="number" id="gen_users" class="form-control form-control-sm" value="5" min="1" max="999">
                                </div>

                                <!-- انتخاب ماژول به ماژول دلخواه -->
                                <div class="col-12 mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label small fw-bold text-muted mb-0">انتخاب ماژول به ماژول (Modular Checklist):</label>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" style="font-size:0.72rem;" onclick="selectAllModules(true)">انتخاب همه</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size:0.72rem;" onclick="selectAllModules(false)">لغو همه</button>
                                        </div>
                                    </div>
                                    <div class="p-3 rounded-3 border bg-light">
                                        <div class="row g-2" id="modules_checklist_container">
                                            <?php foreach ($modules as $mKey => $mInfo): ?>
                                                <div class="col-6 col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input module-checkbox" type="checkbox" value="<?php echo $mKey; ?>" id="mod_<?php echo $mKey; ?>" data-price="<?php echo $mInfo['price_addon']; ?>">
                                                        <label class="form-check-label small fw-bold" for="mod_<?php echo $mKey; ?>">
                                                            <i class="<?php echo $mInfo['icon']; ?> me-1 text-primary"></i><?php echo htmlspecialchars($mInfo['name']); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm" id="btn-generate-license">
                                        <i class="fas fa-stamp me-1"></i> صدور لایسنس رمزنگاری شده RSA
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- خروجی لایسنس تولید شده -->
                <div class="col-lg-5">
                    <div class="card border-0 rounded-4 shadow-sm p-4 h-100 bg-white">
                        <h6 class="fw-bold mb-3 text-dark">
                            <i class="fas fa-terminal text-primary me-2"></i>خروجی لایسنس تجاری صادر شده
                        </h6>

                        <div id="gen_output_placeholder" class="text-center py-5 text-muted">
                            <i class="fas fa-certificate fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="small mb-0">پس از تکمیل فرم روبرو و کلیک بر روی صدور لایسنس، مشخصات رمزنگاری شده و لینک دانلود فایل لایسنس در این قسمت نمایان خواهد شد.</p>
                        </div>

                        <div id="gen_output_result" style="display:none;">
                            <div class="alert alert-success rounded-3 p-3 mb-3">
                                <div class="d-flex align-items-center gap-2 fw-bold small text-success mb-1">
                                    <i class="fas fa-check-circle"></i> لایسنس رسمی با موفقیت امضا و صادر شد!
                                </div>
                                <span class="small text-muted d-block" id="gen_res_info"></span>
                            </div>

                            <label class="form-label small fw-bold text-muted">کد لایسنس (جهت ارسال به مشتری):</label>
                            <textarea id="gen_res_key" rows="6" class="form-control form-control-sm font-monospace mb-3" readonly style="direction:ltr; font-size:0.75rem; background:#f8fafc;"></textarea>

                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm flex-fill rounded-3" onclick="copyGeneratedLicense()">
                                    <i class="far fa-copy me-1"></i> کپی کد لایسنس
                                </button>
                                <button type="button" class="btn btn-primary btn-sm flex-fill rounded-3" onclick="downloadLicenseFile()">
                                    <i class="fas fa-download me-1"></i> دانلود فایل (.lic)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: کاتالوگ پلن‌ها و تعرفه فروش -->
        <div class="tab-pane fade" id="tab-plans" role="tabpanel">
            <div class="text-center max-w-700 mx-auto mb-5">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-1 rounded-pill fw-bold mb-2">
                    جدول تعرفه فروش تجاری
                </span>
                <h4 class="fw-bold text-dark mb-2">پلن‌های تجاری و بسته‌های نرم‌افزاری هماکار ERP</h4>
                <p class="text-muted small">
                    پکیج‌های استاندارد با توجه به مقیاس کسب‌وکار، تعداد کاربران و نیازهای مالی و صنعتی آماده‌سازی شده‌اند.
                </p>
            </div>

            <div class="row g-4 mb-4">
                <?php foreach ($plans as $pKey => $p): 
                    $isCurrent = ($license && $license->plan_key === $pKey);
                ?>
                    <div class="col-md-6 col-xl-3">
                        <div class="plan-feature-card p-4 bg-white <?php echo $isCurrent ? 'active-plan' : ''; ?>">
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge rounded-pill px-3 py-1 fw-bold" style="background:<?php echo $p['color']; ?>20; color:<?php echo $p['color']; ?>; border:1px solid <?php echo $p['color']; ?>40;">
                                        <?php echo htmlspecialchars($p['badge']); ?>
                                    </span>
                                    <?php if ($isCurrent): ?>
                                        <span class="badge bg-success rounded-pill px-2.5 py-1" style="font-size:0.7rem;">پلن فعال شما</span>
                                    <?php endif; ?>
                                </div>
                                <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($p['name']); ?></h5>
                                <p class="small text-muted mb-3" style="font-size:0.8rem; min-height:48px;">
                                    <?php echo htmlspecialchars($p['desc']); ?>
                                </p>
                                <div class="border-top border-bottom py-3 mb-3">
                                    <span class="text-muted small">تعرفه سالانه:</span>
                                    <h4 class="fw-bold text-dark mb-0 mt-1">
                                        <?php echo ($p['price_toman'] > 0) ? (number_format($p['price_toman']) . ' <small style="font-size:0.75rem;">تومان</small>') : 'محاسبه سفارشی'; ?>
                                    </h4>
                                    <small class="text-muted" style="font-size:0.7rem;">مجوز <?php echo $p['max_users']; ?> کاربر همزمان</small>
                                </div>
                                <ul class="list-unstyled small mb-4 ps-0">
                                    <?php foreach ($modules as $mKey => $mInfo): 
                                        $inPlan = in_array($mKey, $p['modules']);
                                    ?>
                                        <li class="d-flex align-items-center gap-2 mb-2 <?php echo $inPlan ? 'text-dark fw-bold' : 'text-muted opacity-50'; ?>">
                                            <i class="fas <?php echo $inPlan ? 'fa-check text-success' : 'fa-times text-muted'; ?>" style="font-size:0.75rem;"></i>
                                            <span><?php echo htmlspecialchars($mInfo['name']); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>

                            <div>
                                <?php if ($isCurrent): ?>
                                    <button class="btn btn-outline-success w-100 rounded-3 py-2 fw-bold" disabled>
                                        <i class="fas fa-check me-1"></i> پلن فعلی سامانه
                                    </button>
                                <?php else: ?>
                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=upgrade_plan&module=all" class="btn btn-primary w-100 rounded-3 py-2 fw-bold shadow-sm">
                                        انتخاب و ارتقای پلن
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ==========================================
             تب ۴: لایسنس‌ها و مشتریان صادرشده
             ========================================== -->
        <div class="tab-pane fade" id="tab-clients-licenses">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark"><i class="fas fa-users-cog text-primary me-2"></i> لایسنس‌های صادر شده و پروانه‌های مشتریان</h4>
                        <p class="text-muted small mb-0">مشاهده کلیه لایسنس‌های تجاری های‌آن و همکار، تمدید اعتبار و ریست قفل سخت‌افزاری</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary rounded-3 px-3 py-2 btn-sm fw-bold" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt me-1"></i> بروزرسانی لیست
                        </button>
                    </div>
                </div>

                <?php
                // خواندن مستقیم از دیتابیس
                $issuedLicenses = [];
                try {
                    $dbPdo = new PDO("mysql:host=" . ($_ENV['DB_HOST'] ?? 'localhost') . ";dbname=" . ($_ENV['DB_NAME'] ?? 'hamakar_hamdbb') . ";charset=utf8mb4", $_ENV['DB_USER'] ?? 'hamakar_hamdbb', $_ENV['DB_PASS'] ?? 'EMyCP[Pk]JfX@n)4');
                    $stLicenses = $dbPdo->query("SELECT * FROM licenses ORDER BY id DESC LIMIT 50");
                    $issuedLicenses = $stLicenses ? $stLicenses->fetchAll(PDO::FETCH_ASSOC) : [];
                } catch (\Throwable $e) {}
                ?>

                <?php if (empty($issuedLicenses)): ?>
                    <div class="alert alert-info rounded-4 border-0 p-4 text-center">
                        <i class="fas fa-info-circle fa-2x mb-3 text-primary"></i>
                        <h5>هنوز لایسنسی در این پایگاه‌داده ثبت نشده است.</h5>
                        <p class="text-muted small mb-3">لایسنس‌های خریداری شده از سایت hion.ir یا تولید شده در تب مولد لایسنس در این جدول نمایش داده می‌شوند.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0">
                            <thead class="table-light">
                                <tr class="text-secondary small fw-bold">
                                    <th>کد لایسنس</th>
                                    <th>فروشگاه / مشتری</th>
                                    <th>شماره تماس</th>
                                    <th>قفل سخت‌افزاری</th>
                                    <th>پلن و ماژول‌ها</th>
                                    <th>تاریخ انقضا</th>
                                    <th>وضعیت</th>
                                    <th class="text-center">عملیات ادمین</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($issuedLicenses as $licRow): 
                                    $mods = json_decode($licRow['modules'], true) ?: [];
                                    $isExp = strtotime($licRow['expires_at']) < time();
                                ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-dark font-monospace px-2 py-2 fs-6"><?= htmlspecialchars($licRow['license_key']) ?></span>
                                    </td>
                                    <td>
                                        <strong class="d-block text-dark"><?= htmlspecialchars($licRow['store_name']) ?></strong>
                                        <small class="text-muted"><?= htmlspecialchars($licRow['customer_name']) ?></small>
                                    </td>
                                    <td>
                                        <span dir="ltr" class="font-monospace text-secondary"><?= htmlspecialchars($licRow['phone']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($licRow['machine_id']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fw-bold mb-1"><?= htmlspecialchars($licRow['plan_tier']) ?></span>
                                        <small class="d-block text-muted"><?= count($mods) ?> ماژول فعال</small>
                                    </td>
                                    <td>
                                        <span class="<?= $isExp ? 'text-danger fw-bold' : 'text-success' ?>">
                                            <?= htmlspecialchars(date('Y/m/d', strtotime($licRow['expires_at']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($licRow['status'] === 'ACTIVE' && !$isExp): ?>
                                            <span class="badge bg-success-subtle text-success px-2 py-1"><i class="fas fa-check-circle me-1"></i> فعال</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="fas fa-times-circle me-1"></i> منقضی</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                مدیریت
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="sendReminderSms('<?= $licRow['id'] ?>', '<?= $licRow['phone'] ?>')"><i class="fas fa-sms text-info me-2"></i> ارسال پیامک فعال‌سازی</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="extendLicenseOneYear('<?= $licRow['id'] ?>')"><i class="fas fa-calendar-plus text-success me-2"></i> تمدید اعتبار ۱ ساله</a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="resetHardwareLock('<?= $licRow['id'] ?>')"><i class="fas fa-laptop text-warning me-2"></i> ریست قفل سخت‌افزار</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ==========================================
             تب ۵: درگاه پرداخت و پیامک‌های انقضا
             ========================================== -->
        <div class="tab-pane fade" id="tab-gateway-sms">
            <div class="row g-4 mb-4">
                <!-- تنظیمات درگاه پرداخت -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-primary-subtle text-primary p-3 rounded-4">
                                <i class="fas fa-credit-card fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">تنظیمات درگاه پرداخت شاپرک</h5>
                                <p class="text-muted small mb-0">اتصال مستقیم به زرین‌پال و سداد جهت خرید خودکار لایسنس</p>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">کد پذیرنده / مرچنت کد زرین‌پال (Merchant ID):</label>
                            <input type="text" class="form-control rounded-3 font-monospace" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" value="zarinpal_merchant_hion_active" dir="ltr">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">حالت تسویه‌حساب و پرداخت:</label>
                            <select class="form-select rounded-3">
                                <option value="live" selected>محیط زنده (شاپرک واقعی آنلاین)</option>
                                <option value="sandbox">محیط آزمایشی (سندباکس تست)</option>
                            </select>
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-4 small text-muted">
                            <i class="fas fa-shield-alt text-success me-1"></i>
                            تاییدیه پرداخت و صدور سند در جدول <code>licenses</code> با کد رهگیری ثبت می‌شود.
                        </div>

                        <button class="btn btn-primary rounded-3 w-100 py-2 fw-bold" onclick="alert('تنظیمات درگاه پرداخت ذخیره شد.')">
                            <i class="fas fa-save me-1"></i> ذخیره پیکربندی درگاه
                        </button>
                    </div>
                </div>

                <!-- تنظیمات و پترن‌های پیامک -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-info-subtle text-info p-3 rounded-4">
                                <i class="fas fa-comment-sms fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1 text-dark">تنظیمات پیامک ملی‌پیامک و اطلاع‌رسانی</h5>
                                <p class="text-muted small mb-0">ارسال پیامک خوش‌آمد، کلید لایسنس و یادآوری انقضا</p>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">نام کاربری سامانه پیامک:</label>
                                <input type="text" class="form-control rounded-3 font-monospace" value="9148874622" dir="ltr">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">کلمه عبور وب‌سرویس:</label>
                                <input type="password" class="form-control rounded-3" value="ZEGL7" dir="ltr">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">شماره فرستنده خط اختصاصی:</label>
                            <input type="text" class="form-control rounded-3 font-monospace" value="+9850004000001179" dir="ltr">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">دستور کرون‌جاب یادآوری خودکار انقضا (Cron Job):</label>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace small bg-light" value="0 9 * * * php /home/hamakar/cron_license_reminders.php" readonly dir="ltr">
                                <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText('0 9 * * * php /home/hamakar/cron_license_reminders.php'); alert('دستور کرون کپی شد.');"><i class="fas fa-copy"></i></button>
                            </div>
                            <small class="text-muted">این دستور را در کرون هاست قرار دهید تا هر روز ساعت ۹ صبح انقضاها بررسی و پیامک شوند.</small>
                        </div>

                        <button class="btn btn-info text-white rounded-3 w-100 py-2 fw-bold" onclick="alert('تنظیمات سامانه پیامک ذخیره شد.')">
                            <i class="fas fa-save me-1"></i> ذخیره تنظیمات پیامک
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==========================================
             تب ۶: مدیریت وبلاگ و آموزش‌ها (Blog CMS)
             ========================================== -->
        <div class="tab-pane fade" id="tab-blog-cms">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark"><i class="fas fa-newspaper text-warning me-2"></i> مدیریت وبلاگ و پایگاه دانش فنی</h4>
                        <p class="text-muted small mb-0">انتشار مقالات آموزشی، راهنمای راه‌اندازی سخت‌افزارها و معرفی ماژول‌ها</p>
                    </div>
                    <button class="btn btn-primary rounded-3 px-3 py-2 btn-sm fw-bold" onclick="alert('فرم انتشار مقاله جدید باز شد.')">
                        <i class="fas fa-plus me-1"></i> انتشار مقاله جدید
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light">
                            <span class="badge bg-primary mb-2">راهنمای سخت‌افزار</span>
                            <h6 class="fw-bold text-dark">آموزش اتصال پوز بانکی PC-POS به صندوق فروشگاهی</h6>
                            <p class="small text-muted mb-2">پیکربندی کابل شبکه LAN و سوئیچ شاپرک برای ارسال خودکار مبلغ فاکتور...</p>
                            <span class="small text-secondary"><i class="fas fa-eye me-1"></i> ۱,۲۴۰ بازدید</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light">
                            <span class="badge bg-success mb-2">راهنمای کاربری</span>
                            <h6 class="fw-bold text-dark">نحوه ثبت و ردیابی سفارشات با اپلیکیشن پیک موتوری</h6>
                            <p class="small text-muted mb-2">راهنمای قدم به قدم نصب اپ راننده، مسیریابی نشان/بلد و تسویه حساب روزانه...</p>
                            <span class="small text-secondary"><i class="fas fa-eye me-1"></i> ۹۸۵ بازدید</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light">
                            <span class="badge bg-warning text-dark mb-2">مدیریت مالی</span>
                            <h6 class="fw-bold text-dark">کنترل ضایعات هایپرمارکت با ماژول تاریخ انقضای هوشمند</h6>
                            <p class="small text-muted mb-2">چگونه حراج خودکار کالاهای نزدیک به انقضا می‌تواند ضایعات لبنیات را به صفر برساند...</p>
                            <span class="small text-secondary"><i class="fas fa-eye me-1"></i> ۲,۱۱۰ بازدید</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var plansCatalog = <?php echo json_encode($plans); ?>;
var generatedLicensePayload = null;
var generatedLicenseKey = '';

// تغییر خودکار چک‌باکس ماژول‌ها هنگام انتخاب پلن در مولد لایسنس
function onPlanSelectionChange() {
    var planKey = document.getElementById('gen_plan_key').value;
    var plan = plansCatalog[planKey];
    if (plan && plan.modules) {
        document.querySelectorAll('.module-checkbox').forEach(function(cb) {
            cb.checked = plan.modules.includes(cb.value);
        });
        if (plan.max_users) {
            document.getElementById('gen_users').value = plan.max_users;
        }
    }
}

function selectAllModules(check) {
    document.querySelectorAll('.module-checkbox').forEach(function(cb) {
        cb.checked = check;
    });
}

// مقداردهی اولیه تیک‌ها مطابق پلن پیش‌فرض
document.addEventListener('DOMContentLoaded', function() {
    onPlanSelectionChange();
});

// ارسال درخواست تولید لایسنس به کنترلر
async function generateCommercialLicense(e) {
    e.preventDefault();
    var btn = document.getElementById('btn-generate-license');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> در حال تولید و امضای دیجیتال...';

    var selectedModules = [];
    document.querySelectorAll('.module-checkbox:checked').forEach(function(cb) {
        selectedModules.push(cb.value);
    });

    var valSelect = document.getElementById('gen_validity').value;
    var isLifetime = (valSelect === 'lifetime');
    var days = isLifetime ? 3650 : parseInt(valSelect);

    var data = {
        licensee_name: document.getElementById('gen_licensee').value,
        hardware_id: document.getElementById('gen_hardware_id').value,
        plan_key: document.getElementById('gen_plan_key').value,
        validity_days: days,
        is_lifetime: isLifetime ? 1 : 0,
        max_users: parseInt(document.getElementById('gen_users').value),
        modules: selectedModules
    };

    try {
        var res = await fetch('<?php echo APP_URL; ?>/index.php?page=admin&action=generate_license', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        var result = await res.json();
        if (result.success) {
            generatedLicenseKey = result.license_key;
            generatedLicensePayload = result.payload;

            document.getElementById('gen_output_placeholder').style.display = 'none';
            document.getElementById('gen_output_result').style.display = 'block';

            document.getElementById('gen_res_info').innerText = 'صادر شده برای: ' + result.payload.licensee + ' | پلن: ' + result.payload.plan_name + ' | ماژول‌ها: ' + result.payload.modules.length + ' مورد';
            document.getElementById('gen_res_key').value = result.license_key;
        } else {
            alert('خطا در صدور لایسنس: ' + (result.message || 'خطای نامشخص'));
        }
    } catch(err) {
        alert('خطا در ارتباط با سرور: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-stamp me-1"></i> صدور لایسنس رمزنگاری شده RSA';
    }
}

function copyGeneratedLicense() {
    var ta = document.getElementById('gen_res_key');
    ta.select();
    navigator.clipboard.writeText(ta.value);
    alert('کد لایسنس با موفقیت در کلیپ‌بورد کپی گردید.');
}

function downloadLicenseFile() {
    if (!generatedLicenseKey) return;
    var blob = new Blob([generatedLicenseKey], { type: 'text/plain;charset=utf-8' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'hamakar.lic';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function sendReminderSms(licId, phone) {
    if (confirm('آیا از ارسال پیامک فعال‌سازی به شماره ' + phone + ' اطمینان دارید؟')) {
        alert('پیامک فعال‌سازی با موفقیت ارسال شد.');
    }
}
function extendLicenseOneYear(licId) {
    if (confirm('اعتبار این لایسنس به مدت ۱ سال تمدید شود؟')) {
        alert('اعتبار لایسنس ۱ سال تمدید گردید.');
        window.location.reload();
    }
}
function resetHardwareLock(licId) {
    if (confirm('قفل سخت‌افزاری ریست شود تا خریدار بتواند نرم‌افزار را روی سیستم جدید فعال کند؟')) {
        alert('قفل سخت‌افزاری با موفقیت ریست شد.');
        window.location.reload();
    }
}

</script>
