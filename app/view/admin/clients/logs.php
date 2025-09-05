<?php
// app/views/admin/clients/logs.php
// این فایل برای نمایش لاگ های یک مشتری خاص استفاده می شود.

$client = $client ?? null;
$logs = $logs ?? [];
$contacts = $contacts ?? [];
?>
<style>
    .section-box {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .section-header {
        border-bottom: 2px solid #e0e0e0;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
    }
    .section-title {
        color: #34495e;
        font-weight: 700;
        font-size: 1.25rem;
    }
    .table thead th {
        background-color: #f8f9fa;
        color: #6c757d;
    }
    .table tbody tr:hover {
        background-color: #f1f3f5;
    }
    .alert-info {
        background-color: #e8f5fd;
        border-color: #cce7f5;
        color: #0c5460;
    }
</style>

<h1 class="mb-4">لاگ های مشتری: <?php echo sanitize($client->name ?? '---'); ?></h1>

<div class="row">
    <!-- بخش ثبت لاگ جدید -->
    <div class="col-lg-12">
        <div class="section-box">
            <div class="section-header">
                <span class="section-title">ثبت لاگ جدید</span>
            </div>
            <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_store_log" method="POST">
                <input type="hidden" name="client_id" value="<?php echo sanitize($client->id); ?>">
                
                <?php if ($client->user_type == 'legal' && !empty($contacts)): ?>
                    <div class="mb-3">
                        <label for="contact_id" class="form-label">رابط شرکت:</label>
                        <select class="form-select" id="contact_id" name="contact_id">
                            <option value="">-- برای ثبت لاگ، رابط را انتخاب کنید --</option>
                            <?php foreach ($contacts as $contact): ?>
                                <option value="<?php echo sanitize($contact->user_id); ?>"><?php echo sanitize($contact->user_name); ?> (<?php echo sanitize($contact->position); ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="log_type" class="form-label">نوع لاگ:</label>
                    <select class="form-select" id="log_type" name="log_type" required>
                        <option value="call">تماس تلفنی</option>
                        <option value="ticket">تیکت</option>
                        <option value="in_person">حضوری</option>
                        <option value="email">ایمیل</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="log_description" class="form-label">توضیحات:</label>
                    <textarea class="form-control" id="log_description" name="description" rows="3" required></textarea>
                </div>
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">ثبت لاگ</button>
                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn btn-secondary">بازگشت به لیست مشتریان</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- بخش تاریخچه لاگ‌ها -->
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">تاریخچه لاگ ها</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>نوع لاگ</th>
                                <th>توضیحات</th>
                                <th>کاربر ثبت کننده</th>
                                <th>رابط مشتری</th>
                                <th>تاریخ و زمان ثبت</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($logs)): ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?php echo sanitize($log->id); ?></td>
                                        <td>
                                            <span class="badge bg-<?php
                                                if ($log->log_type == 'call') echo 'primary';
                                                else if ($log->log_type == 'ticket') echo 'info';
                                                else if ($log->log_type == 'in_person') echo 'success';
                                                else if ($log->log_type == 'email') echo 'warning';
                                                else echo 'secondary';
                                            ?>"><?php echo sanitize($log->log_type); ?></span>
                                        </td>
                                        <td><?php echo nl2br(sanitize($log->description)); ?></td>
                                        <td><?php echo sanitize($log->user_name ?? '---'); ?></td>
                                        <td><?php echo sanitize($log->contact_name ?? '---'); ?></td>
                                        <td><?php echo sanitize(jdate('Y/m/d H:i:s', strtotime($log->log_date))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">هیچ لاگی برای این مشتری ثبت نشده است.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
