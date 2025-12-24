<?php
// app/views/shared/confirm_modal.php
?>
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">تأیید عملیات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                آیا مطمئن هستید که می‌خواهید این عملیات را انجام دهید؟
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">خیر</button>
                <button type="button" class="btn btn-danger" id="confirmActionButton">بله، انجام بده</button>
            </div>
        </div>
    </div>
</div>