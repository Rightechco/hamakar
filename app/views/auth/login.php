<?php
// app/views/auth/login.php
// This content will be injected into guest_layout.php
?>
<div class="card p-4 shadow-sm">
    <div class="card-body">
        <h2 class="card-title text-center mb-4"><i class="fas fa-lock"></i> ورود به سامانه</h2>
        <form action="<?php echo APP_URL; ?>/index.php?page=login" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">ایمیل:</label>
                <input type="email" class="form-control" id="email" name="email" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">رمز عبور:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">ورود</button>
            </div>
            <div class="mt-3 text-center">
                <a href="<?php echo APP_URL; ?>/forgot-password" class="text-secondary">رمز عبور خود را فراموش کرده‌اید؟</a>
            </div>
        </form>
    </div>
</div>
