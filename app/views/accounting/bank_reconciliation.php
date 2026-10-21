<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/bank_reconciliation.php - مغایرت‌گیری بانکی
$bankAccounts = $bankAccounts ?? $data['bankAccounts'] ?? [];
$deposits = $deposits ?? $data['deposits'] ?? [];
$payments = $payments ?? $data['payments'] ?? [];
?>

<style>
    .recon-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.03);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-university text-indigo-600" style="color:#6366f1;"></i>
            <span>مغایرت‌گیری و تطبیق صورتحساب بانک</span>
        </h3>
        <p class="text-muted small mb-0">تطبیق تراکنش‌های دفتر با صورتحساب رسمی بانک جهت استخراج مغایرت‌ها</p>
    </div>
</div>

<div class="card recon-card mb-4 p-4">
    <form action="index.php?page=admin&action=bank_reconciliation" method="POST" class="row g-3 align-items-end">
        <input type="hidden" name="start_reconciliation" value="1">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-dark">انتخاب حساب بانکی:</label>
            <select name="account_id" class="form-select rounded-3" required>
                <option value="">-- انتخاب بانک --</option>
                <?php foreach ($bankAccounts as $acc): ?>
                    <option value="<?php echo $acc->id; ?>"><?php echo sanitize($acc->name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-dark">تاریخ صورتحساب:</label>
            <input type="text" name="statement_date" class="form-control rounded-3 font-monospace text-center" placeholder="1403/01/01" required>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-dark">موجودی طبق صورتحساب بانک (تومان):</label>
            <input type="number" name="statement_balance" class="form-control rounded-3 font-monospace" placeholder="0" required>
        </div>
        <div class="col-12 text-end mt-3">
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">شروع عملیات مغایرت‌گیری</button>
        </div>
    </form>
</div>