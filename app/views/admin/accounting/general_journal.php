<h1 class="mb-4">گزارش دفتر روزنامه</h1>


    <div class="d-flex justify-content-between align-items-center p-3 mb-3 bg-white rounded-3 border shadow-sm flex-wrap gap-2">
        <span class="fw-bold small text-dark"><i class="fas fa-book text-primary me-1"></i>دفتر روزنامه حسابداری</span>
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo APP_URL; ?>/index.php?page=export&action=excel&type=journal" class="btn btn-sm btn-outline-success rounded-3 fw-bold shadow-xs">
                <i class="fas fa-file-excel text-success me-1"></i> خروجی اکسل دفتر روزنامه
            </a>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-3 fw-bold shadow-xs" onclick="HamakarExport.toPdf('main', 'دفتر_روزنامه', 'گزارش رسمی دفتر روزنامه حسابداری')">
                <i class="fas fa-file-pdf text-danger me-1"></i> دانلود PDF
            </button>
            <button type="button" class="btn btn-sm btn-outline-primary rounded-3 fw-bold shadow-xs" onclick="HamakarExport.printOfficial('main', 'گزارش رسمی دفتر روزنامه حسابداری')">
                <i class="fas fa-print text-primary me-1"></i> چاپ رسمی A4
            </button>
        </div>
    </div>

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
                    <button type="submit" class="btn btn-primary w-100">نمایش</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (isset($journalData) && !empty($journalData)): ?>
    <?php foreach($journalData as $voucher): ?>
        <div class="card shadow mb-3">
            <div class="card-header bg-light d-flex justify-content-between">
                <div>
                    <span class="fw-bold">سند شماره: <?php echo $voucher->id; ?></span>
                    <span class="ms-4">تاریخ: <?php echo jdate('Y/m/d', strtotime($voucher->voucher_date)); ?></span>
                </div>
               <div class="d-flex align-items-center">
        <small class="text-muted me-3">شرح: <?php echo htmlspecialchars($voucher->description); ?></small>
         <form action="index.php?page=admin&action=reverse_voucher&id=<?php echo $voucher->id; ?>" method="POST" class="d-inline-block me-1" onsubmit="return confirm('آیا از صدور سند معکوس برای این سند مطمئن هستید؟');">
            <button type="submit" class="btn btn-sm btn-outline-warning py-0 px-1" title="صدور سند معکوس">
                <i class="fas fa-undo fa-xs"></i>
            </button>
        </form>
         
        <form action="index.php?page=admin&action=delete_voucher&id=<?php echo $voucher->id; ?>" method="POST" onsubmit="return confirm('آیا از حذف کامل این سند مطمئن هستید؟ این عملیات غیرقابل بازگشت است.');">
            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1" title="حذف سند">
                <i class="fas fa-trash-alt fa-xs"></i>
            </button>
        </form>
    </div>
</div>
            <div class="card-body p-0">
                <table class="table table-sm table-striped mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50%;">نام حساب</th>
                            <th class="text-center" style="width: 25%;">بدهکار</th>
                            <th class="text-center" style="width: 25%;">بستانکار</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($voucher->entries as $entry): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($entry->account_name . ' (' . $entry->account_code . ')'); ?></td>
                                <td class="text-center"><?php echo $entry->debit > 0 ? number_format($entry->debit) : '-'; ?></td>
                                <td class="text-center"><?php echo $entry->credit > 0 ? number_format($entry->credit) : '-'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
<?php elseif(isset($journalData)): ?>
    <div class="alert alert-info">هیچ سندی در بازه زمانی انتخاب شده یافت نشد.</div>
<?php endif; ?>