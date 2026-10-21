<?php 
//require APPROOT . '/views/layouts/admin_layout.php'; 
?>

<style>
    /* انتخابگر ربات‌های مجاز */
    .bot-toggle-card {
        cursor: pointer;
        padding: 14px 18px;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .bot-toggle-card:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .bot-checkbox:checked + .bot-toggle-card {
        background: #ecfdf5;
        border-color: #10b981;
        color: #065f46;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }

    .bot-checkbox:checked + .bot-toggle-card i.check-icon {
        color: #10b981;
        opacity: 1;
    }

    .check-icon {
        opacity: 0;
        transition: all 0.2s ease;
    }

    /* انتخابگر رنگ */
    .color-picker-item {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        cursor: pointer;
        border: 3px solid #ffffff;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        position: relative;
    }

    .color-radio:checked + .color-picker-item {
        transform: scale(1.2);
        box-shadow: 0 0 0 3px #6366f1;
    }

    .form-control-ultra {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding: 0.85rem 1.1rem;
        font-size: 0.95rem;
        transition: all 0.25s ease;
        background-color: #ffffff;
    }

    .form-control-ultra:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .btn-save-plan {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 50px;
        padding: 0.95rem 3rem;
        font-weight: 800;
        font-size: 1rem;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-save-plan:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
    }
</style>

<div class="row justify-content-center">
    <div class="col-xl-9 col-lg-11">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
            
            <div class="card-header bg-white border-bottom border-light py-4 px-4 px-md-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="fw-extrabold text-dark m-0 d-flex align-items-center gap-2">
                        <i class="fas fa-layer-group text-indigo-600" style="color:#6366f1;"></i>
                        <span>تعریف پلن فروش جدید</span>
                    </h5>
                    <p class="text-muted small mb-0 mt-1">مشخصات بسته اشتراکی جدید را جهت عرضه به مشتریان وارد کنید</p>
                </div>
                <a href="index.php?page=admin&action=plans" class="btn btn-outline-secondary rounded-pill btn-sm px-4 fw-bold d-inline-flex align-items-center gap-2">
                    <i class="fas fa-arrow-right"></i>
                    <span>بازگشت</span>
                </a>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="index.php?page=admin&action=store_plan" method="POST">
                    
                    <div class="row g-4 mb-5">
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-dark small">عنوان پلن: <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-ultra" placeholder="مثال: پلن طلایی (سه ماهه)" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-dark small">قیمت (تومان): <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="price" class="form-control form-control-ultra font-monospace text-center fs-5 fw-bold" placeholder="0" onkeyup="this.value=Number(this.value.replaceAll(',','')).toLocaleString()" required>
                                <span class="input-group-text bg-light border text-muted small fw-bold">تومان</span>
                            </div>
                        </div>
                    </div>

                    <!-- بخش تنظیمات محدودیت -->
                    <div class="p-4 bg-light rounded-4 mb-5 border border-dashed">
                        <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-sliders text-indigo-600" style="color:#6366f1;"></i>
                            <span>تنظیمات محدودیت و اعتبار بسته</span>
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold mb-1">مدت اعتبار (روز):</label>
                                <input type="number" name="days" class="form-control form-control-ultra font-monospace text-center fw-bold" value="30" required>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold mb-1">تعداد ربات / کاربر مجاز:</label>
                                <input type="number" name="max_bots" class="form-control form-control-ultra font-monospace text-center fw-bold" value="1" required>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold mb-1">سقف ذخیره رکورد داده:</label>
                                <input type="number" name="max_records" class="form-control form-control-ultra font-monospace text-center fw-bold" value="5000" required>
                            </div>
                        </div>
                    </div>

                    <!-- بخش ربات‌ها -->
                    <div class="mb-5">
                        <label class="form-label fw-bold text-dark small mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-robot text-indigo-600" style="color:#6366f1;"></i>
                            <span>ربات‌های مجاز در این پلن:</span>
                        </label>
                        <div class="row g-3">
                            <?php foreach($data['bots'] as $bot): ?>
                            <div class="col-md-4 col-sm-6">
                                <input type="checkbox" name="bots[]" value="<?php echo $bot->id; ?>" id="bot_<?php echo $bot->id; ?>" class="d-none bot-checkbox" checked>
                                <label class="bot-toggle-card" for="bot_<?php echo $bot->id; ?>">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="<?php echo $bot->icon; ?> text-muted me-1 fs-5"></i>
                                        <span class="fw-bold small text-dark"><?php echo sanitize($bot->name); ?></span>
                                    </div>
                                    <i class="fas fa-circle-check check-icon text-emerald-600 fs-5" style="color:#059669;"></i>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- تنظیمات تم رنگی و پیشنهاد ویژه -->
                    <div class="row g-4 align-items-center bg-light p-4 rounded-4 mb-4 border">
                        <div class="col-md-6 border-end border-light">
                            <label class="form-label fw-bold text-dark small d-block mb-3">تم رنگی کارت در وب‌سایت:</label>
                            <div class="d-flex gap-3">
                                <label>
                                    <input type="radio" name="color_theme" value="primary" class="d-none color-radio" checked>
                                    <div class="color-picker-item" style="background: #6366f1;" title="بنفش / نیلی"></div>
                                </label>
                                <label>
                                    <input type="radio" name="color_theme" value="success" class="d-none color-radio">
                                    <div class="color-picker-item" style="background: #10b981;" title="زمردی / سبز"></div>
                                </label>
                                <label>
                                    <input type="radio" name="color_theme" value="warning" class="d-none color-radio">
                                    <div class="color-picker-item" style="background: #f59e0b;" title="طلایی / نارنجی"></div>
                                </label>
                                <label>
                                    <input type="radio" name="color_theme" value="dark" class="d-none color-radio">
                                    <div class="color-picker-item" style="background: #1e293b;" title="سرمه‌ای / تاریک"></div>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 ps-md-4">
                            <div class="form-check form-switch p-0 d-flex align-items-center justify-content-between">
                                <label class="form-check-label fw-bold text-dark small" for="featSwitch">نمایش به عنوان پیشنهاد ویژه (Featured)</label>
                                <input class="form-check-input" type="checkbox" name="is_featured" id="featSwitch" style="width: 3.2em; height: 1.6em; cursor: pointer;">
                            </div>
                        </div>
                        
                        <div class="col-12 mt-4 pt-3 border-top">
                            <label class="form-label text-dark small fw-bold">توضیحات کوتاه زیر کارت:</label>
                            <input type="text" name="description" class="form-control form-control-ultra" placeholder="مثلاً: مناسب برای کسب‌وکارهای نوپا و تیم‌های کوچک">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end border-top pt-4 mt-5">
                        <button type="submit" class="btn-save-plan d-inline-flex align-items-center gap-2">
                            <i class="fas fa-floppy-disk fs-5"></i>
                            <span>ذخیره و ایجاد پلن</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>