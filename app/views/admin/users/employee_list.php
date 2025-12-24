<?php
// app/views/admin/users/employee_list.php
$employees = $employees ?? [];
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">لیست کارمندان فعال</h1>
</div>

<?php echo FlashMessage::display(); ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">کارمندان</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>نام کارمند</th>
                        <th>ایمیل</th>
                        <th>نقش/سمت</th>
                        <th>تاریخ استخدام</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($employees)): ?>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($employee->name); ?></td>
                                <td><?php echo htmlspecialchars($employee->email); ?></td>
                                <td><?php echo htmlspecialchars($employee->organizational_position ?? $employee->role); ?></td>
                                <td><?php echo $employee->hire_date ? jdate('Y/m/d', strtotime($employee->hire_date)) : '---'; ?></td>
                                <td class="text-center">
                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=employee_performance_report&id=<?php echo $employee->id; ?>" class="btn btn-sm btn-info" title="گزارش عملکرد">
                                        <i class="fas fa-chart-bar"></i> گزارش عملکرد
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">هیچ کارمندی یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
