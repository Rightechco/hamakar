<?php
// app/views/employee/training/needs_form.php - نسخه اولترا پرمیوم و شاهکار
// فرم نیازسنجی و ارزیابی مهارتی سالانه پرسنل همراه با هماهنگی کامل با بک‌اند

$isSubmitted = !empty($existingNeed);
$year = function_exists('jdate') ? jdate('Y') : date('Y');
?>

<style>
    .training-card-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        padding: 1.5rem;
        border-radius: 20px 20px 0 0;
    }

    .form-control-training {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding: 0.9rem 1.1rem;
        font-size: 0.9rem;
        transition: all 0.25s ease;
        background-color: #ffffff;
    }

    .form-control-training:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        background-color: #ffffff;
    }

    .info-callout-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem;
    }

    .btn-submit-training {
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

    .btn-submit-training:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
    }

    .submitted-details-box {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 1.25rem;
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">نیازسنجی و ارتقای مهارت‌های فردی</h1>
        <p class="text-muted small mb-0">شناسایی نقاط قوت، زمینه‌های قابل بهبود و برنامه‌ریزی دوره‌های آموزشی سالانه پرسنل</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-2 fw-bold font-monospace" style="background:#e0e7ff; color:#4338ca;">
            <i class="fas fa-graduation-cap me-1"></i>
            سال مالی: <?php echo $year; ?>
        </span>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- ستون اصلی فرم یا نمایش اطلاعات ثبت شده -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
            <div class="training-card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-white bg-opacity-10 text-warning" style="width:44px; height:44px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-brain fs-4"></i>
                    </div>
                    <div>
                        <h6 class="m-0 fw-bold text-white fs-6">فرم نیازسنجی آموزشی سال <?php echo $year; ?></h6>
                        <span class="small text-white-50">اطلاعات وارد شده جهت بهبود سطح کیفی و تخصصی شما پردازش می‌شود</span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <?php if ($isSubmitted): ?>
                    <?php
                        $status = $existingNeed->status ?? 'pending';
                        $statusBadge = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                        $statusText = 'در انتظار بررسی واحد آموزش';

                        if ($status === 'approved' || $status === 'confirmed') {
                            $statusBadge = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                            $statusText = 'تایید شده و در حال برنامه‌ریزی';
                        } elseif ($status === 'rejected') {
                            $statusBadge = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                            $statusText = 'رد شده';
                        }
                    ?>

                    <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background:#d1fae5; color:#065f46;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-circle-check fs-2 text-emerald-600" style="color:#059669;"></i>
                            <div>
                                <h6 class="fw-bold mb-1">نیازسنجی شما با موفقیت ثبت شده است.</h6>
                                <p class="small mb-0 opacity-80">درخواست شما در کارتابل مسئول آموزش سازمان قرار گرفته است.</p>
                            </div>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 fw-bold <?php echo $statusBadge; ?>">
                            <?php echo $statusText; ?>
                        </span>
                    </div>

                    <!-- نمایش پاسخ‌های ثبت‌شده قبلی -->
                    <div class="d-flex flex-column gap-3">
                        <div class="submitted-details-box">
                            <strong class="d-block text-dark small fw-bold mb-2">
                                <i class="fas fa-shield-halved text-indigo-600 me-1" style="color:#6366f1;"></i>
                                نقاط قوت و مهارت‌های برجسته ثبت‌شده شما:
                            </strong>
                            <p class="text-secondary small mb-0 lh-lg">
                                <?php echo nl2br(sanitize($existingNeed->strengths ?? '---')); ?>
                            </p>
                        </div>

                        <div class="submitted-details-box">
                            <strong class="d-block text-dark small fw-bold mb-2">
                                <i class="fas fa-chart-line text-warning me-1"></i>
                                زمینه‌ها و نقاط نیازمند ارتقا و آموزش:
                            </strong>
                            <p class="text-secondary small mb-0 lh-lg">
                                <?php echo nl2br(sanitize($existingNeed->weaknesses ?? '---')); ?>
                            </p>
                        </div>
                    </div>

                <?php else: ?>
                    <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_training_need" method="POST">
                        
                        <div class="mb-4">
                            <label for="strengths" class="form-label fw-bold text-dark small">
                                <i class="fas fa-award text-indigo-600 me-1" style="color:#6366f1;"></i>
                                نقاط قوت و مهارتی فعلی خود را شرح دهید: <span class="text-danger">*</span>
                            </label>
                            <textarea name="strengths" id="strengths" rows="4" class="form-control form-control-training" placeholder="دانش فنی، ابزارها و زمینه‌هایی که در آن‌ها تسلط کامل دارید..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="weaknesses" class="form-label fw-bold text-dark small">
                                <i class="fas fa-arrow-trend-up text-warning me-1"></i>
                                نقاط قابل بهبود و دوره‌های آموزشی مورد نیاز: <span class="text-danger">*</span>
                            </label>
                            <textarea name="weaknesses" id="weaknesses" rows="4" class="form-control form-control-training" placeholder="مهارت‌ها، نرم‌افزارها یا مباحثی که جهت بهبود عملکرد شغلی نیاز به گذراندن دوره آموزشی دارید..." required></textarea>
                        </div>

                        <hr class="my-4 text-muted border-dashed">

                        <div class="d-flex align-items-center justify-content-end">
                            <button type="submit" class="btn-submit-training d-inline-flex align-items-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                <span>ثبت و ارسال نیازسنجی</span>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ستون راهنما -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 bg-white mb-4">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-lightbulb text-warning fs-5"></i>
                <span>راهنمای تکمیل نیازسنجی</span>
            </h6>

            <div class="info-callout-box mb-3">
                <strong class="d-block text-dark small fw-bold mb-1"><i class="fas fa-check text-success me-1"></i> شفافیت در شرح نیازها</strong>
                <p class="small text-muted mb-0">عناوین دقیق دوره‌ها یا ابزارهایی که در کار روزمره با آن‌ها چالش دارید را ذکر کنید.</p>
            </div>

            <div class="info-callout-box mb-3">
                <strong class="d-block text-dark small fw-bold mb-1"><i class="fas fa-check text-success me-1"></i> تاثیر در ارزیابی سالانه</strong>
                <p class="small text-muted mb-0">نتایج این نیازسنجی مستقیم در تدوین تقویم آموزشی شرکت و بودجه‌بندی دوره پرسنل موثر است.</p>
            </div>

            <div class="info-callout-box">
                <strong class="d-block text-dark small fw-bold mb-1"><i class="fas fa-lock text-indigo-600 me-1" style="color:#6366f1;"></i> محرمانه بودن پاسخ‌ها</strong>
                <p class="small text-muted mb-0">اطلاعات این فرم صرفاً توسط مدیر منابع انسانی و مدیر مستقیم بررسی می‌گردد.</p>
            </div>
        </div>
    </div>
</div>