<?php 
//require APPROOT . '/views/layouts/admin_layout.php';
?>

<style>
    .stat-card-ultra {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        padding: 1.5rem;
    }

    .stat-card-ultra:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 35px -5px rgba(99, 102, 241, 0.1);
        border-color: #c7d2fe;
    }

    .stat-icon-box {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-left: 1rem;
        flex-shrink: 0;
    }

    .license-table-card {
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        box-shadow: 0 10px 30px -5px rgba(0,0,0,0.04);
        overflow: hidden;
    }

    .license-key-badge {
        font-family: monospace, sans-serif;
        letter-spacing: 1px;
        direction: ltr;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        color: #334155;
        padding: 6px 14px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .license-key-badge:hover {
        background: #e0e7ff;
        border-color: #6366f1;
        color: #4338ca;
    }

    .avatar-initials-circle {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        flex-shrink: 0;
    }

    .table-premium-admin {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-premium-admin th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-premium-admin td {
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
        vertical-align: middle;
    }

    .table-premium-admin tr:hover td {
        background-color: #f8fafc;
    }
</style>

<div class="container-fluid p-4">
    
    <!-- هدر اصلی -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-extrabold text-dark mb-1">مدیریت لایسنس‌ها و سیستم اشتراک</h3>
            <p class="text-muted small mb-0">مشاهده کلیه لایسنس‌های فعال، تاریخ انقضا و مدیریت دسترسی مشترکین</p>
        </div>

        <a href="index.php?page=admin&action=create_license_form" class="btn btn-primary rounded-pill px-4 py-2.5 shadow-sm fw-bold d-inline-flex align-items-center gap-2">
            <i class="fas fa-magic"></i>
            <span>صدور لایسنس جدید</span>
        </a>
    </div>

    <!-- کارت‌های آمار و خلاصه وضعیت -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card-ultra">
                <div class="stat-icon-box bg-indigo-50 text-indigo-700" style="background:#e0e7ff; color:#4338ca;">
                    <i class="fas fa-key"></i>
                </div>
                <div>
                    <h3 class="fw-black mb-0 text-dark font-monospace"><?php echo count($data['licenses']); ?></h3>
                    <span class="text-muted small fw-bold">کل لایسنس‌های صادرشده</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-ultra">
                <div class="stat-icon-box bg-emerald-50 text-emerald-700" style="background:#d1fae5; color:#059669;">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div>
                    <h3 class="fw-black mb-0 text-emerald-600 font-monospace" style="color:#059669;"><?php echo $data['total_active']; ?></h3>
                    <span class="text-muted small fw-bold">اشتراک فعال و معتبر</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card-ultra">
                <div class="stat-icon-box bg-rose-50 text-rose-700" style="background:#ffe4e6; color:#e11d48;">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h3 class="fw-black mb-0 text-danger font-monospace"><?php echo $data['total_expired']; ?></h3>
                    <span class="text-muted small fw-bold">منقضی یا خاتمه یافته</span>
                </div>
            </div>
        </div>
    </div>

    <!-- جدول لایسنس‌ها -->
    <div class="card license-table-card bg-white border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table-premium-admin">
                    <thead>
                        <tr>
                            <th class="px-4">کاربر مقصد</th>
                            <th>کلید اختصاصی لایسنس</th>
                            <th>طرح اشتراک</th>
                            <th class="text-center">مهلت / انقضا</th>
                            <th class="text-center">وضعیت</th>
                            <th class="text-end px-4">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data['licenses'])): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-key fs-2 mb-2 d-block opacity-40"></i>
                                    هنوز هیچ لایسنس و اشتراکی در سیستم صادر نشده است.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($data['licenses'] as $lic): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-initials-circle">
                                                <?php echo function_exists('mb_substr') ? mb_substr($lic->user_name ?? 'U', 0, 1) : substr($lic->user_name ?? 'U', 0, 1); ?>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark mb-0.5"><?php echo sanitize($lic->user_name); ?></div>
                                                <div class="small text-muted font-monospace"><?php echo sanitize($lic->email); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="license-key-badge" onclick="copyTxt('<?php echo $lic->license_key; ?>')" title="برای کپی کلیک کنید">
                                            <span><?php echo substr($lic->license_key, 0, 12) . '...'; ?></span>
                                            <i class="far fa-copy text-muted"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold" style="background:#e0e7ff; color:#4338ca;">
                                            <?php echo sanitize($lic->plan_title ?? 'سفارشی'); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $daysLeft = ceil((strtotime($lic->expires_at) - time()) / 86400);
                                            if($daysLeft < 0) {
                                                echo '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1 fw-bold">منقضی شده</span>';
                                            } else {
                                                echo '<span class="fw-black text-dark font-monospace me-1">' . $daysLeft . '</span><span class="small text-muted">روز مانده</span>';
                                            }
                                        ?>
                                        <div class="text-muted font-monospace small mt-0.5" style="font-size: 11px;"><?php echo date('Y/m/d', strtotime($lic->expires_at)); ?></div>
                                    </td>
                                    <td class="text-center">
                                        <?php if($lic->status == 'active' && $daysLeft >= 0): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1.5 fw-bold">فعال</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1.5 fw-bold">غیرفعال</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="d-inline-flex gap-1">
                                            <a href="#" class="btn btn-sm btn-light text-primary border rounded-circle shadow-sm" style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center;" title="ویرایش">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn btn-sm btn-light text-danger border rounded-circle shadow-sm" style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center;" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </a>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function copyTxt(text) {
        navigator.clipboard.writeText(text);
        const Toast = Swal.mixin({
            toast: true, 
            position: 'top-end', 
            showConfirmButton: false, 
            timer: 1500,
            didOpen: (toast) => { 
                toast.onmouseenter = Swal.stopTimer; 
                toast.onmouseleave = Swal.resumeTimer; 
            }
        });
        Toast.fire({ icon: 'success', title: 'کلید لایسنس کپی شد' });
    }
</script>