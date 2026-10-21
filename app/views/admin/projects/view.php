<?php
// app/views/admin/projects/view.php - داشبورد مشاهده و مدیریت پروژه (اولترا پرمیوم)

global $auth;

$start_date_jalali = !empty($project->start_date_jalali) ? $project->start_date_jalali : 'ثبت نشده';
$due_date_jalali = !empty($project->due_date_jalali) ? $project->due_date_jalali : 'ثبت نشده';
$progress = $progress ?? 0;

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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vazirmatn@33.0.3/Vazirmatn-font-face.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    :root {
        --primary-indigo: #4f46e5;
        --surface-bg: #ffffff;
        --border-color: #e2e8f0;
        --radius-lg: 20px;
        --radius-md: 12px;
        --shadow-subtle: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.02);
    }

    body, .project-view-wrapper {
        font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #0f172a;
        direction: rtl;
    }

    /* Premium Header Banner */
    .project-header-banner {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
        color: #ffffff;
        border-radius: var(--radius-lg);
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 25px -5px rgba(49, 46, 129, 0.2);
        margin-bottom: 2rem;
    }

    .project-header-banner::after {
        content: '';
        position: absolute;
        bottom: 0; right: 0; left: 0;
        height: 5px;
        background: linear-gradient(90deg, #6366f1 0%, #10b981 100%);
    }

    .header-badge {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 6px 16px;
        border-radius: 9999px;
        font-size: 0.85rem;
        color: #ffffff;
    }

    /* Cards */
    .ultra-card {
        background: var(--surface-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-subtle);
        margin-bottom: 1.5rem;
    }

    .ultra-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Kanban Column Styling */
    .kanban-column {
        background-color: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 14px;
        height: 100%;
    }

    .kanban-column-header {
        padding: 10px 14px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.875rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header-todo { background: #f1f5f9; color: #475569; }
    .header-progress { background: #fef3c7; color: #b45309; }
    .header-review { background: #e0f2fe; color: #0369a1; }
    .header-done { background: #dcfce7; color: #15803d; }

    /* Task Card Item */
    .task-item-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
    }

    .task-item-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.06);
        border-color: #cbd5e1;
    }

    .task-title {
        font-weight: 700;
        font-size: 0.925rem;
        color: #1e293b;
    }

    /* Form Inputs & Datepicker */
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid var(--border-color);
        padding: 0.6rem 0.9rem;
        font-size: 0.9rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.12);
    }

    .input-group-text.pdp-trigger {
        border-radius: 0 10px 10px 0;
        border: 1px solid var(--border-color);
        background-color: #f8fafc;
        cursor: pointer;
    }

    /* Checklist Styling */
    .checklist-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 4px 0;
        font-size: 0.85rem;
    }

    .checklist-item-row .btn-delete {
        opacity: 0;
        transition: opacity 0.2s;
    }

    .checklist-item-row:hover .btn-delete {
        opacity: 1;
    }
</style>

