<?php
// app/views/admin/inventory/purchases/create.php
global $auth;
$products = $products ?? [];
$vendors = $vendors ?? [];
?>

<style>
    .invoice-preview-container {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        background-color: #fff;
    }
    .invoice-header {
        border-bottom: 2px solid #34495e;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .invoice-title {
        font-family: 'B Homa', 'Tahoma', sans-serif;
        font-weight: bold;
        color: #34495e;
    }
    .invoice-logo {
        max-width: 150px;
        height: auto;
    }
    .invoice-table th, .invoice-table td {
        border-color: #e0e0e0;
        vertical-align: middle;
        text-align: center;
        font-size: 14px;
        padding: 8px;
    }
    .invoice-total-row td {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .text-sm {
        font-size: 12px;
    }
    /* Dynamic form styles */
    #purchase-items-container .row {
        background-color: #f9f9f9;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #eee;
    }
</style>

<h1 class="mb-4">ثبت خرید جدید</h1>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white p-3">
                <h5 class="mb-0">فرم فاکتور خرید</h5>
            </div>
            <div class="card-body">
                <?php echo FlashMessage::get('message'); ?>
                <form id="purchase-form" action="<?php echo APP_URL; ?>/index.php?page=purchases&action=store" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="vendor_id" class="form-label">فروشنده</label>
                            <select class="form-select" id="vendor_id" name="vendor_id" required>
                                <option value="">انتخاب کنید...</option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?php echo $vendor->id; ?>"><?php echo htmlspecialchars($vendor->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="purchase_date" class="form-label">تاریخ خرید</label>
                            <input type="text" class="form-control" id="purchase_date" name="purchase_date" placeholder="YYYY/MM/DD" required>
                        </div>
                    </div>
                    
                    <hr>
                    <h5>اقلام خرید</h5>
                    <div id="purchase-items-container">
                        </div>
                    
                    <button type="button" id="add-item-btn" class="btn btn-info btn-sm mt-3"><i class="fas fa-plus"></i> افزودن کالا</button>

                    <div class="text-start mt-4">
                        <button type="submit" class="btn btn-success">ثبت فاکتور خرید</button>
                        <a href="<?php echo APP_URL; ?>/index.php?page=purchases&action=index" class="btn btn-secondary">بازگشت</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="invoice-preview-container shadow-sm p-4">
            <div class="row invoice-header align-items-center">
                <div class="col-md-6">
                    <img src="<?php echo APP_URL; ?>/assets/img/company_logo.png" alt="Company Logo" class="invoice-logo float-end">
                </div>
                <div class="col-md-6 text-end">
                    <h4 class="invoice-title">فاکتور خرید</h4>
                    <p class="mb-0 text-sm">شرکت رایان تکرو</p>
                    <p class="mb-0 text-sm">شماره: <span id="preview-invoice-number">---</span></p>
                    <p class="mb-0 text-sm">تاریخ: <span id="preview-purchase-date">---</span></p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12 text-end">
                    <p class="mb-1 text-sm">فروشنده: <strong id="preview-vendor-name">---</strong></p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped invoice-table">
                    <thead>
                        <tr>
                            <th>شرح کالا</th>
                            <th>تعداد</th>
                            <th>واحد</th>
                            <th>قیمت واحد</th>
                            <th>قیمت کل</th>
                        </tr>
                    </thead>
                    <tbody id="preview-items-body">
                        </tbody>
                    <tfoot>
                        <tr class="invoice-total-row">
                            <td colspan="4" class="text-end">جمع کل فاکتور:</td>
                            <td id="preview-total-amount">0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemsContainer = document.getElementById('purchase-items-container');
        const addItemBtn = document.getElementById('add-item-btn');
        const purchaseForm = document.getElementById('purchase-form');
        const vendorsSelect = document.getElementById('vendor_id');

        const previewVendorName = document.getElementById('preview-vendor-name');
        const previewPurchaseDate = document.getElementById('preview-purchase-date');
        const previewItemsBody = document.getElementById('preview-items-body');
        const previewTotalAmount = document.getElementById('preview-total-amount');

        let itemIndex = 0;

        // Function to update the invoice preview
        const updateInvoicePreview = () => {
            let totalAmount = 0;
            let previewHtml = '';
            document.querySelectorAll('.purchase-item-row').forEach(row => {
                const productName = row.querySelector('.product-select option:checked').textContent;
                const quantity = row.querySelector('.quantity-input').value;
                const unitPrice = row.querySelector('.price-input').value;
                const totalPrice = quantity * unitPrice;
                totalAmount += totalPrice;
                if (productName && quantity && unitPrice) {
                     previewHtml += `
                        <tr>
                            <td>${productName}</td>
                            <td>${quantity}</td>
                            <td>---</td>
                            <td>${new Intl.NumberFormat().format(unitPrice)}</td>
                            <td>${new Intl.NumberFormat().format(totalPrice)}</td>
                        </tr>
                    `;
                }
            });
            previewItemsBody.innerHTML = previewHtml;
            previewTotalAmount.textContent = new Intl.NumberFormat().format(totalAmount);
        };

        const createItemRow = () => {
            const products = <?php echo json_encode($products); ?>;
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-3', 'align-items-center', 'purchase-item-row');
            newRow.innerHTML = `
                <div class="col-md-5">
                    <label class="form-label text-sm">کالا</label>
                    <select class="form-select form-select-sm product-select" name="items[${itemIndex}][product_id]" required>
                        <option value="">انتخاب کنید...</option>
                        ${products.map(p => `<option value="${p.id}" data-price="${p.purchase_price}">${p.name} (${p.sku})</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-sm">تعداد</label>
                    <input type="number" class="form-control form-control-sm quantity-input" name="items[${itemIndex}][quantity]" value="1" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-sm">قیمت واحد</label>
                    <input type="number" class="form-control form-control-sm price-input" name="items[${itemIndex}][price]" required min="0">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="fas fa-times"></i> حذف</button>
                </div>
            `;
            itemsContainer.appendChild(newRow);
            itemIndex++;

            const productSelect = newRow.querySelector('.product-select');
            const priceInput = newRow.querySelector('.price-input');
            const quantityInput = newRow.querySelector('.quantity-input');

            // Set default price based on selected product
            productSelect.addEventListener('change', (e) => {
                const selectedOption = e.target.options[e.target.selectedIndex];
                priceInput.value = selectedOption.dataset.price;
                updateInvoicePreview();
            });

            // Update preview on quantity or price change
            priceInput.addEventListener('input', updateInvoicePreview);
            quantityInput.addEventListener('input', updateInvoicePreview);

            // Trigger preview update on initial load for the new row
            updateInvoicePreview();
        };

        addItemBtn.addEventListener('click', createItemRow);
        
        itemsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-item-btn')) {
                e.target.closest('.purchase-item-row').remove();
                updateInvoicePreview(); // Update preview after removing a row
            }
        });

        // Initial row creation
        createItemRow();

        // Live update for vendor and date
        vendorsSelect.addEventListener('change', () => {
            const selectedOption = vendorsSelect.options[vendorsSelect.selectedIndex];
            previewVendorName.textContent = selectedOption.textContent;
        });

        $('#purchase_date').persianDatepicker({
            format: 'YYYY/MM/DD',
            onSelect: function(date) {
                previewPurchaseDate.textContent = date.format('YYYY/MM/DD');
            }
        });
        
        // Initial setup for the preview
        previewVendorName.textContent = vendorsSelect.options[vendorsSelect.selectedIndex].textContent;
        previewPurchaseDate.textContent = $('#purchase_date').val() || '---';
    });
</script>