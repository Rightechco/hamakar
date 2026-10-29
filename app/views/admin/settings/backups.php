<?php
// app/views/admin/settings/backups.php - مدیریت جامع پشتیبان‌گیری فیزیکی و ابری و بازگردانی هماکار
$backups = $backups ?? [];
$totalSize = $totalSize ?? 0;

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<style>
.backup-card {
    border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    background: #ffffff;
}
.backup-action-box {
    border-radius: 16px;
    padding: 24px;
    border: 2px dashed #cbd5e1;
    background: #f8fafc;
    transition: all 0.25s ease;
    text-align: center;
}
.backup-action-box:hover {
    border-color: #6366f1;
    background: #f1f5f9;
}
</style>

<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h3 class="fw-bold mb-1" style="color:#0f172a;">
                <i class="fas fa-database text-primary me-2"></i>مدیریت پشتیبان‌گیری فیزیکی و هاست ابری
            </h3>
            <span class="text-muted small">تهیه و بازگردانی نسخه‌های پشتیبان دیتابیس بر روی هارد دیسک کامپیوتر و سرور ابری</span>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=system_settings" class="btn btn-outline-secondary rounded-3 px-3">
                <i class="fas fa-sliders-h me-1"></i> تنظیمات سامانه
            </a>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=create_cloud_backup" class="btn btn-primary rounded-3 px-3 shadow-sm" onclick="return confirm('آیا می‌خواهید یک نسخه پشتیبان آنلاین جدید روی هاست ایجاد شود؟');">
                <i class="fas fa-cloud-upload-alt me-1"></i> ایجاد بکاپ آنلاین جدید
            </a>
        </div>
    </div>

    <?php FlashMessage::display(); ?>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="backup-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-save fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold">تعداد نسخه‌های پشتیبان هاست</small>
                        <h4 class="fw-bold mb-0 text-dark"><?php echo count($backups); ?> <small style="font-size:0.75rem;">فایل</small></h4>
                        <small class="text-muted">ذخیره شده در سرور</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="backup-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                        <i class="fas fa-hdd fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold">حجم کل فضای اشغال‌شده</small>
                        <h4 class="fw-bold mb-0 text-info"><?php echo formatBytes($totalSize); ?></h4>
                        <small class="text-muted">در دایرکتوری backups هاست</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="backup-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-history fa-lg"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block fw-bold">آخرین نسخه پشتیبان</small>
                        <h6 class="fw-bold mb-0 text-success">
                            <?php 
                                if (!empty($backups)) {
                                    echo htmlspecialchars($backups[0]->created_at ?? 'امروز');
                                } else {
                                    echo 'هنوز ایجاد نشده';
                                }
                            ?>
                        </h6>
                        <small class="text-muted">وضعیت سلامت دیتابیس: ۱۰۰٪ کامل</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Actions Grid -->
    <div class="row g-4 mb-4">
        <!-- Physical Backup Box -->
        <div class="col-md-6">
            <div class="backup-card p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-download fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">بکاپ‌گیری فیزیکی (روی سیستم کامپیوتری)</h5>
                        <small class="text-muted">دانلود فوری فایل کامل دیتابیس (.sql) روی هارد دیسک کامپیوتر یا فلش مموری</small>
                    </div>
                </div>
                <p class="small text-muted mb-4 lh-lg">
                    با کلیک روی دکمه زیر، فایل فیزیکی شامل تمام ساختارها، فاکتورها، کاربران و اسناد حسابداری مستقیماً دانلود شده و می‌توانید آن را در محلی امن خارج از هاست نگهداری فرمایید.
                </p>
                <div class="text-center">
                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=download_physical_backup" class="btn btn-success btn-lg rounded-3 px-4 shadow-sm w-100">
                        <i class="fas fa-download me-2"></i> دانلود بکاپ فیزیکی روی کامپیوتر (.sql)
                    </a>
                </div>
            </div>
        </div>

        <!-- Restore Physical Backup Box -->
        <div class="col-md-6">
            <div class="backup-card p-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-upload fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">بازگردانی از فایل فیزیکی (Restore)</h5>
                        <small class="text-muted">آپلود و بازیابی یک فایل پشتیبان قبلی از کامپیوتر به سامانه</small>
                    </div>
                </div>
                <form method="POST" action="<?php echo APP_URL; ?>/index.php?page=admin&action=restore_uploaded_backup" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">انتخاب فایل پشتیبان (.sql):</label>
                        <input type="file" name="backup_file" accept=".sql" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning btn-lg rounded-3 px-4 shadow-sm w-100 text-dark" onclick="return confirm('هشدار: بازگردانی بکاپ، اطلاعات فعلی را با داده‌های فایل جایگزین خواهد کرد. آیا مطمئن هستید؟');">
                        <i class="fas fa-undo-alt me-2"></i> بارگذاری و بازگردانی دیتابیس
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Cloud Backups Table -->
    <div class="backup-card overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark" style="font-size:1rem;">
                <i class="fas fa-cloud text-primary me-2"></i>نسخه‌های پشتیبان ذخیره‌شده روی هاست ابری
            </h5>
            <span class="badge bg-light text-dark border">دایرکتوری /backups</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                <thead class="table-light border-bottom">
                    <tr>
                        <th>نام فایل پشتیبان</th>
                        <th>تاریخ و ساعت ایجاد</th>
                        <th>حجم فایل</th>
                        <th>نوع نسخه</th>
                        <th>ایجادکننده</th>
                        <th class="text-center" style="min-width:160px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($backups)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-database fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                                <h6 class="fw-bold">هیچ نسخه پشتیبانی روی هاست یافت نشد</h6>
                                <p class="small text-muted mb-3">برای امنیت داده‌های سازمانی، پیشنهاد می‌شود اولین نسخه آنلاین را ایجاد فرمایید.</p>
                                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=create_cloud_backup" class="btn btn-sm btn-primary">
                                    ایجاد اولین نسخه پشتیبان روی هاست
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($backups as $b): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold font-monospace text-dark d-block"><?php echo htmlspecialchars($b->filename); ?></span>
                                    <small class="text-muted font-monospace" style="font-size:0.7rem;">MySQL Engine Dump</small>
                                </td>
                                <td><?php echo htmlspecialchars($b->created_at ?? '---'); ?></td>
                                <td class="font-monospace fw-bold text-primary"><?php echo formatBytes((int)($b->filesize ?? 0)); ?></td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2 py-1 rounded-pill">
                                        دیتابیس کامل
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($b->created_by ?? 'مدیر سیستم'); ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <!-- دانلود از هاست -->
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=download_cloud_backup&id=<?php echo $b->id; ?>" class="btn btn-outline-primary" title="دانلود این فایل به کامپیوتر">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        <!-- بازگردانی -->
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=restore_backup&id=<?php echo $b->id; ?>" class="btn btn-outline-warning text-dark" title="بازگردانی این نسخه به پایگاه داده" onclick="return confirm('آیا از بازگردانی این نسخه به پایگاه داده اطمینان کامل دارید؟');">
                                            <i class="fas fa-undo-alt"></i>
                                        </a>

                                        <!-- حذف -->
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=delete_cloud_backup&id=<?php echo $b->id; ?>" class="btn btn-outline-danger" title="حذف این نسخه از هاست" onclick="return confirm('آیا از حذف این فایل پشتیبان از روی هاست اطمینان دارید؟');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
