<?php
// app/views/admin/users/create_edit.php - نسخه کامل و نهایی

$isEdit = isset($user) && $user !== null;
$formAction = $isEdit ? APP_URL . '/index.php?page=admin&action=users_update&id=' . $user->id : APP_URL . '/index.php?page=admin&action=users_store';
$buttonText = $isEdit ? 'ذخیره تغییرات' : 'افزودن کاربر';
$pageTitle = $isEdit ? 'ویرایش کاربر: ' . htmlspecialchars($user->name) : 'افزودن کاربر جدید';
?>

<h1 class="mb-4"><?php echo $pageTitle; ?></h1>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><?php echo $pageTitle; ?></h5>
    </div>
    <div class="card-body">
        <form action="<?php echo $formAction; ?>" method="POST">
            
            <h5 class="mb-3">اطلاعات پایه</h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">نام و نام خانوادگی <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $isEdit ? htmlspecialchars($user->name) : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">ایمیل <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $isEdit ? htmlspecialchars($user->email) : ''; ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="mobile_number" class="form-label">شماره موبایل</label>
                    <input type="tel" class="form-control" id="mobile_number" name="mobile_number" placeholder="مثلا: 09123456789" value="<?php echo $isEdit && isset($user->mobile_number) ? htmlspecialchars($user->mobile_number) : ''; ?>">
                </div>
            </div>
            <div class="mb-3">
                <label for="postal_address" class="form-label">آدرس پستی</label>
                <textarea class="form-control" id="postal_address" name="postal_address" rows="2"><?php echo $isEdit && isset($user->postal_address) ? htmlspecialchars($user->postal_address) : ''; ?></textarea>
            </div>
            <hr>

            <h5 class="mb-3">اطلاعات ورود و دسترسی</h5>
            <?php if (!$isEdit): ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">رمز عبور <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">تکرار رمز عبور <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                </div>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="role" class="form-label">نقش <span class="text-danger">*</span></label>
                <select class="form-select" id="role" name="role" required>
                    <option value="client" <?php echo ($isEdit && $user->role === 'client') ? 'selected' : ''; ?>>مشتری</option>
                    <option value="employee" <?php echo ($isEdit && $user->role === 'employee') ? 'selected' : ''; ?>>کارمند</option>
                    <option value="accountant" <?php echo ($isEdit && $user->role === 'accountant') ? 'selected' : ''; ?>>حسابدار</option>
                    <option value="accountant_viewer" <?php echo ($isEdit && $user->role === 'accountant_viewer') ? 'selected' : ''; ?>>مشاهده‌گر حسابداری</option>
                    <option value="admin" <?php echo ($isEdit && $user->role === 'admin') ? 'selected' : ''; ?>>مدیر کل</option>
                </select>
            </div>

            <div id="employee_fields" style="display: none;">
                <div class="card bg-light p-3 border">
                    <h6 class="mb-3 text-primary">اطلاعات شغلی و حقوقی (مخصوص کارمند)</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="organizational_position" class="form-label">پست سازمانی</label>
                            <input type="text" class="form-control" id="organizational_position" name="organizational_position" value="<?php echo $isEdit && isset($user->organizational_position) ? htmlspecialchars($user->organizational_position) : ''; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="national_id_code" class="form-label">کد ملی</label>
                            <input type="text" class="form-control" id="national_id_code" name="national_id_code" value="<?php echo $isEdit && isset($user->national_id_code) ? htmlspecialchars($user->national_id_code) : ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="base_salary" class="form-label">حقوق پایه (تومان)</label>
                            <input type="number" class="form-control" name="base_salary" value="<?php echo $isEdit && isset($user->base_salary) ? htmlspecialchars($user->base_salary) : '0'; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hire_date" class="form-label">تاریخ استخدام</label>
                            <input type="text" class="form-control persian-datepicker" id="hire_date" name="hire_date" value="<?php echo $isEdit && !empty($user->hire_date) ? htmlspecialchars(jdate('Y/m/d', strtotime($user->hire_date))) : ''; ?>">
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="marital_status" class="form-label">وضعیت تاهل</label>
                            <select name="marital_status" class="form-select">
                                <option value="single" <?php echo ($isEdit && isset($user->marital_status) && $user->marital_status == 'single') ? 'selected' : ''; ?>>مجرد</option>
                                <option value="married" <?php echo ($isEdit && isset($user->marital_status) && $user->marital_status == 'married') ? 'selected' : ''; ?>>متاهل</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="children_count" class="form-label">تعداد فرزندان</label>
                            <input type="number" class="form-control" id="children_count" name="children_count" value="<?php echo $isEdit && isset($user->children_count) ? htmlspecialchars($user->children_count) : '0'; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($isEdit): ?>
            <div class="mb-3 mt-3">
                <label for="status" class="form-label">وضعیت کاربر</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="active" <?php echo ($isEdit && $user->status === 'active') ? 'selected' : ''; ?>>فعال</option>
                    <option value="inactive" <?php echo ($isEdit && $user->status === 'inactive') ? 'selected' : ''; ?>>غیرفعال</option>
                </select>
            </div>
            <?php endif; ?>

            <hr>
            <button type="submit" class="btn btn-primary"><?php echo $buttonText; ?></button>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=users" class="btn btn-secondary">بازگشت</a>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const employeeFields = document.getElementById('employee_fields');

        function toggleEmployeeFields() {
            // فیلدهای تکمیلی فقط برای نقش "کارمند" نمایش داده می‌شود
            if (roleSelect.value === 'employee') {
                employeeFields.style.display = 'block';
            } else {
                employeeFields.style.display = 'none';
            }
        }
        
        // اجرای اولیه تابع در زمان بارگذاری صفحه (برای حالت ویرایش)
        toggleEmployeeFields();

        // افزودن رویداد برای تغییر مقدار دراپ‌داون
        roleSelect.addEventListener('change', toggleEmployeeFields);
    });
</script>