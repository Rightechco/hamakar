<?php
// app/views/admin/invoices/index.php
global $auth;
$invoices = $invoices ?? [];
$clients = $clients ?? [];
$filters = $filters ?? [];
$invoiceTypes = $invoiceTypes ?? [];

function getStatusBadgeColor($status) {
    switch ($status) {
        case 'paid':
        case 'completed':
            return 'success';
        case 'pending':
            return 'warning';
        case 'overdue':
            return 'danger';
        case 'canceled':
            return 'secondary';
        default:
            return 'info';
    }
}
?>

<style>
    .card-modern {
        border-radius: 18px;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
        border: 1px solid #e2e8f0;
    }
    .table-responsive-modern, .table-responsive {
        border-radius: 14px;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    .invoice-item-link {
        font-weight: 700;
        color: var(--primary, #4f46e5);
    }
    @media (max-width: 768px) {
        #filter-form .d-flex.flex-wrap {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
        }
        #filter-form .flex-grow-1 {
            width: 100% !important;
            min-width: 0 !important;
        }
        #filter-form .d-flex.gap-2 {
            display: flex !important;
            flex-direction: row !important;
            width: 100% !important;
        }
        #filter-form .d-flex.gap-2 .btn {
            flex: 1 !important;
            white-space: nowrap !important;
        }
        .card-header {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 0.75rem !important;
        }
        .card-header .btn {
            width: 100% !important;
            text-align: center !important;
        }
    }
</style>

<h1 class="h3 mb-4 text-gray-800">مدیریت فاکتورها</h1>

<?php FlashMessage::display(); ?>