<div class="project-view-wrapper py-3">
    <!-- Top Banner -->
    <div class="project-header-banner d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="header-badge"><i class="fas fa-folder-open me-1.5"></i> پروژه فعال</span>
                <span class="header-badge"><i class="fas fa-tasks me-1.5"></i> پیشرفت: <?php echo $progress; ?>%</span>
            </div>
            <h2 class="fw-bold mb-2 text-white"><?php echo sanitize($project->name); ?></h2>
            <p class="text-white-50 mb-3 small" style="max-width: 650px;"><?php echo sanitize($project->description ?? 'بدون توضیحات تکمیلی'); ?></p>
            
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <span class="header-badge"><i class="far fa-calendar-alt me-1.5"></i>شروع: <?php echo $start_date_jalali; ?></span>
                <span class="header-badge"><i class="far fa-calendar-check me-1.5"></i>تحویل: <?php echo $due_date_jalali; ?></span>
            </div>
        </div>
        <div>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=projects" class="btn btn-light rounded-pill px-4 btn-sm fw-bold shadow-sm">
                <i class="fas fa-arrow-right me-1.5"></i> بازگشت به لیست
            </a>
        </div>
    </div>

    <!-- Announcements Section -->
    <?php if (!empty($announcements)): ?>
        <div class="ultra-card border-start border-4 border-info p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-bullhorn text-info me-2"></i>اطلاعیه‌های پروژه</h6>
            <?php foreach ($announcements as $announcement): ?>
                <div class="p-3 bg-light rounded-3 mb-2">
                    <h6 class="fw-bold text-dark fs-6 mb-1"><?php echo htmlspecialchars($announcement->title); ?></h6>
                    <p class="mb-2 text-muted small"><?php echo $announcement->body; ?></p>
                    <span class="text-muted fs-7"><i class="far fa-clock me-1"></i><?php echo jdate('Y/m/d H:i', strtotime($announcement->created_at)); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Main Task Area (Left 8 Cols) -->
        <div class="col-lg-8">
            <div class="ultra-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold text-dark mb-0 fs-6"><i class="fas fa-tasks me-2 text-primary"></i>مدیریت وظایف و بورد کانبان</h6>
                </div>

                <!-- Add Task Form -->
                <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=projects_create_task&id=<?php echo $project->id; ?>" method="POST" class="p-3 bg-light rounded-3 mb-4">
                    <h6 class="fw-bold small text-dark mb-3"><i class="fas fa-plus-circle text-success me-1.5"></i>تعریف وظیفه جدید</h6>
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label for="task_title" class="form-label small text-muted mb-1">عنوان وظیفه <span class="text-danger">*</span></label>
                            <input type="text" id="task_title" name="title" class="form-control" placeholder="عنوان کار را بنویسید..." required>
                        </div>
                        <div class="col-md-6">
                            <label for="task_assignee" class="form-label small text-muted mb-1">مسئول اجرا</label>
                            <select name="assigned_to_user_id" id="task_assignee" class="form-select">
                                <option value="">بدون مسئول (عمومی)</option>
                                <?php foreach ($all_users as $user): ?>
                                    <option value="<?php echo $user->id; ?>"><?php echo sanitize($user->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-md-4">
                            <label for="task_due_date" class="form-label small text-muted mb-1">تاریخ سررسید</label>
                            <div class="input-group">
                                <span class="input-group-text pdp-trigger"><i class="far fa-calendar-alt text-muted"></i></span>
                                <input type="text" id="task_due_date" name="due_date" class="form-control pdp-input border-start-0" placeholder="YYYY/MM/DD" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="due_in_days" class="form-label small text-muted mb-1">مهلت (روز)</label>
                            <input type="number" id="due_in_days" name="due_in_days" class="form-control" placeholder="مثلاً: 5">
                        </div>
                        <div class="col-md-4">
                            <label for="due_in_hours" class="form-label small text-muted mb-1">مهلت (ساعت)</label>
                            <input type="number" id="due_in_hours" name="due_in_hours" class="form-control" placeholder="مثلاً: 8">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="task_notes" class="form-label small text-muted mb-1">یادداشت / توضیحات</label>
                        <textarea id="task_notes" name="notes" class="form-control" rows="2" placeholder="جزئیات وظیفه..."></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success btn-sm px-4 rounded-2"><i class="fas fa-plus me-1"></i>ثبت وظیفه</button>
                    </div>
                </form>

                <!-- Kanban Columns -->
                <div class="row g-3">
                    <?php 
                        $task_statuses = [
                            'todo' => ['title' => 'برای انجام', 'class' => 'header-todo'],
                            'in_progress' => ['title' => 'در حال انجام', 'class' => 'header-progress'],
                            'review' => ['title' => 'در حال بررسی', 'class' => 'header-review'],
                            'done' => ['title' => 'انجام شده', 'class' => 'header-done'],
                        ];
                    ?>
                    <?php foreach ($task_statuses as $status_key => $status_info): ?>
                        <div class="col-md-3">
                            <div class="kanban-column">
                                <div class="kanban-column-header <?php echo $status_info['class']; ?>">
                                    <span><?php echo $status_info['title']; ?></span>
                                    <span class="badge bg-white text-dark rounded-pill"><?php echo count($tasks_by_status[$status_key]); ?></span>
                                </div>
                                <div class="kanban-body">
                                    <?php if (!empty($tasks_by_status[$status_key])): ?>
                                        <?php foreach ($tasks_by_status[$status_key] as $task): ?>
                                            <div class="task-item-card">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <span class="task-title <?php echo ($task->status === 'done') ? 'text-decoration-line-through text-muted' : ''; ?>">
                                                        <?php echo sanitize($task->title); ?>
                                                    </span>
                                                </div>
                                                <div class="small text-muted mb-2">
                                                    <div><i class="far fa-user me-1"></i><?php echo sanitize($task->assignee_name ?? 'بدون مسئول'); ?></div>
                                                    <?php if (!empty($task->due_date)): ?>
                                                        <div><i class="far fa-clock me-1"></i><?php echo jdate('Y/m/d', strtotime($task->due_date)); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <!-- Checklist Section -->
                                                <div class="border-top pt-2 mt-2">
                                                    <span class="fw-bold fs-7 text-dark d-block mb-1">چک‌لیست:</span>
                                                    <ul class="list-unstyled mb-2">
                                                        <?php if (!empty($task->checklist_items)): ?>
                                                            <?php foreach ($task->checklist_items as $item): ?>
                                                                <li class="checklist-item-row" id="checklist-item-<?php echo $item->id; ?>">
                                                                    <div class="form-check m-0">
                                                                        <input class="form-check-input checklist-toggle" type="checkbox" data-item-id="<?php echo $item->id; ?>" <?php echo $item->is_completed ? 'checked' : ''; ?>>
                                                                        <label class="form-check-label small <?php echo $item->is_completed ? 'text-decoration-line-through text-muted' : ''; ?>">
                                                                            <?php echo htmlspecialchars($item->item_text); ?>
                                                                        </label>
                                                                    </div>
                                                                    <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteChecklistItem" method="POST" class="d-inline delete-checklist-item-form">
                                                                        <input type="hidden" name="item_id" value="<?php echo $item->id; ?>">
                                                                        <button type="submit" class="btn btn-link text-danger p-0 btn-delete" title="حذف">
                                                                            <i class="fas fa-times"></i>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <li class="text-muted fs-7">آیتمی ندارد.</li>
                                                        <?php endif; ?>
                                                    </ul>
                                                    
                                                    <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=addChecklistItem" method="POST" class="d-flex gap-1">
                                                        <input type="hidden" name="task_id" value="<?php echo $task->id; ?>">
                                                        <input type="text" name="item_text" class="form-control form-control-sm" placeholder="آیتم جدید..." required>
                                                        <button type="submit" class="btn btn-primary btn-sm px-2"><i class="fas fa-plus"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-4 text-muted fs-7">بدون وظیفه</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Info (Right 4 Cols) -->
        <div class="col-lg-4">
            <!-- Progress Card -->
            <div class="ultra-card p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-chart-line text-primary me-2"></i>پیشرفت کلی پروژه</h6>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted">درصد تکمیل:</span>
                    <strong class="text-primary fs-5"><?php echo $progress; ?>%</strong>
                </div>
                <div class="progress mb-3" style="height: 10px; border-radius: 9999px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $progress; ?>%" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="small lh-lg text-muted border-top pt-3">
                    <div class="d-flex justify-content-between"><span>تاریخ شروع:</span><strong class="text-dark"><?php echo $start_date_jalali; ?></strong></div>
                    <div class="d-flex justify-content-between"><span>تاریخ تحویل:</span><strong class="text-dark"><?php echo $due_date_jalali; ?></strong></div>
                    <div class="d-flex justify-content-between"><span>وضعیت:</span><span class="badge bg-soft-primary px-3 py-1 rounded-pill"><?php echo sanitize($project->status); ?></span></div>
                </div>
            </div>

            <!-- Members Card -->
            <div class="ultra-card p-4">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-users text-primary me-2"></i>اعضای تیم پروژه</h6>
                <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=projects_add_member" method="POST" class="mb-3 border-bottom pb-3">
                    <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
                    <div class="mb-2">
                        <select name="user_id" class="form-select form-select-sm" required>
                            <option value="">افزودن عضو جدید...</option>
                            <?php foreach ($all_users as $user): ?>
                                <option value="<?php echo $user->id; ?>"><?php echo sanitize($user->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-group input-group-sm">
                        <input type="text" name="role" class="form-control" placeholder="نقش عضو (مثلا: طراح)" required>
                        <button type="submit" class="btn btn-primary px-3">افزودن</button>
                    </div>
                </form>

                <div class="list-group list-group-flush">
                    <?php if(!empty($members)): ?>
                        <?php foreach ($members as $member): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-soft rounded-circle bg-light p-2 me-2 text-center" style="width: 32px; height: 32px;">
                                        <i class="fas fa-user text-muted small"></i>
                                    </div>
                                    <span class="fw-bold small text-dark"><?php echo sanitize($member->name); ?></span>
                                </div>
                                <span class="badge bg-light text-muted fw-normal fs-7"><?php echo sanitize($member->role); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted small py-2">هیچ عضوی اضافه نشده است.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Checklist Toggle
    document.querySelectorAll('.checklist-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const isCompleted = this.checked ? 1 : 0;
            const label = this.closest('.checklist-item-row').querySelector('.form-check-label');

            fetch('<?php echo APP_URL; ?>/index.php?page=admin&action=toggleChecklistItem', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `item_id=${itemId}&is_completed=${isCompleted}`
            })
            .then(response => {
                if (response.ok) {
                    if (isCompleted) {
                        label.classList.add('text-decoration-line-through', 'text-muted');
                    } else {
                        label.classList.remove('text-decoration-line-through', 'text-muted');
                    }
                } else { this.checked = !this.checked; }
            });
        });
    });

    // Delete Checklist Item
    document.querySelectorAll('.delete-checklist-item-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (confirm('آیا از حذف این آیتم مطمئن هستید؟')) {
                const itemId = this.querySelector('input[name="item_id"]').value;
                fetch('<?php echo APP_URL; ?>/index.php?page=admin&action=deleteChecklistItem', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `item_id=${itemId}`
                })
                .then(response => {
                    if (response.ok) {
                        document.getElementById('checklist-item-' + itemId).remove();
                    }
                });
            }
        });
    });
});

