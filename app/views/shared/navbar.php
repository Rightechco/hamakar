<?php
// app/views/shared/navbar.php - نسخه استاندارد بدون تداخل
global $auth;
$user = $auth->user();
?>

<style>
    .top-header-bar {
        height: 65px;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 0 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 1020;
    }
</style>

<header class="top-header-bar">
    <div class="d-flex align-items-center gap-3">
        <button type="button" id="sidebarToggleTop" class="btn btn-light rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="fas fa-bars"></i>
        </button>
        <span class="fw-bold fs-6 text-dark d-none d-sm-inline">سامانه مدیریت رایان تکرو</span>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="dropdown">
            <a class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                <img class="rounded-circle border" src="https://i.pravatar.cc/80?u=<?php echo $user->id ?? 1; ?>" width="36" height="36" alt="Profile">
                <span class="fw-bold text-dark small d-none d-md-inline"><?php echo sanitize($user->name ?? 'کاربر'); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2" aria-labelledby="userDropdown">
                <li><a class="dropdown-item small" href="#"><i class="fas fa-user text-muted me-2"></i>پروفایل</a></li>
                <li><a class="dropdown-item small" href="#"><i class="fas fa-cog text-muted me-2"></i>تنظیمات</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item small text-danger" href="index.php?page=logout"><i class="fas fa-sign-out-alt me-2"></i>خروج</a></li>
            </ul>
        </div>
    </div>
</header>