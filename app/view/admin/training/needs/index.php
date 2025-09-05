<?php
// app/views/admin/training/needs/index.php
?>
<h1 class="mb-4">نیازسنجی آموزشی</h1>
<div class="card shadow-sm">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">درخواست‌های در انتظار بررسی</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>کارمند</th>
                        <th>سال</th>
                        <th>تاریخ ارسال</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pendingNeeds)): ?>
                        <?php foreach ($pendingNeeds as $need): ?>
                            <tr>
                                <td><?php echo sanitize($need->employee_name); ?></td>
                                <td><?php echo sanitize($need->year); ?></td>
                                <td><?php echo jdate('Y/m/d', strtotime($need->created_at)); ?></td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_training_need&id=<?php echo $need->id; ?>" class="btn btn-sm btn-info">بررسی</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">هیچ درخواست نیازسنجی در انتظار بررسی وجود ندارد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
