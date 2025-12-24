<?php
// admin/invoices/view_invoice.php
// این ویو هم برای ادمین و هم برای مشتری استفاده می شود.
// متغیر $invoice شامل جزئیات فاکتور است.
// $invoice->issue_date_jalali و $invoice->due_date_jalali باید از کنترلر آمده باشند.
// $payments شامل لیست پرداخت‌های مرتبط است.
// $companyInfo شامل اطلاعات شرکت است.
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">جزئیات فاکتور شماره <?php echo sanitize($invoice->invoice_number ?? 'N/A'); ?></h5>
                    <a href="javascript:window.print()" class="btn btn-info btn-sm"><i class="fas fa-print"></i> چاپ</a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>اطلاعات شرکت:</h6>
                            <p class="mb-0"><strong><?php echo sanitize($companyInfo['name']); ?></strong></p>
                            <p class="mb-0"><?php echo sanitize($companyInfo['address']); ?></p>
                            <p class="mb-0">تلفن: <?php echo sanitize($companyInfo['phone']); ?></p>
                            <p class="mb-0">ایمیل: <?php echo sanitize($companyInfo['email']); ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6>اطلاعات مشتری:</h6>
                            <p class="mb-0">نام: <strong><?php echo sanitize($invoice->client_name ?? '---'); ?></strong></p>
                            <p class="mb-0">ایمیل: <?php echo sanitize($invoice->client_email ?? '---'); ?></p>
                            <p class="mb-0">تلفن: <?php echo sanitize($invoice->client_phone ?? '---'); ?></p>
                            <p class="mb-0">آدرس: <?php echo sanitize($invoice->client_address ?? '---'); ?></p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <p class="mb-0"><strong>شماره فاکتور:</strong> <?php echo sanitize($invoice->invoice_number); ?></p>
                            <p class="mb-0"><strong>تاریخ صدور:</strong> <?php echo sanitize($invoice->issue_date_jalali); // ✅ اصلاح شده ?></p>
                            <p class="mb-0"><strong>تاریخ سررسید:</strong> <?php echo sanitize($invoice->due_date_jalali); // ✅ اصلاح شده ?></p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0"><strong>وضعیت:</strong> 
                                <span class="badge bg-<?php 
                                    if ($invoice->status == 'paid') echo 'success';
                                    else if ($invoice->status == 'pending') echo 'warning';
                                    else if ($invoice->status == 'partial') echo 'info';
                                    else if ($invoice->status == 'overdue') echo 'danger';
                                    else if ($invoice->status == 'canceled') echo 'secondary';
                                    else echo 'secondary';
                                ?>"><?php echo sanitize($invoice->status); ?></span>
                            </p>
                            <p class="mb-0"><strong>قرارداد مرتبط:</strong> <?php echo sanitize($invoice->contract_title ?? '---'); ?></p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <p class="mb-0"><strong>مبلغ خالص:</strong> <?php echo number_format(sanitize($invoice->subtotal ?? 0)); ?> تومان</p>
                            <p class="mb-0"><strong>مالیات بر ارزش افزوده (<?php echo sanitize($invoice->vat_rate ?? 0); ?>%):</strong> <?php echo number_format(sanitize($invoice->vat_amount ?? 0)); ?> تومان</p>
                            <p class="mb-0 fs-5"><strong>مبلغ کل:</strong> <?php echo number_format(sanitize($invoice->total_amount ?? 0)); ?> تومان</p>
                            <p class="mb-0 text-success"><strong>پرداخت شده:</strong> <?php echo number_format(sanitize($invoice->paid_amount ?? 0)); ?> تومان</p>
                            <p class="mb-0 text-danger"><strong>باقیمانده:</strong> <?php echo number_format(sanitize(($invoice->total_amount ?? 0) - ($invoice->paid_amount ?? 0))); ?> تومان</p>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h6>توضیحات:</h6>
                        <p><?php echo nl2br(sanitize($invoice->description ?? '---')); ?></p>
                    </div>

                    <?php if (!empty($payments) && is_array($payments)): ?>
                    <div class="mb-4">
                        <h6>تاریخچه پرداخت‌ها:</h6>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($payments as $payment): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>تاریخ: <?php echo sanitize(jdate('Y/m/d', strtotime($payment->payment_date))); ?></span>
                                    <span>مبلغ: <?php echo number_format(sanitize($payment->amount)); ?> تومان</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if ($invoice->status != 'paid' && ($invoice->total_amount ?? 0) > ($invoice->paid_amount ?? 0)): ?>
                    <div class="text-center mt-4">
                        <a href="<?php echo APP_URL; ?>/index.php?page=client&action=invoice_pay&id=<?php echo $invoice->id; ?>" class="btn btn-success btn-lg">
                            <i class="fas fa-money-bill-wave"></i> پرداخت آنلاین فاکتور
                        </a>
                    </div>
                    <?php endif; ?>

                    <div class="row mt-5">
                        <div class="col-6 text-center">
                            <img src="<?php echo sanitize($companyInfo['signature_path']); ?>" alt="Company Signature" class="img-fluid" style="max-height: 80px;"><br>
                            <p class="border-top border-dark d-inline-block px-3 pt-1">امضاء و مهر شرکت</p>
                        </div>
                        <div class="col-6 text-center">
                            <img src="<?php echo sanitize($companyInfo['seal_path']); ?>" alt="Company Seal" class="img-fluid" style="max-height: 80px;"><br>
                            <p class="border-top border-dark d-inline-block px-3 pt-1">مهر مشتری</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>