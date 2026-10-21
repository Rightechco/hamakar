<style>
    .fin-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .fin-card-header {
        padding: 1.25rem 1.5rem;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
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
    
    .btn-fin-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.75rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }
    .btn-fin-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35);
        color: #fff;
    }

    .btn-fin-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-fin-secondary:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Drag and Drop Zone Styles */
    .file-drop-area {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1.5rem;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        background-color: #f8fafc;
        transition: all 0.2s ease-in-out;
        cursor: pointer;
        text-align: center;
    }
    .file-drop-area:hover, .file-drop-area.is-active {
        background-color: #f0f3ff;
        border-color: #6366f1;
    }
    .file-drop-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: #e0e7ff;
        color: #4338ca;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
        transition: transform 0.2s ease;
    }
    .file-drop-area:hover .file-drop-icon {
        transform: scale(1.1);
    }
    .file-input {
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 100%;
        opacity: 0;
        cursor: pointer;
    }

    /* Excel Columns Preview */
    .column-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 0.825rem;
        font-weight: 700;
        color: #334155;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .column-number {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: #6366f1;
        color: #ffffff;
        font-size: 0.7rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    @media print {
        .no-print, #sidebar, .top-header-bar, #chat-toggler, #chat-window { display: none !important; }
        body { background: white !important; }
        .fin-card { border: 1px solid #ccc !important; box-shadow: none !important; }
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">ورود مشتریان از اکسل</h1>
        <p class="text-muted small mb-0">بارگذاری و وارد کردن دسته‌جمعی بانک اطلاعات کارفرمایان و مشتریان جدید</p>
    </div>
    <div class="d-flex align-items-center gap-2 no-print">
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn btn-fin-secondary d-inline-flex align-items-center gap-2">
            <i class="fas fa-arrow-right"></i>
            <span>بازگشت به لیست مشتریان</span>
        </a>
    </div>
</div>

<div class="fin-card max-w-4xl mx-auto">
    <div class="fin-card-header">
        <h6 class="fin-card-title">
            <i class="fas fa-file-excel text-emerald-600" style="color:#10b981;"></i>
            <span>آپلود و پردازش فایل اکسل مشتریان</span>
        </h6>
    </div>
    <div class="card-body p-4 p-md-5">
        
        <!-- راهنمای ساختار اکسل -->
        <div class="p-4 rounded-4 mb-4 border" style="background-color: #f0fdf4; border-color: #bbf7d0 !important;">
            <div class="d-flex align-items-center gap-2 mb-2 text-emerald-800" style="color: #166534;">
                <i class="fas fa-info-circle fs-5"></i>
                <span class="fw-bold">دستورالعمل ساختار ستون‌های فایل اکسل (.xlsx):</span>
            </div>
            <p class="small text-emerald-700 mb-3" style="color: #15803d;">
                فایل اکسل پردازشی شما باید <strong>دقیقاً به ترتیب ۵ ستون زیر</strong> تنظیم شده باشد. اطلاعات از همان <strong>ردیف اول</strong> پردازش می‌شوند[cite: 36]:
            </p>

            <div class="d-flex flex-wrap gap-2 mb-2">
                <div class="column-badge">
                    <span class="column-number">۱</span>
                    <span>نام مشتری</span>
                </div>
                <div class="column-badge">
                    <span class="column-number">۲</span>
                    <span>نام شرکت</span>
                </div>
                <div class="column-badge">
                    <span class="column-number">۳</span>
                    <span>ایمیل</span>
                </div>
                <div class="column-badge">
                    <span class="column-number">۴</span>
                    <span>تلفن تماس</span>
                </div>
                <div class="column-badge">
                    <span class="column-number">۵</span>
                    <span>آدرس</span>
                </div>
            </div>
        </div>

        <!-- فرم آپلود فایل -->
        <form action="index.php?page=admin&action=import_clients" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="excel_file" class="form-label fw-bold text-secondary small mb-2">انتخاب فایل اکسل (.xlsx):</label>
                
                <div class="file-drop-area" id="drop-area">
                    <div class="file-drop-icon">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </div>
                    <span class="fw-bold text-slate-800 mb-1" id="file-msg">فایل اکسل خود را اینجا بکشید یا برای انتخاب کلیک کنید</span>
                    <span class="small text-muted">فقط پسوند XLSX مورد قبول است</span>
                    <input class="file-input" type="file" name="excel_file" id="excel_file" accept=".xlsx" required>
                </div>
            </div>

            <hr class="my-4 border-slate-200">

            <div class="d-flex align-items-center justify-content-end gap-3">
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn btn-fin-secondary">انصراف</a>
                <button type="submit" class="btn btn-fin-primary d-inline-flex align-items-center gap-2">
                    <i class="fas fa-file-import"></i>
                    <span>شروع ورود اطلاعات</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('excel_file');
    const fileMsg = document.getElementById('file-msg');
    const dropArea = document.getElementById('drop-area');

    fileInput.addEventListener('change', function() {
        if (fileInput.files && fileInput.files[0]) {
            fileMsg.textContent = 'فایل انتخاب شده: ' + fileInput.files[0].name;
            dropArea.classList.add('is-active');
        }
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropArea.classList.add('is-active');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropArea.classList.remove('is-active');
        }, false);
    });
});
</script>