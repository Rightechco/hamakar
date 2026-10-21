<?php
// app/views/admin/accounting/accounts_index.php - نسخه اولترا پرمیوم کدینگ حسابداری

// تابع بازنویسی شده برای نمایش درختی و شیک حساب‌ها
function display_accounts_tree($accounts, $level = 0) {
    $typeBadges = [
        'asset' => ['label' => 'دارایی', 'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200'],
        'liability' => ['label' => 'بدهی', 'class' => 'bg-rose-100 text-rose-700 border-rose-200'],
        'equity' => ['label' => 'حقوق سهام', 'class' => 'bg-amber-100 text-amber-700 border-amber-200'],
        'income' => ['label' => 'درآمد', 'class' => 'bg-sky-100 text-sky-700 border-sky-200'],
        'expense' => ['label' => 'هزینه', 'class' => 'bg-purple-100 text-purple-700 border-purple-200']
    ];

    foreach ($accounts as $account) {
        $typeInfo = $typeBadges[$account->type] ?? ['label' => $account->type, 'class' => 'bg-slate-100 text-slate-700'];
        $paddingRight = $level * 24; // ایجاد تورفتگی برای حساب‌های فرزند
        ?>
        <tr class="tree-row <?php echo $level === 0 ? 'fw-bold bg-slate-50/50' : ''; ?>">
            <td class="align-middle">
                <span class="font-monospace fw-bold text-indigo-600 px-2 py-1 bg-indigo-50 rounded-2 border border-indigo-100">
                    <?php echo htmlspecialchars($account->code); ?>
                </span>
            </td>
            <td class="align-middle">
                <div style="padding-right: <?php echo $paddingRight; ?>px;" class="d-flex align-items-center gap-2">
                    <?php if ($level > 0): ?>
                        <span class="text-slate-300 fs-6">↳</span>
                        <i class="far fa-folder text-indigo-400"></i>
                    <?php else: ?>
                        <i class="fas fa-folder text-amber-500 fs-6"></i>
                    <?php endif; ?>
                    <span class="<?php echo $level === 0 ? 'text-slate-900 fw-bold' : 'text-slate-700'; ?>">
                        <?php echo htmlspecialchars($account->name); ?>
                    </span>
                </div>
            </td>
            <td class="align-middle text-center">
                <span class="badge border rounded-pill px-3 py-1.5 fw-bold fs-7 <?php echo $typeInfo['class']; ?>">
                    <?php echo $typeInfo['label']; ?>
                </span>
            </td>
            <td class="align-middle text-center">
                <a href="#" class="btn btn-sm btn-light border hover-bg-indigo text-slate-600 rounded-2 px-3 py-1 transition">
                    <i class="fas fa-edit me-1 text-slate-400"></i> ویرایش
                </a>
            </td>
        </tr>
        <?php
        if (!empty($account->children)) {
            display_accounts_tree($account->children, $level + 1);
        }
    }
}
?>

<style>
    .fin-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1.5rem; }
    .fin-card-header { padding: 1.25rem 1.5rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .fin-card-title { font-weight: 800; color: #0f172a; font-size: 1rem; margin: 0; display: flex; align-items: center; gap: 0.5rem; }
    
    .btn-fin-primary { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #fff; border: none; border-radius: 12px; padding: 0.6rem 1.25rem; font-weight: 700; transition: all 0.2s; text-decoration: none; }
    .btn-fin-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(99,102,241,0.35); color: #fff; }

    .table-coding { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-coding th { background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.8rem; padding: 1rem 1.25rem; border-bottom: 1px solid #e2e8f0; }
    .table-coding td { padding: 0.85rem 1.25rem; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; }
    .table-coding tr:hover td { background-color: #f8fafc; }
    
    /* کلاس‌های رنگی بج نوع حساب */
    .bg-emerald-100 { background-color: #d1fae5; } .text-emerald-700 { color: #047857; } .border-emerald-200 { border-color: #a7f3d0; }
    .bg-rose-100 { background-color: #ffe4e6; } .text-rose-700 { color: #be123c; } .border-rose-200 { border-color: #fecdd3; }
    .bg-amber-100 { background-color: #fef3c7; } .text-amber-700 { color: #b45309; } .border-amber-200 { border-color: #fde68a; }
    .bg-sky-100 { background-color: #e0f2fe; } .text-sky-700 { color: #0369a1; } .border-sky-200 { border-color: #bae6fd; }
    .bg-purple-100 { background-color: #f3e8ff; } .text-purple-700 { color: #6b21a8; } .border-purple-200 { border-color: #e9d5ff; }
</style>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">کدینگ و سرفصل‌های حسابداری</h1>
        <p class="text-muted small mb-0">ساختار درختی طبقه حساب‌ها و کدگذاری عمومی سیستم</p>
    </div>
    <a href="index.php?page=admin&action=create_account_form" class="btn btn-fin-primary d-inline-flex align-items-center gap-2">
        <i class="fas fa-plus fs-6"></i>
        <span>افزودن حساب جدید</span>
    </a>
</div>

<div class="fin-card">
    <div class="fin-card-header">
        <h6 class="fin-card-title">
            <i class="fas fa-sitemap text-indigo-500" style="color:#6366f1;"></i>
            <span>لیست ساختار درختی حساب‌ها</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-coding">
                <thead>
                    <tr>
                        <th style="width: 140px;">کد حساب</th>
                        <th>نام حساب</th>
                        <th class="text-center" style="width: 160px;">نوع حساب</th>
                        <th class="text-center" style="width: 120px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($accounts)): ?>
                        <?php display_accounts_tree($accounts); ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="fas fa-folder-open fs-2 mb-2 d-block opacity-50"></i>
                                هیچ حسابداری و کدینگی ثبت نشده است.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>