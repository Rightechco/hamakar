<?php
// app/view/admin/inventory/services/index.php
$services = $services ?? [];
?>

<h1 class="h3 mb-4 text-gray-800">مدیریت خدمات</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">لیست خدمات</h6>
        <a href="<?php echo APP_URL; ?>/index.php?page=services&action=create" class="btn btn-success btn-sm">افزودن خدمت جدید</a>
    </div>
    <div class="card-body">
        <?php FlashMessage::display(); ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>نام</th>
                        <th>قیمت</th>
                        <th>دسته‌بندی</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($service->id); ?></td>
                            <td><?php echo htmlspecialchars($service->name); ?></td>
                            <td><?php echo htmlspecialchars(number_format($service->price)); ?></td>
                            <td><?php echo htmlspecialchars($service->category_name ?? '---'); ?></td>
                            <td>
                                <a href="<?php echo APP_URL; ?>/index.php?page=services&action=edit&id=<?php echo $service->id; ?>" class="btn btn-warning btn-sm">ویرایش</a>
                                <form action="<?php echo APP_URL; ?>/index.php?page=services&action=delete&id=<?php echo $service->id; ?>" method="POST" style="display:inline-block;">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('آیا از حذف این خدمت اطمینان دارید؟');">حذف</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>