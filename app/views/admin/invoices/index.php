<?php
// app/views/admin/invoices/index.php
global $auth;
$invoices = $invoices ?? [];
$clients = $clients ?? [];
$filters = $filters ?? [];
$invoiceTypes = $invoiceTypes ?? [];

function getInvoiceStatusBadgeDetails($status) {
    switch ($status) {
        case 'paid':
        case 'completed':
            return ['bg' => 'bg-soft-success', 'text' => 'text-success', 'label' => 'پرداخت شده'];
        case 'pending':
            return ['bg' => 'bg-soft-warning', 'text' => 'text-warning', 'label' => 'در انتظار'];
        case 'overdue':
            return ['bg' => 'bg-soft-danger', 'text' => 'text-danger', 'label' => 'سررسید گذشته'];
        case 'canceled':
            return ['bg' => 'bg-soft-secondary', 'text' => 'text-secondary', 'label' => 'لغو شده'];
        default:
            return ['bg' => 'bg-soft-info', 'text' => 'text-info', 'label' => $status ?: 'نامشخص'];
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
    }

    body, .invoices-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .bg-soft-success { background-color: #dcfce7; }
    .text-success { color: #15803d !important; }
    .bg-soft-warning { background-color: #fef3c7; }
    .text-warning { color: #b45309 !important; }
    .bg-soft-danger { background-color: #fee2e2; }
    .text-danger { color: #b91c1c !important; }
    .bg-soft-secondary { background-color: #f1f5f9; }
    .text-secondary { color: #475569 !important; }
    .bg-soft-info { background-color: #e0f2fe; }
    .text-info { color: #0369a1 !important; }

    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
    }

    /* Floating Row Table */
    .table-ultra-invoice {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-ultra-invoice tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-ultra-invoice tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra-invoice td, .table-ultra-invoice th {
        padding: 1rem 1.25rem;
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

<div class="invoices-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">مدیریت فاکتورها</h3>
            <p class="text-muted small mb-0">لیست کلیه فاکتورهای فروش، خرید، قراردادها و وضعیت تسویه</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices_create" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-medium">
            <i class="fas fa-plus me-1"></i> صدور فاکتور جدید
        </a>
    </div>

    <?php FlashMessage::display(); ?>

    <div class="ultra-card p-4">
        <!-- Filter Form -->
        <form id="filter-form" class="mb-4 p-3 border rounded-3 bg-light" action="<?php echo APP_URL; ?>/index.php" method="GET">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="action" value="invoices">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="search" class="form-label text-muted small fw-medium">جستجو:</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="شماره فاکتور یا نام خریدار..." value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <label for="client_id" class="form-label text-muted small fw-medium">مشتری / فروشنده:</label>
                    <select class="form-select" id="client_id" name="client_id">
                        <option value="">همه اشخاص</option>
                        <?php foreach($clients as $client): ?>
                            <option value="<?php echo $client->id; ?>" <?php echo (isset($filters['client_id']) && $filters['client_id'] == $client->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($client->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="invoice_type" class="form-label text-muted small fw-medium">نوع فاکتور:</label>
                    <select class="form-select" id="invoice_type" name="invoice_type">
                        <option value="">همه انواع</option>
                        <?php foreach($invoiceTypes as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo (isset($filters['invoice_type']) && $filters['invoice_type'] == $key) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <button type="submit" class="btn btn-primary px-3 me-1">اعمال</button>
                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices" class="btn btn-outline-secondary px-3">پاک کردن</a>
                </div>
            </div>
        </form>

        <!-- Invoices Table -->
        <div class="table-responsive">
            <table class="table table-ultra-invoice align-middle">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>شماره فاکتور</th>
                        <th>طرف حساب</th>
                        <th>نوع</th>
                        <th>شرح / مربوط به</th>
                        <th>تاریخ</th>
                        <th>مبلغ کل (تومان)</th>
                        <th>وضعیت</th>
                        <th class="text-end">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($invoices)): ?>
                        <?php foreach ($invoices as $invoice): ?>
                            <?php $badge = getInvoiceStatusBadgeDetails($invoice->status ?? ''); ?>
                            <tr>
                                <td class="fw-bold">
                                    <?php
                                    $link = '';
                                    $number = '';
                                    if ($invoice->type === 'contract') {
                                        $link = APP_URL . '/index.php?page=admin&action=view_invoice&id=' . htmlspecialchars($invoice->id);
                                        $number = htmlspecialchars($invoice->invoice_number);
                                    } elseif ($invoice->type === 'sale') {
                                        $link = APP_URL . '/index.php?page=sales&action=view&id=' . htmlspecialchars($invoice->id);
                                        $number = 'فروش #' . htmlspecialchars($invoice->id);
                                    } elseif ($invoice->type === 'purchase') {
                                        $link = APP_URL . '/index.php?page=purchases&action=view&id=' . htmlspecialchars($invoice->id);
                                        $number = 'خرید #' . htmlspecialchars($invoice->id);
                                    }
                                    ?>
                                    <a href="<?php echo $link; ?>" class="text-decoration-none text-primary fw-bold"><?php echo $number; ?></a>
                                </td>
                                <td><div class="fw-bold text-dark"><?php echo htmlspecialchars($invoice->client_name ?? 'نامشخص'); ?></div></td>
                                <td>
                                    <span class="badge bg-soft-secondary text-secondary px-2.5 py-1 rounded-2">
                                        <?php 
                                            if ($invoice->type === 'contract') echo 'قرارداد';
                                            elseif ($invoice->type === 'sale') echo 'فروش';
                                            elseif ($invoice->type === 'purchase') echo 'خرید';
                                            else echo 'نامشخص';
                                        ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <?php
                                        if ($invoice->type === 'sale') {
                                            echo 'فروش کالا به ' . htmlspecialchars($invoice->client_name ?? '---');
                                        } elseif ($invoice->type === 'purchase') {
                                            echo 'خرید کالا از ' . htmlspecialchars($invoice->client_name ?? '---');
                                        } else {
                                            echo htmlspecialchars($invoice->contract_title ?? '---');
                                        }
                                    ?>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    <?php echo jdate('Y/m/d', strtotime($invoice->date)); ?>
                                </td>
                                <td class="fw-bold text-dark"><?php echo number_format($invoice->total_amount); ?></td>
                                <td>
                                    <span class="badge <?php echo $badge['bg'] . ' ' . $badge['text']; ?> px-3 py-1.5 rounded-pill fw-medium">
                                        <?php echo htmlspecialchars($badge['label']); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <?php 
                                        $viewAction = '';
                                        $deleteAction = '';
                                        $page = 'admin';

                                        if ($invoice->type === 'contract') {
                                            $viewAction = 'view_invoice';
                                            $deleteAction = 'invoices_delete';
                                        } elseif ($invoice->type === 'sale') {
                                            $page = 'sales';
                                            $viewAction = 'view';
                                            $deleteAction = 'delete';
                                        } elseif ($invoice->type === 'purchase') {
                                            $page = 'purchases';
                                            $viewAction = 'view';
                                            $deleteAction = 'delete';
                                        }
                                        ?>
                                        <?php if (!empty($viewAction)): ?>
                                            <a href="<?php echo APP_URL; ?>/index.php?page=<?php echo $page; ?>&action=<?php echo $viewAction; ?>&id=<?php echo htmlspecialchars($invoice->id); ?>" class="action-btn bg-soft-info text-info" title="مشاهده">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($deleteAction) && $auth->hasRole(['admin', 'accountant'])): ?>
                                            <form action="<?php echo APP_URL; ?>/index.php?page=<?php echo $page; ?>&action=<?php echo $deleteAction; ?>&id=<?php echo htmlspecialchars($invoice->id); ?>" method="POST" class="d-inline">
                                                <button type="submit" class="action-btn bg-soft-danger text-danger border-0" title="حذف" onclick="return confirm('آیا از حذف این فاکتور اطمینان دارید؟');">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-file-invoice fa-3x mb-3 text-light"></i>
                                <p class="mb-0">هیچ فاکتوری یافت نشد.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>