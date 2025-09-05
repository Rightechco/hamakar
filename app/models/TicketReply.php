<?php
// app/models/TicketReply.php
class TicketReply {
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function create($data) {
        $this->db->query('INSERT INTO ticket_replies (ticket_id, user_id, body) VALUES (:ticket_id, :user_id, :body)');
        $this->db->bind(':ticket_id', $data['ticket_id']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':body', $data['body']);
        if ($this->db->execute()) { return $this->db->lastInsertId(); }
        return false;
    }
    
    public function createAttachment($replyId, $fileName, $filePath, $fileSize) {
        $this->db->query('INSERT INTO ticket_attachments (reply_id, file_name, file_path, file_size) VALUES (:reply_id, :file_name, :file_path, :file_size)');
        $this->db->bind(':reply_id', $replyId);
        $this->db->bind(':file_name', $fileName);
        $this->db->bind(':file_path', $filePath);
        $this->db->bind(':file_size', $fileSize);
        return $this->db->execute();
    }

    public function getRepliesByTicketId($ticketId) {
        $this->db->query('SELECT tr.*, u.name as user_name, u.role as user_role FROM ticket_replies tr JOIN users u ON tr.user_id = u.id WHERE tr.ticket_id = :ticket_id ORDER BY tr.created_at ASC');
        $this->db->bind(':ticket_id', $ticketId);
        $replies = $this->db->fetchAll();
        // دریافت پیوست‌ها برای هر پاسخ
        foreach ($replies as $reply) {
            $this->db->query('SELECT * FROM ticket_attachments WHERE reply_id = :reply_id');
            $this->db->bind(':reply_id', $reply->id);
            $reply->attachments = $this->db->fetchAll();
        }
        return $replies;
    }
}