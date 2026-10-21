<?php
// app/views/employee/training/assessments/peer_form.php - نسخه اولترا پرمیوم و شاهکار
// فرم ارزیابی ۳۶۰ درجه همکار با هماهنگی کامل با بک‌اند، UI/UX مدرن و سیستم امتیازدهی تعاملی

$targetEmployee = $targetEmployee ?? null;
$skills = $skills ?? [];

// استخراج نام و حرف اول جهت آواتار
$employeeName = $targetEmployee ? ($targetEmployee->name ?? $targetEmployee->full_name ?? 'همکار محترم') : 'همکار محترم';
$firstLetter = function_exists('mb_substr') ? mb_substr($employeeName, 0, 1, 'UTF-8') : 'H';
?>

<style>
    .peer-card-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        padding: 1.75rem;
        border-radius: 20px 20px 0 0;
        position: relative;
        overflow: hidden;
    }

    .peer-card-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .peer-avatar-box {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 800;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .skill-assessment-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1.5rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .skill-assessment-card:hover {
        border-color: #a7f3d0;
        box-shadow: 0 12px 28px -5px rgba(16, 185, 129, 0.12);
        background: #fafafa;
    }

    /* سیستم کارت‌پلی‌های انتخاب امتیاز ۵ گانه */
    .score-picker-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .score-option-item {
        flex: 1;
        min-width: 80px;
    }

    .score-option-item input[type="radio"] {
        display: none;
    }

    .score-option-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 0.65rem 0.5rem;
        border-radius: 12px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
    }

    .score-option-label .score-number {
        font-family: monospace, sans-serif;
        font-size: 1.1rem;
        font-weight: 800;
        color: #475569;
        margin-bottom: 2px;
    }

    .score-option-label .score-tag {
        font-size: 0.725rem;
        font-weight: 700;
        color: #94a3b8;
    }

    .score-option-item input[type="radio"]:checked + .score-option-label {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-color: #059669;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
        transform: translateY(-3px);
    }

    .score-option-item input[type="radio"]:checked + .score-option-label .score-number,
    .score-option-item input[type="radio"]:checked + .score-option-label .score-tag {
        color: #ffffff !important;
    }

    .form-control-peer {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding: 0.9rem 1.1rem;
        font-size: 0.9rem;
        transition: all 0.25s ease;
        background-color: #ffffff;
    }

    .form-control-peer:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
        background-color: #ffffff;
    }

    .btn-submit-peer {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 14px;
        padding: 0.95rem 2.75rem;
        font-weight: 800;
        font-size: 0.95rem;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .btn-submit-peer:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">ارزیابی عملکرد همکار: <?php echo sanitize($employeeName); ?></h1>
        <p class="text-muted small mb-0">سامانه ارزیابی ۳۶۰ درجه؛ تحلیل شاخص‌های مهارتی، کارگروهی و اخلاق حرفه‌ای</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-pill px-3 py-2 fw-bold font-monospace" style="background:#d1fae5; color:#059669;">
            <i class="fas fa-users-viewfinder me-1"></i>
            ارزیابی ۳۶۰ درجه
        </span>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            
            <!-- هدر کارت ارزیابی -->
            <div class="peer-card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="peer-avatar-box">
                        <?php echo $firstLetter; ?>
                    </div>
                    <div>
                        <h6 class="m-0 fw-bold text-white fs-5">فرم ارزیابی عملکرد <?php echo sanitize($employeeName); ?></h6>
                        <span class="small text-white-50">نظرات شما کاملاً محرمانه بوده و صرفاً جهت تحلیل توسعه سازمانی بازخورد داده می‌شود</span>
                    </div>
                </div>

                <div class="text-end">
                    <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 rounded-pill px-3 py-1.5 small font-monospace">
                        کد پرسنلی: #<?php echo sanitize($targetEmployee->id ?? '---'); ?>
                    </span>
                </div>
            </div>

            <!-- بدنه فرم -->
            <div class="card-body p-4 p-md-5">
                <?php if ($targetEmployee): ?>
                    <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_peer_assessment" method="POST">
                        <input type="hidden" name="user_id" value="<?php echo sanitize($targetEmployee->id); ?>">

                        <!-- لیست مهارت‌ها -->
                        <div class="d-flex flex-column gap-3.5 mb-4">
                            <?php foreach ($skills as $key => $title): ?>
                                <div class="skill-assessment-card">
                                    <div class="row align-items-center g-3">
                                        <div class="col-lg-5">
                                            <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                                <i class="fas fa-check-circle text-emerald-600" style="color:#059669;"></i>
                                                <span><?php echo sanitize($title); ?></span>
                                            </h6>
                                            <span class="text-muted small">کیفیت عملکرد همکار در این معیار چیست؟</span>
                                        </div>

                                        <div class="col-lg-7">
                                            <div class="score-picker-group">
                                                
                                                <!-- گزینه ۱ (ضعیف) -->
                                                <div class="score-option-item">
                                                    <input type="radio" name="scores[<?php echo $key; ?>]" id="score-<?php echo $key; ?>-1" value="1" required>
                                                    <label for="score-<?php echo $key; ?>-1" class="score-option-label">
                                                        <span class="score-number">۱</span>
                                                        <span class="score-tag">ضعیف</span>
                                                    </label>
                                                </div>

                                                <!-- گزینه ۲ -->
                                                <div class="score-option-item">
                                                    <input type="radio" name="scores[<?php echo $key; ?>]" id="score-<?php echo $key; ?>-2" value="2" required>
                                                    <label for="score-<?php echo $key; ?>-2" class="score-option-label">
                                                        <span class="score-number">۲</span>
                                                        <span class="score-tag">متوسط پایین</span>
                                                    </label>
                                                </div>

                                                <!-- گزینه ۳ (متوسط) -->
                                                <div class="score-option-item">
                                                    <input type="radio" name="scores[<?php echo $key; ?>]" id="score-<?php echo $key; ?>-3" value="3" required>
                                                    <label for="score-<?php echo $key; ?>-3" class="score-option-label">
                                                        <span class="score-number">۳</span>
                                                        <span class="score-tag">متوسط</span>
                                                    </label>
                                                </div>

                                                <!-- گزینه ۴ -->
                                                <div class="score-option-item">
                                                    <input type="radio" name="scores[<?php echo $key; ?>]" id="score-<?php echo $key; ?>-4" value="4" required>
                                                    <label for="score-<?php echo $key; ?>-4" class="score-option-label">
                                                        <span class="score-number">۴</span>
                                                        <span class="score-tag">خوب</span>
                                                    </label>
                                                </div>

                                                <!-- گزینه ۵ (عالی) -->
                                                <div class="score-option-item">
                                                    <input type="radio" name="scores[<?php echo $key; ?>]" id="score-<?php echo $key; ?>-5" value="5" required>
                                                    <label for="score-<?php echo $key; ?>-5" class="score-option-label">
                                                        <span class="score-number">۵</span>
                                                        <span class="score-tag">عالی</span>
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- یادداشت‌های تکمیلی -->
                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold text-dark small">
                                <i class="fas fa-comment-dots text-emerald-600 me-1" style="color:#059669;"></i>
                                یادداشت‌های تکمیلی و پیشنهادات بهبود:
                            </label>
                            <textarea name="notes" id="notes" rows="4" class="form-control form-control-peer" placeholder="توضیحات اختیاری، نقاط قوت برجسته یا موارد قابل ارتقا در همکاری..."></textarea>
                        </div>

                        <!-- دکمه ثبت -->
                        <div class="d-flex align-items-center justify-content-end">
                            <button type="submit" class="btn-submit-peer d-inline-flex align-items-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                <span>ثبت و ارسال ارزیابی</span>
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-xmark fs-1 text-muted mb-3 opacity-40"></i>
                        <h6 class="fw-bold text-dark mb-1">همکار مورد نظر یافت نشد.</h6>
                        <p class="text-muted small mb-0">اطلاعات کاربر جهت ارزیابی معتبر نمی‌باشد.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>