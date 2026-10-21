<?php require APPROOT . '/views/layouts/admin_layout.php'; ?>
<?php
// app/views/accounting/coding.php - کدینگ درختی و لایه‌بندی حساب‌ها
$accounts = $accounts ?? $data['accounts'] ?? [];

$totalAccounts = count($accounts);
$assetCount = 0;
$liabilityCount = 0;
$equityCount = 0;
$incomeCount = 0;
$expenseCount = 0;

foreach ($accounts as $acc) {
    $type = strtolower($acc->type ?? '');
    if ($type === 'asset') $assetCount++;
    elseif ($type === 'liability') $liabilityCount++;
    elseif ($type === 'equity') $equityCount++;
    elseif ($type === 'income' || $type === 'revenue') $incomeCount++;
    elseif ($type === 'expense') $expenseCount++;
}
?>

<style>
    .coding-stat-card {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #ffffff;
        padding: 1.25rem;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.02);
        transition: all 0.3s ease;
    }
    .coding-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.1);
        border-color: #c7d2fe;
    }
    .account-badge-type {
        font-size: 0.75rem;
        font-weight: 800;
        padding: 0.35rem 0.8rem;
        border-radius: 50px;
    }
    .badge-asset { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .badge-liability { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
    .badge-equity { background: #e0e7ff; color: #3730a3; border: 1px solid #c7d2fe; }
    .badge-income { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .badge-expense { background: #f3e8ff; color: #6b21a8; border: 1px solid #e9d5ff; }

    .table-coding-ultra {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-coding-ultra th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }
    .table-coding-ultra td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
        vertical-align: middle;
    }
    .table-coding-ultra tr:hover td {
        background-color: #f8fafc;
    }
    .tree-indent-0 { font-weight: 800; background-color: #ffffff; }
    .tree-indent-1 { padding-right: 2.5rem !important; }
    .tree-indent-2 { padding-right: 4rem !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-extrabold text-dark mb-1 d-flex align-items-center gap-2">
            <i class="fas fa-sitemap text-indigo-600" style="color:#6366f1;"></i>
            <span>کدینگ و ساختار درختی حساب‌ها</span>
        </h3>
        <p class="text-muted small mb-0">مدیریت سطوح حساب‌های کل، معین و تفصیلی سیستم مالی</p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="index.php?page=admin&action=create_account_form" class="btn btn-primary rounded-pill px-4 py-2.5 shadow-sm fw-bold d-inline-flex align-items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>تعریف حساب جدید</span>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4 col-6">
        <div class="coding-stat-card text-center">
            <span class="text-muted small fw-bold d-block mb-1">کل حساب‌ها</span>
            <h4 class="fw-black text-dark font-monospace mb-0"><?php echo number_format($totalAccounts); ?></h4>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="coding-stat-card text-center">
            <span class="text-muted small fw-bold d-block mb-1">دارایی‌ها</span>
            <h4 class="fw-black text-emerald-600 font-monospace mb-0" style="color:#059669;"><?php echo number_format($assetCount); ?></h4>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="coding-stat-card text-center">
            <span class="text-muted small fw-bold d-block mb-1">بدهی‌ها</span>
            <h4 class="fw-black text-danger font-monospace mb-0"><?php echo number_format($liabilityCount); ?></h4>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="coding-stat-card text-center">
            <span class="text-muted small fw-bold d-block mb-1">حقوق مالکان</span>
            <h4 class="fw-black text-indigo-600 font-monospace mb-0" style="color:#4338ca;"><?php echo number_format($equityCount); ?></h4>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="coding-stat-card text-center">
            <span class="text-muted small fw-bold d-block mb-1">درآمدها</span>
            <h4 class="fw-black text-warning font-monospace mb-0"><?php echo number_format($incomeCount); ?></h4>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6">
        <div class="coding-stat-card text-center">
            <span class="text-muted small fw-bold d-block mb-1">هزینه‌ها</span>
            <h4 class="fw-black text-purple-600 font-monospace mb-0" style="color:#7e22ce;"><?php echo number_format($expenseCount); ?></h4>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
    <div class="card-header bg-white border-bottom p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fas fa-list-numeric text-indigo-600" style="color:#6366f1;"></i>
            <span>فهرست کامل کدینگ و گروه حساب‌ها</span>
        </h6>
        <div class="col-12 col-md-4">
            <input type="text" id="codingSearch" class="form-control rounded-3 border-light bg-light text-sm" placeholder="جستجو بر اساس کد یا نام حساب...">
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-coding-ultra" id="codingTable">
                <thead>
                    <tr>
                        <th class="px-4" style="width: 140px;">کد حساب</th>
                        <th>عنوان حساب</th>
                        <th class="text-center" style="width: 160px;">ماهیت</th>
                        <th class="text-center" style="width: 140px;">سطح لایه</th>
                        <th class="text-end px-4" style="width: 120px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($accounts)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fs-2 mb-2 d-block opacity-40"></i>
                                هیچ حسابی در جدول کدینگ یافت نشد.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($accounts as $acc): ?>
                            <?php
                                $type = strtolower($acc->type ?? 'asset');
                                $typeLabel = 'دارایی';
                                $typeClass = 'badge-asset';

                                if ($type === 'liability') { $typeLabel = 'بدهی'; $typeClass = 'badge-liability'; }
                                elseif ($type === 'equity') { $typeLabel = 'حقوق صاحبان سهام'; $typeClass = 'badge-equity'; }
                                elseif ($type === 'income' || $type === 'revenue') { $typeLabel = 'درآمد'; $typeClass = 'badge-income'; }
                                elseif ($type === 'expense') { $typeLabel = 'هزینه'; $typeClass = 'badge-expense'; }

                                $indentClass = empty($acc->parent_id) ? 'tree-indent-0' : 'tree-indent-1';
                                $levelLabel = empty($acc->parent_id) ? 'حساب کل / گروه' : 'حساب معین';
                            ?>
                            <tr class="coding-row" data-search="<?php echo sanitize($acc->code . ' ' . $acc->name); ?>">
                                <td class="px-4 font-monospace fw-black text-indigo-600" style="color:#4338ca;">
                                    <?php echo sanitize($acc->code); ?>
                                </td>
                                <td class="<?php echo $indentClass; ?>">
                                    <span class="fw-bold text-dark"><?php echo sanitize($acc->name); ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="account-badge-type <?php echo $typeClass; ?>"><?php echo $typeLabel; ?></span>
                                </td>
                                <td class="text-center small text-muted font-monospace fw-semibold">
                                    <?php echo $levelLabel; ?>
                                </td>
                                <td class="text-end px-4">
                                    <a href="index.php?page=admin&action=account_ledger&account_id=<?php echo $acc->id; ?>" class="btn btn-sm btn-light border text-primary rounded-circle" style="width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center;" title="دفتر حساب">
                                        <i class="fas fa-book"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('codingSearch');
    const rows = document.querySelectorAll('.coding-row');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            rows.forEach(row => {
                const text = row.getAttribute('data-search').toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
});
</script>