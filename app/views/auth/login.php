<?php
// app/views/auth/login.php - نسخه اصلاح‌شده قطعی و ۱۰۰٪ استاندارد
?>

<!-- لینک مستقیم به آیکون‌ها جهت تضمین بارگذاری -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">

<style>
    .login-container-box {
        font-family: 'Vazirmatn', sans-serif !important;
        direction: rtl !important;
        text-align: right !important;
        max-width: 440px;
        margin: 1.5rem auto;
        width: 100%;
        box-sizing: border-box;
    }

    .login-card-main {
        background: #ffffff;
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.12);
        padding: 2.5rem 2rem;
        box-sizing: border-box;
    }

    .login-header-icon {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin: 0 auto 1.25rem;
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
    }

    .custom-input-group {
        display: flex !important;
        align-items: center !important;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        overflow: hidden;
        background: #ffffff;
        transition: all 0.2s ease;
        margin-top: 0.35rem;
    }

    .custom-input-group:focus-within {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
    }

    .input-addon-icon {
        padding: 0 0.85rem;
        color: #64748b;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 46px;
        border-left: 1px solid #e2e8f0;
        font-size: 1rem;
    }

    .custom-form-input {
        flex: 1;
        border: none !important;
        outline: none !important;
        padding: 0.65rem 0.85rem;
        font-size: 0.9rem;
        color: #0f172a;
        background: transparent;
        height: 46px;
        box-sizing: border-box;
        direction: ltr !important;
        text-align: left !important;
    }

    .password-toggle-btn {
        border: none;
        background: #f8fafc;
        color: #64748b;
        padding: 0 0.85rem;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border-right: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }

    .password-toggle-btn:hover {
        color: #4f46e5;
        background: #f1f5f9;
    }

    .btn-login-submit {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 12px;
        padding: 0.85rem 1.5rem;
        font-weight: 700;
        font-size: 0.95rem;
        width: 100%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);
        transition: all 0.2s ease;
        margin-top: 1.25rem;
    }

    .btn-login-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(79, 70, 229, 0.35);
    }

    .form-label-custom {
        font-weight: 700;
        font-size: 0.825rem;
        color: #475569;
        display: block;
    }

    .checkbox-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        user-select: none;
    }

    .checkbox-container input {
        width: 16px;
        height: 16px;
        accent-color: #4f46e5;
        cursor: pointer;
    }
</style>

<div class="login-container-box">
    <div class="login-card-main">
        
        <!-- آیکون و عنوان فرم -->
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div class="login-header-icon">
                <i class="fas fa-user-lock"></i>
            </div>
            <h2 style="font-weight: 800; color: #0f172a; font-size: 1.25rem; margin-bottom: 0.35rem;">ورود به حساب کاربری</h2>
            <p style="font-size: 0.825rem; color: #64748b; margin: 0;">اطلاعات ورود خود را وارد نمایید</p>
        </div>

        <!-- نمایش پیغام‌های سیستم -->
        <?php if (class_exists('FlashMessage')) { FlashMessage::display(); } ?>

        <!-- فرم ورود -->
        <form action="<?php echo APP_URL; ?>/index.php?page=login" method="POST">
            
            <!-- فیلد ایمیل -->
            <div style="margin-bottom: 1.25rem;">
                <label for="email" class="form-label-custom">پست الکترونیک (ایمیل):</label>
                <div class="custom-input-group">
                    <span class="input-addon-icon">
                        <i class="far fa-envelope"></i>
                    </span>
                    <input type="email" class="custom-form-input font-monospace" id="email" name="email" placeholder="name@example.com" required autofocus>
                </div>
            </div>

            <!-- فیلد رمز عبور -->
            <div style="margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <label for="password" class="form-label-custom">رمز عبور:</label>
                    <a href="<?php echo APP_URL; ?>/forgot-password" style="color: #4f46e5; font-size: 0.775rem; font-weight: 700; text-decoration: none;">فراموشی رمز عبور؟</a>
                </div>
                <div class="custom-input-group">
                    <span class="input-addon-icon">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="custom-form-input font-monospace" id="password" name="password" placeholder="••••••••" required>
                    <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="نمایش/مخفی‌سازی رمز">
                        <i class="far fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>

            <!-- مرا به خاطر بسپار -->
            <div style="margin-bottom: 1.25rem;">
                <label class="checkbox-container">
                    <input type="checkbox" id="remember" name="remember">
                    <span style="font-size: 0.825rem; color: #475569;">مرا به خاطر بسپار</span>
                </label>
            </div>

            <button type="submit" class="btn-login-submit">
                <i class="fas fa-sign-in-alt"></i>
                <span>ورود به سامانه</span>
            </button>

            <div style="text-align: center; padding-top: 1.25rem; margin-top: 1.25rem; border-top: 1px solid #f1f5f9;">
                <span style="font-size: 0.825rem; color: #64748b;">حساب کاربری ندارید؟</span>
                <a href="<?php echo APP_URL; ?>/index.php#pricing" style="color: #4f46e5; font-size: 0.825rem; font-weight: 700; text-decoration: none; margin-right: 0.25rem;">خرید اشتراک و ثبت‌نام</a>
            </div>
        </form>
    </div>

    <!-- لینک بازگشت -->
    <div style="text-align: center; margin-top: 1.25rem;">
        <a href="<?php echo APP_URL; ?>" style="color: #64748b; font-size: 0.825rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
            <i class="fas fa-arrow-right"></i>
            <span>بازگشت به صفحه اصلی سایت</span>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');

    if (togglePasswordBtn && passwordInput) {
        togglePasswordBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            togglePasswordIcon.classList.toggle('fa-eye');
            togglePasswordIcon.classList.toggle('fa-eye-slash');
        });
    }
});
</script>