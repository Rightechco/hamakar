<?php
// app/views/client/renew_subscription.php - نسخه یولترا پرمیوم و سازگار با سیستم لایوت
$plans = $plans ?? ($data['plans'] ?? []);
$license = $license ?? ($data['license'] ?? null);
?>

<style>
    .renew-hero-box {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 24px;
        color: #ffffff;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.25);
    }

    .plan-card-premium {
        background: #ffffff;
        border-radius: 22px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04);
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .plan-card-premium:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.15);
        border-color: #6366f1;
    }

    .plan-header-box {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #ffffff;
        padding: 1.5rem;
        text-align: center;
        border-bottom: none;
    }

    .btn-checkout-plan {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 50px;
        padding: 0.75rem 2rem;
        font-weight: 800;
        font-size: 0.95rem;
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
        transition: all 0.25s ease;
        text-decoration: none !important;
        display: block;
        text-align: center;
    }

    .btn-checkout-plan:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(99, 102, 241, 0.4);
        color: #ffffff !important;
    }
</style>

<div class="container py-4">
    <!-- هدر هشدار انقضای اشتراک -->
    <div class="renew-hero-box text-center mb-5">
        <div class="d-inline-flex align-items-center gap-2 bg-danger bg-opacity-25 border border-danger border-opacity-50 text-danger rounded-pill px-4 py-2 mb-3 fw-bold small" style="background: rgba(239, 68, 68, 0.15) !important; color: #fca5a5 !important;">
            <i class="fas fa-triangle-exclamation"></i>
            <span>اعتبار اشتراک شما به پایان رسیده است</span>
        </div>
        <h2 class="fw-bold mb-2">تمدید فوری اشتراک سامانه و ربات هوشمند</h2>
        <p class="text-white-50 small mb-0" style="max-width: 600px; margin: 0 auto;">
            جهت استمرار دسترسی بدون وقفه به امکانات پیشرفته ربات، پنل مدیریت و ابزارهای اتوماسیون، لطفاً یکی از پلن‌های اشتراک زیر را انتخاب نمایید.
        </p>
    </div>

    <!-- لیست پلن‌های اشتراک -->
    <div class="row justify-content-center g-4">
        <?php if (!empty($plans)): ?>
            <?php foreach($plans as $plan): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="plan-card-premium">
                    <div class="plan-header-box">
                        <h4 class="mb-0 fw-bold fs-5"><?php echo htmlspecialchars($plan->title ?? 'پلن اشتراک'); ?></h4>
                    </div>
                    
                    <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                        <div>
                            <div class="my-3">
                                <span class="fs-2 fw-extrabold text-dark font-monospace"><?php echo number_format($plan->price ?? 0); ?></span> 
                                <span class="text-muted small fw-bold">تومان</span>
                            </div>

                            <ul class="list-unstyled text-start px-2 mt-4 mb-4 small text-secondary">
                                <li class="mb-3 d-flex align-items-center gap-2">
                                    <i class="fas fa-circle-check text-success"></i>
                                    <span><strong><?php echo htmlspecialchars($plan->days ?? 30); ?> روز</strong> اعتبار و دسترسی مداوم</span>
                                </li>
                                <li class="mb-3 d-flex align-items-center gap-2">
                                    <i class="fas fa-circle-check text-success"></i>
                                    <span>دسترسی کامل به ربات‌ها و اتوماسیون</span>
                                </li>
                                <li class="mb-3 d-flex align-items-center gap-2">
                                    <i class="fas fa-circle-check text-success"></i>
                                    <span>پشتیبانی اختصاصی فنی و مالی</span>
                                </li>
                            </ul>
                        </div>

                        <div>
                            <a href="index.php?page=payment&action=checkout&id=<?php echo $plan->id; ?>" class="btn-checkout-plan">
                                <i class="fas fa-bolt me-1"></i> تمدید و فعال‌سازی آنی
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="alert alert-warning rounded-4 shadow-sm border-0 p-4">
                    <i class="fas fa-info-circle fs-3 mb-2 d-block"></i>
                    <span>در حال حاضر هیچ پلن فعالی برای تمدید تعریف نشده است. لطفاً با بخش پشتیبانی تماس بگیرید.</span>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>