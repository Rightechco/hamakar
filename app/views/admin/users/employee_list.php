<?php
// app/views/admin/users/employee_list.php - نسخه اولترا پرمیوم
$employees = $employees ?? [];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 18px;
    }

    body, .employees-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .bg-soft-primary { background-color: #e0e7ff; color: #4338ca !important; }
    .bg-soft-info { background-color: #e0f2fe; color: #0369a1 !important; }
    .bg-soft-secondary { background-color: #f1f5f9; color: #475569 !important; }

    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
    }

    /* Floating Row Table */
    .table-ultra-emp {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-ultra-emp tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-ultra-emp tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra-emp td, .table-ultra-emp th {
        padding: 1.1rem 1.25rem;
        border: none;
        vertical-align: middle;
    }

    .action-btn-report {
        background-color: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
        border-radius: 10px;
        padding: 6px 14px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .action-btn-report:hover {
        background-color: #0284c7;
        color: #ffffff;
        transform: scale(1.05);
    }
</style>

<div class="employees-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">لیست کارمندان فعال</h3>
            <p class="text-muted small mb-0">مشاهده پرسنل، سمت‌های سازمانی، تاریخ استخدام و گزارشات ارزیابی عملکرد</p>
        </div>
    </div>

    <?php FlashMessage::display(); ?>

    <!-- Employees Table Container -->
    <div class="ultra-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-id-badge me-2 text-primary"></i>کارمندان و پرسنل فعال سیستم</h6>
        </div>

        <div class="table-responsive">
            <table class="table table-ultra-emp align-middle">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>نام کارمند</th>
                        <th>پست الکترونیکی (ایمیل)</th>
                        <th>نقش / سمت سازمانی</th>
                        <th>تاریخ استخدام</th>
                        <th class="text-end">ارزیابی و گزارشات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($employees)): ?>
                        <?php foreach ($employees as $employee): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-soft rounded-circle bg-soft-primary p-2 me-2 text-center" style="width: 38px; height: 38px;">
                                            <i class="fas fa-user-tie text-primary"></i>
                                        </div>
                                        <span class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($employee->name); ?></span>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-envelope me-1"></i>
                                    <?php echo htmlspecialchars($employee->email); ?>
                                </td>
                                <td>
                                    <span class="badge bg-soft-primary px-3 py-1.5 rounded-pill fw-medium">
                                        <?php echo htmlspecialchars($employee->organizational_position ?? $employee->role); ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    <?php echo $employee->hire_date ? jdate('Y/m/d', strtotime($employee->hire_date)) : '---'; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=employee_performance_report&id=<?php echo $employee->id; ?>" class="action-btn-report" title="گزارش عملکرد پرسنل">
                                        <i class="fas fa-chart-line me-1.5"></i> گزارش عملکرد
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fa-3x mb-3 text-light"></i>
                                <p class="mb-0">هیچ کارمند فعالی در سیستم یافت نشد.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>