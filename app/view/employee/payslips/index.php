<h1 class="h3 mb-4 text-gray-800"><?php echo sanitize($title); ?></h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>دوره حقوق (سال/ماه)</th>
                        <th>خالص پرداختی</th>
                        <th>تاریخ صدور</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payslips as $payslip): ?>
                    <tr>
                        <td><?php echo $payslip->pay_period_year . ' / ' . jdate_words(['mm' => $payslip->pay_period_month])['mm']; ?></td>
                        <td><strong><?php echo number_format($payslip->net_pay); ?> تومان</strong></td>
                        <td><?php echo jdate('Y/m/d', strtotime($payslip->created_at)); ?></td>
                        <td>
                             <a href="<?php echo APP_URL; ?>/index.php?page=employee&action=view_my_payslip&id=<?php echo $payslip->id; ?>" class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-eye"></i> مشاهده و چاپ
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>