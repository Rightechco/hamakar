<?php
// app/views/admin/clients/index.php
global $auth;
$clients = $clients ?? [];
$userNames = $userNames ?? [];
$filters = $filters ?? [];
?>
<h1 class="mb-4">مدیریت مشتریان</h1>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0">لیست مشتریان</h5>
        <div class="d-flex align-items-center">
            <?php if ($auth->hasRole(['admin'])): ?>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=export_clients_to_excel" class="btn btn-success btn-sm me-2">
                    <i class="fas fa-file-excel"></i> خروجی اکسل
                </a>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=show_import_clients_form" class="btn btn-info btn-sm me-2">
                    <i class="fas fa-file-import"></i> ورود اکسل
                </a>
            <?php endif; ?>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> افزودن مشتری جدید
            </a>
        </div>
    </div>
    <div class="card-body">
        <form id="filter-form" class="mb-4 p-3 border rounded-3 bg-light" action="<?php echo APP_URL; ?>/index.php" method="GET">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="action" value="clients">
            <div class="d-flex flex-wrap align-items-end gap-3">
                <div class="flex-grow-1">
                    <label for="search" class="form-label text-muted small">جستجو:</label>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" placeholder="نام، شرکت یا ایمیل" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                </div>
                <div class="flex-grow-1">
                    <label for="user_type" class="form-label text-muted small">نوع مشتری:</label>
                    <select class="form-select form-select-sm" id="user_type" name="user_type">
                        <option value="">همه</option>
                        <option value="real" <?php echo (isset($filters['user_type']) && $filters['user_type'] == 'real') ? 'selected' : ''; ?>>حقیقی</option>
                        <option value="legal" <?php echo (isset($filters['user_type']) && $filters['user_type'] == 'legal') ? 'selected' : ''; ?>>حقوقی</option>
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label for="start_date_jalali" class="form-label text-muted small">تاریخ ثبت از:</label>
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
                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn btn-outline-secondary btn-sm">پاک کردن فیلتر</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نام مشتری</th>
                        <th>نام شرکت</th>
                        <th>نوع</th>
                        <th>ایمیل</th>
                        <th>تلفن</th>
                        <th>کاربر مرتبط</th>
                        <th>تاریخ ثبت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody id="clients-table-body">
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?php echo sanitize($client->id); ?></td>
                                <td><?php echo sanitize($client->name); ?></td>
                                <td><?php echo sanitize($client->company_name ?? '---'); ?></td>
                                <td><span class="badge bg-<?php echo ($client->user_type == 'real') ? 'info' : 'primary'; ?>"><?php echo sanitize($client->user_type); ?></span></td>
                                <td><?php echo sanitize($client->email); ?></td>
                                <td><?php echo sanitize($client->phone); ?></td>
                                <td><?php echo sanitize($userNames[$client->user_id] ?? 'نامشخص'); ?></td>
                                <td><?php echo sanitize(jdate('Y/m/d', strtotime($client->created_at))); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_edit&id=<?php echo $client->id; ?>" class="btn btn-sm btn-info" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_logs&id=<?php echo $client->id; ?>" class="btn btn-sm btn-warning" title="مشاهده لاگ ها">
                                            <i class="fas fa-list-alt"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_delete&id=<?php echo $client->id; ?>" method="POST" class="d-inline-block">
                                            <button type="button" class="btn btn-sm btn-danger" data-confirm-delete title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">هیچ مشتری یافت نشد.</td>
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

        const userNames = <?php echo json_encode($userNames); ?>;

        const form = document.getElementById('filter-form');
        const searchInput = document.getElementById('search');
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
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(client => {
                        const user_name = userNames[client.user_id] || 'نامشخص';
                        const userTypeBadge = client.user_type === 'real' ? 'bg-info' : 'bg-primary';
                        const row = `
                            <tr>
                                <td>${client.id}</td>
                                <td>${client.name}</td>
                                <td>${client.company_name || '---'}</td>
                                <td><span class="badge ${userTypeBadge}">${client.user_type}</span></td>
                                <td>${client.email}</td>
                                <td>${client.phone}</td>
                                <td>${user_name}</td>
                                <td>${toJalali(client.created_at)}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="${url}?page=admin&action=clients_edit&id=${client.id}" class="btn btn-sm btn-info" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="${url}?page=admin&action=clients_logs&id=${client.id}" class="btn btn-sm btn-warning" title="مشاهده لاگ ها">
                                            <i class="fas fa-list-alt"></i>
                                        </a>
                                        <form action="${url}?page=admin&action=clients_delete&id=${client.id}" method="POST" class="d-inline-block">
                                            <button type="button" class="btn btn-sm btn-danger" data-confirm-delete title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                } else {
                    tableBody.innerHTML = `<tr><td colspan="9" class="text-center py-4">هیچ مشتری یافت نشد.</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                tableBody.innerHTML = `<tr><td colspan="9" class="text-center text-danger py-4">خطا در بارگذاری اطلاعات.</td></tr>`;
            });
        };

        // گوش دادن به تغییرات فیلدها و کلیک دکمه
        searchInput.addEventListener('input', () => {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(applyFilters, 500);
        });
        document.querySelectorAll('#filter-form select').forEach(select => {
            select.addEventListener('change', applyFilters);
        });
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