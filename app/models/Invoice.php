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

    // app/models/Invoice.php

public function create($data) {
    $sql = 'INSERT INTO invoices (invoice_number, client_id, contract_id, invoice_type, category_id, issue_date, due_date, subtotal, vat_amount, total_amount, description, status) 
            VALUES (:invoice_number, :client_id, :contract_id, :invoice_type, :category_id, :issue_date, :due_date, :subtotal, :vat_amount, :total_amount, :description, :status)';
    
    $this->db->query($sql);
    
    $this->db->bind(':invoice_number', $data['invoice_number']);
    $this->db->bind(':client_id', $data['client_id']);
    $this->db->bind(':contract_id', $data['contract_id'] ?? null);
    $this->db->bind(':invoice_type', $data['invoice_type'] ?? 'contract'); // ✅ Bind the new invoice_type
    $this->db->bind(':category_id', $data['category_id'] ?? null);
    $this->db->bind(':issue_date', $data['issue_date']);
    $this->db->bind(':due_date', $data['due_date']);
    $this->db->bind(':subtotal', $data['subtotal']);
    $this->db->bind(':vat_amount', $data['vat_amount']);
    $this->db->bind(':total_amount', $data['total_amount']);
    $this->db->bind(':description', $data['description']);
    $this->db->bind(':status', $data['status']);
    
    return $this->db->execute() ? $this->db->lastInsertId() : false;
}


    public function update($id, $data) {
        $sql = 'UPDATE invoices SET invoice_number = :invoice_number, client_id = :client_id, contract_id = :contract_id, category_id = :category_id, issue_date = :issue_date, due_date = :due_date, total_amount = :total_amount, description = :description, status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':invoice_number', $data['invoice_number']);
        $this->db->bind(':client_id', $data['client_id']);
        $this->db->bind(':contract_id', $data['contract_id']);
        $this->db->bind(':category_id', $data['category_id'] ?? null);
        $this->db->bind(':issue_date', $data['issue_date']);
        $this->db->bind(':due_date', $data['due_date']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function delete($id) {
        $this->db->query('DELETE FROM invoices WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * updatePaymentStatus
     * وضعیت پرداخت فاکتور را بر اساس مبلغ پرداخت شده به روزرسانی می کند.
     * @param int $invoiceId شناسه فاکتور.
     * @param float $amountPaid مبلغی که تازه پرداخت شده است.
     * @return bool
     */
    public function updatePaymentStatus($invoiceId, $amountPaid) {
        $this->db->query('UPDATE invoices SET paid_amount = paid_amount + :amount_paid, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $this->db->bind(':amount_paid', $amountPaid);
        $this->db->bind(':id', $invoiceId);
        $this->db->execute();

        // حالا وضعیت کلی فاکتور را بر اساس paid_amount و total_amount به روز می کنیم
        $invoice = $this->findById($invoiceId);
        if ($invoice) {
            $newStatus = 'pending';
            if ($invoice->paid_amount >= $invoice->total_amount) {
                $newStatus = 'paid';
            } elseif ($invoice->paid_amount > 0 && $invoice->paid_amount < $invoice->total_amount) {
                $newStatus = 'partial';
            }
            // اگر تاریخ سررسید گذشته است و وضعیت paid نیست، overdue می شود (می توانید این را با کرون جاب چک کنید)

            $this->db->query('UPDATE invoices SET status = :status WHERE id = :id');
            $this->db->bind(':status', $newStatus);
            $this->db->bind(':id', $invoiceId);
            return $this->db->execute();
        }
        return false;
    }
    
    /**
     * getLastInvoiceNumber
     * آخرین شماره فاکتور را برای تولید شماره جدید دریافت می کند.
     * @return string
     */
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
public function getInvoicesByClientId($clientId) {
    $this->db->query("
        SELECT 
            i.id, 
            i.invoice_number, 
            i.contract_id, 
            c.title AS contract_title, -- ✅ اینجا عنوان قرارداد را انتخاب می‌کنیم
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
    
    // ... (other filters)
    
    if (!empty($filters['invoice_type'])) { // ✅ This block needs to be added
        $sql .= " AND i.invoice_type = :invoice_type";
    }
    
    $sql .= " ORDER BY i.issue_date DESC";
    $this->db->query($sql);

    // ... (bind other parameters)

    if (!empty($filters['invoice_type'])) { // ✅ This block needs to be added
        $this->db->bind(':invoice_type', $filters['invoice_type']);
    }

    return $this->db->fetchAll();
}

}
