<?php
// app/views/admin/inventory/warehouses/index.php
$warehouses = $warehouses ?? [];
?>

<style>
    .product-table-card {
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
</style>

<h1 class="mb-4">لیست انبارها</h1>

<?php echo FlashMessage::display(); ?>

<div class="card product-table-card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
        <h5 class="m-0">لیست انبارهای موجود</h5>
        <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=create_warehouse" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> افزودن انبار جدید
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>نام انبار</th>
                        <th>مکان</th>
                        <th>تاریخ ایجاد</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($warehouses)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">هیچ انباری یافت نشد.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($warehouse->name); ?></td>
                                <td><?php echo htmlspecialchars($warehouse->location); ?></td>
                                <td><?php echo jdate('Y/m/d', strtotime($warehouse->created_at)); ?></td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=warehouse_report&warehouse_id=<?php echo $warehouse->id; ?>" class="btn btn-sm btn-secondary" title="گزارش موجودی">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=edit_warehouse&id=<?php echo $warehouse->id; ?>" class="btn btn-sm btn-info" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=inventory&action=delete_warehouse" method="POST" class="d-inline-block">
                                            <input type="hidden" name="id" value="<?php echo $warehouse->id; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('آیا از حذف این انبار مطمئن هستید؟');" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>