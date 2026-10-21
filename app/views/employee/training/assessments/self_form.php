<?php
// app/views/employee/training/assessments/self_form.php - نسخه اولترا پرمیوم و شاهکار
// آزمون خودارزیابی عملکرد سالانه پرسنل با انیمیشن رتبه‌بندی ستاره‌ای ۵ امتیازی
$isSubmitted = $isSubmitted ?? false;
$skills = $skills ?? [];
$year = function_exists('jdate') ? jdate('Y') : date('Y');
?>

<style>
    .assessment-card-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        padding: 1.5rem;
        border-radius: 20px 20px 0 0;
    }

    .skill-rating-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        transition: all 0.25s ease;
    }

    .skill-rating-item:hover {
        border-color: #c7d2fe;
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.08);
        background: #fafafa;
    }

    /* استایل سیستم امتیازدهی ستاره‌ای */
    .rating-stars-group {
        display: inline-flex;
        flex-direction: row-reverse;
        gap: 0.5rem;
    }

    .rating-stars-group input[type="radio"] {
        display: none;
    }

    .rating-stars-group label {
        font-size: 1.5rem;
        color: #cbd5e1;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 0.25rem 0.5rem;
        border-radius: 8px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
    }

    .rating-stars-group label::before {
        content: attr(data-score);
        font-size: 0.875rem;
        font-weight: 800;
        font-family: monospace;
    }

    .rating-stars-group input[type="radio"]:checked ~ label,
    .rating-stars-group label:hover,
    .rating-stars-group label:hover ~ label {
        color: #ffffff;
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        transform: scale(1.08);
    }

    .form-control-assessment {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding: 0.9rem 1.1rem;
        font-size: 0.9rem;
        transition: all 0.25s ease;
    }

    .form-control-assessment:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .btn-submit-assessment {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 14px;
        padding: 0.9rem 2.5rem;
        font-weight: 800;
        font-size: 0.95rem;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }

    .btn-submit-assessment:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">آزمون خودارزیابی عملکرد - سال <?php echo $year; ?></h1>
        <p class="text-muted small mb-0">سنجش فردی مهارتی، تحلیل نقاط رشد و ارزیابی سطح عملکرد شغلی</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-2 fw-bold font-monospace" style="background:#e0e7ff; color:#4338ca;">
            <i class="fas fa-user-check me-1"></i>
            ارزیابی فردی
        </span>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
            <div class="assessment-card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-white bg-opacity-10 text-warning" style="width:44px; height:44px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-star fs-4"></i>
                    </div>
                    <div>
                        <h6 class="m-0 fw-bold text-white fs-6">فرم ثبت خودارزیابی عملکرد ۳۶۰ درجه</h6>
                        <span class="small text-white-50">امتیازدهی منصفانه و واقع‌بینانه به کانون‌های ارزیابی زیر</span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <?php if ($isSubmitted): ?>
                    <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 d-flex align-items-center gap-3" style="background:#d1fae5; color:#065f46;">
                        <i class="fas fa-circle-check fs-1 text-emerald-600" style="color:#059669;"></i>
                        <div>
                            <h6 class="fw-bold mb-1">خودارزیابی شما با موفقیت به ثبت رسید.</h6>
                            <p class="small mb-0 opacity-80">شما قبلاً خودارزیابی سالانه خود را جهت تحویل به منابع انسانی تکمیل کرده‌اید.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_self_assessment" method="POST">
                        
                        <div class="d-flex flex-column gap-3 mb-4">
                            <?php foreach ($skills as $key => $title): ?>
                                <div class="skill-rating-item d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-1">
                                            <i class="fas fa-check-circle text-indigo-600 me-2" style="color:#6366f1;"></i>
                                            <span><?php echo sanitize($title); ?></span>
                                        </h6>
                                        <span class="text-muted small">کیفیت و الگوی عملکردی در این شاخص</span>
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="small fw-bold text-muted me-2">امتیاز (۱ تا ۵):</span>
                                        <div class="rating-stars-group">
                                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                                <input type="radio" name="scores[<?php echo $key; ?>]" id="score-<?php echo $key; ?>-<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                                                <label for="score-<?php echo $key; ?>-<?php echo $i; ?>" data-score="<?php echo $i; ?>"></label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label fw-bold text-dark small">
                                <i class="fas fa-pen-to-square text-indigo-600 me-1" style="color:#6366f1;"></i>
                                توضیحات و یادداشت‌های تکمیلی خودارزیابی:
                            </label>
                            <textarea name="notes" id="notes" rows="3" class="form-control form-control-assessment" placeholder="شرح دستاوردها یا موانعی که در ارزیابی شما موثر بوده‌اند..."></textarea>
                        </div>

                        <div class="d-flex align-items-center justify-content-end">
                            <button type="submit" class="btn-submit-assessment d-inline-flex align-items-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                <span>ثبت و ارسال خودارزیابی</span>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>