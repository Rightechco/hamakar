<?php
// app/views/admin/contracts/index.php - با سطوح دسترسی
global $auth;
$contracts = $contracts ?? [];
$clients = $clients ?? [];
$clientNames = $clientNames ?? [];
$categories = $categories ?? []; // ✅ اضافه شدن
$filters = $filters ?? [];
$serviceTypes = $serviceTypes ?? [];
?>
<h1 class="mb-4">مدیریت قراردادها</h1>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0">لیست قراردادها</h5>
        <div class="d-flex align-items-center">
            <?php if ($auth->hasRole(['admin', 'accountant'])): ?>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=export_contracts_to_excel" class="btn btn-success btn-sm me-2">
                    <i class="fas fa-file-excel"></i> خروجی اکسل
                </a>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=contracts_create" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> افزودن قرارداد جدید
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <form id="filter-form" class="mb-4 p-3 border rounded-3 bg-light" action="<?php echo APP_URL; ?>/index.php" method="GET">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="action" value="contracts">
            <div class="d-flex flex-wrap align-items-end gap-3">
                <div class="flex-grow-1">
                    <label for="search" class="form-label text-muted small">جستجو:</label>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" placeholder="عنوان یا نام مشتری" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                </div>
                <div class="flex-grow-1">
                    <label for="client_id" class="form-label text-muted small">کارفرما:</label>
                    <select class="form-select form-select-sm" id="client_id" name="client_id">
                        <option value="">همه</option>
                        <?php foreach($clients as $client): ?>
                            <option value="<?php echo $client->id; ?>" <?php echo (isset($filters['client_id']) && $filters['client_id'] == $client->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($client->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label for="status" class="form-label text-muted small">وضعیت:</label>
                    <select class="form-select form-select-sm" id="status" name="status">
                        <option value="">همه</option>
                        <option value="active" <?php echo (isset($filters['status']) && $filters['status'] == 'active') ? 'selected' : ''; ?>>فعال</option>
                        <option value="pending" <?php echo (isset($filters['status']) && $filters['status'] == 'pending') ? 'selected' : ''; ?>>در انتظار</option>
                        <option value="expired" <?php echo (isset($filters['status']) && $filters['status'] == 'expired') ? 'selected' : ''; ?>>منقضی شده</option>
                        <option value="canceled" <?php echo (isset($filters['status']) && $filters['status'] == 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label for="category_id" class="form-label text-muted small">دسته‌بندی:</label>
                    <select class="form-select form-select-sm" id="category_id" name="category_id">
                        <option value="">همه دسته‌بندی‌ها</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category->id; ?>" <?php echo (isset($filters['category_id']) && $filters['category_id'] == $category->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label for="start_date_jalali" class="form-label text-muted small">از تاریخ:</label>
                    <input type="text" class="form-control form-control-sm persian-datepicker" id="start_date_jalali"
                           value="<?php echo htmlspecialchars($filters['start_date_jalali'] ?? ''); ?>"
                           data-alt-field="#start_date">
                    <input type="hidden" id="start_date" name="start_date" value="<?php echo htmlspecialchars($filters['start_date'] ?? ''); ?>">
                </div>
                <div class="flex-grow-1">
                    <label for="end_date_jalali" class="form-label text-muted small">تا تاریخ:</label>
                    <input type="text" class="form-control form-control-sm persian-datepicker" id="end_date_jalali"
                           value="<?php echo htmlspecialchars($filters['end_date_jalali'] ?? ''); ?>"
                           data-alt-field="#end_date">
                    <input type="hidden" id="end_date" name="end_date" value="<?php echo htmlspecialchars($filters['end_date'] ?? ''); ?>">
                </div>
                <div class="d-flex gap-2">
                    <button type="button" id="apply-filter-btn" class="btn btn-secondary btn-sm">اعمال فیلتر</button>
                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=contracts" class="btn btn-outline-secondary btn-sm">پاک کردن فیلتر</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>عنوان</th>
                        <th>مشتری</th>
                        <th>دسته‌بندی</th> <th>مبلغ کل</th>
                        <th>تاریخ شروع</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody id="contracts-table-body">
                    <?php if (!empty($contracts)): ?>
                        <?php foreach ($contracts as $contract): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contract->id); ?></td>
                                <td><?php echo htmlspecialchars($contract->title); ?></td>
                                <td><?php echo htmlspecialchars($clientNames[$contract->client_id] ?? 'نامشخص'); ?></td>
                                <td><?php echo htmlspecialchars($contract->category_name ?? 'نامشخص'); ?></td> <td><?php echo number_format($contract->total_amount); ?> تومان</td>
                                <td><?php echo htmlspecialchars(jdate('Y/m/d', strtotime($contract->start_date))); ?></td>
                                <td><span class="badge bg-success"><?php echo htmlspecialchars($contract->status); ?></span></td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_contract&id=<?php echo $contract->id; ?>" target="_blank" class="btn btn-sm btn-secondary" title="مشاهده">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    
                                    <?php if ($auth->hasRole(['admin', 'accountant'])): ?>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=contracts_edit&id=<?php echo $contract->id; ?>" class="btn btn-sm btn-info" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=send_contract_reminder&id=<?php echo $contract->id; ?>" class="btn btn-sm btn-success" title="ارسال یادآوری تمدید" onclick="return confirm('آیا از ارسال یادآوری تمدید برای این قرارداد مطمئن هستید؟');">
                                            <i class="fas fa-history"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($auth->hasRole(['admin'])): ?>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=contracts_delete&id=<?php echo $contract->id; ?>" method="POST" class="d-inline-block">
                                            <button type="submit" class="btn btn-sm btn-danger" data-confirm-delete title="حذف" onclick="return confirm('آیا از حذف این قرارداد مطمئن هستید؟');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">هیچ قراردادی یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تابع تبدیل تاریخ میلادی به شمسی
        function toJalali(date) {
            const jalaliDate = new persianDate(new Date(date));
            return jalaliDate.format('YYYY/MM/DD');
        }

        const form = document.getElementById('filter-form');
        const searchInput = document.getElementById('search');
        const clientSelect = document.getElementById('client_id');
        const statusSelect = document.getElementById('status');
        const categorySelect = document.getElementById('category_id'); // ✅ اضافه شدن
        const applyFilterBtn = document.getElementById('apply-filter-btn');
        const tableBody = document.getElementById('contracts-table-body');
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
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(contract => {
                        const row = `
                            <tr>
                                <td>${contract.id}</td>
                                <td>${contract.title}</td>
                                <td>${contract.client_name || 'نامشخص'}</td>
                                <td>${contract.category_name || 'نامشخص'}</td> <td>${new Intl.NumberFormat().format(contract.total_amount)} تومان</td>
                                <td>${toJalali(contract.start_date)}</td>
                                <td><span class="badge bg-success">${contract.status}</span></td>
                                <td>
                                    <a href="${url}?page=admin&action=view_contract&id=${contract.id}" target="_blank" class="btn btn-sm btn-secondary" title="مشاهده">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <?php if ($auth->hasRole(['admin', 'accountant'])): ?>
                                        <a href="${url}?page=admin&action=contracts_edit&id=${contract.id}" class="btn btn-sm btn-info" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="${url}?page=admin&action=send_contract_reminder&id=${contract.id}" class="btn btn-sm btn-success" title="ارسال یادآوری تمدید" onclick="return confirm('آیا از ارسال یادآوری تمدید برای این قرارداد مطمئن هستید؟');">
                                            <i class="fas fa-history"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($auth->hasRole(['admin'])): ?>
                                        <form action="${url}?page=admin&action=contracts_delete&id=${contract.id}" method="POST" class="d-inline-block">
                                            <button type="submit" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('آیا از حذف این قرارداد مطمئن هستید؟');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                } else {
                    tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4">هیچ قراردادی یافت نشد.</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">خطا در بارگذاری اطلاعات.</td></tr>`;
            });
        };

        // گوش دادن به تغییرات فیلدها و کلیک دکمه
        searchInput.addEventListener('input', () => {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(applyFilters, 500);
        });
        clientSelect.addEventListener('change', applyFilters);
        statusSelect.addEventListener('change', applyFilters);
        categorySelect.addEventListener('change', applyFilters); // ✅ اضافه شدن
        applyFilterBtn.addEventListener('click', applyFilters);
        
        // فعال‌سازی datepicker
        $('.persian-datepicker').persianDatepicker({
            format: 'YYYY/MM/DD',
            altField: '.alt-datepicker-gregorian',
            autoClose: true,
            onSelect: function(date) {
                const altFieldId = this.options.altField;
                const altField = document.querySelector(altFieldId);
                if (altField) {
                    altField.value = date;
                }
                applyFilters();
            }
        });
    });
</script>