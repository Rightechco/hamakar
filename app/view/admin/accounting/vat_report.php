<h1 class="mb-4">گزارش مالیات بر ارزش افزوده (VAT)</h1>

<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">انتخاب دوره گزارش</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label class="form-label">از تاریخ:</label>
                    <input type="text" name="start_date" class="form-control persian-datepicker" value="<?php echo $startDateJalali ?? ''; ?>" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">تا تاریخ:</label>
                    <input type="text" name="end_date" class="form-control persian-datepicker" value="<?php echo $endDateJalali ?? ''; ?>" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">تهیه گزارش</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($reportData)): ?>
<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">خلاصه گزارش VAT برای دوره <?php echo htmlspecialchars($startDateJalali); ?> تا <?php echo htmlspecialchars($endDateJalali); ?></h6>
    </div>
    <div class="card-body">
        <div class="list-group">
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>کل مالیات فروش (بستانکاری مالیاتی)</span>
                <span class="badge bg-primary rounded-pill fs-6"><?php echo number_format($reportData['sales_vat']); ?> تومان</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>کل مالیات خرید (اعتبار مالیاتی)</span>
                <span class="badge bg-info rounded-pill fs-6">(<?php echo number_format($reportData['purchase_vat']); ?>) تومان</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center active">
                <strong class="fs-5">مبلغ خالص قابل پرداخت / استرداد</strong>
                <strong class="fs-5">
                    <?php 
                        $payable_vat = $reportData['payable_vat'];
                        echo number_format(abs($payable_vat));
                        echo $payable_vat >= 0 ? ' (قابل پرداخت)' : ' (قابل استرداد)';
                    ?>
                </strong>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>