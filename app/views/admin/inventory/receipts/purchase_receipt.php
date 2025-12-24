<?php
// app/views/admin/inventory/receipts/purchase_receipt.php
$purchase = $purchase ?? null;
$items = $items ?? [];
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card my-4 border-0">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">رسید خرید</h4>
                            <p class="text-muted mb-0">شماره رسید: <?php echo htmlspecialchars($purchase->id ?? '---'); ?></p>
                        </div>
                        <div class="text-left">
                            <img src="<?php echo APP_URL; ?>/assets/img/company_logo.png" alt="لوگوی شرکت" style="max-height: 80px;">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>فروشنده:</strong>
                            <p><?php echo htmlspecialchars($purchase->vendor_name ?? '---'); ?></p>
                            <p>تلفن: <?php echo htmlspecialchars($purchase->vendor_phone ?? '---'); ?></p>
                            <p>آدرس: <?php echo htmlspecialchars($purchase->vendor_address ?? '---'); ?></p>
                        </div>
                        <div class="col-md-6 text-left">
                            <strong>اطلاعات خرید:</strong>
                            <p>تاریخ خرید: <?php echo jdate('Y/m/d', strtotime($purchase->purchase_date ?? date('Y-m-d'))); ?></p>
                            <p>وضعیت: <?php echo htmlspecialchars($purchase->status ?? '---'); ?></p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>محصول</th>
                                    <th>تعداد</th>
                                    <th>واحد</th>
                                    <th>قیمت واحد</th>
                                    <th>قیمت کل</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $grandTotal = 0; ?>
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $item): ?>
                                        <?php $itemTotal = $item->quantity * $item->price; $grandTotal += $itemTotal; ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item->product_name); ?></td>
                                            <td><?php echo number_format($item->quantity); ?></td>
                                            <td><?php echo htmlspecialchars($item->product_unit); ?></td>
                                            <td><?php echo number_format($item->price); ?></td>
                                            <td><?php echo number_format($itemTotal); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">هیچ آیتمی یافت نشد.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-left">جمع کل:</th>
                                    <th><?php echo number_format($grandTotal); ?></th>
                                </tr>
                                <?php if (isset($purchase->vat_amount) && $purchase->vat_amount > 0): ?>
                                    <tr>
                                        <th colspan="4" class="text-left">مالیات بر ارزش افزوده:</th>
                                        <th><?php echo number_format($purchase->vat_amount); ?></th>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <th colspan="4" class="text-left">مبلغ نهایی:</th>
                                    <th><?php echo number_format($purchase->total_amount ?? $grandTotal); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>