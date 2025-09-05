<?php
// app/views/admin/reports/index.php
?>

<h1 class="mb-4">داشبورد گزارشات</h1>

<div class="row">
    <div class="col-12">
        <h4 class="mb-3">گزارش مالی کلی (فاکتورها)</h4>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">درآمد کل (فاکتور شده)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($invoiceSummary->total_revenue ?? 0); ?> تومان</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-calendar fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">مبلغ پرداخت شده</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($invoiceSummary->paid_revenue ?? 0); ?> تومان</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">مبلغ پرداخت نشده</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($invoiceSummary->unpaid_revenue ?? 0); ?> تومان</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-comments-dollar fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<hr class="my-4">

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">خلاصه وضعیت پروژه‌ها</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">در حال انجام <span class="badge bg-info rounded-pill"><?php echo $projectSummary['in_progress']; ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">تکمیل شده <span class="badge bg-success rounded-pill"><?php echo $projectSummary['finished']; ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">شروع نشده <span class="badge bg-secondary rounded-pill"><?php echo $projectSummary['not_started']; ?></span></li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">متوقف شده <span class="badge bg-warning rounded-pill"><?php echo $projectSummary['on_hold']; ?></span></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">عملکرد کارمندان (تعداد وظایف محول شده)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tbody>
                            <?php foreach($userTaskSummary as $user): ?>
                            <tr>
                                <td><?php echo sanitize($user->user_name); ?></td>
                                <td class="text-end"><strong><?php echo $user->assigned_tasks; ?></strong> وظیفه</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>