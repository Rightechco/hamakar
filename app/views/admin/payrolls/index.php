<?php
// app/views/admin/payrolls/index.php
$payrolls = $payrolls ?? [];
$title = $title ?? 'مدیریت فیش‌های حقوقی';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
    }

    body, .payroll-list-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .bg-soft-primary { background-color: #e0e7ff; color: #4338ca !important; }
    .bg-soft-success { background-color: #dcfce7; color: #15803d !important; }
    .bg-soft-danger { background-color: #fee2e2; color: #b91c1c !important; }
    .bg-soft-info { background-color: #e0f2fe; color: #0369a1 !important; }

    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
    }

    /* Floating Table Styling */
    .table-ultra-payroll {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-ultra-payroll tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-ultra-payroll tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra-payroll td, .table-ultra-payroll th {
        padding: 1.1rem 1.25rem;
        border: none;
        vertical-align: middle;
    }

    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .action-btn:hover {
        transform: scale(1.08);
    }
</style>

<div class="payroll-list-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><?php echo sanitize($title); ?></h3> 
            <p class="text-muted small mb-0">لیست کلیه فیش‌های حقوقی صادرشده، جمع مزایا، کسورات و خالص پرداختی پرسنل</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=create_payroll" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-medium"> 
            <i class="fas fa-plus me-1"></i> صدور فیش جدید 
        </a>
    </div>

    <?php FlashMessage::display(); ?>

    <!-- Table Card Container -->
    <div class="ultra-card p-4">
        <div class="table-responsive">
            <table class="table table-ultra-payroll align-middle">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>نام کارمند</th> 
                        <th>دوره حقوق (سال / ماه)</th> 
                        <th>جمع درآمد و مزایا</th> 
                        <th>جمع کسورات</th> 
                        <th>خالص پرداختی</th> 
                        <th class="text-end">عملیات</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payrolls)): ?>
                        <?php foreach ($payrolls as $payroll): ?> 
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-soft rounded-circle bg-soft-primary p-2 me-2 text-center" style="width: 38px; height: 38px;">
                                            <i class="fas fa-user-tie text-primary"></i>
                                        </div>
                                        <span class="fw-bold text-dark fs-6"><?php echo sanitize($payroll->employee_name); ?></span> 
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-primary px-3 py-1.5 rounded-pill fw-medium">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        <?php echo $payroll->pay_period_year . ' / ' . jdate_words(['mm' => $payroll->pay_period_month])['mm']; ?> 
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        <?php echo number_format($payroll->gross_earnings); ?> 
                                    </span>
                                    <span class="text-muted small ms-1">تومان</span> 
                                </td>
                                <td>
                                    <span class="fw-bold text-danger">
                                        <?php echo number_format($payroll->total_deductions); ?> 
                                    </span>
                                    <span class="text-muted small ms-1">تومان</span> 
                                </td>
                                <td>
                                    <span class="badge bg-soft-success px-3 py-2 rounded-pill fs-6 fw-bold">
                                        <?php echo number_format($payroll->net_pay); ?> تومان 
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_payslip&id=<?php echo $payroll->id; ?>" class="action-btn bg-soft-info text-info" target="_blank" title="مشاهده فیش حقوقی"> 
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?> 
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-file-invoice-dollar fa-3x mb-3 text-light"></i>
                                <p class="mb-0">هیچ فیش حقوقی صادر نشده است.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>