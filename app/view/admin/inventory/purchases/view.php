<?php
// app/views/admin/inventory/purchases/view.php
global $auth;
$purchase = $purchase ?? null;
$items = $items ?? [];
$companyInfo = $companyInfo ?? [];

if (!$purchase) {
    echo '<div class="alert alert-danger">فاکتور خرید مورد نظر یافت نشد.</div>';
    return;
}

$purchaseDateJalali = jdate('Y/m/d', strtotime($purchase->purchase_date));
$totalAmount = $purchase->total_amount;
?>

<style>
    @font-face {
        font-family: 'B Homa';
        src: url('<?php echo APP_URL; ?>/assets/fonts/BHoma.eot');
        src: url('<?php echo APP_URL; ?>/assets/fonts/BHoma.eot?#iefix') format('embedded-opentype'),
        url('<?php echo APP_URL; ?>/assets/fonts/BHoma.woff2') format('woff2'),
        url('<?php echo APP_URL; ?>/assets/fonts/BHoma.woff') format('woff'),
        url('<?php echo APP_URL; ?>/assets/fonts/BHoma.ttf') format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    .invoice-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 30px;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-family: Tahoma, sans-serif;
    }
    .invoice-header-box {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .invoice-header-box img {
        max-width: 120px;
    }
    .invoice-table th, .invoice-table td {
        text-align: center;
        vertical-align: middle;
        font-size: 14px;
        padding: 8px;
    }
    .invoice-table th {
        background-color: #34495e;
        color: #fff;
    }
    .invoice-summary td {
        background-color: #f1f1f1;
        font-weight: bold;
    }
    .invoice-signature {
        margin-top: 50px;
    }
    .status-badge {
        font-size: 1rem;
        padding: 0.5em 1em;
        border-radius: 20px;
    }
</style>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 text-gray-800">پیش‌نمایش فاکتور خرید</h1>
    <a href="<?php echo APP_URL; ?>/index.php?page=purchases&action=index" class="btn btn-secondary">بازگشت به لیست</a>
</div>

<div class="invoice-container shadow">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <span class="status-badge bg-primary text-white">وضعیت: <?php echo htmlspecialchars($purchase->status ?? 'نامشخص'); ?></span>
        <form action="<?php echo APP_URL; ?>/index.php?page=purchases&action=updateStatus&id=<?php echo htmlspecialchars($purchase->id ?? ''); ?>" method="POST" class="d-flex align-items-center">
            <label for="status-select" class="form-label mb-0 me-2">تغییر وضعیت:</label>
            <select name="status" id="status-select" class="form-select form-select-sm me-2">
                <option value="pending" <?php echo ($purchase->status === 'pending') ? 'selected' : ''; ?>>در انتظار</option>
                <option value="paid" <?php echo ($purchase->status === 'paid') ? 'selected' : ''; ?>>پرداخت شده</option>
                <option value="canceled" <?php echo ($purchase->status === 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">ذخیره</button>
        </form>
    </div>

    <div class="row text-center mb-4">
        <div class="col-12">
            <h2 style="font-family: 'B Homa', Tahoma, sans-serif; font-weight: bold;">فاکتور خرید</h2>
        </div>
    </div>
    
    <div class="row invoice-header-box">
        <div class="col-6 text-start">
            <img src="<?php echo APP_URL; ?>/assets/img/mohesen-logo.webp" alt="Logo" class="img-fluid">
        </div>
        <div class="col-6 text-end">
            <p class="mb-0"><strong>شماره:</strong> <?php echo htmlspecialchars($purchase->id ?? '---'); ?></p>
            <p class="mb-0"><strong>تاریخ:</strong> <?php echo $purchaseDateJalali; ?></p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <h6>** اطلاعات فروشنده **</h6>
            <p class="mb-1"><strong>نام:</strong> <?php echo htmlspecialchars($purchase->vendor_name ?? '---'); ?></p>
            <p class="mb-1"><strong>کد اقتصادی:</strong> ---</p>
            <p class="mb-1"><strong>آدرس:</strong> ---</p>
            <p class="mb-1"><strong>تلفن:</strong> ---</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered invoice-table" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>ردیف</th>
                    <th>شرح کالا</th>
                    <th>تعداد</th>
                    <th>واحد</th>
                    <th>قیمت واحد</th>
                    <th>قیمت کل</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo htmlspecialchars($item->product_name ?? '---'); ?></td>
                        <td><?php echo number_format($item->quantity ?? 0); ?></td>
                        <td><?php echo htmlspecialchars($item->product_unit ?? 'عدد'); ?></td>
                        <td><?php echo number_format($item->price ?? 0); ?></td>
                        <td><?php echo number_format($item->quantity * $item->price); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="invoice-summary">
                    <td colspan="5" class="text-end fw-bold">جمع کل فاکتور (تومان):</td>
                    <td><?php echo number_format($totalAmount); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="row invoice-signature mt-5">
        <div class="col-6 text-center">
            <p><strong>امضاء خریدار</strong></p>
        </div>
        <div class="col-6 text-center">
            <p><strong>امضاء فروشنده</strong></p>
        </div>
    </div>
</div>