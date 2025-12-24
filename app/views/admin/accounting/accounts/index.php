<?php
// تابعی برای نمایش درختی حساب‌ها
function display_accounts_tree($accounts, $level = 0) {
    foreach ($accounts as $account) {
        echo '<tr>';
        echo '<td>' . str_repeat('&mdash; ', $level) . htmlspecialchars($account->code) . '</td>';
        echo '<td>' . htmlspecialchars($account->name) . '</td>';
        echo '<td>' . htmlspecialchars($account->type) . '</td>';
        echo '<td><a href="#" class="btn btn-sm btn-info">ویرایش</a></td>';
        echo '</tr>';
        if (!empty($account->children)) {
            display_accounts_tree($account->children, $level + 1);
        }
    }
}
?>

<h1 class="mb-4">کدینگ و سرفصل‌های حسابداری</h1>
<div class="card shadow">
    <div class="card-header d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">لیست حساب‌ها</h6>
<a href="index.php?page=admin&action=create_account_form" class="btn btn-primary btn-sm">افزودن حساب جدید</a>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>کد حساب</th>
                    <th>نام حساب</th>
                    <th>نوع</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php display_accounts_tree($accounts); // متغیر $accounts باید از کنترلر ارسال شود ?>
            </tbody>
        </table>
    </div>
</div>