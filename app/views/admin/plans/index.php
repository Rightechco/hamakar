<?php 
//require APPROOT . '/views/layouts/admin_layout.php';
?>

<style>
    .plan-item-card-ultra {
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        background: #ffffff;
        overflow: hidden;
        position: relative;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
    }

    .plan-item-card-ultra:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 45px -10px rgba(99, 102, 241, 0.15);
        border-color: #c7d2fe;
    }

    .plan-head-ultra {
        padding: 32px 20px 24px;
        color: #ffffff;
        position: relative;
        text-align: center;
    }

    .plan-head-ultra::after {
        content: "";
        position: absolute;
        bottom: -18px;
        left: 0;
        right: 0;
        height: 36px;
        background: #ffffff;
        border-radius: 50% 50% 0 0;
    }

    .price-display-num {
        font-size: 2.1rem;
        font-weight: 900;
        color: #0f172a;
        font-family: monospace, 'Vazirmatn', sans-serif;
    }

    .feature-list-ultra li {
        margin-bottom: 12px;
        color: #334155;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }

    .feature-list-ultra i {
        color: #10b981;
        font-size: 0.95rem;
    }

    /* تم‌های رنگی شیب‌دار اولترا */
    .theme-primary { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); }
    .theme-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .theme-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .theme-dark { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }

    .featured-badge-glass {
        position: absolute;
        top: 14px;
        left: 14px;
        background: rgba(255, 255, 255, 0.22);
        backdrop-filter: blur(8px);
        color: #ffffff;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.725rem;
        font-weight: 800;
        border: 1px solid rgba(255, 255, 255, 0.35);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .add-new-card-ultra {
        border: 2px dashed #cbd5e1;
        background: #f8fafc;
        color: #64748b;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 380px;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 24px;
        text-align: center;
        padding: 2rem;
    }

    .add-new-card-ultra:hover {
        border-color: #6366f1;
        color: #4338ca;
        background: #f5f3ff;
        transform: translateY(-5px);
        box-shadow: 0 20px 35px -10px rgba(99, 102, 241, 0.12);
    }

    .btn-action-plan {
        border-radius: 12px;
        padding: 0.5rem 1rem;
        font-size: 0.825rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }
</style>

<!-- هدر اصلی صفحه -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">مدیریت پلن‌های فروش</h3>
        <p class="text-muted small mb-0">تعریف بسته‌های اشتراکی، قیمت‌گذاری و تعیین سطوح دسترسی</p>
    </div>

    <a href="index.php?page=admin&action=create_plan" class="btn btn-primary rounded-pill px-4 py-2.5 shadow-sm fw-bold d-inline-flex align-items-center gap-2">
        <i class="fas fa-plus"></i>
        <span>افزودن پلن جدید</span>
    </a>
</div>

<!-- گرید پلن‌ها -->
<div class="row g-4 mb-4">
    
    <?php foreach($data['plans'] as $plan): 
        $themeClass = 'theme-' . ($plan->color_theme ?? 'primary');
    ?>
    <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="plan-item-card-ultra">
            <div class="plan-head-ultra <?php echo $themeClass; ?>">
                <?php if($plan->is_featured): ?>
                    <div class="featured-badge-glass"><i class="fas fa-star text-warning me-1"></i> پیشنهاد ویژه</div>
                <?php endif; ?>
                <h5 class="fw-extrabold mb-1 text-white"><?php echo sanitize($plan->title); ?></h5>
                <span class="opacity-80 small font-monospace"><i class="far fa-clock me-1"></i> <?php echo $plan->days; ?> روز اعتبار</span>
            </div>
            
            <div class="card-body text-center pt-3 d-flex flex-column justify-content-between p-4">
                <div class="mb-4">
                    <span class="price-display-num"><?php echo number_format($plan->price); ?></span>
                    <span class="small text-muted fw-bold ms-1">تومان</span>
                </div>
                
                <ul class="list-unstyled feature-list-ultra text-start mb-4 flex-grow-1">
                    <li><i class="fas fa-robot text-indigo-600" style="color:#6366f1;"></i> دسترسی به <strong><?php echo $plan->max_bots; ?></strong> ربات / کاربر</li>
                    <li><i class="fas fa-database text-emerald-600" style="color:#059669;"></i> سقف <strong><?php echo number_format($plan->max_records); ?></strong> رکورد داده</li>
                    <?php if($plan->description): ?>
                        <li class="text-muted small mt-3 pt-2 border-top fw-normal"><i class="fas fa-circle-info text-secondary"></i> <?php echo sanitize($plan->description); ?></li>
                    <?php endif; ?>
                </ul>
                
                <div class="d-flex gap-2">
                    <a href="index.php?page=admin&action=edit_plan&id=<?php echo $plan->id; ?>" class="btn btn-light btn-action-plan w-100 border text-dark fw-bold">
                        <i class="fas fa-pen me-1 text-indigo-600" style="color:#6366f1;"></i> ویرایش
                    </a>
                    <a href="index.php?page=admin&action=delete_plan&id=<?php echo $plan->id; ?>" 
                       class="btn btn-outline-danger btn-action-plan w-100 fw-bold"
                       onclick="return confirm('آیا از حذف این پلن اطمینان دارید؟');">
                        <i class="fas fa-trash me-1"></i> حذف
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- کارت تعریف جدید -->
    <div class="col-xl-3 col-lg-4 col-md-6">
        <a href="index.php?page=admin&action=create_plan" class="text-decoration-none">
            <div class="add-new-card-ultra">
                <div class="rounded-circle p-3 bg-indigo-50 text-indigo-600 mb-3" style="background:#e0e7ff; color:#4338ca; width:64px; height:64px; display:flex; align-items:center; justify-content:center;">
                    <i class="fas fa-plus fs-2"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1 fs-5">تعریف پلن جدید</h6>
                <p class="text-muted small mb-0">ایجاد بسته فروش جدید با مشخصات سفارشی</p>
            </div>
        </a>
    </div>

</div>