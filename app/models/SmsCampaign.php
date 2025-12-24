<?php
// app/models/SmsCampaign.php
class SmsCampaign {
    private $db;
    public function __construct() { $this->db = new Database(); }

    public function logCampaign($data) {
        $this->db->query('INSERT INTO sms_campaigns (campaign_type, occasion_name, occasion_date_key, message_body, status, recipients_count) VALUES (:campaign_type, :occasion_name, :occasion_date_key, :message_body, :status, :recipients_count)');
        $this->db->bind(':campaign_type', $data['campaign_type']);
        $this->db->bind(':occasion_name', $data['occasion_name']);
        $this->db->bind(':occasion_date_key', $data['occasion_date_key']);
        $this->db->bind(':message_body', $data['message_body']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':recipients_count', $data['recipients_count']);
        return $this->db->execute();
    }

    /**
     * بررسی می‌کند آیا مناسبت در سال جاری قبلاً ارسال شده است یا خیر
     */
    public function hasOccasionBeenSentThisYear($dateKey) {
        // این کوئری تاریخ میلادی را با تاریخ سرور مقایسه می‌کند
        $this->db->query("SELECT id FROM sms_campaigns WHERE occasion_date_key = :date_key AND YEAR(sent_at) = YEAR(CURDATE())");
        $this->db->bind(':date_key', $dateKey);
        $result = $this->db->fetch();
        return $result ? true : false;
    }
}