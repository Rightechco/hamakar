<?php
// app/views/admin/projects/create_edit.php

$isEdit = isset($project) && $project !== null;
$formAction = $isEdit 
    ? APP_URL . '/index.php?page=admin&action=projects_update&id=' . $project->id 
    : APP_URL . '/index.php?page=admin&action=projects_store';
$pageTitle = $isEdit ? 'ویرایش اطلاعات پروژه' : 'ایجاد پروژه جدید';
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

    body, .project-form-wrapper {
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
        padding: 0.65rem 1.6rem;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: all 0.2s ease;
    }

    .btn-gradient-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        color: #fff;
    }
</style>

<div class="project-form-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><?php echo $pageTitle; ?></h3>
            <p class="text-muted small mb-0">تعریف پروژه، انتخاب کارفرما، زمان‌بندی و وضعیت اجرای آن</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=projects" class="btn btn-outline-secondary btn-sm px-4 rounded-pill fw-medium">
            <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست پروژه‌ها
        </a>
    </div>

    <?php FlashMessage::display(); ?>

    <!-- Form Area -->
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="premium-card">
                <div class="premium-card-header d-flex align-items-center">
                    <i class="fas fa-folder-plus text-primary fs-5 me-2"></i>
                    <h5 class="mb-0 fw-bold fs-6 text-dark"><?php echo $isEdit ? 'فرم ویرایش پروژه' : 'فرم ثبت پروژه جدید'; ?></h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo $formAction; ?>" method="POST">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-medium small text-dark">نام پروژه <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo sanitize($project->name ?? ''); ?>" placeholder="عنوان پروژه را وارد کنید..." required autofocus>
                            </div>
                            <div class="col-md-6">
                                <label for="client_id" class="form-label fw-medium small text-dark">مشتری / کارفرما <span class="text-muted">(اختیاری)</span></label>
                                <select class="form-select" id="client_id" name="client_id">
                                    <option value="">پروژه داخلی (بدون مشتری)</option>
                                    <?php if (!empty($clients)): ?>
                                        <?php foreach($clients as $client): ?>
                                            <option value="<?php echo $client->id; ?>" <?php echo ($isEdit && isset($project->client_id) && $project->client_id == $client->id) ? 'selected' : ''; ?>>
                                                <?php echo sanitize($client->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="contract_id" class="form-label fw-medium small text-dark">قرارداد مرتبط <span class="text-muted">(اختیاری)</span></label>
                                <select class="form-select" id="contract_id" name="contract_id">
                                    <option value="">بدون قرارداد مرتبط</option>
                                    <?php if (!empty($contracts)): ?>
                                        <?php foreach($contracts as $contract): ?>
                                            <option value="<?php echo $contract->id; ?>" <?php echo ($isEdit && isset($project->contract_id) && $project->contract_id == $contract->id) ? 'selected' : ''; ?>>
                                                <?php echo sanitize($contract->title); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="budget" class="form-label fw-medium small text-dark">بودجه پروژه (تومان)</label>
                                <input type="number" class="form-control" id="budget" name="budget" value="<?php echo sanitize($project->budget ?? 0); ?>" placeholder="0" min="0" step="1000">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-medium small text-dark">توضیحات و اهداف پروژه</label>
                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="خلاصه‌ای از اهداف و جزئیات پروژه را بنویسید..."><?php echo sanitize($project->description ?? ''); ?></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-medium small text-dark">تاریخ شروع</label>
                                <div class="input-group">
                                    <span class="input-group-text pdp-trigger"><i class="far fa-calendar-alt text-muted"></i></span>
                                    <input type="text" class="form-control pdp-input border-start-0" id="start_date" name="start_date" value="<?php echo $start_date_jalali ?? ''; ?>" placeholder="YYYY/MM/DD" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="due_date" class="form-label fw-medium small text-dark">تاریخ تحویل (سررسید)</label>
                                <div class="input-group">
                                    <span class="input-group-text pdp-trigger"><i class="far fa-calendar-check text-muted"></i></span>
                                    <input type="text" class="form-control pdp-input border-start-0" id="due_date" name="due_date" value="<?php echo $end_date_jalali ?? ''; ?>" placeholder="YYYY/MM/DD" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label fw-medium small text-dark">وضعیت اجرا <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="not_started" <?php echo ($isEdit && $project->status == 'not_started') ? 'selected' : ''; ?>>شروع نشده</option>
                                <option value="in_progress" <?php echo ($isEdit && $project->status == 'in_progress') ? 'selected' : ''; ?>>در حال انجام</option>
                                <option value="on_hold" <?php echo ($isEdit && $project->status == 'on_hold') ? 'selected' : ''; ?>>متوقف شده</option>
                                <option value="canceled" <?php echo ($isEdit && $project->status == 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
                                <option value="finished" <?php echo ($isEdit && $project->status == 'finished') ? 'selected' : ''; ?>>تکمیل شده</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=projects" class="btn btn-light px-4 rounded-2">انصراف</a>
                            <button type="submit" class="btn btn-gradient-primary px-4">
                                <i class="fas fa-check me-1"></i> <?php echo $isEdit ? 'ذخیره تغییرات' : 'ایجاد پروژه جدید'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Persian Datepicker Loader & Auto Click Handler -->
<script>
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