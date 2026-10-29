<?php
// app/views/auth/login.php - صفحه ورود فوق پرمیوم هماکار ERP
?>
<style>
    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(24px) saturate(190%);
        -webkit-backdrop-filter: blur(24px) saturate(190%);
        border: 1.5px solid rgba(226, 232, 240, 0.95);
        border-radius: 28px;
        box-shadow: 0 25px 65px -15px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(255, 255, 255, 0.6) inset;
        padding: 2.5rem 2.25rem;
        position: relative;
        overflow: hidden;
    }

    .login-card-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .brand-logo-wrap {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.25rem;
        padding: 0.5rem 1rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }

    .brand-logo-wrap img {
        height: 48px;
        width: auto;
        object-fit: contain;
    }

    .login-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #eef2ff;
        color: #4f46e5;
        border: 1px solid #c7d2fe;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        font-size: 0.78rem;
        font-weight: 700;
        margin-bottom: 0.85rem;
    }

    .login-title {
        font-size: 1.5rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 0.35rem;
        letter-spacing: -0.4px;
    }

    .login-subtitle {
        font-size: 0.82rem;
        color: #64748b;
        margin: 0;
        line-height: 1.5;
    }

    .form-group-custom {
        margin-bottom: 1.35rem;
    }

    .form-label-custom {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.84rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.5rem;
    }

    .form-label-custom i {
        color: #4f46e5;
    }

    .input-wrap-custom {
        position: relative;
    }

    .form-control-custom {
        width: 100%;
        padding: 0.8rem 1.15rem;
        background-color: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 14px;
        font-size: 0.92rem;
        color: #0f172a;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        outline: none;
    }

    .form-control-custom:focus {
        background-color: #ffffff;
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
    }

    .btn-toggle-password {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        font-size: 0.95rem;
        transition: color 0.2s;
    }

    .btn-toggle-password:hover {
        color: #4f46e5;
    }

    .login-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        font-size: 0.82rem;
    }

    .remember-custom {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        color: #475569;
        font-weight: 600;
        cursor: pointer;
    }

    .remember-custom input {
        accent-color: #4f46e5;
        width: 16px;
        height: 16px;
    }

    .forgot-link {
        color: #4f46e5;
        text-decoration: none;
        font-weight: 700;
        transition: color 0.2s;
    }

    .forgot-link:hover {
        color: #4338ca;
        text-decoration: underline;
    }

    .btn-login-submit {
        width: 100%;
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff;
        border: none;
        border-radius: 14px;
        padding: 0.85rem 1.5rem;
        font-size: 0.95rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        cursor: pointer;
        box-shadow: 0 8px 22px rgba(79, 70, 229, 0.32);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-login-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(79, 70, 229, 0.42);
        color: #ffffff;
    }

    .btn-login-submit:active {
        transform: translateY(0) scale(0.98);
    }

    .divider-custom {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 1.5rem 0;
        color: #94a3b8;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .divider-custom::before, .divider-custom::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e2e8f0;
    }

    .divider-custom::before { margin-left: 0.85rem; }
    .divider-custom::after { margin-right: 0.85rem; }

    /* 1-Click Instant Demo Button */
    .btn-demo-instant {
        width: 100%;
        background: #ffffff;
        border: 2px dashed #818cf8;
        border-radius: 16px;
        padding: 0.85rem 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.65rem;
        text-decoration: none;
        color: #4338ca;
        font-weight: 800;
        font-size: 0.88rem;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.08);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }

    .btn-demo-instant:hover {
        background: #eef2ff;
        border-color: #4f46e5;
        border-style: solid;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.18);
        color: #3730a3;
    }

    .btn-demo-instant i {
        font-size: 1.1rem;
        color: #4f46e5;
    }

    .demo-hint-box {
        margin-top: 0.85rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.6rem 0.85rem;
        text-align: center;
        font-size: 0.74rem;
        color: #64748b;
    }

    .trust-badges-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.25rem;
        margin-top: 1.75rem;
        padding-top: 1.25rem;
        border-top: 1px solid #f1f5f9;
        font-size: 0.72rem;
        color: #94a3b8;
        font-weight: 700;
    }

    .trust-badge-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .trust-badge-item i {
        color: #10b981;
    }

    .back-home-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        margin-top: 1.5rem;
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
        text-decoration: none;
        transition: color 0.2s;
    }

    .back-home-link:hover {
        color: #4f46e5;
        text-decoration: underline;
    }
