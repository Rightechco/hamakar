<?php
// app/views/admin/inventory/units/create_edit.php
// View for creating or editing a new product unit

$unit = $unit ?? null;
$isEdit = $unit !== null;
?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?php echo $isEdit ? 'ویرایش واحد' : 'افزودن واحد جدید'; ?></h6>
    </div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/index.php?page=inventory&action=<?php echo $isEdit ? 'update_unit&id=' . $unit->id : 'store_unit'; ?>" method="POST">
            <div class="form-group">
                <label for="name">نام واحد</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $isEdit ? htmlspecialchars($unit->name) : ''; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3"><?php echo $isEdit ? 'ذخیره تغییرات' : 'ثبت واحد'; ?></button>
        </form>
    </div>
</div>