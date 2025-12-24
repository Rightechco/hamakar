<?php
// app/views/admin/inventory/warehouses/create_edit.php
// View for creating or editing a new warehouse

$warehouse = $warehouse ?? null;
$isEdit = $warehouse !== null;
?>
<style>
    .card-modern {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><?php echo $isEdit ? 'ویرایش انبار' : 'افزودن انبار جدید'; ?></h1>
</div>

<?php echo FlashMessage::display(); ?>

<div class="card card-modern shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">اطلاعات انبار</h6>
    </div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/index.php?page=inventory&action=<?php echo $isEdit ? 'update_warehouse&id=' . $warehouse->id : 'store_warehouse'; ?>" method="POST">
            <div class="form-group mb-3">
                <label for="name">نام انبار</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $isEdit ? htmlspecialchars($warehouse->name) : ''; ?>" required>
            </div>
            <div class="form-group mb-3">
                <label for="location">مکان انبار</label>
                <textarea class="form-control" id="location" name="location" rows="3"><?php echo $isEdit ? htmlspecialchars($warehouse->location) : ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-3"><?php echo $isEdit ? 'ذخیره تغییرات' : 'ثبت انبار'; ?></button>
            <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=warehouses" class="btn btn-secondary mt-3">بازگشت</a>
        </form>
    </div>
</div>