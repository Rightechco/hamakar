<?php
// app/views/shared/navbar.php - هدر مدرن، نیتیو و فوق تجاری هماکار ERP
global $auth;
$user = $auth->user();

$unreadNotificationsCount = 5;
$notifications = [
    (object)['created_at' => '۱۴۰۳/۰۸/۰۸ ۱۲:۰۰', 'title' => 'صورتحساب مودیان با موفقیت تایید شد.'],
    (object)['created_at' => '۱۴۰۳/۰۸/۰۷ ۱۵:۳۰', 'title' => 'سررسید چک صیادی ۳ روز آینده است.'],
    (object)['created_at' => '۱۴۰۳/۰۸/۰۶ ۰۹:۱۵', 'title' => 'دستور تولید جدید صادر گردید.'],
];
?>
<style>
    .top-navbar {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(20px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
        padding: 0.6rem 1.25rem !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(226, 232, 240, 0.9) !important;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.03) !important;
        position: sticky;
        top: 0;
        z-index: 1000;
        height: 64px;
    }

    .btn-toggle-sidebar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        padding: 0;
    }

    .btn-toggle-sidebar:hover {
        background: #eef2ff;
        color: #4f46e5;
        border-color: #c7d2fe;
    }

    .nav-control-btn {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        position: relative;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .nav-control-btn:hover {
        background: #eef2ff;
        color: #4f46e5;
        border-color: #c7d2fe;
    }

    .nav-badge-dot {
        position: absolute;
        top: 4px;
        left: 4px;
        width: 8px;
        height: 8px;
        background: #ef4444;
        border-radius: 50%;
        border: 1.5px solid #ffffff;
        box-shadow: 0 0 6px rgba(239, 68, 68, 0.6);
        animation: pulseDot 2s infinite ease-in-out;
    }

    @keyframes pulseDot {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.8; }
    }

    .user-pill-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.35rem 0.75rem 0.35rem 0.4rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 9999px;
        cursor: pointer;
        text-decoration: none;
        color: #0f172a;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .user-pill-btn:hover {
        background: #eef2ff;
        border-color: #c7d2fe;
        color: #4f46e5;
    }

    .user-avatar-img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
        border: 1.5px solid #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.06);
    }

    .dropdown-menu-nuxt {
        border-radius: 18px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 14px 40px rgba(15, 23, 42, 0.12) !important;
        padding: 0.6rem !important;
        min-width: 260px;
        margin-top: 8px !important;
    }

    .dropdown-item-nuxt {
        border-radius: 10px !important;
        padding: 0.65rem 0.85rem !important;
        font-size: 0.85rem !important;
        font-weight: 600 !important;
        color: #334155 !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.55rem !important;
        transition: all 0.15s ease !important;
    }

    .dropdown-item-nuxt:hover {
        background: #f8fafc !important;
        color: #4f46e5 !important;
    }

    .dropdown-item-nuxt.text-danger:hover {
        background: #fef2f2 !important;
        color: #ef4444 !important;
    }

    /* هدر سینک و اپ دسکتاپ */
    .header-sync-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 5px 12px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #334155;
    }

    .btn-header-install {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        color: #ffffff !important;
        border: none;
        padding: 5px 14px;
        border-radius: 9999px;
        font-size: 0.78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-header-install:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4);
        color: #ffffff !important;
    }
</style>

