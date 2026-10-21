<?php
// app/views/admin/inventory/reports/product_flow_report.php
$product = $product ?? null;
$logs = $logs ?? [];

// محاسبه خلاصه آمار گردش
$totalIn = 0;
$totalOut = 0;
$totalLogs = count($logs);

foreach ($logs as $log) {
    if (($log->log_type ?? '') === 'in') {
        $totalIn += $log->quantity ?? 0;
    } else {
        $totalOut += $log->quantity ?? 0;
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --danger-gradient: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
        --border-color: #e2e8f0;
        --surface-bg: #ffffff;
    }

    .report-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    /* KPI Summary Cards */
    .kpi-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.06);
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #ffffff;
    }

    /* Ultra Card & Table */
    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .table-ultra-flow {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 6px;
    }

    .table-ultra-flow th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .table-ultra-flow tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.01);
        transition: all 0.2s ease;
    }

    .table-ultra-flow tbody tr:hover {
        background-color: #f8fafc;
        transform: scale(1.002);
    }

    .table-ultra-flow td {
        padding: 14px 16px;
        vertical-align: middle;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    .table-ultra-flow td:first-child { border-top-right-radius: 10px; border-bottom-right-radius: 10px; border-right: 1px solid #f1f5f9; }
    .table-ultra-flow td:last-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; border-left: 1px solid #f1f5f9; }

    /* Soft Badges */
    .badge-soft-success { background-color: #dcfce7; color: #15803d; }
    .badge-soft-danger { background-color: #fee2e2; color: #b91c1c; }
    .badge-soft-secondary { background-color: #f1f5f9; color: #475569; }

    .action-print-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background-color: #f1f5f9;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        text-decoration: none;
    }

    .action-print-btn:hover {
        background-color: #6366f1;
        color: #ffffff;
        transform: scale(1.1);
    }
</style>

<div class="report-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">گزارش گردش کالا</h3>
            <p class="text-muted small mb-0">محصول: <strong class="text-dark fs-6"><?php echo htmlspecialchars($product->name ?? 'نامشخص'); ?></strong></p>
        </div>
        <a href="javascript:history.back()" class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-medium">
            <i class="fas fa-arrow-right me-1"></i> بازگشت
        </a>
    </div>

    <?php echo FlashMessage::display(); ?>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="kpi-card d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium">کل ورودی به انبار</span>
                    <h4 class="fw-bold text-success mb-0 mt-1"><?php echo number_format($totalIn); ?></h4>
                </div>
                <div class="kpi-icon" style="background: var(--success-gradient);">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium">کل خروجی از انبار</span>
                    <h4 class="fw-bold text-danger mb-0 mt-1"><?php echo number_format($totalOut); ?></h4>
                </div>
                <div class="kpi-icon" style="background: var(--danger-gradient);">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kpi-card d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-medium">تعداد کل تراکنش‌ها</span>
                    <h4 class="fw-bold text-dark mb-0 mt-1"><?php echo number_format($totalLogs); ?></h4>
                </div>
                <div class="kpi-icon" style="background: var(--primary-gradient);">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="ultra-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-list-alt me-2 text-primary"></i>ریز تراکنش‌های ثبت‌شده</h6>
        </div>

        <div class="table-responsive">
            <table class="table-ultra-flow align-middle">
                <thead>
                    <tr>
                        <th class="text-center">نوع</th>
                        <th>تعداد / مقدار</th>
                        <th>تاریخ و زمان</th>
                        <th>انبار مربوطه</th>
                        <th>کاربر ثبت‌کننده</th>
                        <th>منبع (سند)</th>
                        <th>طرف حساب</th>
                        <th class="text-center">رسید</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="badge <?php echo $log->log_type === 'in' ? 'badge-soft-success' : 'badge-soft-danger'; ?> px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas <?php echo $log->log_type === 'in' ? 'fa-arrow-down' : 'fa-arrow-up'; ?> me-1"></i>
                                        <?php echo $log->log_type === 'in' ? 'ورود' : 'خروج'; ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-dark"><?php echo number_format($log->quantity); ?></td>
                                <td class="text-muted small">
                                    <i class="far fa-clock me-1"></i>
                                    <?php echo jdate('Y/m/d H:i', strtotime($log->created_at)); ?>
                                </td>
                                <td class="fw-medium text-dark"><?php echo htmlspecialchars($log->warehouse_name ?? 'نامشخص'); ?></td>
                                <td>
                                    <span class="badge badge-soft-secondary px-2.5 py-1 rounded-2">
                                        <i class="far fa-user me-1"></i><?php echo htmlspecialchars($log->user_name ?? 'سیستم'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-medium"><?php echo htmlspecialchars($log->source_type ?? 'موجودی اولیه'); ?></span>
                                    <span class="text-muted small">#<?php echo htmlspecialchars($log->source_id); ?></span>
                                </td>
                                <td class="fw-medium text-dark"><?php echo htmlspecialchars($log->related_entity_name ?? '---'); ?></td>
                                <td class="text-center">
                                    <?php 
                                        $print_action = '';
                                        if ($log->source_type === 'purchase') {
                                            $print_action = 'print_purchase_receipt';
                                        } elseif ($log->source_type === 'sale') {
                                            $print_action = 'print_sale_receipt';
                                        }
                                    ?>
                                    <?php if (!empty($print_action)): ?>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=<?php echo $print_action; ?>&id=<?php echo $log->source_id; ?>" target="_blank" class="action-print-btn" title="چاپ رسید رسمی">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">---</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 text-light"></i>
                                <p class="mb-0">هیچ تراکنشی برای این کالا یافت نشد.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>