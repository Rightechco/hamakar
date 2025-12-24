<h1 class="mb-4">گزارش مقایسه بودجه و عملکرد واقعی</h1>

<div class="card shadow mb-4">
    <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">فیلتر گزارش</h6></div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label class="form-label">انتخاب سال:</label>
                    <input type="number" name="year" class="form-control" value="<?php echo $selectedYear ?? jdate('Y'); ?>" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">انتخاب ماه:</label>
                    <select name="month" class="form-select" required>
                        <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo (isset($selectedMonth) && $selectedMonth == $m) ? 'selected' : ''; ?>>
                                <?php echo jdate('F', mktime(0,0,0,$m,1)); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
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
        <h6 class="m-0 font-weight-bold text-primary">نتیجه گزارش برای: <?php echo jdate('F Y', mktime(0,0,0,$selectedMonth,1,$selectedYear-621)); ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>حساب هزینه</th>
                        <th class="text-center">مبلغ بودجه</th>
                        <th class="text-center">هزینه واقعی</th>
                        <th class="text-center">واریانس (اختلاف)</th>
                        <th class="text-center">درصد تحقق</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reportData)): ?>
                        <tr><td colspan="5" class="text-center">هیچ بودجه‌ای برای این دوره تعریف نشده است.</td></tr>
                    <?php else: ?>
                        <?php foreach($reportData as $row): 
                            $achievement = ($row->budget_amount > 0) ? round(($row->actual_amount / $row->budget_amount) * 100) : 0;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row->account_name); ?></td>
                            <td class="text-center"><?php echo number_format($row->budget_amount); ?></td>
                            <td class="text-center"><?php echo number_format($row->actual_amount); ?></td>
                            <td class="text-center fw-bold <?php echo ($row->variance >= 0) ? 'text-success' : 'text-danger'; ?>">
                                <?php echo number_format($row->variance); ?>
                            </td>
                            <td class="text-center">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar <?php echo ($achievement <= 100) ? 'bg-success' : 'bg-danger'; ?>" role="progressbar" style="width: <?php echo min($achievement, 100); ?>%;" aria-valuenow="<?php echo $achievement; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $achievement; ?>%</div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>