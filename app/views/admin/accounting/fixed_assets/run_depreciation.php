<h1 class="mb-4">محاسبه و ثبت سند استهلاک</h1>

<div class="card shadow">
    <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">اجرای عملیات استهلاک دوره‌ای</h6></div>
    <div class="card-body">
        <p>با اجرای این عملیات، سیستم برای تمام دارایی‌های ثابت، هزینه استهلاک را تا تاریخ مشخص شده محاسبه کرده و یک سند حسابداری جامع برای آن صادر می‌کند.</p>
        <p class="text-danger fw-bold">هشدار: این عملیات را فقط در پایان هر دوره مالی (مثلاً پایان هر ماه) اجرا کنید.</p>
        <hr>
        <form action="index.php?page=admin&action=run_depreciation" method="POST" onsubmit="return confirm('آیا از محاسبه و ثبت سند استهلاک تا تاریخ انتخاب شده مطمئن هستید؟');">
            <div class="row align-items-end">
                <div class="col-md-6">
                    <label for="depreciation_date" class="form-label">محاسبه استهلاک تا تاریخ:</label>
                    <input type="text" name="depreciation_date" class="form-control persian-datepicker" required>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success">شروع محاسبه و صدور سند</button>
                </div>
            </div>
        </form>
    </div>
</div>