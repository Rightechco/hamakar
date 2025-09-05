<?php
// app/views/admin/customers/create_edit.php

$isEdit = isset($customer) && $customer !== null;
$formAction = $isEdit ? APP_URL . '/admin/customers/update/' . $customer->id : APP_URL . '/admin/customers/store';
$buttonText = $isEdit ? 'ذخیره تغییرات' : 'افزودن مشتری';
$pageTitle = $isEdit ? 'ویرایش مشتری' : 'افزودن مشتری جدید';
?>

<h1 class="mb-4"><?php echo $pageTitle; ?></h1>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><?php echo $pageTitle; ?></h5>
    </div>
    <div class="card-body">
        <form action="<?php echo $formAction; ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">نام مشتری:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $isEdit ? sanitize($customer->name) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">ایمیل:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $isEdit ? sanitize($customer->email) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">شماره تلفن:</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $isEdit ? sanitize($customer->phone) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">آدرس:</label>
                <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $isEdit ? sanitize($customer->address) : ''; ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary"><?php echo $buttonText; ?></button>
            <a href="<?php echo APP_URL; ?>/admin/customers" class="btn btn-secondary">بازگشت</a>
        </form>
    </div>
</div>