// Persian Datepicker Loader & Click Handler
(function loadPersianDatePicker() {
    function initPicker() {
        if (typeof $ !== 'undefined' && $.fn && $.fn.pDatepicker) {
            $('.pdp-input').each(function() {
                var $input = $(this);
                $input.pDatepicker({
                    format: 'YYYY/MM/DD',
                    autoClose: true,
                    initialValue: false,
                    observer: true,
                    initialValueType: 'persian'
                });
            });

            $('.pdp-trigger, .pdp-input').on('click', function(e) {
                e.stopPropagation();
                var $input = $(this).hasClass('pdp-input') ? $(this) : $(this).siblings('.pdp-input');
                $input.focus();
            });
        } else { setTimeout(initPicker, 100); }
    }

    if (!document.getElementById('p-datepicker-css')) {
        var css = document.createElement('link'); css.id = 'p-datepicker-css'; css.rel = 'stylesheet';
        css.href = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css';
        document.head.appendChild(css);
    }

    if (typeof $ === 'undefined') {
        var jq = document.createElement('script'); jq.src = 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js';
        jq.onload = loadPDate; document.head.appendChild(jq);
    } else { loadPDate(); }

    function loadPDate() {
        if (typeof window.persianDate === 'undefined') {
            var pd = document.createElement('script'); pd.src = 'https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js';
            pd.onload = loadPDatePicker; document.head.appendChild(pd);
        } else { loadPDatePicker(); }
    }

    function loadPDatePicker() {
        if (typeof $.fn.pDatepicker === 'undefined') {
            var pdp = document.createElement('script'); pdp.src = 'https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js';
            pdp.onload = initPicker; document.head.appendChild(pdp);
        } else { initPicker(); }
    }
})();
</script>