<?php
// app/views/admin/clients/logs.php
$client = $client ?? null;
$logs = $logs ?? [];
$contacts = $contacts ?? [];
?>
<style>
    .fin-card { 
        background: #ffffff; 
        border-radius: 20px; 
        border: 1px solid #e2e8f0; 
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); 
        overflow: hidden; 
        margin-bottom: 1.5rem; 
    }
    .fin-card-header { 
        padding: 1.25rem 1.5rem; 
        background: #ffffff; 
        border-bottom: 1px solid #f1f5f9; 
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .fin-card-title { 
        font-weight: 800; 
        color: #0f172a; 
        font-size: 1rem; 
        margin: 0; 
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-fin-primary { 
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); 
        color: #fff; 
        border: none; 
        border-radius: 12px; 
        padding: 0.65rem 1.25rem; 
        font-weight: 700; 
        transition: all 0.2s ease; 
        text-decoration: none;
    }
    .btn-fin-primary:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 20px rgba(99,102,241,0.35); 
        color: #fff; 
    }

    .btn-fin-action { 
        background: #f1f5f9; 
        color: #334155; 
        border: 1px solid #cbd5e1; 
        border-radius: 10px; 
        padding: 0.45rem 0.9rem; 
        font-size: 0.825rem; 
        font-weight: 700; 
        transition: all 0.2s; 
        text-decoration: none;
    }
    .btn-fin-action:hover { 
        background: #e2e8f0; 
        color: #0f172a; 
    }

    .form-control-premium, .form-select-premium {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.65rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    .form-control-premium:focus, .form-select-premium:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }

    .table-premium { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-premium th { background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.8rem; padding: 1rem; border-bottom: 1px solid #e2e8f0; }
    .table-premium td { padding: 0.85rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; color: #1e293b; vertical-align: middle; }
    .table-premium tr:hover td { background-color: #f8fafc; }

    /* Modal Styling */
    .modal-content-premium {
        border-radius: 24px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }
    .modal-header-premium {
        background: #ffffff;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
    }

    @media print {
        .no-print, #sidebar, .top-header-bar, #chat-toggler, #chat-window { display: none !important; }
        body { background: white !important; }
        .fin-card { border: 1px solid #ccc !important; box-shadow: none !important; margin-bottom: 1rem !important; }
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">لاگ‌های مشتری: <?php echo sanitize($client->name ?? '---'); ?></h1>
        <p class="text-muted small mb-0">ثبت تعاملات، پیگیری‌های تلفنی، جلسات حضوری و یادداشت‌های مرتبط با مشتری</p>
    </div>
    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-print text-indigo-600"></i>
            <span>چاپ گزارش</span>
        </button>
        <button onclick="exportTableToExcel('client-logs-table-area', 'لاگ‌های-مشتری')" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-file-excel text-success"></i>
            <span>خروجی اکسل</span>
        </button>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn btn-fin-secondary d-inline-flex align-items-center gap-2">
            <i class="fas fa-arrow-right"></i>
            <span>بازگشت به لیست مشتریان</span>
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- فرم ثبت لاگ جدید -->
    <div class="col-lg-4 no-print">
        <div class="fin-card">
            <div class="fin-card-header">
                <h6 class="fin-card-title">
                    <i class="fas fa-plus-circle text-indigo-500" style="color:#6366f1;"></i>
                    <span>ثبت لاگ و تعامل جدید</span>
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_store_log" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="client_id" value="<?php echo sanitize($client->id); ?>">
                    
                    <div class="mb-3">
                        <label for="log_contact" class="form-label fw-bold text-secondary small">رابط مشتری:</label>
                        <select class="form-select form-select-premium" id="log_contact" name="contact_id">
                            <option value="">-- انتخاب رابط (اختیاری) --</option>
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
                        <label for="log_type" class="form-label fw-bold text-secondary small">نوع تعامل:</label>
                        <select class="form-select form-select-premium" id="log_type" name="log_type" required>
                            <option value="call">تماس تلفنی</option>
                            <option value="ticket">تیکت پشتیبانی</option>
                            <option value="in_person">جلسه حضوری</option>
                            <option value="email">ارسال ایمیل</option>
                            <option value="sms">پیامک</option>
                            <option value="whatsapp">واتس‌اپ</option>
                            <option value="telegram">تلگرام</option>
                            <option value="bale">بله</option>
                            <option value="eitaa">ایتا</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="log_description" class="form-label fw-bold text-secondary small">شرح تعامل و مذاکرات:</label>
                        <textarea class="form-control form-control-premium" id="log_description" name="description" rows="4" placeholder="خلاصه موارد مطرح شده..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="log_attachment" class="form-label fw-bold text-secondary small">فایل پیوست (مدرک/مستندات):</label>
                        <input class="form-control form-control-premium" type="file" id="log_attachment" name="attachment">
                    </div>

                    <button type="submit" class="btn btn-fin-primary w-100 py-2.5 d-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>ثبت و ذخیره لاگ</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- جدول لیست لاگ‌ها -->
    <div class="col-lg-8" id="client-logs-table-area">
        <div class="fin-card">
            <div class="fin-card-header">
                <h6 class="fin-card-title">
                    <i class="fas fa-history text-indigo-500" style="color:#6366f1;"></i>
                    <span>تاریخچه فعالیت‌های ثبت‌شده</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table-premium w-100">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th style="width: 120px;">نوع لاگ</th>
                                <th>توضیحات و شرح تعامل</th>
                                <th style="width: 130px;">ثبت‌کننده</th>
                                <th style="width: 130px;">رابط مشتری</th>
                                <th class="text-center" style="width: 80px;">پیوست</th>
                                <th class="text-center" style="width: 150px;">تاریخ و زمان</th>
                                <th class="text-center no-print" style="width: 90px;">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($logs)): ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td class="text-center font-monospace fw-bold text-slate-500"><?php echo sanitize($log->id); ?></td>
                                        <td>
                                            <?php 
                                                $badgeStyle = 'background:#f1f5f9; color:#475569;';
                                                if ($log->log_type == 'call') { $badgeStyle = 'background:#e0f2fe; color:#0369a1;'; }
                                                else if ($log->log_type == 'ticket') { $badgeStyle = 'background:#e0e7ff; color:#4338ca;'; }
                                                else if ($log->log_type == 'in_person') { $badgeStyle = 'background:#f0fdf4; color:#15803d;'; }
                                                else if ($log->log_type == 'email') { $badgeStyle = 'background:#fef3c7; color:#b45309;'; }
                                                else if ($log->log_type == 'whatsapp' || $log->log_type == 'eitaa') { $badgeStyle = 'background:#ecfdf5; color:#047857;'; }
                                            ?>
                                            <span class="badge rounded-pill border px-2.5 py-1 fw-bold fs-7" style="<?php echo $badgeStyle; ?>">
                                                <?php echo sanitize($log->log_type); ?>
                                            </span>
                                        </td>
                                        <td class="lh-base"><?php echo nl2br(sanitize($log->description)); ?></td>
                                        <td><span class="small text-muted fw-bold"><?php echo sanitize($log->user_name ?? '---'); ?></span></td>
                                        <td><span class="small text-muted"><?php echo sanitize($log->contact_name ?? '---'); ?></span></td>
                                        <td class="text-center">
                                            <?php if (!empty($log->file_path)): ?>
                                                <a href="<?php echo APP_URL . '/public/uploads/client_logs/' . sanitize($log->file_path); ?>" target="_blank" class="btn btn-sm btn-light border rounded-2 px-2 py-1" title="دانلود فایل">
                                                    <i class="fas fa-paperclip text-primary"></i>
                                                </a>
                                            <?php else: ?>---<?php endif; ?>
                                        </td>
                                        <td class="text-center"><span class="badge bg-light text-dark font-monospace"><?php echo sanitize(jdate('Y/m/d H:i', strtotime($log->log_date))); ?></span></td>
                                        <td class="text-center no-print">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-warning rounded-2 px-2 py-1 edit-log-btn" data-bs-toggle="modal" data-bs-target="#editLogModal" 
                                                        data-log-id="<?php echo sanitize($log->id); ?>" 
                                                        data-log-type="<?php echo sanitize($log->log_type); ?>"
                                                        data-log-contact-id="<?php echo sanitize($log->contact_user_id); ?>"
                                                        data-log-description="<?php echo sanitize($log->description); ?>"
                                                        data-file-name="<?php echo sanitize($log->file_name ?? ''); ?>"
                                                        data-file-path="<?php echo sanitize($log->file_path ?? ''); ?>" title="ویرایش">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteClientLog" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این لاگ اطمینان دارید؟');">
                                                    <input type="hidden" name="log_id" value="<?php echo sanitize($log->id); ?>">
                                                    <input type="hidden" name="client_id" value="<?php echo sanitize($client->id); ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1" title="حذف"><i class="fas fa-trash-alt"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center text-muted py-5"><i class="fas fa-history fs-2 mb-2 d-block opacity-50"></i>هیچ لاگی برای این مشتری ثبت نشده است.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- مدال ویرایش لاگ -->
<div class="modal fade" id="editLogModal" tabindex="-1" aria-labelledby="editLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-premium">
            <div class="modal-header modal-header-premium">
                <h5 class="modal-title fw-bold fs-6 text-slate-800 d-flex align-items-center gap-2" id="editLogModalLabel">
                    <i class="fas fa-edit text-indigo-500" style="color:#6366f1;"></i>
                    <span>ویرایش لاگ تعامل مشتری</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <form id="editLogForm" action="<?php echo APP_URL; ?>/index.php?page=admin&action=updateClientLog" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="log_id" id="edit-log-id">
                <input type="hidden" name="client_id" value="<?php echo sanitize($client->id); ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit-log-contact" class="form-label fw-bold text-secondary small">رابط مشتری:</label>
                        <select class="form-select form-select-premium" id="edit-log-contact" name="contact_id">
                            <option value="">-- انتخاب رابط --</option>
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
                        <label for="edit-log-type" class="form-label fw-bold text-secondary small">نوع لاگ:</label>
                        <select class="form-select form-select-premium" id="edit-log-type" name="log_type" required>
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
                        <label for="edit-log-description" class="form-label fw-bold text-secondary small">توضیحات:</label>
                        <textarea class="form-control form-control-premium" id="edit-log-description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3" id="edit-file-section">
                        <label for="edit-log-attachment" class="form-label fw-bold text-secondary small">فایل پیوست جدید:</label>
                        <div id="current-file-container"></div>
                        <input class="form-control form-control-premium" type="file" id="edit-log-attachment" name="new_attachment">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-fin-action" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-fin-primary d-inline-flex align-items-center gap-1.5">
                        <i class="fas fa-check-circle"></i>
                        <span>ذخیره تغییرات</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editLogModal = document.getElementById('editLogModal');
        if(editLogModal) {
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
                        <div class="p-2.5 bg-indigo-50 border border-indigo-100 rounded-3 d-flex justify-content-between align-items-center mb-2">
                            <span class="small font-mono text-indigo-900">فایل فعلی: ${fileName}</span>
                            <div class="d-flex gap-1">
                                <a href="<?php echo APP_URL; ?>/public/uploads/client_logs/${filePath}" target="_blank" class="btn btn-sm btn-light border">مشاهده</a>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="remove-file-btn" data-log-id="${logId}">حذف</button>
                            </div>
                        </div>
                    `;
                }
            });
            
            document.getElementById('editLogForm').addEventListener('click', function(e) {
                if (e.target && e.target.id === 'remove-file-btn') {
                    e.preventDefault();
                    const logId = e.target.getAttribute('data-log-id');
                    if (confirm('آیا مطمئن هستید که می‌خواهید فایل پیوست را حذف کنید؟')) {
                        fetch('<?php echo APP_URL; ?>/index.php?page=admin&action=deleteLogAttachment', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ log_id: logId })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('current-file-container').innerHTML = '';
                                location.reload(); 
                            } else { alert('خطا: ' + data.message); }
                        })
                        .catch(error => { console.error('Error:', error); });
                    }
                }
            });
        }
    });

    function exportTableToExcel(tableID, filename = ''){
        var downloadLink;
        var dataType = 'application/vnd.ms-excel';
        var tableSelect = document.getElementById(tableID);
        var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
        filename = filename?filename+'.xls':'excel_data.xls';
        downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);
        if(navigator.msSaveOrOpenBlob){
            var blob = new Blob(['\ufeff' + tableHTML], { type: dataType });
            navigator.msSaveOrOpenBlob(blob, filename);
        }else{
            downloadLink.href = 'data:' + dataType + ', ' + '\ufeff' + tableHTML;
            downloadLink.download = filename;
            downloadLink.click();
        }
    }
</script>