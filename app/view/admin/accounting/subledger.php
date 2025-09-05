<h1 class="mb-4">گزارش دفتر تفصیلی</h1>

<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">فیلتر گزارش</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label">انتخاب حساب کل:</label>
                    <select name="account_id" class="form-select" required>
                        <option value="">یک حساب را انتخاب کنید...</option>
                        <?php foreach($allAccounts as $account): ?>
                            <option value="<?php echo $account->id; ?>" <?php echo (isset($selectedAccountId) && $selectedAccountId == $account->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($account->name . ' (' . $account->code . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">انتخاب مشتری:</label>
                    <select name="entity_id" class="form-select" required>
                        <option value="">یک مشتری را انتخاب کنید...</option>
                        <?php foreach($allClients as $client): ?>
                            <option value="<?php echo $client->id; ?>" <?php echo (isset($selectedEntityId) && $selectedEntityId == $client->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($client->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">از تاریخ:</label>
                    <input type="text" name="start_date" class="form-control persian-datepicker" value="<?php echo $startDateJalali ?? ''; ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">تا تاریخ:</label>
                    <input type="text" name="end_date" class="form-control persian-datepicker" value="<?php echo $endDateJalali ?? ''; ?>" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">نمایش</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($ledgerData)): ?>
<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">گردش حساب تفصیلی: <?php echo htmlspecialchars($selectedAccountName . ' - ' . $selectedEntityName); ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>تاریخ</th>
                        <th>شرح</th>
                        <th class="text-center">بدهکار</th>
                        <th class="text-center">بستانکار</th>
                        <th class="text-center">مانده</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4" class="fw-bold">مانده از قبل</td>
                        <td class="text-center fw-bold"><?php echo number_format($ledgerData['opening_balance']); ?></td>
                    </tr>
                    <?php 
                        $runningBalance = $ledgerData['opening_balance'];
                        foreach ($ledgerData['transactions'] as $tx): 
                        $runningBalance += ($tx->debit - $tx->credit);
                    ?>
                    <tr>
                        <td><?php echo jdate('Y/m/d', strtotime($tx->voucher_date)); ?></td>
                        <td><?php echo htmlspecialchars($tx->description); ?></td>
                        <td class="text-center text-success"><?php echo $tx->debit > 0 ? number_format($tx->debit) : '-'; ?></td>
                        <td class="text-center text-danger"><?php echo $tx->credit > 0 ? number_format($tx->credit) : '-'; ?></td>
                        <td class="text-center fw-bold"><?php echo number_format($runningBalance); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-group-divider">
                    <tr>
                        <th colspan="4" class="text-end">مانده نهایی</th>
                        <th class="text-center"><?php echo number_format($runningBalance); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>