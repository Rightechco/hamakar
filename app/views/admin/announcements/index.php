<?php
// app/views/admin/announcements/index.php
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">مدیریت اطلاعیه‌ها</h1>
    <a href="index.php?page=admin&action=announcements_create" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> ایجاد اطلاعیه جدید
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>عنوان</th>
                        <th>مخاطبین</th>
                        <th>تاریخ ایجاد</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $announcement): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($announcement->title); ?></td>
                            <td><?php echo htmlspecialchars($announcement->target_roles); ?></td>
                            <td><?php echo jdate('Y/m/d H:i', strtotime($announcement->created_at)); ?></td>
                            <td>
                                <a href="index.php?page=admin&action=announcements_edit&id=<?php echo $announcement->id; ?>" class="btn btn-sm btn-info">ویرایش</a>
                                <form action="index.php?page=admin&action=announcements_delete&id=<?php echo $announcement->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('آیا از حذف این اطلاعیه مطمئن هستید؟');">
                                    <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>