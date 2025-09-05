<?php
// app/views/admin/users/index.php - نمای جدید با فیلتر، جستجو و AJAX
global $auth;
$users = $users ?? [];
$filters = $filters ?? [];
?>
<h1 class="mb-4">مدیریت کاربران</h1>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0">لیست کاربران</h5>
        <div class="d-flex align-items-center">
            <?php if ($auth->hasRole(['admin'])): ?>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=export_users_to_excel" class="btn btn-success btn-sm me-2">
                    <i class="fas fa-file-excel"></i> خروجی اکسل
                </a>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=show_import_users_form" class="btn btn-info btn-sm me-2">
                    <i class="fas fa-file-import"></i> ورود اکسل
                </a>
            <?php endif; ?>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=users_create" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> افزودن کاربر جدید
            </a>
        </div>
    </div>
    <div class="card-body">
        <form id="filter-form" class="mb-4 p-3 border rounded-3 bg-light" action="<?php echo APP_URL; ?>/index.php" method="GET">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="action" value="users">
            <div class="d-flex flex-wrap align-items-end gap-3">
                <div class="flex-grow-1">
                    <label for="search" class="form-label text-muted small">جستجو:</label>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" placeholder="نام یا ایمیل" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                </div>
                <div class="flex-grow-1">
                    <label for="role" class="form-label text-muted small">نقش:</label>
                    <select class="form-select form-select-sm" id="role" name="role">
                        <option value="">همه</option>
                        <option value="admin" <?php echo (isset($filters['role']) && $filters['role'] == 'admin') ? 'selected' : ''; ?>>مدیر</option>
                        <option value="accountant" <?php echo (isset($filters['role']) && $filters['role'] == 'accountant') ? 'selected' : ''; ?>>حسابدار</option>
                        <option value="accountant_viewer" <?php echo (isset($filters['role']) && $filters['role'] == 'accountant_viewer') ? 'selected' : ''; ?>>مشاهده‌گر حسابداری</option>
                        <option value="employee" <?php echo (isset($filters['role']) && $filters['role'] == 'employee') ? 'selected' : ''; ?>>کارمند</option>
                        <option value="client" <?php echo (isset($filters['role']) && $filters['role'] == 'client') ? 'selected' : ''; ?>>کارفرما</option>
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label for="status" class="form-label text-muted small">وضعیت:</label>
                    <select class="form-select form-select-sm" id="status" name="status">
                        <option value="">همه</option>
                        <option value="active" <?php echo (isset($filters['status']) && $filters['status'] == 'active') ? 'selected' : ''; ?>>فعال</option>
                        <option value="inactive" <?php echo (isset($filters['status']) && $filters['status'] == 'inactive') ? 'selected' : ''; ?>>غیرفعال</option>
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
                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=users" class="btn btn-outline-secondary btn-sm">پاک کردن فیلتر</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نام</th>
                        <th>ایمیل</th>
                        <th>نقش</th>
                        <th>وضعیت</th>
                        <th>تاریخ ثبت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody id="users-table-body">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo sanitize($user->id); ?></td>
                                <td><?php echo sanitize($user->name); ?></td>
                                <td><?php echo sanitize($user->email); ?></td>
                                <td><?php echo sanitize($user->role); ?></td>
                                <td><span class="badge bg-<?php echo ($user->status == 'active') ? 'success' : 'danger'; ?>"><?php echo sanitize($user->status); ?></span></td>
                                <td><?php echo sanitize(jdate('Y/m/d', strtotime($user->created_at))); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=users_edit&id=<?php echo $user->id; ?>" class="btn btn-sm btn-info" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=users_delete&id=<?php echo $user->id; ?>" method="POST" class="d-inline-block">
                                            <button type="submit" class="btn btn-sm btn-danger" data-confirm-delete title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">هیچ کاربری یافت نشد.</td>
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
        const applyFilterBtn = document.getElementById('apply-filter-btn');
        const tableBody = document.getElementById('users-table-body');
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
                    data.forEach(user => {
                        const statusBadge = user.status === 'active' ? '<span class="badge bg-success">active</span>' : '<span class="badge bg-danger">inactive</span>';
                        const row = `
                            <tr>
                                <td>${user.id}</td>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>${user.role}</td>
                                <td>${statusBadge}</td>
                                <td>${toJalali(user.created_at)}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="${url}?page=admin&action=users_edit&id=${user.id}" class="btn btn-sm btn-info" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="${url}?page=admin&action=users_delete&id=${user.id}" method="POST" class="d-inline-block">
                                            <button type="submit" class="btn btn-sm btn-danger" data-confirm-delete title="حذف">
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
                    tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">هیچ کاربری یافت نشد.</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger py-4">خطا در بارگذاری اطلاعات.</td></tr>`;
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