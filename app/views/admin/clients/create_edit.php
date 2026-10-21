<?php
// app/views/admin/clients/create_edit.php - نسخه نهایی اولترا پرمیوم

$isEdit = isset($client) && $client !== null;
$formAction = $isEdit ? APP_URL . '/index.php?page=admin&action=updateClient&id=' . htmlspecialchars($client->id) : APP_URL . '/index.php?page=admin&action=clients_store';
$pageTitle = $isEdit ? 'ویرایش پرونده مشتری' : 'افزودن مشتری جدید';
$submitButtonText = $isEdit ? 'ذخیره تغییرات' : 'ثبت و ایجاد مشتری';

$users = $users ?? [];
$contacts = $contacts ?? [];

$birth_date_jalali = '';
if ($isEdit && isset($client->birth_date) && $client->birth_date && $client->birth_date !== '0000-00-00') {
    $birth_date_jalali = JalaliDate::toJalali($client->birth_date);
}

$user_type = $isEdit ? ($client->user_type ?? 'real') : 'real';
$client_type = $isEdit ? ($client->client_type ?? 'customer') : 'customer';
?>
<style>
    .fin-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 1.5rem;
        transition: all 0.25s ease;
    }
    .fin-card-header {
        padding: 1.25rem 1.5rem;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .fin-card-title {
        font-weight: 800;
        color: #0f172a;
        font-size: 1rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-fin-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.75rem;
        font-weight: 700;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-fin-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99,102,241,0.35);
        color: #fff;
    }

    .btn-fin-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-fin-secondary:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .form-control-premium, .form-select-premium {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.65rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    .form-control-premium:focus, .form-select-premium:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    /* Contact Row Premium Card */
    .contact-card-row {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s ease;
    }
    .contact-card-row:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }

    .img-preview-box {
        width: 90px;
        height: 90px;
        border-radius: 14px;
        object-fit: cover;
        border: 2px solid #e2e8f0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1"><?php echo $pageTitle; ?></h1>
        <p class="text-muted small mb-0">تنظیم پرونده اشخاص حقیقی و حقوقی، نمایندگان و مدارک هویتی</p>
    </div>
    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn btn-fin-secondary d-inline-flex align-items-center gap-2">
        <i class="fas fa-arrow-right"></i>
        <span>بازگشت به لیست مشتریان</span>
    </a>
</div>

<form action="<?php echo $formAction; ?>" method="POST" enctype="multipart/form-data">
    
    <!-- ۱. اطلاعات پایه -->
    <div class="fin-card">
        <div class="fin-card-header">
            <h6 class="fin-card-title">
                <i class="fas fa-sliders text-indigo-500" style="color:#6366f1;"></i>
                <span>اطلاعات پایه و دسته‌بندی</span>
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="user_type" class="form-label fw-bold text-secondary small">نوع شخص:</label>
                    <select class="form-select form-select-premium" id="user_type" name="user_type" required>
                        <option value="real" <?php echo ($user_type == 'real') ? 'selected' : ''; ?>>شخص حقیقی</option>
                        <option value="legal" <?php echo ($user_type == 'legal') ? 'selected' : ''; ?>>شخص حقوقی (شرکت / سازمان)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="client_type" class="form-label fw-bold text-secondary small">نقش کاری:</label>
                    <select class="form-select form-select-premium" id="client_type" name="client_type" required>
                        <option value="customer" <?php echo ($client_type == 'customer') ? 'selected' : ''; ?>>مشتری / کارفرما</option>
                        <option value="vendor" <?php echo ($client_type == 'vendor') ? 'selected' : ''; ?>>تامین‌کننده / فروشنده</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label for="name" class="form-label fw-bold text-secondary small">نام مشتری (حقیقی) / عنوان برند و شرکت (حقوقی):</label>
                    <input type="text" class="form-control form-control-premium" id="name" name="name" value="<?php echo $isEdit ? sanitize($client->name) : ''; ?>" placeholder="مثال: علی محمدی یا شرکت رایان تکرو" required>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ۲. اطلاعات اختصاصی مشتری حقیقی -->
    <div id="real-fields" class="fin-card" style="<?php echo ($user_type == 'legal') ? 'display: none;' : ''; ?>">
        <div class="fin-card-header">
            <h6 class="fin-card-title">
                <i class="fas fa-id-card text-indigo-500" style="color:#6366f1;"></i>
                <span>اطلاعات مشتری حقیقی و مدارک هویتی</span>
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="contact_person_real" class="form-label fw-bold text-secondary small">نام شخص تماس (اختیاری):</label>
                    <input type="text" class="form-control form-control-premium" id="contact_person_real" name="contact_person" value="<?php echo $isEdit ? sanitize($client->contact_person) : ''; ?>">
                </div>
                <div class="col-md-6">
                    <label for="national_code" class="form-label fw-bold text-secondary small">کد ملی:</label>
                    <input type="text" class="form-control form-control-premium font-monospace" id="national_code" name="national_code" value="<?php echo $isEdit ? sanitize($client->national_code) : ''; ?>" placeholder="1234567890">
                </div>
                <div class="col-md-6">
                    <label for="birth_date_jalali" class="form-label fw-bold text-secondary small">تاریخ تولد (شمسی):</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted"><i class="far fa-calendar-alt"></i></span>
                        <input type="text" class="form-control form-control-premium border-start-0 rounded-end-3 persian-datepicker" id="birth_date_jalali" value="<?php echo htmlspecialchars($birth_date_jalali); ?>" autocomplete="off" data-alt-field="#birth_date">
                    </div>
                    <input type="hidden" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($client->birth_date ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label for="profile_image" class="form-label fw-bold text-secondary small">تصویر پرتره مشتری:</label>
                    <input type="file" class="form-control form-control-premium" id="profile_image" name="profile_image">
                    <?php if ($isEdit && $client->profile_image): ?>
                        <div class="mt-3 d-flex align-items-center gap-3 p-2 bg-light rounded-3 border">
                            <img src="<?php echo APP_URL; ?>/public/uploads/clients_images/<?php echo sanitize($client->profile_image); ?>" alt="Profile" class="img-preview-box">
                            <div>
                                <span class="small fw-bold d-block text-slate-700">تصویر فعلی پرتره</span>
                                <span class="text-muted text-xs">در صورت آپلود فایل جدید، جایگزین می‌شود.</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-12">
                    <label for="national_card_image" class="form-label fw-bold text-secondary small">تصویر کارت ملی:</label>
                    <input type="file" class="form-control form-control-premium" id="national_card_image" name="national_card_image">
                    <?php if ($isEdit && $client->national_card_image): ?>
                        <div class="mt-3 d-flex align-items-center gap-3 p-2 bg-light rounded-3 border">
                            <img src="<?php echo APP_URL; ?>/public/uploads/clients_images/<?php echo sanitize($client->national_card_image); ?>" alt="National Card" class="img-preview-box">
                            <div>
                                <span class="small fw-bold d-block text-slate-700">تصویر فعلی کارت ملی</span>
                                <span class="text-muted text-xs">آپلود شده در سامانه</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ۳. اطلاعات اختصاصی شرکت / شخص حقوقی -->
    <div id="legal-fields" class="fin-card" style="<?php echo ($user_type == 'real') ? 'display: none;' : ''; ?>">
        <div class="fin-card-header">
            <h6 class="fin-card-title">
                <i class="fas fa-building text-indigo-500" style="color:#6366f1;"></i>
                <span>مشخصات رسمی و ثبتی شرکت</span>
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="company_name" class="form-label fw-bold text-secondary small">نام رسمی شرکت:</label>
                    <input type="text" class="form-control form-control-premium" id="company_name" name="company_name" value="<?php echo $isEdit ? sanitize($client->company_name ?? '') : ''; ?>">
                </div>
                <div class="col-md-6">
                    <label for="economic_code" class="form-label fw-bold text-secondary small">کد اقتصادی:</label>
                    <input type="text" class="form-control form-control-premium font-monospace" id="economic_code" name="economic_code" value="<?php echo $isEdit ? sanitize($client->economic_code ?? '') : ''; ?>">
                </div>
                <div class="col-md-6">
                    <label for="registration_number" class="form-label fw-bold text-secondary small">شماره ثبت / شناسه ملی:</label>
                    <input type="text" class="form-control form-control-premium font-monospace" id="registration_number" name="registration_number" value="<?php echo $isEdit ? sanitize($client->registration_number ?? '') : ''; ?>">
                </div>
                <div class="col-md-6">
                    <label for="company_logo_image" class="form-label fw-bold text-secondary small">لوگوی رسمی شرکت:</label>
                    <input type="file" class="form-control form-control-premium" id="company_logo_image" name="company_logo_image">
                    <?php if ($isEdit && $client->company_logo_image): ?>
                        <div class="mt-3 d-flex align-items-center gap-3 p-2 bg-light rounded-3 border">
                            <img src="<?php echo APP_URL; ?>/public/uploads/clients_images/<?php echo sanitize($client->company_logo_image); ?>" alt="Company Logo" class="img-preview-box">
                            <div>
                                <span class="small fw-bold d-block text-slate-700">لوگوی فعلی</span>
                                <span class="text-muted text-xs">ثبت‌شده در پرونده</span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ۴. رابطین شرکت -->
    <div id="legal-contacts-section" class="fin-card" style="<?php echo ($user_type == 'real') ? 'display: none;' : ''; ?>">
        <div class="fin-card-header">
            <h6 class="fin-card-title">
                <i class="fas fa-users-gear text-indigo-500" style="color:#6366f1;"></i>
                <span>رابطین و نمایندگان شرکت</span>
            </h6>
            <button type="button" class="btn btn-sm btn-indigo text-white fw-bold px-3 py-2 rounded-3" id="add-contact-btn" style="background:#6366f1;">
                <i class="fas fa-plus me-1"></i> افزودن رابط جدید
            </button>
        </div>
        <div class="card-body p-4">
            <p class="text-muted small mb-3">مشخصات افرادی که به نمایندگی از این شرکت با مجموعه در ارتباط هستند:</p>
            
            <div id="contacts-container">
                <?php if ($isEdit && !empty($contacts)): ?>
                    <?php 
                    $contactCounter = 0;
                    foreach ($contacts as $contact): 
                    ?>
                        <div class="contact-card-row">
                            <input type="hidden" name="contacts[<?php echo $contactCounter; ?>][id]" value="<?php echo htmlspecialchars($contact->id); ?>">
                            <input type="hidden" name="contacts[<?php echo $contactCounter; ?>][user_id]" value="<?php echo htmlspecialchars($contact->user_id); ?>">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">نام و نام خانوادگی:</label>
                                    <input type="text" class="form-control form-control-premium" name="contacts[<?php echo $contactCounter; ?>][name]" value="<?php echo htmlspecialchars($contact->user_name); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">سمت سازمانی:</label>
                                    <input type="text" class="form-control form-control-premium" name="contacts[<?php echo $contactCounter; ?>][position]" value="<?php echo htmlspecialchars($contact->position); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">شماره همراه:</label>
                                    <input type="text" class="form-control form-control-premium font-monospace" name="contacts[<?php echo $contactCounter; ?>][phone]" value="<?php echo htmlspecialchars($contact->mobile_number); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-secondary small">پست الکترونیک (ایمیل):</label>
                                    <input type="email" class="form-control form-control-premium font-monospace" name="contacts[<?php echo $contactCounter; ?>][email]" value="<?php echo htmlspecialchars($contact->email); ?>">
                                </div>
                                <div class="col-12 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-3 remove-contact-btn" data-contact-id="<?php echo htmlspecialchars($contact->id); ?>">
                                        <i class="fas fa-trash me-1"></i> حذف رابط
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php 
                    $contactCounter++; 
                    endforeach; 
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- ۵. اطلاعات تماس و حساب کاربری -->
    <div class="fin-card">
        <div class="fin-card-header">
            <h6 class="fin-card-title">
                <i class="fas fa-address-book text-indigo-500" style="color:#6366f1;"></i>
                <span>اطلاعات تماس و اتصال به حساب کاربر</span>
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="email" class="form-label fw-bold text-secondary small">پست الکترونیک اصلی:</label>
                    <input type="email" class="form-control form-control-premium font-monospace" id="email" name="email" value="<?php echo $isEdit ? sanitize($client->email ?? '') : ''; ?>" required placeholder="info@example.com">
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label fw-bold text-secondary small">شماره تلفن ثابت / همراه اصلی:</label>
                    <input type="text" class="form-control form-control-premium font-monospace" id="phone" name="phone" value="<?php echo $isEdit ? sanitize($client->phone ?? '') : ''; ?>" required placeholder="02188888888">
                </div>
                <div class="col-md-12">
                    <label for="address" class="form-label fw-bold text-secondary small">نشانی اقامتگاه / دفتر مرکزی:</label>
                    <textarea class="form-control form-control-premium" id="address" name="address" rows="3" placeholder="آدرس کامل بر اساس اسناد رسمی..."><?php echo $isEdit ? sanitize($client->address ?? '') : ''; ?></textarea>
                </div>
                <div class="col-md-12">
                    <label for="user_id" class="form-label fw-bold text-secondary small">اتصال به کاربر سیستم (جهت دسترسی به پنل):</label>
                    <select class="form-select form-select-premium" id="user_id" name="user_id">
                        <option value="">انتخاب کاربر سیستم (اختیاری)</option>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo sanitize($user->id); ?>"
                                    <?php echo ($isEdit && $client->user_id == $user->id) ? 'selected' : ''; ?>>
                                    <?php echo sanitize($user->name); ?> (<?php echo sanitize($user->email); ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <small class="form-text text-muted mt-1">کاربر انتخابی باید دارای نقش دسترسی "client" باشد.</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="d-flex align-items-center justify-content-end gap-3 mb-5">
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn btn-fin-secondary">انصراف</a>
        <button type="submit" class="btn btn-fin-primary d-inline-flex align-items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span><?php echo $submitButtonText; ?></span>
        </button>
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
            contactDiv.classList.add('contact-card-row');
            contactDiv.innerHTML = `
                <input type="hidden" name="contacts[${key}][id]" value="">
                <input type="hidden" name="contacts[${key}][user_id]" value="">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">نام و نام خانوادگی:</label>
                        <input type="text" class="form-control form-control-premium" name="contacts[${key}][name]" value="" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">سمت سازمانی:</label>
                        <input type="text" class="form-control form-control-premium" name="contacts[${key}][position]" value="" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">شماره همراه:</label>
                        <input type="text" class="form-control form-control-premium font-monospace" name="contacts[${key}][phone]" value="" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-secondary small">پست الکترونیک (ایمیل):</label>
                        <input type="email" class="form-control form-control-premium font-monospace" name="contacts[${key}][email]" value="">
                    </div>
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-3 remove-contact-btn"><i class="fas fa-trash me-1"></i> حذف رابط</button>
                    </div>
                </div>
            `;
            contactsContainer.appendChild(contactDiv);
        }

        addContactBtn.addEventListener('click', () => addContactField());
        
        contactsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-contact-btn') || event.target.closest('.remove-contact-btn')) {
                const row = event.target.closest('.contact-card-row');
                row.remove();
            }
        });
    });

    (function loadPersianDatePicker() {
        function initPicker() {
            if (typeof $ !== 'undefined' && $.fn && $.fn.pDatepicker) {
                $('.persian-datepicker').pDatepicker({ format: 'YYYY/MM/DD', autoClose: true, initialValue: false, observer: true, initialValueType: 'persian' });
            } else { setTimeout(initPicker, 100); }
        }
        if (!document.getElementById('p-datepicker-css')) {
            var css = document.createElement('link'); css.id = 'p-datepicker-css'; css.rel = 'stylesheet';
            css.href = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css';
            document.head.appendChild(css);
        }
        if (typeof $ === 'undefined') {
            var jq = document.createElement('script'); jq.src = 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'; jq.onload = loadPDate; document.head.appendChild(jq);
        } else { loadPDate(); }
        function loadPDate() {
            if (typeof window.persianDate === 'undefined') {
                var pd = document.createElement('script'); pd.src = 'https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js'; pd.onload = loadPDatePicker; document.head.appendChild(pd);
            } else { loadPDatePicker(); }
        }
        function loadPDatePicker() {
            if (typeof $.fn.pDatepicker === 'undefined') {
                var pdp = document.createElement('script'); pdp.src = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js'; pdp.onload = initPicker; document.head.appendChild(pdp);
            } else { initPicker(); }
        }
    })();
</script>