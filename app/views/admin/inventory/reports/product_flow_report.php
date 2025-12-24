<?php
// app/views/admin/inventory/reports/product_flow_report.php
$product = $product ?? null;
$logs = $logs ?? [];
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">گزارش گردش محصول: <?php echo htmlspecialchars($product->name ?? 'نامشخص'); ?></h1>
</div>

<?php echo FlashMessage::display(); ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">جزئیات تراکنش‌های محصول</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>نوع</th>
                        <th>مقدار</th>
                        <th>تاریخ</th>
                        <th>انبار</th>
                        <th>کاربر ثبت کننده</th>
                        <th>منبع (سند #)</th>
                        <th>طرف حساب</th>
                        <th class="text-center">رسید</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-<?php echo $log->log_type === 'in' ? 'success' : 'danger'; ?>">
                                        <?php echo $log->log_type === 'in' ? 'ورود' : 'خروج'; ?>
                                    </span>
                                </td>
                                <td><?php echo number_format($log->quantity); ?></td>
                                <td><?php echo jdate('Y/m/d H:i', strtotime($log->created_at)); ?></td>
                                <td><?php echo htmlspecialchars($log->warehouse_name ?? 'نامشخص'); ?></td>
                                <td><?php echo htmlspecialchars($log->user_name ?? 'سیستم'); ?></td>
                                <td><?php echo htmlspecialchars($log->source_type ?? 'موجودی اولیه'); ?> #<?php echo htmlspecialchars($log->source_id); ?></td>
                                <td><?php echo htmlspecialchars($log->related_entity_name ?? '---'); ?></td>
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
                                        <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=<?php echo $print_action; ?>&id=<?php echo $log->source_id; ?>" target="_blank" class="btn btn-sm btn-secondary" title="چاپ رسید">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    <?php else: ?>
                                        ---
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">هیچ تراکنشی برای این محصول یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
