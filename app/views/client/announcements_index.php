<?php
// app/views/client/announcements_index.php - نسخه اولترا پرمیوم
$announcements = $announcements ?? [];
?>

<style>
    .announcement-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
        padding: 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .announcement-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 35px -5px rgba(99, 102, 241, 0.12);
        border-color: #c7d2fe;
    }

    .announcement-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: #e0e7ff;
        color: #4338ca;
        flex-shrink: 0;
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">اطلاعیه‌ها و اخبار سیستم</h1>
        <p class="text-muted small mb-0">آخرین بروزرسانی‌ها، اطلاعیه‌های فنی و بیانیه‌های رسمی مجموعه</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <?php if (!empty($announcements)): ?>
        <?php foreach ($announcements as $item): ?>
            <div class="col-12">
                <div class="announcement-card">
                    <div class="d-flex align-items-start gap-3">
                        <div class="announcement-icon-box">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div class="w-100">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <h5 class="fw-bold text-dark mb-0 fs-6"><?php echo sanitize($item->title ?? 'اطلاعیه جدید'); ?></h5>
                                <span class="badge bg-light text-muted border font-monospace small px-2.5 py-1">
                                    <i class="far fa-clock me-1"></i>
                                    <?php echo sanitize($item->created_at_jalali ?? '---'); ?>
                                </span>
                            </div>
                            <p class="text-secondary small mb-0 lh-lg">
                                <?php echo nl2br(sanitize($item->content ?? $item->body ?? '')); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <div class="card shadow-sm border-0 rounded-4 p-5">
                <i class="fas fa-bullhorn fs-1 text-muted mb-3 opacity-40"></i>
                <h6 class="fw-bold text-dark mb-1">در حال حاضر هیچ اطلاعیه‌ای ثبت نشده است.</h6>
                <p class="text-muted small mb-0">اطلاعیه‌ها و اخبار جدید سیستم در این بخش نمایش داده خواهند شد.</p>
            </div>
        </div>
    <?php endif; ?>
</div>