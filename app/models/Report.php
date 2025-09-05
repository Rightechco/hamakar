<?php
// app/models/Report.php

class Report {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * خلاصه‌ای از وضعیت مالی فاکتورها را برمی‌گرداند.
     * @return object
     */
    public function getInvoiceFinancialSummary() {
        $this->db->query("
            SELECT
                COUNT(*) as total_invoices,
                SUM(total_amount) as total_revenue,
                SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END) as paid_revenue,
                SUM(CASE WHEN status = 'pending' OR status = 'overdue' THEN total_amount ELSE 0 END) as unpaid_revenue
            FROM invoices
            WHERE status != 'canceled'
        ");
        return $this->db->fetch();
    }

    /**
     * خلاصه‌ای از وضعیت پروژه‌ها را برمی‌گرداند.
     * @return array
     */
    public function getProjectStatusSummary() {
        $this->db->query("
            SELECT status, COUNT(*) as count
            FROM projects
            GROUP BY status
        ");
        return $this->db->fetchAll();
    }

    /**
     * خلاصه‌ای از تعداد وظایف هر کاربر را برمی‌گرداند.
     * @return array
     */
    public function getUserTaskSummary() {
        $this->db->query("
            SELECT u.name as user_name, COUNT(t.id) as assigned_tasks
            FROM users u
            LEFT JOIN tasks t ON u.id = t.assigned_to_user_id
            WHERE u.role != 'client' -- فقط کارمندان و ادمین‌ها
            GROUP BY u.id
            ORDER BY assigned_tasks DESC
        ");
        return $this->db->fetchAll();
    }
    
    /**
     * لیست مشتریان جدید اخیر را برمی‌گرداند.
     * @param int $limit
     * @return array
     */
    public function getRecentClients($limit = 5) {
        $this->db->query("SELECT name, email, created_at FROM clients ORDER BY created_at DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->fetchAll();
    }
    
    public function getInvoicesReport($filters = []) {
        $sql = "SELECT i.*, c.name as client_name 
                FROM invoices i
                JOIN clients c ON i.client_id = c.id
                WHERE 1=1";

        if (!empty($filters['status'])) {
            $sql .= " AND i.status = :status";
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND i.issue_date >= :start_date";
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND i.issue_date <= :end_date";
        }
        $sql .= " ORDER BY i.issue_date DESC";

        $this->db->query($sql);

        if (!empty($filters['status'])) {
            $this->db->bind(':status', $filters['status']);
        }
        if (!empty($filters['start_date'])) {
            $this->db->bind(':start_date', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $this->db->bind(':end_date', $filters['end_date']);
        }

        return $this->db->fetchAll();
    }

    /**
     * گزارش کاملی از پروژه‌ها بر اساس وضعیت ارائه می‌دهد.
     * @param array $filters شامل status
     * @return array
     */
    public function getProjectsReport($filters = []) {
        $sql = "SELECT p.*, c.name as client_name,
                   (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as total_tasks,
                   (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'done') as completed_tasks
                FROM projects p
                LEFT JOIN clients c ON p.client_id = c.id
                WHERE 1=1";

        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status";
        }
        $sql .= " ORDER BY p.created_at DESC";
        
        $this->db->query($sql);

        if (!empty($filters['status'])) {
            $this->db->bind(':status', $filters['status']);
        }
        
        return $this->db->fetchAll();
    }

    /**
     * گزارش عملکرد کارمندان بر اساس تعداد وظایف.
     * @return array
     */
    public function getEmployeeActivityReport() {
        $this->db->query("
            SELECT u.name as employee_name, u.email,
                   COUNT(t.id) as total_tasks,
                   SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) as completed_tasks,
                   SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks
            FROM users u
            LEFT JOIN tasks t ON u.id = t.assigned_to_user_id
            WHERE u.role IN ('admin', 'employee')
            GROUP BY u.id
            ORDER BY total_tasks DESC
        ");
        return $this->db->fetchAll();
    }

}