<?php
// app/views/admin/payrolls/employee_performance_report.php
$employee = $employee ?? null;
$performance = $performance ?? [];
$attendanceRecords = $attendanceRecords ?? [];
$attendanceSummary = $attendanceSummary ?? (object)['total_hours' => 0, 'work_days_count' => 0];
$tasks = $tasks ?? [];
$currentYear = $currentYear ?? jdate('Y');
$currentMonth = $currentMonth ?? jdate('n');
$averagePerformance = $averagePerformance ?? 0;

if (!$employee) {
    echo '<div class="alert alert-danger">اطلاعات کارمند مورد نظر یافت نشد.</div>';
    return;
}

// برای جلوگیری از خطای Undefined index
$total_tasks = $performance['total_tasks'] ?? 0;
$completed_tasks = $performance['completed_tasks'] ?? 0;
$success_rate = $performance['success_rate'] ?? 0;

$pending_tasks = $total_tasks - $completed_tasks;
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">گزارش عملکرد: <?php echo htmlspecialchars($employee->name); ?> (سال <?php echo $currentYear; ?>)</h1>
    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=employee_list" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-right"></i> بازگشت به لیست کارمندان
    </a>
</div>

<?php echo FlashMessage::display(); ?>

<!-- ✅ فیلتر ماهانه -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">فیلتر گزارش بر اساس ماه</h6>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <?php for ($m = 1; $m <= 12; $m++): 
                $monthName = jdate_words(['mm' => $m])['mm'];
                $isActive = ($m == $currentMonth) ? 'btn-primary' : 'btn-outline-primary';
            ?>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=employee_performance_report&id=<?php echo $employee->id; ?>&year=<?php echo $currentYear; ?>&month=<?php echo $m; ?>" class="btn btn-sm <?php echo $isActive; ?>">
                    <?php echo $monthName; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</div>

<div class="row">
    <!-- کارت نرخ تکمیل وظایف ماه جاری -->
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col me-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        نرخ تکمیل وظایف **ماه جاری**
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?php echo htmlspecialchars($performance['success_rate']); ?>%
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-tasks fa-2x text-gray-300"></i>
                </div>
            </div>
            <small class="text-muted">وظایف تکمیل شده: <?php echo htmlspecialchars($performance['completed_tasks']); ?> از <?php echo htmlspecialchars($performance['total_tasks']); ?></small>
        </div>
    </div>
</div>

<div class="col-xl-3 col-md-6 mb-4">
    <div class="card border-left-secondary shadow h-100 py-2">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col me-2">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                        میانگین عملکرد (۳ ماه قبل)
                    </div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        <?php echo htmlspecialchars($averagePerformance); ?>%
                    </div>
                </div>
                <div class="col-auto">
                    <i class="fas fa-chart-area fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
    </div>
</div>
    
    <!-- کارت وضعیت پاداش/جریمه -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-<?php 
            if ($success_rate >= 95) echo 'success';
            elseif ($success_rate < 60) echo 'danger';
            else echo 'warning';
        ?> shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            وضعیت حقوق ماهانه
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php 
                                if ($success_rate >= 95) {
                                    echo '✅ مستحق پاداش';
                                } elseif ($success_rate >= 70) {
                                    echo '🟡 بدون تغییر';
                                } elseif ($success_rate >= 60) {
                                    echo '⚠️ مستحق جریمه کم';
                                } else {
                                    echo '❌ مستحق جریمه شدید';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-medal fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- کارت نظم و حضور و غیاب -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            کل ساعات کاری (ماه جاری)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo htmlspecialchars($attendanceSummary->total_hours) . ' ساعت'; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
                <small class="text-muted">روزهای کاری ثبت شده: <?php echo htmlspecialchars($attendanceSummary->work_days_count); ?></small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">نمودار پیشرفت وظایف</h6></div>
            <div class="card-body">
                <?php if ($total_tasks > 0): ?>
                    <div class="chart-pie pt-4">
                        <canvas id="taskCompletionChart"></canvas>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">هیچ وظیفه‌ای برای این ماه ثبت نشده است.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- گزارش وظایف محوله -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">وظایف محوله در ماه <?php echo jdate_words(['mm' => $currentMonth])['mm']; ?></h6></div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>پروژه</th>
                                <th>عنوان وظیفه</th>
                                <th>وضعیت</th>
                                <th>سررسید</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tasks)): ?>
                                <?php foreach ($tasks as $task): ?>
                                    <tr>
                                        <td><span class="badge bg-secondary">#<?php echo htmlspecialchars($task->project_id); ?></span></td>
                                        <td><?php echo htmlspecialchars($task->title); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                if ($task->status === 'done') echo 'success';
                                                elseif ($task->status === 'in_progress') echo 'warning';
                                                else echo 'danger';
                                            ?>">
                                                <?php echo getStatusInPersian($task->status); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $task->due_date ? jdate('Y/m/d', strtotime($task->due_date)) : '---'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">هیچ وظیفه‌ای در این بازه زمانی برای این کارمند یافت نشد.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- گزارش حضور و غیاب -->
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">آخرین رکوردهای حضور و غیاب (نظم کاری)</h6></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>تاریخ</th>
                                <th>ورود</th>
                                <th>خروج</th>
                                <th>مدت (ساعت)</th>
                                <th>تأخیر/اضافه (دقیقه)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($attendanceRecords)): ?>
                                <?php foreach ($attendanceRecords as $record): ?>
                                    <tr>
                                        <td><?php echo jdate('Y/m/d', strtotime($record->clock_in)); ?></td>
                                        <td><?php echo jdate('H:i', strtotime($record->clock_in)); ?></td>
                                        <td><?php echo $record->clock_out ? jdate('H:i', strtotime($record->clock_out)) : '---'; ?></td>
                                        <td><?php echo $record->total_duration ? round($record->total_duration / 60, 2) : '---'; ?></td>
                                        <td>---</td> 
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">هیچ رکورد حضوری در این بازه زمانی یافت نشد.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($total_tasks > 0): ?>
    
        // داده‌های نمودار
        const completed = <?php echo $completed_tasks; ?>;
        const pending = <?php echo $pending_tasks; ?>;
        
        const ctx = document.getElementById("taskCompletionChart").getContext('2d');
        const taskCompletionChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["وظایف تکمیل شده", "وظایف در انتظار"],
                datasets: [{
                    data: [completed, pending],
                    backgroundColor: ['#1cc88a', '#e74a3b'],
                    hoverBackgroundColor: ['#17a673', '#d93729'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                cutoutPercentage: 80,
            },
        });
    <?php endif; ?>
});
</script>