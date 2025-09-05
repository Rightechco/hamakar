<h3 class="mb-4">مدیریت مرخصی‌ها</h3>
<div class="card shadow mb-4">
    <div class="card-header">ثبت درخواست جدید</div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_leave_request" method="POST">
            <textarea name="reason" class="form-control" placeholder="دلیل درخواست..." required></textarea>
            <button type="submit" class="btn btn-primary mt-3">ثبت درخواست</button>
        </form>
    </div>
</div>
<div class="card shadow">
    <div class="card-header">تاریخچه درخواست‌ها</div>
    <div class="card-body">
        <table class="table">
            </table>
    </div>
</div>