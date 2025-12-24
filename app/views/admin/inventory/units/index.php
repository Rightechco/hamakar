<?php
// app/views/admin/inventory/units/index.php
$units = $units ?? [];
?>
<style>
    .card-modern {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .table-responsive-modern {
        border-radius: 10px;
        overflow: hidden;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">واحدها</h1>
    <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=create_unit" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> افزودن واحد جدید
    </a>
</div>

<?php echo FlashMessage::display(); ?>

<div class="card card-modern shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">لیست واحدهای محصول</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive table-responsive-modern">
            <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>نام واحد</th>
                        <th>تاریخ ایجاد</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($units)): ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">هیچ واحدی یافت نشد.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($units as $unit): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($unit->name); ?></td>
                                <td><?php echo jdate('Y/m/d', strtotime($unit->created_at)); ?></td>
                                <td class="text-center">
                                    <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=edit_unit&id=<?php echo $unit->id; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo APP_URL; ?>/index.php?page=inventory&action=delete_unit" method="POST" class="d-inline">
                                        <input type="hidden" name="id" value="<?php echo $unit->id; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('آیا از حذف این واحد مطمئن هستید؟');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>