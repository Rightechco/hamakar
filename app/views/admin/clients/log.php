<?php
// app/views/admin/clients/logs.php
$client = $client ?? null;
$logs = $logs ?? [];
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
        transition: all 0.2s; 
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
        <h1 class="h3 fw-bold text-dark mb-1">لاگ‌های کارفرما: <?php echo sanitize($client->name ?? '---'); ?></h1>
        <p class="text-muted small mb-0">تاریخچه کامل پیگیری‌ها، جلسات، تماس‌ها و تعاملات ثبت‌شده</p>
    </div>

    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-print text-indigo-600"></i>
            <span>چاپ لیست</span>
        </button>
        <button onclick="exportTableToExcel('client-logs-area', 'لاگ‌های-کارفرما')" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-file-excel text-success"></i>
            <span>خروجی اکسل</span>
        </button>
        <button type="button" class="btn btn-fin-primary d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#logModal">
            <i class="fas fa-plus"></i>
            <span>افزودن لاگ جدید</span>
        </button>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=clients" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-arrow-right"></i>
            <span>بازگشت</span>
        </a>
    </div>
</div>

<div class="fin-card" id="client-logs-area">
    <div class="fin-card-header">
        <h6 class="fin-card-title">
            <i class="fas fa-history text-indigo-500" style="color:#6366f1;"></i>
            <span>تاریخچه فعالیت‌ها و تعاملات</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium w-100">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60px;">#</th>
                        <th style="width: 140px;">نوع لاگ</th>
                        <th>توضیحات و شرح فعالیت</th>
                        <th style="width: 180px;">کاربر ثبت‌کننده</th>
                        <th class="text-center" style="width: 180px;">تاریخ و زمان ثبت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="text-center font-monospace text-slate-500 fw-bold"><?php echo sanitize($log->id); ?></td>
                                <td>
                                    <?php 
                                        $badgeStyle = 'background:#e0e7ff; color:#4338ca;';
                                        $typeText = sanitize($log->log_type);
                                        if ($log->log_type == 'call') { $badgeStyle = 'background:#e0f2fe; color:#0369a1;'; $typeText = 'تماس تلفنی'; }
                                        else if ($log->log_type == 'ticket') { $badgeStyle = 'background:#f0fdf4; color:#15803d;'; $typeText = 'تیکت پشتیبانی'; }
                                        else if ($log->log_type == 'in_person') { $badgeStyle = 'background:#fef3c7; color:#b45309;'; $typeText = 'جلسه حضوری'; }
                                        else if ($log->log_type == 'email') { $badgeStyle = 'background:#f3e8ff; color:#6b21a8;'; $typeText = 'ارسال ایمیل'; }
                                    ?>
                                    <span class="badge rounded-pill border px-3 py-1.5 fw-bold fs-7" style="<?php echo $badgeStyle; ?>">
                                        <?php echo $typeText; ?>
                                    </span>
                                </td>
                                <td class="lh-base"><?php echo nl2br(sanitize($log->description)); ?></td>
                                <td><span class="small text-muted fw-bold"><?php echo sanitize($log->user_name ?? '---'); ?></span></td>
                                <td class="text-center"><span class="badge bg-light text-dark font-monospace"><?php echo sanitize(JalaliDate::toJalali($log->log_date)); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-history fs-2 mb-2 d-block opacity-50"></i>
                                هیچ لاگی برای این کارفرما ثبت نشده است.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal ثبت لاگ جدید -->
<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-premium">
            <div class="modal-header modal-header-premium">
                <h5 class="modal-title fw-bold fs-6 text-slate-800 d-flex align-items-center gap-2" id="logModalLabel">
                    <i class="fas fa-plus-circle text-indigo-500" style="color:#6366f1;"></i>
                    <span>ثبت لاگ برای کارفرما</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_store_log" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="client_id" value="<?php echo sanitize($client->id); ?>">
                    
                    <div class="mb-3">
                        <label for="log_type" class="form-label fw-bold text-secondary small">نوع لاگ / تعامل:</label>
                        <select class="form-select form-select-premium" id="log_type" name="log_type" required>
                            <option value="call">تماس تلفنی</option>
                            <option value="in_person">جلسه حضوری</option>
                            <option value="ticket">تیکت پشتیبانی</option>
                            <option value="email">ارسال ایمیل</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="log_description" class="form-label fw-bold text-secondary small">توضیحات و خلاصه گفتگو:</label>
                        <textarea class="form-control form-control-premium" id="log_description" name="description" rows="4" placeholder="شرح پیگیری یا مذاکره انجام شده..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-fin-action" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-fin-primary d-inline-flex align-items-center gap-1.5">
                        <i class="fas fa-save"></i>
                        <span>ثبت لاگ</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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