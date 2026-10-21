<?php
// app/views/admin/inventory/purchases/create.php
global $auth;
$products = $products ?? [];
$vendors = $vendors ?? [];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 16px;
        --radius-md: 10px;
        --shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    body, .premium-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #1e293b;
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
        padding: 0.6rem 0.9rem;
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
        padding: 0.6rem 1.4rem;
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
        transition: border-color 0.2s;
    }

    .item-row:hover { border-color: #cbd5e1; }

    .invoice-preview-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        position: sticky;
        top: 20px;
    }

    .invoice-header-brand {
        border-bottom: 2px dashed var(--border-color);
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .table-modern { font-size: 0.875rem; }
    .table-modern th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        border-bottom: 1px solid var(--border-color);
    }
</style>

<div class="premium-wrapper py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">ثبت فاکتور خرید جدید</h3>
            <p class="text-muted small mb-0">اطلاعات خرید جدید و اقلام آن را وارد نمایید</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=purchases&action=index" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
            <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست
        </a>
    </div>

    <div class="row g-4">
        <!-- Form Section -->
        <div class="col-lg-7">
            <div class="premium-card">
                <div class="premium-card-header">
                    <h5 class="mb-0 fw-bold fs-6 text-dark"><i class="fas fa-edit me-2 text-primary"></i>مشخصات فاکتور</h5>
                </div>
                <div class="card-body p-4">
                    <?php 
                    $message = FlashMessage::get('message');
                    if ($message) {
                        echo '<div class="alert alert-info border-0 rounded-3 shadow-sm mb-4">' . (is_array($message) ? implode(' ', $message) : $message) . '</div>';
                    }
                    ?>
                    
                    <form id="purchase-form" action="<?php echo APP_URL; ?>/index.php?page=purchases&action=store" method="POST">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="vendor_id" class="form-label fw-medium small">فروشنده <span class="text-danger">*</span></label>
                                <select class="form-select" id="vendor_id" name="vendor_id" required>
                                    <option value="">انتخاب فروشنده...</option>
                                    <?php foreach ($vendors as $vendor): ?>
                                        <option value="<?php echo $vendor->id; ?>"><?php echo htmlspecialchars($vendor->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="purchase_date" class="form-label fw-medium small">تاریخ خرید <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text pdp-trigger"><i class="far fa-calendar-alt text-muted"></i></span>
                                    <input type="text" class="form-control pdp-input border-start-0" id="purchase_date" name="purchase_date" placeholder="YYYY/MM/DD" autocomplete="off" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-dark fs-6">اقلام فاکتور</h6>
                            <button type="button" id="add-item-btn" class="btn btn-light btn-sm text-primary border fw-medium rounded-2">
                                <i class="fas fa-plus me-1"></i> افزودن کالا
                            </button>
                        </div>

                        <div id="purchase-items-container"></div>

                        <div class="row g-3 mt-3 pt-3 border-top">
                            <div class="col-md-6">
                                <label for="vat_rate" class="form-label fw-medium small">نرخ مالیات بر ارزش افزوده (%)</label>
                                <input type="number" class="form-control" id="vat_rate" name="vat_rate" value="9" min="0" max="100">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="<?php echo APP_URL; ?>/index.php?page=purchases&action=index" class="btn btn-light px-4 rounded-2">انصراف</a>
                            <button type="submit" class="btn btn-gradient-primary px-4">ثبت نهایی فاکتور</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Live Preview Section -->
        <div class="col-lg-5">
            <div class="invoice-preview-card p-4">
                <div class="invoice-header-brand d-flex justify-content-between align-items-start">
                    <div>
                        <span class="badge bg-soft-primary text-primary fw-normal mb-2 px-3 py-1.5 rounded-pill" style="background: #e0e7ff; color: #4338ca;">پیش‌نمایش زنده</span>
                        <h5 class="fw-bold mb-1">پیش‌نمایش فاکتور</h5>
                        <p class="text-muted small mb-0">فروشنده: <span id="preview-vendor-name" class="fw-bold text-dark">---</span></p>
                    </div>
                    <div class="text-end text-muted small">
                        <div>تاریخ: <span id="preview-purchase-date" class="fw-medium text-dark">---</span></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>کالا</th>
                                <th class="text-center">تعداد</th>
                                <th class="text-end">فی</th>
                                <th class="text-end">جمع</th>
                            </tr>
                        </thead>
                        <tbody id="preview-items-body"></tbody>
                    </table>
                </div>

                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between text-muted mb-2 small">
                        <span>جمع کل بدون مالیات:</span>
                        <span id="preview-subtotal-amount" class="fw-medium text-dark">0 تومان</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted mb-2 small">
                        <span>مالیات بر ارزش افزوده:</span>
                        <span id="preview-vat-amount" class="fw-medium text-dark">0 تومان</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold fs-6 border-top pt-2 mt-2 text-primary">
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
    const itemsContainer = document.getElementById('purchase-items-container');
    const addItemBtn = document.getElementById('add-item-btn');
    const vendorsSelect = document.getElementById('vendor_id');
    const vatRateInput = document.getElementById('vat_rate');

    const previewVendorName = document.getElementById('preview-vendor-name');
    const previewPurchaseDate = document.getElementById('preview-purchase-date');
    const previewItemsBody = document.getElementById('preview-items-body');
    const previewSubtotalAmount = document.getElementById('preview-subtotal-amount');
    const previewVatAmount = document.getElementById('preview-vat-amount');
    const previewTotalAmount = document.getElementById('preview-total-amount');

    let itemIndex = 0;

    const updateInvoicePreview = () => {
        let subtotal = 0;
        let previewHtml = '';
        
        document.querySelectorAll('.item-row').forEach(row => {
            const select = row.querySelector('.product-select');
            const productName = select.options[select.selectedIndex]?.text.split('(')[0] || '';
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.price-input').value) || 0;
            const totalPrice = quantity * unitPrice;
            subtotal += totalPrice;

            if (productName && quantity > 0) {
                 previewHtml += `
                    <tr>
                        <td class="fw-medium">${productName}</td>
                        <td class="text-center">${quantity}</td>
                        <td class="text-end">${new Intl.NumberFormat('fa-IR').format(unitPrice)}</td>
                        <td class="text-end fw-bold">${new Intl.NumberFormat('fa-IR').format(totalPrice)}</td>
                    </tr>
                `;
            }
        });

        const vatRate = parseFloat(vatRateInput.value) || 0;
        const vatAmount = (subtotal * vatRate) / 100;
        const totalAmount = subtotal + vatAmount;

        previewItemsBody.innerHTML = previewHtml || '<tr><td colspan="4" class="text-center text-muted py-3">هیچ کالایی اضافه نشده است</td></tr>';
        previewSubtotalAmount.textContent = new Intl.NumberFormat('fa-IR').format(subtotal) + ' تومان';
        previewVatAmount.textContent = new Intl.NumberFormat('fa-IR').format(vatAmount) + ' تومان';
        previewTotalAmount.textContent = new Intl.NumberFormat('fa-IR').format(totalAmount) + ' تومان';
    };

    const createItemRow = () => {
        const products = <?php echo json_encode($products); ?>;
        const newRow = document.createElement('div');
        newRow.classList.add('item-row');
        newRow.innerHTML = `
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small text-muted mb-1">کالا</label>
                    <select class="form-select form-select-sm product-select" name="items[${itemIndex}][product_id]" required>
                        <option value="">انتخاب کنید...</option>
                        ${products.map(p => `<option value="${p.id}" data-price="${p.purchase_price}">${p.name} (${p.sku})</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">تعداد</label>
                    <input type="number" class="form-control form-control-sm quantity-input" name="items[${itemIndex}][quantity]" value="1" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">قیمت واحد</label>
                    <input type="number" class="form-control form-control-sm price-input" name="items[${itemIndex}][price]" required min="0">
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-item-btn rounded-2"><i class="fas fa-trash-alt me-1"></i> حذف</button>
                </div>
            </div>
        `;
        itemsContainer.appendChild(newRow);
        itemIndex++;

        const productSelect = newRow.querySelector('.product-select');
        const priceInput = newRow.querySelector('.price-input');
        const quantityInput = newRow.querySelector('.quantity-input');

        productSelect.addEventListener('change', (e) => {
            const selectedOption = e.target.options[e.target.selectedIndex];
            priceInput.value = selectedOption.dataset.price || 0;
            updateInvoicePreview();
        });

        priceInput.addEventListener('input', updateInvoicePreview);
        quantityInput.addEventListener('input', updateInvoicePreview);
        vatRateInput.addEventListener('input', updateInvoicePreview);
        
        updateInvoicePreview();
    };

    addItemBtn.addEventListener('click', createItemRow);
    
    itemsContainer.addEventListener('click', (e) => {
        if (e.target.closest('.remove-item-btn')) {
            e.target.closest('.item-row').remove();
            updateInvoicePreview();
        }
    });

    createItemRow();

    vendorsSelect.addEventListener('change', () => {
        const selectedOption = vendorsSelect.options[vendorsSelect.selectedIndex];
        previewVendorName.textContent = selectedOption.text !== 'انتخاب فروشنده...' ? selectedOption.text : '---';
    });
});

// Persian Datepicker Dynamic Loader & Auto Click Handler
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
                        var pDate = new persianDate(unix).format('YYYY/MM/DD');
                        document.getElementById('preview-purchase-date').textContent = pDate;
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