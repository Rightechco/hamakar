<?php
// app/views/shared/edit_user_modal.php
// این فایل شامل کد HTML یک مودال Bootstrap برای ویرایش اطلاعات کاربر است.
// این مودال به صورت پویا توسط JavaScript و AJAX پر می شود و ارسال می گردد.
?>
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">ویرایش کاربر</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <form id="editUserForm" action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" value="">
                    
                    <div class="mb-3">
                        <label for="edit_user_name" class="form-label">نام:</label>
                        <input type="text" class="form-control" id="edit_user_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_user_email" class="form-label">ایمیل:</label>
                        <input type="email" class="form-control" id="edit_user_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_user_role" class="form-label">نقش:</label>
                        <select class="form-select" id="edit_user_role" name="role" required>
                            <option value="admin">مدیر</option>
                            <option value="employee">کارمند</option>
                            <option value="client">مشتری</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_user_status" class="form-label">وضعیت:</label>
                        <select class="form-select" id="edit_user_status" name="status" required>
                            <option value="active">فعال</option>
                            <option value="inactive">غیرفعال</option>
                        </select>
                    </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                </div>
            </form>
        </div>
    </div>
</div>