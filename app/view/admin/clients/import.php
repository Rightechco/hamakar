<h1 class="mb-4">ورود مشتریان از اکسل</h1>
<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">آپلود فایل اکسل</h6>
    </div>
    <div class="card-body">
        <p>فایل اکسل شما باید شامل ستون‌های زیر به ترتیب باشد: <strong>نام، نام شرکت، ایمیل، تلفن، آدرس</strong></p>
        <p>ردیف اول به عنوان هدر در نظر گرفته نمی‌شود و از همان ردیف اول، اطلاعات مشتریان وارد می‌شود.</p>
        <hr>
        <form action="index.php?page=admin&action=import_clients" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="excel_file" class="form-label">انتخاب فایل اکسل (.xlsx):</label>
                <input class="form-control" type="file" name="excel_file" id="excel_file" accept=".xlsx" required>
            </div>
            <button type="submit" class="btn btn-primary">شروع ورود اطلاعات</button>
        </form>
    </div>
</div>