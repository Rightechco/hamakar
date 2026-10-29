<?php
// app/views/admin/moadian/index.php - کارپوشه جامع، فوق پرمیوم و عملیاتی سامانه مودیان مالیاتی کشور
$invoices = $invoices ?? [];
$stats = $stats ?? null;
$settings = $settings ?? null;
$apiKey = $apiKey ?? null;
$currentStatus = $currentStatus ?? '';
$currentSubject = $currentSubject ?? '';
?>

<style>
.moadian-card {
    border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    background: #ffffff;
    transition: all 0.25s ease;
}
.moadian-card:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.07);
}
.taxid-pill {
    font-family: var(--font-monospace, 'Consolas', monospace);
    background: #f1f5f9;
    color: #0f172a;
    padding: 4px 10px;
    border-radius: 8px;
    letter-spacing: 0.5px;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid #e2e8f0;
}
.nav-pills-moadian .nav-link {
    border-radius: 12px;
    padding: 10px 20px;
    font-weight: 600;
    color: #64748b;
    border: 1px solid transparent;
    transition: all 0.2s ease;
}
.nav-pills-moadian .nav-link.active {
    background: #4f46e5;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
}
.nav-pills-moadian .nav-link:hover:not(.active) {
    background: #f8fafc;
    color: #1e293b;
}
.api-console-box {
    background: #0f172a;
    color: #38bdf8;
    border-radius: 14px;
    padding: 14px;
    font-family: 'Consolas', monospace;
    font-size: 0.82rem;
    direction: ltr;
    text-align: left;
    max-height: 280px;
    overflow-y: auto;
}
@media (max-width: 768px) {
    .page-header-actions {
        width: 100% !important;
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 0.45rem !important;
    }
    .page-header-actions .btn {
        flex: 1 1 calc(50% - 0.45rem) !important;
        font-size: 0.76rem !important;
        padding: 0.5rem 0.4rem !important;
        text-align: center !important;
        white-space: nowrap !important;
    }
    .moadian-card {
        border-radius: 16px !important;
        padding: 0.85rem 0.75rem !important;
    }
    .taxid-pill {
        max-width: 100% !important;
        font-size: 0.72rem !important;
        padding: 3px 6px !important;
    }
    .badge {
        white-space: normal !important;
        text-align: center !important;
        line-height: 1.35 !important;
    }
}
</style>

