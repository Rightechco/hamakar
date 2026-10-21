<?php
// app/view/admin/invoices/create_edit.php
$invoice = $invoice ?? null;
$clients = $clients ?? [];
$contracts = $contracts ?? [];
$products = $products ?? [];
$services = $services ?? [];
$invoiceItems = $invoiceItems ?? [];
$newInvoiceNumber = $newInvoiceNumber ?? '';
$issue_date_jalali = $issue_date_jalali ?? '';
$due_date_jalali = $due_date_jalali ?? '';

$sellerInfo = [
    'name' => 'شرکت رایان تکرو',
    'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
    'address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه ۳',
    'economic_code' => '۱۴۰۰۹۱۵۶۳۲۱',
    'registration_number' => '۵۵۲۰۰',
];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 18px;
        --radius-md: 10px;
        --shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    body, .invoice-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    .premium-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
    }

    .premium-card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 1.25rem 1.5rem;
    }

    .form-control, .form-select {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
        padding: 0.65rem 0.95rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .input-group-text.pdp-trigger {
        border-radius: 0 var(--radius-md) var(--radius-md) 0;
        border: 1px solid var(--border-color);
        background-color: #f8fafc;
        cursor: pointer;
    }

    .btn-gradient-primary {
        background: var(--primary-gradient);
        color: #fff;
        border: none;
        border-radius: var(--radius-md);
        padding: 0.65rem 1.6rem;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        transition: all 0.2s ease;
    }

    .btn-gradient-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
        color: #fff;
    }

    .item-row {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: var(--radius-md);
        padding: 12px;
        margin-bottom: 12px;
    }

    /* Sticky Live Preview Panel */
    .invoice-preview-sticky {
        position: sticky;
        top: 20px;
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        padding: 24px;
    }

    .preview-table th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 0.85rem;
    }
</style>

