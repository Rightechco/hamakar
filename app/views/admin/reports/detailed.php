<?php
// app/views/admin/reports/detailed.php
$filters = $filters ?? [
    'invoice_status' => '',
    'project_status' => '',
    'start_date' => '',
    'end_date' => ''
];
$reports = $reports ?? ['invoices' => [], 'projects' => [], 'employees' => []];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 18px;
        --radius-md: 12px;
        --shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    body, .detailed-reports-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    /* Ultra Card */
    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        margin-bottom: 1.5rem;
    }

    .ultra-card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
    }

    /* Form Inputs */
    .form-control, .form-select {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        padding: 0.65rem 0.95rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .input-group-text.pdp-trigger {
        border-radius: 0 var(--radius-md) var(--radius-md) 0;
        border: 1px solid var(--border-color);
        background-color: #f8fafc;
        cursor: pointer;
    }

    .btn-gradient-primary {
        background: var(--primary-gradient);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        padding: 0.65rem 1.8rem;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: all 0.2s ease;
    }

    .btn-gradient-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        color: #fff;
    }

    /* Modern Tabs */
    .nav-tabs-ultra {
        border-bottom: none;
        gap: 8px;
        margin-bottom: 1.5rem;
    }

    .nav-tabs-ultra .nav-link {
        border: 1px solid var(--border-color);
        background-color: #f8fafc;
        color: #64748b;
        border-radius: var(--radius-md);
        padding: 10px 20px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .nav-tabs-ultra .nav-link:hover {
        background-color: #f1f5f9;
        color: #0f172a;
    }

    .nav-tabs-ultra .nav-link.active {
        background: var(--primary-gradient);
        color: #ffffff;
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }

    /* Floating Row Table */
    .table-ultra-report {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-ultra-report tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-ultra-report tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra-report td, .table-ultra-report th {
        padding: 1.1rem 1.25rem;
        border: none;
        vertical-align: middle;
    }

    .bg-soft-primary { background-color: #e0e7ff; color: #4338ca !important; }
    .bg-soft-success { background-color: #dcfce7; color: #15803d !important; }
    .bg-soft-warning { background-color: #fef3c7; color: #b45309 !important; }
    .bg-soft-danger { background-color: #fee2e2; color: #b91c1c !important; }
    .bg-soft-secondary { background-color: #f1f5f9; color: #475569 !important; }

    @media print {
        .no-print { display: none !important; }
        body { background: #fff !important; }
        .ultra-card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>

<div class="detailed-reports-wrapper py-3">
    <!-- Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">گزارشات جامع و دقیق</h3>
            <p class="text-muted small mb-0">تحلیل فیلترشده فاکتورها، وضعیت پروژه‌ها و گزارش کارکرد پرسنل</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary rounded-pill px-4 btn-sm fw-medium no-print">
            <i class="fas fa-print me-1.5"></i> چاپ گزارش
        </button>
    </div>

    <!-- Filter Card -->
    <div class="ultra-card no-print">
        <div class="ultra-card-header d-flex align-items-center">
            <i class="fas fa-filter text-primary fs-5 me-2"></i>
            <h5 class="mb-0 fw-bold fs-6 text-dark">فیلتر هوشمند گزارشات</h5>
        </div>
        <div class="card-body p-4">
            <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=detailed_reports" method="POST">
                <div class="row g-3 align-items-end">
                    <!-- Invoice Status -->
                    <div class="col-md-3">
                        <label class="form-label fw-medium small text-dark">وضعیت فاکتور</label>
                        <select name="invoice_status" class="form-select">
                            <option value="">همه وضعیت‌ها</option>
                            <option value="pending" <?php echo (isset($filters['invoice_status']) && $filters['invoice_status'] == 'pending') ? 'selected' : ''; ?>>در انتظار پرداخت</option>
                            <option value="paid" <?php echo (isset($filters['invoice_status']) && $filters['invoice_status'] == 'paid') ? 'selected' : ''; ?>>پرداخت شده</option>
                            <option value="overdue" <?php echo (isset($filters['invoice_status']) && $filters['invoice_status'] == 'overdue') ? 'selected' : ''; ?>>سررسید گذشته</option>
                            <option value="canceled" <?php echo (isset($filters['invoice_status']) && $filters['invoice_status'] == 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
                        </select>
                    </div>

                    <!-- Project Status -->
                    <div class="col-md-3">
                        <label class="form-label fw-medium small text-dark">وضعیت پروژه</label>
                        <select name="project_status" class="form-select">
                            <option value="">همه وضعیت‌ها</option>
                            <option value="not_started" <?php echo (isset($filters['project_status']) && $filters['project_status'] == 'not_started') ? 'selected' : ''; ?>>شروع نشده</option>
                            <option value="in_progress" <?php echo (isset($filters['project_status']) && $filters['project_status'] == 'in_progress') ? 'selected' : ''; ?>>در حال انجام</option>
                            <option value="finished" <?php echo (isset($filters['project_status']) && $filters['project_status'] == 'finished') ? 'selected' : ''; ?>>تکمیل شده</option>
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div class="col-md-3">
                        <label class="form-label fw-medium small text-dark">از تاریخ</label>
                        <div class="input-group">
                            <span class="input-group-text pdp-trigger"><i class="far fa-calendar-alt text-muted"></i></span>
                            <input type="text" name="start_date" class="form-control pdp-input border-start-0" value="<?php echo !empty($filters['start_date']) ? jdate('Y/m/d', strtotime($filters['start_date'])) : ''; ?>" placeholder="YYYY/MM/DD" autocomplete="off">
                        </div>
                    </div>

                    <!-- End Date -->
                    <div class="col-md-3">
                        <label class="form-label fw-medium small text-dark">تا تاریخ</label>
                        <div class="input-group">
                            <span class="input-group-text pdp-trigger"><i class="far fa-calendar-check text-muted"></i></span>
                            <input type="text" name="end_date" class="form-control pdp-input border-start-0" value="<?php echo !empty($filters['end_date']) ? jdate('Y/m/d', strtotime($filters['end_date'])) : ''; ?>" placeholder="YYYY/MM/DD" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end pt-3 mt-3 border-top">
                    <button type="submit" class="btn btn-gradient-primary">
                        <i class="fas fa-search me-1.5"></i> اعمال فیلتر گزارشات
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs nav-tabs-ultra no-print" id="reportTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button" role="tab">
                <i class="fas fa-file-invoice me-1.5"></i>گزارش فاکتورها
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab">
                <i class="fas fa-project-diagram me-1.5"></i>گزارش پروژه‌ها
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="employees-tab" data-bs-toggle="tab" data-bs-target="#employees" type="button" role="tab">
                <i class="fas fa-users me-1.5"></i>گزارش کارمندان
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="reportTabsContent">
        <!-- Invoices Tab -->
        <div class="tab-pane fade show active" id="invoices" role="tabpanel">
            <div class="ultra-card p-4">
                <div class="table-responsive">
                    <table class="table table-ultra-report align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>شماره فاکتور</th>
                                <th>نام مشتری / خریدار</th>
                                <th>مبلغ کل</th>
                                <th>تاریخ صدور</th>
                                <th>تاریخ سررسید</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reports['invoices'])): ?>
                                <?php foreach($reports['invoices'] as $invoice): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-soft-primary px-3 py-1.5 rounded-pill fw-bold">
                                                #<?php echo sanitize($invoice->invoice_number); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark fs-6"><?php echo sanitize($invoice->client_name); ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success fs-6"><?php echo number_format($invoice->total_amount); ?></span>
                                            <span class="text-muted small ms-1">تومان</span>
                                        </td>
                                        <td class="text-muted small">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <?php echo jdate('Y/m/d', strtotime($invoice->issue_date)); ?>
                                        </td>
                                        <td class="text-muted small">
                                            <i class="far fa-calendar-check me-1"></i>
                                            <?php echo jdate('Y/m/d', strtotime($invoice->due_date)); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-file-invoice fa-3x mb-3 text-light"></i>
                                        <p class="mb-0">هیچ فاکتوری بر اساس فیلترهای انتخابی یافت نشد.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Projects Tab -->
        <div class="tab-pane fade" id="projects" role="tabpanel">
            <div class="ultra-card p-4">
                <div class="table-responsive">
                    <table class="table table-ultra-report align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>نام پروژه</th>
                                <th>مشتری</th>
                                <th>تاریخ شروع</th>
                                <th>وضعیت</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reports['projects'])): ?>
                                <?php foreach($reports['projects'] as $project): ?>
                                    <tr>
                                        <td class="fw-bold text-dark"><?php echo sanitize($project->name ?? '---'); ?></td>
                                        <td class="text-muted"><?php echo sanitize($project->client_name ?? 'پروژه داخلی'); ?></td>
                                        <td class="text-muted small"><?php echo !empty($project->start_date) ? jdate('Y/m/d', strtotime($project->start_date)) : '---'; ?></td>
                                        <td>
                                            <span class="badge bg-soft-primary px-3 py-1.5 rounded-pill">
                                                <?php echo sanitize($project->status ?? 'نامشخص'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i class="fas fa-project-diagram fa-3x mb-3 text-light"></i>
                                        <p class="mb-0">هیچ پروژه‌ای یافت نشد.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Employees Tab -->
        <div class="tab-pane fade" id="employees" role="tabpanel">
            <div class="ultra-card p-4">
                <div class="table-responsive">
                    <table class="table table-ultra-report align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th>نام کارمند</th>
                                <th>وظایف محول شده</th>
                                <th>ساعات کاری</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reports['employees'])): ?>
                                <?php foreach($reports['employees'] as $emp): ?>
                                    <tr>
                                        <td class="fw-bold text-dark"><?php echo sanitize($emp->name ?? '---'); ?></td>
                                        <td><span class="badge bg-soft-info px-3 py-1.5 rounded-pill"><?php echo number_format($emp->assigned_tasks ?? 0); ?> وظیفه</span></td>
                                        <td class="fw-medium text-dark"><?php echo number_format($emp->total_hours ?? 0); ?> ساعت</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="fas fa-users fa-3x mb-3 text-light"></i>
                                        <p class="mb-0">اطلاعاتی برای کارمندان یافت نشد.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Persian Datepicker Loader & Click Handler -->
<script>
(function loadPersianDatePicker() {
    function initPicker() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.pDatepicker) {
            $('.pdp-input').each(function() {
                var $input = $(this);
                $input.pDatepicker({
                    format: 'YYYY/MM/DD',
                    autoClose: true,
                    initialValue: false,
                    observer: true,
                    initialValueType: 'persian'
                });
            });

            $('.pdp-trigger, .pdp-input').on('click', function(e) {
                e.stopPropagation();
                var $input = $(this).hasClass('pdp-input') ? $(this) : $(this).siblings('.pdp-input');
                $input.focus();
            });
        } else { setTimeout(initPicker, 100); }
    }

    if (!document.getElementById('p-datepicker-css')) {
        var css = document.createElement('link'); css.id = 'p-datepicker-css'; css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css';
        document.head.appendChild(css);
    }

    if (typeof $ === 'undefined') {
        var jq = document.createElement('script'); jq.src = 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js';
        jq.onload = loadPDate; document.head.appendChild(jq);
    } else { loadPDate(); }

    function loadPDate() {
        if (typeof window.persianDate === 'undefined') {
            var pd = document.createElement('script'); pd.src = 'https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js';
            pd.onload = loadPDatePicker; document.head.appendChild(pd);
        } else { loadPDatePicker(); }
    }

    function loadPDatePicker() {
        if (typeof $.fn.pDatepicker === 'undefined') {
            var pdp = document.createElement('script'); pdp.src = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js';
            pdp.onload = initPicker; document.head.appendChild(pdp);
        } else { initPicker(); }
    }
})();
</script>