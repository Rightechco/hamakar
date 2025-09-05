<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?php echo sanitize($title); ?></h1>
    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=create_payroll" class="btn btn-primary">صدور فیش جدید</a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>کارمند</th>
                        <th>دوره حقوق (سال/ماه)</th>
                        <th>جمع درآمد</th>
                        <th>جمع کسورات</th>
                        <th>خالص پرداختی</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payrolls as $payroll): ?>
                    <tr>
                        <td><?php echo sanitize($payroll->employee_name); ?></td>
                        <td><?php echo $payroll->pay_period_year . ' / ' . jdate_words(['mm' => $payroll->pay_period_month])['mm']; ?></td>
                        <td><?php echo number_format($payroll->gross_earnings); ?> تومان</td>
                        <td><?php echo number_format($payroll->total_deductions); ?> تومان</td>
                        <td><strong><?php echo number_format($payroll->net_pay); ?> تومان</strong></td>
                        <td>
                            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_payslip&id=<?php echo $payroll->id; ?>" class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-eye"></i> مشاهده
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>