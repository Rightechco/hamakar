<?php
// app/views/admin/invoices/create_edit.php
// نسخه کامل و نهایی با قابلیت انتخاب قرارداد و محاسبه ارزش افزوده
global $categories; // ✅ اضافه شدن
$isEdit = isset($invoice) && $invoice !== null;
$formAction = $isEdit ? APP_URL . '/index.php?page=admin&action=invoices_update&id=' . $invoice->id : APP_URL . '/index.php?page=admin&action=invoices_store';
$buttonText = $isEdit ? 'ذخیره تغییرات' : 'صدور فاکتور';
$pageTitle = $isEdit ? 'ویرایش فاکتور' : 'صدور فاکتور جدید';

?>

<h1 class="mb-4"><?php echo $pageTitle; ?></h1>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><?php echo $pageTitle; ?></h5>
    </div>
    <div class="card-body">
        <form action="<?php echo $formAction; ?>" method="POST" id="invoice-form">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="invoice_number" class="form-label">شماره فاکتور:</label>
                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo $isEdit ? htmlspecialchars($invoice->invoice_number) : htmlspecialchars($newInvoiceNumber ?? ''); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="client_id" class="form-label">کارفرما:</label>
                    <select class="form-select" id="client_id" name="client_id" required>
                        <option value="">انتخاب کارفرما</option>
                        <?php if (!empty($clients)): ?>
                            <?php foreach ($clients as $client): ?>
                                <option value="<?php echo htmlspecialchars($client->id); ?>" <?php echo ($isEdit && $invoice->client_id == $client->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($client->name); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="contract_id" class="form-label">قرارداد مرتبط (اختیاری):</label>
                <select class="form-select" id="contract_id" name="contract_id">
                    <option value="">-- بدون قرارداد --</option>
                    <?php if (!empty($contracts)): ?>
                        <?php foreach ($contracts as $contract): ?>
                            <option value="<?php echo htmlspecialchars($contract->id); ?>" <?php echo ($isEdit && isset($invoice->contract_id) && $invoice->contract_id == $contract->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($contract->title); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

             <div class="mb-3">
                <label for="category_id" class="form-label">دسته‌بندی:</label>
                <select class="form-select" id="category_id" name="category_id">
                    <option value="">-- بدون دسته‌بندی --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category->id); ?>" 
                                <?php echo ($isEdit && isset($invoice->category_id) && $invoice->category_id == $category->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="issue_date" class="form-label">تاریخ صدور (شمسی):</label>
                    <input type="text" class="form-control persian-datepicker" id="issue_date" name="issue_date" value="<?php echo isset($issue_date_jalali) ? htmlspecialchars($issue_date_jalali) : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="due_date" class="form-label">تاریخ سررسید (شمسی):</label>
                    <input type="text" class="form-control persian-datepicker" id="due_date" name="due_date" value="<?php echo isset($due_date_jalali) ? htmlspecialchars($due_date_jalali) : ''; ?>">
                </div>
            </div>

            <hr class="my-4">

            <div class="row align-items-center">
                <div class="col-md-4 mb-3">
                    <label for="subtotal" class="form-label">مبلغ خالص (تومان):</label>
                    <input type="number" class="form-control" id="subtotal" name="subtotal" value="<?php echo $isEdit ? htmlspecialchars($invoice->subtotal) : ''; ?>" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="vat_rate" class="form-label">نرخ م.ا.ا (%):</label>
                    <input type="number" class="form-control" id="vat_rate" name="vat_rate" value="9">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">مبلغ ارزش افزوده:</label>
                    <input type="text" class="form-control bg-light" id="vat_amount_display" readonly>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">مبلغ کل قابل پرداخت:</label>
                    <input type="text" class="form-control fw-bold bg-light" id="total_amount_display" readonly>
                </div>
            </div>
             <hr class="my-4">

            <div class="mb-3">
                <label for="description" class="form-label">توضیحات:</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo $isEdit ? htmlspecialchars($invoice->description) : ''; ?></textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">وضعیت فاکتور:</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="pending" <?php echo ($isEdit && $invoice->status == 'pending') ? 'selected' : ''; ?>>در انتظار پرداخت</option>
                    <option value="paid" <?php echo ($isEdit && $invoice->status == 'paid') ? 'selected' : ''; ?>>پرداخت شده</option>
                    <option value="overdue" <?php echo ($isEdit && $invoice->status == 'overdue') ? 'selected' : ''; ?>>سررسید گذشته</option>
                    <option value="canceled" <?php echo ($isEdit && $invoice->status == 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary"><?php echo $buttonText; ?></button>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=invoices" class="btn btn-secondary">بازگشت</a>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const subtotalInput = document.getElementById('subtotal');
    const vatRateInput = document.getElementById('vat_rate');
    const vatDisplay = document.getElementById('vat_amount_display');
    const totalDisplay = document.getElementById('total_amount_display');

    function calculateInvoice() {
        const subtotal = parseFloat(subtotalInput.value) || 0;
        const vatRate = parseFloat(vatRateInput.value) || 0;
        const vatAmount = Math.round((subtotal * vatRate) / 100);
        const totalAmount = subtotal + vatAmount;

        vatDisplay.value = vatAmount.toLocaleString('fa-IR');
        totalDisplay.value = totalAmount.toLocaleString('fa-IR');
    }

    subtotalInput.addEventListener('input', calculateInvoice);
    vatRateInput.addEventListener('input', calculateInvoice);
    
    if (subtotalInput.value) {
        calculateInvoice();
    }
});
</script>