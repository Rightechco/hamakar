<?php
// app/views/admin/clients/logs.php

$client = $client ?? null;
$logs = $logs ?? [];
?>
<h1 class="mb-4 text-center">لاگ های کارفرما: <?php echo sanitize($client->name ?? '---'); ?></h1>

<div class="card shadow-lg p-4 mx-auto" style="max-width: 1000px;">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">تاریخچه فعالیت‌ها</h5>
        <div>
            <button type="button" class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#logModal">
                <i class="fas fa-plus me-1"></i> افزودن لاگ جدید
            </button>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> بازگشت
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>نوع لاگ</th>
                        <th>توضیحات</th>
                        <th>کاربر ثبت کننده</th>
                        <th>تاریخ و زمان ثبت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="text-center"><?php echo sanitize($log->id); ?></td>
                                <td><span class="badge bg-<?php
                                    if ($log->log_type == 'call') echo 'primary';
                                    else if ($log->log_type == 'ticket') echo 'info';
                                    else if ($log->log_type == 'in_person') echo 'success';
                                    else if ($log->log_type == 'email') echo 'warning';
                                    else echo 'secondary';
                                ?>"><?php echo sanitize($log->log_type); ?></span></td>
                                <td><?php echo nl2br(sanitize($log->description)); ?></td>
                                <td><?php echo sanitize($log->user_name ?? '---'); ?></td>
                                <td><?php echo sanitize(JalaliDate::toJalali($log->log_date)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">هیچ لاگی برای این کارفرما ثبت نشده است.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logModalLabel">ثبت لاگ برای کارفرما</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_store_log" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="client_id" value="<?php echo sanitize($client->id); ?>">
                    <div class="mb-3">
                        <label for="log_type" class="form-label">نوع لاگ:</label>
                        <select class="form-select" id="log_type" name="log_type" required>
                            <option value="call">تماس تلفنی</option>
                            <option value="in_person">حضوری</option>
                            <option value="ticket">تیکت</option>
                            <option value="email">ایمیل</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="log_description" class="form-label">توضیحات:</label>
                        <textarea class="form-control" id="log_description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                    <button type="submit" class="btn btn-primary">ثبت لاگ</button>
                </div>
            </form>
        </div>
    </div>
</div>