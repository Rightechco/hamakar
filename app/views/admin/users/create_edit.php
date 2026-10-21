<?php
// app/views/admin/users/create_edit.php - نسخه اولترا پرمیوم

$isEdit = isset($user) && $user !== null;
$formAction = $isEdit ? APP_URL . '/index.php?page=admin&action=users_update&id=' . $user->id : APP_URL . '/index.php?page=admin&action=users_store';
$buttonText = $isEdit ? 'ذخیره تغییرات' : 'افزودن کاربر جدید';
$pageTitle = $isEdit ? 'ویرایش کاربر: ' . htmlspecialchars($user->name) : 'افزودن کاربر جدید';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 18px;
        --radius-md: 10px;
        --shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    body, .user-form-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .premium-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        margin-bottom: 1.5rem;
    }

    .premium-card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
    }

    .form-control, .form-select {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        padding: 0.65rem 0.95rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .input-group-text.pdp-trigger {
        border-radius: 0 var(--radius-md) var(--radius-md) 0;
        border: 1px solid var(--border-color);
        background-color: #f8fafc;
        cursor: pointer;
    }

    .btn-gradient-primary {
        background: var(--primary-gradient);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        padding: 0.75rem 2rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: all 0.2s ease;
    }

    .btn-gradient-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        color: #fff;
    }

    .section-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #4f46e5;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .employee-box {
        background-color: #f8fafc;
        border: 1px solid #e0e7ff;
        border-radius: var(--radius-md);
        padding: 1.25rem;
        margin-bottom: 1rem;
    }
</style>

<div class="user-form-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><?php echo $pageTitle; ?></h3>
            <p class="text-muted small mb-0">مدیریت حساب کاربری، سطوح دسترسی، اطلاعات شغلی و پرسنلی</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=users" class="btn btn-outline-secondary btn-sm px-4 rounded-pill fw-medium">
            <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست کاربران
        </a>
    </div>

    <!-- Form Area -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="premium-card">
                <div class="premium-card-header d-flex align-items-center">
                    <i class="fas fa-user-gear text-primary fs-5 me-2"></i>
                    <h5 class="mb-0 fw-bold fs-6 text-dark"><?php echo $pageTitle; ?></h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo $formAction; ?>" method="POST">
                        
                        <!-- Section 1: Basic Info -->
                        <div class="section-title">
                            <i class="fas fa-id-card"></i>
                            <span>اطلاعات پایه کاربری</span>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-medium small text-dark">نام و نام خانوادگی <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $isEdit ? htmlspecialchars($user->name) : ''; ?>" placeholder="مثلا: علی محمدی..." required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-medium small text-dark">پست الکترونیکی (ایمیل) <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $isEdit ? htmlspecialchars($user->email) : ''; ?>" placeholder="name@example.com" required>
                            </div>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="mobile_number" class="form-label fw-medium small text-dark">شماره موبایل</label>
                                <input type="tel" class="form-control" id="mobile_number" name="mobile_number" placeholder="09123456789" value="<?php echo $isEdit && isset($user->mobile_number) ? htmlspecialchars($user->mobile_number) : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label fw-medium small text-dark">نقش و سطح دسترسی <span class="text-danger">*</span></label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="client" <?php echo ($isEdit && $user->role === 'client') ? 'selected' : ''; ?>>مشتری / کارفرما</option>
                                    <option value="employee" <?php echo ($isEdit && $user->role === 'employee') ? 'selected' : ''; ?>>کارمند</option>
                                    <option value="accountant" <?php echo ($isEdit && $user->role === 'accountant') ? 'selected' : ''; ?>>حسابدار</option>
                                    <option value="accountant_viewer" <?php echo ($isEdit && $user->role === 'accountant_viewer') ? 'selected' : ''; ?>>مشاهده‌گر حسابداری</option>
                                    <option value="admin" <?php echo ($isEdit && $user->role === 'admin') ? 'selected' : ''; ?>>مدیر کل سیستم</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="postal_address" class="form-label fw-medium small text-dark">آدرس پستی</label>
                            <textarea class="form-control" id="postal_address" name="postal_address" rows="2" placeholder="نشانی کامل پستی..."><?php echo $isEdit && isset($user->postal_address) ? htmlspecialchars($user->postal_address) : ''; ?></textarea>
                        </div>

                        <!-- Section 2: Security Password -->
                        <?php if (!$isEdit): ?>
                        <div class="section-title border-top pt-3">
                            <i class="fas fa-lock"></i>
                            <span>اطلاعات رمز عبور</span>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-medium small text-dark">رمز عبور <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-medium small text-dark">تکرار رمز عبور <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Section 3: Employee Specific Fields (Dynamic) -->
                        <div id="employee_fields" style="display: none;">
                            <div class="employee-box mb-4">
                                <div class="section-title text-primary mb-3">
                                    <i class="fas fa-briefcase"></i>
                                    <span>اطلاعات شغلی، پرسنلی و حقوقی (مخصوص کارمند)</span>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="organizational_position" class="form-label fw-medium small text-dark">پست سازمانی / سمت</label>
                                        <input type="text" class="form-control" id="organizational_position" name="organizational_position" placeholder="مثلا: مدیر فنی..." value="<?php echo $isEdit && isset($user->organizational_position) ? htmlspecialchars($user->organizational_position) : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="national_id_code" class="form-label fw-medium small text-dark">کد ملی</label>
                                        <input type="text" class="form-control" id="national_id_code" name="national_id_code" placeholder="10 رقم کد ملی..." value="<?php echo $isEdit && isset($user->national_id_code) ? htmlspecialchars($user->national_id_code) : ''; ?>">
                                    </div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="base_salary" class="form-label fw-medium small text-dark">حقوق پایه (تومان)</label>
                                        <input type="number" class="form-control fw-bold" name="base_salary" value="<?php echo $isEdit && isset($user->base_salary) ? htmlspecialchars($user->base_salary) : '0'; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="hire_date" class="form-label fw-medium small text-dark">تاریخ استخدام</label>
                                        <div class="input-group">
                                            <span class="input-group-text pdp-trigger"><i class="far fa-calendar-alt text-muted"></i></span>
                                            <input type="text" class="form-control pdp-input border-start-0" id="hire_date" name="hire_date" value="<?php echo $isEdit && !empty($user->hire_date) ? htmlspecialchars(jdate('Y/m/d', strtotime($user->hire_date))) : ''; ?>" placeholder="YYYY/MM/DD" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="marital_status" class="form-label fw-medium small text-dark">وضعیت تأهل</label>
                                        <select name="marital_status" class="form-select">
                                            <option value="single" <?php echo ($isEdit && isset($user->marital_status) && $user->marital_status == 'single') ? 'selected' : ''; ?>>مجرد</option>
                                            <option value="married" <?php echo ($isEdit && isset($user->marital_status) && $user->marital_status == 'married') ? 'selected' : ''; ?>>متأهل</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="children_count" class="form-label fw-medium small text-dark">تعداد فرزندان</label>
                                        <input type="number" class="form-control" id="children_count" name="children_count" value="<?php echo $isEdit && isset($user->children_count) ? htmlspecialchars($user->children_count) : '0'; ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Option for Edit -->
                        <?php if ($isEdit): ?>
                        <div class="mb-4 border-top pt-3">
                            <label for="status" class="form-label fw-medium small text-dark">وضعیت حساب کاربری</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active" <?php echo ($isEdit && $user->status === 'active') ? 'selected' : ''; ?>>فعال (مجاز به ورود)</option>
                                <option value="inactive" <?php echo ($isEdit && $user->status === 'inactive') ? 'selected' : ''; ?>>غیرفعال (مسدود شده)</option>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=users" class="btn btn-light px-4 rounded-2">انصراف</a>
                            <button type="submit" class="btn btn-gradient-primary px-4">
                                <i class="fas fa-check me-1.5"></i> <?php echo $buttonText; ?>
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
    const roleSelect = document.getElementById('role');
    const employeeFields = document.getElementById('employee_fields');

    function toggleEmployeeFields() {
        if (roleSelect.value === 'employee') {
            employeeFields.style.display = 'block';
        } else {
            employeeFields.style.display = 'none';
        }
    }
    
    toggleEmployeeFields();
    roleSelect.addEventListener('change', toggleEmployeeFields);
});

