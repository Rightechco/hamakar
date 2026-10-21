<?php
// app/views/admin/reports/index.php
$invoiceSummary = $invoiceSummary ?? (object)['total_revenue' => 0, 'paid_revenue' => 0, 'unpaid_revenue' => 0];
$projectSummary = $projectSummary ?? ['in_progress' => 0, 'finished' => 0, 'not_started' => 0, 'on_hold' => 0];
$userTaskSummary = $userTaskSummary ?? [];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 18px;
        --radius-md: 12px;
        --shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    body, .reports-dashboard-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    /* KPI Financial Cards */
    .kpi-fin-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 22px;
        box-shadow: var(--shadow-subtle);
        height: 100%;
        transition: transform 0.2s;
    }

    .kpi-fin-card:hover {
        transform: translateY(-2px);
    }

    .kpi-fin-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .bg-soft-primary { background-color: #e0e7ff; color: #4338ca !important; }
    .bg-soft-success { background-color: #dcfce7; color: #15803d !important; }
    .bg-soft-warning { background-color: #fef3c7; color: #b45309 !important; }
    .bg-soft-info { background-color: #e0f2fe; color: #0369a1 !important; }

    /* Ultra Card */
    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        margin-bottom: 1.5rem;
        height: 100%;
    }

    .ultra-card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
    }

    /* List group styling */
    .list-group-ultra .list-group-item {
        border-color: #f1f5f9;
        padding: 1rem 1.25rem;
        font-size: 0.9rem;
    }

    /* Floating Table */
    .table-ultra-emp {
        border-collapse: separate;
        border-spacing: 0 6px;
        width: 100%;
    }

    .table-ultra-emp tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01);
        border-radius: 10px;
    }

    .table-ultra-emp td {
        padding: 1rem 1.25rem;
        border: none;
        vertical-align: middle;
        font-size: 0.9rem;
    }
</style>

<div class="reports-dashboard-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">داشبورد گزارشات مالی و عملکردی</h3>
            <p class="text-muted small mb-0">نمای کلی درآمدها، وضعیت پیشرفت پروژه‌ها و راندمان کاری پرسنل</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-medium no-print">
            <i class="fas fa-print me-1.5"></i> چاپ داشبورد
        </button>
    </div>

    <?php FlashMessage::display(); ?>

    <!-- Financial KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Revenue -->
        <div class="col-xl-4 col-md-6">
            <div class="kpi-fin-card d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-medium">درآمد کل (فاکتور شده)</span>
                    <h3 class="fw-bold text-dark mb-0 mt-1"><?php echo number_format($invoiceSummary->total_revenue ?? 0); ?> <span class="fs-6 font-normal text-muted">تومان</span></h3>
                </div>
                <div class="kpi-fin-icon bg-soft-primary">
                    <i class="fas fa-chart-bar"></i>
                </div>
            </div>
        </div>

        <!-- Paid Revenue -->
        <div class="col-xl-4 col-md-6">
            <div class="kpi-fin-card d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-medium">مبلغ پرداخت شده (وصولی)</span>
                    <h3 class="fw-bold text-success mb-0 mt-1"><?php echo number_format($invoiceSummary->paid_revenue ?? 0); ?> <span class="fs-6 font-normal text-muted">تومان</span></h3>
                </div>
                <div class="kpi-fin-icon bg-soft-success">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <!-- Unpaid Revenue -->
        <div class="col-xl-4 col-md-12">
            <div class="kpi-fin-card d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small fw-medium">مبلغ پرداخت نشده (بدهی)</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1"><?php echo number_format($invoiceSummary->unpaid_revenue ?? 0); ?> <span class="fs-6 font-normal text-muted">تومان</span></h3>
                </div>
                <div class="kpi-fin-icon bg-soft-warning">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row: Projects Summary & Employee Performance -->
    <div class="row g-4">
        <!-- Project Status Summary -->
        <div class="col-lg-6">
            <div class="ultra-card">
                <div class="ultra-card-header d-flex align-items-center">
                    <i class="fas fa-project-diagram text-primary fs-5 me-2"></i>
                    <h5 class="mb-0 fw-bold fs-6 text-dark">خلاصه وضعیت پروژه‌ها</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush list-group-ultra">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-medium text-dark"><i class="fas fa-spinner text-info me-2"></i> در حال انجام</span>
                            <span class="badge bg-soft-info px-3 py-1.5 rounded-pill fw-bold"><?php echo $projectSummary['in_progress']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-medium text-dark"><i class="fas fa-check text-success me-2"></i> تکمیل شده</span>
                            <span class="badge bg-soft-success px-3 py-1.5 rounded-pill fw-bold"><?php echo $projectSummary['finished']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-medium text-dark"><i class="fas fa-hourglass text-muted me-2"></i> شروع نشده</span>
                            <span class="badge bg-secondary px-3 py-1.5 rounded-pill fw-bold text-white"><?php echo $projectSummary['not_started']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-medium text-dark"><i class="fas fa-pause text-warning me-2"></i> متوقف شده</span>
                            <span class="badge bg-soft-warning px-3 py-1.5 rounded-pill fw-bold"><?php echo $projectSummary['on_hold']; ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Employee Task Summary -->
        <div class="col-lg-6">
            <div class="ultra-card">
                <div class="ultra-card-header d-flex align-items-center">
                    <i class="fas fa-users-cog text-primary fs-5 me-2"></i>
                    <h5 class="mb-0 fw-bold fs-6 text-dark">عملکرد کارمندان (تعداد وظایف محوله)</h5>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive" style="max-height: 260px; overflow-y: auto;">
                        <table class="table table-ultra-emp align-middle">
                            <tbody>
                                <?php if (!empty($userTaskSummary)): ?>
                                    <?php foreach($userTaskSummary as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-soft rounded-circle bg-light p-2 me-2 text-center" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user text-muted small"></i>
                                                </div>
                                                <span class="fw-bold text-dark"><?php echo sanitize($user->user_name); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-soft-primary px-3 py-1.5 rounded-pill fw-bold">
                                                <?php echo $user->assigned_tasks; ?> وظیفه
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center py-4 text-muted small">هیچ آماری از وظایف پرسنل یافت نشد.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>