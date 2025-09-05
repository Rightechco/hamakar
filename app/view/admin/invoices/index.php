<?php
// app/views/admin/invoices/index.php
global $auth;
$invoices = $invoices ?? [];
$clients = $clients ?? [];
$filters = $filters ?? [];
$invoiceTypes = $invoiceTypes ?? [];

function getStatusBadgeColor($status) {
    switch ($status) {
        case 'paid': return 'success';
        case 'pending': return 'warning';
        case 'overdue': return 'danger';
        case 'canceled': return 'secondary';
        default: return 'info';
    }
}
?>

<style>
    .card-modern {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .table-responsive-modern {
        border-radius: 10px;
        overflow: hidden;
    }
    .invoice-item-link {
        font-weight: 600;
        color: #343a40;
    }
</style>

<h1 class="h3 mb-4 text-gray-800">مدیریت فاکتورها</h1>

<?php FlashMessage::display(); ?>

<div class="card card-modern mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0 text-primary">لیست فاکتورها</h5>
    </div>
    <div class="card-body">
        <form id="filter-form" class="mb-4 p-3 border rounded-3 bg-light" action="<?php echo APP_URL; ?>/index.php" method="GET">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="action" value="invoices">
            <div class="d-flex flex-wrap align-items-end gap-3">
                <div class="flex-grow-1">
                    <label for="search" class="form-label text-muted small">جستجو:</label>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" placeholder="شماره فاکتور یا نام مشتری" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                </div>
                <div class="flex-grow-1">
                    <label for="client_id" class="form-label text-muted small">مشتری:</label>
                    <select class="form-select form-select-sm" id="client_id" name="client_id">
                        <option value="">همه مشتریان</option>
                        <?php foreach($clients as $client): ?>
                            <option value="<?php echo $client->id; ?>" <?php echo (isset($filters['client_id']) && $filters['client_id'] == $client->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($client->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label for="invoice_type" class="form-label text-muted small">نوع فاکتور:</label>
                    <select class="form-select form-select-sm" id="invoice_type" name="invoice_type">
                        <option value="">همه</option>
                        <?php foreach($invoiceTypes as $key => $label): ?>
                            <option value="<?php echo $key; ?>" <?php echo (isset($filters['invoice_type']) && $filters['invoice_type'] == $key) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm">اعمال فیلتر</button>
                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices" class="btn btn-outline-secondary btn-sm">پاک کردن فیلتر</a>
                </div>
            </div>
        </form>

        <div class="table-responsive table-responsive-modern">
            <table class="table table-hover table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>شماره فاکتور</th>
                        <th>مشتری</th>
                        <th>مربوط به</th>
                        <th>تاریخ صدور</th>
                        <th>مبلغ کل</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($invoices)): ?>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td><a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_invoice&id=<?php echo htmlspecialchars($invoice->id); ?>" class="invoice-item-link"><?php echo htmlspecialchars($invoice->invoice_number); ?></a></td>
                                <td><?php echo htmlspecialchars($invoice->client_name ?? 'نامشخص'); ?></td>
                                <td><?php echo htmlspecialchars($invoice->contract_title ?? '---'); ?></td>
                                <td><?php echo jdate('Y/m/d', strtotime($invoice->issue_date)); ?></td>
                                <td><?php echo number_format($invoice->total_amount); ?></td>
                                <td><span class="badge bg-<?php echo getStatusBadgeColor($invoice->status); ?>"><?php echo htmlspecialchars($invoice->status); ?></span></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_invoice&id=<?php echo htmlspecialchars($invoice->id); ?>" class="btn btn-sm btn-info" title="مشاهده">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=editInvoice&id=<?php echo htmlspecialchars($invoice->id); ?>" class="btn btn-sm btn-warning" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteInvoice&id=<?php echo htmlspecialchars($invoice->id); ?>" method="POST" class="d-inline-block">
                                            <button type="submit" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('آیا از حذف این فاکتور اطمینان دارید؟');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">هیچ فاکتوری یافت نشد.</td>
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
        const tableBody = document.getElementById('invoices-table-body');
        const url = '<?php echo APP_URL; ?>/index.php';

        const applyFilters = () => {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            // اضافه کردن هدر برای شناسایی درخواست AJAX در سمت سرور
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
                    data.forEach(invoice => {
                        const row = `
                            <tr>
                                <td>${invoice.invoice_number}</td>
                                <td>${invoice.client_name || 'نامشخص'}</td>
                                <td>${new Intl.NumberFormat().format(invoice.total_amount)} تومان</td>
                                <td>${toJalali(invoice.issue_date)}</td>
                                <td><span class="badge bg-warning">${invoice.status}</span></td>
                                <td>
                                    <a href="${url}?page=admin&action=view_invoice&id=${invoice.id}" target="_blank" class="btn btn-sm btn-secondary" title="مشاهده">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <?php if ($auth->hasRole(['admin', 'accountant'])): ?>
                                        <a href="${url}?page=admin&action=invoices_edit&id=${invoice.id}" class="btn btn-sm btn-info" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="${url}?page=admin&action=send_invoice_reminder&id=${invoice.id}" class="btn btn-sm btn-warning" title="ارسال یادآوری پرداخت" onclick="return confirm('آیا از ارسال یادآوری برای این فاکتور مطمئن هستید؟');">
                                            <i class="fas fa-bell"></i>
                                        </a>
                                        <form action="${url}?page=admin&action=invoices_delete&id=${invoice.id}" method="POST" class="d-inline-block">
                                            <button type="submit" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('آیا از حذف این فاکتور مطمئن هستید؟');">
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
                    tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-4">هیچ فاکتوری یافت نشد.</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">خطا در بارگذاری اطلاعات.</td></tr>`;
            });
        };

        // گوش دادن به تغییرات فیلدها و کلیک دکمه
        searchInput.addEventListener('input', () => {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(applyFilters, 500); // 500 میلی‌ثانیه تأخیر
        });
        document.querySelectorAll('#filter-form select').forEach(select => {
            select.addEventListener('change', applyFilters);
        });
        applyFilterBtn.addEventListener('click', applyFilters);
        
        // فعال‌سازی datepicker
        $('.persian-datepicker').persianDatepicker({
            format: 'YYYY-MM-DD',
            altField: '.alt-datepicker-gregorian',
            autoClose: true,
            onSelect: function(date) {
                applyFilters();
            }
        });
    });
</script>