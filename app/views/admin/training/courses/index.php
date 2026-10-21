<?php
// app/views/admin/training/courses/index.php - نسخه اولترا پرمیوم
$courses = $courses ?? [];
?>

<style>
    .courses-card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .table-premium {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-premium th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-premium td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #1e293b;
        vertical-align: middle;
    }

    .table-premium tr:hover td {
        background-color: #f8fafc;
    }

    .btn-create-course {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 12px;
        padding: 0.65rem 1.25rem;
        font-weight: 700;
        font-size: 0.875rem;
        box-shadow: 0 8px 20px rgba(79, 70, 229, 0.25);
        transition: all 0.25s ease;
        cursor: pointer;
        text-decoration: none !important;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-create-course:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(79, 70, 229, 0.35);
    }

    .modal-content-premium {
        border-radius: 24px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }

    .modal-header-premium {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .form-control-premium {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.65rem 0.9rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-control-premium:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">مدیریت دوره‌های آموزشی سازمان</h1>
        <p class="text-muted small mb-0">تعریف، برنامه‌ریزی و ساماندهی دوره‌های ارتقای مهارت‌های شغلی کارمندان</p>
    </div>

    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
            <i class="fas fa-print"></i>
            <span>چاپ لیست</span>
        </button>
        <button type="button" class="btn-create-course" data-bs-toggle="modal" data-bs-target="#addCourseModal">
            <i class="fas fa-plus"></i>
            <span>افزودن دوره جدید</span>
        </button>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="courses-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fas fa-graduation-cap text-indigo-600" style="color: #4f46e5;"></i>
            <span>لیست دوره‌های آموزشی تعریف‌شده</span>
        </h6>
        <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold" style="background:#e0e7ff; color:#4338ca;">
            تعداد دوره‌ها: <?php echo count($courses); ?>
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table-premium">
                <thead>
                    <tr>
                        <th style="width: 220px;">عنوان دوره</th>
                        <th>توضیحات و محتوای آموزشی</th>
                        <th>مخاطبان هدف</th>
                        <th class="text-center" style="width: 130px;">تاریخ ثبت</th>
                        <th class="text-center no-print" style="width: 120px;">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 bg-light p-2 text-indigo-600 border" style="color: #4f46e5;">
                                            <i class="fas fa-book-open"></i>
                                        </div>
                                        <span><?php echo sanitize($course->course_title ?? ''); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-secondary small d-block text-truncate" style="max-width: 320px;">
                                        <?php echo sanitize($course->description ?? '---'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                        $audiences = array_filter(explode(',', $course->target_audience ?? ''));
                                        if (!empty($audiences)):
                                            foreach ($audiences as $aud): 
                                    ?>
                                                <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 mb-1 font-monospace">
                                                    <i class="fas fa-user-tag text-muted me-1"></i><?php echo trim(sanitize($aud)); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted small">عمومی (همه پرسنل)</span>
                                        <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark font-monospace border">
                                        <?php echo !empty($course->created_at) ? sanitize(jdate('Y/m/d', strtotime($course->created_at))) : '---'; ?>
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-primary rounded-2 px-2 py-1 me-1" title="ویرایش دوره">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=delete_training_course&id=<?php echo $course->id ?? 0; ?>" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این دوره اطمینان دارید؟');">
                                            <button type="submit" class="btn btn-outline-danger rounded-2 px-2 py-1" title="حذف دوره">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-graduation-cap fs-2 mb-2 d-block opacity-40"></i>
                                هیچ دوره آموزشی ثبت نشده است.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal افزودن دوره جدید -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-premium">
            <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=store_training_course" method="POST">
                <div class="modal-header modal-header-premium">
                    <h5 class="modal-title fw-bold text-dark fs-6 d-flex align-items-center gap-2" id="addCourseModalLabel">
                        <i class="fas fa-plus-circle text-indigo-600" style="color: #4f46e5;"></i>
                        <span>تعریف دوره آموزشی جدید</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="course_title" class="form-label fw-bold text-secondary small">عنوان دوره آموزشی:</label>
                        <input type="text" class="form-control form-control-premium" id="course_title" name="course_title" placeholder="مثال: مدیریت زمان و بهره‌وری شغلی" required>
                    </div>

                    <div class="mb-3">
                        <label for="target_audience" class="form-label fw-bold text-secondary small">مخاطبان هدف (با کاما جدا کنید):</label>
                        <input type="text" class="form-control form-control-premium" id="target_audience" name="target_audience" placeholder="مثلاً: employee, manager, developer">
                        <small class="text-muted d-block mt-1" style="font-size:0.75rem;">نقش‌های شغلی که این دوره برای آن‌ها الزام یا پیشنهاد شده است.</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold text-secondary small">توضیحات و سرفصل‌ها:</label>
                        <textarea class="form-control form-control-premium" id="description" name="description" rows="4" placeholder="شرح خلاصه سرفصل‌های آموزشی و خروجی دوره..."></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 p-3">
                    <button type="button" class="btn btn-light border btn-sm rounded-3 px-3 fw-bold" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" class="btn btn-create-course py-2 px-4">
                        <i class="fas fa-check"></i>
                        <span>ذخیره و ثبت دوره</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>