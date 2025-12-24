<?php
// app/views/employee/projects/index.php - داشبورد پروژه‌های کارمند (نسخه نهایی)
// این فایل شامل نمایش پروژه‌ها و وظایف، به همراه چک‌لیست‌ها با طراحی زیبا است.

global $auth;
?>

<style>
    /* Custom styles for a modern and clean look */
    .project-card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.2s ease-in-out;
        background-color: #fff;
    }
    .project-card:hover {
        transform: translateY(-5px);
    }
    .project-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
        border-radius: 15px 15px 0 0;
    }
    .project-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #343a40;
    }
    .task-list-item {
        border-bottom: 1px solid #e9ecef;
        padding: 1.5rem;
    }
    .task-list-item:last-child {
        border-bottom: none;
    }
    .task-title-text {
        font-size: 1.1rem;
        font-weight: 600;
        color: #495057;
    }
    .task-meta-text {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 5px;
    }
    .checklist-section {
        margin-top: 1rem;
        border-top: 1px solid #f1f3f5;
        padding-top: 1rem;
    }
    .checklist-title {
        font-size: 1rem;
        font-weight: 600;
        color: #495057;
    }
    .checklist-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .checklist-item .form-check-label {
        flex-grow: 1;
        margin-right: 0.5rem;
        cursor: pointer;
    }
    .text-decoration-line-through {
        text-decoration: line-through;
        color: #adb5bd !important;
    }
</style>

<h1 class="h3 mb-4 text-gray-800"><?php echo sanitize($title); ?></h1>

<?php FlashMessage::display(); ?>

<?php if (empty($projects)): ?>
    <div class="alert alert-info border-0 shadow-sm">در حال حاضر شما در هیچ پروژه‌ای عضو نیستید.</div>
<?php else: ?>
    <div class="accordion" id="projectsAccordion">
        <?php foreach ($projects as $index => $project): ?>
            <div class="card project-card mb-4">
                <div class="card-header project-header" id="heading-<?php echo $project->id; ?>">
                    <button class="btn btn-link w-100 text-end d-flex align-items-center" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $project->id; ?>" aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                        <i class="fas fa-project-diagram me-2 fa-lg text-primary"></i>
                        <span class="project-title"><?php echo sanitize($project->name); ?></span>
                        <span class="badge bg-primary ms-auto me-2">
                            <?php echo count($project->tasks); ?> وظیفه
                        </span>
                    </button>
                </div>
                <div id="collapse-<?php echo $project->id; ?>" class="collapse <?php echo $index === 0 ? 'show' : ''; ?>" aria-labelledby="heading-<?php echo $project->id; ?>" data-bs-parent="#projectsAccordion">
                    <div class="card-body">
                        <?php if (empty($project->tasks)): ?>
                            <p class="text-muted text-center py-4">هیچ وظیفه‌ای برای شما در این پروژه تعریف نشده است.</p>
                        <?php else: ?>
                            <ul class="list-unstyled">
                                <?php foreach ($project->tasks as $task): ?>
                                    <li class="task-list-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                                        <div class="task-details mb-3 mb-md-0">
                                            <span class="task-title-text d-block"><?php echo sanitize($task->title); ?></span>
                                            <span class="task-meta-text">
                                                تاریخ سررسید: <?php echo $task->due_date ? jdate('Y/m/d', strtotime($task->due_date)) : 'ندارد'; ?>
                                            </span>
                                        </div>

                                        <div class="checklist-section w-100">
                                            <h6 class="checklist-title">چک‌لیست:</h6>
                                            <?php if (!empty($task->checklist_items)): ?>
                                                <ul class="list-unstyled">
                                                    <?php foreach ($task->checklist_items as $item): ?>
                                                        <li class="checklist-item" id="checklist-item-<?php echo $item->id; ?>">
                                                            <div class="form-check">
                                                                <input class="form-check-input checklist-toggle" type="checkbox" data-item-id="<?php echo $item->id; ?>" <?php echo $item->is_completed ? 'checked' : ''; ?>>
                                                                <label class="form-check-label <?php echo $item->is_completed ? 'text-decoration-line-through' : ''; ?>">
                                                                    <?php echo htmlspecialchars($item->item_text); ?>
                                                                </label>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <small class="text-muted">هیچ آیتم چک‌لیستی برای این وظیفه وجود ندارد.</small>
                                            <?php endif; ?>
                                        </div>

                                        <div class="task-actions mt-3 mt-md-0">
                                            <form action="<?php echo APP_URL; ?>/index.php?page=employee&action=update_task_status" method="POST" class="d-flex align-items-center">
                                                <input type="hidden" name="task_id" value="<?php echo $task->id; ?>">
                                                <select name="status" class="form-select form-select-sm me-2">
                                                    <option value="todo" <?php echo $task->status === 'todo' ? 'selected' : ''; ?>>برای انجام</option>
                                                    <option value="in_progress" <?php echo $task->status === 'in_progress' ? 'selected' : ''; ?>>در حال انجام</option>
                                                    <option value="done" <?php echo $task->status === 'done' ? 'selected' : ''; ?>>انجام شده</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm">ذخیره</button>
                                            </form>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
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
            const label = this.closest('.checklist-item').querySelector('.form-check-label');

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
});
</script>