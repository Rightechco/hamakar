<?php
// app/models/ClientLog.php

class ClientLog {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * لاگ‌های یک مشتری را به همراه نام کاربر ثبت کننده و نام رابط (در صورت وجود) برمی‌گرداند.
     * شامل اطلاعات فایل پیوست نیز می‌شود.
     */
    public function getLogsByClientId($clientId) {
        $this->db->query('SELECT cl.*, u.name as user_name, cu.name as contact_name, cla.file_name, cla.file_path
                          FROM client_logs cl 
                          JOIN users u ON cl.user_id = u.id 
                          LEFT JOIN users cu ON cl.contact_user_id = cu.id
                          LEFT JOIN client_log_attachments cla ON cl.id = cla.log_id
                          WHERE cl.client_id = :client_id 
                          ORDER BY cl.log_date DESC');
        $this->db->bind(':client_id', $clientId);
        return $this->db->fetchAll();
    }

    /**
     * یک لاگ جدید در دیتابیس ایجاد می‌کند.
     * @param array $data شامل client_id, log_type, description, log_date, user_id, contact_user_id
     * @return int|false شناسه لاگ ایجاد شده یا false در صورت خطا
     */
    public function create($data) {
        $this->db->query('INSERT INTO client_logs (client_id, log_type, description, log_date, user_id, contact_user_id) 
                          VALUES (:client_id, :log_type, :description, :log_date, :user_id, :contact_user_id)');
        $this->db->bind(':client_id', $data['client_id']);
        $this->db->bind(':log_type', $data['log_type']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':log_date', $data['log_date']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':contact_user_id', $data['contact_user_id']);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * اطلاعات یک فایل پیوست را در جدول client_log_attachments ثبت می‌کند.
     */
    public function createAttachment($logId, $fileName, $filePath, $fileSize) {
        $this->db->query('INSERT INTO client_log_attachments (log_id, file_name, file_path, file_size) VALUES (:log_id, :file_name, :file_path, :file_size)');
        $this->db->bind(':log_id', $logId);
        $this->db->bind(':file_name', $fileName);
        $this->db->bind(':file_path', $filePath);
        $this->db->bind(':file_size', $fileSize);
        return $this->db->execute();
    }

    /**
     * اطلاعات یک لاگ را بر اساس شناسه آن پیدا می‌کند.
     * @param int $id شناسه لاگ
     * @return object|false
     */
    public function findById($id) {
        $this->db->query('SELECT * FROM client_logs WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * یک لاگ موجود را به روز رسانی می‌کند.
     * @param int $id شناسه لاگ
     * @param array $data شامل log_type, description, contact_user_id
     * @return bool
     */
    public function update($id, $data) {
        $this->db->query('UPDATE client_logs SET log_type = :log_type, description = :description, contact_user_id = :contact_user_id, updated_at = NOW() WHERE id = :id');
        $this->db->bind(':log_type', $data['log_type']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':contact_user_id', $data['contact_user_id']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * یک لاگ را به همراه فایل پیوست آن حذف می‌کند.
     * @param int $id شناسه لاگ
     * @return bool
     */
    public function delete($id) {
        // ابتدا فایل پیوست را از دیتابیس و سرور حذف کنید (اگر وجود داشته باشد)
        $this->db->query('SELECT file_path FROM client_log_attachments WHERE log_id = :log_id');
        $this->db->bind(':log_id', $id);
        $attachment = $this->db->fetch();
        if ($attachment && !empty($attachment->file_path)) {
            $fullPath = 'public/uploads/client_logs/' . $attachment->file_path; // مسیر کامل فایل
            if (file_exists($fullPath)) {
                unlink($fullPath); // حذف فایل از سرور
            }
        }
        
        $this->db->query('DELETE FROM client_log_attachments WHERE log_id = :log_id');
        $this->db->bind(':log_id', $id);
        $this->db->execute();

        // سپس لاگ اصلی را حذف کنید
        $this->db->query('DELETE FROM client_logs WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * اطلاعات فایل پیوست یک لاگ را حذف می‌کند.
     * @param int $logId شناسه لاگ
     * @return bool
     */
    public function deleteAttachment($logId) {
        $this->db->query('SELECT file_path FROM client_log_attachments WHERE log_id = :log_id');
        $this->db->bind(':log_id', $logId);
        $attachment = $this->db->fetch();
        
        if ($attachment && !empty($attachment->file_path)) {
            $fullPath = 'public/uploads/client_logs/' . $attachment->file_path; // مسیر کامل فایل
            if (file_exists($fullPath)) {
                unlink($fullPath); // حذف فایل از سرور
            }
        }
        
        $this->db->query('DELETE FROM client_log_attachments WHERE log_id = :log_id');
        $this->db->bind(':log_id', $logId);
        return $this->db->execute();
    }
}