<nav class="top-navbar">
    <!-- Right side: Sidebar Toggler & Brand Title -->
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn-toggle-sidebar" id="sidebarToggleTop" title="منوی سایدبار">
            <i class="fa fa-bars-staggered"></i>
        </button>

        <div class="d-none d-md-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1" style="font-size:0.78rem; font-weight:800;">
                <i class="fas fa-layer-group me-1"></i>سامانه سازمانی هماکار
            </span>
        </div>
    </div>

    <!-- Center: Desktop Install & Offline Sync Status in Header (انتقال وضعیت سینک و نصب دسکتاپ به هدر) -->
    <div class="d-flex align-items-center gap-2">
        <!-- ابزارک وضعیت شبکه و سینک آفلاین -->
        <div class="header-sync-pill" id="header-network-widget" title="وضعیت ارتباط با سرور و پایگاه داده">
            <i id="network-status-icon" class="fas fa-circle text-success" style="font-size:0.6rem;"></i>
            <span id="network-status-text" class="d-none d-sm-inline">آنلاین (سرور متصل)</span>
            <span id="offline-sync-count" class="badge bg-danger rounded-pill" style="display:none; font-size:0.65rem;">0</span>
            <button id="btn-manual-sync" type="button" class="btn btn-link p-0 text-primary" onclick="if(window.triggerOfflineSync) window.triggerOfflineSync();" title="همگام‌سازی داده‌های آفلاین" style="font-size:0.75rem; text-decoration:none;">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>

        <!-- دکمه نصب اپلیکیشن دسکتاپ ویندوز در هدر -->
        <button id="btn-install-desktop-app" type="button" class="btn-header-install" onclick="window.showDesktopInstallModal()" title="نصب اپلیکیشن مستقل دسکتاپ ویندوز هماکار">
            <i class="fab fa-windows"></i>
            <span class="d-none d-sm-inline">نصب دسکتاپ</span>
            <span class="d-inline d-sm-none">نصب</span>
        </button>
    </div>

    <!-- Left side: Notifications & User Profile -->
    <div class="d-flex align-items-center gap-2">
        <!-- Notifications Dropdown -->
        <div class="dropdown">
            <a class="nav-control-btn" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="اعلان‌ها">
                <i class="fas fa-bell"></i>
                <?php if (isset($unreadNotificationsCount) && $unreadNotificationsCount > 0): ?>
                    <span class="nav-badge-dot"></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-start dropdown-menu-nuxt shadow" aria-labelledby="alertsDropdown">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom mb-2">
                    <span class="fw-bold small text-dark"><i class="fas fa-bell text-primary me-1"></i>اعلان‌های سامانه</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:0.68rem;"><?php echo $unreadNotificationsCount; ?> جدید</span>
                </div>
                <?php foreach ($notifications as $notif): ?>
                    <a class="dropdown-item-nuxt text-wrap" href="#">
                        <i class="fas fa-circle-dot text-primary" style="font-size:0.55rem;"></i>
                        <div>
                            <div class="text-dark" style="font-size:0.82rem; line-height:1.35;"><?php echo htmlspecialchars($notif->title); ?></div>
                            <span class="text-muted" style="font-size:0.68rem;"><?php echo htmlspecialchars($notif->created_at); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
                <div class="border-top pt-2 mt-2 text-center">
                    <a href="#" class="small fw-bold text-primary text-decoration-none">مشاهده همه اعلان‌ها</a>
                </div>
            </div>
        </div>

        <!-- User Profile Pill Dropdown -->
        <div class="dropdown">
            <a class="user-pill-btn" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img class="user-avatar-img" src="https://i.pravatar.cc/80?u=<?php echo $user->id ?? 'admin'; ?>" alt="پروفایل">
                <span class="d-none d-md-inline fw-bold small"><?php echo sanitize($user->name ?? 'مدیر سیستم'); ?></span>
                <i class="fas fa-chevron-down text-muted" style="font-size:0.65rem;"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-start dropdown-menu-nuxt shadow" aria-labelledby="userDropdown">
                <div class="px-3 py-2 border-bottom mb-1">
                    <div class="fw-bold small text-dark"><?php echo sanitize($user->name ?? 'مدیر کل'); ?></div>
                    <span class="text-muted" style="font-size:0.72rem;"><?php echo sanitize($user->email ?? 'admin@hamakar.ir'); ?></span>
                </div>
                <a class="dropdown-item-nuxt" href="index.php?page=admin&action=dashboard">
                    <i class="fas fa-home text-muted"></i>
                    <span>پیشخوان مدیریت</span>
                </a>
                <a class="dropdown-item-nuxt" href="index.php?page=admin&action=settings">
                    <i class="fas fa-sliders text-muted"></i>
                    <span>تنظیمات سامانه</span>
                </a>
                <div class="dropdown-divider my-1"></div>
                <a class="dropdown-item-nuxt text-danger" href="index.php?page=logout">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                    <span>خروج از حساب</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- مودال فوق پرمیوم نصب نرم‌افزار دسکتاپ ویندوز هماکار -->
