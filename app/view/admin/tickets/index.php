<?php
// app/views/admin/tickets/index.php
?>

<h1 class="mb-4">مدیریت تیکت‌های پشتیبانی</h1>

<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">لیست تمام تیکت‌ها</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>موضوع تیکت</th>
                        <th>ایجاد شده توسط (مشتری)</th>
                        <th>دپارتمان</th>
                        <th>اولویت</th>
                        <th>وضعیت</th>
                        <th>آخرین به‌روزرسانی</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tickets)): ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><strong><?php echo sanitize($ticket->subject); ?></strong></td>
                                <td><?php echo sanitize($ticket->client_name); ?></td>
                                <td><?php echo sanitize($ticket->department); ?></td>
                                <td>
                                    <?php 
                                        $priority_map = ['low' => 'کم', 'medium' => 'متوسط', 'high' => 'زیاد'];
                                        echo $priority_map[$ticket->priority] ?? sanitize($ticket->priority);
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        $status_map = ['open' => 'باز', 'answered' => 'پاسخ داده شده', 'client_reply' => 'پاسخ مشتری', 'closed' => 'بسته شده'];
                                        $status_color_map = ['open' => 'primary', 'answered' => 'success', 'client_reply' => 'warning', 'closed' => 'secondary'];
                                    ?>
                                    <span class="badge rounded-pill bg-<?php echo $status_color_map[$ticket->status] ?? 'dark'; ?>">
                                        <?php echo $status_map[$ticket->status] ?? sanitize($ticket->status); ?>
                                    </span>
                                </td>
                                <td><?php echo jdate('Y/m/d H:i', strtotime($ticket->updated_at)); ?></td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_ticket&id=<?php echo $ticket->id; ?>" class="btn btn-sm btn-info">
                                        مشاهده و پاسخ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">هیچ تیکتی یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>