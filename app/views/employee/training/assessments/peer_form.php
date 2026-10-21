<?php
// app/views/employee/training/assessments/peer_form.php - نسخه اولترا پرمیوم و شاهکار
// فرم ارزیابی همکار / ارزیابی ۳۶۰ درجه پرسنل همراه با آواتار داینامیک و سیستم امتیازدهی
$targetEmployee = $targetEmployee ?? null;
$skills = $skills ?? [];
?>

<style>
    .peer-card-header {
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
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        transform: scale(1.08);
    }

    .form-control-peer {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding: 0.9rem 1.1rem;
        font-size: 0.9rem;
        transition: all 0.25s ease;
    }

    .form-control-peer:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
    }

    .btn-submit-peer {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 14px;
        padding: 0.9rem 2.5rem;
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

    .peer-avatar-circle {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: #d1fae5;
        color: #059669;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        font-weight: 800;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">ارزیابی عملکرد همکار (۳۶۰ درجه)</h1>
        <p class="text-muted small mb-0">ارزیابی کارگروهی، روحیه‌ی همیاری و استانداردهای حرفه‌ای همکاران پروژه</p>
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
            <div class="peer-card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="peer-avatar-circle">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <h6 class="m-0 fw-bold text-white fs-6">فرم ارزیابی برای: <?php echo sanitize($targetEmployee->name ?? $targetEmployee->full_name ?? 'همکار محترم'); ?></h6>
                        <span class="small text-white-50">پاسخ‌های شما کاملاً محرمانه نگه داشته شده و صرفاً جهت توسعه سازمانی استفاده می‌شود</span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_peer_assessment" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo sanitize($targetEmployee->id ?? ''); ?>">

                    <div class="d-flex flex-column gap-3 mb-4">
                        <?php foreach ($skills as $key => $title): ?>
                            <div class="skill-rating-item d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">
                                        <i class="fas fa-check-circle text-emerald-600 me-2" style="color:#059669;"></i>
                                        <span><?php echo sanitize($title); ?></span>
                                    </h6>
                                    <span class="text-muted small">ارزیابی سطح عملکرد همکار در این معیار</span>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <span class="small fw-bold text-muted me-2">امتیاز پیشنهادی (۱ تا ۵):</span>
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
                            <i class="fas fa-comment-dots text-emerald-600 me-1" style="color:#059669;"></i>
                            بازخورد و یادداشت‌های پیشنهادی جهت رشد همکار:
                        </label>
                        <textarea name="notes" id="notes" rows="3" class="form-control form-control-peer" placeholder="شرح ویژگی‌های مثبت یا حوزه‌های قابل توسعه در همکاری مشترک..."></textarea>
                    </div>

                    <div class="d-flex align-items-center justify-content-end">
                        <button type="submit" class="btn-submit-peer d-inline-flex align-items-center gap-2">
                            <i class="fas fa-paper-plane"></i>
                            <span>ثبت نهایی ارزیابی همکار</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>