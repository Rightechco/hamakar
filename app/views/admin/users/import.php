<?php
// app/views/admin/users/import.php - نسخه اولترا پرمیوم
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 18px;
        --radius-md: 12px;
        --shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    body, .import-users-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .premium-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
    }

    .premium-card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
    }

    /* Drag & Drop File Upload Box */
    .file-upload-box {
        border: 2px dashed #cbd5e1;
        border-radius: var(--radius-lg);
        padding: 2.5rem 1.5rem;
        text-align: center;
        background-color: #f8fafc;
        transition: all 0.2s ease;
        cursor: pointer;
        position: relative;
    }

    .file-upload-box:hover {
        border-color: #0891b2;
        background-color: #ecfeff;
    }

    .file-upload-box input[type="file"] {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .btn-gradient-info {
        background: var(--primary-gradient);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        padding: 0.75rem 2rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(8, 145, 178, 0.25);
        transition: all 0.2s ease;
    }

    .btn-gradient-info:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(8, 145, 178, 0.35);
        color: #fff;
    }
</style>

<div class="import-users-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">ورود گروهی کاربران از فایل اکسل</h3>
            <p class="text-muted small mb-0">بارگذاری فایل .xlsx و ثبت اتوماتیک مشخصات کاربران در بانک اطلاعاتی سیستم</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=users" class="btn btn-outline-secondary btn-sm px-4 rounded-pill fw-medium">
            <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست کاربران
        </a>
    </div>

    <!-- Instructions Banner -->
    <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4 p-3 d-flex align-items-center">
        <i class="fas fa-file-excel fa-2x text-info me-3"></i>
        <div class="small">
            <strong>راهنمای فرمت فایل اکسل:</strong> فایل انتخابی باید حتماً دارای پسوند <strong>.xlsx</strong> بوده و سطر اول آن شامل ستون‌های <strong>"نام"</strong>، <strong>"ایمیل"</strong>، <strong>"کلمه عبور"</strong> و <strong>"نقش"</strong> باشد.
        </div>
    </div>

    <!-- Upload Card -->
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="premium-card">
                <div class="premium-card-header d-flex align-items-center">
                    <i class="fas fa-file-import text-info fs-5 me-2"></i>
                    <h5 class="mb-0 fw-bold fs-6 text-dark">بارگذاری فایل اکسل (.xlsx)</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=import_users" method="POST" enctype="multipart/form-data">
                        
                        <div class="file-upload-box mb-4">
                            <i class="fas fa-cloud-upload-alt fa-3x text-info mb-3"></i>
                            <h6 class="fw-bold text-dark mb-1">فایل اکسل خود را اینجا رها کنید یا کلیک نمایید</h6>
                            <span class="text-muted small d-block mb-2" id="selected-file-name">فرمت‌های مجاز: .xlsx</span>
                            <input type="file" id="excel_file" name="excel_file" accept=".xlsx" required>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=users" class="btn btn-light px-4 rounded-2">انصراف</a>
                            <button type="submit" class="btn btn-gradient-info px-4">
                                <i class="fas fa-file-import me-1.5"></i> شروع پردازش و ورود اطلاعات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('excel_file');
    const fileNameDisplay = document.getElementById('selected-file-name');

    fileInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            fileNameDisplay.textContent = 'فایل انتخاب شده: ' + this.files[0].name;
            fileNameDisplay.classList.add('text-info', 'fw-bold');
        } else {
            fileNameDisplay.textContent = 'فرمت‌های مجاز: .xlsx';
            fileNameDisplay.classList.remove('text-info', 'fw-bold');
        }
    });
});
</script>