<?php
// app/views/admin/customers/index.php
?>
<h1 class="mb-4">مدیریت مشتریان</h1>

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">لیست مشتریان</h5>
        <a href="<?php echo APP_URL; ?>/admin/customers/create" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> افزودن مشتری جدید
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نام</th>
                        <th>ایمیل</th>
                        <th>تلفن</th>
                        <th>آدرس</th>
                        <th>تاریخ ثبت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo sanitize($customer->id); ?></td>
                                <td><?php echo sanitize($customer->name); ?></td>
                                <td><?php echo sanitize($customer->email); ?></td>
                                <td><?php echo sanitize($customer->phone); ?></td>
                                <td><?php echo sanitize($customer->address); ?></td>
                                <td><?php echo sanitize(date('Y/m/d', strtotime($customer->created_at))); ?></td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/admin/customers/edit/<?php echo $customer->id; ?>" class="btn btn-sm btn-info" title="ویرایش">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo APP_URL; ?>/admin/customers/delete/<?php echo $customer->id; ?>" method="POST" class="d-inline-block">
                                        <button type="button" class="btn btn-sm btn-danger" data-confirm-delete title="حذف">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">هیچ مشتری یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>