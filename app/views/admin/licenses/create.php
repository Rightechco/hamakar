<?php 
//require APPROOT . '/views/layouts/admin_layout.php'; 
?>

<style>
    /* کارت‌های اولترا پرمیوم انتخاب پلن */
    .plan-select-card {
        cursor: pointer;
        position: relative;
        height: 100%;
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 20px;
        padding: 24px 20px;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .plan-select-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.12);
        border-color: #c7d2fe;
    }

    .plan-radio:checked + .plan-select-card {
        border-color: #6366f1;
        background: linear-gradient(180deg, #ffffff 0%, #f5f3ff 100%);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .check-indicator-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 28px;
        height: 28px;
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: scale(0);
        opacity: 0;
        transition: all 0.25s ease;
        font-size: 12px;
    }

    .plan-radio:checked + .plan-select-card .check-indicator-badge {
        transform: scale(1);
        opacity: 1;
    }

    .plan-radio:checked + .plan-select-card .plan-title-text {
        color: #4338ca;
    }

    .plan-price-display {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        margin: 12px 0;
        font-family: monospace, 'Vazirmatn', sans-serif;
    }

    .feature-tag-item {
        font-size: 0.75rem;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 4px 10px;
        border-radius: 8px;
        color: #475569;
        font-weight: 600;
    }

    .form-select-premium {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding: 0.95rem 1.1rem;
        font-size: 0.9rem;
        transition: all 0.25s ease;
        background-color: #f8fafc;
    }

    .form-select-premium:focus {
        border-color: #6366f1;
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .btn-submit-license {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 12px;
        padding: 0.85rem 2.5rem;
        font-weight: 800;
        font-size: 0.95rem;
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-submit-license:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
    }
</style>

<div class="row justify-content-center">
    <div class="col-xl-10 col-lg-12">
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            
            <!-- هدر کارت -->
            <div class="card-header bg-white border-bottom border-light py-4 px-4 px-md-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                        <i class="fas fa-certificate text-indigo-600" style="color:#6366f1;"></i>
                        <span>صدور لایسنس جدید</span>
                    </h4>
                    <p class="text-muted small mb-0">تخصیص طرح اشتراک اعتباری به کاربر</p>
                </div>

                <a href="index.php?page=admin&action=licenses" class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-bold d-inline-flex align-items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>بازگشت به لیست</span>
                </a>
            </div>

            <!-- بدنه فرم -->
            <div class="card-body p-4 p-md-5">
                <form action="index.php?page=admin&action=store_license" method="POST">
                    
                    <!-- ۱. انتخاب کاربر -->
                    <div class="mb-5">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2 pb-2 border-bottom">
                            <span class="badge bg-indigo-50 text-indigo-700 border rounded-circle p-2" style="background:#e0e7ff; color:#4338ca; width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center;">۱</span>
                            <span>کاربر مقصد را انتخاب کنید:</span>
                        </h6>

                        <div class="form-floating">
                            <select class="form-select form-select-premium fw-bold text-dark" name="user_id" id="userSelect" required>
                                <option value="" disabled selected>جستجو و انتخاب کاربر...</option>
                                <?php foreach($data['users'] as $user): ?>
                                    <option value="<?php echo $user->id; ?>"><?php echo sanitize($user->name) . ' (' . sanitize($user->email) . ')'; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="userSelect" class="text-muted small">لیست کاربران سیستم</label>
                        </div>
                    </div>

                    <!-- ۲. انتخاب پلن -->
                    <div class="mb-5">
                        <h6 class="fw-bold text-dark mb-4 d-flex align-items-center gap-2 pb-2 border-bottom">
                            <span class="badge bg-indigo-50 text-indigo-700 border rounded-circle p-2" style="background:#e0e7ff; color:#4338ca; width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center;">۲</span>
                            <span>طرح اشتراک را انتخاب کنید:</span>
                        </h6>
                        
                        <?php if(empty($data['plans'])): ?>
                            <div class="alert alert-warning rounded-4 p-4 text-center border-0 shadow-sm">
                                <i class="fas fa-exclamation-triangle fs-3 me-2"></i>
                                هنوز هیچ پلنی تعریف نشده است. لطفاً ابتدا از منوی "مدیریت پلن‌ها"، بسته‌ها را ایجاد کنید.
                            </div>
                        <?php else: ?>
                            <div class="row g-4">
                                <?php foreach($data['plans'] as $plan): ?>
                                <div class="col-xl-4 col-md-6">
                                    <input type="radio" class="btn-check plan-radio" name="plan_id" id="p_<?php echo $plan->id; ?>" value="<?php echo $plan->id; ?>" required>
                                    <label class="plan-select-card w-100" for="p_<?php echo $plan->id; ?>">
                                        <div class="check-indicator-badge"><i class="fas fa-check"></i></div>
                                        
                                        <div>
                                            <div class="d-flex align-items-center justify-content-center mb-2 gap-2">
                                                <div class="rounded-3 bg-light p-2 text-indigo-600 border" style="color:#6366f1;">
                                                    <i class="fas fa-box-open"></i>
                                                </div>
                                                <h6 class="fw-bold m-0 plan-title-text fs-6"><?php echo sanitize($plan->title); ?></h6>
                                            </div>

                                            <div class="plan-price-display">
                                                <?php echo number_format($plan->price); ?> <small class="fs-6 text-muted fw-normal">تومان</small>
                                            </div>
                                            
                                            <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold font-monospace mb-3" style="background:#e0e7ff; color:#4338ca;">
                                                <i class="far fa-clock me-1"></i> <?php echo $plan->days; ?> روز اعتبار
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-center flex-wrap gap-2 mt-3 pt-3 border-top">
                                            <span class="feature-tag-item"><i class="fas fa-robot text-indigo-600 me-1" style="color:#6366f1;"></i> <?php echo $plan->max_bots; ?> بات</span>
                                            <span class="feature-tag-item"><i class="fas fa-database text-emerald-600 me-1" style="color:#059669;"></i> <?php echo number_format($plan->max_records); ?> رکورد</span>
                                        </div>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- دکمه ثبت -->
                    <div class="d-flex justify-content-end mt-5 pt-4 border-top">
                        <button type="submit" class="btn-submit-license d-inline-flex align-items-center gap-2">
                            <i class="fas fa-circle-check fs-5"></i>
                            <span>تأیید نهایی و صدور لایسنس</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>