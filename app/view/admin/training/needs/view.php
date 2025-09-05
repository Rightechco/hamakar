
<?php
// app/views/admin/training/needs/view.php
?>
<h1 class="mb-4">بررسی نیازسنجی: <?php echo sanitize($trainingNeed->employee_name); ?></h1>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">پاسخ کارمند</h6></div>
            <div class="card-body">
                <p><strong>نقاط قوت:</strong> <?php echo nl2br(sanitize($trainingNeed->strengths)); ?></p>
                <hr>
                <p><strong>نقاط ضعف:</strong> <?php echo nl2br(sanitize($trainingNeed->weaknesses)); ?></p>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">تحلیل و پیشنهاد مدیر</h6></div>
            <div class="card-body">
                <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=process_training_need&id=<?php echo $trainingNeed->id; ?>" method="POST">
                    <div class="mb-3">
                        <label for="development_areas" class="form-label">زمینه‌های توسعه پیشنهادی:</label>
                        <textarea name="development_areas" id="development_areas" rows="4" class="form-control" required><?php echo sanitize($trainingNeed->development_areas ?? ''); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="course_suggestions" class="form-label">دوره‌های آموزشی پیشنهادی:</label>
                        <select name="course_suggestions[]" id="course_suggestions" class="form-select" multiple>
                            <?php foreach($courses as $course): ?>
                                <option value="<?php echo $course->id; ?>"><?php echo sanitize($course->course_title); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">وضعیت:</label>
                        <select name="status" id="status" class="form-select">
                            <option value="approved">تایید شده</option>
                            <option value="rejected">رد شده</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">ثبت نهایی</button>
                </form>
            </div>
        </div>
    </div>
</div>
