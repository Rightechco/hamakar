<h1 class="mb-4">ایجاد تیکت جدید</h1>

<div class="card shadow">
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/index.php?page=client&action=store_ticket" method="POST">
            <div class="mb-3">
                <label for="subject" class="form-label">موضوع <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="subject" name="subject" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="department" class="form-label">دپارتمان <span class="text-danger">*</span></label>
                    <select class="form-select" id="department" name="department" required>
                        <option value="support">پشتیبانی فنی</option>
                        <option value="sales">فروش</option>
                        <option value="billing">مالی و صورتحساب</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="priority" class="form-label">اولویت <span class="text-danger">*</span></label>
                    <select class="form-select" id="priority" name="priority" required>
                        <option value="low">کم</option>
                        <option value="medium" selected>متوسط</option>
                        <option value="high">زیاد</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">پیام شما <span class="text-danger">*</span></label>
                <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
            </div>
            
            <hr>
            <button type="submit" class="btn btn-primary">ارسال تیکت</button>
            <a href="<?php echo APP_URL; ?>/index.php?page=client&action=my_tickets" class="btn btn-secondary">انصراف</a>
        </form>
    </div>
</div>