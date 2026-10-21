<style>
    .fin-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); overflow: hidden; margin-bottom: 1.5rem; }
    .fin-card-header { padding: 1.25rem 1.5rem; background: #ffffff; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
    .fin-card-title { font-weight: 800; color: #0f172a; font-size: 1rem; margin: 0; }
    .btn-fin-primary { background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #fff; border: none; border-radius: 12px; padding: 0.6rem 1.25rem; font-weight: 700; }
    .btn-fin-action { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0.45rem 0.9rem; font-size: 0.825rem; font-weight: 700; transition: all 0.2s; }
    .btn-fin-action:hover { background: #e2e8f0; color: #0f172a; }
    .table-premium { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-premium th { background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.8rem; padding: 1rem; border-bottom: 1px solid #e2e8f0; }
    .table-premium td { padding: 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; color: #1e293b; vertical-align: middle; }
    .progress-premium { height: 10px; border-radius: 10px; background-color: #e2e8f0; overflow: hidden; }

    @media print {
        .no-print, #sidebar, .top-header-bar, #chat-toggler, #chat-window { display: none !important; }
        body { background: white !important; }
        .fin-card { border: 1px solid #ccc !important; box-shadow: none !important; }
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">گزارش مقایسه بودجه و عملکرد واقعی</h1>
        <p class="text-muted small mb-0">تحلیل انحرافات بودجه‌ای و میزان تحقق برنامه‌های مالی</p>
    </div>
    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-print text-indigo-600"></i>
            <span>چاپ گزارش</span>
        </button>
        <button onclick="exportTableToExcel('budget-report-area', 'گزارش-انحراف-بودجه')" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-file-excel text-success"></i>
            <span>خروجی اکسل</span>
        </button>
    </div>
</div>

<div class="fin-card no-print">
    <div class="fin-card-header"><h6 class="fin-card-title"><i class="fas fa-filter text-indigo-500" style="color:#6366f1;"></i> فیلتر گزارش بودجه</h6></div>
    <div class="card-body p-4">
        <form method="POST" action="">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-bold text-secondary small">انتخاب سال:</label>
                    <input type="number" name="year" class="form-control form-control-lg rounded-3 fs-6" value="<?php echo $selectedYear ?? jdate('Y'); ?>" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold text-secondary small">انتخاب ماه:</label>
                    <select name="month" class="form-select form-select-lg rounded-3 fs-6" required>
                        <?php for($m=1; $m<=12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php echo (isset($selectedMonth) && $selectedMonth == $m) ? 'selected' : ''; ?>>
                                <?php echo jdate('F', mktime(0,0,0,$m,1)); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-fin-primary w-100 py-3">نمایش گزارش</button></div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($reportData)): ?>
<div class="fin-card" id="budget-report-area">
    <div class="fin-card-header"><h6 class="fin-card-title">نتیجه گزارش برای: <?php echo jdate('F Y', mktime(0,0,0,$selectedMonth,1,$selectedYear-621)); ?></h6></div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th>حساب هزینه</th>
                        <th class="text-center">مبلغ بودجه</th>
                        <th class="text-center">هزینه واقعی</th>
                        <th class="text-center">واریانس (اختلاف)</th>
                        <th class="text-center" style="width: 200px;">درصد تحقق</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reportData)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">هیچ بودجه‌ای برای این دوره تعریف نشده است.</td></tr>
                    <?php else: ?>
                        <?php foreach($reportData as $row): 
                            $achievement = ($row->budget_amount > 0) ? round(($row->actual_amount / $row->budget_amount) * 100) : 0;
                        ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($row->account_name); ?></td>
                            <td class="text-center font-monospace"><?php echo number_format($row->budget_amount); ?></td>
                            <td class="text-center font-monospace"><?php echo number_format($row->actual_amount); ?></td>
                            <td class="text-center fw-bold font-monospace <?php echo ($row->variance >= 0) ? 'text-success' : 'text-danger'; ?>">
                                <?php echo number_format($row->variance); ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress-premium flex-grow-1">
                                        <div class="progress-bar <?php echo ($achievement <= 100) ? 'bg-success' : 'bg-danger'; ?>" role="progressbar" style="width: <?php echo min($achievement, 100); ?>%;"></div>
                                    </div>
                                    <span class="small fw-bold"><?php echo $achievement; ?>%</span>
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

<script>
function exportTableToExcel(tableID, filename = ''){
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    filename = filename?filename+'.xls':'excel_data.xls';
    downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff' + tableHTML], { type: dataType });
        navigator.msSaveOrOpenBlob(blob, filename);
    }else{
        downloadLink.href = 'data:' + dataType + ', ' + '\ufeff' + tableHTML;
        downloadLink.download = filename;
        downloadLink.click();
    }
}
</script>