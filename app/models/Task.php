<?php
// app/models/Task.php

class Task {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createTask($data) {
        $this->db->query('INSERT INTO tasks (project_id, title, description, assigned_to_user_id, due_date, due_in_days, due_in_hours, notes, priority, status) VALUES (:project_id, :title, :description, :assigned_to_user_id, :due_date, :due_in_days, :due_in_hours, :notes, :priority, :status)');
        $this->db->bind(':project_id', $data['project_id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':assigned_to_user_id', $data['assigned_to_user_id']);
        $this->db->bind(':due_date', $data['due_date']);
        $this->db->bind(':due_in_days', $data['due_in_days']);
        $this->db->bind(':due_in_hours', $data['due_in_hours']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':priority', $data['priority']);
        $this->db->bind(':status', $data['status']);
        return $this->db->execute();
    }

    public function getTasksByProjectId($projectId) {
        $this->db->query('SELECT t.*, u.name as assignee_name FROM tasks t LEFT JOIN users u ON t.assigned_to_user_id = u.id WHERE t.project_id = :project_id ORDER BY t.due_date ASC');
        $this->db->bind(':project_id', $projectId);
        return $this->db->fetchAll();
    }

    public function getOverdueTasksForReminder() {
        $this->db->query("
            SELECT t.id, t.title, t.due_date, u.name as user_name, u.phone as user_phone
            FROM tasks t
            JOIN users u ON t.assigned_to_user_id = u.id
            WHERE t.due_date < CURDATE()
              AND t.status != 'done'
              AND t.sms_reminder_sent_at IS NULL
        ");
        return $this->db->fetchAll();
    }

    public function markSmsReminderAsSent($taskId) {
        $this->db->query('UPDATE tasks SET sms_reminder_sent_at = NOW() WHERE id = :id');
        $this->db->bind(':id', $taskId);
        return $this->db->execute();
    }

    public function getTasksByProjectIdAndUserId($projectId, $userId) {
        $this->db->query('
            SELECT * FROM tasks 
            WHERE project_id = :project_id AND assigned_to_user_id = :user_id 
            ORDER BY FIELD(priority, "high", "medium", "low"), due_date ASC
        ');
        $this->db->bind(':project_id', $projectId);
        $this->db->bind(':user_id', $userId);
        return $this->db->fetchAll();
    }

    public function updateStatus($taskId, $status) {
        $this->db->query('UPDATE tasks SET status = :status WHERE id = :id');
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $taskId);
        return $this->db->execute();
    }

    public function addChecklistItem($taskId, $itemText) {
        $this->db->query('INSERT INTO task_checklists (task_id, item_text) VALUES (:task_id, :item_text)');
        $this->db->bind(':task_id', $taskId);
        $this->db->bind(':item_text', $itemText);
        return $this->db->execute();
    }

    /**
     * Toggles the completion status of a checklist item.
     * @param int $itemId
     * @param bool $isCompleted
     * @return bool
     */
    public function toggleChecklistItem($itemId, $isCompleted) {
        $this->db->query('UPDATE task_checklists SET is_completed = :is_completed WHERE id = :id');
        $this->db->bind(':id', $itemId);
        $this->db->bind(':is_completed', (int)$isCompleted);
        return $this->db->execute();
    }

    /**
     * Deletes a checklist item by its ID.
     * @param int $itemId
     * @return bool
     */
    public function deleteChecklistItem($itemId) {
        $this->db->query('DELETE FROM task_checklists WHERE id = :id');
        $this->db->bind(':id', $itemId);
        return $this->db->execute();
    }

    /**
     * Gets the project ID for a given task ID.
     * @param int $taskId
     * @return int|null
     */
    public function getProjectIdByTaskId($taskId) {
        $this->db->query('SELECT project_id FROM tasks WHERE id = :task_id');
        $this->db->bind(':task_id', $taskId);
        $result = $this->db->fetch();
        return $result ? $result->project_id : null;
    }

public function findById($id) {
    $this->db->query('SELECT * FROM tasks WHERE id = :id');
    $this->db->bind(':id', $id);
    return $this->db->fetch();
}
public function getChecklistItemsByTaskId($taskId) {
    $this->db->query('SELECT * FROM task_checklists WHERE task_id = :task_id ORDER BY created_at ASC');
    $this->db->bind(':task_id', $taskId);
    return $this->db->fetchAll();
}
}