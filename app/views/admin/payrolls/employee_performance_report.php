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
    echo '<div class="alert alert-danger border-0 shadow-sm rounded-3">اطلاعات کارمند مورد نظر یافت نشد.</div>';
    return;
}

$total_tasks = $performance['total_tasks'] ?? 0;
$completed_tasks = $performance['completed_tasks'] ?? 0;
$success_rate = $performance['success_rate'] ?? 0;
$pending_tasks = $total_tasks - $completed_tasks;
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
    }

    body, .performance-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    /* KPI Modern Cards */
    .kpi-card-ultra {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
        height: 100%;
        transition: transform 0.2s;
    }

    .kpi-card-ultra:hover {
        transform: translateY(-2px);
    }

    .kpi-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Month Selector Pills */
    .month-pill {
        border-radius: 10px;
        padding: 6px 14px;
        font-size: 0.85rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s;
    }

    .month-pill.active {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    .month-pill.inactive {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid var(--border-color);
    }

    .month-pill.inactive:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    /* Ultra Card & Table */
    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
    }

    .table-ultra-perf {
        border-collapse: separate;
        border-spacing: 0 6px;
        width: 100%;
    }

    .table-ultra-perf tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01);
        border-radius: 10px;
        transition: transform 0.2s;
    }

    .table-ultra-perf tbody tr:hover {
        background-color: #f8fafc;
    }

    .table-ultra-perf td, .table-ultra-perf th {
        padding: 0.9rem 1.1rem;
        border: none;
        vertical-align: middle;
    }

    .bg-soft-success { background-color: #dcfce7; color: #15803d !important; }
    .bg-soft-warning { background-color: #fef3c7; color: #b45309 !important; }
    .bg-soft-danger { background-color: #fee2e2; color: #b91c1c !important; }
    .bg-soft-primary { background-color: #e0e7ff; color: #4338ca !important; }
    .bg-soft-info { background-color: #e0f2fe; color: #0369a1 !important; }

    @media print {
        .no-print { display: none !important; }
        body { background: #fff !important; }
        .ultra-card, .kpi-card-ultra { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>

<div class="performance-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">گزارش عملکرد: <span class="text-primary"><?php echo htmlspecialchars($employee->name); ?></span></h3>
            <p class="text-muted small mb-0">تحلیل ارزیابی وظایف محوله، نظم کاری و کارکرد سال <?php echo $currentYear; ?></p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=employee_list" class="btn btn-outline-secondary btn-sm px-4 rounded-pill fw-medium no-print">
            <i class="fas fa-arrow-right me-1"></i> لیست پرسنل
        </a>
    </div>

    <?php FlashMessage::display(); ?>

    <!-- Month Filter Bar -->
    <div class="ultra-card p-3 mb-4 no-print">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fas fa-filter text-primary"></i>
            <span class="fw-bold small text-dark">انتخاب ماه ارزیابی:</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?php for ($m = 1; $m <= 12; $m++): 
                $monthName = jdate_words(['mm' => $m])['mm'];
                $isActive = ($m == $currentMonth) ? 'active' : 'inactive';
            ?>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=employee_performance_report&id=<?php echo $employee->id; ?>&year=<?php echo $currentYear; ?>&month=<?php echo $m; ?>" class="month-pill <?php echo $isActive; ?>">
                    <?php echo $monthName; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Top KPI Dashboard Cards -->
    <div class="row g-3 mb-4">
        <!-- Task Success Rate -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card-ultra d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-medium">نرخ تکمیل وظایف (ماه جاری)</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?php echo htmlspecialchars($performance['success_rate']); ?>%</h3>
                    <span class="text-muted small fs-7"><?php echo htmlspecialchars($performance['completed_tasks']); ?> از <?php echo htmlspecialchars($performance['total_tasks']); ?> وظیفه</span>
                </div>
                <div class="kpi-icon-wrapper bg-soft-primary">
                    <i class="fas fa-tasks"></i>
                </div>
            </div>
        </div>

        <!-- 3-Month Average -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card-ultra d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-medium">میانگین عملکرد (۳ ماه اخیر)</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?php echo htmlspecialchars($averagePerformance); ?>%</h3>
                    <span class="text-muted small fs-7">شاخص کل سه دوره</span>
                </div>
                <div class="kpi-icon-wrapper bg-soft-info">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>

        <!-- Bonus/Penalty Status -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card-ultra d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-medium">وضعیت حقوقی ماه جاری</span>
                    <h5 class="fw-bold mb-0 mt-1">
                        <?php 
                            if ($success_rate >= 95) {
                                echo '<span class="text-success"><i class="fas fa-award me-1"></i> مستحق پاداش</span>';
                            } elseif ($success_rate >= 70) {
                                echo '<span class="text-primary"><i class="fas fa-check-circle me-1"></i> بدون تغییر</span>';
                            } elseif ($success_rate >= 60) {
                                echo '<span class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i> جریمه کم</span>';
                            } else {
                                echo '<span class="text-danger"><i class="fas fa-times-circle me-1"></i> جریمه شدید</span>';
                            }
                        ?>
                    </h5>
                </div>
                <div class="kpi-icon-wrapper <?php echo $success_rate >= 95 ? 'bg-soft-success' : ($success_rate < 60 ? 'bg-soft-danger' : 'bg-soft-warning'); ?>">
                    <i class="fas fa-medal"></i>
                </div>
            </div>
        </div>

        <!-- Working Hours -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card-ultra d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-medium">ساعات کاری ثبت‌شده</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?php echo htmlspecialchars($attendanceSummary->total_hours); ?> <span class="fs-6 font-normal">ساعت</span></h3>
                    <span class="text-muted small fs-7"><?php echo htmlspecialchars($attendanceSummary->work_days_count); ?> روز کاری ثبت شده</span>
                </div>
                <div class="kpi-icon-wrapper bg-soft-primary">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Chart and Tasks Grid -->
    <div class="row g-4 mb-4">
        <!-- Donut Progress Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="ultra-card p-4 h-100">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-chart-pie text-primary me-2"></i>نمودار پیشرفت وظایف</h6>
                <?php if ($total_tasks > 0): ?>
                    <div style="height: 250px; position: relative;" class="pt-2">
                        <canvas id="taskCompletionChart"></canvas>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-muted my-auto">
                        <i class="fas fa-info-circle fa-2x mb-2 text-light"></i>
                        <p class="mb-0 small">هیچ وظیفه‌ای برای این ماه ثبت نشده است.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Assigned Tasks Table -->
        <div class="col-xl-8 col-lg-7">
            <div class="ultra-card p-4 h-100">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-tasks text-primary me-2"></i>وظایف محوله در ماه <?php echo jdate_words(['mm' => $currentMonth])['mm']; ?></h6>
                <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-ultra-perf align-middle">
                        <thead>
                            <tr class="text-muted small">
                                <th>پروژه</th>
                                <th>عنوان وظیفه</th>
                                <th class="text-center">وضعیت</th>
                                <th class="text-end">سررسید</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tasks)): ?>
                                <?php foreach ($tasks as $task): ?>
                                    <tr>
                                        <td><span class="badge bg-soft-primary px-2.5 py-1">#<?php echo htmlspecialchars($task->project_id); ?></span></td>
                                        <td class="fw-bold text-dark small"><?php echo htmlspecialchars($task->title); ?></td>
                                        <td class="text-center">
                                            <?php 
                                                $statusClass = 'bg-soft-danger';
                                                if ($task->status === 'done') $statusClass = 'bg-soft-success';
                                                elseif ($task->status === 'in_progress') $statusClass = 'bg-soft-warning';
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?> px-3 py-1.5 rounded-pill small">
                                                <?php echo getStatusInPersian($task->status); ?>
                                            </span>
                                        </td>
                                        <td class="text-end text-muted small">
                                            <?php echo $task->due_date ? jdate('Y/m/d', strtotime($task->due_date)) : '---'; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">هیچ وظیفه‌ای در این بازه زمانی ثبت نشده است.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance & Discipline Table -->
    <div class="ultra-card p-4">
        <h6 class="fw-bold text-dark mb-3"><i class="fas fa-business-time text-primary me-2"></i>آخرین رکوردهای حضور و غیاب (نظم کاری)</h6>
        <div class="table-responsive">
            <table class="table table-ultra-perf align-middle">
                <thead>
                    <tr class="text-muted small">
                        <th>تاریخ</th>
                        <th class="text-center">ورود</th>
                        <th class="text-center">خروج</th>
                        <th class="text-center">مدت حضور (ساعت)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($attendanceRecords)): ?>
                        <?php foreach ($attendanceRecords as $record): ?>
                            <tr>
                                <td class="fw-bold text-dark small">
                                    <i class="far fa-calendar-alt me-1 text-muted"></i>
                                    <?php echo jdate('Y/m/d', strtotime($record->clock_in)); ?>
                                </td>
                                <td class="text-center text-success fw-bold small">
                                    <?php echo jdate('H:i', strtotime($record->clock_in)); ?>
                                </td>
                                <td class="text-center text-danger fw-bold small">
                                    <?php echo $record->clock_out ? jdate('H:i', strtotime($record->clock_out)) : '---'; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-primary px-3 py-1.5 rounded-pill fw-bold">
                                        <?php echo $record->total_duration ? round($record->total_duration / 60, 2) : '---'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted small">هیچ رکورد حضوری در این بازه زمانی یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($total_tasks > 0): ?>
        const completed = <?php echo $completed_tasks; ?>;
        const pending = <?php echo $pending_tasks; ?>;
        
        const ctx = document.getElementById("taskCompletionChart").getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ["وظایف تکمیل شده", "وظایف در انتظار"],
                datasets: [{
                    data: [completed, pending],
                    backgroundColor: ['#10b981', '#f43f5e'],
                    borderWidth: 0
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        fontFamily: 'Vazirmatn',
                        boxWidth: 12
                    }
                },
                cutoutPercentage: 78
            },
        });
    <?php endif; ?>
});
</script>