<?php
// app/views/admin/training/needs/view.php - نسخه اولترا پرمیوم
$trainingNeed = $trainingNeed ?? null;
$courses = $courses ?? [];
?>

<style>
    .view-card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .form-control-premium, .form-select-premium {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.65rem 0.9rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-control-premium:focus, .form-select-premium:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
    }

    .course-chip-checkbox {
        display: none;
    }

    .course-chip-label {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #475569;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
    }

    .course-chip-label:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
    }

    .course-chip-checkbox:checked + .course-chip-label {
        background: #e0e7ff;
        color: #4338ca;
        border-color: #818cf8;
        box-shadow: 0 4px 10px rgba(99, 102, 241, 0.15);
    }

    .btn-submit-final {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
        transition: all 0.25s ease;
    }

    .btn-submit-final:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(16, 185, 129, 0.35);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">بررسی فرم نیازسنجی: <?php echo sanitize($trainingNeed->employee_name ?? 'کارمند'); ?></h1>
        <p class="text-muted small mb-0">تحلیل نقاط قوت و ضعف اعلامی پرسنل و تعیین مسیر توسعه مهارت‌های شغلی</p>
    </div>

    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=training_needs" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
        <i class="fas fa-arrow-right"></i>
        <span>بازگشت به لیست نیازسنجی‌ها</span>
    </a>
</div>

<div class="row g-4">
    <!-- ستون اول: پاسخ‌های کارمند -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 rounded-4 h-100">
            <div class="view-card-header d-flex align-items-center gap-2">
                <i class="fas fa-user-pen text-indigo-600" style="color: #4f46e5;"></i>
                <h6 class="m-0 fw-bold text-dark">خودارزیابی و پاسخ‌های کارمند</h6>
            </div>
            
            <div class="card-body p-4">
                <div class="mb-4">
                    <label class="fw-bold text-success small d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-plus-circle"></i>
                        <span>نقاط قوت اعلام‌شده:</span>
                    </label>
                    <div class="p-3 bg-light rounded-3 border text-secondary small text-justify-custom">
                        <?php echo !empty($trainingNeed->strengths) ? nl2br(sanitize($trainingNeed->strengths)) : 'موردی ذکر نشده است.'; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold text-danger small d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-minus-circle"></i>
                        <span>نقاط نیازمند بهبود و ضعف:</span>
                    </label>
                    <div class="p-3 bg-light rounded-3 border text-secondary small text-justify-custom">
                        <?php echo !empty($trainingNeed->weaknesses) ? nl2br(sanitize($trainingNeed->weaknesses)) : 'موردی ذکر نشده است.'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ستون دوم: فرم تحلیل و پیشنهاد مدیر -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="view-card-header d-flex align-items-center gap-2">
                <i class="fas fa-chart-line text-emerald-600" style="color: #10b981;"></i>
                <h6 class="m-0 fw-bold text-dark">تحلیل، تصمیم‌گیری و پیشنهاد مدیر</h6>
            </div>

            <div class="card-body p-4">
                <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=process_training_need&id=<?php echo $trainingNeed->id ?? 0; ?>" method="POST">
                    
                    <div class="mb-3">
                        <label for="development_areas" class="form-label fw-bold text-secondary small">زمینه‌های توسعه و برنامه‌های پیشنهادی:</label>
                        <textarea name="development_areas" id="development_areas" rows="4" class="form-control form-control-premium" placeholder="توضیحات و راه‌کارهای ارتقای مهارتی کارمند..." required><?php echo sanitize($trainingNeed->development_areas ?? ''); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary small d-block mb-2">انتخاب دوره‌های آموزشی پیشنهادی:</label>
                        <div class="d-flex flex-wrap gap-2 max-height-180 overflow-y-auto p-2 border rounded-3 bg-light">
                            <?php if (!empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <div>
                                        <input type="checkbox" name="course_suggestions[]" value="<?php echo $course->id; ?>" id="course-<?php echo $course->id; ?>" class="course-chip-checkbox">
                                        <label for="course-<?php echo $course->id; ?>" class="course-chip-label">
                                            <i class="fas fa-graduation-cap"></i>
                                            <span><?php echo sanitize($course->course_title); ?></span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted small p-2">هیچ دوره آموزشی در سیستم تعریف نشده است.</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label fw-bold text-secondary small">وضعیت نهایی فرم نیازسنجی:</label>
                        <select name="status" id="status" class="form-select form-select-premium">
                            <option value="approved" selected>تایید شده (ارسال به برنامه‌ریزی)</option>
                            <option value="rejected">رد شده (نیاز به بازبینی)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn btn-submit-final d-inline-flex align-items-center gap-2">
                            <i class="fas fa-check-double"></i>
                            <span>ثبت نهایی و ذخیره نظر مدیریت</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>