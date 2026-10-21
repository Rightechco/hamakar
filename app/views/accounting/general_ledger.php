<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/general_ledger.php - دفتر کل حساب
$allAccounts = $allAccounts ?? $data['allAccounts'] ?? [];
$ledgerData = $ledgerData ?? $data['ledgerData'] ?? [];
$selectedAccountName = $selectedAccountName ?? $data['selectedAccountName'] ?? '';
$selectedAccountId = $selectedAccountId ?? $data['selectedAccountId'] ?? '';
?>

<style>
    .ledger-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
    }
    .table-ledger th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-ledger td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-book-open text-teal-600" style="color:#0d9488;"></i>
            <span>دفتر کل حساب</span>
        </h3>
        <p class="text-muted small mb-0">مشاهده گردش و مانده حساب به تفکیک آرتیکل‌های بدهکار و بستانکار</p>
    </div>
</div>

<div class="card ledger-card mb-4 p-4">
    <form action="index.php?page=admin&action=account_ledger" method="POST" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-bold text-dark">انتخاب حساب:</label>
            <select name="account_id" class="form-select rounded-3" required>
                <option value="">-- حساب مورد نظر را انتخاب کنید --</option>
                <?php foreach ($allAccounts as $acc): ?>
                    <option value="<?php echo $acc->id; ?>" <?php echo ($selectedAccountId == $acc->id) ? 'selected' : ''; ?>>[<?php echo sanitize($acc->code); ?>] <?php echo sanitize($acc->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-dark">از تاریخ:</label>
            <input type="text" name="start_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $startDateJalali ?? ''; ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-dark">تا تاریخ:</label>
            <input type="text" name="end_date" class="form-control rounded-3 font-monospace text-center" value="<?php echo $endDateJalali ?? ''; ?>" required>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-teal w-100 rounded-3 fw-bold" style="background:#0d9488; color:#fff;">نمایش</button>
        </div>
    </form>
</div>

<div class="card ledger-card overflow-hidden">
    <div class="card-header bg-white border-bottom p-3.5 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-dark">
            دفتر کل حساب: <span class="text-teal-600" style="color:#0d9488;"><?php echo sanitize($selectedAccountName ?? 'انتخاب نشده'); ?></span>
        </h6>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-ledger w-100">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 100px;">تاریخ</th>
                        <th class="text-center" style="width: 90px;">شماره سند</th>
                        <th>شرح آرتیکل / تراکنش</th>
                        <th class="text-center">بدهکار (تومان)</th>
                        <th class="text-center">بستانکار (تومان)</th>
                        <th class="text-center">مانده (تومان)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ledgerData['entries'] ?? [])): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                گردش حسابی برای نمایش یافت نشد.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ledgerData['entries'] as $entry): ?>
                            <tr>
                                <td class="text-center font-monospace small text-muted"><?php echo function_exists('jdate') ? jdate('Y/m/d', strtotime($entry->voucher_date)) : $entry->voucher_date; ?></td>
                                <td class="text-center font-monospace fw-bold text-teal-600" style="color:#0d9488;">#<?php echo $entry->voucher_number; ?></td>
                                <td class="fw-bold text-dark"><?php echo sanitize($entry->description); ?></td>
                                <td class="text-center font-monospace fw-bold text-emerald-600"><?php echo number_format($entry->debit); ?></td>
                                <td class="text-center font-monospace fw-bold text-indigo-600"><?php echo number_format($entry->credit); ?></td>
                                <td class="text-center font-monospace fw-black"><?php echo number_format($entry->running_balance ?? 0); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>