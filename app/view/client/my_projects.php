<h1 class="mb-4">پروژه‌های من</h1>

<?php if (empty($projects)): ?>
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle fa-2x mb-2"></i>
        <p>شما در حال حاضر هیچ پروژه فعالی ندارید.</p>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($projects as $project): ?>
            <?php
                // محاسبه درصد پیشرفت پروژه
                $progress = ($project->total_tasks > 0) ? round(($project->completed_tasks / $project->total_tasks) * 100) : 0;
            ?>
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary"><?php echo htmlspecialchars($project->name); ?></h6>
                        <span class="badge bg-light text-dark"><?php echo htmlspecialchars($project->status); ?></span>
                    </div>
                    <div class="card-body">
                        <p class="card-text text-muted">
                            <?php echo !empty($project->description) ? htmlspecialchars(mb_substr($project->description, 0, 150) . '...') : 'توضیحاتی برای این پروژه ثبت نشده است.'; ?>
                        </p>
                        <hr>
                        <div class="mb-2">
                            <small>پیشرفت پروژه</small>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <?php echo $progress; ?>%
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span>تاریخ شروع: <?php echo jdate('Y/m/d', strtotime($project->start_date)); ?></span>
                            <span>سررسید: <?php echo !empty($project->due_date) ? jdate('Y/m/d', strtotime($project->due_date)) : 'نامشخص'; ?></span>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="<?php echo APP_URL . '/index.php?page=admin&action=view_project&id=' . $project->id; ?>" class="btn btn-outline-primary btn-sm">مشاهده جزئیات</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>