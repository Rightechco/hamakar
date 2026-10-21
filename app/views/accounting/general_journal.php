<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/general_journal.php - دفتر روزنامه اسناد
$journalData = $journalData ?? $data['journalData'] ?? [];
$startDateJalali = $startDateJalali ?? $data['startDateJalali'] ?? (function_exists('jdate') ? jdate('Y/m/01') : '');
$endDateJalali = $endDateJalali ?? $data['endDateJalali'] ?? (function_exists('jdate') ? jdate('Y/m/d') : '');
?>

<style>
    .journal-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
    }
    .table-journal th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-journal td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
        vertical-align: middle;
    }
    .table-journal tr:hover td {
        background-color: #f8fafc;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-book text-indigo-600" style="color:#6366f1;"></i>
            <span>دفتر روزنامه اسناد حسابداری</span>
        </h3>
        <p class="text-muted small mb-0">مشاهده کلیه تراکنش‌های مالی و اسناد دوبل به ترتیب تاریخ ثبت</p>
    </div>

    <?php if ($auth->hasRole(['admin', 'accountant'])): ?>
        <a href="index.php?page=admin&action=create_voucher_form" class="btn btn-primary rounded-pill px-4 py-2.5 shadow-sm fw-bold">
            <i class="fas fa-plus me-1"></i> ثبت سند جدید
        </a>
    <?php endif; ?>
</div>

<!-- فرم فیلتر بازه زمانی -->
<div class="card journal-card mb-4 p-3.5">
    <form action="index.php?page=admin&action=general_journal" method="POST" class="row g-3 align-items-center">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-dark">از تاریخ:</label>
            <input type="text" name="start_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $startDateJalali; ?>" placeholder="1403/01/01" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-dark">تا تاریخ:</label>
            <input type="text" name="end_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $endDateJalali; ?>" placeholder="1403/12/29" required>
        </div>
        <div class="col-md-4 mt-md-4">
            <button type="submit" class="btn btn-indigo w-100 rounded-3 fw-bold" style="background:#6366f1; color:#fff;">
                <i class="fas fa-filter me-1"></i> مشاهده دفتر روزنامه
            </button>
        </div>
    </form>
</div>

<div class="card journal-card overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-journal w-100">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 90px;">شماره</th>
                        <th class="text-center" style="width: 110px;">تاریخ</th>
                        <th>شرح عمومی سند / بابت</th>
                        <th class="text-center">بدهکار (تومان)</th>
                        <th class="text-center">بستانکار (تومان)</th>
                        <th class="text-end px-4" style="width: 100px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($journalData)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-book-open fs-2 mb-2 d-block opacity-40"></i>
                                هیچ سندی در این بازه زمانی یافت نشد.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($journalData as $v): ?>
                            <tr>
                                <td class="text-center font-monospace fw-bold text-indigo-600" style="color:#4338ca;">#<?php echo sanitize($v->voucher_number ?? $v->id); ?></td>
                                <td class="text-center font-monospace small text-muted"><?php echo function_exists('jdate') ? jdate('Y/m/d', strtotime($v->voucher_date)) : $v->voucher_date; ?></td>
                                <td class="fw-bold text-dark"><?php echo sanitize($v->description); ?></td>
                                <td class="text-center font-monospace fw-bold text-emerald-600" style="color:#059669;"><?php echo number_format($v->total_debit ?? 0); ?></td>
                                <td class="text-center font-monospace fw-bold text-indigo-600" style="color:#4338ca;"><?php echo number_format($v->total_credit ?? 0); ?></td>
                                <td class="text-end px-4">
                                    <?php if ($auth->hasRole(['admin'])): ?>
                                        <form action="index.php?page=admin&action=delete_voucher&id=<?php echo $v->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این سند اطمینان دارید؟');">
                                            <button type="submit" class="btn btn-sm btn-light text-danger rounded-circle" style="width:34px; height:34px;" title="حذف سند"><i class="fas fa-trash"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>