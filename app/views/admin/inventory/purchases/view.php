<?php
// app/views/admin/inventory/purchases/view.php
global $auth;
$purchase = $purchase ?? null;
$items = $items ?? [];

if (!$purchase) {
    echo '<div class="alert alert-danger border-0 shadow-sm rounded-3">فاکتور خرید مورد نظر یافت نشد.</div>';
    return;
}

$purchaseDateJalali = jdate('Y/m/d', strtotime($purchase->purchase_date));
$subtotal = $purchase->subtotal;
$vatAmount = $purchase->vat_amount;
$totalAmount = $purchase->total_amount;
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">

<style>
    .invoice-card {
        max-width: 850px;
        margin: 0 auto;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        padding: 40px;
    }

    .invoice-brand-header {
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 25px;
        margin-bottom: 25px;
    }

    .vendor-info-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #f1f5f9;
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
    <!-- Top Bar Controls -->
    <div class="d-flex justify-content-between align-items-center mb-4 no-print max-w-850 mx-auto" style="max-width: 850px;">
        <a href="<?php echo APP_URL; ?>/index.php?page=purchases&action=index" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fas fa-arrow-right me-1"></i> بازگشت
        </a>

        <div class="d-flex align-items-center gap-3">
            <button onclick="window.print()" class="btn btn-light border rounded-pill px-3">
                <i class="fas fa-print me-1"></i> چاپ فاکتور
            </button>
            <form action="<?php echo APP_URL; ?>/index.php?page=purchases&action=updateStatus&id=<?php echo htmlspecialchars($purchase->id ?? ''); ?>" method="POST" class="d-flex align-items-center">
                <select name="status" class="form-select form-select-sm rounded-2 me-2">
                    <option value="pending" <?php echo ($purchase->status === 'pending') ? 'selected' : ''; ?>>در انتظار</option>
                    <option value="paid" <?php echo ($purchase->status === 'paid') ? 'selected' : ''; ?>>پرداخت شده</option>
                    <option value="canceled" <?php echo ($purchase->status === 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary rounded-2 px-3">به‌روزرسانی</button>
            </form>
        </div>
    </div>

    <!-- Official Invoice Body -->
    <div class="invoice-card">
        <div class="invoice-brand-header d-flex justify-content-between align-items-center">
            <div>
                <img src="<?php echo APP_URL; ?>/assets/img/mohesen-logo.webp" alt="Logo" style="max-height: 50px;">
                <h4 class="fw-bold mt-2 mb-0">فاکتور خرید کالا</h4>
            </div>
            <div class="text-end text-muted">
                <div class="mb-1">شماره فاکتور: <strong class="text-dark">#<?php echo htmlspecialchars($purchase->id ?? '---'); ?></strong></div>
                <div>تاریخ ثبت: <strong class="text-dark"><?php echo $purchaseDateJalali; ?></strong></div>
            </div>
        </div>

        <!-- Vendor & Buyer Details -->
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <div class="vendor-info-box">
                    <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-store me-2"></i>اطلاعات فروشنده</h6>
                    <div class="row g-2 text-muted small">
                        <div class="col-md-6">نام فروشنده / شرکت: <strong class="text-dark"><?php echo htmlspecialchars($purchase->vendor_name ?? '---'); ?></strong></div>
                        <div class="col-md-6">کد اقتصادی: <strong class="text-dark">---</strong></div>
                        <div class="col-md-6">تلفن تماس: <strong class="text-dark">---</strong></div>
                        <div class="col-md-6">نشانی: <strong class="text-dark">---</strong></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
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
                            <td class="fw-medium"><?php echo htmlspecialchars($item->product_name ?? '---'); ?></td>
                            <td class="text-center"><?php echo number_format($item->quantity ?? 0); ?></td>
                            <td class="text-center text-muted"><?php echo htmlspecialchars($item->product_unit ?? 'عدد'); ?></td>
                            <td class="text-end"><?php echo number_format($item->price ?? 0); ?></td>
                            <td class="text-end fw-bold"><?php echo number_format($item->quantity * $item->price); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Summary Calculation Box -->
        <div class="row justify-content-end">
            <div class="col-md-6">
                <div class="bg-light p-3 rounded-3">
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>جمع جزء (Subtotal):</span>
                        <span class="fw-medium text-dark"><?php echo number_format($subtotal); ?> تومان</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>مالیات بر ارزش افزوده (VAT <?php echo $purchase->vat_rate; ?>%):</span>
                        <span class="fw-medium text-dark"><?php echo number_format($vatAmount); ?> تومان</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold border-top pt-2 mt-2 text-primary fs-5">
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