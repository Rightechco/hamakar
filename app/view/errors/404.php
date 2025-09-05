<?php
// app/views/errors/404.php
?>
<div class="container text-center" style="padding: 80px 15px;">
    <div class="row">
        <div class="col-md-12">
            <h1 style="font-size: 120px; font-weight: bold; color: #6c757d;">404</h1>
            <h2 class="mb-4">صفحه مورد نظر یافت نشد</h2>
            <p class="text-muted mb-4">
                متاسفانه صفحه‌ای که به دنبال آن بودید در این آدرس وجود ندارد. ممکن است آدرس را اشتباه وارد کرده یا صفحه حذف شده باشد.
            </p>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=dashboard" class="btn btn-primary btn-lg">بازگشت به داشبورد</a>
        </div>
    </div>
</div>