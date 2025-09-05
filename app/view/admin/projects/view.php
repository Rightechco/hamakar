<?php
// app/views/admin/projects/view.php - داشبورد مشاهده پروژه (نسخه نهایی)

global $auth;

// آماده‌سازی تاریخ‌ها برای نمایش
$start_date_jalali = !empty($project->start_date_jalali) ? $project->start_date_jalali : 'ثبت نشده';
$due_date_jalali = !empty($project->due_date_jalali) ? $project->due_date_jalali : 'ثبت نشده';
$progress = $progress ?? 0;

// دسته‌بندی وظایف بر اساس وضعیت برای نمایش در ستون‌های مجزا
$tasks_by_status = [
    'todo' => [],
    'in_progress' => [],
    'review' => [],
    'done' => []
];
if (!empty($tasks)) {
    foreach ($tasks as $task) {
        if (isset($tasks_by_status[$task->status])) {
            $tasks_by_status[$task->status][] = $task;
        } else {
            $tasks_by_status['todo'][] = $task;
        }
    }
}
?>
<style>
    /* Modern Styles for Project View */
    .project-header-card {
        background: linear-gradient(to right, #4e73df, #6610f2);
        color: white;
        border-radius: 15px;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .project-header-card h1 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    .project-header-card .badge {
        font-size: 1rem;
        padding: 0.5em 1em;
        border-radius: 20px;
        background-color: rgba(255, 255, 255, 0.2);
    }
    .project-header-card .progress-bar {
        background-color: #28a745;
        border-radius: 50px;
    }
    .project-details-card, .task-board-card, .members-card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: none;
    }
    .task-board-card .card-body {
        padding: 1rem;
    }
    .task-column-header {
        border-radius: 10px;
        padding: 10px;
        color: white;
        margin-bottom: 1rem;
    }
    .task-column-header.bg-light {
        background-color: #f1f3f5 !important;
        color: #212529 !important;
    }
    .task-column-header.bg-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }
    .task-column-header.bg-info {
        background-color: #0dcaf0 !important;
        color: #212529 !important;
    }
    .task-column-header.bg-success {
        background-color: #198754 !important;
        color: #fff !important;
    }
    .task-item {
        background-color: white;
        border-radius: 8px;
        margin-bottom: 10px;
        padding: 15px;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 5px rgba(0,0,0,0.03);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .task-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .task-title {
        font-weight: 600;
        margin-bottom: 5px;
    }
    .task-meta {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 8px;
    }
    .task-action-buttons {
        opacity: 0;
        transition: opacity 0.2s;
    }
    .task-item:hover .task-action-buttons {
        opacity: 1;
    }
    .checklist-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .checklist-item .form-check-label {
        flex-grow: 1;
        margin-right: 0.5rem;
        cursor: pointer;
    }
    .checklist-item .delete-checklist-item {
        opacity: 0;
        transition: opacity 0.2s;
    }
    .checklist-item:hover .delete-checklist-item {
        opacity: 1;
    }
</style>

<div class="row mb-4">
    <div class="col-12">
        <div class="project-header-card d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-0 text-white">پروژه: <?php echo sanitize($project->name); ?></h1>
                <p class="text-white-50 fs-5 mt-2"><?php echo sanitize($project->description ?? ''); ?></p>
                <div class="d-flex align-items-center mt-3">
                    <span class="badge bg-light text-dark me-3"><i class="fas fa-calendar-alt me-2"></i>شروع: <?php echo $start_date_jalali; ?></span>
                    <span class="badge bg-light text-dark"><i class="fas fa-calendar-check me-2"></i>پایان: <?php echo $due_date_jalali; ?></span>
                </div>
            </div>
            <div>
                <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=projects" class="btn btn-light shadow-sm"><i class="fas fa-arrow-left me-2"></i>بازگشت</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="task-board-card card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">وظایف پروژه</h6>
            </div>
            <div class="card-body">
                <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=projects_create_task&id=<?php echo $project->id; ?>" method="POST" class="mb-4 pb-4 border-bottom">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="task_title" class="form-label">عنوان وظیفه جدید:</label>
                            <input type="text" id="task_title" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="task_assignee" class="form-label">مسئول وظیفه:</label>
                            <select name="assigned_to_user_id" id="task_assignee" class="form-select">
                                <option value="">بدون مسئول</option>
                                <?php foreach ($all_users as $user): ?>
                                    <option value="<?php echo $user->id; ?>"><?php echo sanitize($user->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="task_due_date" class="form-label">تاریخ سررسید:</label>
                            <input type="text" id="task_due_date" name="due_date" class="form-control persian-datepicker" placeholder="YYYY/MM/DD">
                        </div>
                        <div class="col-md-4">
                            <label for="due_in_days" class="form-label">مهلت (روز):</label>
                            <input type="number" id="due_in_days" name="due_in_days" class="form-control" placeholder="مثلاً: 5">
                        </div>
                        <div class="col-md-4">
                            <label for="due_in_hours" class="form-label">مهلت (ساعت):</label>
                            <input type="number" id="due_in_hours" name="due_in_hours" class="form-control" placeholder="مثلاً: 8">
                        </div>
                        <div class="col-12">
                            <label for="task_notes" class="form-label">یادداشت:</label>
                            <textarea id="task_notes" name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success mt-3"><i class="fas fa-plus me-2"></i>افزودن وظیفه</button>
                </form>

                <div class="row g-3">
                    <?php 
                        $task_statuses = [
                            'todo' => ['title' => 'برای انجام', 'class' => 'bg-light'],
                            'in_progress' => ['title' => 'در حال انجام', 'class' => 'bg-warning text-dark'],
                            'review' => ['title' => 'در حال بررسی', 'class' => 'bg-info text-dark'],
                            'done' => ['title' => 'انجام شده', 'class' => 'bg-success text-white'],
                        ];
                    ?>
                    <?php foreach ($task_statuses as $status_key => $status_info): ?>
                        <div class="col-md-3">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header py-2 <?php echo $status_info['class']; ?>">
                                    <h6 class="m-0 fw-bold"><?php echo $status_info['title']; ?></h6>
                                </div>
                                <div class="card-body card-task-list">
                                    <ul class="list-group list-group-flush">
                                        <?php if (!empty($tasks_by_status[$status_key])): ?>
                                            <?php foreach ($tasks_by_status[$status_key] as $task): ?>
                                                <li class="list-group-item task-item">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="task-title <?php echo ($task->status === 'done') ? 'text-decoration-line-through' : ''; ?>">
                                                            <?php echo sanitize($task->title); ?>
                                                        </span>
                                                        <div class="task-action-buttons">
                                                             <a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                                                             <a href="#" class="btn btn-sm btn-outline-info"><i class="fas fa-edit"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="task-meta mt-1">
                                                        <span class="text-muted">مسئول: <?php echo sanitize($task->assignee_name ?? 'بدون مسئول'); ?></span>
                                                        <br>
                                                        <?php if (!empty($task->due_date)): ?>
                                                            <span class="text-muted">سررسید: <?php echo jdate('Y/m/d', strtotime($task->due_date)); ?></span>
                                                        <?php endif; ?>
                                                        <?php if (!empty($task->notes)): ?>
                                                            <br>
                                                            <span class="text-muted">یادداشت: <?php echo nl2br(sanitize($task->notes)); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <div class="mt-3">
                                                        <h6 class="fw-bold fs-6">چک‌لیست:</h6>
                                                        <ul class="list-unstyled mb-0">
                                                            <?php if (!empty($task->checklist_items)): ?>
                                                                <?php foreach ($task->checklist_items as $item): ?>
                                                                    <li class="checklist-item" id="checklist-item-<?php echo $item->id; ?>">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input checklist-toggle" type="checkbox" data-item-id="<?php echo $item->id; ?>" <?php echo $item->is_completed ? 'checked' : ''; ?>>
                                                                            <label class="form-check-label <?php echo $item->is_completed ? 'text-decoration-line-through' : ''; ?>" for="checklist-item-<?php echo $item->id; ?>">
                                                                                <?php echo htmlspecialchars($item->item_text); ?>
                                                                            </label>
                                                                        </div>
                                                                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteChecklistItem" method="POST" class="d-inline-block delete-checklist-item-form">
                                                                            <input type="hidden" name="item_id" value="<?php echo $item->id; ?>">
                                                                            <button type="submit" class="btn btn-sm text-danger p-0 delete-checklist-item" title="حذف آیتم" onclick="return confirm('آیا از حذف این آیتم مطمئن هستید؟');">
                                                                                <i class="fas fa-trash-alt"></i>
                                                                            </button>
                                                                        </form>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <li class="text-muted small">چک‌لیستی برای این وظیفه وجود ندارد.</li>
                                                            <?php endif; ?>
                                                        </ul>
                                                        
                                                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=addChecklistItem" method="POST" class="d-flex mt-2">
                                                            <input type="hidden" name="task_id" value="<?php echo $task->id; ?>">
                                                            <input type="text" name="item_text" class="form-control form-control-sm me-2" placeholder="آیتم جدید" required>
                                                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></button>
                                                        </form>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="list-group-item text-center text-muted">هیچ وظیفه‌ای وجود ندارد.</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4 project-details-card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">پیشرفت پروژه</h6>
            </div>
            <div class="card-body">
                <h4 class="small font-weight-bold">تکمیل وظایف <span class="float-start"><?php echo $progress; ?>%</span></h4>
                <div class="progress mb-4" style="height: 10px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%" aria-valuenow="<?php echo $progress; ?>"></div>
                </div>
                <hr>
                <p><strong>تاریخ شروع:</strong> <?php echo $start_date_jalali; ?></p>
                <p><strong>تاریخ تحویل:</strong> <?php echo $due_date_jalali; ?></p>
                <p><strong>وضعیت:</strong> <span class="badge bg-info"><?php echo sanitize($project->status); ?></span></p>
            </div>
        </div>

        <div class="card shadow mb-4 members-card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">اعضای پروژه</h6>
            </div>
            <div class="card-body">
                <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=projects_add_member" method="POST" class="mb-3 border-bottom pb-3">
                    <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
                    <div class="input-group">
                        <select name="user_id" class="form-select" required>
                            <option value="">افزودن عضو جدید...</option>
                            <?php foreach ($all_users as $user): ?>
                                <option value="<?php echo $user->id; ?>"><?php echo sanitize($user->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-secondary btn-sm">افزودن</button>
                    </div>
                    <input type="text" name="role" class="form-control form-control-sm mt-2" placeholder="نقش عضو (مثلا: توسعه‌دهنده)" required>
                </form>
                
                <ul class="list-group list-group-flush">
                    <?php foreach ($members as $member): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo sanitize($member->name); ?>
                            <span class="badge bg-light text-dark"><?php echo sanitize($member->role); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // مدیریت تعامل با چک‌باکس‌ها
    document.querySelectorAll('.checklist-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const isCompleted = this.checked ? 1 : 0;
            const label = this.closest('.checklist-item').querySelector('.form-check-label');

            fetch('<?php echo APP_URL; ?>/index.php?page=admin&action=toggleChecklistItem', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `item_id=${itemId}&is_completed=${isCompleted}`
            })
            .then(response => {
                if (response.ok) {
                    if (isCompleted) {
                        label.classList.add('text-decoration-line-through');
                    } else {
                        label.classList.remove('text-decoration-line-through');
                    }
                } else {
                    this.checked = !this.checked;
                    alert('خطا در به‌روزرسانی آیتم.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !this.checked;
                alert('خطا در ارتباط با سرور.');
            });
        });
    });

    // مدیریت حذف آیتم چک‌لیست
    document.querySelectorAll('.delete-checklist-item-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('آیا از حذف این آیتم مطمئن هستید؟')) {
                const itemId = this.querySelector('input[name="item_id"]').value;
                
                fetch('<?php echo APP_URL; ?>/index.php?page=admin&action=deleteChecklistItem', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `item_id=${itemId}`
                })
                .then(response => {
                    if (response.ok) {
                        document.getElementById('checklist-item-' + itemId).remove();
                    } else {
                        alert('خطا در حذف آیتم.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('خطا در ارتباط با سرور.');
                });
            }
        });
    });
});
</script>