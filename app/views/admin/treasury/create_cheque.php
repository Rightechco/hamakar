<?php
// app/views/admin/treasury/create_cheque.php - ثبت چک صیادی
?>
<div class="container py-3" style="max-width:860px;">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-plus-circle text-primary me-2"></i>ثبت سند چک صیادی جدید
            </h5>
            <a href="index.php?page=admin&action=treasury_cheques" class="btn btn-sm btn-outline-secondary rounded-3">
                <i class="fas fa-arrow-right me-1"></i> بازگشت
            </a>
        </div>
        <div class="card-body p-4">
            <form action="index.php?page=admin&action=store_cheque" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">نوع چک:</label>
                        <select name="type" class="form-select rounded-3">
                            <option value="received">چک دریافتی (از مشتری / بدهکاران)</option>
                            <option value="paid">چک پرداختی (به تأمین‌کننده / هزینه‌ها)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">شناسه ۱۶ رقمی صیاد:</label>
                        <input type="text" name="sayad_id" maxlength="16" required class="form-control rounded-3 font-monospace" placeholder="مثال: 1234567890123456" style="direction:ltr;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">شماره سریال چک:</label>
                        <input type="text" name="cheque_number" required class="form-control rounded-3 font-monospace" placeholder="شماره چاپ شده روی برگه چک">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">مبلغ چک (تومان):</label>
                        <input type="number" name="amount" required class="form-control rounded-3 font-monospace" placeholder="مبلغ به عدد">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">بانک صادرکننده:</label>
                        <input type="text" name="bank_name" required class="form-control rounded-3" placeholder="مثال: ملت، ملی، پاسارگاد">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">نام شعبه:</label>
                        <input type="text" name="branch" class="form-control rounded-3" placeholder="نام یا کد شعبه">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">طرف حساب (مشتری):</label>
                        <select name="client_id" class="form-select rounded-3">
                            <option value="">-- انتخاب مشتری / شخص --</option>
                            <?php foreach ($clients as $c): ?>
                                <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">نام صادرکننده / صاحب حساب:</label>
                        <input type="text" name="drawer_name" class="form-control rounded-3" placeholder="نام درج شده روی چک">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">تاریخ صدور:</label>
                        <input type="text" name="issue_date" value="<?php echo date('Y/m/d'); ?>" class="form-control rounded-3 persian-datepicker" placeholder="YYYY/MM/DD">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">تاریخ سررسید چک:</label>
                        <input type="text" name="due_date" required class="form-control rounded-3 persian-datepicker" placeholder="YYYY/MM/DD">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small text-secondary">یادداشت / بابت:</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="بابت فاکتور شماره... یا توضیحات دیگر"></textarea>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-primary rounded-3 px-4 shadow-sm">
                        <i class="fas fa-check me-1"></i> ثبت و ذخیره چک صیادی
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
