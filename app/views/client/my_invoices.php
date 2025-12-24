<?php
?>
<h1 class="mb-4">فاکتورهای من</h1>

<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0">لیست فاکتورهای شما</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>شماره فاکتور</th>
                        <th>قرارداد مرتبط</th>
                        <th>تاریخ صدور</th>
                        <th>سررسید</th>
                        <th>مبلغ کل</th>
                        <th>پرداخت شده</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($invoices)): ?>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td><?php echo sanitize($invoice->id); ?></td>
                                <td><?php echo sanitize($invoice->invoice_number); ?></td>
                                <td><?php echo sanitize($invoice->contract_title ?? '---'); ?></td>
                                {{-- ✅ استفاده از فیلدهای تبدیل شده به شمسی از کنترلر --}}
                                <td><?php echo sanitize($invoice->issue_date_jalali); ?></td>
                                <td><?php echo sanitize($invoice->due_date_jalali); ?></td>
                                <td><?php echo number_format(sanitize($invoice->total_amount)); ?> تومان</td>
                                <td><?php echo number_format(sanitize($invoice->paid_amount)); ?> تومان</td>
                                <td><span class="badge bg-<?php 
                                    if ($invoice->status == 'paid') echo 'success';
                                    else if ($invoice->status == 'pending') echo 'warning';
                                    else if ($invoice->status == 'partial') echo 'info';
                                    else if ($invoice->status == 'overdue') echo 'danger';
                                    else if ($invoice->status == 'canceled') echo 'secondary';
                                    else echo 'secondary';
                                ?>"><?php echo sanitize($invoice->status); ?></span></td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/index.php?page=client&action=view_invoice&id=<?php echo $invoice->id; ?>" class="btn btn-sm btn-primary" title="مشاهده/پرداخت">
                                        <i class="fas fa-eye"></i> مشاهده
                                    </a>
                                    <?php if ($invoice->status != 'paid' && $invoice->status != 'canceled'): ?>
                                    <a href="<?php echo APP_URL; ?>/index.php?page=client&action=invoice_pay&id=<?php echo $invoice->id; ?>" class="btn btn-sm btn-success mt-1" title="پرداخت آنلاین">
                                        <i class="fas fa-money-bill-wave"></i> پرداخت
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">هیچ فاکتوری یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>