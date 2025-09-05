<?php
// app/views/admin/contracts/create_edit.php
// نسخه کامل و نهایی با قابلیت انتخاب قرارداد و محاسبه ارزش افزوده
global $categories; // ✅ اضافه شدن
$isEdit = isset($contract) && $contract !== null;
$formAction = $isEdit ? APP_URL . '/index.php?page=admin&action=contracts_update&id=' . $contract->id : APP_URL . '/index.php?page=admin&action=contracts_store';
$buttonText = $isEdit ? 'ذخیره تغییرات' : 'افزودن قرارداد';
$pageTitle = $isEdit ? 'ویرایش قرارداد' : 'افزودن قرارداد جدید';


$serviceTypes = [
    'wordpress_website_design' => 'طراحی سایت وردپرسی',
    'dedicated_website_design' => 'طراحی سایت اختصاصی',
    'branding_marketing' => 'برندینگ و بازاریابی',
    'social_media_management' => 'مدیریت شبکه های اجتماعی',
    'seo_services' => 'خدمات سئو',
    'content_production' => 'خدمات تولید محتوا',
    'server_renewal' => 'تمدید سرور', 
    'support_renewal' => 'تمدید پشتیبانی', 
];

?>

<h1 class="mb-4"><?php echo $pageTitle; ?></h1>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><?php echo $pageTitle; ?></h5>
    </div>
    <div class="card-body">
        <form action="<?php echo $formAction; ?>" method="POST">
            <div class="mb-3">
                <label for="client_id" class="form-label">کارفرما:</label>
                <select class="form-select" id="client_id" name="client_id" required>
                    <option value="">انتخاب کارفرما</option>
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?php echo htmlspecialchars($client->id); ?>" 
                                <?php echo ($isEdit && $contract->client_id == $client->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($client->name); ?> (<?php echo htmlspecialchars($client->email); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">عنوان قرارداد:</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo $isEdit ? htmlspecialchars($contract->title) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="service_type" class="form-label">نوع خدمات:</label>
                <select class="form-select" id="service_type" name="service_type" required>
                    <option value="">انتخاب نوع خدمات</option>
                    <?php foreach ($serviceTypes as $key => $value): ?>
                        <option value="<?php echo htmlspecialchars($key); ?>" 
                                <?php echo ($isEdit && $contract->service_type == $key) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($value); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
             <div class="mb-3">
                <label for="category_id" class="form-label">دسته‌بندی:</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">-- بدون دسته‌بندی --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category->id); ?>" 
                                <?php echo ($isEdit && $contract->category_id == $category->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="total_amount" class="form-label">مبلغ کل قرارداد (تومان):</label>
                        <input type="number" class="form-control" id="total_amount" name="total_amount" value="<?php echo $isEdit ? htmlspecialchars($contract->total_amount) : ''; ?>" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="prepayment_amount" class="form-label">مبلغ پیش‌پرداخت (تومان):</label>
                        <input type="number" class="form-control" id="prepayment_amount" name="prepayment_amount" value="<?php echo $isEdit && isset($contract->prepayment_amount) ? htmlspecialchars($contract->prepayment_amount) : '0'; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                     <div class="mb-3">
                        <label for="final_payment_due_date" class="form-label">تاریخ سررسید تسویه نهایی:</label>
                        <input type="text" class="form-control persian-datepicker" id="final_payment_due_date" name="final_payment_due_date" value="<?php echo $isEdit && isset($contract->final_payment_due_date) ? htmlspecialchars(jdate('Y/m/d', strtotime($contract->final_payment_due_date))) : ''; ?>">
                    </div>
                </div>
                 </div>
           
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">تاریخ شروع (شمسی):</label>
                        <input type="text" class="form-control persian-datepicker" id="start_date" name="start_date" 
                               value="<?php echo isset($start_date_jalali) ? htmlspecialchars($start_date_jalali) : ''; ?>" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">تاریخ پایان (شمسی - اختیاری):</label>
                        <input type="text" class="form-control persian-datepicker" id="end_date" name="end_date"
                               value="<?php echo isset($end_date_jalali) ? htmlspecialchars($end_date_jalali) : ''; ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="renewal_type" class="form-label">نوع تمدید:</label>
                        <select class="form-select" id="renewal_type" name="renewal_type" required>
                            <option value="none" <?php echo ($isEdit && $contract->renewal_type == 'none') ? 'selected' : ''; ?>>تمدید ندارد</option>
                            <option value="monthly" <?php echo ($isEdit && $contract->renewal_type == 'monthly') ? 'selected' : ''; ?>>ماهانه</option>
                            <option value="yearly" <?php echo ($isEdit && $contract->renewal_type == 'yearly') ? 'selected' : ''; ?>>سالانه</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">توضیحات/بندهای قرارداد:</label>
                <textarea class="form-control" id="description" name="description" rows="5"><?php echo $isEdit ? htmlspecialchars($contract->description) : ''; ?></textarea>
                <small class="form-text text-muted">جزئیات کامل یا بندهای سفارشی قرارداد را اینجا وارد کنید.</small>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">وضعیت قرارداد:</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="pending" <?php echo ($isEdit && $contract->status == 'pending') ? 'selected' : ''; ?>>در انتظار</option>
                    <option value="active" <?php echo ($isEdit && $contract->status == 'active') ? 'selected' : ''; ?>>فعال</option>
                    <option value="completed" <?php echo ($isEdit && $contract->status == 'completed') ? 'selected' : ''; ?>>تکمیل شده</option>
                    <option value="canceled" <?php echo ($isEdit && $contract->status == 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary"><?php echo $buttonText; ?></button>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=contracts" class="btn btn-secondary">بازگشت</a>
        </form>
    </div>
</div>