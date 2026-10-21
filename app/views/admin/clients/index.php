<?php
// app/views/admin/clients/index.php - نسخه اولترا پرمیوم، کاملاً ایزوله و شیک
global $auth;
$clients = $clients ?? [];
$userNames = $userNames ?? [];
$filters = $filters ?? [];
?>

<!-- لینک مستقیم به فاز آیکون‌ها جهت اطمینان از نمایش -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
    /* ایزوله‌سازی کامل استایل‌ها بدون وابستگی به Bootstrap */
    .client-page-container {
        width: 100%;
        margin: 0 auto;
        font-family: inherit;
    }

    .header-action-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .fin-card { 
        background: #ffffff; 
        border-radius: 20px; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); 
        overflow: hidden; 
        margin-bottom: 1.5rem; 
    }

    .fin-card-header { 
        padding: 1.25rem 1.5rem; 
        background: #ffffff; 
        border-bottom: 1px solid #f1f5f9; 
        display: flex;
        justify-content: space-between;
        align-items: center;
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
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); 
        color: #ffffff !important; 
        border: none; 
        border-radius: 12px; 
        padding: 0.65rem 1.25rem; 
        font-weight: 700; 
        font-size: 0.875rem;
        transition: all 0.2s ease; 
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .btn-fin-primary:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 20px rgba(99,102,241,0.35); 
    }

    .btn-fin-action { 
        background: #f1f5f9; 
        color: #334155 !important; 
        border: 1px solid #cbd5e1; 
        border-radius: 10px; 
        padding: 0.5rem 0.9rem; 
        font-size: 0.825rem; 
        font-weight: 700; 
        transition: all 0.2s ease; 
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
    }

    .btn-fin-action:hover { 
        background: #e2e8f0; 
        color: #0f172a !important; 
    }

    /* سیستم Grid اختصاصی فرم جهت جلوگیری از به هم خوردگی */
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .form-group-custom {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .form-group-custom label {
        font-weight: 700;
        font-size: 0.8rem;
        color: #475569;
    }

    .form-control-custom, .form-select-custom { 
        width: 100%;
        border-radius: 10px; 
        border: 1px solid #cbd5e1; 
        padding: 0.6rem 0.85rem; 
        font-size: 0.85rem; 
        outline: none;
        background-color: #ffffff;
        box-sizing: border-box;
        transition: all 0.2s ease;
    }

    .form-control-custom:focus, .form-select-custom:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
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
        padding: 1rem; 
        border-bottom: 1px solid #e2e8f0; 
        text-align: right;
    }

    .table-premium td { 
        padding: 0.85rem 1rem; 
        border-bottom: 1px solid #f1f5f9; 
        font-size: 0.875rem; 
        color: #1e293b; 
        vertical-align: middle; 
        text-align: right;
    }

    .table-premium tr:hover td { 
        background-color: #f8fafc; 
    }

    .badge-custom {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        border: 1px solid transparent;
    }

    .badge-real {
        background-color: #e0f2fe;
        color: #0369a1;
        border-color: #bae6fd;
    }

    .badge-legal {
        background-color: #e0e7ff;
        color: #4338ca;
        border-color: #c7d2fe;
    }

    .action-btn-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #475569;
        text-decoration: none !important;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-icon:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .btn-icon.edit:hover { background: #e0e7ff; color: #4338ca; border-color: #c7d2fe; }
    .btn-icon.logs:hover { background: #fef3c7; color: #b45309; border-color: #fde68a; }
    .btn-icon.delete:hover { background: #ffe4e6; color: #be123c; border-color: #fecdd3; }

    @media print {
        .no-print, #sidebar, .top-header-bar, #chat-toggler, #chat-window { display: none !important; }
        body { background: white !important; }
        .fin-card { border: 1px solid #ccc !important; box-shadow: none !important; }
    }
</style>

<div class="client-page-container">
    <div class="header-action-bar">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">مدیریت مشتریان و کارفرمایان</h1>
            <p class="text-muted small mb-0">بانک اطلاعاتی جامع اشخاص حقیقی، حقوقی و حساب‌های کاربری مرتبط</p>
        </div>
        
        <div class="d-flex align-items-center gap-2 no-print">
            <button onclick="window.print()" class="btn-fin-action">
                <i class="fas fa-print text-indigo-600"></i>
                <span>چاپ لیست</span>
            </button>
            <?php if ($auth->hasRole(['admin', 'accountant'])): ?>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=export_clients_to_excel" class="btn-fin-action">
                    <i class="fas fa-file-excel text-success"></i>
                    <span>خروجی اکسل</span>
                </a>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=show_import_clients_form" class="btn-fin-action">
                    <i class="fas fa-file-import text-info"></i>
                    <span>ورود از اکسل</span>
                </a>
            <?php endif; ?>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_create" class="btn-fin-primary">
                <i class="fas fa-user-plus"></i>
                <span>افزودن مشتری جدید</span>
            </a>
        </div>
    </div>

    <div class="fin-card">
        <div class="fin-card-header no-print">
            <h6 class="fin-card-title">
                <i class="fas fa-filter" style="color:#6366f1;"></i>
                <span>فیلتر و جستجوی هوشمند</span>
            </h6>
        </div>
        
        <div class="p-4 no-print border-bottom" style="background-color: #ffffff;">
            <form id="filter-form" action="<?php echo APP_URL; ?>/index.php" method="GET">
                <input type="hidden" name="page" value="admin">
                <input type="hidden" name="action" value="clients">
                
                <div class="filter-grid">
                    <div class="form-group-custom" style="grid-column: span 2;">
                        <label for="search">جستجو:</label>
                        <input type="text" class="form-control-custom" id="search" name="search" placeholder="نام، شرکت، ایمیل یا تلفن..." value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                    </div>

                    <div class="form-group-custom">
                        <label for="user_type">نوع شخص:</label>
                        <select class="form-select-custom" id="user_type" name="user_type">
                            <option value="">همه موارد</option>
                            <option value="real" <?php echo (isset($filters['user_type']) && $filters['user_type'] == 'real') ? 'selected' : ''; ?>>حقیقی</option>
                            <option value="legal" <?php echo (isset($filters['user_type']) && $filters['user_type'] == 'legal') ? 'selected' : ''; ?>>حقوقی</option>
                        </select>
                    </div>

                    <div class="form-group-custom">
                        <label for="start_date_jalali">تاریخ ثبت از:</label>
                        <input type="text" class="form-control-custom persian-datepicker" id="start_date_jalali" value="<?php echo htmlspecialchars($filters['start_date_jalali'] ?? ''); ?>" autocomplete="off" data-alt-field="#start_date">
                        <input type="hidden" id="start_date" name="start_date" value="<?php echo htmlspecialchars($filters['start_date'] ?? ''); ?>">
                    </div>

                    <div class="form-group-custom">
                        <label for="end_date_jalali">تا تاریخ:</label>
                        <input type="text" class="form-control-custom persian-datepicker" id="end_date_jalali" value="<?php echo htmlspecialchars($filters['end_date_jalali'] ?? ''); ?>" autocomplete="off" data-alt-field="#end_date">
                        <input type="hidden" id="end_date" name="end_date" value="<?php echo htmlspecialchars($filters['end_date'] ?? ''); ?>">
                    </div>

                    <div class="d-flex gap-2" style="align-self: end;">
                        <button type="button" id="apply-filter-btn" class="btn-fin-primary" style="padding: 0.6rem 1rem;" title="اعمال فیلتر"><i class="fas fa-search"></i></button>
                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn-fin-action" style="padding: 0.6rem 0.8rem;" title="بازنشانی"><i class="fas fa-undo"></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>نام مشتری</th>
                        <th>نام شرکت</th>
                        <th style="text-align: center;">نوع</th>
                        <th>ایمیل</th>
                        <th>تلفن</th>
                        <th>کاربر مرتبط</th>
                        <th style="text-align: center;">تاریخ ثبت</th>
                        <th style="text-align: center;" class="no-print">عملیات</th>
                    </tr>
                </thead>
                <tbody id="clients-table-body">
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td style="font-family: monospace; font-weight: bold; color: #64748b;"><?php echo sanitize($client->id); ?></td>
                                <td style="font-weight: 700; color: #0f172a;"><?php echo sanitize($client->name); ?></td>
                                <td><?php echo sanitize($client->company_name ?? '---'); ?></td>
                                <td style="text-align: center;">
                                    <span class="badge-custom <?php echo ($client->user_type == 'real') ? 'badge-real' : 'badge-legal'; ?>">
                                        <?php echo ($client->user_type == 'real') ? 'حقیقی' : 'حقوقی'; ?>
                                    </span>
                                </td>
                                <td style="font-family: monospace; font-size: 0.8rem;"><?php echo sanitize($client->email ?? '---'); ?></td>
                                <td style="font-family: monospace; font-size: 0.85rem;"><?php echo sanitize($client->phone ?? '---'); ?></td>
                                <td><span style="font-size: 0.8rem; color: #64748b; font-weight: 600;"><?php echo sanitize($userNames[$client->user_id] ?? 'نامشخص'); ?></span></td>
                                <td style="text-align: center; font-family: monospace; font-size: 0.85rem;"><?php echo sanitize(jdate('Y/m/d', strtotime($client->created_at))); ?></td>
                                <td style="text-align: center;" class="no-print">
                                    <div class="action-btn-group">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_edit&id=<?php echo $client->id; ?>" class="btn-icon edit" title="ویرایش پرونده"><i class="fas fa-edit"></i></a>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_logs&id=<?php echo $client->id; ?>" class="btn-icon logs" title="لاگ‌ها و پیگیری‌ها"><i class="fas fa-list-alt"></i></a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_delete&id=<?php echo $client->id; ?>" method="POST" style="display: inline;" onsubmit="return confirm('آیا از حذف این مشتری اطمینان دارید؟');">
                                            <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" style="text-align: center; color: #94a3b8; padding: 3rem;"><i class="fas fa-users-slash fs-2 mb-2 d-block opacity-50"></i>هیچ مشتری یافت نشد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function toJalali(date) {
        if (typeof window.persianDate !== 'undefined' && date) {
            const jalaliDate = new persianDate(new Date(date));
            return jalaliDate.format('YYYY/MM/DD');
        }
        return date || '---';
    }

    const userNames = <?php echo json_encode($userNames); ?>;
    const form = document.getElementById('filter-form');
    const searchInput = document.getElementById('search');
    const userTypeSelect = document.getElementById('user_type');
    const applyFilterBtn = document.getElementById('apply-filter-btn');
    const tableBody = document.getElementById('clients-table-body');
    const url = '<?php echo APP_URL; ?>/index.php';

    const applyFilters = () => {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        fetch(url + '?' + params.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("پاسخ دریافتی از سرور فرمت JSON ندارد.");
            }
            return response.json();
        })
        .then(data => {
            tableBody.innerHTML = '';
            if (data && data.length > 0) {
                data.forEach(client => {
                    const user_name = userNames[client.user_id] || 'نامشخص';
                    const badgeClass = client.user_type === 'real' ? 'badge-real' : 'badge-legal';
                    const userTypeText = client.user_type === 'real' ? 'حقیقی' : 'حقوقی';
                    
                    const row = `
                        <tr>
                            <td style="font-family: monospace; font-weight: bold; color: #64748b;">${client.id}</td>
                            <td style="font-weight: 700; color: #0f172a;">${client.name}</td>
                            <td>${client.company_name || '---'}</td>
                            <td style="text-align: center;">
                                <span class="badge-custom ${badgeClass}">${userTypeText}</span>
                            </td>
                            <td style="font-family: monospace; font-size: 0.8rem;">${client.email || '---'}</td>
                            <td style="font-family: monospace; font-size: 0.85rem;">${client.phone || '---'}</td>
                            <td><span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">${user_name}</span></td>
                            <td style="text-align: center; font-family: monospace; font-size: 0.85rem;">${toJalali(client.created_at)}</td>
                            <td style="text-align: center;" class="no-print">
                                <div class="action-btn-group">
                                    <a href="${url}?page=admin&action=clients_edit&id=${client.id}" class="btn-icon edit" title="ویرایش پرونده"><i class="fas fa-edit"></i></a>
                                    <a href="${url}?page=admin&action=clients_logs&id=${client.id}" class="btn-icon logs" title="لاگ‌ها و پیگیری‌ها"><i class="fas fa-list-alt"></i></a>
                                    <form action="${url}?page=admin&action=clients_delete&id=${client.id}" method="POST" style="display: inline;" onsubmit="return confirm('آیا از حذف این مشتری اطمینان دارید؟');">
                                        <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = `<tr><td colspan="9" style="text-align: center; color: #94a3b8; padding: 3rem;"><i class="fas fa-users-slash fs-2 mb-2 d-block opacity-50"></i>هیچ مشتری یافت نشد.</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            tableBody.innerHTML = `<tr><td colspan="9" style="text-align: center; color: #ef4444; padding: 2rem;"><i class="fas fa-exclamation-circle me-1"></i> خطا در دریافت اطلاعات.</td></tr>`;
        });
    };

    searchInput.addEventListener('input', () => {
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(applyFilters, 500);
    });
    userTypeSelect.addEventListener('change', applyFilters);
    applyFilterBtn.addEventListener('click', applyFilters);
});

(function loadPersianDatePicker() {
    function initPicker() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.pDatepicker) {
            $('.persian-datepicker').pDatepicker({
                format: 'YYYY/MM/DD',
                autoClose: true,
                initialValue: false,
                observer: true,
                initialValueType: 'persian',
                onSelect: function(date) {
                    const altFieldId = this.options.altField;
                    const altField = document.querySelector(altFieldId);
                    if (altField) { altField.value = date; }
                }
            });
        } else { setTimeout(initPicker, 100); }
    }
    if (!document.getElementById('p-datepicker-css')) {
        var css = document.createElement('link'); css.id = 'p-datepicker-css'; css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css';
        document.head.appendChild(css);
    }
    if (typeof $ === 'undefined') {
        var jq = document.createElement('script'); jq.src = 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'; jq.onload = loadPDate; document.head.appendChild(jq);
    } else { loadPDate(); }
    function loadPDate() {
        if (typeof window.persianDate === 'undefined') {
            var pd = document.createElement('script'); pd.src = 'https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js'; pd.onload = loadPDatePicker; document.head.appendChild(pd);
        } else { loadPDatePicker(); }
    }
    function loadPDatePicker() {
        if (typeof $.fn.pDatepicker === 'undefined') {
            var pdp = document.createElement('script'); pdp.src = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js'; pdp.onload = initPicker; document.head.appendChild(pdp);
        } else { initPicker(); }
    }
})();
</script>