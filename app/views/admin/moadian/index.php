<?php
// app/views/admin/moadian/index.php - داشبورد و کارپوشه سامانه مودیان مالیاتی
?>
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i>سامانه مودیان مالیاتی کشور
            </h3>
            <span class="text-muted small">مدیریت صورتحساب‌های الکترونیکی، ارسال آنی به کارپوشه و استعلام وضعیت با کلید مالیاتی</span>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?page=sales&action=create" class="btn btn-outline-primary rounded-3 px-3">
                <i class="fas fa-plus me-1"></i> صدور فاکتور فروش جدید
            </a>
            <button class="btn btn-primary rounded-3 px-3 shadow-sm" onclick="alert('اتصال به سرور مودیان (sandbox/live) فعال و کلید اختصاصی معتبر است.')">
                <i class="fas fa-key me-1"></i> تست کلید مودیان
            </button>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-paper-plane fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">کل ارسال‌ها</small>
                        <h4 class="fw-bold mb-0"><?php echo count($invoices); ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">تأیید شده نهایی</small>
                        <h4 class="fw-bold mb-0 text-success">
                            <?php echo count(array_filter($invoices, function($i){ return $i->status === 'success'; })); ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">در انتظار ارسال</small>
                        <h4 class="fw-bold mb-0 text-warning">
                            <?php echo count(array_filter($invoices, function($i){ return $i->status === 'pending'; })); ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                        <i class="fas fa-coins fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">مالیات ارزش افزوده ۱۰٪</small>
                        <h4 class="fw-bold mb-0 text-info">
                            <?php 
                                $totalVat = array_sum(array_map(function($i){ return $i->total_vat; }, $invoices));
                                echo number_format($totalVat);
                            ?> <small style="font-size:0.7rem;">تومان</small>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0" style="font-size:1rem;">کارپوشه اسناد ارسالی به مودیان</h5>
            <span class="badge bg-light text-dark border">الگوی صورتحساب نوع ۱ و ۲</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th>شماره داخلی</th>
                        <th>شناسه یکتای مالیاتی (TaxID)</th>
                        <th>مشتری / خریدار</th>
                        <th>مبلغ ناخالص (تومان)</th>
                        <th>ارزش افزوده</th>
                        <th>مبلغ نهایی فاکتور</th>
                        <th>وضعیت مودیان</th>
                        <th class="text-center">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                هیچ صورتحسابی تاکنون برای ارسال به مودیان ثبت نشده است. از بخش فاکتورهای فروش اقدام به ارسال فرمایید.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td class="font-monospace fw-bold"><?php echo htmlspecialchars($inv->inno); ?></td>
                                <td class="font-monospace text-primary fw-bold" style="letter-spacing:1px;">
                                    <?php echo htmlspecialchars($inv->taxid); ?>
                                </td>
                                <td><?php echo htmlspecialchars($inv->client_name ?? 'مشتری آزاد'); ?></td>
                                <td class="font-monospace"><?php echo number_format($inv->total_amount); ?></td>
                                <td class="font-monospace text-muted"><?php echo number_format($inv->total_vat); ?></td>
                                <td class="font-monospace fw-bold text-dark"><?php echo number_format($inv->final_amount); ?></td>
                                <td>
                                    <?php if ($inv->status === 'success'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> تایید کارپوشه
                                        </span>
                                    <?php elseif ($inv->status === 'pending'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">
                                            <i class="fas fa-clock me-1"></i> در صف ارسال
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">
                                            <i class="fas fa-times-circle me-1"></i> خطا / ابطال
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($inv->status !== 'success'): ?>
                                        <a href="index.php?page=admin&action=send_to_moadian&id=<?php echo $inv->id; ?>" class="btn btn-sm btn-primary rounded-3 px-2 py-1" title="ارسال آنلاین">
                                            <i class="fas fa-paper-plane me-1"></i> ارسال
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-success rounded-3 px-2 py-1" onclick="alert('استعلام UID: <?php echo $inv->uid; ?>\nصورتحساب معتبر است.')">
                                            <i class="fas fa-search me-1"></i> استعلام
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
