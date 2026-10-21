<?php
// app/views/client/my_projects.php - نسخه اولترا پرمیوم
$projects = $projects ?? [];

$totalProjects = count($projects);
$inProgressCount = 0;
$completedCount = 0;

foreach ($projects as $p) {
    $status = $p->status ?? '';
    if (in_array($status, ['in_progress', 'active', 'todo', 'review'])) {
        $inProgressCount++;
    } elseif (in_array($status, ['completed', 'done'])) {
        $completedCount++;
    }
}
?>

<style>
    .projects-card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .project-item-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
        padding: 1.5rem;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .project-item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 35px -5px rgba(99, 102, 241, 0.12);
        border-color: #c7d2fe;
    }

    .project-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: #e0e7ff;
        color: #4338ca;
    }

    .custom-progress {
        height: 8px;
        border-radius: 10px;
        background-color: #f1f5f9;
        overflow: hidden;
    }

    .custom-progress-bar {
        background: linear-gradient(90deg, #6366f1, #4f46e5);
        border-radius: 10px;
        transition: width 0.6s ease;
    }

    .meta-badge {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.35rem 0.65rem;
        font-size: 0.775rem;
        color: #64748b;
        font-weight: 600;
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">پروژه‌های من</h1>
        <p class="text-muted small mb-0">مشاهده روند توسعه، درصد پیشرفت و فازهای عملیاتی پروژه‌های جاری و تکمیل‌شده شما</p>
    </div>

    <div class="d-flex align-items-center gap-2 no-print">
        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-2 fw-bold font-monospace" style="background:#e0e7ff; color:#4338ca;">
            در حال انجام: <?php echo $inProgressCount; ?> پروژه
        </span>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
            <i class="fas fa-print"></i>
            <span>چاپ لیست</span>
        </button>
    </div>
</div>

<!-- گرید پروژه‌ها -->
<div class="row g-4 mb-4">
    <?php if (!empty($projects)): ?>
        <?php foreach ($projects as $project): ?>
            <?php 
                $progress = (int)($project->progress ?? $project->completion_percentage ?? 0);
                if ($progress > 100) $progress = 100;
                if ($progress < 0) $progress = 0;

                $status = $project->status ?? 'in_progress';
                $statusClass = 'bg-primary';
                $statusText = 'در حال انجام';

                if ($status === 'completed' || $progress === 100) {
                    $statusClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                    $statusText = 'تکمیل شده';
                } elseif ($status === 'in_progress') {
                    $statusClass = 'bg-indigo-50 text-indigo-700 border border-indigo-200';
                    $statusText = 'در حال توسعه';
                } elseif ($status === 'on_hold') {
                    $statusClass = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                    $statusText = 'معلق / متوقف';
                } elseif ($status === 'canceled') {
                    $statusClass = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                    $statusText = 'لغو شده';
                }
            ?>
            <div class="col-lg-6 col-xl-4">
                <div class="project-item-card">
                    <div>
                        <!-- هدر کارت -->
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="project-icon-box">
                                    <i class="fas fa-diagram-project"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1"><?php echo sanitize($project->title ?? 'عنوان پروژه'); ?></h6>
                                    <span class="small text-muted font-monospace">کد: #PRJ-<?php echo sanitize($project->id); ?></span>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold <?php echo $statusClass; ?>" style="<?php echo ($status === 'in_progress') ? 'background:#e0e7ff; color:#4338ca;' : ''; ?>">
                                <?php echo $statusText; ?>
                            </span>
                        </div>

                        <!-- توضیحات کوتاه -->
                        <?php if (!empty($project->description)): ?>
                            <p class="text-secondary small mb-3 line-clamp-2 text-justify-custom">
                                <?php echo sanitize($project->description); ?>
                            </p>
                        <?php endif; ?>

                        <!-- نوار پیشرفت -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between small fw-bold mb-1">
                                <span class="text-muted">پیشرفت کل پروژه</span>
                                <span class="font-monospace text-indigo-600" style="color:#4f46e5;"><?php echo $progress; ?>%</span>
                            </div>
                            <div class="progress custom-progress">
                                <div class="progress-bar custom-progress-bar" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>

                    <!-- فوتر کارت -->
                    <div class="border-top pt-3 mt-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="meta-badge font-monospace">
                            <i class="far fa-calendar-alt me-1"></i>
                            تاریخ شروع: <?php echo sanitize($project->start_date_jalali ?? '---'); ?>
                        </span>

                        <a href="<?php echo APP_URL; ?>/index.php?page=client&action=my_tickets" class="btn btn-light border btn-sm rounded-3 fw-bold small text-indigo-600" style="color:#4f46e5;">
                            <i class="fas fa-headset me-1"></i> پیگیری
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <div class="card shadow-sm border-0 rounded-4 p-5">
                <i class="fas fa-diagram-project fs-1 text-muted mb-3 opacity-40"></i>
                <h6 class="fw-bold text-dark mb-1">هنوز هیچ پروژه‌ای برای شما تعریف نشده است.</h6>
                <p class="text-muted small mb-0">پس از نهایی‌سازی قرارداد، پروژه‌های اجرایی شما در این بخش قابل مشاهده خواهند بود.</p>
            </div>
        </div>
    <?php endif; ?>
</div>