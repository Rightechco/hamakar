<h1 class="mb-4">گزارش آنالیز هزینه‌ها</h1>

<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">فیلتر گزارش</h6>
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
                    <button type="submit" class="btn btn-primary w-100">نمایش گزارش</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($reportData)): ?>
<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">تجزیه و تحلیل هزینه‌ها برای دوره <?php echo htmlspecialchars($startDateJalali); ?> تا <?php echo htmlspecialchars($endDateJalali); ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>شرح هزینه</th>
                        <th class="text-center">مبلغ کل هزینه (تومان)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reportData)): ?>
                        <tr><td colspan="2" class="text-center">هیچ هزینه‌ای در این دوره یافت نشد.</td></tr>
                    <?php else: ?>
                        <?php 
                            $totalExpenses = 0;
                            foreach($reportData as $row): 
                            $totalExpenses += $row->total_expense;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row->account_name); ?></td>
                            <td class="text-center"><?php echo number_format($row->total_expense); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($reportData)): ?>
                <tfoot class="table-group-divider">
                    <tr class="fw-bold fs-5">
                        <td class="text-end">جمع کل هزینه‌ها:</td>
                        <td class="text-center"><?php echo number_format($totalExpenses); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>