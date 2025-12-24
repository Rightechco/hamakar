<?php
// app/views/shared/log_modal.php
// این فایل یک مودال Bootstrap برای ثبت لاگ کارفرما است.
?>
<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logModalLabel">ثبت لاگ برای کارفرما</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
            </div>
            <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=clients_store_log" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="client_id" id="log_client_id">
                    <div class="mb-3">
                        <label for="log_type" class="form-label">نوع لاگ:</label>
                        <select class="form-select" id="log_type" name="log_type" required>
                            <option value="call">تماس تلفنی</option>
                            <option value="ticket">تیکت</option>
                            <option value="in_person">حضوری</option>
                            <option value="email">ایمیل</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="log_description" class="form-label">توضیحات:</label>
                        <textarea class="form-control" id="log_description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                    <button type="submit" class="btn btn-primary">ثبت لاگ</button>
                </div>
            </form>
        </div>
    </div>
</div>