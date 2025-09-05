<h3 class="mb-4">موضوع: <?php echo sanitize($ticket->subject); ?></h3>
<?php foreach($replies as $reply): ?>
    <div class="card mb-3 <?php echo ($reply->user_role === 'client') ? 'border-primary' : 'border-success'; ?>">
        <div class="card-header bg-light d-flex justify-content-between">
            <strong><?php echo sanitize($reply->user_name); ?></strong>
            <span class="text-muted small"><?php echo jdate('Y/m/d H:i', strtotime($reply->created_at)); ?></span>
        </div>
        <div class="card-body">
            <?php echo $reply->body; // متن HTML از ویرایشگر ?>
            <?php if (!empty($reply->attachments)): ?>
                <hr>
                <p class="mb-1"><strong>پیوست‌ها:</strong></p>
                <?php foreach($reply->attachments as $attachment): ?>
                    <a href="<?php echo APP_URL . '/' . $attachment->file_path; ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="fas fa-paperclip"></i> <?php echo sanitize($attachment->file_name); ?>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>

<hr>
<h4>ارسال پاسخ جدید</h4>
<form action="<?php echo APP_URL; ?>/index.php?page=<?php echo $auth->isAdmin() ? 'admin' : 'client'; ?>&action=store_ticket_reply&id=<?php echo $ticket->id; ?>" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <textarea id="ticket_reply_editor" name="body" class="form-control" rows="8"></textarea>
    </div>
    <div class="mb-3">
        <label for="attachment" class="form-label">پیوست فایل (اختیاری)</label>
        <input type="file" name="attachment" id="attachment" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">ارسال پاسخ</button>
</form>