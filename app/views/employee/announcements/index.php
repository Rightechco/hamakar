<?php
// app/views/employee/announcements/index.php - نسخه اولترا پرمیوم
$announcements = $announcements ?? [];
?>

<style>
    .announcement-card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .announcement-box-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1.35rem 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-right: 5px solid #6366f1;
        position: relative;
    }

    .announcement-box-item:hover {
        box-shadow: 0 12px 25px -5px rgba(99, 102, 241, 0.12);
        border-color: #cbd5e1;
        border-right-color: #4f46e5;
        transform: translateX(-4px);
    }

    .announcement-box-item:last-child {
        margin-bottom: 0;
    }

    .announcement-title {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.6rem;
    }

    .announcement-body {
        font-size: 0.875rem;
        color: #475569;
        line-height: 1.75;
        margin-bottom: 0.85rem;
    }

    .announcement-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.775rem;
        color: #64748b;
    }

    .announcement-date-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.25rem 0.65rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">اطلاعیه‌ها و اخبار سازمان</h1>
        <p class="text-muted small mb-0">مشاهده آخرین بخشنامه‌ها، اطلاعیه‌های مدیریتی و اخبار داخلی شرکت</p>
    </div>

    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
            <i class="fas fa-print"></i>
            <span>چاپ اطلاعیه‌ها</span>
        </button>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="announcement-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fas fa-bullhorn text-indigo-600" style="color: #4f46e5;"></i>
            <span>لیست اطلاعیه‌های فعال</span>
        </h6>
        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold" style="background:#e0e7ff; color:#4338ca;">
            تعداد اطلاعیه‌ها: <?php echo count($announcements); ?>
        </span>
    </div>

    <div class="card-body p-4">
        <?php if (!empty($announcements)): ?>
            <?php foreach ($announcements as $ann): ?>
                <div class="announcement-box-item">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <span class="announcement-title mb-0">
                            <i class="fas fa-circle-info me-2" style="color: #6366f1;"></i>
                            <?php echo sanitize($ann->title ?? ''); ?>
                        </span>
                        <span class="badge bg-light text-dark font-monospace border">اطلاعیه عمومی</span>
                    </div>

                    <div class="announcement-body text-justify-custom">
                        <?php echo nl2br(sanitize($ann->body ?? '')); ?>
                    </div>

                    <div class="announcement-meta">
                        <span class="announcement-date-badge font-monospace">
                            <i class="far fa-calendar-alt text-muted"></i>
                            زمان انتشار: <?php echo !empty($ann->created_at) ? sanitize(jdate('Y/m/d H:i', strtotime($ann->created_at))) : '---'; ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-bullhorn fs-2 mb-3 d-block opacity-40"></i>
                <span class="fw-bold small d-block mb-1">هیچ اطلاعیه جدیدی ثبت نشده است.</span>
                <span class="small text-muted">اخبار و بخشنامه‌های جدید سازمان در این بخش به شما نمایش داده خواهد شد.</span>
            </div>
        <?php endif; ?>
    </div>
</div>