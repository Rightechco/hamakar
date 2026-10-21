<?php
// app/views/admin/contracts/index.php - نسخه اصلاح شده با رنگ‌بندی شفاف وضعیت‌ها
global $auth;
$contracts = $contracts ?? [];
$clients = $clients ?? [];
$clientNames = $clientNames ?? [];
$categories = $categories ?? [];
$filters = $filters ?? [];
$serviceTypes = $serviceTypes ?? [];

// محاسبات و آمارهای مالی پیشرفته
$totalContracts = count($contracts);
$activeContracts = 0;
$totalValue = 0;
$totalPrepayment = 0;
$totalRemaining = 0;

foreach ($contracts as $c) {
    if (($c->status ?? '') === 'active') $activeContracts++;
    
    $total = (float)($c->total_amount ?? 0);
    $prepayment = (float)($c->prepayment_amount ?? 0);
    $remaining = $total - $prepayment;

    $totalValue += $total;
    $totalPrepayment += $prepayment;
    $totalRemaining += ($remaining > 0) ? $remaining : 0;
}
?>

<!-- کتابخانه آیکون‌ها و فونت -->
<link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
    .contract-index-wrapper {
        font-family: 'Vazirmatn', sans-serif !important;
        color: #0f172a;
    }

    .fin-card { 
        background: #ffffff; 
        border-radius: 20px; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 10px 30px -5px rgba(0,0,0,0.03); 
        overflow: hidden; 
        margin-bottom: 1.5rem; 
    }
    .fin-card-header { 
        padding: 1.25rem 1.5rem; 
        background: #f8fafc; 
        border-bottom: 1px solid #e2e8f0; 
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .fin-card-title { 
        font-weight: 800; 
        color: #0f172a; 
        font-size: 1rem; 
        margin: 0; 
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-fin-primary { 
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); 
        color: #fff !important; 
        border: none; 
        border-radius: 12px; 
        padding: 0.65rem 1.35rem; 
        font-weight: 800; 
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.25);
        transition: all 0.2s ease; 
        text-decoration: none;
    }
    .btn-fin-primary:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.38); 
    }

    .btn-fin-action { 
        background: #ffffff; 
        color: #475569 !important; 
        border: 1px solid #cbd5e1; 
        border-radius: 10px; 
        padding: 0.55rem 1rem; 
        font-size: 0.85rem; 
        font-weight: 700; 
        transition: all 0.2s ease; 
        text-decoration: none;
    }
    .btn-fin-action:hover { 
        background: #f1f5f9; 
        color: #0f172a !important; 
        border-color: #94a3b8;
    }

    /* کارت‌های شاخص‌های مالی (KPIs) */
    .stat-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.2rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
    }
    .stat-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }
    .stat-kpi-icon {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }

    .form-control-premium, .form-select-premium { 
        border-radius: 12px !important; 
        border: 1px solid #cbd5e1 !important; 
        padding: 0.6rem 0.9rem !important; 
        font-size: 0.85rem !important; 
    }
    .form-control-premium:focus, .form-select-premium:focus {
        border-color: #4338ca !important;
        box-shadow: 0 0 0 4px rgba(67, 56, 202, 0.12) !important;
    }

    /* جدول پرمیوم */
    .table-premium { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-premium th { 
        background: #f8fafc; 
        color: #475569; 
        font-weight: 800; 
        font-size: 0.82rem; 
        padding: 1.1rem 0.9rem; 
        border-bottom: 1px solid #e2e8f0; 
        white-space: nowrap;
    }
    .table-premium td { 
        padding: 1rem 0.9rem; 
        border-bottom: 1px solid #f1f5f9; 
        font-size: 0.875rem; 
        color: #1e293b; 
        vertical-align: middle; 
    }
    .table-premium tr:hover td { background-color: #f8fafc; }

    /* استایل اختصاصی نشانگر وضعیت */
    .badge-status { 
        padding: 0.45em 0.95em; 
        font-size: 0.78rem; 
        font-weight: 800; 
        border-radius: 50rem;
        display: inline-block;
        line-height: 1.2;
    }

    .remaining-pill {
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
        padding: 0.25rem 0.65rem;
        border-radius: 8px;
        font-weight: 800;
        font-family: monospace;
    }

    .prepayment-pill {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
        padding: 0.25rem 0.65rem;
        border-radius: 8px;
        font-weight: 800;
        font-family: monospace;
    }

    @media print {
        .no-print, #sidebar, .top-header-bar { display: none !important; }
        body { background: white !important; }
        .fin-card { border: 1px solid #ccc !important; box-shadow: none !important; }
    }
</style>

<div class="contract-index-wrapper">
    <!-- هدر اصلی -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">مدیریت قراردادها</h1>
            <p class="text-muted small mb-0">بانک جامع اسناد رسمی، مبالغ کل، وضعیت پیش‌پرداخت‌ها و مطالبات مانده</p>
        </div>
        
        <div class="d-flex align-items-center gap-2 no-print">
            <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-2">
                <i class="fas fa-print text-indigo-600"></i>
                <span>چاپ لیست</span>
            </button>
            <?php if ($auth->hasRole(['admin', 'accountant'])): ?>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=export_contracts_to_excel" class="btn btn-fin-action d-inline-flex align-items-center gap-2">
                    <i class="fas fa-file-excel text-success"></i>
                    <span>خروجی اکسل</span>
                </a>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=contracts_create" class="btn btn-fin-primary d-inline-flex align-items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>افزودن قرارداد جدید</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- کارت‌های شاخص‌های کلیدی مالی (KPIs) -->
    <div class="row g-3 mb-4 no-print">
        <div class="col-xl-3 col-md-6">
            <div class="stat-kpi-card">
                <div class="stat-kpi-icon" style="background:#e0e7ff; color:#3730a3;">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div>
                    <span class="text-muted small d-block fw-bold">کل قراردادها</span>
                    <span class="fs-4 fw-black font-monospace text-dark"><?php echo number_format($totalContracts); ?> <small class="fs-7 text-muted">(<?php echo $activeContracts; ?> فعال)</small></span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-kpi-card">
                <div class="stat-kpi-icon" style="background:#dbeafe; color:#1d4ed8;">
                    <i class="fas fa-vault"></i>
                </div>
                <div>
                    <span class="text-muted small d-block fw-bold">مجموع ارزش قراردادها</span>
                    <span class="fs-5 fw-black font-monospace text-dark"><?php echo number_format($totalValue); ?> <small class="fs-7 text-muted">تومان</small></span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-kpi-card">
                <div class="stat-kpi-icon" style="background:#dcfce7; color:#15803d;">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div>
                    <span class="text-muted small d-block fw-bold">مجموع پیش‌پرداخت‌ها</span>
                    <span class="fs-5 fw-black font-monospace text-emerald-700" style="color: #15803d;"><?php echo number_format($totalPrepayment); ?> <small class="fs-7 text-muted">تومان</small></span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-kpi-card">
                <div class="stat-kpi-icon" style="background:#fee2e2; color:#991b1b;">
                    <i class="fas fa-clock" style="color: #991b1b;"></i>
                </div>
                <div>
                    <span class="text-muted small d-block fw-bold">مجموع مطالبات (مانده)</span>
                    <span class="fs-5 fw-black font-monospace" style="color: #991b1b;"><?php echo number_format($totalRemaining); ?> <small class="fs-7 text-muted">تومان</small></span>
                </div>
            </div>
        </div>
    </div>

    <!-- بخش فیلتر و جدول لیست -->
    <div class="fin-card" id="contracts-table-area">
        <div class="fin-card-header no-print">
            <h6 class="fin-card-title"><i class="fas fa-filter text-indigo-600"></i> فیلتر و جستجوی پیشرفته</h6>
        </div>

        <!-- فرم فیلتر -->
        <div class="card-body p-4 no-print border-bottom">
            <form id="filter-form" action="<?php echo APP_URL; ?>/index.php" method="GET">
                <input type="hidden" name="page" value="admin">
                <input type="hidden" name="action" value="contracts">
                <div class="row g-3 align-items-end">
                    <div class="col-md-2">
                        <label for="search" class="form-label fw-bold text-secondary small">جستجو:</label>
                        <input type="text" class="form-control form-control-premium" id="search" name="search" placeholder="عنوان یا نام کارفرما..." value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="client_id" class="form-label fw-bold text-secondary small">کارفرما:</label>
                        <select class="form-select form-select-premium" id="client_id" name="client_id">
                            <option value="">همه کارفرمایان</option>
                            <?php foreach($clients as $client): ?>
                                <option value="<?php echo $client->id; ?>" <?php echo (isset($filters['client_id']) && $filters['client_id'] == $client->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($client->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label fw-bold text-secondary small">وضعیت:</label>
                        <select class="form-select form-select-premium" id="status" name="status">
                            <option value="">همه وضعیت‌ها</option>
                            <option value="active" <?php echo (isset($filters['status']) && $filters['status'] == 'active') ? 'selected' : ''; ?>>فعال</option>
                            <option value="pending" <?php echo (isset($filters['status']) && $filters['status'] == 'pending') ? 'selected' : ''; ?>>در انتظار</option>
                            <option value="completed" <?php echo (isset($filters['status']) && $filters['status'] == 'completed') ? 'selected' : ''; ?>>تکمیل شده</option>
                            <option value="canceled" <?php echo (isset($filters['status']) && $filters['status'] == 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="category_id" class="form-label fw-bold text-secondary small">دسته‌بندی:</label>
                        <select class="form-select form-select-premium" id="category_id" name="category_id">
                            <option value="">همه دسته‌ها</option>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category->id; ?>" <?php echo (isset($filters['category_id']) && $filters['category_id'] == $category->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="start_date_jalali" class="form-label fw-bold text-secondary small">از تاریخ:</label>
                        <input type="text" class="form-control form-control-premium persian-datepicker" id="start_date_jalali" value="<?php echo htmlspecialchars($filters['start_date_jalali'] ?? ''); ?>" autocomplete="off">
                        <input type="hidden" id="start_date" name="start_date" value="<?php echo htmlspecialchars($filters['start_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date_jalali" class="form-label fw-bold text-secondary small">تا تاریخ:</label>
                        <input type="text" class="form-control form-control-premium persian-datepicker" id="end_date_jalali" value="<?php echo htmlspecialchars($filters['end_date_jalali'] ?? ''); ?>" autocomplete="off">
                        <input type="hidden" id="end_date" name="end_date" value="<?php echo htmlspecialchars($filters['end_date'] ?? ''); ?>">
                    </div>
                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                        <button type="submit" class="btn btn-fin-primary px-4"><i class="fas fa-search me-1"></i> اعمال فیلتر</button>
                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=contracts" class="btn btn-outline-secondary rounded-3 px-3"><i class="fas fa-undo me-1"></i> بازنشانی</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- جدول پرمیوم قراردادها -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table-premium w-100">
                    <thead>
                        <tr>
                            <th style="width: 60px;">شناسه</th>
                            <th>عنوان قرارداد</th>
                            <th>کارفرما / مشتری</th>
                            <th>مبلغ کل (تومان)</th>
                            <th>پیش‌پرداخت</th>
                            <th>مبلغ مانده</th>
                            <th>مالیات</th>
                            <th class="text-center">تاریخ شروع</th>
                            <th class="text-center">تاریخ پایان</th>
                            <th class="text-center">وضعیت</th>
                            <th class="text-center no-print" style="width: 130px;">عملیات</th>
                        </tr>
                    </thead>
                    <tbody id="contracts-table-body">
                        <?php if (!empty($contracts)): ?>
                            <?php foreach ($contracts as $contract): 
                                $total = (float)($contract->total_amount ?? 0);
                                $prepayment = (float)($contract->prepayment_amount ?? 0);
                                $remaining = $total - $prepayment;

                                // تعیین استایل مستقیم وضعیت جهت جلوگیری از سفید شدن متون
                                $statusStyle = 'background-color: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;';
                                $statusText = htmlspecialchars($contract->status ?? '');

                                if ($contract->status === 'active') {
                                    $statusStyle = 'background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;';
                                    $statusText = 'فعال';
                                } elseif ($contract->status === 'pending') {
                                    $statusStyle = 'background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;';
                                    $statusText = 'در انتظار';
                                } elseif ($contract->status === 'completed') {
                                    $statusStyle = 'background-color: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe;';
                                    $statusText = 'تکمیل شده';
                                } elseif ($contract->status === 'canceled') {
                                    $statusStyle = 'background-color: #ffe4e6; color: #be123c; border: 1px solid #fecdd3;';
                                    $statusText = 'لغو شده';
                                }
                            ?>
                                <tr>
                                    <td class="font-monospace fw-bold text-slate-500">#<?php echo htmlspecialchars($contract->id); ?></td>
                                    <td class="fw-bold text-slate-800">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_contract&id=<?php echo $contract->id; ?>" target="_blank" class="text-decoration-none text-dark hover-indigo">
                                            <?php echo htmlspecialchars($contract->title); ?>
                                        </a>
                                    </td>
                                    <td><span class="small fw-bold text-slate-700"><?php echo htmlspecialchars($clientNames[$contract->client_id] ?? 'نامشخص'); ?></span></td>
                                    <td class="font-monospace fw-bold text-indigo-700"><?php echo number_format($total); ?></td>
                                    <td class="font-monospace"><span class="prepayment-pill"><?php echo number_format($prepayment); ?></span></td>
                                    <td class="font-monospace"><span class="remaining-pill"><?php echo number_format($remaining); ?></span></td>
                                    <td class="font-monospace text-slate-600"><?php echo htmlspecialchars($contract->vat_rate ?? 0); ?>٪</td>
                                    <td class="text-center"><span class="badge bg-light text-dark font-monospace border"><?php echo htmlspecialchars(!empty($contract->start_date) ? jdate('Y/m/d', strtotime($contract->start_date)) : '---'); ?></span></td>
                                    <td class="text-center"><span class="badge bg-light text-dark font-monospace border"><?php echo htmlspecialchars(!empty($contract->end_date) ? jdate('Y/m/d', strtotime($contract->end_date)) : '---'); ?></span></td>
                                    <td class="text-center">
                                        <span class="badge badge-status" style="<?php echo $statusStyle; ?>">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td class="text-center no-print">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_contract&id=<?php echo $contract->id; ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-2 px-2 py-1" title="مشاهده سند">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($auth->hasRole(['admin', 'accountant'])): ?>
                                                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=contracts_edit&id=<?php echo $contract->id; ?>" class="btn btn-sm btn-outline-primary rounded-2 px-2 py-1" title="ویرایش">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                            <?php if ($auth->hasRole(['admin'])): ?>
                                                <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=contracts_delete&id=<?php echo $contract->id; ?>" method="POST" class="d-inline">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1" title="حذف" onclick="return confirm('آیا از حذف این قرارداد مطمئن هستید؟');">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="11" class="text-center text-muted py-5"><i class="fas fa-folder-open fs-2 mb-2 d-block opacity-50"></i>هیچ قراردادی یافت نشد.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>