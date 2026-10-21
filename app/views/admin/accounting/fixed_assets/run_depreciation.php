<style>
    .fin-card { 
        background: #ffffff; 
        border-radius: 20px; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); 
        overflow: hidden; 
        margin-bottom: 1.5rem; 
    }
    .fin-card-header { 
        padding: 1.25rem 1.5rem; 
        background: #ffffff; 
        border-bottom: 1px solid #f1f5f9; 
    }
    .fin-card-title { 
        font-weight: 800; 
        color: #0f172a; 
        font-size: 1rem; 
        margin: 0; 
        display: flex; 
        align-items: center; 
        gap: 0.5rem; 
    }
    
    .btn-fin-success { 
        background: linear-gradient(135deg, #10b981 0%, #047857 100%); 
        color: #fff; 
        border: none; 
        border-radius: 12px; 
        padding: 0.75rem 1.75rem; 
        font-weight: 700; 
        transition: all 0.2s; 
    }
    .btn-fin-success:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 20px rgba(16,185,129,0.35); 
        color: #fff; 
    }

    .form-control-premium {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.7rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    .form-control-premium:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
    }

    .info-notice-box {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 16px;
        padding: 1.25rem;
        color: #166534;
        font-size: 0.875rem;
        line-height: 1.7;
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">محاسبه و ثبت سند استهلاک</h1>
        <p class="text-muted small mb-0">اجرای خودکار الگوریتم استهلاک و صدور اسناد بدهکار/بستانکار مربوطه</p>
    </div>
</div>

<div class="fin-card max-w-3xl">
    <div class="fin-card-header">
        <h6 class="fin-card-title">
            <i class="fas fa-calculator text-emerald-500" style="color:#10b981;"></i>
            <span>اجرای عملیات استهلاک دوره‌ای</span>
        </h6>
    </div>
    <div class="card-body p-4 p-md-5">
        <div class="info-notice-box mb-4">
            <div class="d-flex align-items-center gap-2 font-bold mb-2">
                <i class="fas fa-info-circle text-emerald-600 fs-5"></i>
                <span>راهنمای سیستم:</span>
            </div>
            <p class="mb-2">
                با اجرای این عملیات، سیستم برای تمامی دارایی‌های ثابت ثبت‌شده، میزان هزینه استهلاک را تا تاریخ مشخص شده محاسبه کرده و یک **سند حسابداری جامع** به‌صورت خودکار صادر می‌نماید.
            </p>
            <p class="mb-0 fw-bold text-amber-700" style="color: #b45309;">
                <i class="fas fa-exclamation-triangle me-1"></i> توصیه مالی: این عملیات ترجیحاً در پایان هر دوره مالی (مانند پایان هر ماه یا پایان سال) اجرا شود.
            </p>
        </div>

        <form action="index.php?page=admin&action=run_depreciation" method="POST" onsubmit="return confirm('آیا از محاسبه و ثبت سند استهلاک تا تاریخ انتخاب شده مطمئن هستید؟');">
            <div class="mb-4">
                <label for="depreciation_date" class="form-label fw-bold text-secondary small">محاسبه استهلاک تا تاریخ:</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-3 text-muted">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                    <input type="text" id="depreciation_date" name="depreciation_date" class="form-control form-control-premium border-start-0 rounded-end-3 persian-datepicker" placeholder="YYYY/MM/DD" autocomplete="off" required>
                </div>
            </div>

            <hr class="my-4 border-slate-200">

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <button type="submit" class="btn btn-fin-success d-inline-flex align-items-center gap-2">
                    <i class="fas fa-play-circle fs-6"></i>
                    <span>شروع محاسبه و صدور سند</span>
                </button>
                <a href="index.php?page=admin&action=fixed_assets" class="text-decoration-none text-slate-500 hover-text-slate-800 small font-bold">
                    بازگشت به لیست دارایی‌ها
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