<div class="card card-modern mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0 text-primary">لیست فاکتورها</h5>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices_create" class="btn btn-success btn-sm">

            <i class="fas fa-plus"></i> افزودن فاکتور جدید
        </a>
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
                    <label for="client_id" class="form-label text-muted small">مشتری/فروشنده:</label>
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
                <div class="flex-grow-1">
                    <label for="official_type" class="form-label text-muted small">رسمیت / مودیان:</label>
                    <select class="form-select form-select-sm" id="official_type" name="official_type">
                        <option value="">همه (رسمی و غیررسمی)</option>
                        <option value="1" <?php echo (isset($_GET['official_type']) && $_GET['official_type'] === '1') ? 'selected' : ''; ?>>فقط رسمی (سامانه مودیان)</option>
                        <option value="0" <?php echo (isset($_GET['official_type']) && $_GET['official_type'] === '0') ? 'selected' : ''; ?>>فقط غیر رسمی (داخلی)</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm">اعمال فیلتر</button>
                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices" class="btn btn-outline-secondary btn-sm">پاک کردن فیلتر</a>
                </div>
            </div>
        </form>

        
        <!-- پنل خروجی رسمی اکسل و PDF فاکتورها -->
        <div class="d-flex justify-content-between align-items-center p-3 mb-3 bg-light rounded-3 border flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-pill fw-bold">
                    <i class="fas fa-file-invoice me-1"></i>فهرست فاکتورها
                </span>
                <span class="text-muted small">خروجی رسمی با سربرگ شرکتی و فونت فارسی وزیرمتن</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="<?php echo APP_URL; ?>/index.php?page=export&action=excel&type=invoices" class="btn btn-sm btn-outline-success rounded-3 fw-bold shadow-xs">
                    <i class="fas fa-file-excel text-success me-1"></i> خروجی کامل اکسل (Excel)
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-3 fw-bold shadow-xs" onclick="HamakarExport.toPdf('.table-responsive', 'گزارش_فاکتورها', 'گزارش فاکتورهای رسمی و غیررسمی هماکار')">
                    <i class="fas fa-file-pdf text-danger me-1"></i> دانلود PDF
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-3 fw-bold shadow-xs" onclick="HamakarExport.printOfficial('.table-responsive', 'گزارش فاکتورهای رسمی و غیررسمی هماکار')">
                    <i class="fas fa-print text-primary me-1"></i> چاپ رسمی A4
                </button>
            </div>
        </div>

        <div class="table-responsive table-responsive-modern">
            <table class="table table-hover table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>شماره فاکتور</th>
                        <th>مشتری/فروشنده</th>
                        <th>نوع</th>
                        <th>رسمیت / مودیان</th>
                        <th>مربوط به</th>
                        <th>تاریخ</th>
                        <th>مبلغ کل</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($invoices)): ?>
                        <?php foreach ($invoices as $invoice): ?>
                            <tr>
                                <td>
                                    <?php
                                    $link = '';
                                    $number = '';
                                    if ($invoice->type === 'contract') {
                                        $link = APP_URL . '/index.php?page=admin&action=view_invoice&id=' . htmlspecialchars($invoice->id);
                                        $number = htmlspecialchars($invoice->invoice_number);
                                    } elseif ($invoice->type === 'sale') {
                                        $link = APP_URL . '/index.php?page=sales&action=view&id=' . htmlspecialchars($invoice->id);
                                        $number = 'فروش #' . htmlspecialchars($invoice->id);
                                    } elseif ($invoice->type === 'purchase') {
                                        $link = APP_URL . '/index.php?page=purchases&action=view&id=' . htmlspecialchars($invoice->id);
                                        $number = 'خرید #' . htmlspecialchars($invoice->id);
                                    }
                                    ?>
                                    <a href="<?php echo $link; ?>" class="invoice-item-link"><?php echo $number; ?></a>
                                </td>
                                <td><?php echo htmlspecialchars($invoice->client_name ?? 'نامشخص'); ?></td>
                                <td>
                                    <?php 
                                        if ($invoice->type === 'contract') echo 'قرارداد';
                                        elseif ($invoice->type === 'sale') echo 'فروش';
                                        elseif ($invoice->type === 'purchase') echo 'خرید';
                                        else echo 'نامشخص';
                                    ?>
                                </td>
                                <td>
                                    <?php $isOfficial = (isset($invoice->is_official) && $invoice->is_official == 0) ? false : true; ?>
                                    <?php if ($isOfficial): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill" title="فاکتور رسمی مشمول سامانه مودیان">
                                            <i class="fas fa-stamp me-1"></i> رسمی (مودیان)
                                        </span>
                                        <?php if (!empty($invoice->moadian_taxid)): ?>
                                            <div class="small font-monospace text-primary mt-1" style="font-size:0.7rem;" title="TaxID">
                                                <?php echo substr($invoice->moadian_taxid, 0, 10) . '...'; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-2 py-1 rounded-pill" title="فاکتور غیر رسمی و مدیریت داخلی">
                                            <i class="fas fa-file-alt me-1"></i> غیر رسمی (داخلی)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        // ✅ منطق جدید برای ستون "مربوط به"
                                        if ($invoice->type === 'sale') {
                                            echo 'فروش کالا به ' . htmlspecialchars($invoice->client_name ?? '---');
                                        } elseif ($invoice->type === 'purchase') {
                                            echo 'خرید کالا از ' . htmlspecialchars($invoice->client_name ?? '---');
                                        } else {
                                            echo htmlspecialchars($invoice->contract_title ?? '---');
                                        }
                                    ?>
                                </td>
                                <td><?php echo jdate('Y/m/d', strtotime($invoice->date)); ?></td>
                                <td><?php echo number_format($invoice->total_amount); ?></td>
                                <td><span class="badge bg-<?php echo getStatusBadgeColor($invoice->status); ?>"><?php echo htmlspecialchars($invoice->status); ?></span></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <?php 
                                        $viewAction = '';
                                        $deleteAction = '';
                                        $page = 'admin'; // پیش‌فرض

                                        if ($invoice->type === 'contract') {
                                            $viewAction = 'view_invoice';
                                            $deleteAction = 'invoices_delete';
                                        } elseif ($invoice->type === 'sale') {
                                            $page = 'sales';
                                            $viewAction = 'view';
                                            $deleteAction = 'delete';
                                        } elseif ($invoice->type === 'purchase') {
                                            $page = 'purchases';
                                            $viewAction = 'view';
                                            $deleteAction = 'delete';
                                        }
                                        ?>
                                        <?php if (!empty($viewAction)): ?>
                                            <a href="<?php echo APP_URL; ?>/index.php?page=<?php echo $page; ?>&action=<?php echo $viewAction; ?>&id=<?php echo htmlspecialchars($invoice->id); ?>" class="btn btn-sm btn-info" title="مشاهده">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($isOfficial && empty($invoice->moadian_taxid) && $invoice->type === 'contract'): ?>
                                            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=send_invoice_to_moadian&id=<?php echo htmlspecialchars($invoice->id); ?>" class="btn btn-sm btn-outline-primary" title="ارسال آنلاین به سامانه مودیان" onclick="return confirm('آیا می‌خواهید این فاکتور رسمی مستقیماً به سامانه مودیان ارسال و تایید شود؟');">
                                                <i class="fas fa-paper-plane"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($deleteAction) && $auth->hasRole(['admin', 'accountant'])): ?>
                                            <form action="<?php echo APP_URL; ?>/index.php?page=<?php echo $page; ?>&action=<?php echo $deleteAction; ?>&id=<?php echo htmlspecialchars($invoice->id); ?>" method="POST" class="d-inline-block">
                                                <button type="submit" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('آیا از حذف این فاکتور اطمینان دارید؟');">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">هیچ فاکتوری یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    };
    
    function toJalali(date) {
        if (!date) return '---';
        const jalaliDate = new persianDate(new Date(date));
        return jalaliDate.format('YYYY/MM/DD');
    }

    const form = document.getElementById('filter-form');
    const searchInput = document.getElementById('search');
    const clientSelect = document.getElementById('client_id');
    const invoiceTypeSelect = document.getElementById('invoice_type');
    const tableBody = document.querySelector('.table tbody');
    const url = '<?php echo APP_URL; ?>/index.php';

    const applyFilters = () => {
        const params = new URLSearchParams({
            page: 'admin',
            action: 'invoices_ajax',
            search: searchInput.value,
            client_id: clientSelect.value,
            invoice_type: invoiceTypeSelect.value
        });

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
                    const statusBadgeColor = getStatusBadgeColor(invoice.status);
                    
                    let viewLink = '';
                    let deleteLink = '';
                    let invoiceNumber = '';
                    let relatedTo = '';

                    // ✅ منطق PHP برای تعیین اکشن‌ها و شماره فاکتور
                    let page = 'admin';
                    if (invoice.type === 'contract') {
                        viewLink = `${url}?page=admin&action=view_invoice&id=${invoice.id}`;
                        deleteLink = `${url}?page=admin&action=invoices_delete&id=${invoice.id}`;
                        invoiceNumber = invoice.invoice_number;
                        relatedTo = invoice.contract_title || '---';

                    } else if (invoice.type === 'sale') {
                        page = 'sales';
                        viewLink = `${url}?page=sales&action=view&id=${invoice.id}`;
                        deleteLink = `${url}?page=sales&action=delete&id=${invoice.id}`;
                        invoiceNumber = `فروش #${invoice.id}`;
                        relatedTo = `فروش کالا به ${invoice.client_name || '---'}`;
                    } else if (invoice.type === 'purchase') {
                        page = 'purchases';
                        viewLink = `${url}?page=purchases&action=view&id=${invoice.id}`;
                        deleteLink = `${url}?page=purchases&action=delete&id=${invoice.id}`;
                        invoiceNumber = `خرید #${invoice.id}`;
                        relatedTo = `خرید کالا از ${invoice.client_name || '---'}`;
                    }

                    const row = `
                        <tr>
                            <td><a href="${viewLink}" class="invoice-item-link">${invoiceNumber}</a></td>
                            <td>${invoice.client_name || 'نامشخص'}</td>
                            <td>${invoice.type === 'contract' ? 'قرارداد' : (invoice.type === 'sale' ? 'فروش' : 'خرید')}</td>
                            <td>${relatedTo}</td>
                            <td>${toJalali(invoice.date)}</td>
                            <td>${new Intl.NumberFormat().format(invoice.total_amount)}</td>
                            <td><span class="badge bg-${statusBadgeColor}">${invoice.status}</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="${viewLink}" class="btn btn-sm btn-info" title="مشاهده">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="${deleteLink}" method="POST" class="d-inline-block">
                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف" onclick="return confirm('آیا از حذف این فاکتور اطمینان دارید؟');">
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
                tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">هیچ فاکتوری یافت نشد.</td></tr>`;
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
        window.searchTimeout = setTimeout(applyFilters, 500); // 500 میلی‌ثانیه تأخیر
    });
    document.querySelectorAll('#filter-form select').forEach(select => {
        select.addEventListener('change', applyFilters);
    });
    
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        history.pushState(null, '', '?' + params.toString());
        applyFilters();
    });
    
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