<?php
// app/models/Invoice.php

class Invoice {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

   public function getAllInvoices() {
        $this->db->query("SELECT i.*, c.name as client_name, co.title as contract_title 
                          FROM invoices i 
                          JOIN clients c ON i.client_id = c.id 
                          LEFT JOIN contracts co ON i.contract_id = co.id 
                          ORDER BY i.issue_date DESC");
        return $this->db->fetchAll();
    }

    public function findById($id) {
        $this->db->query("SELECT i.*, c.name as client_name, c.email as client_email, c.phone as client_phone, co.title as contract_title 
                          FROM invoices i 
                          JOIN clients c ON i.client_id = c.id 
                          LEFT JOIN contracts co ON i.contract_id = co.id 
                          WHERE i.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    
    public function countAll() {
        $this->db->query('SELECT COUNT(*) as count FROM invoices');
        $result = $this->db->fetch();
        return $result ? $result->count : 0;
    }

    public function create($data) {
        $sql = 'INSERT INTO invoices (invoice_number, client_id, contract_id, invoice_type, is_official, category_id, issue_date, due_date, subtotal, vat_amount, total_amount, description, status, economic_code, national_id, pattern_type) 
                VALUES (:invoice_number, :client_id, :contract_id, :invoice_type, :is_official, :category_id, :issue_date, :due_date, :subtotal, :vat_amount, :total_amount, :description, :status, :economic_code, :national_id, :pattern_type)';
        
        $this->db->query($sql);
        
        $this->db->bind(':invoice_number', $data['invoice_number']);
        $this->db->bind(':client_id', $data['client_id']);
        $this->db->bind(':contract_id', $data['contract_id'] ?? null);
        $this->db->bind(':invoice_type', $data['invoice_type'] ?? 'contract');
        $this->db->bind(':is_official', isset($data['is_official']) ? (int)$data['is_official'] : 1);
        $this->db->bind(':category_id', $data['category_id'] ?? null);
        $this->db->bind(':issue_date', $data['issue_date']);
        $this->db->bind(':due_date', $data['due_date']);
        $this->db->bind(':subtotal', $data['subtotal']);
        $this->db->bind(':vat_amount', $data['vat_amount'] ?? 0);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':economic_code', $data['economic_code'] ?? null);
        $this->db->bind(':national_id', $data['national_id'] ?? null);
        $this->db->bind(':pattern_type', $data['pattern_type'] ?? 1);
        
        return $this->db->execute() ? $this->db->lastInsertId() : false;
    }

    public function update($id, $data) {
        $sql = 'UPDATE invoices SET 
                    invoice_number = :invoice_number, 
                    client_id = :client_id, 
                    contract_id = :contract_id, 
                    category_id = :category_id, 
                    issue_date = :issue_date, 
                    due_date = :due_date, 
                    total_amount = :total_amount, 
                    description = :description, 
                    status = :status, 
                    is_official = :is_official,
                    economic_code = :economic_code,
                    national_id = :national_id,
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':invoice_number', $data['invoice_number']);
        $this->db->bind(':client_id', $data['client_id']);
        $this->db->bind(':contract_id', $data['contract_id'] ?? null);
        $this->db->bind(':category_id', $data['category_id'] ?? null);
        $this->db->bind(':issue_date', $data['issue_date']);
        $this->db->bind(':due_date', $data['due_date']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':is_official', isset($data['is_official']) ? (int)$data['is_official'] : 1);
        $this->db->bind(':economic_code', $data['economic_code'] ?? null);
        $this->db->bind(':national_id', $data['national_id'] ?? null);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query('DELETE FROM invoices WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    public function updatePaymentStatus($invoiceId, $amountPaid) {
        $this->db->query('UPDATE invoices SET paid_amount = paid_amount + :amount_paid, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $this->db->bind(':amount_paid', $amountPaid);
        $this->db->bind(':id', $invoiceId);
        $this->db->execute();

        $invoice = $this->findById($invoiceId);
        if ($invoice) {
            $newStatus = 'pending';
            if ($invoice->paid_amount >= $invoice->total_amount) {
                $newStatus = 'paid';
            } elseif ($invoice->paid_amount > 0 && $invoice->paid_amount < $invoice->total_amount) {
                $newStatus = 'partial';
            }

            $this->db->query('UPDATE invoices SET status = :status WHERE id = :id');
            $this->db->bind(':status', $newStatus);
            $this->db->bind(':id', $invoiceId);
            return $this->db->execute();
        }
        return false;
    }
    
    public function getLastInvoiceNumber() {
        $this->db->query('SELECT invoice_number FROM invoices ORDER BY id DESC LIMIT 1');
        $result = $this->db->fetch();
        return $result ? $result->invoice_number : 'INV-000000';
    }

    public function findByIdWithClient($id) {
        $sql = "
            SELECT 
                invoices.*, 
                clients.name AS client_name, 
                clients.address AS client_address,
                clients.phone AS client_phone,
                clients.email AS client_email,
                contracts.title AS contract_title
            FROM 
                invoices
            LEFT JOIN 
                clients ON invoices.client_id = clients.id
            LEFT JOIN
                contracts ON invoices.contract_id = contracts.id
            WHERE 
                invoices.id = :id
        ";
        $this->db->query($sql);
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    
    /**
     * Retrieves all items for a given invoice ID.
     */
    public function getItemsByInvoiceId($invoiceId) {
        $this->db->query("
            SELECT 
                ii.*,
                p.name AS product_name,
                s.name AS service_name
            FROM invoice_items ii
            LEFT JOIN products p ON ii.product_id = p.id
            LEFT JOIN services s ON ii.service_id = s.id
            WHERE ii.invoice_id = :invoice_id
        ");
        $this->db->bind(':invoice_id', $invoiceId);
        return $this->db->fetchAll();
    }
    
    public function getInvoicesByClientId($clientId) {
        $this->db->query("
            SELECT 
                i.id, 
                i.invoice_number, 
                i.contract_id, 
                c.title AS contract_title,
                i.issue_date, 
                i.due_date, 
                i.total_amount, 
                i.paid_amount, 
                i.status, 
                i.description 
            FROM invoices AS i
            LEFT JOIN contracts AS c ON i.contract_id = c.id
            WHERE i.client_id = :client_id 
            ORDER BY i.issue_date DESC
        ");
        $this->db->bind(':client_id', $clientId);
        return $this->db->fetchAll();
    }
    
    public function getFilteredInvoices($filters) {
        $sql = "
            SELECT i.*, c.name as client_name, co.title as contract_title, cat.name as category_name
            FROM invoices i 
            JOIN clients c ON i.client_id = c.id 
            LEFT JOIN contracts co ON i.contract_id = co.id
            LEFT JOIN categories cat ON i.category_id = cat.id
            WHERE 1=1
        ";
        
        if (!empty($filters['search'])) {
            $sql .= " AND (i.invoice_number LIKE :search OR c.name LIKE :search)";
        }
        if (!empty($filters['client_id'])) {
            $sql .= " AND i.client_id = :client_id";
        }
        if (!empty($filters['status'])) {
            $sql .= " AND i.status = :status";
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND i.issue_date >= :start_date";
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND i.issue_date <= :end_date";
        }
        if (!empty($filters['invoice_type'])) { 
            $sql .= " AND i.invoice_type = :invoice_type";
        }
        
        $sql .= " ORDER BY i.issue_date DESC";
        $this->db->query($sql);

        if (!empty($filters['search'])) {
            $this->db->bind(':search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['client_id'])) {
            $this->db->bind(':client_id', $filters['client_id']);
        }
        if (!empty($filters['status'])) {
            $this->db->bind(':status', $filters['status']);
        }
        if (!empty($filters['start_date'])) {
            $this->db->bind(':start_date', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $this->db->bind(':end_date', $filters['end_date']);
        }
        if (!empty($filters['invoice_type'])) {
            $this->db->bind(':invoice_type', $filters['invoice_type']);
        }

        return $this->db->fetchAll();
    }
    
    public function getAllCombinedInvoices() {
        $this->db->query("
            (
                SELECT 
                    i.id,
                    i.client_id,
                    c.name AS client_name,
                    i.contract_id,
                    co.title AS contract_title,
                    i.invoice_number AS invoice_number,
                    i.total_amount,
                    i.issue_date AS date,
                    i.status,
                    'contract' AS type,
                    i.description AS item_description,
                    COALESCE(i.is_official, 1) AS is_official,
                    i.moadian_taxid,
                    COALESCE(i.moadian_status, 'not_sent') AS moadian_status
                FROM invoices i
                JOIN clients c ON i.client_id = c.id
                LEFT JOIN contracts co ON i.contract_id = co.id
            )
            UNION ALL
            (
                SELECT
                    s.id,
                    s.client_id,
                    c.name AS client_name,
                    NULL AS contract_id,
                    NULL AS contract_title,
                    NULL AS invoice_number,
                    s.total_amount,
                    s.sale_date AS date,
                    s.status,
                    'sale' AS type,
                    GROUP_CONCAT(p.name SEPARATOR ', ') AS item_description,
                    0 AS is_official,
                    NULL AS moadian_taxid,
                    'not_sent' AS moadian_status
                FROM sales s
                JOIN clients c ON s.client_id = c.id
                LEFT JOIN sale_items si ON s.id = si.sale_id
                LEFT JOIN products p ON si.product_id = p.id
                GROUP BY s.id
            )
            UNION ALL
            (
                SELECT
                    p.id,
                    p.vendor_id AS client_id,
                    c.name AS client_name,
                    NULL AS contract_id,
                    NULL AS contract_title,
                    NULL AS invoice_number,
                    p.total_amount,
                    p.purchase_date AS date,
                    p.status,
                    'purchase' AS type,
                    GROUP_CONCAT(pro.name SEPARATOR ', ') AS item_description,
                    0 AS is_official,
                    NULL AS moadian_taxid,
                    'not_sent' AS moadian_status
                FROM purchases p
                LEFT JOIN clients c ON p.vendor_id = c.id
                LEFT JOIN purchase_items pi ON p.id = pi.purchase_id
                LEFT JOIN products pro ON pi.product_id = pro.id
                GROUP BY p.id
            )
            ORDER BY date DESC
        ");
        return $this->db->fetchAll();
    }
     public function updateStatus($id, $status) {
        $this->db->query("UPDATE invoices SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}