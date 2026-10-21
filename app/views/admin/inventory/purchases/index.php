<?php
// app/views/admin/inventory/purchases/index.php
global $auth;
$purchases = $purchases ?? [];

function getStatusBadgeDetails($status) {
    switch ($status) {
        case 'paid':
            return ['bg' => 'bg-soft-success', 'text' => 'text-success', 'label' => 'پرداخت شده'];
        case 'pending':
            return ['bg' => 'bg-soft-warning', 'text' => 'text-warning', 'label' => 'در انتظار'];
        case 'canceled':
            return ['bg' => 'bg-soft-danger', 'text' => 'text-danger', 'label' => 'لغو شده'];
        default:
            return ['bg' => 'bg-soft-secondary', 'text' => 'text-secondary', 'label' => 'نامشخص'];
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">

<style>
    .bg-soft-success { background-color: #dcfce7; }
    .text-success { color: #15803d !important; }
    .bg-soft-warning { background-color: #fef3c7; }
    .text-warning { color: #b45309 !important; }
    .bg-soft-danger { background-color: #fee2e2; }
    .text-danger { color: #b91c1c !important; }
    .bg-soft-secondary { background-color: #f1f5f9; }
    .text-secondary { color: #475569 !important; }

    .ultra-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
    }

    .table-ultra {
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .table-ultra tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-ultra tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra td, .table-ultra th {
        padding: 1rem;
        border: none;
        vertical-align: middle;
    }

    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .action-btn:hover {
        transform: scale(1.08);
    }
</style>

<div class="py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">مدیریت فاکتورهای خرید</h3>
            <p class="text-muted small mb-0">لیست تمامی فاکتورها و وضعیت پرداخت آن‌ها</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=purchases&action=create" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm">
            <i class="fas fa-plus me-1"></i> ثبت خرید جدید
        </a>
    </div>

    <?php FlashMessage::display(); ?>

    <div class="ultra-card p-4">
        <div class="table-responsive">
            <table class="table table-ultra align-middle">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>شناسه</th>
                        <th>فروشنده</th>
                        <th>تاریخ خرید</th>
                        <th>مبلغ کل (تومان)</th>
                        <th>وضعیت</th>
                        <th class="text-end">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($purchases)): ?>
                        <?php foreach ($purchases as $purchase): ?>
                            <?php $badge = getStatusBadgeDetails($purchase->status ?? ''); ?>
                            <tr>
                                <td class="fw-bold text-secondary">#<?php echo htmlspecialchars($purchase->id ?? ''); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($purchase->vendor_name ?? 'نامشخص'); ?></div>
                                </td>
                                <td class="text-muted">
                                    <i class="far fa-calendar me-1"></i>
                                    <?php 
                                        echo ($purchase->purchase_date ?? '') ? 
                                             htmlspecialchars(jdate('Y/m/d', strtotime($purchase->purchase_date))) : 
                                             'نامشخص';
                                    ?>
                                </td>
                                <td class="fw-bold"><?php echo number_format($purchase->total_amount ?? 0); ?></td>
                                <td>
                                    <span class="badge <?php echo $badge['bg'] . ' ' . $badge['text']; ?> px-3 py-2 rounded-pill fw-medium">
                                        <?php echo htmlspecialchars($badge['label']); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=purchases&action=view&id=<?php echo htmlspecialchars($purchase->id ?? ''); ?>" class="action-btn bg-soft-secondary text-secondary" title="مشاهده">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=purchases&action=edit&id=<?php echo htmlspecialchars($purchase->id ?? ''); ?>" class="action-btn bg-soft-warning text-warning" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=purchases&action=delete&id=<?php echo htmlspecialchars($purchase->id ?? ''); ?>" method="POST" class="d-inline">
                                            <button type="submit" class="action-btn bg-soft-danger text-danger border-0" title="حذف" onclick="return confirm('آیا از حذف این فاکتور اطمینان دارید؟');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fa-3x mb-3 text-light"></i>
                                <p class="mb-0">هیچ فاکتور خریدی ثبت نشده است.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>