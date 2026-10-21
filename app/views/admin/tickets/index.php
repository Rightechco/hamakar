<?php
// app/views/admin/tickets/index.php
$tickets = $tickets ?? [];

// مپینگ اولویت‌ها با بج‌های شیک
function getPriorityBadgeDetails($priority) {
    switch ($priority) {
        case 'high':
            return ['bg' => 'bg-soft-danger', 'text' => 'text-danger', 'label' => 'زیاد'];
        case 'medium':
            return ['bg' => 'bg-soft-warning', 'text' => 'text-warning', 'label' => 'متوسط'];
        case 'low':
            return ['bg' => 'bg-soft-info', 'text' => 'text-info', 'label' => 'کم'];
        default:
            return ['bg' => 'bg-soft-secondary', 'text' => 'text-secondary', 'label' => $priority];
    }
}

// مپینگ وضعیت‌ها با بج‌های شیک
function getStatusBadgeDetails($status) {
    switch ($status) {
        case 'open':
            return ['bg' => 'bg-soft-primary', 'text' => 'text-primary', 'label' => 'باز'];
        case 'answered':
            return ['bg' => 'bg-soft-success', 'text' => 'text-success', 'label' => 'پاسخ داده شده'];
        case 'client_reply':
            return ['bg' => 'bg-soft-warning', 'text' => 'text-warning', 'label' => 'پاسخ مشتری'];
        case 'closed':
            return ['bg' => 'bg-soft-secondary', 'text' => 'text-secondary', 'label' => 'بسته شده'];
        default:
            return ['bg' => 'bg-soft-secondary', 'text' => 'text-secondary', 'label' => $status];
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 18px;
        --radius-md: 12px;
        --shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    body, .tickets-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .bg-soft-primary { background-color: #e0e7ff; color: #4338ca !important; }
    .bg-soft-success { background-color: #dcfce7; color: #15803d !important; }
    .bg-soft-warning { background-color: #fef3c7; color: #b45309 !important; }
    .bg-soft-danger { background-color: #fee2e2; color: #b91c1c !important; }
    .bg-soft-secondary { background-color: #f1f5f9; color: #475569 !important; }
    .bg-soft-info { background-color: #e0f2fe; color: #0369a1 !important; }

    /* Ultra Card */
    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
    }

    /* Floating Row Table */
    .table-ultra-tickets {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-ultra-tickets tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-ultra-tickets tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra-tickets td, .table-ultra-tickets th {
        padding: 1.1rem 1.25rem;
        border: none;
        vertical-align: middle;
    }

    .action-btn-view {
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

    .action-btn-view:hover {
        background-color: #0284c7;
        color: #ffffff;
        transform: scale(1.05);
    }
</style>

<div class="tickets-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">مدیریت تیکت‌های پشتیبانی</h3>
            <p class="text-muted small mb-0">مشاهده، بررسی و پاسخ‌دهی به درخواست‌های پشتیبانی خریداران و پرسنل</p>
        </div>
    </div>

    <!-- Tickets Table Container -->
    <div class="ultra-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-headset me-2 text-primary"></i>لیست تمامی تیکت‌ها</h6>
        </div>

        <div class="table-responsive">
            <table class="table table-ultra-tickets align-middle">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>موضوع تیکت</th>
                        <th>ایجاد شده توسط (مشتری)</th>
                        <th>دپارتمان</th>
                        <th class="text-center">اولویت</th>
                        <th class="text-center">وضعیت</th>
                        <th>آخرین به‌روزرسانی</th>
                        <th class="text-end">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <?php 
                                $priorityBadge = getPriorityBadgeDetails($ticket->priority ?? '');
                                $statusBadge = getStatusBadgeDetails($ticket->status ?? '');
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-soft rounded-3 bg-soft-primary p-2 me-2 text-center" style="width: 38px; height: 38px;">
                                            <i class="fas fa-ticket-alt text-primary"></i>
                                        </div>
                                        <span class="fw-bold text-dark fs-6"><?php echo sanitize($ticket->subject); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark small">
                                        <i class="far fa-user me-1 text-muted"></i>
                                        <?php echo sanitize($ticket->client_name); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-soft-secondary text-secondary px-2.5 py-1 rounded-2">
                                        <?php echo sanitize($ticket->department); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?php echo $priorityBadge['bg'] . ' ' . $priorityBadge['text']; ?> px-3 py-1.5 rounded-pill fw-medium">
                                        <?php echo htmlspecialchars($priorityBadge['label']); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?php echo $statusBadge['bg'] . ' ' . $statusBadge['text']; ?> px-3 py-1.5 rounded-pill fw-medium">
                                        <?php echo htmlspecialchars($statusBadge['label']); ?>
                                    </span>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-clock me-1"></i>
                                    <?php echo jdate('Y/m/d H:i', strtotime($ticket->updated_at)); ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_ticket&id=<?php echo $ticket->id; ?>" class="action-btn-view" title="مشاهده و پاسخ تیکت">
                                        <i class="fas fa-reply me-1.5"></i> مشاهده و پاسخ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 text-light"></i>
                                <p class="mb-0">هیچ تیکت پشتیبانی یافت نشد.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>