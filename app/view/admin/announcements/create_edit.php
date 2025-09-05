<?php
// app/views/admin/announcements/create_edit.php
$isEdit = isset($announcement);
?>
<style>
    .announcement-form-container {
        max-width: 900px;
        margin: 0 auto;
    }
    .form-header {
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }
    .card-body {
        padding: 2rem;
    }
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
    }
    .form-check-label {
        font-weight: 500;
    }
    .ql-toolbar.ql-snow {
        border-radius: 8px 8px 0 0;
        border-color: #e0e0e0;
    }
    .ql-container.ql-snow {
        border-radius: 0 0 8px 8px;
        border-color: #e0e0e0;
        min-height: 250px;
    }
</style>

<div class="announcement-form-container">
    <div class="d-sm-flex align-items-center justify-content-between mb-4 form-header">
        <h1 class="h3 mb-0 text-gray-800"><?php echo $isEdit ? 'ویرایش اطلاعیه' : 'ایجاد اطلاعیه جدید'; ?></h1>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="index.php?page=admin&action=<?php echo $isEdit ? 'announcements_update&id=' . $announcement->id : 'announcements_store'; ?>" method="POST">
                
                <div class="mb-4">
                    <label for="title" class="form-label">عنوان اطلاعیه</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($announcement->title ?? ''); ?>" required>
                </div>
                
                <div class="mb-4">
                    <label for="editor-container" class="form-label">محتوا</label>
                    <div id="editor-container" style="height: 250px;"><?php echo $announcement->body ?? ''; ?></div>
                    <input type="hidden" name="body" id="announcement_body">
                </div>
                
                <div class="mb-4">
                    <label class="form-label">مخاطبین:</label>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="target_roles[]" value="all" id="target_all" <?php echo (!$isEdit || strpos($announcement->target_roles, 'all') !== false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="target_all">همه کاربران</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="target_roles[]" value="admin" id="target_admin" <?php echo ($isEdit && strpos($announcement->target_roles, 'admin') !== false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="target_admin">مدیران</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="target_roles[]" value="employee" id="target_employee" <?php echo ($isEdit && strpos($announcement->target_roles, 'employee') !== false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="target_employee">کارمندان</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="target_roles[]" value="client" id="target_client" <?php echo ($isEdit && strpos($announcement->target_roles, 'client') !== false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="target_client">مشتریان</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save me-2"></i> ذخیره اطلاعیه</button>
            </form>
        </div>
    </div>
</div>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'محتوای اطلاعیه را اینجا وارد کنید...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image'],
                [{ 'color': [] }, { 'background': [] }],
                ['clean']
            ]
        }
    });
    
    var form = document.querySelector('form');
    var bodyInput = document.getElementById('announcement_body');
    
    form.onsubmit = function() {
        bodyInput.value = quill.root.innerHTML;
        // از Quill برای جلوگیری از ارسال محتوای خالی استفاده می‌کنیم
        if(quill.getText().trim().length === 0) {
            alert('محتوای اطلاعیه نمی‌تواند خالی باشد.');
            return false;
        }
    };
</script>