</style>

<div class="login-card">
    
    <!-- Header with Branding -->
    <div class="login-card-header">
        <div class="brand-logo-wrap">
            <img src="https://hamakar.ir/images/webLogoOfHamakar.png" alt="لوگوی هماکار">
        </div>
        <br>
        <span class="login-badge">
            <i class="fas fa-shield-check"></i>
            سامانه سازمانی هماکار ERP
        </span>
        <h1 class="login-title">ورود به پرتال سازمانی</h1>
        <p class="login-subtitle">مدیریت یکپارچه مالی، کارپوشه مودیان و اتوماسیون سازمانی</p>
    </div>

    <!-- Login Form -->
    <form action="<?php echo APP_URL; ?>/index.php?page=login" method="POST" autocomplete="on">
        
        <div class="form-group-custom">
            <label class="form-label-custom" for="login-email">
                <i class="fas fa-user-circle"></i>
                ایمیل سازمانی یا نام کاربری
            </label>
            <div class="input-wrap-custom">
                <input type="text" id="login-email" name="email" class="form-control-custom" placeholder="مثال: admin@company.ir" required autofocus autocomplete="username">
            </div>
        </div>

        <div class="form-group-custom">
            <label class="form-label-custom" for="login-password">
                <i class="fas fa-key"></i>
                کلمه عبور
            </label>
            <div class="input-wrap-custom">
                <input type="password" id="login-password" name="password" class="form-control-custom" placeholder="رمز عبور خود را وارد کنید" required autocomplete="current-password">
                <button type="button" class="btn-toggle-password" onclick="togglePassVisibility()" title="نمایش/مخفی‌سازی رمز">
                    <i class="fas fa-eye" id="pass-eye-icon"></i>
                </button>
            </div>
        </div>

        <div class="login-meta-row">
            <label class="remember-custom">
                <input type="checkbox" name="remember" value="1" checked>
                <span>مرا به خاطر بسپار</span>
            </label>
            <a href="tel:04134401179" class="forgot-link" onclick="alert('جهت بازیابی رمز عبور سازمانی، لطفاً با مدیر سیستم سازمان خود یا پشتیبانی هماکار (۰۴۱-۳۴۴۰۱۱۷۹) تماس حاصل فرمایید.'); return false;">
                فراموشی رمز؟
            </a>
        </div>

        <button type="submit" class="btn-login-submit">
            <span>ورود به پرتال هماکار</span>
            <i class="fas fa-arrow-left"></i>
        </button>
    </form>

    <div class="divider-custom">
        <span>یا دسترسی فوری به محیط دمو</span>
    </div>

    <!-- 1-Click Instant Demo Button -->
    <a href="<?php echo APP_URL; ?>/index.php?demo_login=1" class="btn-demo-instant">
        <i class="fas fa-rocket"></i>
        <span>ورود به عنوان مدیر سامانه (۱ کلیک)</span>
        <i class="fas fa-chevron-left" style="font-size:0.75rem;"></i>
    </a>

    <div class="demo-hint-box">
        <i class="fas fa-info-circle text-primary me-1"></i>
        جهت بررسی امکانات، بدون نیاز به وارد کردن کلمه عبور وارد شوید.
    </div>

    <!-- Trust Badges -->
    <div class="trust-badges-row">
        <div class="trust-badge-item">
            <i class="fas fa-lock"></i>
            <span>امنیت SSL ۲۵۶ بیتی</span>
        </div>
        <div class="trust-badge-item">
            <i class="fas fa-shield-alt"></i>
            <span>پایداری ۹۹.۹٪</span>
        </div>
        <div class="trust-badge-item">
            <i class="fas fa-server"></i>
            <span>پشتیبان‌گیری ابری</span>
        </div>
    </div>

</div>

<!-- Back to Landing Page Link -->
<a href="https://hamakar.ir" class="back-home-link">
    <i class="fas fa-arrow-right"></i>
    <span>بازگشت به صفحه اصلی سایت هماکار (hamakar.ir)</span>
</a>

<script>
function togglePassVisibility() {
    const input = document.getElementById('login-password');
    const icon = document.getElementById('pass-eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
