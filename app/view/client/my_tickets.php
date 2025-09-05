<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">تیکت‌های پشتیبانی</h1>
    <a href="<?php echo APP_URL; ?>/index.php?page=client&action=create_ticket" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-2"></i>ایجاد تیکت جدید
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>موضوع</th>
                        <th>دپارتمان</th>
                        <th>اولویت</th>
                        <th>وضعیت</th>
                        <th>آخرین بروزرسانی</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">شما تاکنون هیچ تیکتی ثبت نکرده‌اید.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($ticket->subject); ?></td>
                                <td><?php echo htmlspecialchars($ticket->department); ?></td>
                                <td><?php echo htmlspecialchars($ticket->priority); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        if ($ticket->status == 'open') echo 'success';
                                        elseif ($ticket->status == 'answered') echo 'info';
                                        else echo 'secondary';
                                    ?>"><?php echo htmlspecialchars($ticket->status); ?></span>
                                </td>
                                <td><?php echo jdate('Y/m/d H:i', strtotime($ticket->updated_at)); ?></td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/index.php?page=client&action=view_ticket&id=<?php echo $ticket->id; ?>" class="btn btn-sm btn-outline-primary">
                                        مشاهده
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>