<div class="invoice-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><?php echo $invoice ? 'ویرایش فاکتور' : 'صدور فاکتور جدید'; ?></h3>
            <p class="text-muted small mb-0">تنظیم مشخصات فاکتور فروش، قرارداد و خدمات سیستم</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices" class="btn btn-outline-secondary btn-sm px-4 rounded-pill fw-medium">
            <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست
        </a>
    </div>

    <?php FlashMessage::display(); ?>

    <div class="row g-4">
        <!-- Form Area -->
        <div class="col-lg-7">
            <div class="premium-card">
                <div class="premium-card-header d-flex align-items-center">
                    <i class="fas fa-file-invoice text-primary fs-5 me-2"></i>
                    <h5 class="mb-0 fw-bold fs-6 text-dark">اطلاعات فاکتور</h5>
                </div>
                <div class="card-body p-4">
                    <form id="invoice-form" action="<?php echo APP_URL; ?>/index.php?page=admin&action=<?php echo $invoice ? 'invoices_update&id=' . $invoice->id : 'invoices_store'; ?>" method="POST">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="invoice_number" class="form-label fw-medium small">شماره فاکتور <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo htmlspecialchars($invoice->invoice_number ?? $newInvoiceNumber); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="client_id" class="form-label fw-medium small">مشتری / خریدار <span class="text-danger">*</span></label>
                                <select class="form-select" id="client_id" name="client_id" required>
                                    <option value="">انتخاب خریدار...</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?php echo $client->id; ?>" <?php echo ($invoice && $invoice->client_id == $client->id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($client->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="issue_date" class="form-label fw-medium small">تاریخ صدور <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text pdp-trigger"><i class="far fa-calendar-alt text-muted"></i></span>
                                    <input type="text" class="form-control pdp-input border-start-0" id="issue_date" name="issue_date" value="<?php echo htmlspecialchars($issue_date_jalali); ?>" placeholder="YYYY/MM/DD" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="due_date" class="form-label fw-medium small">تاریخ سررسید</label>
                                <div class="input-group">
                                    <span class="input-group-text pdp-trigger"><i class="far fa-calendar-alt text-muted"></i></span>
                                    <input type="text" class="form-control pdp-input border-start-0" id="due_date" name="due_date" value="<?php echo htmlspecialchars($due_date_jalali); ?>" placeholder="YYYY/MM/DD" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="status" class="form-label fw-medium small">وضعیت فاکتور <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" <?php echo ($invoice && $invoice->status == 'pending') ? 'selected' : ''; ?>>در انتظار پرداخت</option>
                                    <option value="paid" <?php echo ($invoice && $invoice->status == 'paid') ? 'selected' : ''; ?>>پرداخت شده</option>
                                    <option value="overdue" <?php echo ($invoice && $invoice->status == 'overdue') ? 'selected' : ''; ?>>سررسید گذشته</option>
                                    <option value="canceled" <?php echo ($invoice && $invoice->status == 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="invoice_type" class="form-label fw-medium small">نوع فاکتور <span class="text-danger">*</span></label>
                            <select class="form-select" id="invoice_type" name="invoice_type" required>
                                <option value="contract" <?php echo ($invoice && $invoice->invoice_type == 'contract') ? 'selected' : ''; ?>>قرارداد</option>
                                <option value="service" <?php echo ($invoice && $invoice->invoice_type == 'service') ? 'selected' : ''; ?>>خدمات</option>
                                <option value="product" <?php echo ($invoice && $invoice->invoice_type == 'product') ? 'selected' : ''; ?>>محصول</option>
                                <option value="mixed" <?php echo ($invoice && $invoice->invoice_type == 'mixed') ? 'selected' : ''; ?>>ترکیبی (خدمات و محصول)</option>
                            </select>
                        </div>
                        
                        <div id="item-fields-container" class="border-top pt-3 mb-3">
                            <div id="contract-items" style="display: none;">
                                <h6 class="fw-bold fs-6 mb-3 text-dark">جزئیات قرارداد</h6>
                                <div class="mb-3">
                                    <label for="contract_id" class="form-label small text-muted">قرارداد مرتبط</label>
                                    <select class="form-select" id="contract_id" name="contract_id">
                                        <option value="">انتخاب کنید...</option>
                                        <?php foreach ($contracts as $contract): ?>
                                            <option value="<?php echo $contract->id; ?>" data-amount="<?php echo htmlspecialchars($contract->total_amount); ?>" <?php echo ($invoice && $invoice->invoice_type === 'contract' && $invoice->contract_id == $contract->id) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($contract->title); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="contract_subtotal" class="form-label small text-muted">مبلغ کل قرارداد</label>
                                    <input type="text" class="form-control" id="contract_subtotal" name="contract_subtotal" value="<?php echo htmlspecialchars($invoice->subtotal ?? 0); ?>" readonly>
                                </div>
                            </div>

                            <div id="dynamic-items-container" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold fs-6 mb-0 text-dark">جزئیات آیتم‌های فاکتور</h6>
                                    <div class="gap-2 d-flex">
                                        <button type="button" class="btn btn-sm btn-outline-success rounded-2" id="add-service-btn" style="display: none;"><i class="fas fa-plus me-1"></i> افزودن خدمت</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-2" id="add-product-btn" style="display: none;"><i class="fas fa-plus me-1"></i> افزودن محصول</button>
                                    </div>
                                </div>
                                <div id="item-list"></div>
                            </div>
                        </div>

                        <div class="mb-3 border-top pt-3">
                            <label for="description" class="form-label fw-medium small">شرح / توضیحات فاکتور</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="توضیحات تکمیلی فاکتور..."><?php echo htmlspecialchars($invoice->description ?? ''); ?></textarea>
                        </div>
                        
                        <div class="row g-3 border-top pt-3">
                            <div class="col-md-6">
                                <label for="vat_rate" class="form-label fw-medium small">نرخ مالیات بر ارزش افزوده (%)</label>
                                <input type="number" class="form-control" id="vat_rate" name="vat_rate" value="<?php echo htmlspecialchars($invoice->vat_rate ?? 9); ?>" min="0" max="100">
                            </div>
                            <div class="col-md-6">
                                <label for="subtotal-input" class="form-label fw-medium small">جمع کل بدون مالیات (تومان)</label>
                                <input type="text" class="form-control fw-bold" id="subtotal-input" value="<?php echo htmlspecialchars($invoice->subtotal ?? 0); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices" class="btn btn-light px-4 rounded-2">انصراف</a>
                            <button type="submit" class="btn btn-gradient-primary px-4"><?php echo $invoice ? 'به‌روزرسانی فاکتور' : 'صدور نهایی فاکتور'; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Live Preview Sidebar -->
        <div class="col-lg-5">
            <div class="invoice-preview-sticky">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <div>
                        <span class="badge bg-soft-primary text-primary px-3 py-1.5 rounded-pill small mb-2" style="background:#e0e7ff; color:#4338ca;">پیش‌نمایش زنده</span>
                        <h5 class="fw-bold mb-0">پیش‌نمایش فاکتور</h5>
                    </div>
                    <div class="text-end small text-muted">
                        <div>شماره: <strong id="preview-invoice-number" class="text-dark">---</strong></div>
                        <div>تاریخ: <span id="preview-issue-date" class="text-dark">---</span></div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3 small">
                            <span class="text-muted d-block mb-1">خریدار:</span>
                            <strong id="preview-client-name" class="text-dark d-block">---</strong>
                            <span id="preview-client-address" class="text-muted text-truncate d-block">---</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3 small">
                            <span class="text-muted d-block mb-1">فروشنده:</span>
                            <strong class="text-dark d-block"><?php echo htmlspecialchars($sellerInfo['name']); ?></strong>
                            <span class="text-muted d-block text-truncate"><?php echo htmlspecialchars($sellerInfo['address']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table preview-table align-middle">
                        <thead>
                            <tr>
                                <th>شرح کالا / خدمت</th>
                                <th class="text-center">تعداد</th>
                                <th class="text-end">فی</th>
                                <th class="text-end">جمع</th>
                            </tr>
                        </thead>
                        <tbody id="preview-items-table">
                            <tr><td colspan="4" class="text-center text-muted py-3">آیتمی اضافه نشده است.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="border-top pt-3 small">
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>جمع کل بدون مالیات:</span>
                        <span id="preview-subtotal" class="fw-bold text-dark">0 تومان</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>مالیات بر ارزش افزوده (<span id="preview-vat-rate">9</span>%):</span>
                        <span id="preview-vat-amount" class="fw-bold text-dark">0 تومان</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold text-primary fs-6 border-top pt-2 mt-2">
                        <span>مبلغ قابل پرداخت:</span>
                        <span id="preview-total-amount">0 تومان</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const invoiceTypeSelect = $('#invoice_type');
    const contractItems = $('#contract-items');
    const dynamicItemsContainer = $('#dynamic-items-container');
    const addServiceBtn = $('#add-service-btn');
    const addProductBtn = $('#add-product-btn');
    const contractSelect = $('#contract_id');
    const itemList = $('#item-list');
    const clientSelect = $('#client_id');
    const vatRateInput = $('#vat_rate');
    const subtotalInput = $('#subtotal-input');
    const invoiceNumberInput = $('#invoice_number');
    
    // Preview elements
    const previewInvoiceNumber = $('#preview-invoice-number');
    const previewIssueDate = $('#preview-issue-date');
    const previewClientName = $('#preview-client-name');
    const previewClientAddress = $('#preview-client-address');
    const previewItemsTable = $('#preview-items-table');
    const previewSubtotal = $('#preview-subtotal');
    const previewVatRate = $('#preview-vat-rate');
    const previewVatAmount = $('#preview-vat-amount');
    const previewTotalAmount = $('#preview-total-amount');

    const allProducts = <?php echo json_encode($products); ?>;
    const allServices = <?php echo json_encode($services); ?>;
    const allClients = <?php echo json_encode($clients); ?>;

    function addServiceItem(serviceId = '', quantity = 1, price = 0) {
        let optionsHtml = '<option value="">انتخاب خدمت...</option>';
        allServices.forEach(service => {
            const selected = (serviceId == service.id) ? 'selected' : '';
            optionsHtml += `<option value="${service.id}" data-price="${service.price}" ${selected}>${service.name}</option>`;
        });
        const newItemHtml = `
            <div class="row g-2 align-items-end item-row service-item mb-2" data-type="service">
                <div class="col-md-5">
                    <label class="form-label small text-muted mb-1">خدمت</label>
                    <select class="form-select form-select-sm item-select" name="service_items[service_id][]" required>${optionsHtml}</select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">قیمت واحد</label>
                    <input type="text" class="form-control form-control-sm item-price" name="service_items[price][]" value="${price}" placeholder="قیمت واحد" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">تعداد</label>
                    <input type="number" class="form-control form-control-sm item-quantity" name="service_items[quantity][]" value="${quantity}" required min="1">
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-item rounded-2"><i class="fas fa-trash-alt me-1"></i> حذف</button>
                </div>
            </div>`;
        itemList.append(newItemHtml);
    }
    
    function addProductItem(productId = '', quantity = 1, price = 0) {
        let optionsHtml = '<option value="">انتخاب محصول...</option>';
        allProducts.forEach(product => {
            const selected = (productId == product.id) ? 'selected' : '';
            optionsHtml += `<option value="${product.id}" data-price="${product.sale_price}" ${selected}>${product.name}</option>`;
        });
        const newItemHtml = `
            <div class="row g-2 align-items-end item-row product-item mb-2" data-type="product">
                <div class="col-md-5">
                    <label class="form-label small text-muted mb-1">محصول</label>
                    <select class="form-select form-select-sm item-select" name="product_items[product_id][]" required>${optionsHtml}</select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">قیمت واحد</label>
                    <input type="text" class="form-control form-control-sm item-price" name="product_items[price][]" value="${price}" placeholder="قیمت واحد" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">تعداد</label>
                    <input type="number" class="form-control form-control-sm item-quantity" name="product_items[quantity][]" value="${quantity}" required min="1">
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-item rounded-2"><i class="fas fa-trash-alt me-1"></i> حذف</button>
                </div>
            </div>`;
        itemList.append(newItemHtml);
    }

    function toggleInvoiceType() {
        const type = invoiceTypeSelect.val();
        contractItems.hide();
        dynamicItemsContainer.hide();
        addServiceBtn.hide();
        addProductBtn.hide();
        itemList.empty();
        
        if (type === 'contract') {
            contractItems.show();
        } else if (type === 'service') {
            dynamicItemsContainer.show();
            addServiceBtn.show();
            addServiceItem();
        } else if (type === 'product') {
            dynamicItemsContainer.show();
            addProductBtn.show();
            addProductItem();
        } else if (type === 'mixed') {
            dynamicItemsContainer.show();
            addServiceBtn.show();
            addProductBtn.show();
            addServiceItem();
            addProductItem();
        }
        updateCalculations();
    }

    function updateCalculations() {
        const type = invoiceTypeSelect.val();
        let subtotal = 0;
        let itemsForPreview = [];

        if (type === 'contract') {
            const selectedOption = contractSelect.find('option:selected');
            subtotal = parseFloat($('#contract_subtotal').val()) || 0;
            const title = selectedOption.text() || '---';
            if (subtotal > 0) {
                itemsForPreview.push({ description: title, quantity: 1, price: subtotal, total: subtotal });
            }
        } else {
            itemList.find('.item-row').each(function() {
                const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
                const price = parseFloat($(this).find('.item-price').val()) || 0;
                const total = quantity * price;
                subtotal += total;

                const description = $(this).find('.item-select option:selected').text() || '---';
                if (quantity > 0 && price > 0) {
                    itemsForPreview.push({ description: description, quantity: quantity, price: price, total: total });
                }
            });
        }
        
        const vatRate = parseFloat(vatRateInput.val()) || 0;
        const vatAmount = (subtotal * vatRate) / 100;
        const totalAmount = subtotal + vatAmount;

        subtotalInput.val(subtotal);

        // Update preview table
        previewItemsTable.empty();
        if (itemsForPreview.length > 0) {
            itemsForPreview.forEach(item => {
                const itemRow = `<tr>
                                    <td class="fw-medium">${item.description}</td>
                                    <td class="text-center">${item.quantity}</td>
                                    <td class="text-end">${new Intl.NumberFormat('fa-IR').format(item.price)}</td>
                                    <td class="text-end fw-bold">${new Intl.NumberFormat('fa-IR').format(item.total)}</td>
                                 </tr>`;
                previewItemsTable.append(itemRow);
            });
        } else {
            previewItemsTable.html('<tr><td colspan="4" class="text-center text-muted py-3">آیتمی اضافه نشده است.</td></tr>');
        }

        previewSubtotal.text(new Intl.NumberFormat('fa-IR').format(subtotal) + ' تومان');
        previewVatRate.text(vatRate);
        previewVatAmount.text(new Intl.NumberFormat('fa-IR').format(vatAmount) + ' تومان');
        previewTotalAmount.text(new Intl.NumberFormat('fa-IR').format(totalAmount) + ' تومان');
    }

    function updateClientDetails() {
        const clientId = clientSelect.val();
        if (clientId) {
            const client = allClients.find(c => c.id == clientId);
            if (client) {
                previewClientName.text(client.name || '---');
                previewClientAddress.text(client.address || '---');
            }
        } else {
            previewClientName.text('---');
            previewClientAddress.text('---');
        }
    }

    // Event Listeners
    invoiceTypeSelect.on('change', toggleInvoiceType);
    addServiceBtn.on('click', () => addServiceItem());
    addProductBtn.on('click', () => addProductItem());
    clientSelect.on('change', updateClientDetails);
    contractSelect.on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const amount = selectedOption.data('amount') || 0;
        $('#contract_subtotal').val(amount);
        updateCalculations();
    });
    vatRateInput.on('input', updateCalculations);
    invoiceNumberInput.on('input', () => previewInvoiceNumber.text(invoiceNumberInput.val() || '---'));
    
    itemList.on('input', '.item-row .item-quantity, .item-row .item-price', updateCalculations);
    itemList.on('change', '.item-row .item-select', function() {
        const selectedOption = $(this).find('option:selected');
        const price = selectedOption.data('price') || 0;
        $(this).closest('.item-row').find('.item-price').val(price);
        updateCalculations();
    });
    itemList.on('click', '.remove-item', function() {
        $(this).closest('.item-row').remove();
        updateCalculations();
    });

    toggleInvoiceType();
    updateCalculations();
    updateClientDetails();
});

