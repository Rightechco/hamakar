<?php
// app/views/admin/accounting/create_account.php - نسخه اولترا پرمیوم فرم ایجاد حساب
?>
<style>
    .fin-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1.5rem; }
    .fin-card-header { padding: 1.25rem 1.5rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; }
    .fin-card-title { font-weight: 800; color: #0f172a; font-size: 1rem; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    
    .btn-fin-primary { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #fff; border: none; border-radius: 12px; padding: 0.65rem 1.5rem; font-weight: 700; transition: all 0.2s; }
    .btn-fin-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(99,102,241,0.35); color: #fff; }
    
    .btn-fin-secondary { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 12px; padding: 0.65rem 1.5rem; font-weight: 700; transition: all 0.2s; text-decoration: none; }
    .btn-fin-secondary:hover { background: #e2e8f0; color: #0f172a; }

    .form-control-premium, .form-select-premium {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.65rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    .form-control-premium:focus, .form-select-premium:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">افزودن سرفصل حساب جدید</h1>
        <p class="text-muted small mb-0">تعریف لایه‌های جدید کل یا معین در کدینگ حسابداری</p>
    </div>
</div>

<div class="fin-card max-w-4xl">
    <div class="fin-card-header">
        <h6 class="fin-card-title">
            <i class="fas fa-folder-plus text-indigo-500" style="color:#6366f1;"></i>
            <span>مشخصات و اطلاعات حساب</span>
        </h6>
    </div>
    <div class="card-body p-4 p-md-5">
        <form action="index.php?page=admin&action=store_account" method="POST">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label for="parent_id" class="form-label fw-bold text-secondary small">حساب والد (اختیاری):</label>
                    <select class="form-select form-select-premium" id="parent_id" name="parent_id">
                        <option value="">-- حساب اصلی (بدون والد) --</option>
                        <?php foreach ($accounts as $account): ?>
                            <option value="<?php echo $account->id; ?>">
                                <?php echo htmlspecialchars($account->name) . ' (' . htmlspecialchars($account->code) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text text-muted small mt-1">در صورت انتخاب والد، این حساب به صورت معین/زیرمجموعه آن قرار می‌گیرد.</div>
                </div>

                <div class="col-md-6">
                    <label for="type" class="form-label fw-bold text-secondary small">نوع حساب:</label>
                    <select class="form-select form-select-premium" id="type" name="type" required>
                        <option value="asset">دارایی (Asset)</option>
                        <option value="liability">بدهی (Liability)</option>
                        <option value="equity">حقوق صاحبان سهام (Equity)</option>
                        <option value="income">درآمد (Income)</option>
                        <option value="expense">هزینه (Expense)</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="code" class="form-label fw-bold text-secondary small">کد حساب:</label>
                    <input type="text" class="form-control form-control-premium font-monospace" id="code" name="code" placeholder="مثال: 1101" required>
                </div>

                <div class="col-md-6">
                    <label for="name" class="form-label fw-bold text-secondary small">نام حساب:</label>
                    <input type="text" class="form-control form-control-premium" id="name" name="name" placeholder="مثال: موجودی نقد و بانک" required>
                </div>
            </div>

            <hr class="my-4 border-slate-200">

            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-fin-primary d-inline-flex align-items-center gap-2">
                    <i class="fas fa-save fs-6"></i>
                    <span>ذخیره حساب</span>
                </button>
                <a href="index.php?page=admin&action=accounting_accounts" class="btn btn-fin-secondary d-inline-flex align-items-center gap-2">
                    <i class="fas fa-arrow-right fs-6"></i>
                    <span>بازگشت به لیست</span>
                </a>
            </div>
        </form>
    </div>
</div>