<?php
// app/views/employee/projects/index.php - نسخه اولترا پرمیوم و شاهکار
// نمایش پروژه‌ها، مدیریت وظایف تخصیصی و چک‌لیست‌های انیمیشن‌دار

global $auth;
$title = $title ?? 'مدیریت پروژه‌ها و وظایف من';
$projects = $projects ?? [];
?>

<style>
    .project-accordion-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        margin-bottom: 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .project-accordion-card:hover {
        box-shadow: 0 20px 35px -5px rgba(99, 102, 241, 0.1);
        border-color: #c7d2fe;
    }

    .project-header-btn {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: none;
        padding: 1.25rem 1.5rem;
        width: 100%;
        text-align: right;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .project-header-btn:not(.collapsed) {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        border-bottom: 1px solid #cbd5e1;
    }

    .project-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: #e0e7ff;
        color: #4338ca;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    .task-card-item {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.25s ease;
    }

    .task-card-item:hover {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .checklist-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 0.85rem;
    }

    .checklist-form-check .form-check-input {
        width: 1.15em;
        height: 1.15em;
        cursor: pointer;
        border-radius: 6px;
    }

    .checklist-form-check .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }

    .text-completed {
        text-decoration: line-through;
        color: #94a3b8 !important;
        transition: all 0.25s ease;
    }

    .status-select-sm {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 0.4rem 0.75rem;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .btn-save-status {
        background: #6366f1;
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 0.4rem 0.9rem;
        font-size: 0.85rem;
        font-weight: 700;
        transition: all 0.2s ease;
    }

    .btn-save-status:hover {
        background: #4f46e5;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1"><?php echo sanitize($title); ?></h1>
        <p class="text-muted small mb-0">مدیریت وظایف محوله، به‌روزرسانی وضعیت و تکمیل چک‌لیست‌های اجرایی</p>
    </div>
</div>

<?php FlashMessage::display(); ?>

<?php if (empty($projects)): ?>
    <div class="card shadow-sm border-0 rounded-4 p-5 text-center">
        <i class="fas fa-diagram-project fs-1 text-muted mb-3 opacity-40"></i>
        <h6 class="fw-bold text-dark mb-1">شما در حال حاضر در هیچ پروژه‌ای عضو نیستید.</h6>
        <p class="text-muted small mb-0">به محض افزودن شما به پروژه، وظایف در این صفحه قابل مشاهده خواهند بود.</p>
    </div>
<?php else: ?>
    <div class="accordion" id="projectsAccordion">
        <?php foreach ($projects as $index => $project): ?>
            <div class="project-accordion-card">
                <button class="project-header-btn <?php echo $index !== 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $project->id; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                    <div class="d-flex align-items-center gap-3">
                        <div class="project-icon-wrapper">
                            <i class="fas fa-diagram-project"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6"><?php echo sanitize($project->name); ?></h6>
                            <span class="small text-muted font-monospace">کد: #PRJ-<?php echo sanitize($project->id); ?></span>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold font-monospace" style="background:#e0e7ff; color:#4338ca;">
                            <i class="fas fa-tasks me-1"></i>
                            <?php echo count($project->tasks); ?> وظیفه
                        </span>
                        <i class="fas fa-chevron-down text-muted ms-2"></i>
                    </div>
                </button>

                <div id="collapse-<?php echo $project->id; ?>" class="collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#projectsAccordion">
                    <div class="p-4">
                        <?php if (empty($project->tasks)): ?>
                            <p class="text-muted text-center py-4 mb-0 small">
                                <i class="fas fa-circle-check fs-4 d-block mb-2 text-emerald-500"></i>
                                هیچ وظیفه‌ای برای شما در این پروژه تعریف نشده است.
                            </p>
                        <?php else: ?>
                            <?php foreach ($project->tasks as $task): ?>
                                <div class="task-card-item">
                                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-2">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                                                <i class="fas fa-check-double text-indigo-600" style="color:#6366f1;"></i>
                                                <span><?php echo sanitize($task->title); ?></span>
                                            </h6>
                                            <span class="text-muted small font-monospace">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                سررسید: <?php echo $task->due_date ? (function_exists('jdate') ? jdate('Y/m/d', strtotime($task->due_date)) : $task->due_date) : 'بدون زمان‌بندی'; ?>
                                            </span>
                                        </div>

                                        <!-- تغییر وضعیت -->
                                        <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=update_task_status" method="POST" class="d-flex align-items-center gap-2">
                                            <input type="hidden" name="task_id" value="<?php echo $task->id; ?>">
                                            <select name="status" class="form-select status-select-sm">
                                                <option value="todo" <?php echo $task->status === 'todo' ? 'selected' : ''; ?>>برای انجام</option>
                                                <option value="in_progress" <?php echo $task->status === 'in_progress' ? 'selected' : ''; ?>>در حال انجام</option>
                                                <option value="done" <?php echo $task->status === 'done' ? 'selected' : ''; ?>>انجام شده</option>
                                            </select>
                                            <button type="submit" class="btn-save-status">
                                                <span>ذخیره</span>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- چک‌لیست -->
                                    <div class="checklist-box">
                                        <strong class="d-block small fw-bold text-dark mb-2">
                                            <i class="fas fa-list-check me-1 text-indigo-600" style="color:#4f46e5;"></i>
                                            چک‌لیست زیرمجموعه:
                                        </strong>
                                        <?php if (!empty($task->checklist_items)): ?>
                                            <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                                                <?php foreach ($task->checklist_items as $item): ?>
                                                    <li id="checklist-item-<?php echo $item->id; ?>">
                                                        <div class="form-check checklist-form-check d-flex align-items-center gap-2">
                                                            <input class="form-check-input checklist-toggle" type="checkbox" data-item-id="<?php echo $item->id; ?>" <?php echo $item->is_completed ? 'checked' : ''; ?>>
                                                            <label class="form-check-label small fw-semibold <?php echo $item->is_completed ? 'text-completed' : 'text-dark'; ?>">
                                                                <?php echo htmlspecialchars($item->item_text); ?>
                                                            </label>
                                                        </div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <span class="text-muted small">هیچ آیتم چک‌لیستی برای این وظیفه ثبت نشده است.</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.checklist-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const isCompleted = this.checked ? 1 : 0;
            const label = this.closest('.checklist-form-check').querySelector('.form-check-label');

            fetch('<?php echo APP_URL; ?>/index.php?page=employee&action=toggleChecklistItem', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `item_id=${itemId}&is_completed=${isCompleted}`
            })
            .then(response => {
                if (response.ok) {
                    if (isCompleted) {
                        label.classList.add('text-completed');
                        label.classList.remove('text-dark');
                    } else {
                        label.classList.remove('text-completed');
                        label.classList.add('text-dark');
                    }
                } else {
                    this.checked = !this.checked;
                    alert('خطا در به‌روزرسانی وضعیت چک‌لیست.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.checked = !this.checked;
                alert('خطا در ارتباط با سرور.');
            });
        });
    });
});
</script>