<?php
// app/views/admin/dashboard.php - نسخه نهایی با طراحی مدرن و بخش اطلاعیه‌ها
?>
<style>
    .stat-card {
        border-left: 4px solid;
        border-radius: .35rem;
        box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
        transition: transform 0.2s ease-in-out;
    }
    .stat-card:hover { transform: translateY(-3px); }
    .stat-card .card-body { padding: 1.25rem; }
    .stat-card .text-xs { font-size: .7rem; font-weight: 700; text-transform: uppercase; }
    .stat-card .h5 { font-weight: 700; }
    .stat-card i { font-size: 2rem; color: #dddfeb; }
    .border-left-primary { border-left-color: #4e73df !important; }
    .border-left-success { border-left-color: #1cc88a !important; }
    .border-left-info { border-left-color: #36b9cc !important; }
    .border-left-warning { border-left-color: #f6c23e !important; }
    
    /* استایل‌های جذاب برای اطلاعیه‌ها */
    .announcement-card {
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .announcement-item {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        transition: all 0.2s;
    }
    .announcement-item:hover {
        background-color: #e2e6ea;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .announcement-item .announcement-title {
        font-weight: bold;
        color: #34495e;
        display: block;
        margin-bottom: 5px;
    }
    .announcement-item .announcement-body {
        color: #6c757d;
    }
    .announcement-item .announcement-date {
        font-size: 0.75rem;
        color: #adb5bd;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">داشبورد</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> تولید گزارش</a>
</div>

<div class="row">
    <!-- کارت‌های آماری -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">مشتریان فعال</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $clientsCount ?? '0'; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">قراردادهای فعال</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $contractsCount ?? '0'; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-file-signature"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">پروژه‌های در حال انجام</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">5</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-tasks"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">فاکتورهای پرداخت‌نشده</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $invoicesCount ?? '0'; ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-file-invoice-dollar"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- بخش اطلاعیه‌ها -->
    <div class="col-lg-12 mb-4">
        <div class="card shadow announcement-card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">اطلاعیه‌ها و اخبار</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($announcements)): ?>
                    <?php foreach ($announcements as $announcement): ?>
                        <div class="announcement-item">
                            <span class="announcement-title"><?php echo htmlspecialchars($announcement->title); ?></span>
                            <div class="announcement-body">
                                <?php echo $announcement->body; ?>
                            </div>
                            <span class="announcement-date">تاریخ انتشار: <?php echo jdate('Y/m/d H:i', strtotime($announcement->created_at)); ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-light text-center" role="alert">
                        هیچ اطلاعیه‌ای برای نمایش وجود ندارد.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- نمودار درآمد ماهانه -->
    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">نمودار درآمد ماهانه</h6></div>
            <div class="card-body"><canvas id="myAreaChart"></canvas></div>
        </div>
    </div>
    
    <!-- فعالیت‌های اخیر -->
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">فعالیت‌های اخیر</h6></div>
            <div class="card-body"><p>در این بخش می‌توانید آخرین فعالیت‌های سیستم را مشاهده کنید.</p></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('myAreaChart').getContext('2d');
    var gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(78, 115, 223, 0.2)');   
    gradient.addColorStop(1, 'rgba(78, 115, 223, 0)');
    
    var myAreaChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور'],
            datasets: [{
                label: 'درآمد (تومان)',
                data: [1200000, 1900000, 1500000, 2500000, 2200000, 3000000],
                backgroundColor: gradient,
                borderColor: '#4e73df',
                borderWidth: 3,
                pointBackgroundColor: '#4e73df',
                pointBorderColor: '#fff',
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#4e73df',
                pointHoverBorderColor: '#fff',
                fill: true,
                tension: 0.4
            }],
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
});
</script>
