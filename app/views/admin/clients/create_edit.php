<?php
// app/views/admin/clients/create_edit.php - نسخه نهایی و کامل

$isEdit = isset($client) && $client !== null;
$formAction = $isEdit ? APP_URL . '/index.php?page=admin&action=updateClient&id=' . htmlspecialchars($client->id) : APP_URL . '/index.php?page=admin&action=clients_store';
$pageTitle = $isEdit ? 'ویرایش مشتری' : 'افزودن مشتری جدید';
$submitButtonText = $isEdit ? 'ذخیره تغییرات' : 'افزودن مشتری';

$users = $users ?? [];
$contacts = $contacts ?? [];

$birth_date_jalali = '';
if ($isEdit && isset($client->birth_date) && $client->birth_date && $client->birth_date !== '0000-00-00') {
    $birth_date_jalali = JalaliDate::toJalali($client->birth_date);
}

$user_type = $isEdit ? ($client->user_type ?? 'real') : 'real';
$display_real_fields = ($user_type === 'real') ? 'block' : 'none';
$display_legal_fields = ($user_type === 'legal') ? 'block' : 'none';

$client_type = $isEdit ? ($client->client_type ?? 'customer') : 'customer';
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
    
    <div class="section-box">
        <div class="section-header">
            <span class="section-title">اطلاعات پایه</span>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <label for="user_type" class="form-label">نوع شخص:</label>
                <select class="form-select" id="user_type" name="user_type" required>
                    <option value="real" <?php echo ($user_type == 'real') ? 'selected' : ''; ?>>حقیقی</option>
                    <option value="legal" <?php echo ($user_type == 'legal') ? 'selected' : ''; ?>>حقوقی</option>
                </select>
            </div>
            <div class="col-md-6">
                 <label for="client_type" class="form-label">نقش:</label>
                <select class="form-select" id="client_type" name="client_type" required>
                    <option value="customer" <?php echo ($client_type == 'customer') ? 'selected' : ''; ?>>مشتری</option>
                    <option value="vendor" <?php echo ($client_type == 'vendor') ? 'selected' : ''; ?>>تامین‌کننده</option>
                </select>
            </div>
            <div class="col-md-12">
                <label for="name" class="form-label">نام مشتری (حقیقی) / نام شرکت (حقوقی):</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $isEdit ? sanitize($client->name) : ''; ?>" required>
            </div>
        </div>
    </div>
    
    <div id="real-fields" class="section-box bg-light-blue" style="<?php echo ($user_type == 'legal') ? 'display: none;' : ''; ?>">
        <div class="section-header">
            <span class="section-title">اطلاعات مشتری حقیقی</span>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <label for="contact_person_real" class="form-label">نام شخص تماس (اختیاری):</label>
                <input type="text" class="form-control" id="contact_person_real" name="contact_person" value="<?php echo $isEdit ? sanitize($client->contact_person) : ''; ?>">
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

    <div id="legal-fields" class="section-box" style="<?php echo ($user_type == 'real') ? 'display: none;' : ''; ?>">
        <div class="section-header">
            <span class="section-title">اطلاعات شرکت</span>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <label for="company_name" class="form-label">نام شرکت</label>
                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo $isEdit ? sanitize($client->company_name ?? '') : ''; ?>">
            </div>
            <div class="col-md-6">
                <label for="economic_code" class="form-label">کد اقتصادی:</label>
                <input type="text" class="form-control" id="economic_code" name="economic_code" value="<?php echo $isEdit ? sanitize($client->economic_code ?? '') : ''; ?>">
            </div>
            <div class="col-md-6">
                <label for="registration_number" class="form-label">شماره ثبت:</label>
                <input type="text" class="form-control" id="registration_number" name="registration_number" value="<?php echo $isEdit ? sanitize($client->registration_number ?? '') : ''; ?>">
            </div>
            <div class="col-md-6">
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

    <div id="legal-contacts-section" class="section-box bg-light-blue" style="<?php echo ($user_type == 'real') ? 'display: none;' : ''; ?>">
        <div class="section-header">
            <span class="section-title">رابطین شرکت</span>
        </div>
        <p class="text-muted">اطلاعات افرادی که به نمایندگی از شرکت با سامانه در ارتباط هستند را وارد کنید.</p>
        <div id="contacts-container">
            <?php if ($isEdit && !empty($contacts)): ?>
                <?php 
                $contactCounter = 0;
                foreach ($contacts as $contact): 
                ?>
                    <div class="row g-3 mb-3 p-3 border rounded contact-row">
                        <input type="hidden" name="contacts[<?php echo $contactCounter; ?>][id]" value="<?php echo htmlspecialchars($contact->id); ?>">
                        <input type="hidden" name="contacts[<?php echo $contactCounter; ?>][user_id]" value="<?php echo htmlspecialchars($contact->user_id); ?>">
                        <div class="col-md-6">
                            <label class="form-label">نام و نام خانوادگی:</label>
                            <input type="text" class="form-control" name="contacts[<?php echo $contactCounter; ?>][name]" value="<?php echo htmlspecialchars($contact->user_name); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">سمت:</label>
                            <input type="text" class="form-control" name="contacts[<?php echo $contactCounter; ?>][position]" value="<?php echo htmlspecialchars($contact->position); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">شماره همراه:</label>
                            <input type="text" class="form-control" name="contacts[<?php echo $contactCounter; ?>][phone]" value="<?php echo htmlspecialchars($contact->mobile_number); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ایمیل:</label>
                            <input type="email" class="form-control" name="contacts[<?php echo $contactCounter; ?>][email]" value="<?php echo htmlspecialchars($contact->email); ?>">
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-contact-btn" data-contact-id="<?php echo htmlspecialchars($contact->id); ?>"><i class="fas fa-trash"></i> حذف</button>
                        </div>
                    </div>
                <?php 
                $contactCounter++; 
                endforeach; 
                ?>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-contact-btn"><i class="fas fa-plus"></i> افزودن رابط جدید</button>
    </div>
    
    <div class="section-box">
        <div class="section-header">
            <span class="section-title">اطلاعات تماس</span>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <label for="email" class="form-label">ایمیل:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $isEdit ? sanitize($client->email ?? '') : ''; ?>" required>
            </div>
            <div class="col-md-6">
                <label for="phone" class="form-label">شماره تلفن:</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $isEdit ? sanitize($client->phone ?? '') : ''; ?>" required>
            </div>
            <div class="col-md-12">
                <label for="address" class="form-label">آدرس:</label>
                <textarea class="form-control" id="address" name="address" rows="3"><?php echo $isEdit ? sanitize($client->address ?? '') : ''; ?></textarea>
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
        <button type="submit" class="btn btn-primary me-2"><?php echo $submitButtonText; ?></button>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn btn-secondary">بازگشت</a>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userTypeSelect = document.getElementById('user_type');
        const clientTypeSelect = document.getElementById('client_type');
        const realFields = document.getElementById('real-fields');
        const legalFields = document.getElementById('legal-fields');
        const legalContactsSection = document.getElementById('legal-contacts-section');
        const addContactBtn = document.getElementById('add-contact-btn');
        const contactsContainer = document.getElementById('contacts-container');
        let contactCounter = <?php echo $isEdit && !empty($contacts) ? count($contacts) : '0'; ?>;

        function toggleUserTypeFields() {
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
        
        userTypeSelect.addEventListener('change', toggleUserTypeFields);
        toggleUserTypeFields();

        function addContactField() {
            const key = `new_${contactCounter++}`;
            const contactDiv = document.createElement('div');
            contactDiv.classList.add('row', 'g-3', 'mb-3', 'p-3', 'border', 'rounded', 'contact-row');
            contactDiv.innerHTML = `
                <input type="hidden" name="contacts[${key}][id]" value="">
                <input type="hidden" name="contacts[${key}][user_id]" value="">
                <div class="col-md-6">
                    <label class="form-label">نام و نام خانوادگی:</label>
                    <input type="text" class="form-control" name="contacts[${key}][name]" value="" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">سمت:</label>
                    <input type="text" class="form-control" name="contacts[${key}][position]" value="" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">شماره همراه:</label>
                    <input type="text" class="form-control" name="contacts[${key}][phone]" value="" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ایمیل:</label>
                    <input type="email" class="form-control" name="contacts[${key}][email]" value="">
                </div>
                <div class="col-12 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-contact-btn"><i class="fas fa-trash"></i> حذف</button>
                </div>
            `;
            contactsContainer.appendChild(contactDiv);
        }

        addContactBtn.addEventListener('click', () => addContactField());
        
        contactsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-contact-btn') || event.target.closest('.remove-contact-btn')) {
                const row = event.target.closest('.contact-row');
                row.remove();
            }
        });
    });
</script>