<?php
// app/views/admin/announcements/create_edit.php
$isEdit = isset($announcement);
?>
<style>
    .announcement-form-container {
        max-width: 950px;
        margin: 0 auto;
    }
    .fin-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .fin-card-header {
        padding: 1.25rem 1.5rem;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .fin-card-title {
        font-weight: 800;
        color: #0f172a;
        font-size: 1rem;
        margin: 0;
    }
    .form-control-premium, .form-select-premium {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 0.7rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }
    .form-control-premium:focus, .form-select-premium:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    }
    
    /* Target Roles Selector Styling */
    .role-card-checkbox {
        position: relative;
    }
    .role-card-checkbox input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    .role-card-label {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.85rem 1.1rem;
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .role-card-checkbox input[type="checkbox"]:checked + .role-card-label {
        background-color: #e0e7ff;
        border-color: #6366f1;
        color: #4338ca;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }

    /* Quill Editor Premium Customization */
    .ql-toolbar.ql-snow {
        border-radius: 14px 14px 0 0 !important;
        border-color: #cbd5e1 !important;
        background-color: #f8fafc !important;
        padding: 0.75rem !important;
    }
    .ql-container.ql-snow {
        border-radius: 0 0 14px 14px !important;
        border-color: #cbd5e1 !important;
        min-height: 260px;
        font-family: 'Vazirmatn', sans-serif !important;
        font-size: 0.925rem !important;
    }

    .btn-fin-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.75rem;
        font-weight: 700;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-fin-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99,102,241,0.35);
        color: #fff;
    }
    .btn-fin-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-fin-secondary:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
</style>

<div class="announcement-form-container">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1"><?php echo $isEdit ? 'ویرایش اطلاعیه' : 'ایجاد اطلاعیه جدید'; ?></h1>
            <p class="text-muted small mb-0">انتشار اخبار، اطلاعیه‌ها و پیام‌های عمومی برای کاربران سیستم</p>
        </div>
        <a href="index.php?page=admin&action=announcements_index" class="btn btn-fin-secondary d-inline-flex align-items-center gap-2">
            <i class="fas fa-arrow-right"></i>
            <span>بازگشت به لیست</span>
        </a>
    </div>

    <div class="fin-card">
        <div class="fin-card-header">
            <i class="fas fa-bullhorn text-indigo-500" style="color:#6366f1;"></i>
            <h6 class="fin-card-title"><?php echo $isEdit ? 'ویرایش مشخصات اطلاعیه' : 'فرم فرمول‌بندی اطلاعیه جدید'; ?></h6>
        </div>
        <div class="card-body p-4 p-md-5">
            <form action="index.php?page=admin&action=<?php echo $isEdit ? 'announcements_update&id=' . $announcement->id : 'announcements_store'; ?>" method="POST" id="announcement-form">
                
                <div class="row g-4 mb-4">
                    <div class="col-md-7">
                        <label for="title" class="form-label fw-bold text-secondary small">عنوان اطلاعیه:</label>
                        <input type="text" class="form-control form-control-premium" id="title" name="title" value="<?php echo htmlspecialchars($announcement->title ?? ''); ?>" placeholder="مثال: به‌روزرسانی جدید سامانه مالی" required>
                    </div>
                    
                    <div class="col-md-5">
                        <label for="project_id" class="form-label fw-bold text-secondary small">پروژه مرتبط (اختیاری):</label>
                        <select class="form-select form-select-premium" id="project_id" name="project_id">
                            <option value="">-- برای همه پروژه‌ها --</option>
                            <?php foreach ($projects as $project_item): ?>
                                <option value="<?php echo $project_item->id; ?>" 
                                    <?php echo ($isEdit && $announcement->project_id == $project_item->id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($project_item->name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary small">محتوا و متن اصلی اطلاعیه:</label>
                    <div id="editor-container"><?php echo $announcement->body ?? ''; ?></div>
                    <input type="hidden" name="body" id="announcement_body">
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary small mb-3">گیرندگان و مخاطبین هدف:</label>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="role-card-checkbox">
                                <input type="checkbox" name="target_roles[]" value="all" id="target_all" <?php echo (!$isEdit || strpos($announcement->target_roles, 'all') !== false) ? 'checked' : ''; ?>>
                                <label class="role-card-label" for="target_all">
                                    <i class="fas fa-users-viewfinder text-indigo-500"></i>
                                    <span>همه کاربران</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="role-card-checkbox">
                                <input type="checkbox" name="target_roles[]" value="admin" id="target_admin" <?php echo ($isEdit && strpos($announcement->target_roles, 'admin') !== false) ? 'checked' : ''; ?>>
                                <label class="role-card-label" for="target_admin">
                                    <i class="fas fa-user-shield text-emerald-500"></i>
                                    <span>مدیران</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="role-card-checkbox">
                                <input type="checkbox" name="target_roles[]" value="employee" id="target_employee" <?php echo ($isEdit && strpos($announcement->target_roles, 'employee') !== false) ? 'checked' : ''; ?>>
                                <label class="role-card-label" for="target_employee">
                                    <i class="fas fa-user-tie text-sky-500"></i>
                                    <span>کارمندان</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="role-card-checkbox">
                                <input type="checkbox" name="target_roles[]" value="client" id="target_client" <?php echo ($isEdit && strpos($announcement->target_roles, 'client') !== false) ? 'checked' : ''; ?>>
                                <label class="role-card-label" for="target_client">
                                    <i class="fas fa-user-tag text-amber-500"></i>
                                    <span>مشتریان</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4 border-slate-200">
                
                <div class="d-flex align-items-center justify-content-end gap-3">
                    <a href="index.php?page=admin&action=announcements_index" class="btn btn-fin-secondary">انصراف</a>
                    <button type="submit" class="btn btn-fin-primary d-inline-flex align-items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span><?php echo $isEdit ? 'ذخیره تغییرات اطلاعیه' : 'انتشار اطلاعیه جدید'; ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'متن و محتوای کامل اطلاعیه را اینجا وارد نمایید...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image'],
                [{ 'color': [] }, { 'background': [] }],
                ['clean']
            ]
        }
    });
    
    var form = document.getElementById('announcement-form');
    var bodyInput = document.getElementById('announcement_body');
    
    form.onsubmit = function() {
        bodyInput.value = quill.root.innerHTML;
        if(quill.getText().trim().length === 0) {
            alert('لطفاً محتوای اطلاعیه را وارد نمایید.');
            return false;
        }
    };
</script>