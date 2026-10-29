<?php
// app/views/admin/dashboard.php - پیشخوان مدیریت یکپارچه هماکار (سبک Nuxt 3 و Suha)
?>
<div class="container-fluid py-2">

    <!-- Page Header & Action Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-primary bg-opacity-10 text-primary mb-2" style="font-size:0.8rem; font-weight:700;">
                <i class="fas fa-sparkles"></i>
                <span>میز کار هوشمند سازمانی | نسخه ابری هماکار</span>
            </div>
            <h2 class="fw-bold mb-1" style="color:#0f172a; font-size:1.6rem; letter-spacing:-0.4px;">
                پیشخوان مدیریت منابع سازمان
            </h2>
            <p class="text-muted small mb-0">گزارش لحظه‌ای شاخص‌های مالی، کارپوشه مودیان، خزانه‌داری چک و وضعیت عملیاتی</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="index.php?page=admin&action=moadianInvoices" class="btn btn-primary px-3 shadow-sm">
                <i class="fas fa-file-invoice-dollar me-1"></i> سامانه مودیان
            </a>
            <a href="index.php?page=admin&action=treasuryCheques" class="btn btn-outline-primary px-3">
                <i class="fas fa-money-check-dollar me-1"></i> ثبت چک صیاد
            </a>
            <a href="index.php?page=admin&action=aiAnalytics" class="btn btn-outline-secondary px-3" style="background:#ffffff;">
                <i class="fas fa-brain text-primary me-1"></i> تحلیل AI
            </a>
        </div>
    </div>

    <!-- Quick Navigation Pills Row -->
    <div class="card border-0 shadow-sm rounded-4 p-2 mb-4 bg-white">
        <div class="d-flex align-items-center gap-2 overflow-auto py-1 px-2" style="white-space:nowrap; -webkit-overflow-scrolling:touch;">
            <span class="text-muted small fw-bold me-2"><i class="fas fa-bolt text-warning me-1"></i>دسترسی سریع:</span>
            <a href="index.php?page=admin&action=moadianInvoices" class="badge bg-light text-dark border px-3 py-2 text-decoration-none rounded-pill">
                <i class="fas fa-file-invoice-dollar text-danger me-1"></i> مودیان مالیاتی
            </a>
            <a href="index.php?page=admin&action=treasuryCheques" class="badge bg-light text-dark border px-3 py-2 text-decoration-none rounded-pill">
                <i class="fas fa-receipt text-success me-1"></i> خزانه‌داری و چک
            </a>
            <a href="index.php?page=admin&action=contractGuarantees" class="badge bg-light text-dark border px-3 py-2 text-decoration-none rounded-pill">
                <i class="fas fa-shield-halved text-primary me-1"></i> تضامین و وثایق
            </a>
            <a href="index.php?page=admin&action=boms" class="badge bg-light text-dark border px-3 py-2 text-decoration-none rounded-pill">
                <i class="fas fa-sitemap text-warning me-1"></i> فرمول ساخت (BOM)
            </a>
            <a href="index.php?page=admin&action=productionOrders" class="badge bg-light text-dark border px-3 py-2 text-decoration-none rounded-pill">
                <i class="fas fa-industry text-purple me-1" style="color:#7c3aed;"></i> دستورات تولید
            </a>
            <a href="index.php?page=admin&action=procurementRequisitions" class="badge bg-light text-dark border px-3 py-2 text-decoration-none rounded-pill">
                <i class="fas fa-cart-flatbed text-info me-1"></i> درخواست خرید
            </a>
            <a href="index.php?page=admin&action=accounting" class="badge bg-light text-dark border px-3 py-2 text-decoration-none rounded-pill">
                <i class="fas fa-calculator text-primary me-1"></i> حسابداری دوبل
            </a>
        </div>
    </div>

    <!-- Nuxt 3 KPI Metrics Grid -->
    <div class="row g-3 mb-4">
        
        <!-- KPI 1: Active Clients -->
        <div class="col-xl-3 col-md-6">
            <div class="nuxt-kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="nuxt-kpi-label">مشتریان و طرف‌حساب‌ها</div>
                        <div class="nuxt-kpi-value"><?php echo $clientsCount ?? '184'; ?></div>
                        <div class="d-inline-flex align-items-center gap-1 text-success small fw-bold mt-2">
                            <i class="fas fa-arrow-trend-up"></i>
                            <span>+۱۲٪ این ماه</span>
                        </div>
                    </div>
                    <div class="nuxt-kpi-icon" style="background:#eef2ff; color:#4f46e5;">
                        <i class="fas fa-users-gear"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 2: Active Contracts -->
        <div class="col-xl-3 col-md-6">
            <div class="nuxt-kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="nuxt-kpi-label">قراردادهای جاری و فعال</div>
                        <div class="nuxt-kpi-value"><?php echo $contractsCount ?? '42'; ?></div>
                        <div class="d-inline-flex align-items-center gap-1 text-success small fw-bold mt-2">
                            <i class="fas fa-shield-check"></i>
                            <span>تضامین معتبر</span>
                        </div>
                    </div>
                    <div class="nuxt-kpi-icon" style="background:#ecfdf5; color:#10b981;">
                        <i class="fas fa-file-signature"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 3: Sayad Cheques & Treasury -->
        <div class="col-xl-3 col-md-6">
            <div class="nuxt-kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="nuxt-kpi-label">اسناد در جریان وصول</div>
                        <div class="nuxt-kpi-value">۲۴ <small style="font-size:0.9rem; font-weight:600; color:#64748b;">فقره</small></div>
                        <div class="d-inline-flex align-items-center gap-1 text-warning small fw-bold mt-2">
                            <i class="fas fa-clock"></i>
                            <span>راس‌گیری ۷۸ روز</span>
                        </div>
                    </div>
                    <div class="nuxt-kpi-icon" style="background:#fffbeb; color:#d97706;">
                        <i class="fas fa-money-check-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 4: Pending Invoices -->
        <div class="col-xl-3 col-md-6">
            <div class="nuxt-kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="nuxt-kpi-label">فاکتورهای پرداخت‌نشده</div>
                        <div class="nuxt-kpi-value"><?php echo $invoicesCount ?? '9'; ?></div>
                        <div class="d-inline-flex align-items-center gap-1 text-danger small fw-bold mt-2">
                            <i class="fas fa-triangle-exclamation"></i>
                            <span>پیگیری مالی</span>
                        </div>
                    </div>
                    <div class="nuxt-kpi-icon" style="background:#fef2f2; color:#ef4444;">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Charts & Activities Grid -->
    <div class="row g-4 mb-4">
        
        <!-- Revenue Line Chart -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark" style="font-size:1.05rem;">
                            <i class="fas fa-chart-line text-primary me-2"></i>نمودار گردش مالی و درآمدهای ۶ ماهه
                        </h5>
                        <small class="text-muted">بر اساس اسناد دوبل ثبت شده در سیستم حسابداری</small>
                    </div>
                    <div class="d-flex gap-1">
                        <button class="btn btn-sm btn-outline-primary active rounded-pill px-3">ماهانه</button>
                        <button class="btn btn-sm btn-light border rounded-pill px-3">فصلی</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="myAreaChart" height="115"></canvas>
                </div>
            </div>
        </div>

        <!-- AI Assistant & Health Widget -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size:1.05rem;">
                        <i class="fas fa-brain text-primary me-2"></i>هوش مصنوعی و سلامت مالی
                    </h5>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill">
                        وضعیت: مطلوب (A+)
                    </span>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="p-3 rounded-4 mb-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fas fa-circle-check text-success"></i>
                                <span class="fw-bold small" style="color:#0f172a;">ارسال به‌موقع کارپوشه مودیان</span>
                            </div>
                            <p class="text-muted small mb-0">۱۰۰٪ فاکتورهای مشمول دوره جاری با شناسه یکتا به کارپوشه مالیاتی ارسال شده‌اند.</p>
                        </div>

                        <div class="p-3 rounded-4 mb-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fas fa-chart-pie text-primary"></i>
                                <span class="fw-bold small" style="color:#0f172a;">پیش‌بینی نقدینگی هفته آتی</span>
                            </div>
                            <p class="text-muted small mb-0">موازنه مثبت دریافتنی‌ها نسبت به چک‌های صادره تا ۲۵ روز آینده پیش‌بینی می‌شود.</p>
                        </div>
                    </div>

                    <a href="index.php?page=admin&action=aiAnalytics" class="btn btn-primary w-100 rounded-3 mt-2">
                        <i class="fas fa-arrow-left me-1"></i> مشاهده گزارش کامل دستیار هوش مصنوعی
                    </a>
                </div>
            </div>
        </div>

    </div>

    <!-- Announcements & System Notices -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size:1.05rem;">
                        <i class="fas fa-bullhorn text-warning me-2"></i>اطلاعیه‌ها و رویدادهای سازمانی
                    </h5>
                    <span class="badge bg-light text-muted border">به‌روزرسانی خودکار</span>
                </div>
                <div class="card-body">
                    <?php if (!empty($announcements)): ?>
                        <div class="row g-3">
                            <?php foreach ($announcements as $announcement): ?>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-4 h-100" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($announcement->title); ?></h6>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill" style="font-size:0.7rem;">
                                                <?php echo jdate('Y/m/d', strtotime($announcement->created_at)); ?>
                                            </span>
                                        </div>
                                        <p class="text-muted small mb-0"><?php echo $announcement->body; ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-4 rounded-4 text-center" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <div class="d-inline-flex p-3 rounded-circle bg-primary bg-opacity-10 text-primary mb-2">
                                <i class="fas fa-bell-slash fa-lg"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">اطلاعیه جدیدی ثبت نشده است</h6>
                            <p class="text-muted small mb-0">اعلان‌های جدید سامانه و هشدارهای مهم در این بخش نمایش داده می‌شوند.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ChartJS Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var chartElem = document.getElementById('myAreaChart');
    if (!chartElem) return;
    
    var ctx = chartElem.getContext('2d');
    var gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(79, 70, 229, 0.22)');   
    gradient.addColorStop(1, 'rgba(79, 70, 229, 0.00)');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور'],
            datasets: [{
                label: 'درآمد ثبت شده (تومان)',
                data: [34000000, 48000000, 42000000, 65000000, 59000000, 78000000],
                backgroundColor: gradient,
                borderColor: '#4f46e5',
                borderWidth: 3,
                pointBackgroundColor: '#4f46e5',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#4f46e5',
                fill: true,
                tension: 0.35
            }],
        },
        options: { 
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: { 
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(226, 232, 240, 0.6)' },
                    ticks: {
                        callback: function(value) {
                            return (value / 1000000).toLocaleString('fa-IR') + ' م';
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
