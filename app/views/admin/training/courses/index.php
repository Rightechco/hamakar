<?php
// app/views/admin/training/courses/index.php
// این فایل برای مدیریت دوره‌های آموزشی توسط ادمین است.
$courses = $courses ?? [];
?>
<style>
    .section-box {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .section-header {
        border-bottom: 2px solid #e0e0e0;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
    }
    .section-title {
        color: #34495e;
        font-weight: 700;
        font-size: 1.25rem;
    }
</style>

<h1 class="mb-4">مدیریت دوره‌های آموزشی</h1>

<div class="row">
    <div class="col-lg-12">
        <div class="section-box">
            <div class="section-header d-flex justify-content-between align-items-center">
                <span class="section-title">لیست دوره‌ها</span>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    <i class="fas fa-plus me-2"></i>افزودن دوره جدید
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead>
                        <tr>
                            <th>عنوان دوره</th>
                            <th>توضیحات</th>
                            <th>مخاطب هدف</th>
                            <th>تاریخ ثبت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?php echo sanitize($course->course_title); ?></td>
                                    <td><?php echo sanitize($course->description); ?></td>
                                    <td><?php echo sanitize($course->target_audience); ?></td>
                                    <td><?php echo jdate('Y/m/d', strtotime($course->created_at)); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info me-2"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">هیچ دوره آموزشی یافت نشد.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=store_training_course" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCourseModalLabel">افزودن دوره جدید</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="course_title" class="form-label">عنوان دوره:</label>
                        <input type="text" class="form-control" id="course_title" name="course_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">توضیحات:</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="target_audience" class="form-label">مخاطب هدف (با کاما جدا کنید):</label>
                        <input type="text" class="form-control" id="target_audience" name="target_audience" placeholder="مثلا: employee, developer">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                    <button type="submit" class="btn btn-primary">ذخیره دوره</button>
                </div>
            </form>
        </div>
    </div>
</div>