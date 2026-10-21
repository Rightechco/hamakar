<?php
// app/views/admin/hr/leave_requests.php
$requests = $requests ?? [];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
    }

    body, .leave-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .bg-soft-primary { background-color: #e0e7ff; color: #4338ca !important; }
    .bg-soft-info { background-color: #e0f2fe; color: #0369a1 !important; }
    .bg-soft-success { background-color: #dcfce7; color: #15803d !important; }
    .bg-soft-danger { background-color: #fee2e2; color: #b91c1c !important; }

    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: 18px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
    }

    /* Floating Table Styling */
    .table-ultra-leave {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-ultra-leave tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-ultra-leave tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra-leave td, .table-ultra-leave th {
        padding: 1.1rem 1.25rem;
        border: none;
        vertical-align: middle;
    }

    .btn-action-approve {
        background-color: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-action-approve:hover {
        background-color: #22c55e;
        color: #ffffff;
        transform: scale(1.05);
    }

    .btn-action-reject {
        background-color: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-action-reject:hover {
        background-color: #ef4444;
        color: #ffffff;
        transform: scale(1.05);
    }
</style>

<div class="leave-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">مدیریت درخواست‌های مرخصی</h3>
            <p class="text-muted small mb-0">بررسی، تایید یا رد درخواست‌های مرخصی روزانه و ساعتی پرسنل</p>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="ultra-card p-4">
        <div class="table-responsive">
            <table class="table table-ultra-leave align-middle">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>نام کارمند</th>
                        <th>نوع مرخصی</th>
                        <th>از تاریخ و زمان</th>
                        <th>تا تاریخ و زمان</th>
                        <th>دلیل مرخصی</th>
                        <th class="text-end">عملیات مدیریت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach($requests as $req): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-soft rounded-circle bg-soft-primary p-2 me-2 text-center" style="width: 38px; height: 38px;">
                                            <i class="fas fa-user text-primary"></i>
                                        </div>
                                        <span class="fw-bold text-dark fs-6"><?php echo sanitize($req->employee_name); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($req->leave_type == 'daily'): ?>
                                        <span class="badge bg-soft-primary px-3 py-1.5 rounded-pill fw-medium">
                                            <i class="far fa-calendar-alt me-1"></i> روزانه
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-soft-info px-3 py-1.5 rounded-pill fw-medium">
                                            <i class="far fa-clock me-1"></i> ساعتی
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-calendar me-1"></i>
                                    <?php echo jdate('Y/m/d H:i', strtotime($req->start_date)); ?>
                                </td>
                                <td class="text-muted small">
                                    <i class="far fa-calendar-check me-1"></i>
                                    <?php echo jdate('Y/m/d H:i', strtotime($req->end_date)); ?>
                                </td>
                                <td>
                                    <span class="text-dark small" style="max-width: 250px; display: inline-block;">
                                        <?php echo sanitize($req->reason); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=process_leave_request&id=<?php echo $req->id; ?>" method="POST" class="d-inline">
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-action-approve" title="تایید درخواست">
                                                <i class="fas fa-check me-1"></i> تایید
                                            </button>
                                        </form>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=process_leave_request&id=<?php echo $req->id; ?>" method="POST" class="d-inline">
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-action-reject" title="رد درخواست">
                                                <i class="fas fa-times me-1"></i> رد
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-user-clock fa-3x mb-3 text-light"></i>
                                <p class="mb-0">هیچ درخواست مرخصی جدیدی ثبت نشده است.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>