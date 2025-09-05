<?php
// app/views/admin/inventory/sales/index.php
global $auth;
$sales = $sales ?? [];

function getStatusBadgeColor($status) {
    switch ($status) {
        case 'paid':
        case 'completed':
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

<h1 class="h3 mb-4 text-gray-800">مدیریت فروش‌ها</h1>

<?php FlashMessage::display(); ?>

<div class="card card-modern mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0 text-primary">لیست فاکتورهای فروش</h5>
        <a href="<?php echo APP_URL; ?>/index.php?page=sales&action=create" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-plus fa-sm"></i> ثبت فروش جدید
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive table-responsive-modern">
            <table class="table table-hover table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>مشتری</th>
                        <th>تاریخ فروش</th>
                        <th>مبلغ کل</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sales)): ?>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sale->id ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($sale->client_name ?? 'نامشخص'); ?></td>
                                <td>
                                    <?php
                                        echo ($sale->sale_date ?? '') ?
                                             htmlspecialchars(jdate('Y/m/d', strtotime($sale->sale_date))) :
                                             'نامشخص';
                                    ?>
                                </td>
                                <td><?php echo number_format($sale->total_amount ?? 0); ?></td>
                                <td>
                                    <?php
                                    $status = $sale->status ?? 'unknown';
                                    $badgeColor = getStatusBadgeColor($status);
                                    ?>
                                    <span class="badge bg-<?php echo $badgeColor; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td>
                                    <div class="btn-group action-buttons" role="group">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=sales&action=view&id=<?php echo htmlspecialchars($sale->id ?? ''); ?>" class="btn btn-sm btn-info" title="مشاهده">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=sales&action=updateStatus&id=<?php echo htmlspecialchars($sale->id ?? ''); ?>" method="POST" class="d-inline-block">
                                            <input type="hidden" name="status" value="completed">
                                            <?php if ($sale->status !== 'completed'): ?>
                                                <button type="submit" class="btn btn-sm btn-success" title="تکمیل و ارسال به حسابداری" onclick="return confirm('آیا از تکمیل این فاکتور و ارسال به حسابداری اطمینان دارید؟');">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">هیچ فاکتور فروشی یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>