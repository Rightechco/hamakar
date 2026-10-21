<?php
// app/views/admin/inventory/sales/index.php
global $auth;
$sales = $sales ?? [];

function getSaleStatusBadgeDetails($status) {
    switch (strtolower($status)) {
        case 'paid':
        case 'completed':
            return ['bg' => 'bg-soft-success', 'text' => 'text-success', 'label' => 'تکمیل شده'];
        case 'pending':
            return ['bg' => 'bg-soft-warning', 'text' => 'text-warning', 'label' => 'در انتظار'];
        case 'canceled':
            return ['bg' => 'bg-soft-danger', 'text' => 'text-danger', 'label' => 'لغو شده'];
        default:
            return ['bg' => 'bg-soft-secondary', 'text' => 'text-secondary', 'label' => $status ?: 'نامشخص'];
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

    .table-ultra-sale {
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .table-ultra-sale tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-ultra-sale tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra-sale td, .table-ultra-sale th {
        padding: 1rem;
        border: none;
        vertical-align: middle;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .action-btn:hover {
        transform: scale(1.08);
    }
</style>

<div class="py-3" style="direction: rtl; font-family: 'Vazirmatn', sans-serif;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">مدیریت فاکتورهای فروش</h3>
            <p class="text-muted small mb-0">لیست تمامی فاکتورهای فروش، وضعیت صادرشده و انتقال به حسابداری</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=sales&action=create" class="btn btn-success px-4 py-2 rounded-3 shadow-sm fw-medium">
            <i class="fas fa-plus me-1"></i> ثبت فروش جدید
        </a>
    </div>

    <?php FlashMessage::display(); ?>

    <div class="ultra-card p-4">
        <div class="table-responsive">
            <table class="table table-ultra-sale align-middle">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>شماره فاکتور</th>
                        <th>نام مشتری</th>
                        <th>تاریخ فروش</th>
                        <th>مبلغ کل (تومان)</th>
                        <th>وضعیت</th>
                        <th class="text-end">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sales)): ?>
                        <?php foreach ($sales as $sale): ?>
                            <?php $badge = getSaleStatusBadgeDetails($sale->status ?? ''); ?>
                            <tr>
                                <td class="fw-bold text-secondary">#<?php echo htmlspecialchars($sale->id ?? ''); ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($sale->client_name ?? 'نامشخص'); ?></div>
                                </td>
                                <td class="text-muted">
                                    <i class="far fa-calendar me-1"></i>
                                    <?php
                                        echo ($sale->sale_date ?? '') ?
                                             htmlspecialchars(jdate('Y/m/d', strtotime($sale->sale_date))) :
                                             'نامشخص';
                                    ?>
                                </td>
                                <td class="fw-bold text-dark"><?php echo number_format($sale->total_amount ?? 0); ?></td>
                                <td>
                                    <span class="badge <?php echo $badge['bg'] . ' ' . $badge['text']; ?> px-3 py-2 rounded-pill fw-medium">
                                        <?php echo htmlspecialchars($badge['label']); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=sales&action=view&id=<?php echo htmlspecialchars($sale->id ?? ''); ?>" class="action-btn bg-soft-secondary text-secondary" title="مشاهده فاکتور">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (($sale->status ?? '') !== 'completed'): ?>
                                            <form action="<?php echo APP_URL; ?>/index.php?page=sales&action=updateStatus&id=<?php echo htmlspecialchars($sale->id ?? ''); ?>" method="POST" class="d-inline">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="action-btn bg-soft-success text-success border-0" title="تکمیل و ارسال به حسابداری" onclick="return confirm('آیا از تکمیل این فاکتور و ارسال به حسابداری اطمینان دارید؟');">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-shopping-bag fa-3x mb-3 text-light"></i>
                                <p class="mb-0">هیچ فاکتور فروشی ثبت نشده است.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>