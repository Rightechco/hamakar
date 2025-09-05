<?php
// app/views/admin/clients/create_edit.php

$isEdit = isset($client) && $client !== null;
$formAction = $isEdit ? APP_URL . '/index.php?page=admin&action=clients_update&id=' . $client->id : APP_URL . '/index.php?page=admin&action=clients_store';
$buttonText = $isEdit ? 'ذخیره تغییرات' : 'افزودن مشتری';
$pageTitle = $isEdit ? 'ویرایش مشتری' : 'افزودن مشتری جدید';

$users = $users ?? [];
$contacts = $contacts ?? [];

// تبدیل تاریخ تولد میلادی به شمسی برای نمایش در فرم
$birth_date_jalali = '';
if ($isEdit && $client->birth_date && $client->birth_date !== '0000-00-00') {
    $birth_date_jalali = JalaliDate::toJalali($client->birth_date);
}

$currentClientType = $isEdit ? $client->user_type : 'real';

?>
<style>
    .section-box {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .section-header {
        border-bottom: 2px solid #e0e0e0;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
    }
    .section-title {
        color: #34495e;
        font-weight: 700;
        font-size: 1.25rem;
    }
    .section-box.bg-light-blue {
        background-color: #f0f4f7;
    }
    .form-label {
        font-weight: 600;
        color: #555;
    }
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        font-weight: 600;
    }
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
    }
    .remove-contact-btn {
        font-size: 0.8rem;
    }
</style>

<h1 class="mb-4"><?php echo $pageTitle; ?></h1>