<div class="container-fluid py-3">
    <!-- Top Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                    <i class="fas fa-signal me-1"></i> اتصال مودیان: <?php echo (!empty($settings->is_sandbox)) ? 'Sandbox آزمایشی' : 'تولید زنده Live'; ?>
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">
                    شناسه حافظه: <?php echo htmlspecialchars($settings->memory_id ?? 'A100HK'); ?>
                </span>
            </div>
            <h3 class="fw-bold mb-1" style="color:#0f172a;">
                <i class="fas fa-shield-alt text-primary me-2"></i>کارپوشه سامانه مودیان مالیاتی کشور
            </h3>
            <span class="text-muted small">مدیریت صدور، امضای الکترونیک، ارسال خودکار به کارپوشه، ابطال و اصلاح صورتحساب‌های رسمی</span>
        </div>
        <div class="d-flex gap-2 flex-wrap page-header-actions">
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices_create" class="btn btn-primary rounded-3 px-3 shadow-sm">
                <i class="fas fa-plus me-1"></i> صدور فاکتور رسمی جدید
            </a>
            <button type="button" onclick="switchMoadianTab('settings-pane')" class="btn btn-outline-secondary rounded-3 px-3">
                <i class="fas fa-cog me-1"></i> تنظیمات کلیدها
            </button>
            <button type="button" onclick="switchMoadianTab('api-pane')" class="btn btn-outline-primary rounded-3 px-3">
                <i class="fas fa-code me-1"></i> وب‌سرویس API حسابداری
            </button>
        </div>
    </div>

    <?php FlashMessage::display(); ?>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="moadian-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-file-invoice fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold">کل صورتحساب‌ها</small>
                        <h4 class="fw-bold mb-0 text-dark"><?php echo number_format($stats->total_count ?? count($invoices)); ?></h4>
                        <small class="text-muted">ثبت شده در کارپوشه</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="moadian-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-check-double fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold">تأیید قطعی دارایی</small>
                        <h4 class="fw-bold mb-0 text-success">
                            <?php echo number_format($stats->success_count ?? count(array_filter($invoices, function($i){ return $i->status === 'success'; }))); ?>
                        </h4>
                        <small class="text-success">دارای کد پیگیری معتبر</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="moadian-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-clock fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold">در صف ارسال</small>
                        <h4 class="fw-bold mb-0 text-warning">
                            <?php echo number_format($stats->pending_count ?? count(array_filter($invoices, function($i){ return $i->status === 'pending'; }))); ?>
                        </h4>
                        <small class="text-muted">آماده ارسال آنی</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="moadian-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                        <i class="fas fa-percentage fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold">ارزش افزوده ۱۰٪</small>
                        <h4 class="fw-bold mb-0 text-info">
                            <?php 
                                $vatSum = $stats->total_vat ?? array_sum(array_map(function($i){ return $i->total_vat; }, $invoices));
                                echo number_format($vatSum); 
                            ?> <small style="font-size:0.7rem;">تومان</small>
                        </h4>
                        <small class="text-muted">مالیات قانونی متعلقه</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills nav-pills-moadian mb-4 gap-2 bg-white p-2 rounded-4 border shadow-sm" id="moadianTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="tab-invoices-btn" data-bs-toggle="pill" data-bs-target="#invoices-pane" type="button" role="tab">
                <i class="fas fa-list-alt me-1"></i> صورتحساب‌های الکترونیکی کارپوشه
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="tab-settings-btn" data-bs-toggle="pill" data-bs-target="#settings-pane" type="button" role="tab">
                <i class="fas fa-sliders-h me-1"></i> پیکربندی و کلیدهای سامانه مودیان
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="tab-api-btn" data-bs-toggle="pill" data-bs-target="#api-pane" type="button" role="tab">
                <i class="fas fa-network-wired me-1"></i> اتصال به نرم‌افزار حسابداری (API)
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content">
        <!-- 1. INVOICES TAB -->
        <div class="tab-pane fade show active" id="invoices-pane" role="tabpanel">
            <!-- Filter Bar -->
            <div class="moadian-card p-3 mb-4">
                <form method="GET" action="<?php echo APP_URL; ?>/index.php" class="row g-2 align-items-center">
                    <input type="hidden" name="page" value="admin">
                    <input type="hidden" name="action" value="moadian_invoices">
                    <div class="col-md-3">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">همه وضعیت‌ها</option>
                            <option value="success" <?php echo ($currentStatus === 'success') ? 'selected' : ''; ?>>تأیید شده نهایی (موفق)</option>
                            <option value="pending" <?php echo ($currentStatus === 'pending') ? 'selected' : ''; ?>>در انتظار ارسال (Pending)</option>
                            <option value="canceled" <?php echo ($currentStatus === 'canceled') ? 'selected' : ''; ?>>ابطال شده (Canceled)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="subject" class="form-select form-select-sm">
                            <option value="">همه موضوعات (اصلی / اصلاحی / ابطالی)</option>
                            <option value="main" <?php echo ($currentSubject === 'main') ? 'selected' : ''; ?>>صورتحساب اصلی</option>
                            <option value="correction" <?php echo ($currentSubject === 'correction') ? 'selected' : ''; ?>>صورتحساب اصلاحی</option>
                            <option value="revocation" <?php echo ($currentSubject === 'revocation') ? 'selected' : ''; ?>>صورتحساب ابطالی</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary btn-sm w-100">
                            <i class="fas fa-filter me-1"></i> اعمال فیلتر
                        </button>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="text-muted small">نمایش <?php echo count($invoices); ?> صورتحساب رسمی</span>
                    </div>
                </form>
            </div>

            <!-- Invoices Table -->
            <div class="moadian-card overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th>شماره فاکتور</th>
                                <th>شناسه یکتای ۲۲ رقمی (TaxID)</th>
                                <th>مشتری / خریدار</th>
                                <th>موضوع / الگو</th>
                                <th>مبلغ ناخالص (تومان)</th>
                                <th>ارزش افزوده ۱۰٪</th>
                                <th>مبلغ کل</th>
                                <th>وضعیت مودیان</th>
                                <th class="text-center" style="min-width:180px;">عملیات مالیاتی</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($invoices)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                        <h6 class="fw-bold">هیچ صورتحسابی برای مودیان ثبت نشده است</h6>
                                        <p class="small text-muted mb-3">شما می‌توانید از بخش صدور فاکتور رسمی، صورتحساب جدید صادر فرمایید یا فاکتورهای موجود را ارسال نمایید.</p>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices_create" class="btn btn-sm btn-primary">
                                            صدور فاکتور رسمی
                                        </a>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($invoices as $inv): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold font-monospace text-dark"><?php echo htmlspecialchars($inv->inno); ?></span>
                                            <small class="text-muted d-block" style="font-size:0.7rem;">
                                                <?php echo date('Y/m/d H:i', (int)($inv->indatim / 1000)); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="taxid-pill">
                                                <span><?php echo htmlspecialchars($inv->taxid); ?></span>
                                                <button class="btn btn-link btn-sm p-0 text-muted" onclick="navigator.clipboard.writeText('<?php echo $inv->taxid; ?>'); alert('شناسه مالیاتی کپی شد: <?php echo $inv->taxid; ?>');" title="کپی شناسه ۲۲ رقمی">
                                                    <i class="far fa-copy"></i>
                                                </button>
                                            </div>
                                            <?php if (!empty($inv->reference_taxid)): ?>
                                                <small class="text-danger d-block font-monospace mt-1" style="font-size:0.7rem;">
                                                    مرجع: <?php echo htmlspecialchars($inv->reference_taxid); ?>
                                                </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($inv->client_name ?? 'مشتری آزاد'); ?></span>
                                            <small class="text-muted font-monospace" style="font-size:0.75rem;">
                                                اقتصادی: <?php echo htmlspecialchars($inv->customer_economic_code ?: '---'); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php 
                                                $subj = $inv->subject_type ?? 'main';
                                                if ($subj === 'main') {
                                                    echo '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">اصلی</span>';
                                                } elseif ($subj === 'correction') {
                                                    echo '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">اصلاحی</span>';
                                                } elseif ($subj === 'revocation') {
                                                    echo '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">ابطالی</span>';
                                                } else {
                                                    echo '<span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1 rounded-pill">برگشت</span>';
                                                }
                                            ?>
                                            <small class="text-muted d-block mt-1" style="font-size:0.7rem;">
                                                الگوی <?php echo (int)($inv->pattern_type ?? 1); ?>
                                            </small>
                                        </td>
                                        <td class="font-monospace"><?php echo number_format($inv->total_amount); ?></td>
                                        <td class="font-monospace text-muted"><?php echo number_format($inv->total_vat); ?></td>
                                        <td class="font-monospace fw-bold text-dark"><?php echo number_format($inv->final_amount); ?></td>
                                        <td>
                                            <?php if ($inv->status === 'success'): ?>
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                                    <i class="fas fa-check-circle me-1"></i> تایید کارپوشه
                                                </span>
                                                <?php if (!empty($inv->uid)): ?>
                                                    <small class="text-muted d-block font-monospace mt-1" style="font-size:0.65rem;" title="<?php echo htmlspecialchars($inv->uid); ?>">
                                                        UID: <?php echo substr($inv->uid, 0, 12) . '...'; ?>
                                                    </small>
                                                <?php endif; ?>
                                            <?php elseif ($inv->status === 'pending'): ?>
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">
                                                    <i class="fas fa-clock me-1"></i> در صف ارسال
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">
                                                    <i class="fas fa-times-circle me-1"></i> ابطال / خطا
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <?php if ($inv->status !== 'success'): ?>
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=send_to_moadian&id=<?php echo $inv->id; ?>" class="btn btn-primary" title="ارسال آنلاین به سرور مودیان">
                                                        <i class="fas fa-paper-plane me-1"></i> ارسال
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=inquiry_moadian&id=<?php echo $inv->id; ?>" class="btn btn-outline-success" title="استعلام آخرین وضعیت از کارپوشه دارایی">
                                                        <i class="fas fa-sync-alt me-1"></i> استعلام
                                                    </a>
                                                <?php endif; ?>
                                                
                                                <!-- چاپ دو برگ رسمی دارایی -->
                                                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=print_moadian&id=<?php echo $inv->id; ?>" target="_blank" class="btn btn-outline-secondary" title="چاپ استاندارد صورتحساب الکترونیکی با QR Code">
                                                    <i class="fas fa-print"></i>
                                                </a>

                                                <!-- دکمه ابطال -->
                                                <?php if ($inv->status === 'success' && $inv->subject_type !== 'revocation'): ?>
                                                    <button class="btn btn-outline-danger" onclick="cancelMoadianInvoice(<?php echo $inv->id; ?>, '<?php echo $inv->taxid; ?>')" title="ابطال صورتحساب در سامانه مودیان">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. SETTINGS TAB (کلیدها و تست اتصال واقعی به سامانه مودیان) -->
        <div class="tab-pane fade" id="settings-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- کارت تست و عیب‌یابی زنده اتصال به سرور مودیان -->
                    <div class="moadian-card p-4 mb-4 border-primary border-opacity-25" style="background:#f8fafc;">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">
                                    <i class="fas fa-stethoscope text-primary me-2"></i>تست و عیب‌یابی زنده اتصال به سامانه مودیان و اعتبارسنجی کلیدها
                                </h6>
                                <span class="small text-muted">بررسی ارتباط سرور هماکار با سامانه امور مالیاتی کشور و صحه‌گذاری امضای OpenSSL</span>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-3" onclick="generateAutoRsaKeys()" title="تولید خودکار جفت کلید RSA ۲۰۴۸ بیتی">
                                    <i class="fas fa-magic me-1"></i> تولید خودکار کلید RSA
                                </button>
                                <button type="button" class="btn btn-sm btn-primary rounded-3 shadow-sm" id="btn-test-connection" onclick="runMoadianDiagnostic()">
                                    <i class="fas fa-network-wired me-1"></i> تست اتصال زنده
                                </button>
                            </div>
                        </div>

                        <!-- نتیجه تست تشخیصی -->
                        <div id="diagnostic-result" class="p-3 rounded-3 bg-white border" style="display:none; font-size:0.84rem;">
                            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                                <span class="fw-bold text-dark"><i class="fas fa-server text-success me-1"></i>نتیجه تست ارتباط زنده با مودیان:</span>
                                <span id="diag-status-badge" class="badge bg-success">متصل</span>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <span class="text-muted">اکستنشن OpenSSL:</span>
                                    <strong id="diag-openssl" class="font-monospace text-dark ms-1">فعال</strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted">کلید خصوصی RSA:</span>
                                    <strong id="diag-privkey" class="font-monospace text-dark ms-1">معتبر</strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted">کلید عمومی و تطابق زوج‌کلید:</span>
                                    <strong id="diag-pubkey" class="font-monospace text-dark ms-1">تایید شده</strong>
                                </div>
                                <div class="col-md-6">
                                    <span class="text-muted">پاسخ سرور دارایی (Latency):</span>
                                    <strong id="diag-latency" class="font-monospace text-dark ms-1">120 ms</strong>
                                </div>
                                <div class="col-12 mt-1">
                                    <span class="text-muted">پاسخ دریافتی از Endpoint:</span>
                                    <div id="diag-response-text" class="font-monospace small bg-light p-2 rounded mt-1 border text-secondary" style="direction:ltr; text-align:left;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- فرم اصلی تنظیمات -->
                    <div class="moadian-card p-4">
                        <h5 class="fw-bold mb-3 text-dark">
                            <i class="fas fa-key text-primary me-2"></i>پیکربندی کلیدها و شناسه حافظه مالیاتی
                        </h5>
                        <form method="POST" action="<?php echo APP_URL; ?>/index.php?page=admin&action=save_moadian_settings">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">شناسه یکتای حافظه مالیاتی (Client ID / Memory ID):</label>
                                    <input type="text" id="setting_memory_id" name="memory_id" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($settings->memory_id ?? 'A100HK'); ?>" required>
                                    <small class="text-muted" style="font-size:0.75rem;">کد ۶ کاراکتری اختصاصی صادر شده از کارپوشه امور مالیاتی</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">نرخ پیش‌فرض مالیات بر ارزش افزوده (درصد):</label>
                                    <input type="number" step="0.1" name="tax_rate" class="form-control form-control-sm" value="<?php echo htmlspecialchars($settings->tax_rate ?? '10.00'); ?>" required>
                                    <small class="text-muted" style="font-size:0.75rem;">مطابق قانون مالیات بر ارزش افزوده کشور (۱۰ درصد)</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">کد اقتصادی مودی / شرکت:</label>
                                    <input type="text" name="economic_code" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($settings->economic_code ?? '14009156321'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">شناسه ملی / کد ملی فروشنده:</label>
                                    <input type="text" name="national_id" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($settings->national_id ?? '14009156321'); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">نام رسمی مودی / شرکت:</label>
                                    <input type="text" name="taxpayer_name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($settings->taxpayer_name ?? 'سامانه یکپارچه هماکار'); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">آدرس وب‌سرویس سامانه مودیان (API Endpoint):</label>
                                    <input type="text" id="setting_api_endpoint" name="api_endpoint" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($settings->api_endpoint ?? 'https://tp.tax.gov.ir/req/api'); ?>" required>
                                    <small class="text-muted" style="font-size:0.75rem;">برای سرور اصلی امور مالیاتی: <code>https://tp.tax.gov.ir/req/api</code></small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">کلید خصوصی RSA (Private Key - PEM Format):</label>
                                    <textarea id="setting_private_key" name="private_key" rows="4" class="form-control form-control-sm font-monospace" placeholder="-----BEGIN PRIVATE KEY-----&#10;...&#10;-----END PRIVATE KEY-----"><?php echo htmlspecialchars($settings->private_key ?? ''); ?></textarea>
                                    <small class="text-muted" style="font-size:0.75rem;">جهت امضای دیجیتال SHA256 بسته‌های ارسالی به کارپوشه</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">کلید عمومی یا امضای گواهی CSR (Public Key):</label>
                                    <textarea id="setting_public_key" name="public_key" rows="3" class="form-control form-control-sm font-monospace" placeholder="-----BEGIN CERTIFICATE-----&#10;...&#10;-----END CERTIFICATE-----"><?php echo htmlspecialchars($settings->public_key ?? ''); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_sandbox" id="is_sandbox" value="1" <?php echo (!empty($settings->is_sandbox)) ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="is_sandbox">
                                            حالت شبیه‌ساز آزمایشی (Sandbox)
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">با غیرفعال کردن این گزینه، صورتحساب‌ها مستقیماً با امضای معتبر در درگاه رسمی سازمان امور مالیاتی کشور ثبت قطعی می‌گردند.</small>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm">
                                        <i class="fas fa-save me-1"></i> ذخیره تنظیمات مودیان
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="moadian-card p-4 h-100 bg-light">
                        <h6 class="fw-bold mb-3 text-dark">
                            <i class="fas fa-info-circle text-info me-2"></i>راهنمای گام به گام اتصال واقعی
                        </h6>
                        <ol class="small text-muted ps-3 lh-lg mb-3">
                            <li>در درگاه ملی خدمات مالیاتی (<strong>my.tax.gov.ir</strong>) وارد کارپوشه اختصاصی شرکت یا مودی شوید.</li>
                            <li>در بخش «عضویت و پایانه فروشگاهی»، روی گزینه ثبت پایانه جدید کلیک کنید.</li>
                            <li>با کلیک روی دکمه «تولید خودکار کلید RSA»، زوج‌کلید اختصاصی را ایجاد کرده و کلید عمومی را در درگاه مالیاتی بارگذاری فرمایید.</li>
                            <li>شناسه یکتای ۶ کاراکتری حافظه مالیاتی صادر شده توسط دارایی را در فیلد روبرو وارد کرده و ذخیره نمایید.</li>
                            <li>دکمه «تست اتصال زنده» را کلیک فرمایید تا تاییدیه ارتباط و امضای دیجیتال صادر شود.</li>
                        </ol>
                        <div class="p-3 bg-white rounded-3 border">
                            <small class="fw-bold text-dark d-block mb-1">الگوریتم تولید شناسه یکتای ۲۲ رقمی:</small>
                            <code style="font-size:0.75rem;">MemoryID(6) + JulianDaysHex(5) + SerialHex(10) + LuhnMod11(1)</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. ACCOUNTING API TAB (کنسول تست زنده و راهنمای جامع اتصال نرم‌افزارهای حسابداری) -->
        <div class="tab-pane fade" id="api-pane" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-8">
                    <!-- کنسول تست زنده وب‌سرویس -->
                    <div class="moadian-card p-4 mb-4 border-primary border-opacity-25" style="background:#f8fafc;">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h6 class="fw-bold mb-1 text-dark">
                                    <i class="fas fa-play-circle text-primary me-2"></i>کنسول تست تعاملی و زنده وب‌سرویس API حسابداری
                                </h6>
                                <span class="small text-muted">تست آنی فراخوانی اندپوینت‌ها، صدور فاکتور و مشاهده خروجی JSON زنده</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary rounded-3 shadow-sm px-3" id="btn-run-api-test" onclick="executeApiTest()">
                                <i class="fas fa-paper-plane me-1"></i> اجرای درخواست تست (Send)
                            </button>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold text-muted">اندپوینت انتخابی جهت تست:</label>
                                <select id="api-test-endpoint" class="form-select form-select-sm" onchange="onEndpointChange()">
                                    <option value="ping">GET /ping (تست ارتباط وب‌سرویس)</option>
                                    <option value="official_invoices">GET /official_invoices (لیست فاکتورهای رسمی)</option>
                                    <option value="moadian_summary">GET /moadian_summary (خلاصه آمار کارپوشه)</option>
                                    <option value="create_official_invoice">POST /create_official_invoice (صدور فاکتور رسمی از سپیدار / هلو)</option>
                                    <option value="sync_moadian">POST /sync_moadian (استعلام و سینک کارپوشه)</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold text-muted">آدرس کامل فراخوانی (Request URL):</label>
                                <input type="text" id="api-request-url" class="form-control form-control-sm font-monospace bg-white" readonly value="<?php echo APP_URL; ?>/index.php?page=api&action=ping">
                            </div>
                        </div>

                        <!-- بدنه درخواستی برای متدهای POST -->
                        <div id="api-payload-group" class="mb-3" style="display:none;">
                            <label class="form-label small fw-bold text-muted">بدنه ارسالی (JSON Payload):</label>
                            <textarea id="api-request-payload" rows="6" class="form-control form-control-sm font-monospace bg-white" style="direction:ltr; text-align:left;">{
  "invoice_number": "ACC-TEST-<?php echo rand(1000, 9999); ?>",
  "customer_name": "شرکت مهندسی داده‌پردازان تبریز",
  "customer_economic_code": "411122334455",
  "customer_national_id": "10109988776",
  "customer_phone": "04134401179",
  "issue_date": "<?php echo date('Y/m/d'); ?>",
  "pattern_type": 1,
  "vat_rate": 10,
  "auto_send_moadian": true,
  "items": [
    {
      "product_name": "سامانه مدیریت ابری هماکار",
      "goods_code": "2710000138241",
      "quantity": 1,
      "unit_price": 50000000
    }
  ]
}</textarea>
                        </div>

                        <!-- خروجی زنده پاسخ سرور -->
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-muted">پاسخ زنده وب‌سرویس (Response JSON):</span>
                                <div class="d-flex gap-2 align-items-center">
                                    <span id="api-response-status" class="badge bg-secondary" style="font-size:0.75rem;">آماده تست</span>
                                    <span id="api-response-time" class="small text-muted font-monospace">0 ms</span>
                                </div>
                            </div>
                            <pre id="api-response-viewer" class="api-console-box mb-0">// روی دکمه «اجرای درخواست تست» کلیک فرمایید تا پاسخ زنده سرور نمایش داده شود.</pre>
                        </div>
                    </div>

                    <!-- مستندات اندپوینت‌ها -->
                    <div class="moadian-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-book text-primary me-2"></i>مستندات کامل اندپوینت‌های RESTful
                            </h5>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                نسخه ۳.۵.۰ فعال
                            </span>
                        </div>
                        <p class="text-muted small">
                            این وب‌سرویس به نرم‌افزارهای حسابداری سازمانی (سپیدار همکاران سیستم، هلو، شایگان، راهکاران و...) اجازه می‌دهد تا فاکتورهای رسمی خود را تزریق کرده، شناسه ۲۲ رقمی مالیاتی (TaxID) دریافت نمایند و بدون واسطه به سامانه مودیان ارسال کنند.
                        </p>

                        <!-- API Key Box -->
                        <div class="p-3 rounded-3 border bg-light mb-4">
                            <label class="form-label text-muted small fw-bold d-block">کلید امنیتی اتصال نرم‌افزار حسابداری (X-API-KEY):</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm font-monospace bg-white" id="api_key_val" value="<?php echo htmlspecialchars($apiKey->api_key ?? 'hmk_ca6806f53a4007f7d910e4f519a48af2'); ?>" readonly>
                                <button class="btn btn-outline-secondary btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('api_key_val').value); alert('کلید امنیتی وب‌سرویس در کلیپ‌بورد کپی شد.');">
                                    <i class="far fa-copy me-1"></i> کپی کلید
                                </button>
                            </div>
                            <small class="text-muted" style="font-size:0.75rem;">در تمام درخواست‌ها، هدر <code>X-API-KEY: {key}</code> را ارسال نمایید.</small>
                        </div>

                        <!-- Endpoints List -->
                        <div class="list-group mb-4">
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-secondary">GET</span>
                                    <code class="fw-bold">/index.php?page=api&action=ping</code>
                                </div>
                                <small class="text-muted d-block">بررسی سلامت سرور، زمان جلالی و وضعیت اتصال به سامانه مودیان.</small>
                            </div>
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-success">GET</span>
                                    <code class="fw-bold">/index.php?page=api&action=official_invoices</code>
                                </div>
                                <small class="text-muted d-block">دریافت لیست فاکتورهای رسمی با فیلتر تاریخ، شماره فاکتور و وضعیت سامانه مودیان.</small>
                            </div>
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-primary">POST</span>
                                    <code class="fw-bold">/index.php?page=api&action=create_official_invoice</code>
                                </div>
                                <small class="text-muted d-block">ثبت فاکتور رسمی از نرم‌افزار حسابداری در هماکار، صدور خودکار شناسه یکتای مالیاتی (TaxID) و ارسال آنی به کارپوشه دارایی.</small>
                            </div>
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-info">GET</span>
                                    <code class="fw-bold">/index.php?page=api&action=invoice_details&id={id}</code>
                                </div>
                                <small class="text-muted d-block">دریافت جزئیات کامل فاکتور رسمی، اقلام، نرخ‌های مالیات و کد پیگیری UID دارایی.</small>
                            </div>
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-warning text-dark">POST</span>
                                    <code class="fw-bold">/index.php?page=api&action=sync_moadian</code>
                                </div>
                                <small class="text-muted d-block">همگام‌سازی و استعلام وضعیت صورتحساب بین نرم‌افزار حسابداری و سامانه مودیان.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- نمونه کدهای اتصال نرم‌افزارها -->
                <div class="col-lg-4">
                    <div class="moadian-card p-4 h-100">
                        <h6 class="fw-bold mb-3 text-dark">
                            <i class="fas fa-laptop-code text-success me-2"></i>راهنمای اتصال نرم‌افزارهای مالی
                        </h6>

                        <!-- سپیدار همکاران سیستم -->
                        <div class="alert alert-light border rounded-3 p-3 mb-3">
                            <h6 class="fw-bold text-primary mb-1" style="font-size:0.85rem;">
                                <i class="fas fa-cube me-1"></i>اتصال سپیدار همکاران سیستم:
                            </h6>
                            <p class="small text-muted mb-2" style="font-size:0.75rem;">
                                از طریق ماژول وب‌سرویس یا افزونه اختصاصی سپیدار، آدرس <code>create_official_invoice</code> را به عنوان وب‌هوک خروج فاکتور فروش تنظیم فرمایید. هماکار بلافاصله فاکتور را دریافت، تبدیل و به مودیان ارسال می‌کند.
                            </p>
                        </div>

                        <!-- هلو -->
                        <div class="alert alert-light border rounded-3 p-3 mb-3">
                            <h6 class="fw-bold text-info mb-1" style="font-size:0.85rem;">
                                <i class="fas fa-boxes me-1"></i>اتصال نرم‌افزار حسابداری هلو:
                            </h6>
                            <p class="small text-muted mb-2" style="font-size:0.75rem;">
                                با فراخوانی متد POST و هدر امنیتی X-API-KEY در وب‌سرویس هلو، شناسه مالیاتی صادر شده در فیلد اختصاصی صورتحساب هلو ذخیره می‌گردد.
                            </p>
                        </div>

                        <!-- نمونه کد cURL -->
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">نمونه دستور cURL:</label>
                            <pre class="bg-light p-2 rounded-3 font-monospace text-dark" style="direction:ltr; text-align:left; font-size:0.75rem;">
curl -X GET "<?php echo APP_URL; ?>/index.php?page=api&action=ping" \
  -H "X-API-KEY: <?php echo htmlspecialchars($apiKey->api_key ?? 'hmk_ca6806f53a4007f7d910e4f519a48af2'); ?>"
                            </pre>
                        </div>

                        <!-- نمونه کد C# (.NET) -->
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">نمونه کد C# (.NET / سپیدار):</label>
                            <pre class="bg-light p-2 rounded-3 font-monospace text-dark" style="direction:ltr; text-align:left; font-size:0.7rem; max-height:160px; overflow-y:auto;">
using var client = new HttpClient();
client.DefaultRequestHeaders.Add("X-API-KEY", "<?php echo htmlspecialchars($apiKey->api_key ?? 'hmk_key'); ?>");
var response = await client.GetAsync("<?php echo APP_URL; ?>/index.php?page=api&action=official_invoices");
string json = await response.Content.ReadAsStringAsync();
                            </pre>
                        </div>

                        <div class="alert alert-success rounded-3 p-2 small mb-0">
                            <i class="fas fa-shield-alt me-1"></i> ارتباط امن SSL و اعتبارسنجی خودکار ساختار مالیاتی
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cancel Invoice -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold" style="font-size:1rem;">
                    <i class="fas fa-ban me-2"></i>ابطال صورتحساب الکترونیکی در مودیان
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="cancelForm" method="POST" action="<?php echo APP_URL; ?>/index.php?page=admin&action=cancel_moadian">
                <div class="modal-body p-4">
                    <p class="text-muted small">
                        با تایید این عملیات، یک صورتحساب ابطالی رسمی با ارجاع به شناسه مالیاتی 
                        <strong id="modal_taxid" class="text-danger font-monospace"></strong> در کارپوشه صادر خواهد شد.
                    </p>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">علت ابطال صورتحساب:</label>
                        <textarea name="reason" class="form-control form-control-sm" rows="3" required placeholder="مثال: اشتباه در اقلام فاکتور یا درخواست خریدار">ابطال بنا به درخواست مودی و ثبت فاکتور جایگزین</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-danger btn-sm px-3">تایید و صدور صورتحساب ابطالی</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function switchMoadianTab(paneId) {
    var btn = document.querySelector('button[data-bs-target="#' + paneId + '"]');
    if (btn) {
        var tab = new bootstrap.Tab(btn);
        tab.show();
        btn.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        window.location.hash = '#' + paneId;
    }
}

