<h1 class="mb-4">مدیریت درخواست‌های مرخصی</h1>
<div class="card shadow">
    <div class="card-body">
        <table class="table table-hover">
            <thead><tr><th>کارمند</th><th>نوع</th><th>از تاریخ</th><th>تا تاریخ</th><th>دلیل</th><th>عملیات</th></tr></thead>
            <tbody>
                <?php foreach($requests as $req): ?>
                <tr>
                    <td><?php echo sanitize($req->employee_name); ?></td>
                    <td><?php echo ($req->leave_type == 'daily') ? 'روزانه' : 'ساعتی'; ?></td>
                    <td><?php echo jdate('Y/m/d H:i', strtotime($req->start_date)); ?></td>
                    <td><?php echo jdate('Y/m/d H:i', strtotime($req->end_date)); ?></td>
                    <td><?php echo sanitize($req->reason); ?></td>
                    <td>
                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=process_leave_request&id=<?php echo $req->id; ?>" method="POST">
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-sm btn-success">تایید</button>
                        </form>
                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=process_leave_request&id=<?php echo $req->id; ?>" method="POST" class="mt-1">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-sm btn-danger">رد</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>