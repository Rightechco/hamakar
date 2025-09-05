<h1 class="mb-4">مدیریت هزینه‌ها</h1>
<div class="row">
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">ثبت هزینه جدید</h6></div>
            <div class="card-body">
                <form action="index.php?page=admin&action=store_expense" method="post">
                    <div class="mb-2">
                        <label class="form-label">تاریخ</label>
                        <input type="text" name="expense_date" class="form-control persian-datepicker" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">مبلغ (تومان)</label>
                        <input type="number" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">شرح هزینه</label>
                        <input type="text" name="description" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">سرفصل هزینه</label>
                        <select name="expense_account_id" class="form-select" required>
                            </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">پرداخت از</label>
                        <select name="payment_account_id" class="form-select" required>
                            </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">ثبت هزینه</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header"><h6 class="m-0 font-weight-bold text-primary">لیست هزینه‌های ثبت‌شده</h6></div>
            <div class="card-body">
                <table class="table">
                    </table>
            </div>
        </div>
    </div>
</div>