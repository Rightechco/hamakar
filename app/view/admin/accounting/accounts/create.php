<h1 class="mb-4">افزودن سرفصل حساب جدید</h1>

<div class="card shadow">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">اطلاعات حساب</h6>
    </div>
    <div class="card-body">
        <form action="index.php?page=admin&action=store_account" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="parent_id" class="form-label">حساب والد (اختیاری):</label>
                    <select class="form-select" id="parent_id" name="parent_id">
                        <option value="">-- حساب اصلی (بدون والد) --</option>
                        <?php foreach ($accounts as $account): ?>
                            <option value="<?php echo $account->id; ?>">
                                <?php echo htmlspecialchars($account->name) . ' (' . htmlspecialchars($account->code) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">نوع حساب:</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="asset">دارایی</option>
                        <option value="liability">بدهی</option>
                        <option value="equity">حقوق صاحبان سهام</option>
                        <option value="income">درآمد</option>
                        <option value="expense">هزینه</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label">کد حساب:</label>
                    <input type="text" class="form-control" id="code" name="code" placeholder="مثال: 1101" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">نام حساب:</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="مثال: موجودی نقد و بانک" required>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">ذخیره حساب</button>
            <a href="index.php?page=admin&action=accounting_accounts" class="btn btn-secondary">بازگشت</a>
        </form>
    </div>
</div>