// Persian Datepicker Loader & Auto Click Handler
(function loadPersianDatePicker() {
    function initPicker() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.pDatepicker) {
            $('.pdp-input').each(function() {
                var $input = $(this);
                $input.pDatepicker({
                    format: 'YYYY/MM/DD',
                    autoClose: true,
                    initialValue: false,
                    observer: true,
                    initialValueType: 'persian',
                    onSelect: function(unix) {
                        if ($input.attr('id') === 'issue_date') {
                            document.getElementById('preview-issue-date').textContent = new persianDate(unix).format('YYYY/MM/DD');
                        }
                    }
                });
            });

            $('.pdp-trigger, .pdp-input').on('click', function(e) {
                e.stopPropagation();
                var $input = $(this).hasClass('pdp-input') ? $(this) : $(this).siblings('.pdp-input');
                $input.focus();
            });
        } else { setTimeout(initPicker, 100); }
    }

    if (!document.getElementById('p-datepicker-css')) {
        var css = document.createElement('link'); css.id = 'p-datepicker-css'; css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css';
        document.head.appendChild(css);
    }

    if (typeof $ === 'undefined') {
        var jq = document.createElement('script'); jq.src = 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js';
        jq.onload = loadPDate; document.head.appendChild(jq);
    } else { loadPDate(); }

    function loadPDate() {
        if (typeof window.persianDate === 'undefined') {
            var pd = document.createElement('script'); pd.src = 'https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js';
            pd.onload = loadPDatePicker; document.head.appendChild(pd);
        } else { loadPDatePicker(); }
    }

    function loadPDatePicker() {
        if (typeof $.fn.pDatepicker === 'undefined') {
            var pdp = document.createElement('script'); pdp.src = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js';
            pdp.onload = initPicker; document.head.appendChild(pdp);
        } else { initPicker(); }
    }
})();
</script>