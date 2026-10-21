<?php
// app/views/client/ai_settings.php - نسخه اولترا پرمیوم
$profile = $profile ?? null;
?>

<style>
    .ai-card-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        padding: 1.5rem;
        border-radius: 20px 20px 0 0;
    }

    .form-control-ai {
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        padding: 0.85rem 1.1rem;
        font-size: 0.9rem;
        transition: all 0.25s ease;
        background-color: #ffffff;
    }

    .form-control-ai:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        background-color: #ffffff;
    }

    .ai-info-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1.25rem;
    }

    .btn-save-ai {
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

    .btn-save-ai:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">پروفایل و منشی هوشمند (AI)</h1>
        <p class="text-muted small mb-0">تنظیم دانش‌پایه و پایگاه داده اختصاصی ربات برای پاسخگویی خودکار به مشتریان شما</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5" style="background:#e0e7ff; color:#4338ca;">
            <i class="fas fa-brain"></i>
            هوش مصنوعی فعال
        </span>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- ستون فرم تنظیمات -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
            <div class="ai-card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-white bg-opacity-10 text-warning" style="width:44px; height:44px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-robot fs-4"></i>
                    </div>
                    <div>
                        <h6 class="m-0 fw-bold text-white fs-6">تنظیمات دانش‌پایه ربات پاسخگو</h6>
                        <span class="small text-white-50">اطلاعات وارد شده در این بخش مستقیماً پردازش و یادگیری می‌شوند</span>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <form action="<?php echo APP_URL; ?>/index.php?page=client&action=storeAiProfile" method="POST">
                    
                    <div class="row g-4">
                        <!-- نام کسب‌وکار -->
                        <div class="col-12">
                            <label for="business_name" class="form-label fw-bold text-dark small">نام تجاری / برند کسب‌وکار: <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted"><i class="fas fa-store"></i></span>
                                <input type="text" class="form-control form-control-ai border-start-0 rounded-end-3" id="business_name" name="business_name" value="<?php echo sanitize($profile->business_name ?? ''); ?>" placeholder="مثال: مجموعه بازرگانی و تجاری هماکار" required>
                            </div>
                        </div>

                        <!-- شرح فعالیت شرکت -->
                        <div class="col-12">
                            <label for="description" class="form-label fw-bold text-dark small">توضیحات کلی و حوزه فعالیت کسب‌وکار:</label>
                            <textarea class="form-control form-control-ai" id="description" name="description" rows="4" placeholder="شرح کوتاهی از خدمات، ساعات کاری، نحوه ارائه سرویس و معرفی مجموعه بنویسید..."><?php echo sanitize($profile->description ?? ''); ?></textarea>
                            <div class="form-text small text-muted"><i class="fas fa-circle-info me-1"></i> این بخش برای لحن‌سازی و معرفی اولیه ربات به مخاطبان استفاده می‌شود.</div>
                        </div>

                        <!-- لیست محصولات و خدمات -->
                        <div class="col-12">
                            <label for="products" class="form-label fw-bold text-dark small">فهرست محصولات، کالاها یا خدمات اصلی به همراه قیمت/شرایط:</label>
                            <textarea class="form-control form-control-ai font-monospace" id="products" name="products" rows="5" placeholder="مثال:&#10;۱. پلن پایه - ۱۰۰ هزار تومان&#10;۲. لایسنس اختصاصی - ۵۰۰ هزار تومان"><?php echo sanitize($profile->products_list ?? ''); ?></textarea>
                        </div>

                        <!-- سوالات متداول (FAQ) -->
                        <div class="col-12">
                            <label for="faq" class="form-label fw-bold text-dark small">سوالات متداول مشتریان و پاسخ‌های آماده (FAQ):</label>
                            <textarea class="form-control form-control-ai" id="faq" name="faq" rows="5" placeholder="سوال: آدرس دقیق شرکت کجاست؟&#10;پاسخ: تبریز، بلوار آزادی..."><?php echo sanitize($profile->faq_list ?? ''); ?></textarea>
                        </div>
                    </div>

                    <hr class="my-4 text-muted border-dashed">

                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <button type="submit" class="btn-save-ai d-inline-flex align-items-center gap-2">
                            <i class="fas fa-floppy-disk"></i>
                            <span>ذخیره و به‌روزرسانی منشی هوشمند</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ستون راهنما و نکات یادگیری هوش مصنوعی -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 mb-4 bg-white">
            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-lightbulb text-warning fs-5"></i>
                <span>نکات طلایی آموزش به هوش مصنوعی</span>
            </h6>
            
            <div class="ai-info-box mb-3">
                <strong class="d-block text-dark small fw-bold mb-1"><i class="fas fa-check text-success me-1"></i> متن شفاف و ساختاریافته</strong>
                <p class="small text-muted mb-0">هرچه اطلاعات محصولات و سوالات متداول را منظم‌تر بنویسید، پاسخ‌های منشی هوشمند دقیق‌تر و بدون خطای احتمالی خواهد بود.</p>
            </div>

            <div class="ai-info-box mb-3">
                <strong class="d-block text-dark small fw-bold mb-1"><i class="fas fa-check text-success me-1"></i> به‌روزرسانی قیمت‌ها</strong>
                <p class="small text-muted mb-0">در صورت تغییر قیمت‌ها یا آدرس، حتماً اطلاعات این صفحه را ویرایش نمایید تا ربات بر اساس قیمت جدید پاسخ دهد.</p>
            </div>

            <div class="ai-info-box">
                <strong class="d-block text-dark small fw-bold mb-1"><i class="fas fa-shield-halved text-indigo-600 me-1" style="color:#6366f1;"></i> امنیت داده‌ها</strong>
                <p class="small text-muted mb-0">اطلاعات این بخش کاملاً محرمانه بوده و فقط پردازش متنی پاسخگویی به ربات شما را تغذیه می‌کند.</p>
            </div>
        </div>
    </div>
</div>