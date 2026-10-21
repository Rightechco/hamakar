<?php
// app/views/admin/training/needs/index.php - نسخه اولترا پرمیوم
$pendingNeeds = $pendingNeeds ?? [];
?>

<style>
    .needs-card-header {
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

    .btn-review-need {
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

    .btn-review-need:hover {
        background: #4f46e5;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">نیازسنجی آموزشی پرسنل</h1>
        <p class="text-muted small mb-0">بررسی درخواست‌های خودارزیابی، تحلیل نقاط ارتقا و تعیین دوره‌های پیشنهادی</p>
    </div>

    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
            <i class="fas fa-print"></i>
            <span>چاپ لیست</span>
        </button>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=training_needs_analysis" class="btn btn-outline-primary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
            <i class="fas fa-chart-pie"></i>
            <span>گزارش تحلیلی نیازسنجی</span>
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="needs-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fas fa-clock-rotate-left text-indigo-600" style="color: #4f46e5;"></i>
            <span>درخواست‌های در انتظار بررسی مدیریت</span>
        </h6>
        <span class="badge bg-amber-50 text-amber-700 border border-amber-200 rounded-pill px-3 py-1.5 fw-bold" style="background:#fef3c7; color:#b45309;">
            در انتظار بررسی: <?php echo count($pendingNeeds); ?> مورد
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th>نام کارمند</th>
                        <th class="text-center">سال ارزیابی</th>
                        <th class="text-center">تاریخ ارسال فرم</th>
                        <th class="text-center">وضعیت بررسی</th>
                        <th class="text-center no-print" style="width: 120px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pendingNeeds)): ?>
                        <?php foreach ($pendingNeeds as $need): ?>
                            <tr>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-primary fw-bold" style="width:36px; height:36px;">
                                            <?php echo mb_substr(htmlspecialchars($need->employee_name ?? 'U'), 0, 1); ?>
                                        </div>
                                        <span><?php echo sanitize($need->employee_name ?? 'نامشخص'); ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark font-monospace border">
                                        <?php echo sanitize($need->year ?? jdate('Y')); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark font-monospace border">
                                        <?php echo !empty($need->created_at) ? sanitize(jdate('Y/m/d', strtotime($need->created_at))) : '---'; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background:#fef3c7; color:#b45309; border:1px solid #fde68a;">
                                        در انتظار تحلیل مدیر
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_training_need&id=<?php echo $need->id ?? 0; ?>" class="btn-review-need" title="بررسی فرم و ارائه پیشنهاد">
                                        <i class="fas fa-file-signature"></i>
                                        <span>بررسی</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-check-double fs-2 mb-2 d-block opacity-40"></i>
                                هیچ درخواست نیازسنجی جدیدی در انتظار بررسی وجود ندارد.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>