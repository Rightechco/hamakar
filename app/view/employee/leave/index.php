<?php
// app/views/employee/leave/index.php - صفحه مدیریت مرخصی برای کارمند
?>

<h3 class="mb-4">مدیریت درخواست‌های مرخصی</h3>

<div class="row">
    <div class="col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ثبت درخواست جدید</h6>
            </div>
            <div class="card-body">
                <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=submit_leave_request" method="POST" id="leave-form">
                    <div class="mb-3">
                        <label class="form-label">نوع مرخصی:</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="leave_type" id="type_daily" value="daily" checked>
                                <label class="form-check-label" for="type_daily">روزانه</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="leave_type" id="type_hourly" value="hourly">
                                <label class="form-check-label" for="type_hourly">ساعتی</label>
                            </div>
                        </div>
                    </div>

                    <div id="daily-fields">
                        <div class="mb-3">
                            <label for="start_date_daily" class="form-label">از تاریخ:</label>
                            <input type="text" class="form-control persian-datepicker" name="start_date" id="start_date_daily">
                        </div>
                        <div class="mb-3">
                            <label for="end_date_daily" class="form-label">تا تاریخ:</label>
                            <input type="text" class="form-control persian-datepicker" name="end_date" id="end_date_daily">
                        </div>
                    </div>

                    <div id="hourly-fields" style="display: none;">
                         <div class="mb-3">
                            <label for="start_date_hourly" class="form-label">در تاریخ:</label>
                            <input type="text" class="form-control persian-datepicker" name="start_date_hourly" id="start_date_hourly">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="start_time" class="form-label">از ساعت:</label>
                                <input type="time" class="form-control" name="start_time" id="start_time">
                            </div>
                            <div class="col-6">
                                <label for="end_time" class="form-label">تا ساعت:</label>
                                <input type="time" class="form-control" name="end_time" id="end_time">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">دلیل درخواست:</label>
                        <textarea name="reason" id="reason" class="form-control" rows="4" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100">ثبت درخواست</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">تاریخچه درخواست‌های شما</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>نوع</th>
                                <th>بازه زمانی</th>
                                <th>وضعیت</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($requests)): ?>
                                <?php foreach ($requests as $req): ?>
                                <tr>
                                    <td><?php echo ($req->leave_type == 'daily') ? 'روزانه' : 'ساعتی'; ?></td>
                                    <td>
                                        <?php if ($req->leave_type == 'daily'): ?>
                                            <?php echo jdate('Y/m/d', strtotime($req->start_date)); ?> تا <?php echo jdate('Y/m/d', strtotime($req->end_date)); ?>
                                        <?php else: ?>
                                            <?php echo jdate('Y/m/d', strtotime($req->start_date)); ?> از ساعت <?php echo jdate('H:i', strtotime($req->start_date)); ?> تا <?php echo jdate('H:i', strtotime($req->end_date)); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                            $status_map = ['pending' => ['text' => 'در انتظار', 'color' => 'warning'], 'approved' => ['text' => 'تایید شده', 'color' => 'success'], 'rejected' => ['text' => 'رد شده', 'color' => 'danger']];
                                        ?>
                                        <span class="badge bg-<?php echo $status_map[$req->status]['color']; ?>">
                                            <?php echo $status_map[$req->status]['text']; ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center">هیچ درخواستی ثبت نشده است.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const leaveTypeRadios = document.querySelectorAll('input[name="leave_type"]');
    const dailyFields = document.getElementById('daily-fields');
    const hourlyFields = document.getElementById('hourly-fields');
    const startDateDaily = document.getElementById('start_date_daily');
    const endDateDaily = document.getElementById('end_date_daily');
    const startDateHourly = document.getElementById('start_date_hourly');

    function toggleFields() {
        if (document.getElementById('type_daily').checked) {
            dailyFields.style.display = 'block';
            hourlyFields.style.display = 'none';
            // برای اطمینان از ارسال مقادیر صحیح، مقادیر فیلدهای دیگر را کپی یا خالی می‌کنیم
            endDateDaily.name = 'end_date';
            startDateDaily.name = 'start_date';
            startDateHourly.name = 'start_date_hourly_disabled'; // نام را تغییر می‌دهیم تا ارسال نشود
        } else {
            dailyFields.style.display = 'none';
            hourlyFields.style.display = 'block';
            // برای مرخصی ساعتی، تاریخ شروع و پایان یکی است
            endDateDaily.name = 'end_date_disabled';
            startDateDaily.name = 'start_date_disabled';
            startDateHourly.name = 'start_date';
        }
    }

    leaveTypeRadios.forEach(radio => radio.addEventListener('change', toggleFields));
    
    // اجرای اولیه برای تنظیم فرم
    toggleFields();
});
</script>