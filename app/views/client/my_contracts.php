<?php
// app/views/client/my_contracts.php - نسخه اولترا پرمیوم
$contracts = $contracts ?? [];
?>

<style>
    .contracts-card-header {
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

    .btn-view-contract {
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

    .btn-view-contract:hover {
        background: #4f46e5;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">قراردادهای من</h1>
        <p class="text-muted small mb-0">مشاهده و بررسی جزئیات تمامی قراردادهای فعال و گذشته شما با مجموعه هماکار</p>
    </div>

    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
            <i class="fas fa-print"></i>
            <span>چاپ لیست</span>
        </button>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="contracts-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fas fa-file-signature text-indigo-600" style="color: #4f46e5;"></i>
            <span>لیست قراردادهای حقوقی و خدماتی شما</span>
        </h6>
        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold" style="background:#e0e7ff; color:#4338ca;">
            تعداد کل: <?php echo count($contracts); ?> قرارداد
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 70px;">#</th>
                        <th>عنوان قرارداد</th>
                        <th>نوع خدمات</th>
                        <th>مبلغ کل قرارداد</th>
                        <th class="text-center">تاریخ شروع</th>
                        <th class="text-center">وضعیت</th>
                        <th class="text-center no-print" style="width: 120px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($contracts)): ?>
                        <?php foreach ($contracts as $contract): ?>
                            <tr>
                                <td class="text-center font-monospace text-muted">#<?php echo sanitize($contract->id); ?></td>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 bg-light p-2 text-indigo-600 border" style="color: #4f46e5;">
                                            <i class="fas fa-file-contract"></i>
                                        </div>
                                        <span><?php echo sanitize($contract->title); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace px-2.5 py-1">
                                        <?php echo sanitize($contract->service_type); ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-dark font-monospace">
                                    <?php echo number_format((float)sanitize($contract->total_amount)); ?> <small class="text-muted font-normal" style="font-size: 0.75rem;">تومان</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark font-monospace border">
                                        <?php echo sanitize(date('Y/m/d', strtotime($contract->start_date))); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $status = $contract->status;
                                        $badgeClass = 'bg-secondary';
                                        $statusText = 'نامشخص';

                                        if ($status == 'active') {
                                            $badgeClass = 'bg-success bg-opacity-10 text-success border border-success border-opacity-25';
                                            $statusText = 'فعال';
                                        } elseif ($status == 'pending') {
                                            $badgeClass = 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25';
                                            $statusText = 'در انتظار تایید';
                                        } elseif ($status == 'completed') {
                                            $badgeClass = 'bg-info bg-opacity-10 text-info border border-info border-opacity-25';
                                            $statusText = 'تکمیل شده';
                                        } elseif ($status == 'canceled') {
                                            $badgeClass = 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25';
                                            $statusText = 'لغو شده';
                                        }
                                    ?>
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold <?php echo $badgeClass; ?>">
                                        <?php echo $statusText; ?>
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <a href="<?php echo APP_URL; ?>/index.php?page=client&action=view_contract&id=<?php echo $contract->id; ?>" target="_blank" class="btn-view-contract" title="مشاهده / چاپ نسخه رسمی">
                                        <i class="fas fa-eye"></i>
                                        <span>مشاهده</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-file-circle-xmark fs-2 mb-2 d-block opacity-40"></i>
                                هنوز هیچ قراردادی برای حساب شما ثبت یا فعال نشده است.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>