<?php
// cron_task_reminders.php

// بارگذاری فایل‌های ضروری
require_once __DIR__ . '/app/config/app.php';
require_once __DIR__ . '/app/config/database.php';
require_once __DIR__ . '/app/config/sms.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/lib/SmsService.php';
require_once __DIR__ . '/app/models/Task.php';

echo "Task Reminder Cron Job Started: " . date('Y-m-d H:i:s') . "\n";

$taskModel = new Task();
$smsService = new SmsService();

$overdueTasks = $taskModel->getOverdueTasksForReminder();

echo "Found " . count($overdueTasks) . " overdue tasks to remind.\n";

foreach ($overdueTasks as $task) {
    if (!empty($task->user_phone)) {
        $args = [$task->title, $task->due_date];
        // شما باید یک الگوی جدید برای این پیامک در پنل خود تعریف کنید
        // define('SMS_BODY_ID_TASK_REMINDER', XXXXX);
        $isSent = $smsService->sendSmsByPattern($task->user_phone, SMS_BODY_ID_TASK_REMINDER, $args);
        
        if ($isSent) {
            $taskModel->markSmsReminderAsSent($task->id);
            echo "Reminder sent for task #{$task->id} to {$task->user_name}.\n";
        }
    }
}

echo "Cron Job Finished.\n";
?>