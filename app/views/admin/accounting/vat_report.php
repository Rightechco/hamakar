<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i>گزارش مالیات بر ارزش افزوده (VAT)
            </h3>
            <span class="text-muted small">محاسبه فصلی و دوره‌ای اعتبار و بدهی ارزش افزوده با تفکیک فاکتورهای رسمی سامانه مودیان و غیررسمی</span>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?page=admin&action=moadian_invoices" class="btn btn-outline-primary rounded-3 px-3">
                <i class="fas fa-shield-alt me-1"></i> کارپوشه سامانه مودیان
            </a>
            <a href="index.php?page=admin&action=invoices_create" class="btn btn-primary rounded-3 px-3 shadow-sm">
                <i class="fas fa-plus me-1"></i> صدور فاکتور جدید
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="fw-bold mb-0" style="font-size:1rem;">انتخاب دوره و محدوده گزارش</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">از تاریخ صدور:</label>
                        <input type="text" name="start_date" class="form-control form-control-sm persian-datepicker" value="<?php echo $startDateJalali ?? ''; ?>" placeholder="۱۴۰۳/۰۱/۰۱" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold">تا تاریخ صدور:</label>
                        <input type="text" name="end_date" class="form-control form-control-sm persian-datepicker" value="<?php echo $endDateJalali ?? ''; ?>" placeholder="۱۴۰۳/۱۲/۲۹" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label text-muted small fw-bold">نوع رسمیت فاکتورها:</label>
                        <select name="filter_type" class="form-select form-select-sm">
                            <option value="official" <?php echo (($filterType ?? 'official') === 'official') ? 'selected' : ''; ?>>
                                فقط فاکتورهای رسمی (سامانه مودیان)
                            </option>
                            <option value="all" <?php echo (($filterType ?? '') === 'all') ? 'selected' : ''; ?>>
                                کلیه فاکتورها (رسمی + غیررسمی)
                            </option>
                            <option value="unofficial" <?php echo (($filterType ?? '') === 'unofficial') ? 'selected' : ''; ?>>
                                فقط فاکتورهای غیررسمی (داخلی)
                            </option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="fas fa-search me-1"></i> گزارش
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($reportData)): ?>
    <!-- Summary KPIs -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-primary border-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-arrow-down fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">مالیات بر ارزش افزوده فروش (بستانکاری)</small>
                        <h4 class="fw-bold mb-0 text-primary"><?php echo number_format($reportData['sales_vat']); ?> <small style="font-size:0.75rem;">تومان</small></h4>
                        <small class="text-muted">تعداد: <?php echo number_format($reportData['inv_count'] ?? 0); ?> فاکتور</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start border-info border-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                        <i class="fas fa-arrow-up fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">اعتبار مالیاتی خریدها (بدهکاری)</small>
                        <h4 class="fw-bold mb-0 text-info"><?php echo number_format($reportData['purchase_vat']); ?> <small style="font-size:0.75rem;">تومان</small></h4>
                        <small class="text-muted">قابل تهاتر با دارایی</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-start <?php echo ($reportData['payable_vat'] >= 0) ? 'border-danger' : 'border-success'; ?> border-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 <?php echo ($reportData['payable_vat'] >= 0) ? 'bg-danger text-danger' : 'bg-success text-success'; ?> bg-opacity-10">
                        <i class="fas fa-balance-scale fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">مبلغ خالص ارزش افزوده دوره</small>
                        <h4 class="fw-bold mb-0 <?php echo ($reportData['payable_vat'] >= 0) ? 'text-danger' : 'text-success'; ?>">
                            <?php echo number_format(abs($reportData['payable_vat'])); ?> <small style="font-size:0.75rem;">تومان</small>
                        </h4>
                        <small class="fw-bold <?php echo ($reportData['payable_vat'] >= 0) ? 'text-danger' : 'text-success'; ?>">
                            <?php echo ($reportData['payable_vat'] >= 0) ? 'قابل پرداخت به سازمان امور مالیاتی' : 'بستانکار و قابل استرداد / انتقال به دوره بعد'; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Official Notice -->
    <div class="alert alert-info rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 p-3">
        <i class="fas fa-info-circle fa-2x text-primary"></i>
        <div>
            <div class="fw-bold">محدوده اعمال گزارش: 
                <span class="badge bg-primary text-white">
                    <?php 
                        if (($filterType ?? '') === 'unofficial') echo 'فقط فاکتورهای غیررسمی (داخلی)';
                        elseif (($filterType ?? '') === 'all') echo 'جامع (رسمی + غیررسمی)';
                        else echo 'فقط فاکتورهای رسمی منطبق بر کارپوشه سامانه مودیان';
                    ?>
                </span>
            </div>
            <small class="text-muted">مطابق قانون پایانه‌های فروشگاهی و سامانه مودیان، تنها فاکتورهای رسمی تایید شده در کارپوشه مشمول تهاتر اعتبار مالیاتی می‌باشند.</small>
        </div>
    </div>
    <?php endif; ?>
</div>
