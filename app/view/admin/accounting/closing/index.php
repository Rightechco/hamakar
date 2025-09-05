<h1 class="mb-4">بستن سال مالی</h1>
<div class="card border-danger shadow">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">هشدار: عملیات غیرقابل بازگشت</h5>
    </div>
    <div class="card-body">
        <p>عملیات بستن سال مالی، تمام حساب‌های درآمد و هزینه را صفر کرده و سود یا زیان دوره را به حساب سود انباشته منتقل می‌کند. این عملیات تنها باید در **پایان دوره مالی** انجام شود و **غیرقابل بازگشت** است.</p>
        <p>لطفاً قبل از ادامه، از اطلاعات خود نسخه پشتیبان تهیه کنید.</p>
        <hr>
        <form action="" method="POST" onsubmit="return confirm('آیا از انجام عملیات بستن سال مالی اطمینان کامل دارید؟ این عمل قابل بازگشت نیست.');">
            <div class="mb-3">
                <label for="end_date" class="form-label">تاریخ پایان سال مالی جهت بستن حساب‌ها:</label>
                <input type="text" name="end_date" class="form-control persian-datepicker" required>
            </div>
            <button type="submit" class="btn btn-danger">اجرای عملیات بستن سال مالی</button>
        </form>
    </div>
</div>