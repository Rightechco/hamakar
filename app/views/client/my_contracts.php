<?php
// app/views/client/my_contracts.php
?>
<h1 class="mb-4">قراردادهای من</h1>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0">لیست قراردادهای شما</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان</th>
                        <th>نوع خدمات</th>
                        <th>مبلغ کل</th>
                        <th>تاریخ شروع</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($contracts)): ?>
                        <?php foreach ($contracts as $contract): ?>
                            <tr>
                                <td><?php echo sanitize($contract->id); ?></td>
                                <td><?php echo sanitize($contract->title); ?></td>
                                <td><?php echo sanitize($contract->service_type); ?></td>
                                <td><?php echo number_format(sanitize($contract->total_amount)); ?> تومان</td>
                                <td><?php echo sanitize(date('Y/m/d', strtotime($contract->start_date))); ?></td>
                                <td><span class="badge bg-<?php 
                                    if ($contract->status == 'active') echo 'success';
                                    else if ($contract->status == 'pending') echo 'warning';
                                    else if ($contract->status == 'completed') echo 'info';
                                    else if ($contract->status == 'canceled') echo 'danger';
                                    else echo 'secondary';
                                ?>"><?php echo sanitize($contract->status); ?></span></td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/index.php?page=client&action=view_contract&id=<?php echo $contract->id; ?>" target="_blank" class="btn btn-sm btn-primary" title="مشاهده/پرینت">
                                        <i class="fas fa-eye"></i> مشاهده
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">هنوز قراردادی برای شما ثبت نشده است.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>