<?php
// app/views/admin/moadian/index.php - کارپوشه جامع و فوق پرمیوم سامانه مودیان مالیاتی کشور
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
                    <i class="fas fa-signal me-1"></i> متصل به سرور مودیان (<?php echo ($settings && $settings->is_sandbox) ? 'Sandbox آزمایشی' : 'تولید زنده Live'; ?>)
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">
                    حافظه مالیاتی: <?php echo htmlspecialchars($settings->memory_id ?? 'A100HK'); ?>
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
            <a href="#settings-pane" data-bs-toggle="pill" class="btn btn-outline-secondary rounded-3 px-3">
                <i class="fas fa-cog me-1"></i> تنظیمات کلیدها
            </a>
            <a href="#api-pane" data-bs-toggle="pill" class="btn btn-outline-primary rounded-3 px-3">
                <i class="fas fa-code me-1"></i> وب‌سرویس API حسابداری
            </a>
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
                        <small class="text-muted">نیازمند ارسال آنلاین</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="moadian-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                        <i class="fas fa-coins fa-lg"></i>
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
    <ul class="nav nav-pills nav-pills-moadian mb-4 gap-2 bg-white p-2 rounded-4 border shadow-sm">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#invoices-pane">
                <i class="fas fa-list-alt me-1"></i> صورتحساب‌های الکترونیکی کارپوشه
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#settings-pane" id="settings-tab-btn">
                <i class="fas fa-sliders-h me-1"></i> پیکربندی و کلیدهای سامانه مودیان
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#api-pane">
                <i class="fas fa-network-wired me-1"></i> اتصال به نرم‌افزار حسابداری (API)
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content">
        <!-- 1. INVOICES TAB -->
        <div class="tab-pane fade show active" id="invoices-pane">
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

        <!-- 2. SETTINGS TAB -->
        <div class="tab-pane fade" id="settings-pane">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="moadian-card p-4">
                        <h5 class="fw-bold mb-3 text-dark">
                            <i class="fas fa-key text-primary me-2"></i>پیکربندی کلیدها و شناسه حافظه مالیاتی
                        </h5>
                        <form method="POST" action="<?php echo APP_URL; ?>/index.php?page=admin&action=save_moadian_settings">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">شناسه یکتای حافظه مالیاتی (Client ID / Memory ID):</label>
                                    <input type="text" name="memory_id" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($settings->memory_id ?? 'A100HK'); ?>" required>
                                    <small class="text-muted" style="font-size:0.75rem;">کد ۶ کاراکتری دریافت شده از کارپوشه سازمان امور مالیاتی</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small fw-bold">نرخ پیش‌فرض مالیات بر ارزش افزوده (درصد):</label>
                                    <input type="number" step="0.1" name="tax_rate" class="form-control form-control-sm" value="<?php echo htmlspecialchars($settings->tax_rate ?? '10.00'); ?>" required>
                                    <small class="text-muted" style="font-size:0.75rem;">مطابق قانون بودجه کشور (۱۰ درصد)</small>
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
                                    <input type="text" name="api_endpoint" class="form-control form-control-sm font-monospace" value="<?php echo htmlspecialchars($settings->api_endpoint ?? 'https://tp.tax.gov.ir/req/api'); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">کلید خصوصی RSA (Private Key - PEM Format):</label>
                                    <textarea name="private_key" rows="4" class="form-control form-control-sm font-monospace" placeholder="-----BEGIN PRIVATE KEY-----&#10;...&#10;-----END PRIVATE KEY-----"><?php echo htmlspecialchars($settings->private_key ?? ''); ?></textarea>
                                    <small class="text-muted" style="font-size:0.75rem;">جهت امضای دیجیتال بسته‌های اطلاعاتی صورتحساب‌های الکترونیکی</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small fw-bold">کلید عمومی یا امضای گواهی CSR (Public Key):</label>
                                    <textarea name="public_key" rows="3" class="form-control form-control-sm font-monospace" placeholder="-----BEGIN CERTIFICATE-----&#10;...&#10;-----END CERTIFICATE-----"><?php echo htmlspecialchars($settings->public_key ?? ''); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_sandbox" id="is_sandbox" value="1" <?php echo ($settings && $settings->is_sandbox) ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="is_sandbox">
                                            حالت آزمایشی سرور مودیان (Sandbox / شبیه‌ساز)
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">در این حالت، پکت‌های مالیاتی با کد پیگیری معتبر شبیه‌سازی می‌شوند و آسیبی به پرونده مالیاتی زنده نمی‌زنند.</small>
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
                            <i class="fas fa-info-circle text-info me-2"></i>راهنمای اتصال به مودیان
                        </h6>
                        <ol class="small text-muted ps-3 lh-lg mb-3">
                            <li>در درگاه ملی خدمات مالیاتی (my.tax.gov.ir) وارد کارپوشه اختصاصی خود شوید.</li>
                            <li>در بخش «عضویت و پایانه فروشگاهی»، یک پرونده ثبت کرده و کلید عمومی (Public Key) را بارگذاری نمایید.</li>
                            <li>شناسه یکتای حافظه مالیاتی (کد ۶ حرفی) را دریافت کرده و در فیلد روبرو ثبت کنید.</li>
                            <li>پس از ذخیره تنظیمات، تمام فاکتورهای رسمی سامانه با امضای اختصاصی شما به صورت خودکار به مودیان ارسال می‌گردند.</li>
                        </ol>
                        <div class="p-3 bg-white rounded-3 border">
                            <small class="fw-bold text-dark d-block mb-1">الگوریتم تولید شناسه یکتای ۲۲ رقمی:</small>
                            <code style="font-size:0.75rem;">MemoryID(6) + JulianDaysHex(5) + SerialHex(10) + LuhnMod11(1)</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. ACCOUNTING API TAB -->
        <div class="tab-pane fade" id="api-pane">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="moadian-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-plug text-primary me-2"></i>وب‌سرویس RESTful اتصال نرم‌افزارهای حسابداری
                            </h5>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">
                                نسخه ۳.۵.۰ فعال
                            </span>
                        </div>
                        <p class="text-muted small">
                            این وب‌سرویس به نرم‌افزارهای حسابداری سازمانی (سپیدار، هلو، شایگان، راهکاران و...) اجازه می‌دهد تا فاکتورهای رسمی را دریافت کرده یا مستقیماً فاکتورهای صادر شده خود را به هماکار تزریق نمایند تا شناسه ۲۲ رقمی مالیاتی صادر و به کارپوشه ارسال گردد.
                        </p>

                        <!-- API Key Box -->
                        <div class="p-3 rounded-3 border bg-light mb-4">
                            <label class="form-label text-muted small fw-bold d-block">کلید امنیتی اتصال نرم‌افزار حسابداری (X-API-KEY):</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm font-monospace bg-white" id="api_key_val" value="<?php echo htmlspecialchars($apiKey->api_key ?? 'hmk_live_acc_software_secret_key'); ?>" readonly>
                                <button class="btn btn-outline-secondary btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('api_key_val').value); alert('کلید API کپی شد!');">
                                    <i class="far fa-copy me-1"></i> کپی کلید
                                </button>
                            </div>
                            <small class="text-muted" style="font-size:0.75rem;">در تمام درخواست‌ها، این کلید را در هدر <code>X-API-KEY: {key}</code> ارسال فرمایید.</small>
                        </div>

                        <!-- Endpoints List -->
                        <h6 class="fw-bold text-dark mb-3">اندپوینت‌های فعال وب‌سرویس:</h6>
                        <div class="list-group mb-4">
                            <!-- Endpoint 1 -->
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-success">GET</span>
                                    <code class="fw-bold">/index.php?page=api&action=official_invoices</code>
                                </div>
                                <small class="text-muted d-block">دریافت لیست فاکتورهای رسمی با امکان فیلتر بر اساس تاریخ و وضعیت سامانه مودیان.</small>
                            </div>

                            <!-- Endpoint 2 -->
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-primary">POST</span>
                                    <code class="fw-bold">/index.php?page=api&action=create_official_invoice</code>
                                </div>
                                <small class="text-muted d-block">ثبت فاکتور رسمی از نرم‌افزار حسابداری در هماکار، تولید خودکار شناسه یکتای مالیاتی (TaxID) و ارسال آنی به کارپوشه دارایی.</small>
                            </div>

                            <!-- Endpoint 3 -->
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-info">GET</span>
                                    <code class="fw-bold">/index.php?page=api&action=invoice_details&id={id}</code>
                                </div>
                                <small class="text-muted d-block">دریافت جزئیات کامل یک فاکتور رسمی، اقلام، نرخ‌های مالیات و کد پیگیری دارایی.</small>
                            </div>

                            <!-- Endpoint 4 -->
                            <div class="list-group-item p-3">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-warning text-dark">POST</span>
                                    <code class="fw-bold">/index.php?page=api&action=sync_moadian</code>
                                </div>
                                <small class="text-muted d-block">همگام‌سازی و استعلام وضعیت صورتحساب بین نرم‌افزار حسابداری و سامانه مودیان.</small>
                            </div>
                        </div>

                        <!-- Sample JSON Payload -->
                        <h6 class="fw-bold text-dark mb-2">نمونه بدنه ارسالی (JSON Payload) برای ثبت فاکتور رسمی:</h6>
                        <pre class="bg-dark text-light p-3 rounded-3 font-monospace" style="direction:ltr; text-align:left; font-size:0.8rem; max-height:260px; overflow-y:auto;">
{
  "invoice_number": "ACC-1403-9001",
  "customer_name": "شرکت مهندسی داده‌پردازان",
  "customer_economic_code": "411122334455",
  "customer_national_id": "10109988776",
  "customer_phone": "02188990011",
  "issue_date": "1403/08/10",
  "pattern_type": 1,
  "vat_rate": 10,
  "auto_send_moadian": true,
  "items": [
    {
      "product_name": "سامانه مدیریت ابری هماکار",
      "goods_code": "2710000138241",
      "quantity": 1,
      "unit_price": 75000000
    }
  ]
}
                        </pre>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="moadian-card p-4 h-100">
                        <h6 class="fw-bold mb-3 text-dark">
                            <i class="fas fa-terminal text-success me-2"></i>نمونه کدهای اتصال سریع
                        </h6>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">تست با cURL:</label>
                            <pre class="bg-light p-2 rounded-3 font-monospace text-dark" style="direction:ltr; text-align:left; font-size:0.75rem;">
