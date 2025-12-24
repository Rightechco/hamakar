<?php
// app/views/admin/inventory/purchases/index.php
global $auth;
$purchases = $purchases ?? [];

// Helper function to get badge color based on status
function getStatusBadgeColor($status) {
    switch ($status) {
        case 'paid':
            return 'success';
        case 'pending':
            return 'warning';
        case 'canceled':
            return 'danger';
        default:
            return 'secondary';
    }
}
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
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .action-buttons .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }
</style>

<h1 class="h3 mb-4 text-gray-800">مدیریت خریدها</h1>

<?php FlashMessage::display(); ?>

<div class="card card-modern mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0 text-primary">لیست فاکتورهای خرید</h5>
        <a href="<?php echo APP_URL; ?>/index.php?page=purchases&action=create" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm"></i> ثبت خرید جدید
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive table-responsive-modern">
            <table class="table table-hover table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>فروشنده</th>
                        <th>تاریخ خرید</th>
                        <th>مبلغ کل</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($purchases)): ?>
                        <?php foreach ($purchases as $purchase): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($purchase->id ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($purchase->vendor_name ?? 'نامشخص'); ?></td>
                                <td>
                                    <?php 
                                        echo ($purchase->purchase_date ?? '') ? 
                                             htmlspecialchars(jdate('Y/m/d', strtotime($purchase->purchase_date))) : 
                                             'نامشخص';
                                    ?>
                                </td>
                                <td><?php echo number_format($purchase->total_amount ?? 0); ?></td>
                                <td>
                                    <?php
                                    $status = $purchase->status ?? 'unknown';
                                    $badgeColor = getStatusBadgeColor($status);
                                    ?>
                                    <span class="badge bg-<?php echo $badgeColor; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td>
                                    <div class="btn-group action-buttons" role="group">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=purchases&action=view&id=<?php echo htmlspecialchars($purchase->id ?? ''); ?>" class="btn btn-sm btn-info" title="مشاهده">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=purchases&action=edit&id=<?php echo htmlspecialchars($purchase->id ?? ''); ?>" class="btn btn-sm btn-warning" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=purchases&action=delete&id=<?php echo htmlspecialchars($purchase->id ?? ''); ?>" method="POST" class="d-inline-block">
                                            <button type="submit" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('آیا از حذف این فاکتور خرید اطمینان دارید؟');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">هیچ فاکتور خریدی یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>