function cancelMoadianInvoice(id, taxid) {
    document.getElementById('modal_taxid').innerText = taxid;
    document.getElementById('cancelForm').action = '<?php echo APP_URL; ?>/index.php?page=admin&action=cancel_moadian&id=' + id;
    var modal = new bootstrap.Modal(document.getElementById('cancelModal'));
    modal.show();
}

// تغییر اندپوینت انتخابی در کنسول تست زنده API
function onEndpointChange() {
    var endpoint = document.getElementById('api-test-endpoint').value;
    var baseUrl = '<?php echo APP_URL; ?>/index.php?page=api&action=' + endpoint;
    document.getElementById('api-request-url').value = baseUrl;
    
    var payloadGroup = document.getElementById('api-payload-group');
    if (endpoint === 'create_official_invoice' || endpoint === 'sync_moadian') {
        payloadGroup.style.display = 'block';
    } else {
        payloadGroup.style.display = 'none';
    }
}

// اجرای تست زنده وب‌سرویس API حسابداری
async function executeApiTest() {
    var endpoint = document.getElementById('api-test-endpoint').value;
    var url = document.getElementById('api-request-url').value;
    var apiKey = document.getElementById('api_key_val').value;
    var viewer = document.getElementById('api-response-viewer');
    var statusBadge = document.getElementById('api-response-status');
    var timeBadge = document.getElementById('api-response-time');
    var btn = document.getElementById('btn-run-api-test');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> در حال ارسال...';
    viewer.innerText = '// در حال ارسال درخواست به وب‌سرویس هماکار...';

    var startTime = performance.now();
    try {
        var options = {
            method: (endpoint === 'create_official_invoice' || endpoint === 'sync_moadian') ? 'POST' : 'GET',
            headers: {
                'X-API-KEY': apiKey,
                'Content-Type': 'application/json'
            }
        };

        if (options.method === 'POST') {
            options.body = document.getElementById('api-request-payload').value;
        }

        var response = await fetch(url, options);
        var endTime = performance.now();
        var duration = Math.round(endTime - startTime);
        timeBadge.innerText = duration + ' ms';

        statusBadge.innerText = response.status + ' ' + response.statusText;
        if (response.ok) {
            statusBadge.className = 'badge bg-success';
        } else {
            statusBadge.className = 'badge bg-danger';
        }

        var text = await response.text();
        try {
            var json = JSON.parse(text);
            viewer.innerText = JSON.stringify(json, null, 2);
        } catch(e) {
            viewer.innerText = text;
        }
    } catch(err) {
        statusBadge.innerText = 'Network Error';
        statusBadge.className = 'badge bg-danger';
        viewer.innerText = '// خطا در برقراری ارتباط با وب‌سرویس: ' + err.message;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> اجرای درخواست تست (Send)';
    }
}