curl -X GET "<?php echo APP_URL; ?>/index.php?page=api&action=official_invoices" \
  -H "X-API-KEY: <?php echo htmlspecialchars($apiKey->api_key ?? 'hmk_live_acc_software_secret_key'); ?>"
                            </pre>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">تست در PHP (cURL):</label>
                            <pre class="bg-light p-2 rounded-3 font-monospace text-dark" style="direction:ltr; text-align:left; font-size:0.72rem;">
$ch = curl_init("<?php echo APP_URL; ?>/index.php?page=api&action=official_invoices");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-API-KEY: <?php echo htmlspecialchars($apiKey->api_key ?? 'hmk_key'); ?>",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
                            </pre>
                        </div>
                        <div class="alert alert-success rounded-3 p-2 small mb-0">
                            <i class="fas fa-check-circle me-1"></i> سازگار با تمام نسخه‌های نرم‌افزارهای سپیدار همکاران سیستم و هلو
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
function cancelMoadianInvoice(id, taxid) {
    document.getElementById('modal_taxid').innerText = taxid;
    document.getElementById('cancelForm').action = '<?php echo APP_URL; ?>/index.php?page=admin&action=cancel_moadian&id=' + id;
    var modal = new bootstrap.Modal(document.getElementById('cancelModal'));
    modal.show();
}

// اگر هش در آدرس وجود داشت تب مربوطه باز شود
if (window.location.hash === '#settings-tab') {
    var tabBtn = document.getElementById('settings-tab-btn');
    if (tabBtn) {
        var tab = new bootstrap.Tab(tabBtn);
        tab.show();
    }
}
</script>
