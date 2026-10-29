<?php
// app/view/admin/invoices/create_edit.php
$invoice = $invoice ?? null;
$clients = $clients ?? [];
$contracts = $contracts ?? [];
$products = $products ?? [];
$services = $services ?? [];
$invoiceItems = $invoiceItems ?? []; // ✅ تعریف متغیر برای آیتم‌های فاکتور
$newInvoiceNumber = $newInvoiceNumber ?? '';
$issue_date_jalali = $issue_date_jalali ?? '';
$due_date_jalali = $due_date_jalali ?? '';
$clientInfo = (object)['name' => '', 'address' => '', 'phone' => '', 'economic_code' => ''];

// اطلاعات فروشنده (ثابت)
$sellerInfo = [
    'name' => 'شرکت رایان تکرو',
    'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
    'address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه ۳',
    'economic_code' => '۱۴۰۰۹۱۵۶۳۲۱',
    'registration_number' => '۵۵۲۰۰',
];

function getClientDetails($clients, $clientId) {
    foreach ($clients as $client) {
        if ($client->id == $clientId) {
            return $client;
        }
    }
    return (object)['name' => '', 'address' => '', 'phone' => '', 'economic_code' => ''];
}
?>

<style>
    .invoice-form-container {
        display: flex;
        gap: 30px;
        flex-direction: row;
    }
    .invoice-form {
        flex: 1;
    }
    .invoice-preview {
        flex: 1;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        position: sticky;
        top: 20px;
        height: fit-content;
        border: 1px solid #e9ecef;
    }
    .card-invoice {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 2rem;
    }
    .invoice-header {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }
    .invoice-logo {
        max-width: 150px;
        height: auto;
    }
    .invoice-title {
        font-weight: 700;
        color: #343a40;
    }
    .invoice-details span, .invoice-details p {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .invoice-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
        margin-bottom: 1rem;
    }
    .table-invoice th, .table-invoice td {
        font-size: 0.9rem;
        vertical-align: middle;
    }
    .table-invoice thead th {
        border-bottom: 2px solid #dee2e6;
    }
    .table-invoice tbody tr:last-child td {
        border-bottom: none;
    }
    .total-row {
        font-weight: 700;
        font-size: 1.1rem;
    }
    .invoice-footer {
        border-top: 2px solid #e9ecef;
        padding-top: 2rem;
        margin-top: 2rem;
    }
    .info-box {
        background-color: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 1.5rem;
    }
    .item-row {
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        background-color: #fcfcfc;
    }
    @media (max-width: 992px) {
        .invoice-form-container {
            flex-direction: column;
        }
        .invoice-preview {
            position: static;
        }
    }
</style>

<h1 class="h3 mb-4 text-gray-800"><?php echo $invoice ? 'ویرایش فاکتور' : 'صدور فاکتور جدید'; ?></h1>

<?php FlashMessage::display(); ?>

