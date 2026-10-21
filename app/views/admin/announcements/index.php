<?php
// app/views/admin/announcements/index.php
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
    }
    .btn-fin-action:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .table-premium { width: 100%; border-collapse: separate; border-spacing: 0; }
    .table-premium th { background: #f8fafc; color: #475569; font-weight: 700; font-size: 0.8rem; padding: 1rem; border-bottom: 1px solid #e2e8f0; }
    .table-premium td { padding: 0.9rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; color: #1e293b; vertical-align: middle; }
    .table-premium tr:hover td { background-color: #f8fafc; }

    .role-badge {
        display: inline-block;
        padding: 0.25rem 0.65rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        background-color: #e0e7ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
    }

    @media print {
        .no-print, #sidebar, .top-header-bar, #chat-toggler, #chat-window { display: none !important; }
        body { background: white !important; }
        .fin-card { border: 1px solid #ccc !important; box-shadow: none !important; }
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">مدیریت اطلاعیه‌ها</h1>
        <p class="text-muted small mb-0">لیست کلیه اطلاعیه‌ها، اخبار سیستم و سطوح مخاطبین مرتبط</p>
    </div>
    
    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-print text-indigo-600"></i>
            <span>چاپ لیست</span>
        </button>
        <button onclick="exportTableToExcel('announcements-table-area', 'لیست-اطلاعیه‌ها')" class="btn btn-fin-action d-inline-flex align-items-center gap-1.5">
            <i class="fas fa-file-excel text-success"></i>
            <span>خروجی اکسل</span>
        </button>
        <a href="index.php?page=admin&action=announcements_create" class="btn btn-fin-primary d-inline-flex align-items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>ایجاد اطلاعیه جدید</span>
        </a>
    </div>
</div>

<div class="fin-card" id="announcements-table-area">
    <div class="fin-card-header">
        <h6 class="fin-card-title">
            <i class="fas fa-bullhorn text-indigo-500" style="color:#6366f1;"></i>
            <span>اطلاعیه‌های ثبت‌شده</span>
        </h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium w-100">
                <thead>
                    <tr>
                        <th>عنوان اطلاعیه</th>
                        <th class="text-center" style="width: 220px;">مخاطبین هدف</th>
                        <th class="text-center" style="width: 180px;">تاریخ ایجاد</th>
                        <th class="text-center no-print" style="width: 140px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($announcements)): ?>
                        <?php foreach ($announcements as $announcement): ?>
                            <tr>
                                <td class="fw-bold text-slate-800"><?php echo htmlspecialchars($announcement->title); ?></td>
                                <td class="text-center">
                                    <span class="role-badge">
                                        <?php echo htmlspecialchars($announcement->target_roles); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark font-monospace">
                                        <?php echo jdate('Y/m/d H:i', strtotime($announcement->created_at)); ?>
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <a href="index.php?page=admin&action=announcements_edit&id=<?php echo $announcement->id; ?>" class="btn btn-sm btn-outline-primary rounded-2 px-2 py-1" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="index.php?page=admin&action=announcements_delete&id=<?php echo $announcement->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این اطلاعیه مطمئن هستید؟');">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2 py-1" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="fas fa-bullhorn fs-2 mb-2 d-block opacity-50"></i>
                                هیچ اطلاعیه‌ای تاکنون ثبت نشده است.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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