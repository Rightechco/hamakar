<?php
// app/views/admin/projects/index.php - نسخه نهایی با باکس‌های وضعیت
global $auth;
$projects = $projects ?? [];
$categories = $categories ?? [];
$projectSummary = $projectSummary ?? []; // ✅ دریافت متغیر جدید
?>
<style>
    .status-card {
        border-radius: 10px;
        color: #fff;
        padding: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .status-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }
    .status-card .icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
    .status-card h4 {
        font-size: 1.25rem;
        margin-top: 1rem;
        font-weight: 600;
    }
    .status-card p {
        font-size: 2.5rem;
        font-weight: bold;
        line-height: 1;
    }
    .bg-primary-light { background-color: #5a74e4; }
    .bg-warning-light { background-color: #f7a01d; }
    .bg-success-light { background-color: #1cc88a; }
    .bg-danger-light { background-color: #e74a3b; }
    .bg-info-light { background-color: #36b9cc; }
</style>

<h1 class="mb-4">مدیریت پروژه‌ها</h1>

<div class="row mb-4 g-4">
    <div class="col-md-2 col-sm-6">
        <div class="status-card bg-primary-light text-center">
            <i class="fas fa-list-alt icon"></i>
            <h4>کل پروژه‌ها</h4>
            <p><?php echo number_format($projectSummary['total'] ?? 0); ?></p>
        </div>
    </div>
    <div class="col-md-2 col-sm-6">
        <div class="status-card bg-info-light text-center">
            <i class="fas fa-clock icon"></i>
            <h4>در حال انجام</h4>
            <p><?php echo number_format($projectSummary['in_progress'] ?? 0); ?></p>
        </div>
    </div>
    <div class="col-md-2 col-sm-6">
        <div class="status-card bg-success-light text-center">
            <i class="fas fa-check-circle icon"></i>
            <h4>تکمیل شده</h4>
            <p><?php echo number_format($projectSummary['finished'] ?? 0); ?></p>
        </div>
    </div>
    <div class="col-md-2 col-sm-6">
        <div class="status-card bg-warning-light text-center">
            <i class="fas fa-pause-circle icon"></i>
            <h4>متوقف شده</h4>
            <p><?php echo number_format($projectSummary['on_hold'] ?? 0); ?></p>
        </div>
    </div>
    <div class="col-md-2 col-sm-6">
        <div class="status-card bg-secondary text-center">
            <i class="fas fa-hourglass-start icon"></i>
            <h4>شروع نشده</h4>
            <p><?php echo number_format($projectSummary['not_started'] ?? 0); ?></p>
        </div>
    </div>
    <div class="col-md-2 col-sm-6">
        <div class="status-card bg-danger-light text-center">
            <i class="fas fa-times-circle icon"></i>
            <h4>لغو شده</h4>
            <p><?php echo number_format($projectSummary['canceled'] ?? 0); ?></p>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">لیست پروژه‌ها</h5>
        <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=projects_create" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> ایجاد پروژه جدید
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>نام پروژه</th>
                        <th>مشتری</th>
                        <th>دسته‌بندی</th> <th style="width: 20%;">پیشرفت</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($projects)): ?>
                        <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><strong><?php echo sanitize($project->name); ?></strong></td>
                                <td><?php echo sanitize($project->client_name ?? 'پروژه داخلی'); ?></td>
                                <td><?php echo sanitize($project->category_name ?? 'بدون دسته‌بندی'); ?></td> <td>
                                    <?php $progress = ($project->total_tasks > 0) ? round(($project->completed_tasks / $project->total_tasks) * 100) : 0; ?>
                                    <div class="progress" title="<?php echo "{$project->completed_tasks} از {$project->total_tasks} وظیفه انجام شده"; ?>">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $progress; ?>%</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-primary"><?php echo sanitize($project->status); ?></span>
                                </td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=view_project&id=<?php echo $project->id; ?>" class="btn btn-sm btn-info" title="مشاهده و مدیریت">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=projects_edit&id=<?php echo $project->id; ?>" class="btn btn-sm btn-warning" title="ویرایش">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=projects_delete&id=<?php echo $project->id; ?>" method="POST" class="d-inline-block">
                                        <button type="button" class="btn btn-sm btn-danger" data-confirm-delete title="حذف">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">هیچ پروژه‌ای یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>