<div class="invoice-form-container">
    <div class="invoice-form">
        <div class="card card-invoice">
            <form id="invoice-form" action="<?php echo APP_URL; ?>/index.php?page=admin&action=<?php echo $invoice ? 'invoices_update&id=' . $invoice->id : 'invoices_store'; ?>" method="POST">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="invoice_number" class="form-label">شماره فاکتور</label>
                            <input type="text" class="form-control form-control-sm" id="invoice_number" name="invoice_number" value="<?php echo htmlspecialchars($invoice->invoice_number ?? $newInvoiceNumber); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="client_id" class="form-label">مشتری</label>
                            <select class="form-select form-select-sm" id="client_id" name="client_id" required>
                                <option value="">انتخاب کنید...</option>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?php echo $client->id; ?>" <?php echo ($invoice && $invoice->client_id == $client->id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($client->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="issue_date" class="form-label">تاریخ صدور</label>
                            <input type="text" class="form-control form-control-sm persian-datepicker" id="issue_date" name="issue_date" value="<?php echo htmlspecialchars($issue_date_jalali); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="due_date" class="form-label">تاریخ سررسید</label>
                            <input type="text" class="form-control form-control-sm persian-datepicker" id="due_date" name="due_date" value="<?php echo htmlspecialchars($due_date_jalali); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="status" class="form-label">وضعیت</label>
                            <select class="form-select form-select-sm" id="status" name="status" required>
                                <option value="pending" <?php echo ($invoice && $invoice->status == 'pending') ? 'selected' : ''; ?>>در انتظار پرداخت</option>
                                <option value="paid" <?php echo ($invoice && $invoice->status == 'paid') ? 'selected' : ''; ?>>پرداخت شده</option>
                                <option value="overdue" <?php echo ($invoice && $invoice->status == 'overdue') ? 'selected' : ''; ?>>سررسید گذشته</option>
                                <option value="canceled" <?php echo ($invoice && $invoice->status == 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="invoice_type" class="form-label">نوع فاکتور</label>
                    <select class="form-select form-select-sm" id="invoice_type" name="invoice_type" required>
                        <option value="contract" <?php echo ($invoice && $invoice->invoice_type == 'contract') ? 'selected' : ''; ?>>قرارداد</option>
                        <option value="service" <?php echo ($invoice && $invoice->invoice_type == 'service') ? 'selected' : ''; ?>>خدمات</option>
                        <option value="product" <?php echo ($invoice && $invoice->invoice_type == 'product') ? 'selected' : ''; ?>>محصول</option>
                        <option value="mixed" <?php echo ($invoice && $invoice->invoice_type == 'mixed') ? 'selected' : ''; ?>>ترکیبی (خدمات و محصول)</option>
                    </select>
                </div>

                <!-- انتخاب نوع رسمیت و تنظیمات سامانه مودیان -->
                <div class="card border p-3 rounded-3 mb-3" style="background:#f8fafc; border-color:#e2e8f0 !important;">
                    <label class="form-label fw-bold text-dark mb-2">
                        <i class="fas fa-stamp text-primary me-1"></i> نوع رسمیت فاکتور:
                    </label>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="d-flex align-items-center p-2 rounded-3 border bg-white shadow-sm" style="cursor:pointer;">
                                <input type="radio" name="is_official" value="1" id="official_radio" class="form-check-input me-2 mt-0" <?php echo (!isset($invoice) || ($invoice->is_official ?? 1) == 1) ? 'checked' : ''; ?> onchange="toggleOfficialFields()">
                                <div>
                                    <span class="fw-bold text-success d-block"><i class="fas fa-check-circle me-1"></i> فاکتور رسمی (سامانه مودیان)</span>
                                    <small class="text-muted" style="font-size:0.75rem;">دارای ارزش افزوده ۱۰٪، کد اقتصادی و ثبت در کارپوشه دارایی</small>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label class="d-flex align-items-center p-2 rounded-3 border bg-white shadow-sm" style="cursor:pointer;">
                                <input type="radio" name="is_official" value="0" id="unofficial_radio" class="form-check-input me-2 mt-0" <?php echo (isset($invoice) && ($invoice->is_official ?? 1) == 0) ? 'checked' : ''; ?> onchange="toggleOfficialFields()">
                                <div>
                                    <span class="fw-bold text-secondary d-block"><i class="fas fa-file-alt me-1"></i> فاکتور غیر رسمی (داخلی)</span>
                                    <small class="text-muted" style="font-size:0.75rem;">ویژه امور داخلی، بدون ارسال به مودیان و بدون ارزش افزوده اجباری</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- فیلدهای تکمیلی سامانه مودیان -->
                    <div id="moadian-extra-fields" style="<?php echo (isset($invoice) && ($invoice->is_official ?? 1) == 0) ? 'display:none;' : 'display:block;'; ?>">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label for="economic_code" class="form-label text-muted small">کد اقتصادی خریدار:</label>
                                <input type="text" class="form-control form-control-sm font-monospace" id="economic_code" name="economic_code" placeholder="۴۱۱۱۱۱۱۱۱۱۱۱" value="<?php echo htmlspecialchars($invoice->economic_code ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="national_id" class="form-label text-muted small">شناسه ملی / کد ملی خریدار:</label>
                                <input type="text" class="form-control form-control-sm font-monospace" id="national_id" name="national_id" placeholder="۱۰۱۰۱۰۱۰۱۰۱" value="<?php echo htmlspecialchars($invoice->national_id ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="pattern_type" class="form-label text-muted small">الگوی صورتحساب الکترونیکی:</label>
                                <select class="form-select form-select-sm" id="pattern_type" name="pattern_type">
                                    <option value="1" <?php echo (($invoice->pattern_type ?? 1) == 1) ? 'selected' : ''; ?>>الگوی ۱: فروش با اطلاعات کامل خریدار</option>
                                    <option value="2" <?php echo (($invoice->pattern_type ?? 1) == 2) ? 'selected' : ''; ?>>الگوی ۲: فروش به مصرف کننده نهایی</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" id="send_to_moadian" name="send_to_moadian" value="1" checked>
                            <label class="form-check-label small fw-bold text-primary" for="send_to_moadian">
                                <i class="fas fa-paper-plane me-1"></i> ارسال خودکار به کارپوشه سامانه مودیان بلافاصله پس از ثبت فاکتور
                            </label>
                        </div>
                    </div>
                </div>

                <script>
                function toggleOfficialFields() {
                    var isOfficial = document.getElementById('official_radio').checked;
                    var extra = document.getElementById('moadian-extra-fields');
                    var vatInput = document.getElementById('vat_rate');
                    if (isOfficial) {
                        if (extra) extra.style.display = 'block';
                        if (vatInput) vatInput.value = '10';
                    } else {
                        if (extra) extra.style.display = 'none';
                        if (vatInput) vatInput.value = '0';
                    }
                    if (typeof calculateTotals === 'function') calculateTotals();
                }
                </script>
                
                <hr>
                
                <div id="item-fields-container">
                    <div id="contract-items" style="display: none;">
                        <p class="invoice-section-title">جزئیات قرارداد:</p>
                        <div class="mb-3">
                            <label for="contract_id" class="form-label">قرارداد مرتبط</label>
                            <select class="form-select form-select-sm" id="contract_id" name="contract_id">
                                <option value="">انتخاب کنید...</option>
                                <?php foreach ($contracts as $contract): ?>
                                    <option value="<?php echo $contract->id; ?>" data-amount="<?php echo htmlspecialchars($contract->total_amount); ?>" <?php echo ($invoice && $invoice->invoice_type === 'contract' && $invoice->contract_id == $contract->id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($contract->title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="contract_subtotal" class="form-label">مبلغ کل قرارداد</label>
                            <input type="text" class="form-control form-control-sm" id="contract_subtotal" name="contract_subtotal" value="<?php echo htmlspecialchars($invoice->subtotal ?? 0); ?>" readonly>
                        </div>
                    </div>

                    <div id="dynamic-items-container" style="display: none;">
                        <p class="invoice-section-title">جزئیات آیتم‌ها:</p>
                        <div class="d-flex justify-content-end mb-2 gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success" id="add-service-btn" style="display: none;">+ افزودن خدمت</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="add-product-btn" style="display: none;">+ افزودن محصول</button>
                        </div>
                        <div id="item-list">
                            </div>
                    </div>
                </div>

                <hr>

                <div class="mb-4">
                    <label for="description" class="form-label">شرح فاکتور</label>
                    <textarea class="form-control form-control-sm" id="description" name="description" rows="3"><?php echo htmlspecialchars($invoice->description ?? ''); ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="vat_rate" class="form-label">نرخ مالیات (درصد)</label>
                            <input type="number" class="form-control form-control-sm" id="vat_rate" name="vat_rate" value="<?php echo htmlspecialchars($invoice->vat_rate ?? 9); ?>" min="0" max="100">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="subtotal-input" class="form-label">جمع کل (بدون مالیات)</label>
                            <input type="text" class="form-control form-control-sm" id="subtotal-input" value="<?php echo htmlspecialchars($invoice->subtotal ?? 0); ?>" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg"><?php echo $invoice ? 'به‌روزرسانی فاکتور' : 'صدور فاکتور'; ?></button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="invoice-preview">
        <div class="row invoice-header">
            <div class="col-6">
                <img src="<?php echo APP_URL; ?>/assets/img/company_logo.png" alt="Company Logo" class="invoice-logo">
            </div>
            <div class="col-6 text-end">
                <h2 class="invoice-title">فاکتور فروش</h2>
                <div class="invoice-details mt-2">
                    <p class="mb-0"><strong>شماره فاکتور:</strong> <span id="preview-invoice-number">---</span></p>
                    <p class="mb-0"><strong>تاریخ صدور:</strong> <span id="preview-issue-date">---</span></p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <p class="invoice-section-title">اطلاعات خریدار:</p>
                <div class="info-box">
                    <p class="mb-1"><strong>نام:</strong> <span id="preview-client-name">---</span></p>
                    <p class="mb-1"><strong>آدرس:</strong> <span id="preview-client-address">---</span></p>
                    <p class="mb-0"><strong>کد اقتصادی:</strong> <span id="preview-client-economic-code">---</span></p>
                </div>
            </div>
            <div class="col-6">
                <p class="invoice-section-title">اطلاعات فروشنده:</p>
                <div class="info-box">
                    <p class="mb-1"><strong>نام:</strong> <?php echo htmlspecialchars($sellerInfo['name']); ?></p>
                    <p class="mb-1"><strong>آدرس:</strong> <?php echo htmlspecialchars($sellerInfo['address']); ?></p>
                    <p class="mb-0"><strong>کد اقتصادی:</strong> <?php echo htmlspecialchars($sellerInfo['economic_code']); ?></p>
                </div>
            </div>
        </div>
        
        <p class="invoice-section-title">ریز فاکتور:</p>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-invoice">
                <thead>
                    <tr>
                        <th>شرح</th>
                        <th>تعداد</th>
                        <th>قیمت واحد</th>
                        <th>قیمت کل</th>
                    </tr>
                </thead>
                <tbody id="preview-items-table">
                    <tr><td colspan="4" class="text-center text-muted">آیتمی اضافه نشده است.</td></tr>
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end text-start">
            <div class="col-md-8">
                <table class="table table-sm table-borderless">
                    <tbody>
                        <tr>
                            <td>جمع کل (ریال):</td>
                            <td class="text-end" id="preview-subtotal">۰</td>
                        </tr>
                        <tr>
                            <td>مالیات بر ارزش افزوده (<span id="preview-vat-rate">۹</span>%):</td>
                            <td class="text-end" id="preview-vat-amount">۰</td>
                        </tr>
                        <tr class="total-row">
                            <td>مبلغ قابل پرداخت (ریال):</td>
                            <td class="text-end" id="preview-total-amount">۰</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center">
                                <p class="mb-0 text-muted" id="preview-total-amount-in-words">
                                    مبلغ به حروف: ---
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $(".persian-datepicker").pDatepicker({
        format: 'YYYY/MM/DD',
        autoClose: true,
        initialValue: false,
        onSelect: function(date) {
            updatePreview();
        }
    });

    const invoiceTypeSelect = $('#invoice_type');
    const contractItems = $('#contract-items');
    const dynamicItemsContainer = $('#dynamic-items-container');
    const addServiceBtn = $('#add-service-btn');
    const addProductBtn = $('#add-product-btn');
    const contractSelect = $('#contract_id');
    const itemList = $('#item-list');
    const clientSelect = $('#client_id');
    const vatRateInput = $('#vat_rate');
    const vatAmountInput = $('#vat_amount');
    const subtotalInput = $('#subtotal-input');
    const totalAmountInput = $('#total_amount');
    const invoiceNumberInput = $('#invoice_number');
    const issueDateInput = $('#issue_date');
    
    // Preview elements
    const previewInvoiceNumber = $('#preview-invoice-number');
    const previewIssueDate = $('#preview-issue-date');
    const previewClientName = $('#preview-client-name');
    const previewClientAddress = $('#preview-client-address');
    const previewClientEconomicCode = $('#preview-client-economic-code');
    const previewItemsTable = $('#preview-items-table');
    const previewSubtotal = $('#preview-subtotal');
    const previewVatRate = $('#preview-vat-rate');
    const previewVatAmount = $('#preview-vat-amount');
    const previewTotalAmount = $('#preview-total-amount');
    const previewTotalAmountInWords = $('#preview-total-amount-in-words');

    const allProducts = <?php echo json_encode($products); ?>;
    const allServices = <?php echo json_encode($services); ?>;
    const allClients = <?php echo json_encode($clients); ?>;
    const invoiceData = <?php echo json_encode($invoice); ?>;
    const invoiceItems = <?php echo json_encode($invoiceItems ?? []); ?>;

    function addServiceItem(serviceId = '', quantity = 1, price = 0) {
        let optionsHtml = '<option value="">انتخاب خدمت...</option>';
        allServices.forEach(service => {
            const selected = (serviceId == service.id) ? 'selected' : '';
            optionsHtml += `<option value="${service.id}" data-price="${service.price}" ${selected}>${service.name}</option>`;
        });
        const newItemHtml = `
            <div class="row item-row service-item" data-type="service">
                <div class="col-md-5">
                    <select class="form-select form-select-sm item-select" name="service_items[service_id][]" required>${optionsHtml}</select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm item-price" name="service_items[price][]" value="${price}" placeholder="قیمت واحد" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control form-control-sm item-quantity" name="service_items[quantity][]" value="${quantity}" required min="1">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-item">حذف</button>
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
            <div class="row item-row product-item" data-type="product">
                <div class="col-md-5">
                    <select class="form-select form-select-sm item-select" name="product_items[product_id][]" required>${optionsHtml}</select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control form-control-sm item-price" name="product_items[price][]" value="${price}" placeholder="قیمت واحد" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control form-control-sm item-quantity" name="product_items[quantity][]" value="${quantity}" required min="1">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-item">حذف</button>
                </div>
            </div>`;
        itemList.append(newItemHtml);
    }
    
    function loadInvoiceItems() {
        if (invoiceItems.length > 0) {
            itemList.empty();
            invoiceItems.forEach(item => {
                if (item.product_id) {
                    addProductItem(item.product_id, item.quantity, item.price);
                } else if (item.service_id) {
                    addServiceItem(item.service_id, item.quantity, item.price);
                }
            });
        }
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
            if (invoiceData && invoiceData.invoice_type === 'service') {
                loadInvoiceItems();
            } else {
                addServiceItem();
            }
        } else if (type === 'product') {
            dynamicItemsContainer.show();
            addProductBtn.show();
             if (invoiceData && invoiceData.invoice_type === 'product') {
                loadInvoiceItems();
            } else {
                addProductItem();
            }
        } else if (type === 'mixed') {
            dynamicItemsContainer.show();
            addServiceBtn.show();
            addProductBtn.show();
            if (invoiceData && invoiceData.invoice_type === 'mixed') {
                loadInvoiceItems();
            } else {
                 addServiceItem();
                 addProductItem();
            }
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
                const itemType = $(this).data('type');
                const quantity = parseFloat($(this).find('.item-quantity').val()) || 0;
                const price = parseFloat($(this).find('.item-price').val().replace(/,/g, '')) || 0;
                const total = quantity * price;
                subtotal += total;

                let description = '---';
                if (itemType === 'service') {
                    description = $(this).find('.item-select option:selected').text();
                } else if (itemType === 'product') {
                    description = $(this).find('.item-select option:selected').text();
                }

                itemsForPreview.push({ description: description, quantity: quantity, price: price, total: total });
            });
        }
        
        const vatRate = parseFloat(vatRateInput.val()) || 0;
        const vatAmount = (subtotal * vatRate) / 100;
        const totalAmount = subtotal + vatAmount;

        // Update preview
        previewItemsTable.empty();
        if (itemsForPreview.length > 0) {
            itemsForPreview.forEach(item => {
                const itemRow = `<tr>
                                    <td>${item.description}</td>
                                    <td>${new Intl.NumberFormat().format(item.quantity)}</td>
                                    <td>${new Intl.NumberFormat().format(item.price)}</td>
                                    <td>${new Intl.NumberFormat().format(item.total)}</td>
                                 </tr>`;
                previewItemsTable.append(itemRow);
            });
        } else {
            previewItemsTable.html('<tr><td colspan="4" class="text-center text-muted">آیتمی اضافه نشده است.</td></tr>');
        }

        previewSubtotal.text(new Intl.NumberFormat().format(subtotal));
        previewVatRate.text(vatRate);
        previewVatAmount.text(new Intl.NumberFormat().format(vatAmount));
        previewTotalAmount.text(new Intl.NumberFormat().format(totalAmount));
        
        // Update total amount in words
        // فرض بر وجود یک API برای این کار
        fetch(`<?php echo APP_URL; ?>/index.php?page=ajax&action=number_to_words&number=${totalAmount}`)
            .then(response => response.json())
            .then(data => {
                if(data.words) {
                    previewTotalAmountInWords.text(`مبلغ به حروف: ${data.words}`);
                }
            })
            .catch(error => {
                console.error('Error fetching number to words:', error);
                previewTotalAmountInWords.text('مبلغ به حروف: ---');
            });
    }

    function updateClientDetails() {
        const clientId = clientSelect.val();
        if (clientId) {
            const client = allClients.find(c => c.id == clientId);
            if (client) {
                previewClientName.text(client.name || '---');
                previewClientAddress.text(client.address || '---');
                previewClientEconomicCode.text(client.economic_code || '---');
            }
        } else {
            previewClientName.text('---');
            previewClientAddress.text('---');
            previewClientEconomicCode.text('---');
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
    issueDateInput.on('change', () => previewIssueDate.text(issueDateInput.val() || '---'));
    
    // Dynamic event listener for item list
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

    // Initial load
    toggleInvoiceType();
    updateCalculations();
    updateClientDetails();
});
</script>