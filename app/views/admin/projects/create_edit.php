<?php

$isEdit = isset($project) && $project !== null;
// ✅ اصلاح action برای اشاره به مسیر صحیح
$formAction = $isEdit 
    ? APP_URL . '/index.php?page=admin&action=projects_update&id=' . $project->id 
    : APP_URL . '/index.php?page=admin&action=projects_store';
$pageTitle = $isEdit ? 'ویرایش پروژه' : 'ایجاد پروژه جدید';
?>

<h1 class="mb-4"><?php echo $pageTitle; ?></h1>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?php echo $formAction; ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">نام پروژه <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo sanitize($project->name ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="client_id" class="form-label">مشتری (اختیاری)</label>
                <select class="form-select" id="client_id" name="client_id">
                    <option value="">پروژه داخلی (بدون مشتری)</option>
                    <?php foreach($clients as $client): ?>
                        <option value="<?php echo $client->id; ?>" <?php echo ($isEdit && isset($project->client_id) && $project->client_id == $client->id) ? 'selected' : ''; ?>>
                            <?php echo sanitize($client->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">توضیحات پروژه</label>
                <textarea class="form-control" id="description" name="description" rows="5"><?php echo sanitize($project->description ?? ''); ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">تاریخ شروع</label>
                    <input type="text" class="form-control persian-datepicker" name="start_date" value="<?php echo $start_date_jalali ?? ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="due_date" class="form-label">تاریخ تحویل</label>
                    <input type="text" class="form-control persian-datepicker" name="due_date" value="<?php echo $end_date_jalali ?? ''; ?>">
                </div>
            </div>

             <div class="mb-3">
                 <label for="status" class="form-label">وضعیت پروژه <span class="text-danger">*</span></label>
                <select class="form-select" id="status" name="status" required>
                    <option value="not_started" <?php echo ($isEdit && $project->status == 'not_started') ? 'selected' : ''; ?>>شروع نشده</option>
                    <option value="in_progress" <?php echo ($isEdit && $project->status == 'in_progress') ? 'selected' : ''; ?>>در حال انجام</option>
                    <option value="on_hold" <?php echo ($isEdit && $project->status == 'on_hold') ? 'selected' : ''; ?>>متوقف شده</option>
                    <option value="canceled" <?php echo ($isEdit && $project->status == 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
                    <option value="finished" <?php echo ($isEdit && $project->status == 'finished') ? 'selected' : ''; ?>>تکمیل شده</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">ذخیره پروژه</button>
            <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=projects" class="btn btn-secondary">بازگشت</a>
        </form>
    </div>
</div>