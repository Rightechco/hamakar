<style>
    /* Premium Close Fiscal Year Styles */
    .fin-card-danger {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #fecdd3;
        box-shadow: 0 10px 25px -5px rgba(225, 29, 72, 0.08);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    
    .danger-header-banner {
        background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
        padding: 1.5rem;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .danger-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .warning-info-box {
        background: #fff1f2;
        border: 1px solid #ffe4e6;
        border-radius: 16px;
        padding: 1.25rem;
        color: #9f1239;
        font-size: 0.875rem;
        line-height: 1.7;
    }

    .btn-danger-premium {
        background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.75rem;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 4px 15px rgba(225, 29, 72, 0.3);
        transition: all 0.25s ease;
    }
    .btn-danger-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(225, 29, 72, 0.4);
        color: #ffffff;
    }

    .form-control-premium {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.7rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    .form-control-premium:focus {
        border-color: #e11d48;
        box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.15);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">بستن سال مالی</h1>
        <p class="text-muted small mb-0">انتقال مانده حساب‌های موقت و بستن دفاتر دوره مالی</p>
    </div>
</div>

<div class="fin-card-danger max-w-3xl">
    <!-- Banner Header -->
    <div class="danger-header-banner">
        <div class="danger-icon-box">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-1">هشدار بسیار مهم: عملیات حساس و غیرقابل بازگشت</h5>
            <p class="small text-white-50 mb-0">لطفاً پیش از اجرای این بخش، دستورالعمل را با دقت مطالعه فرمایید.</p>
        </div>
    </div>

    <!-- Body Content -->
    <div class="card-body p-4 p-md-5">
        <div class="warning-info-box mb-4">
            <div class="d-flex items-center gap-2 font-bold mb-2">
                <i class="fas fa-info-circle"></i>
                <span>توضیحات مکانیزم بستن حساب‌ها:</span>
            </div>
            <p class="mb-2">
                عملیات بستن سال مالی، تمام حساب‌های **درآمد و هزینه** را صفر کرده و سود یا زیان خالص دوره را محاسبه و به حساب **سود/زیان انباشته** منتقل می‌کند.
            </p>
            <p class="mb-0 fw-bold text-rose-800">
                <i class="fas fa-shield-alt me-1"></i> الزامی: حتماً پیش از اقدام، از پایگاه داده و اطلاعات حسابداری خود نسخه پشتیبان (Backup) تهیه کنید.
            </p>
        </div>

        <form action="" method="POST" onsubmit="return confirm('آیا از انجام عملیات بستن سال مالی اطمینان کامل دارید؟ این عمل قابل بازگشت نیست.');">
            <div class="mb-4">
                <label for="end_date" class="form-label fw-bold text-secondary small">تاریخ پایان سال مالی جهت بستن حساب‌ها:</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                    <input type="text" id="end_date" name="end_date" class="form-control form-control-premium border-start-0 rounded-end-3 persian-datepicker" placeholder="مثال: 1404/12/29" autocomplete="off" required>
                </div>
            </div>

            <hr class="my-4 border-slate-200">

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <button type="submit" class="btn btn-danger-premium d-inline-flex align-items-center gap-2">
                    <i class="fas fa-lock fs-6"></i>
                    <span>اجرای عملیات بستن سال مالی</span>
                </button>
                
                <a href="index.php?page=admin&action=dashboard" class="text-decoration-none text-slate-500 hover-text-slate-800 small font-bold">
                    انصراف و بازگشت به داشبورد
                </a>
            </div>
        </form>
    </div>
</div>

<!-- اسکریپت تضمینی لودر تقویم شمسی -->
<script>
(function loadPersianDatePicker() {
    function initPicker() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.pDatepicker) {
            $('.persian-datepicker').pDatepicker({
                format: 'YYYY/MM/DD',
                autoClose: true,
                initialValue: false,
                observer: true,
                initialValueType: 'persian'
            });
        } else { setTimeout(initPicker, 100); }
    }
    if (!document.getElementById('p-datepicker-css')) {
        var css = document.createElement('link'); css.id = 'p-datepicker-css'; css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css';
        document.head.appendChild(css);
    }
    if (typeof $ === 'undefined') {
        var jq = document.createElement('script'); jq.src = 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'; jq.onload = loadPDate; document.head.appendChild(jq);
    } else { loadPDate(); }
    function loadPDate() {
        if (typeof window.persianDate === 'undefined') {
            var pd = document.createElement('script'); pd.src = 'https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js'; pd.onload = loadPDatePicker; document.head.appendChild(pd);
        } else { loadPDatePicker(); }
    }
    function loadPDatePicker() {
        if (typeof $.fn.pDatepicker === 'undefined') {
            var pdp = document.createElement('script'); pdp.src = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js'; pdp.onload = initPicker; document.head.appendChild(pdp);
        } else { initPicker(); }
    }
})();
</script>