<?php
class JournalEntry {
    private $db;
    public function __construct() { $this->db = new Database(); }
// app/models/JournalEntry.php
public function create($voucher_id, $entry) {
    $this->db->query('INSERT INTO journal_entries (voucher_id, account_id, debit, credit, entity_type, entity_id) VALUES (:voucher_id, :account_id, :debit, :credit, :entity_type, :entity_id)');
    $this->db->bind(':voucher_id', $voucher_id);
    $this->db->bind(':account_id', $entry['account_id']);
    $this->db->bind(':debit', $entry['debit'] ?? 0);
    $this->db->bind(':credit', $entry['credit'] ?? 0);
    $this->db->bind(':entity_type', $entry['entity_type'] ?? null); // ✅ اضافه شد
    $this->db->bind(':entity_id', $entry['entity_id'] ?? null);     // ✅ اضافه شد
    return $this->db->execute();
}

public function markAsReconciled($entryIds, $reconciliationId) {
    if (empty($entryIds)) {
        return true;
    }
    // تبدیل آرایه ID ها به یک رشته برای استفاده در کوئری IN
    $placeholders = implode(',', array_fill(0, count($entryIds), '?'));
    
    $this->db->query("UPDATE journal_entries SET reconciliation_id = ? WHERE id IN ($placeholders)");
    
    // پارامترها را به ترتیب bind می‌کنیم
    $params = array_merge([$reconciliationId], $entryIds);
    
    return $this->db->execute($params);
}
}