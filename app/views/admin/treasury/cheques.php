<?php
// app/views/admin/treasury/cheques.php - مدیریت چک‌های صیادی و خزانه‌داری
?>
<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#1e293b;">
                <i class="fas fa-money-check-dollar text-primary me-2"></i>خزانه‌داری و مدیریت چک‌های صیادی
            </h3>
            <span class="text-muted small">ردیابی اسناد دریافتنی و پرداختنی، استعلام صیاد و صدور سند وصول یا واگذاری</span>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?page=admin&action=treasury_rasgiri" class="btn btn-outline-primary rounded-3 px-3">
                <i class="fas fa-calculator me-1"></i> ماشین‌حساب راس‌گیری
            </a>
            <a href="index.php?page=admin&action=create_cheque" class="btn btn-primary rounded-3 px-3 shadow-sm">
                <i class="fas fa-plus me-1"></i> ثبت چک جدید
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card border-0 shadow-sm rounded-4 p-2 mb-4 bg-white">
        <ul class="nav nav-pills gap-2">
            <li class="nav-item">
                <a class="nav-link rounded-3 <?php echo empty($currentType) ? 'active' : ''; ?>" href="index.php?page=admin&action=treasury_cheques">همه اسناد</a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-3 <?php echo ($currentType === 'received') ? 'active' : ''; ?>" href="index.php?page=admin&action=treasury_cheques&type=received">چک‌های دریافتی</a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-3 <?php echo ($currentType === 'paid') ? 'active' : ''; ?>" href="index.php?page=admin&action=treasury_cheques&type=paid">چک‌های پرداختی</a>
            </li>
        </ul>
    </div>

    <!-- Cheques Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th>نوع</th>
                        <th>شماره صیاد (۱۶ رقم)</th>
                        <th>شماره چک</th>
                        <th>بانک صادرکننده</th>
                        <th>صاحب حساب / مشتری</th>
                        <th>مبلغ (تومان)</th>
                        <th>سررسید</th>
                        <th>وضعیت</th>
                        <th class="text-center">تغییر وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($cheques)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">هیچ چکی در این بخش ثبت نشده است.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cheques as $ch): ?>
                            <tr>
                                <td>
                                    <?php if ($ch->type === 'received'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">دریافتی</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2">پرداختی</span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-monospace text-primary fw-bold" style="letter-spacing:1px;"><?php echo htmlspecialchars($ch->sayad_id); ?></td>
                                <td class="font-monospace"><?php echo htmlspecialchars($ch->cheque_number); ?></td>
                                <td><?php echo htmlspecialchars($ch->bank_name . ' ' . $ch->branch); ?></td>
                                <td><?php echo htmlspecialchars($ch->drawer_name ?: ($ch->client_name ?? '-')); ?></td>
                                <td class="font-monospace fw-bold"><?php echo number_format($ch->amount); ?></td>
                                <td class="font-monospace"><?php echo htmlspecialchars($ch->due_date); ?></td>
                                <td>
                                    <?php 
                                        $badges = [
                                            'pending' => 'bg-warning text-warning border-warning',
                                            'deposited' => 'bg-info text-info border-info',
                                            'cleared' => 'bg-success text-success border-success',
                                            'bounced' => 'bg-danger text-danger border-danger',
                                            'transferred' => 'bg-secondary text-secondary border-secondary'
                                        ];
                                        $labels = [
                                            'pending' => 'در جریان وصول',
                                            'deposited' => 'خوابانده به حساب',
                                            'cleared' => 'پاس شده',
                                            'bounced' => 'برگشت خورده',
                                            'transferred' => 'انتقال به غیر (خرج شده)'
                                        ];
                                        $badgeClass = $badges[$ch->status] ?? 'bg-light text-dark';
                                        $statusLabel = $labels[$ch->status] ?? $ch->status;
                                    ?>
                                    <span class="badge bg-opacity-10 border border-opacity-25 rounded-pill px-2 py-1 <?php echo $badgeClass; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle rounded-3 px-2 py-1" type="button" data-bs-toggle="dropdown">
                                            اقدام
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                            <li><a class="dropdown-item" href="index.php?page=admin&action=change_cheque_status&id=<?php echo $ch->id; ?>&status=cleared"><i class="fas fa-check text-success me-2"></i> وصول و پاس شدن</a></li>
                                            <li><a class="dropdown-item" href="index.php?page=admin&action=change_cheque_status&id=<?php echo $ch->id; ?>&status=bounced"><i class="fas fa-ban text-danger me-2"></i> اعلام برگشتی</a></li>
                                            <li><a class="dropdown-item" href="index.php?page=admin&action=change_cheque_status&id=<?php echo $ch->id; ?>&status=deposited"><i class="fas fa-building-columns text-info me-2"></i> خواباندن به حساب</a></li>
                                        </ul>
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
