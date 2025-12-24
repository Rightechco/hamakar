<?php
// app/views/admin/users/import.php
?>
<h1 class="mb-4">ورود کاربران از فایل اکسل</h1>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">فایل اکسل را بارگذاری کنید</h5>
    </div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=import_users" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="excel_file" class="form-label">انتخاب فایل اکسل:</label>
                <input type="file" class="form-control" id="excel_file" name="excel_file" required>
                <small class="form-text text-muted">فایل باید با فرمت .xlsx باشد و شامل ستون‌های "نام", "ایمیل", "کلمه عبور" و "نقش" باشد.</small>
            </div>
            <button type="submit" class="btn btn-primary">شروع ورود اطلاعات</button>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=users" class="btn btn-secondary">بازگشت</a>
        </form>
    </div>
</div>