// تست و عیب‌یابی زنده اتصال به سرور مودیان
async function runMoadianDiagnostic() {
    var btn = document.getElementById('btn-test-connection');
    var resultBox = document.getElementById('diagnostic-result');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> بررسی زنده...';

    try {
        var res = await fetch('<?php echo APP_URL; ?>/index.php?page=admin&action=test_moadian_connection');
        var data = await res.json();
        
        resultBox.style.display = 'block';
        if (data.status === 'success' && data.data) {
            var d = data.data;
            document.getElementById('diag-openssl').innerText = d.openssl_installed ? 'فعال و عملیاتی' : 'غیرفعال';
            document.getElementById('diag-openssl').className = d.openssl_installed ? 'text-success fw-bold' : 'text-danger fw-bold';

            document.getElementById('diag-privkey').innerText = d.private_key_valid ? 'معتبر (امضای SHA256 تایید شد)' : 'نامعتبر یا خالی';
            document.getElementById('diag-privkey').className = d.private_key_valid ? 'text-success fw-bold' : 'text-danger fw-bold';

            document.getElementById('diag-pubkey').innerText = d.keypair_match ? 'تطابق کامل زوج‌کلید' : (d.public_key_valid ? 'کلید عمومی معتبر' : 'ثبت نشده');
            document.getElementById('diag-pubkey').className = d.keypair_match ? 'text-success fw-bold' : 'text-warning fw-bold';

            document.getElementById('diag-latency').innerText = d.latency_ms + ' ms';
            document.getElementById('diag-response-text').innerText = d.server_response;

            var statusBadge = document.getElementById('diag-status-badge');
            if (d.server_reachable) {
                statusBadge.className = 'badge bg-success';
                statusBadge.innerText = 'سرور مودیان در دسترس (HTTP ' + d.http_code + ')';
            } else {
                statusBadge.className = 'badge bg-info text-dark';
                statusBadge.innerText = 'حالت شبیه‌ساز امن (Sandbox فعال)';
            }
        }
    } catch(err) {
        alert('خطا در تست اتصال به سامانه مودیان: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-network-wired me-1"></i> تست اتصال زنده';
    }
}

// تولید خودکار کلیدهای RSA
async function generateAutoRsaKeys() {
    if (!confirm('آیا مایل به تولید خودکار زوج‌کلید RSA استاندارد ۲۰۴۸ بیتی برای مودیان هستید؟')) {
        return;
    }
    try {
        var res = await fetch('<?php echo APP_URL; ?>/index.php?page=admin&action=generate_moadian_keys');
        var data = await res.json();
        if (data.status === 'success' && data.data && data.data.success) {
            document.getElementById('setting_private_key').value = data.data.private_key;
            document.getElementById('setting_public_key').value = data.data.public_key;
            alert('زوج‌کلید RSA با موفقیت تولید شد. برای اعمال نهایی، روی «ذخیره تنظیمات مودیان» کلیک فرمایید.');
        } else {
            alert('خطا در تولید کلید: ' + (data.data.message || 'خطای سرور'));
        }
    } catch(e) {
        alert('خطا در برقراری ارتباط با سرور.');
    }
}

// باز کردن تب بر اساس هش URL
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash) {
        var hash = window.location.hash.replace('#', '');
        if (hash === 'settings-pane' || hash === 'api-pane' || hash === 'invoices-pane') {
            switchMoadianTab(hash);
        }
    }
});
</script>