<div class="modal fade" id="hamakar-desktop-install-modal" tabindex="-1" aria-labelledby="desktopModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-gradient" style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%); color:#fff;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-white bg-opacity-10 p-2.5 text-info">
                        <i class="fab fa-windows fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0" id="desktopModalLabel">نصب نسخه دسکتاپ هماکار ERP</h5>
                        <span class="small text-light text-opacity-75">نسخه رسمی ویندوز ۱۱ / ۱۰ با کارکرد آفلاین مستقل</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 rounded-3 mb-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-2 mb-2 text-dark fw-bold" style="font-size:0.88rem;">
                        <i class="fas fa-check-circle text-success"></i> ویژگی‌های نسخه دسکتاپ هماکار:
                    </div>
                    <ul class="small text-muted mb-0 ps-3 lh-lg" style="font-size:0.8rem;">
                        <li>کارکرد کاملاً مستقل در نوار وظیفه (Taskbar) و منوی استارت ویندوز</li>
                        <li>پشتیبانی ۱۰۰٪ آفلاین با دیتابیس محلی IndexedDB و سینک خودکار</li>
                        <li>سرعت بارگذاری فوق‌العاده بالا بدون نیاز به باز نگه داشتن مرورگر</li>
                        <li>امکان صدور، چاپ فاکتور و ثبت عملیات در زمان قطع کامل اینترنت</li>
                    </ul>
                </div>

                <!-- دکمه نصب مستقیم مرورگر -->
                <div id="desktop-prompt-action" class="mb-3">
                    <button type="button" class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" onclick="window.executeNativeInstallPrompt()">
                        <i class="fab fa-windows fa-lg"></i>
                        <span>نصب مستقیم اپلیکیشن در ویندوز (۱ کلیک)</span>
                    </button>
                </div>

                <!-- راهنمای نصب سریع برای اج و کروم -->
                <div class="card border rounded-3 p-3 bg-light">
                    <div class="fw-bold small text-dark mb-2">
                        <i class="fas fa-info-circle text-primary me-1"></i> راهنمای نصب دستی در مرورگر مایکروسافت اج یا کروم:
                    </div>
                    <ol class="small text-muted ps-3 mb-0 lh-lg" style="font-size:0.78rem;">
                        <li>در نوار آدرس بالای همین صفحه، روی آیکون <strong>نصب (Install <i class="fas fa-arrow-down"></i>)</strong> کلیک کنید.</li>
                        <li>یا از منوی سه نقطه <i class="fas fa-ellipsis-v"></i> گزینه <strong>Apps</strong> و سپس <strong>Install Hamakar ERP</strong> را بزنید.</li>
                        <li>تیک گزینه‌های <em>«Pin to taskbar»</em> و <em>«Create Desktop shortcut»</em> را فعال نمایید.</li>
                    </ol>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-3 px-4 border" data-bs-dismiss="modal">متوجه شدم</button>
            </div>
        </div>
    </div>
</div>

<script>
window.showDesktopInstallModal = function() {
    var modalEl = document.getElementById('hamakar-desktop-install-modal');
    if (modalEl) {
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
};

window.executeNativeInstallPrompt = function() {
    if (window.installHamakarDesktopApp) {
        window.installHamakarDesktopApp();
    } else {
        alert('لطفاً از طریق منوی مرورگر خود گزینه Install Hamakar ERP را انتخاب نمایید.');
    }
};
</script>