// Persian Datepicker Loader & Auto Click Handler
(function loadPersianDatePicker() {
    function initPicker() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.pDatepicker) {
            $('.pdp-input').each(function() {
                var $input = $(this);
                $input.pDatepicker({
                    format: 'YYYY/MM/DD',
                    autoClose: true,
                    initialValue: false,
                    observer: true,
                    initialValueType: 'persian'
                });
            });

            $('.pdp-trigger, .pdp-input').on('click', function(e) {
                e.stopPropagation();
                var $input = $(this).hasClass('pdp-input') ? $(this) : $(this).siblings('.pdp-input');
                $input.focus();
            });
        } else { setTimeout(initPicker, 100); }
    }

    if (!document.getElementById('p-datepicker-css')) {
        var css = document.createElement('link'); css.id = 'p-datepicker-css'; css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css';
        document.head.appendChild(css);
    }

    if (typeof $ === 'undefined') {
        var jq = document.createElement('script'); jq.src = 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js';
        jq.onload = loadPDate; document.head.appendChild(jq);
    } else { loadPDate(); }

    function loadPDate() {
        if (typeof window.persianDate === 'undefined') {
            var pd = document.createElement('script'); pd.src = 'https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js';
            pd.onload = loadPDatePicker; document.head.appendChild(pd);
        } else { loadPDatePicker(); }
    }

    function loadPDatePicker() {
        if (typeof $.fn.pDatepicker === 'undefined') {
            var pdp = document.createElement('script'); pdp.src = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js';
            pdp.onload = initPicker; document.head.appendChild(pdp);
        } else { initPicker(); }
    }
})();
</script>