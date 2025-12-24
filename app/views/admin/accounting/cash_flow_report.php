<h1 class="mb-4">گزارش صورت جریان وجوه نقد</h1>

<div class="card shadow mb-4">
    <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">فیلتر گزارش</h6></div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row align-items-end"><div class="col-md-5"><label class="form-label">از تاریخ:</label><input type="text" name="start_date" class="form-control persian-datepicker" value="<?php echo $startDateJalali ?? ''; ?>" required></div><div class="col-md-5"><label class="form-label">تا تاریخ:</label><input type="text" name="end_date" class="form-control persian-datepicker" value="<?php echo $endDateJalali ?? ''; ?>" required></div><div class="col-md-2"><button type="submit" class="btn btn-primary w-100">نمایش</button></div></div>
        </form>
    </div>
</div>

<?php if (isset($reportData)): ?>
<div class="card shadow">
    <div class="card-header"><h6 class="m-0">جریان وجوه نقد برای دوره <?php echo htmlspecialchars($startDateJalali); ?> تا <?php echo htmlspecialchars($endDateJalali); ?></h6></div>
    <div class="card-body">
        <table class="table table-sm">
            <tr>
                <td class="fw-bold">موجودی نقد اولیه</td>
                <td class="text-end fw-bold"><?php echo number_format($reportData['opening_balance']); ?></td>
            </tr>
        </table>
        <hr>
        <h5 class="text-success">جریان‌های ورودی نقد</h5>
        <table class="table table-sm table-striped">
            <?php foreach($reportData['inflows'] as $tx): ?>
            <tr>
                <td><?php echo jdate('Y/m/d', strtotime($tx->voucher_date)); ?></td>
                <td><?php echo htmlspecialchars($tx->description); ?></td>
                <td class="text-end"><?php echo number_format($tx->debit); ?></td>
            </tr>
            <?php endforeach; ?>
            <tfoot class="table-group-divider"><tr class="text-success"><th colspan="2" class="text-end">جمع ورودی‌ها:</th><th class="text-end"><?php echo number_format($reportData['total_inflow']); ?></th></tr></tfoot>
        </table>
        <hr>
        <h5 class="text-danger">جریان‌های خروجی نقد</h5>
        <table class="table table-sm table-striped">
            <?php foreach($reportData['outflows'] as $tx): ?>
            <tr>
                <td><?php echo jdate('Y/m/d', strtotime($tx->voucher_date)); ?></td>
                <td><?php echo htmlspecialchars($tx->description); ?></td>
                <td class="text-end">(<?php echo number_format($tx->credit); ?>)</td>
            </tr>
            <?php endforeach; ?>
            <tfoot class="table-group-divider"><tr class="text-danger"><th colspan="2" class="text-end">جمع خروجی‌ها:</th><th class="text-end">(<?php echo number_format($reportData['total_outflow']); ?>)</th></tr></tfoot>
        </table>
        <hr>
        <table class="table table-sm bg-light">
            <tr>
                <td class="fw-bold fs-5">خالص جریان وجوه نقد</td>
                <td class="text-end fw-bold fs-5"><?php echo number_format($reportData['net_cash_flow']); ?></td>
            </tr>
            <tr>
                <td class="fw-bold fs-5">موجودی نقد پایانی</td>
                <td class="text-end fw-bold fs-5"><?php echo number_format($reportData['ending_balance']); ?></td>
            </tr>
        </table>
    </div>
</div>
<?php endif; ?>