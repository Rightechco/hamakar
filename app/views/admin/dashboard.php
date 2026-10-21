<?php
// app/views/admin/dashboard.php - نسخه نهایی اولترا پرمیوم و هوشمند
$clientsCount = $clientsCount ?? 0;
$contractsCount = $contractsCount ?? 0;
$invoicesCount = $invoicesCount ?? 0;
$announcements = $announcements ?? [];
?>

<style>
    :root {
        --dash-card-bg: #ffffff;
        --dash-text-main: #0f172a;
        --dash-text-muted: #64748b;
        --dash-border: #f1f5f9;
        --dash-accent: #6366f1;
        --dash-accent-hover: #4f46e5;
        --dash-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04), 0 4px 12px -2px rgba(0, 0, 0, 0.02);
        --dash-shadow-hover: 0 20px 35px -5px rgba(99, 102, 241, 0.12), 0 10px 15px -5px rgba(0, 0, 0, 0.04);
    }

    /* Header Section */
    .dash-header-title {
        font-weight: 900;
        color: var(--dash-text-main);
        letter-spacing: -0.5px;
        font-size: 1.65rem;
    }

    .live-pulse {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse-green 2s infinite;
    }

    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .btn-premium-action {
        background: linear-gradient(135deg, var(--dash-accent) 0%, var(--dash-accent-hover) 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 14px;
        padding: 0.7rem 1.4rem;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none !important;
        cursor: pointer;
    }
    .btn-premium-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px -4px rgba(99, 102, 241, 0.5);
    }

    /* Stat Cards Glassmorphism */
    .premium-stat-card {
        background: var(--dash-card-bg);
        border-radius: 22px;
        border: 1px solid var(--dash-border);
        box-shadow: var(--dash-shadow);
        padding: 1.5rem;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .premium-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 4px;
        background: transparent;
        transition: background 0.3s ease;
    }
    .premium-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--dash-shadow-hover);
        border-color: rgba(99, 102, 241, 0.2);
    }
    .premium-stat-card.card-primary:hover::before { background: linear-gradient(90deg, #6366f1, #818cf8); }
    .premium-stat-card.card-success:hover::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .premium-stat-card.card-info:hover::before { background: linear-gradient(90deg, #0284c7, #38bdf8); }
    .premium-stat-card.card-warning:hover::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

    .stat-icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        transition: transform 0.35s ease;
    }
    .premium-stat-card:hover .stat-icon-wrapper {
        transform: scale(1.12) rotate(-6deg);
    }

    .icon-bg-primary { background: #e0e7ff; color: #4f46e5; }
    .icon-bg-success { background: #d1fae5; color: #059669; }
    .icon-bg-info { background: #e0f2fe; color: #0284c7; }
    .icon-bg-warning { background: #fef3c7; color: #d97706; }

    .stat-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--dash-text-muted);
        margin-bottom: 0.4rem;
    }
    .stat-value {
        font-size: 1.85rem;
        font-weight: 900;
        color: var(--dash-text-main);
        line-height: 1;
        font-family: monospace, 'Vazirmatn';
    }

    /* Container Boxes */
    .premium-box {
        background: var(--dash-card-bg);
        border-radius: 24px;
        border: 1px solid var(--dash-border);
        box-shadow: var(--dash-shadow);
        overflow: hidden;
        margin-bottom: 1.75rem;
        transition: all 0.3s ease;
    }
    .premium-box-header {
        padding: 1.25rem 1.6rem;
        background: #ffffff;
        border-bottom: 1px solid #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .premium-box-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--dash-text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    /* Announcement Cards */
    .announcement-card-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1.25rem 1.4rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        border-right: 5px solid #6366f1;
        position: relative;
    }
    .announcement-card-item:hover {
        background: #ffffff;
        box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
        border-right-color: #4f46e5;
        transform: translateX(-4px);
    }
    .announcement-card-item:last-child {
        margin-bottom: 0;
    }
    .announcement-card-item .title {
        font-size: 0.975rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.5rem;
        display: block;
    }
    .announcement-card-item .body-text {
        font-size: 0.875rem;
        color: #475569;
        line-height: 1.7;
        margin-bottom: 0.75rem;
    }
    .announcement-card-item .date-badge {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #ffffff;
        padding: 0.3rem 0.75rem;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    /* Activity Timeline */
    .timeline-wrapper {
        position: relative;
        padding: 0.5rem 0;
    }
    .timeline-item {
        position: relative;
        padding-right: 2rem;
        padding-bottom: 1.5rem;
        border-right: 2px dashed #e2e8f0;
    }
    .timeline-item:last-child {
        border-right-color: transparent;
        padding-bottom: 0;
    }
    .timeline-dot {
        position: absolute;
        right: -7px;
        top: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #6366f1;
        outline: 4px solid #ffffff;
        box-shadow: 0 0 0 1px #e2e8f0;
    }
    .timeline-content {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.4;
    }
    .timeline-time {
        font-size: 0.75rem;
        color: #94a3b8;
        display: block;
        margin-top: 0.3rem;
        font-weight: 500;
    }
</style>

<!-- Header Action Area -->
<div class="d-flex align-items-center justify-content-between mb-4 pb-2 flex-wrap gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="dash-header-title mb-0">داشبورد مدیریتی</h1>
            <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-2.5 py-1 small fw-bold d-inline-flex align-items-center gap-1.5" style="background:#e0e7ff; color:#4338ca;">
                <span class="live-pulse"></span>
                بروزرسانی آنلاین
            </span>
        </div>
        <p class="text-muted small mb-0">خلاصه وضعیت کلی سامانه هماکار، گزارش مالی و عملکرد آنلاین سازمان</p>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=reports" class="btn btn-premium-action d-inline-flex align-items-center gap-2">
            <i class="fas fa-chart-line"></i>
            <span>گزارش جامع سیستم</span>
        </a>
    </div>
</div>

<!-- Stat Cards Row -->
<div class="row g-3 mb-4">
    <!-- مشتریان فعال -->
    <div class="col-xl-3 col-md-6">
        <div class="premium-stat-card card-primary">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">مشتریان فعال</div>
                    <div class="stat-value"><?php echo number_format((int)$clientsCount); ?></div>
                </div>
                <div class="stat-icon-wrapper icon-bg-primary">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- قراردادهای فعال -->
    <div class="col-xl-3 col-md-6">
        <div class="premium-stat-card card-success">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">قراردادهای فعال</div>
                    <div class="stat-value"><?php echo number_format((int)$contractsCount); ?></div>
                </div>
                <div class="stat-icon-wrapper icon-bg-success">
                    <i class="fas fa-file-signature"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- پروژه‌ها -->
    <div class="col-xl-3 col-md-6">
        <div class="premium-stat-card card-info">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">پروژه‌های در حال انجام</div>
                    <div class="stat-value">5</div>
                </div>
                <div class="stat-icon-wrapper icon-bg-info">
                    <i class="fas fa-diagram-project"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- فاکتورهای پرداخت نشده -->
    <div class="col-xl-3 col-md-6">
        <div class="premium-stat-card card-warning">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">فاکتورهای معوق</div>
                    <div class="stat-value"><?php echo number_format((int)$invoicesCount); ?></div>
                </div>
                <div class="stat-icon-wrapper icon-bg-warning">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Announcements Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="premium-box">
            <div class="premium-box-header">
                <h6 class="premium-box-title">
                    <i class="fas fa-bullhorn" style="color:#6366f1;"></i>
                    <span>اطلاعیه‌ها و اخبار سازمان</span>
                </h6>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=announcements_index" class="text-decoration-none small fw-bold text-indigo-600" style="color: #6366f1;">مدیریت اطلاعیه‌ها <i class="fas fa-arrow-left ms-1"></i></a>
            </div>
            <div class="p-3 p-md-4">
                <?php if (!empty($announcements)): ?>
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement-card-item">
                            <span class="title"><?php echo htmlspecialchars($announcement->title ?? ''); ?></span>
                            <div class="body-text">
                                <?php echo nl2br(htmlspecialchars($announcement->body ?? '')); ?>
                            </div>
                            <div class="date-badge">
                                <i class="far fa-clock"></i>
                                <span>زمان انتشار: <?php echo !empty($announcement->created_at) ? jdate('Y/m/d H:i', strtotime($announcement->created_at)) : 'نامشخص'; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="far fa-bell-slash fs-2 d-block mb-2 opacity-40"></i>
                        <span class="small fw-bold">هیچ اطلاعیه جدیدی جهت نمایش ثبت نشده است.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Charts & Activity Row -->
<div class="row g-3">
    <!-- Chart Box -->
    <div class="col-lg-7">
        <div class="premium-box h-100 mb-0">
            <div class="premium-box-header">
                <h6 class="premium-box-title">
                    <i class="fas fa-chart-area text-primary"></i>
                    <span>نمودار روند درآمد ماهیانه</span>
                </h6>
                <span class="badge bg-light text-dark font-monospace border">سال جاری</span>
            </div>
            <div class="p-3 p-md-4">
                <div style="position: relative; height: 290px; width: 100%;">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Timeline -->
    <div class="col-lg-5">
        <div class="premium-box h-100 mb-0">
            <div class="premium-box-header">
                <h6 class="premium-box-title">
                    <i class="fas fa-clock-rotate-left text-info"></i>
                    <span>آخرین فعالیت‌های ثبت‌شده</span>
                </h6>
            </div>
            <div class="p-3 p-md-4">
                <div class="timeline-wrapper">
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:#10b981;"></div>
                        <div class="timeline-content">صدور فاکتور رسمی جدید در سیستم</div>
                        <span class="timeline-time">۱۰ دقیقه پیش</span>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:#6366f1;"></div>
                        <div class="timeline-content">ثبت و پاسخ به تیکت پشتیبانی کارفرما</div>
                        <span class="timeline-time">۴۵ دقیقه پیش</span>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:#0284c7;"></div>
                        <div class="timeline-content">به‌روزرسانی قرارداد خدمات سئو و پشتیبانی</div>
                        <span class="timeline-time">۲ ساعت پیش</span>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:#f59e0b;"></div>
                        <div class="timeline-content">تایید درخواست مرخصی پرسنل در بخش منابع انسانی</div>
                        <span class="timeline-time">امروز، ۱۱:۳۰</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Engine -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var chartCanvas = document.getElementById('myAreaChart');
    if (!chartCanvas) return;
    
    var ctx = chartCanvas.getContext('2d');
    
    var gradient = ctx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.35)');   
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور'],
            datasets: [{
                label: 'درآمد کل',
                data: [12500000, 19800000, 15400000, 25000000, 22100000, 31000000],
                backgroundColor: gradient,
                borderColor: '#6366f1',
                borderWidth: 3.5,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#6366f1',
                pointBorderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#6366f1',
                pointHoverBorderColor: '#ffffff',
                fill: true,
                tension: 0.4
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    rtl: true,
                    titleFont: { family: 'Vazirmatn' },
                    bodyFont: { family: 'Vazirmatn' },
                    callbacks: {
                        label: function(context) {
                            var value = context.raw || 0;
                            return 'درآمد: ' + new Intl.NumberFormat('fa-IR').format(value) + ' تومان';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { 
                        color: '#64748b', 
                        font: { family: 'Vazirmatn' },
                        callback: function(value) {
                            return new Intl.NumberFormat('fa-IR').format(value / 1000000) + ' م';
                        }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { family: 'Vazirmatn' } }
                }
            }
        }
    });
});
</script>