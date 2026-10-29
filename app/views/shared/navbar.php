<?php
// app/views/shared/navbar.php - هدر مدرن و نیتیو سبک Nuxt 3 هماکار
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
        background: rgba(255, 255, 255, 0.94) !important;
        backdrop-filter: blur(20px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(20px) saturate(180%) !important;
        padding: 0.65rem 1.4rem !important;
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
</style>

<nav class="top-navbar">
    <!-- Right side: Sidebar Toggler & Brand Title -->
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn-toggle-sidebar" id="sidebarToggleTop" title="منوی سایدبار">
            <i class="fa fa-bars-staggered"></i>
        </button>

        <div class="d-none d-sm-flex align-items-center gap-2">
            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1" style="font-size:0.78rem; font-weight:800;">
                <i class="fas fa-layer-group me-1"></i>سامانه سازمانی هماکار
            </span>
        </div>
    </div>

    <!-- Center: Quick Mobile Title -->
    <div class="d-flex d-sm-none align-items-center">
        <span class="fw-bold" style="font-size:0.95rem; color:#0f172a; letter-spacing:-0.3px;">
            هماکار <span class="text-primary">ERP</span>
        </span>
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
