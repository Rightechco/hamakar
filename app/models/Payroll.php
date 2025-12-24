<?php
// app/models/Payroll.php

class Payroll {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * ایجاد یک رکورد حقوق جدید و آیتم‌های آن
     */
    public function createPayroll($payrollData, $itemsData) {
        $this->db->beginTransaction();
        try {
            // 1. ذخیره رکورد اصلی حقوق
            $this->db->query('INSERT INTO payrolls (user_id, pay_period_year, pay_period_month, gross_earnings, total_deductions, net_pay, status, notes) 
                             VALUES (:user_id, :year, :month, :gross, :deductions, :net, :status, :notes)');
            $this->db->bind(':user_id', $payrollData['user_id']);
            $this->db->bind(':year', $payrollData['pay_period_year']);
            $this->db->bind(':month', $payrollData['pay_period_month']);
            $this->db->bind(':gross', $payrollData['gross_earnings']);
            $this->db->bind(':deductions', $payrollData['total_deductions']);
            $this->db->bind(':net', $payrollData['net_pay']);
            $this->db->bind(':status', $payrollData['status']);
            $this->db->bind(':notes', $payrollData['notes']);
            $this->db->execute();
            
            $payrollId = $this->db->lastInsertId();

            // 2. ذخیره آیتم‌های حقوق و کسورات
            foreach ($itemsData as $item) {
                $this->db->query('INSERT INTO payroll_items (payroll_id, item_type, description, amount) 
                                 VALUES (:payroll_id, :item_type, :description, :amount)');
                $this->db->bind(':payroll_id', $payrollId);
                $this->db->bind(':item_type', $item['type']);
                $this->db->bind(':description', $item['description']);
                $this->db->bind(':amount', $item['amount']);
                $this->db->execute();
            }

            $this->db->commit();
            return $payrollId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage()); // لاگ کردن خطا
            return false;
        }
    }

    /**
     * دریافت تمام فیش‌های حقوقی برای یک کارمند
     */
    public function getPayrollsForUser($userId) {
        $this->db->query('SELECT * FROM payrolls WHERE user_id = :user_id ORDER BY pay_period_year DESC, pay_period_month DESC');
        $this->db->bind(':user_id', $userId);
        return $this->db->fetchAll();
    }
    
    /**
     * دریافت تمام فیش‌های حقوقی (برای ادمین)
     */
    public function getAllPayrolls() {
        $this->db->query('SELECT p.*, u.name as employee_name 
                         FROM payrolls p
                         JOIN users u ON p.user_id = u.id
                         ORDER BY p.created_at DESC');
        return $this->db->fetchAll();
    }


    public function findPayrollById($id) {
        $this->db->query('SELECT * FROM payrolls WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    public function getPayrollItems($payrollId) {
        $this->db->query('SELECT * FROM payroll_items WHERE payroll_id = :payroll_id');
        $this->db->bind(':payroll_id', $payrollId);
        return $this->db->fetchAll();
    }
    
public function findPayrollByIdWithDetails($id) {
    // ۱. دریافت اطلاعات اصلی فیش
    $this->db->query('SELECT * FROM payrolls WHERE id = :id');
    $this->db->bind(':id', $id);
    $payroll = $this->db->fetch();

    if ($payroll) {
        // ۲. دریافت اطلاعات کارمند
        $this->db->query('SELECT * FROM users WHERE id = :user_id');
        $this->db->bind(':user_id', $payroll->user_id);
        $payroll->user = $this->db->fetch();

        // ۳. دریافت آیتم‌های حقوق و کسورات
        $this->db->query('SELECT * FROM payroll_items WHERE payroll_id = :id');
        $this->db->bind(':id', $id);
        $payroll->items = $this->db->fetchAll();
    }
    return $payroll;
}
}