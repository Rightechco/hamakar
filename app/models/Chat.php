<?php
// app/models/Chat.php - نسخه نهایی با پشتیبانی از چت خصوصی

class Chat {
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function createMessage($data) {
        $this->db->query('INSERT INTO chat_messages (sender_user_id, receiver_user_id, message_text, file_name, file_path) VALUES (:sender, :receiver, :text, :fname, :fpath)');
        $this->db->bind(':sender', $data['sender_user_id']);
        $this->db->bind(':receiver', $data['receiver_user_id']);
        $this->db->bind(':text', $data['message_text']);
        $this->db->bind(':fname', $data['file_name']);
        $this->db->bind(':fpath', $data['file_path']);
        return $this->db->execute();
    }

    /**
     * ✅ متد جدید: دریافت پیام‌های یک گفتگوی خصوصی
     */
    public function getConversationMessages($userId1, $userId2, $lastId = 0) {
        $this->db->query('
            SELECT cm.*, u.name as sender_name 
            FROM chat_messages cm 
            JOIN users u ON cm.sender_user_id = u.id 
            WHERE 
                ((cm.sender_user_id = :user1 AND cm.receiver_user_id = :user2) OR 
                 (cm.sender_user_id = :user2 AND cm.receiver_user_id = :user1))
                AND cm.id > :last_id
            ORDER BY cm.created_at ASC
        ');
        $this->db->bind(':user1', $userId1);
        $this->db->bind(':user2', $userId2);
        $this->db->bind(':last_id', $lastId);
        return $this->db->fetchAll();
    }
}