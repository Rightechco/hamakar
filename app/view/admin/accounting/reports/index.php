<h1 class="mb-4">گزارشات مالی</h1>

<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">انتخاب دوره گزارش</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label">از تاریخ:</label>
                    <input type="text" name="start_date" class="form-control persian-datepicker" value="<?php echo $startDateJalali ?? ''; ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">تا تاریخ:</label>
                    <input type="text" name="end_date" class="form-control persian-datepicker" value="<?php echo $endDateJalali ?? ''; ?>" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">تهیه گزارش</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($trialBalance)): // این بخش تنها زمانی نمایش داده می‌شود که فرم ارسال شده باشد ?>

<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">تراز آزمایشی</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>کد حساب</th>
                        <th>نام حساب</th>
                        <th>جمع بدهکار</th>
                        <th>جمع بستانکار</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $totalDebit = 0; $totalCredit = 0;
                        foreach ($trialBalance as $row): 
                        $totalDebit += $row->total_debit;
                        $totalCredit += $row->total_credit;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row->code); ?></td>
                        <td><?php echo htmlspecialchars($row->name); ?></td>
                        <td><?php echo number_format($row->total_debit, 0); ?></td>
                        <td><?php echo number_format($row->total_credit, 0); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-group-divider">
                    <tr>
                        <th colspan="2" class="text-end">جمع کل</th>
                        <th><?php echo number_format($totalDebit, 0); ?></th>
                        <th><?php echo number_format($totalCredit, 0); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php if (isset($profitAndLoss)): ?>
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">صورت سود و زیان</h6>
        <small>برای دوره <?php echo htmlspecialchars($startDateJalali); ?> تا <?php echo htmlspecialchars($endDateJalali); ?></small>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5>درآمدها</h5>
                <hr>
                <table class="table table-sm">
                    <tbody><?php foreach ($profitAndLoss['incomes'] as $income): ?><tr><td><?php echo htmlspecialchars($income->name); ?></td><td class="text-end"><?php echo number_format($income->balance, 0); ?></td></tr><?php endforeach; ?></tbody>
                    <tfoot><tr class="table-group-divider"><th class="text-end">جمع درآمدها:</th><th class="text-end"><?php echo number_format($profitAndLoss['totalIncome'], 0); ?></th></tr></tfoot>
                </table>
            </div>
            <div class="col-md-6">
                <h5>هزینه‌ها</h5>
                <hr>
                <table class="table table-sm">
                    <tbody><?php foreach ($profitAndLoss['expenses'] as $expense): ?><tr><td><?php echo htmlspecialchars($expense->name); ?></td><td class="text-end">(<?php echo number_format($expense->balance, 0); ?>)</td></tr><?php endforeach; ?></tbody>
                    <tfoot><tr class="table-group-divider"><th class="text-end">جمع هزینه‌ها:</th><th class="text-end">(<?php echo number_format($profitAndLoss['totalExpenses'], 0); ?>)</th></tr></tfoot>
                </table>
            </div>
        </div>
        <hr class="my-4">
        <div class="d-flex justify-content-center">
            <?php $netProfit = $profitAndLoss['netProfit']; $isProfit = $netProfit >= 0; ?>
            <div class="p-3 rounded text-white <?php echo $isProfit ? 'bg-success' : 'bg-danger'; ?>">
                <h5 class="mb-0"><?php echo $isProfit ? 'سود خالص: ' : 'زیان خالص: '; ?><?php echo number_format(abs($netProfit), 0); ?></h5>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if (isset($balanceSheet)): ?>
<div class="card shadow mt-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">ترازنامه</h6>
        <small>در تاریخ <?php echo htmlspecialchars($endDateJalali); ?></small>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="text-center">دارایی‌ها</h5>
                <hr>
                <table class="table table-sm">
                    <?php foreach($balanceSheet['assets'] as $asset): ?><tr><td><?php echo htmlspecialchars($asset->name); ?></td><td class="text-end"><?php echo number_format($asset->balance, 0); ?></td></tr><?php endforeach; ?>
                </table>
                <hr>
                <div class="d-flex justify-content-between fw-bold p-2 bg-light">
                    <span>جمع دارایی‌ها:</span>
                    <span><?php echo number_format($balanceSheet['totalAssets'], 0); ?></span>
                </div>
            </div>
            <div class="col-md-6">
                <h5 class="text-center">بدهی‌ها و حقوق صاحبان سهام</h5>
                <hr>
                <h6>بدهی‌ها</h6>
                <table class="table table-sm">
                     <?php foreach($balanceSheet['liabilities'] as $liability): ?><tr><td><?php echo htmlspecialchars($liability->name); ?></td><td class="text-end"><?php echo number_format($liability->balance, 0); ?></td></tr><?php endforeach; ?>
                    <tr class="table-group-divider"><th class="text-end">جمع بدهی‌ها:</th><th class="text-end"><?php echo number_format($balanceSheet['totalLiabilities'], 0); ?></th></tr>
                </table>
                <h6 class="mt-4">حقوق صاحبان سهام</h6>
                <table class="table table-sm">
                    <?php foreach($balanceSheet['equity'] as $eq): ?><tr><td><?php echo htmlspecialchars($eq->name); ?></td><td class="text-end"><?php echo number_format($eq->balance, 0); ?></td></tr><?php endforeach; ?>
                    <tr><td>سود (زیان) انباشته دوره</td><td class="text-end"><?php echo number_format($balanceSheet['netProfitForPeriod'], 0); ?></td></tr>
                     <tr class="table-group-divider">
                        <th class="text-end">جمع حقوق صاحبان سهام:</th>
                        <?php $totalEquityAndProfit = $balanceSheet['totalEquity'] + $balanceSheet['netProfitForPeriod']; ?>
                        <th class="text-end"><?php echo number_format($totalEquityAndProfit, 0); ?></th>
                    </tr>
                </table>
                <hr>
                <?php $totalLiabilitiesAndEquity = $balanceSheet['totalLiabilities'] + $totalEquityAndProfit; ?>
                <div class="d-flex justify-content-between fw-bold p-2 <?php echo (round($balanceSheet['totalAssets']) == round($totalLiabilitiesAndEquity)) ? 'bg-success-subtle' : 'bg-danger-subtle'; ?>">
                    <span>جمع بدهی‌ها و حقوق صاحبان سهام:</span>
                    <span><?php echo number_format($totalLiabilitiesAndEquity, 0); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>