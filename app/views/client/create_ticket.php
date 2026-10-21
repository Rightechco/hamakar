<?php
// app/views/client/create_ticket.php - نسخه اولترا پرمیوم
?>

<style>
    .ticket-card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .form-control-premium, .form-select-premium {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        background-color: #ffffff;
    }

    .form-control-premium:focus, .form-select-premium:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        background-color: #ffffff;
    }

    .btn-submit-ticket {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 12px;
        padding: 0.8rem 2rem;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);
        transition: all 0.25s ease;
    }

    .btn-submit-ticket:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(79, 70, 229, 0.35);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">ثبت تیکت پشتیبانی جدید</h1>
        <p class="text-muted small mb-0">ارسال درخواست، مشکلات فنی یا سوالات مالی جهت بررسی توسط تیم پشتیبانی هماکار</p>
    </div>

    <a href="<?php echo APP_URL; ?>/index.php?page=client&action=my_tickets" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
        <i class="fas fa-arrow-right"></i>
        <span>بازگشت به تیکت‌های من</span>
    </a>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="ticket-card-header d-flex align-items-center gap-2">
        <div class="rounded-3 bg-indigo-50 p-2 text-indigo-600 d-flex align-items-center justify-content-center" style="background:#e0e7ff; color:#4f46e5; width:38px; height:38px;">
            <i class="fas fa-headset fs-5"></i>
        </div>
        <h6 class="m-0 fw-bold text-dark">فرم درخواست پشتیبانی جدید</h6>
    </div>

    <div class="card-body p-4 p-md-5">
        <form action="<?php echo APP_URL; ?>/index.php?page=client&action=store_ticket" method="POST" enctype="multipart/form-data">
            
            <div class="row g-4 mb-4">
                <!-- موضوع تیکت -->
                <div class="col-12">
                    <label for="subject" class="form-label fw-bold text-secondary small">موضوع تیکت: <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted"><i class="fas fa-heading"></i></span>
                        <input type="text" class="form-control form-control-premium border-start-0 rounded-end-3" id="subject" name="subject" placeholder="مثال: سوال در خصوص صدور فاکتور رسمی و تمدید لایسنس" required>
                    </div>
                </div>

                <!-- دپارتمان مربوطه -->
                <div class="col-md-6">
                    <label for="department" class="form-label fw-bold text-secondary small">دپارتمان مربوطه: <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted"><i class="fas fa-sitemap"></i></span>
                        <select class="form-select form-select-premium border-start-0 rounded-end-3" id="department" name="department" required>
                            <option value="support" selected>پشتیبانی فنی و سامانه</option>
                            <option value="sales">بخش فروش و ارتقای پلن</option>
                            <option value="billing">امور مالی، فاکتورها و قراردادها</option>
                        </select>
                    </div>
                </div>

                <!-- اولویت -->
                <div class="col-md-6">
                    <label for="priority" class="form-label fw-bold text-secondary small">اولویت بررسی: <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted"><i class="fas fa-layer-group"></i></span>
                        <select class="form-select form-select-premium border-start-0 rounded-end-3" id="priority" name="priority" required>
                            <option value="low">کم (پاسخگویی عادی)</option>
                            <option value="medium" selected>متوسط (پاسخگویی سریع)</option>
                            <option value="high">زیاد (فوری و حیاتی)</option>
                        </select>
                    </div>
                </div>

                <!-- متن پیام -->
                <div class="col-12">
                    <label for="message" class="form-label fw-bold text-secondary small">شرح کامل درخواست یا مشکل: <span class="text-danger">*</span></label>
                    <textarea class="form-control form-control-premium" id="message" name="message" rows="6" placeholder="جزئیات درخواست خود را با شرح دقیق بنویسید..." required></textarea>
                </div>

                <!-- پیوست فایل (اختیاری) -->
                <div class="col-12">
                    <label for="attachment" class="form-label fw-bold text-secondary small">پیوست تصویر یا مستندات (اختیاری):</label>
                    <input type="file" class="form-control form-control-premium" id="attachment" name="attachment" accept="image/*,.pdf,.zip">
                    <div class="form-text small text-muted"><i class="fas fa-info-circle me-1"></i> فرمت‌های مجاز: JPG, PNG, PDF, ZIP (حداکثر ۵ مگابایت)</div>
                </div>
            </div>

            <hr class="my-4 text-muted border-dashed">

            <div class="d-flex align-items-center justify-content-end gap-2">
                <a href="<?php echo APP_URL; ?>/index.php?page=client&action=my_tickets" class="btn btn-light border rounded-3 px-4 fw-bold">انصراف</a>
                <button type="submit" class="btn btn-submit-ticket d-inline-flex align-items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>ثبت و ارسال تیکت</span>
                </button>
            </div>
        </form>
    </div>
</div>