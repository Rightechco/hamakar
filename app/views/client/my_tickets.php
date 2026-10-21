<?php
// app/views/client/my_tickets.php - نسخه اولترا پرمیوم
$tickets = $tickets ?? [];

$totalTickets = count($tickets);
$openTicketsCount = 0;
$answeredTicketsCount = 0;

foreach ($tickets as $t) {
    $status = $t->status ?? '';
    if (in_array($status, ['open', 'pending', 'customer_reply'])) {
        $openTicketsCount++;
    } elseif (in_array($status, ['answered', 'closed'])) {
        $answeredTicketsCount++;
    }
}
?>

<style>
    .tickets-card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .table-premium {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-premium th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-premium td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
        vertical-align: middle;
    }

    .table-premium tr:hover td {
        background-color: #f8fafc;
    }

    .btn-action-view {
        background: #e0e7ff;
        color: #4338ca !important;
        border: 1px solid #c7d2fe;
        border-radius: 10px;
        padding: 0.45rem 0.9rem;
        font-size: 0.825rem;
        font-weight: 700;
        transition: all 0.2s ease;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .btn-action-view:hover {
        background: #4f46e5;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">تیکت‌ها و پشتیبانی فنی</h1>
        <p class="text-muted small mb-0">ارسال پیگیری، درخواست سوالات فنی، امور مالی یا مشاوره با کارشناسان هماکار</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="<?php echo APP_URL; ?>/index.php?page=client&action=create_ticket" class="btn btn-primary rounded-3 px-3.5 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm text-white text-decoration-none" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); border:none;">
            <i class="fas fa-plus-circle"></i>
            <span>ثبت تیکت جدید</span>
        </a>
    </div>
</div>

<!-- کارت‌های خلاصه وضعیت تیکت‌ها -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">کل تیکت‌های ثبت شده</span>
                <h4 class="fw-black text-dark font-monospace mb-0"><?php echo number_format($totalTickets); ?></h4>
            </div>
            <div class="rounded-3 p-3 text-indigo-600" style="background:#e0e7ff; color:#4338ca;">
                <i class="fas fa-headset fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">تیکت‌های در جریان</span>
                <h4 class="fw-black text-warning font-monospace mb-0"><?php echo number_format($openTicketsCount); ?></h4>
            </div>
            <div class="rounded-3 p-3 text-warning" style="background:#fef3c7; color:#b45309;">
                <i class="fas fa-hourglass-half fs-4"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3.5 bg-white d-flex flex-row align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold d-block mb-1">پاسخ داده شده / بسته</span>
                <h4 class="fw-black text-emerald-600 font-monospace mb-0" style="color:#059669;"><?php echo number_format($answeredTicketsCount); ?></h4>
            </div>
            <div class="rounded-3 p-3 text-emerald-600" style="background:#d1fae5; color:#059669;">
                <i class="fas fa-circle-check fs-4"></i>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="tickets-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fas fa-comments text-indigo-600" style="color: #4f46e5;"></i>
            <span>فهرست درخواست‌های پشتیبانی</span>
        </h6>
        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold" style="background:#e0e7ff; color:#4338ca;">
            تعداد کل: <?php echo $totalTickets; ?>
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 70px;">کد</th>
                        <th>موضوع درخواست</th>
                        <th>دپارتمان</th>
                        <th class="text-center">اولویت</th>
                        <th class="text-center">آخرین بروزرسانی</th>
                        <th class="text-center">وضعیت</th>
                        <th class="text-center no-print" style="width: 110px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <?php
                                $status = $ticket->status ?? 'open';
                                $statusBadge = 'bg-secondary';
                                $statusLabel = 'نامشخص';

                                if ($status === 'open' || $status === 'pending') {
                                    $statusBadge = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                                    $statusLabel = 'در انتظار بررسی';
                                } elseif ($status === 'customer_reply') {
                                    $statusBadge = 'bg-info bg-opacity-10 text-info border border-info border-opacity-25';
                                    $statusLabel = 'پاسخ مشتری';
                                } elseif ($status === 'answered') {
                                    $statusBadge = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                    $statusLabel = 'پاسخ داده شده';
                                } elseif ($status === 'closed') {
                                    $statusBadge = 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25';
                                    $statusLabel = 'بسته شده';
                                }

                                $priority = $ticket->priority ?? 'medium';
                                $priorityBadge = 'bg-light text-dark';
                                $priorityLabel = 'متوسط';

                                if ($priority === 'high') {
                                    $priorityBadge = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                    $priorityLabel = 'فوری';
                                } elseif ($priority === 'low') {
                                    $priorityBadge = 'bg-light text-muted border';
                                    $priorityLabel = 'کم';
                                }

                                $dept = $ticket->department ?? 'support';
                                $deptLabel = 'پشتیبانی فنی';
                                if ($dept === 'sales') $deptLabel = 'بخش فروش';
                                elseif ($dept === 'billing') $deptLabel = 'امور مالی';
                            ?>
                            <tr>
                                <td class="text-center font-monospace text-muted">#<?php echo sanitize($ticket->id); ?></td>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 bg-light p-2 text-indigo-600 border" style="color: #4f46e5;">
                                            <i class="fas fa-ticket"></i>
                                        </div>
                                        <span><?php echo sanitize($ticket->subject); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace px-2.5 py-1">
                                        <i class="fas fa-sitemap me-1 text-muted"></i>
                                        <?php echo $deptLabel; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold <?php echo $priorityBadge; ?>">
                                        <?php echo $priorityLabel; ?>
                                    </span>
                                </td>
                                <td class="text-center font-monospace small text-muted">
                                    <?php echo sanitize($ticket->created_at_jalali ?? '---'); ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold <?php echo $statusBadge; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <a href="<?php echo APP_URL; ?>/index.php?page=client&action=view_ticket&id=<?php echo $ticket->id; ?>" class="btn-action-view" title="مشاهده گفتگو">
                                        <i class="fas fa-comments"></i>
                                        <span>پاسخ‌ها</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-headset fs-2 mb-2 d-block opacity-40"></i>
                                هنوز هیچ تیکت یا درخواست پشتیبانی ثبت نکرده‌اید.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>