<form action="<?php echo $formAction; ?>" method="POST" enctype="multipart/form-data">
    <!-- بخش انتخاب نوع مشتری -->
    <div class="section-box">
        <div class="section-header">
            <span class="section-title">اطلاعات پایه</span>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <label for="user_type" class="form-label">نوع مشتری:</label>
                <select class="form-select" id="user_type" name="user_type" required>
                    <option value="real" <?php echo ($currentClientType == 'real') ? 'selected' : ''; ?>>حقیقی</option>
                    <option value="legal" <?php echo ($currentClientType == 'legal') ? 'selected' : ''; ?>>حقوقی</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="name" class="form-label">نام شخص مسئول / نام مشتری:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $isEdit ? sanitize($client->name) : ''; ?>" required>
            </div>
        </div>
    </div>
    
    <!-- بخش فیلدهای مشتری حقیقی -->
    <div id="real-fields" class="section-box bg-light-blue" style="<?php echo ($currentClientType == 'legal') ? 'display: none;' : ''; ?>">
        <div class="section-header">
            <span class="section-title">اطلاعات مشتری حقیقی</span>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <label for="contact_person" class="form-label">نام شخص تماس (اختیاری):</label>
                <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo $isEdit ? sanitize($client->contact_person) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label for="national_code" class="form-label">کد ملی:</label>
                <input type="text" class="form-control" id="national_code" name="national_code" value="<?php echo $isEdit ? sanitize($client->national_code) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label for="birth_date_jalali" class="form-label">تاریخ تولد (شمسی):</label>
                <input type="text" class="form-control persian-datepicker" id="birth_date_jalali"
                       value="<?php echo htmlspecialchars($birth_date_jalali); ?>"
                       data-alt-field="#birth_date">
                <input type="hidden" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($client->birth_date ?? ''); ?>">
            </div>
            <div class="col-md-6">
                <label for="profile_image" class="form-label">تصویر مشتری:</label>
                <input type="file" class="form-control" id="profile_image" name="profile_image">
                <?php if ($isEdit && $client->profile_image): ?>
                    <div class="mt-2">
                        <small class="text-muted">تصویر فعلی:</small>
                        <img src="<?php echo APP_URL; ?>/public/uploads/clients_images/<?php echo sanitize($client->profile_image); ?>" alt="Profile Image" class="img-thumbnail" style="width: 100px;">
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-12">
                <label for="national_card_image" class="form-label">تصویر کارت ملی:</label>
                <input type="file" class="form-control" id="national_card_image" name="national_card_image">
                <?php if ($isEdit && $client->national_card_image): ?>
                    <div class="mt-2">
                        <small class="text-muted">تصویر فعلی:</small>
                        <img src="<?php echo APP_URL; ?>/public/uploads/clients_images/<?php echo sanitize($client->national_card_image); ?>" alt="National Card" class="img-thumbnail" style="width: 100px;">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- بخش فیلدهای مشتری حقوقی -->
    <div id="legal-fields" class="section-box" style="<?php echo ($currentClientType == 'real') ? 'display: none;' : ''; ?>">
        <div class="section-header">
            <span class="section-title">اطلاعات شرکت</span>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <label for="company_name" class="form-label">نام شرکت:</label>
                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo $isEdit ? sanitize($client->company_name) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label for="company_national_id" class="form-label">شناسه ملی:</label>
                <input type="text" class="form-control" id="company_national_id" name="company_national_id" value="<?php echo $isEdit ? sanitize($client->company_national_id) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label for="company_phone" class="form-label">تلفن ثابت:</label>
                <input type="text" class="form-control" id="company_phone" name="company_phone" value="<?php echo $isEdit ? sanitize($client->company_phone) : ''; ?>">
            </div>
            <div class="col-md-6">
                <label for="company_address" class="form-label">آدرس شرکت:</label>
                <textarea class="form-control" id="company_address" name="company_address" rows="1"><?php echo $isEdit ? sanitize($client->company_address) : ''; ?></textarea>
            </div>
            <div class="col-md-12">
                <label for="company_logo_image" class="form-label">لوگوی شرکت:</label>
                <input type="file" class="form-control" id="company_logo_image" name="company_logo_image">
                <?php if ($isEdit && $client->company_logo_image): ?>
                    <div class="mt-2">
                        <small class="text-muted">لوگوی فعلی:</small>
                        <img src="<?php echo APP_URL; ?>/public/uploads/clients_images/<?php echo sanitize($client->company_logo_image); ?>" alt="Company Logo" class="img-thumbnail" style="width: 100px;">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- بخش جدید برای ثبت رابط‌های مشتری حقوقی -->
    <div id="legal-contacts-section" class="section-box bg-light-blue" style="<?php echo ($currentClientType == 'real') ? 'display: none;' : ''; ?>">
        <div class="section-header">
            <span class="section-title">رابطین شرکت</span>
        </div>
        <p class="text-muted">اطلاعات افرادی که به نمایندگی از شرکت با سامانه در ارتباط هستند را وارد کنید.</p>
        <div id="contacts-container">
            <?php if ($isEdit && !empty($contacts)): ?>
                <?php foreach ($contacts as $contact): ?>
                    <div class="row g-3 mb-3 p-3 border rounded">
                        <div class="col-md-6">
                            <label class="form-label">نام و نام خانوادگی:</label>
                            <input type="text" class="form-control" name="contacts[<?php echo sanitize($contact->id); ?>][name]" value="<?php echo sanitize($contact->user_name); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">سمت:</label>
                            <input type="text" class="form-control" name="contacts[<?php echo sanitize($contact->id); ?>][position]" value="<?php echo sanitize($contact->position); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">شماره همراه:</label>
                            <input type="text" class="form-control" name="contacts[<?php echo sanitize($contact->id); ?>][phone]" value="<?php echo sanitize($contact->mobile_number); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ایمیل:</label>
                            <input type="email" class="form-control" name="contacts[<?php echo sanitize($contact->id); ?>][email]" value="<?php echo sanitize($contact->email); ?>">
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-contact-btn">حذف</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-contact-btn"><i class="fas fa-plus"></i> افزودن رابط جدید</button>
    </div>

    <!-- بخش فیلدهای مشترک -->
    <div class="section-box">
        <div class="section-header">
            <span class="section-title">اطلاعات تماس</span>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <label for="email" class="form-label">ایمیل:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $isEdit ? sanitize($client->email) : ''; ?>" required>
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">شماره تلفن:</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $isEdit ? sanitize($client->phone) : ''; ?>" required>
            </div>
            <div class="col-md-12">
                <label for="address" class="form-label">آدرس:</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?php echo $isEdit ? sanitize($client->address) : ''; ?></textarea>
            </div>
            <div class="col-md-12">
                <label for="user_id" class="form-label">کاربر مرتبط:</label>
                <select class="form-select" id="user_id" name="user_id">
                    <option value="">انتخاب کاربر (اختیاری)</option>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo sanitize($user->id); ?>"
                                <?php echo ($isEdit && $client->user_id == $user->id) ? 'selected' : ''; ?>>
                                <?php echo sanitize($user->name); ?> (<?php echo sanitize($user->email); ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <small class="form-text text-muted">این کاربر باید نقش "client" داشته باشد.</small>
            </div>
        </div>
    </div>
    
    <div class="mt-4 text-start">
        <button type="submit" class="btn btn-primary me-2"><?php echo $buttonText; ?></button>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn btn-secondary">بازگشت</a>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userTypeSelect = document.getElementById('user_type');
        const realFields = document.getElementById('real-fields');
        const legalFields = document.getElementById('legal-fields');
        const legalContactsSection = document.getElementById('legal-contacts-section');
        const addContactBtn = document.getElementById('add-contact-btn');
        const contactsContainer = document.getElementById('contacts-container');
        let contactCounter = 0;

        function toggleFields() {
            if (userTypeSelect.value === 'real') {
                realFields.style.display = 'block';
                legalFields.style.display = 'none';
                legalContactsSection.style.display = 'none';
            } else {
                realFields.style.display = 'none';
                legalFields.style.display = 'block';
                legalContactsSection.style.display = 'block';
            }
        }
        userTypeSelect.addEventListener('change', toggleFields);
        toggleFields(); // Initial call to set correct state

        function addContactField(contact = {}) {
            contactCounter++;
            const contactDiv = document.createElement('div');
            contactDiv.classList.add('row', 'g-3', 'mb-3', 'p-3', 'border', 'rounded');
            contactDiv.innerHTML = `
                <div class="col-md-6">
                    <label class="form-label">نام و نام خانوادگی:</label>
                    <input type="text" class="form-control" name="contacts[new_${contactCounter}][name]" value="${contact.name || ''}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">سمت:</label>
                    <input type="text" class="form-control" name="contacts[new_${contactCounter}][position]" value="${contact.position || ''}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">شماره همراه:</label>
                    <input type="text" class="form-control" name="contacts[new_${contactCounter}][phone]" value="${contact.phone || ''}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ایمیل:</label>
                    <input type="email" class="form-control" name="contacts[new_${contactCounter}][email]" value="${contact.email || ''}">
                </div>
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-contact-btn">حذف</button>
                </div>
            `;
            contactsContainer.appendChild(contactDiv);
        }

        addContactBtn.addEventListener('click', () => addContactField());
        
        contactsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-contact-btn')) {
                event.target.closest('.row').remove();
            }
        });
        
        // اگر در حال ویرایش هستیم، رابط‌های موجود را بارگذاری کن
        <?php if ($isEdit && $currentClientType == 'legal' && !empty($contacts)): ?>
            <?php foreach ($contacts as $contact): ?>
                addContactField({
                    name: "<?php echo sanitize($contact->user_name); ?>",
                    position: "<?php echo sanitize($contact->position); ?>",
                    phone: "<?php echo sanitize($contact->mobile_number); ?>",
                    email: "<?php echo sanitize($contact->email); ?>"
                });
            <?php endforeach; ?>
        <?php endif; ?>
    });
</script>
