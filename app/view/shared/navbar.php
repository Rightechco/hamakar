<?php
// app/views/shared/navbar.php - نسخه نهایی و مدرن
global $auth;
$user = $auth->user();

// فرض می‌کنیم این اطلاعات از کنترلر به ویو ارسال می‌شود
$unreadNotificationsCount = 5; // تعداد نوتیفیکیشن‌های خوانده نشده
$notifications = [ // یک آرایه نمونه از نوتیفیکیشن‌ها
    (object)['created_at' => '2025-08-17 12:00:00', 'title' => 'فاکتور جدید صادر شد.'],
    (object)['created_at' => '2025-08-16 15:30:00', 'title' => 'یک تیکت پشتیبانی جدید دریافت شد.'],
];
?>
<style>
    /* استایل‌های ناوبار مدرن */
    .top-navbar {
        background: #ffffff;
        padding: 0.75rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid #e0e0e0;
    }
    .top-navbar .navbar-nav {
        flex-direction: row;
        align-items: center;
    }
    .top-navbar .nav-link {
        color: #495057;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease-in-out;
        position: relative; /* برای قرار دادن badge */
    }
    .top-navbar .nav-link:hover {
        background-color: #f8f9fa;
        color: #212529;
    }
    .navbar-nav .dropdown-menu {
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border: none;
        margin-top: 10px;
    }
    .navbar-nav .dropdown-item {
        color: #495057;
        font-weight: 500;
        transition: background-color 0.2s;
        padding: 10px 20px;
    }
    .navbar-nav .dropdown-item:hover {
        background-color: #f1f3f5;
        color: #212529;
    }
    .img-profile {
        object-fit: cover;
        border: 2px solid #e9ecef;
    }
    .badge-counter {
        position: absolute;
        top: 5px;
        left: 5px; /* انتقال به چپ */
        font-size: 0.6rem;
        padding: 0.4rem 0.5rem;
        border-radius: 50%;
        line-height: 0.7;
    }
    .topbar-divider {
        width: 0;
        border-right: 1px solid #e0e0e0;
        height: 2.5rem;
        margin: 0 1rem;
    }
</style>

<nav class="top-navbar">
    <!-- دکمه همبرگری برای نمایش/پنهان کردن سایدبار -->
    <button class="btn btn-link" id="sidebarToggleTop"><i class="fa fa-bars text-secondary"></i></button>

    <!-- محتوای سمت راست (آیکون‌ها و پروفایل) -->
    <ul class="navbar-nav ms-auto">
        <!-- Dropdown نوتیفیکیشن‌ها -->
        <li class="nav-item dropdown no-arrow mx-1">
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- نمایش تعداد نوتیفیکیشن‌های جدید -->
                <?php if (isset($unreadNotificationsCount) && $unreadNotificationsCount > 0): ?>
                    <span class="badge bg-danger badge-counter"><?php echo $unreadNotificationsCount; ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-start shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">نوتیفیکیشن‌ها</h6>
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $notif): ?>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <div>
                                <div class="small text-gray-500"><?php echo htmlspecialchars($notif->created_at); ?></div>
                                <span class="font-weight-bold"><?php echo htmlspecialchars($notif->title); ?></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <a class="dropdown-item text-center text-muted">نوتیفیکیشن جدیدی ندارید.</a>
                <?php endif; ?>
                <a class="dropdown-item text-center small text-gray-500" href="#">نمایش همه نوتیفیکیشن‌ها</a>
            </div>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- اطلاعات کاربر و منوی پروفایل -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="img-profile rounded-circle me-2" src="https://i.pravatar.cc/40?u=<?php echo $user->id; ?>" width="32" height="32" alt="پروفایل">
                <span class="d-none d-lg-inline text-gray-600 small"><?php echo sanitize($user->name); ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-start shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                    <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                    پروفایل
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>
                    تنظیمات
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="index.php?page=logout">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                    خروج
                </a>
            </div>
        </li>
    </ul>
</nav>
