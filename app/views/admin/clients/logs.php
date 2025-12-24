<?php
// app/views/admin/clients/logs.php

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
    <div class="col-lg-12">
        <div class="section-box">
            <div class="section-header">
                <span class="section-title">ثبت لاگ جدید</span>
            </div>
            <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_store_log" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="client_id" value="<?php echo sanitize($client->id); ?>">
                
                <div class="mb-3">
                    <label for="log_contact" class="form-label">رابط مشتری:</label>
                    <select class="form-select" id="log_contact" name="contact_id">
                        <option value="">-- برای ثبت لاگ، رابط را انتخاب کنید --</option>
                        <?php if ($client->user_type == 'legal' && !empty($client->contact_person)): ?>
                            <option value="<?php echo sanitize($client->user_id); ?>"><?php echo sanitize($client->contact_person); ?> (مسئول شرکت)</option>
                        <?php endif; ?>
                        <?php if ($client->user_type == 'legal' && !empty($contacts)): ?>
                            <?php foreach ($contacts as $contact): ?>
                                <option value="<?php echo sanitize($contact->user_id); ?>"><?php echo sanitize($contact->user_name); ?> (<?php echo sanitize($contact->position); ?>)</option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="log_type" class="form-label">نوع لاگ:</label>
                    <select class="form-select" id="log_type" name="log_type" required>
                        <option value="call">تماس تلفنی</option>
                        <option value="ticket">تیکت</option>
                        <option value="in_person">حضوری</option>
                        <option value="email">ایمیل</option>
                        <option value="sms">پیامک</option>
                        <option value="whatsapp">واتس‌اپ</option>
                        <option value="telegram">تلگرام</option>
                        <option value="bale">بله</option>
                        <option value="eitaa">ایتا</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="log_description" class="form-label">توضیحات:</label>
                    <textarea class="form-control" id="log_description" name="description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="log_attachment" class="form-label">فایل پیوست:</label>
                    <input class="form-control" type="file" id="log_attachment" name="attachment">
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">ثبت لاگ</button>
                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn btn-secondary">بازگشت به لیست مشتریان</a>
                </div>
            </form>
        </div>
    </div>
    
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
                                <th>فایل پیوست</th>
                                <th>تاریخ و زمان ثبت</th>
                                <th>عملیات</th>
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
                                                else if ($log->log_type == 'sms') echo 'secondary';
                                                else if ($log->log_type == 'whatsapp') echo 'success';
                                                else if ($log->log_type == 'telegram') echo 'info';
                                                else if ($log->log_type == 'bale') echo 'primary';
                                                else if ($log->log_type == 'eitaa') echo 'warning';
                                                else echo 'dark';
                                            ?>"><?php echo sanitize($log->log_type); ?></span>
                                        </td>
                                        <td><?php echo nl2br(sanitize($log->description)); ?></td>
                                        <td><?php echo sanitize($log->user_name ?? '---'); ?></td>
                                        <td><?php echo sanitize($log->contact_name ?? '---'); ?></td>
                                        <td>
                                            <?php if (!empty($log->file_path)): ?>
                                                <a href="<?php echo APP_URL . '/public/uploads/client_logs/' . sanitize($log->file_path); ?>" target="_blank" class="btn btn-sm btn-outline-primary">دانلود</a>
                                            <?php else: ?>
                                                ---
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo sanitize(jdate('Y/m/d H:i:s', strtotime($log->log_date))); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning edit-log-btn" data-bs-toggle="modal" data-bs-target="#editLogModal" 
                                                    data-log-id="<?php echo sanitize($log->id); ?>" 
                                                    data-log-type="<?php echo sanitize($log->log_type); ?>"
                                                    data-log-contact-id="<?php echo sanitize($log->contact_user_id); ?>"
                                                    data-log-description="<?php echo sanitize($log->description); ?>"
                                                    data-file-name="<?php echo sanitize($log->file_name ?? ''); ?>"
                                                    data-file-path="<?php echo sanitize($log->file_path ?? ''); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteClientLog" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این لاگ اطمینان دارید؟');">
                                                <input type="hidden" name="log_id" value="<?php echo sanitize($log->id); ?>">
                                                <input type="hidden" name="client_id" value="<?php echo sanitize($client->id); ?>">
                                                <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">هیچ لاگی برای این مشتری ثبت نشده است.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editLogModal" tabindex="-1" aria-labelledby="editLogModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLogModalLabel">ویرایش لاگ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <form id="editLogForm" action="<?php echo APP_URL; ?>/index.php?page=admin&action=updateClientLog" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="log_id" id="edit-log-id">
                <input type="hidden" name="client_id" value="<?php echo sanitize($client->id); ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-log-contact" class="form-label">رابط مشتری:</label>
                        <select class="form-select" id="edit-log-contact" name="contact_id">
                            <option value="">-- برای ثبت لاگ، رابط را انتخاب کنید --</option>
                            <?php if ($client->user_type == 'legal' && !empty($client->contact_person)): ?>
                                <option value="<?php echo sanitize($client->user_id); ?>"><?php echo sanitize($client->contact_person); ?> (مسئول شرکت)</option>
                            <?php endif; ?>
                            <?php if ($client->user_type == 'legal' && !empty($contacts)): ?>
                                <?php foreach ($contacts as $contact): ?>
                                    <option value="<?php echo sanitize($contact->user_id); ?>"><?php echo sanitize($contact->user_name); ?> (<?php echo sanitize($contact->position); ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-log-type" class="form-label">نوع لاگ:</label>
                        <select class="form-select" id="edit-log-type" name="log_type" required>
                            <option value="call">تماس تلفنی</option>
                            <option value="ticket">تیکت</option>
                            <option value="in_person">حضوری</option>
                            <option value="email">ایمیل</option>
                            <option value="sms">پیامک</option>
                            <option value="whatsapp">واتس‌اپ</option>
                            <option value="telegram">تلگرام</option>
                            <option value="bale">بله</option>
                            <option value="eitaa">ایتا</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-log-description" class="form-label">توضیحات:</label>
                        <textarea class="form-control" id="edit-log-description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3" id="edit-file-section">
                        <label for="edit-log-attachment" class="form-label">فایل پیوست:</label>
                        <div id="current-file-container">
                        </div>
                        <input class="form-control" type="file" id="edit-log-attachment" name="new_attachment">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editLogModal = document.getElementById('editLogModal');
        editLogModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const logId = button.getAttribute('data-log-id');
            const logType = button.getAttribute('data-log-type');
            const logContactId = button.getAttribute('data-log-contact-id');
            const logDescription = button.getAttribute('data-log-description');
            
            const fileName = button.getAttribute('data-file-name');
            const filePath = button.getAttribute('data-file-path');

            const modalLogIdInput = editLogModal.querySelector('#edit-log-id');
            const modalLogTypeSelect = editLogModal.querySelector('#edit-log-type');
            const modalLogContactSelect = editLogModal.querySelector('#edit-log-contact');
            const modalLogDescriptionTextarea = editLogModal.querySelector('#edit-log-description');
            const currentFileContainer = editLogModal.querySelector('#current-file-container');

            modalLogIdInput.value = logId;
            modalLogTypeSelect.value = logType;
            modalLogContactSelect.value = logContactId;
            modalLogDescriptionTextarea.value = logDescription;
            
            currentFileContainer.innerHTML = '';
            if (fileName && filePath) {
                currentFileContainer.innerHTML = `
                    <div class="alert alert-info d-flex justify-content-between align-items-center mb-2">
                        <span>فایل فعلی: ${fileName}</span>
                        <div>
                            <a href="<?php echo APP_URL; ?>/public/uploads/client_logs/${filePath}" target="_blank" class="btn btn-sm btn-info me-2">مشاهده</a>
                            <button type="button" class="btn btn-sm btn-danger" id="remove-file-btn" data-log-id="${logId}">حذف فایل</button>
                        </div>
                    </div>
                `;
            }
        });
        
        // منطق حذف فایل از طریق Ajax
        document.getElementById('editLogForm').addEventListener('click', function(e) {
            if (e.target && e.target.id === 'remove-file-btn') {
                e.preventDefault();
                const logId = e.target.getAttribute('data-log-id');
                if (confirm('آیا مطمئن هستید که می‌خواهید فایل پیوست را حذف کنید؟')) {
                    fetch('<?php echo APP_URL; ?>/index.php?page=admin&action=deleteLogAttachment', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ log_id: logId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('فایل با موفقیت حذف شد.');
                            document.getElementById('current-file-container').innerHTML = '';
                            location.reload(); 
                        } else {
                            alert('خطا در حذف فایل: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('خطا در برقراری ارتباط با سرور.');
                    });
                }
            }
        });
    });
</script>