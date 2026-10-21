<?php
// app/views/admin/projects/index.php - مدیریت و لیست پروژه‌ها (اولترا پرمیوم)
global $auth;
$projects = $projects ?? [];
$categories = $categories ?? [];
$projectSummary = $projectSummary ?? [];
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 18px;
        --radius-md: 12px;
        --shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    body, .projects-list-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    /* KPI Status Cards */
    .kpi-status-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 18px;
        box-shadow: var(--shadow-subtle);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .kpi-status-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.08);
    }

    .kpi-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .bg-soft-primary { background-color: #e0e7ff; color: #4338ca !important; }
    .bg-soft-info { background-color: #e0f2fe; color: #0369a1 !important; }
    .bg-soft-success { background-color: #dcfce7; color: #15803d !important; }
    .bg-soft-warning { background-color: #fef3c7; color: #b45309 !important; }
    .bg-soft-secondary { background-color: #f1f5f9; color: #475569 !important; }
    .bg-soft-danger { background-color: #fee2e2; color: #b91c1c !important; }

    /* Ultra Card & Table */
    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
    }

    .table-ultra-project {
        border-collapse: separate;
        border-spacing: 0 8px;
        width: 100%;
    }

    .table-ultra-project tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .table-ultra-project tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.05);
    }

    .table-ultra-project td, .table-ultra-project th {
        padding: 1.1rem 1.25rem;
        border: none;
        vertical-align: middle;
    }

    /* Action Buttons */
    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
        text-decoration: none;
    }

    .action-btn:hover {
        transform: scale(1.08);
    }

    .progress-ultra {
        height: 8px;
        border-radius: 9999px;
        background-color: #f1f5f9;
        overflow: hidden;
    }

    .progress-bar-ultra {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        border-radius: 9999px;
    }
</style>

<div class="projects-list-wrapper py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">مدیریت پروژه‌ها</h3>
            <p class="text-muted small mb-0">داشبورد جامع نظارت بر وضعیت پیشرفت پروژه‌ها، کارفرمایان و زمان‌بندی‌ها</p>
        </div>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=projects_create" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-medium">
            <i class="fas fa-plus me-1.5"></i> ایجاد پروژه جدید
        </a>
    </div>

    <?php FlashMessage::display(); ?>

    <!-- Summary KPI Grid -->
    <div class="row g-3 mb-4">
        <!-- Total Projects -->
        <div class="col-lg-2 col-md-4 col-6">
            <div class="kpi-status-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-medium">کل پروژه‌ها</span>
                    <div class="kpi-icon-box bg-soft-primary"><i class="fas fa-folder"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0"><?php echo number_format($projectSummary['total'] ?? 0); ?></h3>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-lg-2 col-md-4 col-6">
            <div class="kpi-status-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-medium">در حال انجام</span>
                    <div class="kpi-icon-box bg-soft-info"><i class="fas fa-spinner"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0"><?php echo number_format($projectSummary['in_progress'] ?? 0); ?></h3>
            </div>
        </div>

        <!-- Finished -->
        <div class="col-lg-2 col-md-4 col-6">
            <div class="kpi-status-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-medium">تکمیل شده</span>
                    <div class="kpi-icon-box bg-soft-success"><i class="fas fa-check-circle"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0"><?php echo number_format($projectSummary['finished'] ?? 0); ?></h3>
            </div>
        </div>

        <!-- On Hold -->
        <div class="col-lg-2 col-md-4 col-6">
            <div class="kpi-status-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-medium">متوقف شده</span>
                    <div class="kpi-icon-box bg-soft-warning"><i class="fas fa-pause-circle"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0"><?php echo number_format($projectSummary['on_hold'] ?? 0); ?></h3>
            </div>
        </div>

        <!-- Not Started -->
        <div class="col-lg-2 col-md-4 col-6">
            <div class="kpi-status-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-medium">شروع نشده</span>
                    <div class="kpi-icon-box bg-soft-secondary"><i class="fas fa-hourglass-start"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0"><?php echo number_format($projectSummary['not_started'] ?? 0); ?></h3>
            </div>
        </div>

        <!-- Canceled -->
        <div class="col-lg-2 col-md-4 col-6">
            <div class="kpi-status-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small fw-medium">لغو شده</span>
                    <div class="kpi-icon-box bg-soft-danger"><i class="fas fa-times-circle"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-0"><?php echo number_format($projectSummary['canceled'] ?? 0); ?></h3>
            </div>
        </div>
    </div>

    <!-- Projects Table Container -->
    <div class="ultra-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-list me-2 text-primary"></i>لیست پروژه‌های فعال و آرشیو</h6>
        </div>

        <div class="table-responsive">
            <table class="table table-ultra-project align-middle">
                <thead>
                    <tr class="text-muted small text-uppercase">
                        <th>نام پروژه</th>
                        <th>مشتری / کارفرما</th>
                        <th>دسته‌بندی</th>
                        <th style="width: 22%;">میزان پیشرفت</th>
                        <th class="text-center">وضعیت</th>
                        <th class="text-end">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($projects)): ?>
                        <?php foreach ($projects as $project): ?>
                            <?php $progress = ($project->total_tasks > 0) ? round(($project->completed_tasks / $project->total_tasks) * 100) : 0; ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-soft rounded-3 bg-soft-primary p-2 me-2 text-center" style="width: 38px; height: 38px;">
                                            <i class="fas fa-project-diagram text-primary"></i>
                                        </div>
                                        <span class="fw-bold text-dark fs-6"><?php echo sanitize($project->name); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark small">
                                        <?php echo sanitize($project->client_name ?? 'پروژه داخلی'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-soft-secondary text-secondary px-2.5 py-1 rounded-2">
                                        <?php echo sanitize($project->category_name ?? 'بدون دسته‌بندی'); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress progress-ultra flex-grow-1" title="<?php echo "{$project->completed_tasks} از {$project->total_tasks} وظیفه انجام شده"; ?>">
                                            <div class="progress-bar progress-bar-ultra" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="fw-bold small text-dark" style="min-width: 35px;"><?php echo $progress; ?>%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-soft-primary text-primary px-3 py-1.5 rounded-pill small fw-medium">
                                        <?php echo sanitize($project->status); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_project&id=<?php echo $project->id; ?>" class="action-btn bg-soft-info text-info" title="مشاهده و بورد کانبان">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=projects_edit&id=<?php echo $project->id; ?>" class="action-btn bg-soft-warning text-warning" title="ویرایش پروژه">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=projects_delete&id=<?php echo $project->id; ?>" method="POST" class="d-inline">
                                            <button type="submit" class="action-btn bg-soft-danger text-danger border-0" title="حذف" onclick="return confirm('آیا از حذف این پروژه مطمئن هستید؟');">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                                <p class="mb-0">هیچ پروژه‌ای یافت نشد.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>