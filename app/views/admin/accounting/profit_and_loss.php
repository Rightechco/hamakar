<?php // app/views/admin/accounting/profit_and_loss.php ?>

<h1 class="h3 mb-4 text-gray-800"><?php echo sanitize($title); ?></h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">گزارش سود و زیان</h6>
    </div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=profitAndLossReport" method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-5">
                    <label for="start_date">از تاریخ:</label>
                    <input type="text" id="start_date" name="start_date" class="form-control persian-datepicker" value="<?php echo htmlspecialchars($startDateJalali ?? ''); ?>" required>
                </div>
                <div class="col-md-5">
                    <label for="end_date">تا تاریخ:</label>
                    <input type="text" id="end_date" name="end_date" class="form-control persian-datepicker" value="<?php echo htmlspecialchars($endDateJalali ?? ''); ?>" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">نمایش گزارش</button>
                </div>
            </div>
        </form>

        <?php if (isset($reportData)): ?>
            <hr>
            <div class="table-responsive mt-4">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th colspan="2">درآمدها</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totalIncome = 0; ?>
                        <?php if (!empty($reportData['incomes'])): ?>
                            <?php foreach ($reportData['incomes'] as $income): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($income->name); ?></td>
                                    <td class="text-start"><?php echo number_format($income->balance); ?></td>
                                </tr>
                                <?php $totalIncome += $income->balance; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted">هیچ درآمدی در این دوره ثبت نشده است.</td>
                            </tr>
                        <?php endif; ?>
                        <tr class="table-success">
                            <td class="fw-bold">کل درآمدها</td>
                            <td class="text-start fw-bold"><?php echo number_format($totalIncome); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th colspan="2">بهای تمام‌شده کالای فروش‌رفته (COGS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-danger">
                            <td class="fw-bold">COGS</td>
                            <td class="text-start fw-bold"><?php echo number_format($reportData['totalCogs'] ?? 0); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th colspan="2">سود ناخالص</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-info">
                            <td class="fw-bold">سود ناخالص</td>
                            <td class="text-start fw-bold"><?php echo number_format($reportData['grossProfit']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th colspan="2">هزینه‌های عملیاتی</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $totalExpenses = 0; ?>
                        <?php if (!empty($reportData['expenses'])): ?>
                            <?php foreach ($reportData['expenses'] as $expense): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($expense->name); ?></td>
                                    <td class="text-start"><?php echo number_format($expense->balance); ?></td>
                                </tr>
                                <?php $totalExpenses += $expense->balance; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted">هیچ هزینه‌ای در این دوره ثبت نشده است.</td>
                            </tr>
                        <?php endif; ?>
                        <tr class="table-danger">
                            <td class="fw-bold">کل هزینه‌ها</td>
                            <td class="text-start fw-bold"><?php echo number_format($totalExpenses); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="table-responsive mt-4">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th colspan="2">سود خالص</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-primary">
                            <td class="fw-bold">سود خالص</td>
                            <td class="text-start fw-bold"><?php echo number_format($reportData['netProfit']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">لطفاً بازه زمانی را انتخاب کرده و دکمه "نمایش گزارش" را بزنید.</div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof persianDatepicker === 'function') {
            new persianDatepicker('input.persian-datepicker', {
                format: 'YYYY/MM/DD'
            });
        }
    });
</script>