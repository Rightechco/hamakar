<?php
// app/views/admin/inventory/sales/view.php
global $auth;
$sale = $sale ?? null;
$items = $items ?? [];
$companyInfo = $companyInfo ?? [];

if (!$sale) {
    echo '<div class="alert alert-danger border-0 shadow-sm rounded-3">فاکتور فروش مورد نظر یافت نشد.</div>';
    return;
}

$saleDateJalali = jdate('Y/m/d', strtotime($sale->sale_date));
$subtotal = $sale->subtotal;
$vatAmount = $sale->vat_amount;
$totalAmount = $sale->total_amount;
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .invoice-card {
        max-width: 880px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        padding: 40px;
        direction: rtl;
        font-family: 'Vazirmatn', sans-serif;
    }

    .invoice-brand-header {
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 25px;
        margin-bottom: 25px;
    }

    .info-box-ultra {
        background: #f8fafc;
        border-radius: 12px;
        padding: 18px;
        border: 1px solid #f1f5f9;
        height: 100%;
    }

    .invoice-table-ultra th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 16px;
    }

    .invoice-table-ultra td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    @media print {
        body * { visibility: hidden; }
        .invoice-card, .invoice-card * { visibility: visible; }
        .invoice-card { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
        .no-print { display: none !important; }
    }
</style>

<div class="py-3">
    <!-- Action Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print mx-auto" style="max-width: 880px;">
        <a href="<?php echo APP_URL; ?>/index.php?page=sales&action=index" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-right me-1"></i> بازگشت
        </a>

        <div class="d-flex align-items-center gap-3">
            <button onclick="window.print()" class="btn btn-light border rounded-pill px-3">
                <i class="fas fa-print me-1"></i> چاپ فاکتور
            </button>
            <form action="<?php echo APP_URL; ?>/index.php?page=sales&action=updateStatus&id=<?php echo htmlspecialchars($sale->id ?? ''); ?>" method="POST" class="d-flex align-items-center">
                <select name="status" class="form-select form-select-sm rounded-2 me-2">
                    <option value="pending" <?php echo ($sale->status === 'pending') ? 'selected' : ''; ?>>در انتظار</option>
                    <option value="completed" <?php echo ($sale->status === 'completed') ? 'selected' : ''; ?>>تکمیل شده</option>
                    <option value="canceled" <?php echo ($sale->status === 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
                </select>
                <button type="submit" class="btn btn-sm btn-success rounded-2 px-3">ذخیره</button>
            </form>
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="invoice-card">
        <div class="invoice-brand-header d-flex justify-content-between align-items-center">
            <div>
                <img src="<?php echo APP_URL; ?>/assets/img/mohesen-logo.webp" alt="Logo" style="max-height: 50px;">
                <h4 class="fw-bold mt-2 mb-0 text-dark">فاکتور رسمی فروش کالا</h4>
            </div>
            <div class="text-end text-muted">
                <div class="mb-1">شماره فاکتور: <strong class="text-dark">#<?php echo htmlspecialchars($sale->id ?? '---'); ?></strong></div>
                <div>تاریخ صدور: <strong class="text-dark"><?php echo $saleDateJalali; ?></strong></div>
            </div>
        </div>

        <!-- Vendor & Client Information -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="info-box-ultra">
                    <h6 class="fw-bold mb-3 text-success"><i class="fas fa-building me-2"></i>فروشنده</h6>
                    <div class="small lh-lg text-muted">
                        <div>نام مجموعه: <strong class="text-dark">نرم‌افزاری محسن</strong></div>
                        <div>کد اقتصادی: <strong class="text-dark">۴۴۵۵۲۲۴۴</strong></div>
                        <div>تلفن: <strong class="text-dark">۴۴۵۵۲۲۴۴</strong></div>
                        <div>نشانی: <strong class="text-dark">تهران خیابان ولیعصر (عج)</strong></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box-ultra">
                    <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-user me-2"></i>خریدار</h6>
                    <div class="small lh-lg text-muted">
                        <div>نام مشتری: <strong class="text-dark"><?php echo htmlspecialchars($sale->client_name ?? '---'); ?></strong></div>
                        <div>شماره تماس: <strong class="text-dark"><?php echo htmlspecialchars($sale->client_phone ?? '---'); ?></strong></div>
                        <div>نشانی تحویل: <strong class="text-dark"><?php echo htmlspecialchars($sale->client_address ?? '---'); ?></strong></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Table -->
        <div class="table-responsive mb-4">
            <table class="table invoice-table-ultra align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>شرح کالا / خدمات</th>
                        <th class="text-center">تعداد</th>
                        <th class="text-center">واحد</th>
                        <th class="text-end">قیمت واحد (تومان)</th>
                        <th class="text-end">جمع کل (تومان)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1; ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="text-center text-muted"><?php echo $counter++; ?></td>
                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($item->product_name ?? '---'); ?></td>
                            <td class="text-center"><?php echo number_format($item->quantity ?? 0); ?></td>
                            <td class="text-center text-muted"><?php echo htmlspecialchars($item->product_unit ?? 'عدد'); ?></td>
                            <td class="text-end"><?php echo number_format($item->price ?? 0); ?></td>
                            <td class="text-end fw-bold text-dark"><?php echo number_format($item->quantity * $item->price); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Summary Totals -->
        <div class="row justify-content-end">
            <div class="col-md-6">
                <div class="bg-light p-3 rounded-3">
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>جمع جزء (Subtotal):</span>
                        <span class="fw-medium text-dark"><?php echo number_format($subtotal); ?> تومان</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>مالیات بر ارزش افزوده (VAT <?php echo $sale->vat_rate; ?>%):</span>
                        <span class="fw-medium text-dark"><?php echo number_format($vatAmount); ?> تومان</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2 text-success fs-5">
                        <span>جمع کل فاکتور:</span>
                        <span><?php echo number_format($totalAmount); ?> تومان</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="row text-center mt-5 pt-4 text-muted">
            <div class="col-6">
                <p class="mb-4">مهر و امضاء خریدار</p>
                <div style="border-bottom: 1px dashed #cbd5e1; width: 60%; margin: 0 auto;"></div>
            </div>
            <div class="col-6">
                <p class="mb-4">مهر و امضاء فروشنده</p>
                <div style="border-bottom: 1px dashed #cbd5e1; width: 60%; margin: 0 auto;"></div>
            </div>
        </div>
    </div>
</div>