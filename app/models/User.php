<?php
// app/models/User.php

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // دریافت تمام کاربران
    public function getAllUsers() {
        $this->db->query('SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC');
        return $this->db->fetchAll();
    }

    // اضافه کردن این متد برای شمردن کل کاربران
    public function countAll() {
        $this->db->query('SELECT COUNT(*) as count FROM users');
        $result = $this->db->fetch();
        return $result ? $result->count : 0;
    }

   
public function findById($id) {
    $this->db->query('SELECT * FROM users WHERE id = :id');
    $this->db->bind(':id', $id);
    return $this->db->fetch();
}

    public function findByEmail($email) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->fetch();
    }
public function create($data, $returnId = false) {
    $sql = "INSERT INTO users (name, email, password, role, status, 
                              mobile_number, postal_address, organizational_position, national_id_code, 
                              base_salary, hire_date, marital_status, children_count) 
            VALUES (:name, :email, :password, :role, :status, 
                    :mobile_number, :postal_address, :organizational_position, :national_id_code, 
                    :base_salary, :hire_date, :marital_status, :children_count)";
    
    $this->db->query($sql);

    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':password', password_hash($data['password'], PASSWORD_BCRYPT));
    $this->db->bind(':role', $data['role']);
    $this->db->bind(':status', $data['status'] ?? 'active');
    $this->db->bind(':mobile_number', $data['mobile_number'] ?? null);
    $this->db->bind(':postal_address', $data['postal_address'] ?? null);
    $this->db->bind(':organizational_position', $data['organizational_position'] ?? null);
    $this->db->bind(':national_id_code', $data['national_id_code'] ?? null);
    $this->db->bind(':base_salary', $data['base_salary'] ?? 0);
    $this->db->bind(':hire_date', $data['hire_date'] ?? null);
    $this->db->bind(':marital_status', $data['marital_status'] ?? 'single');
    $this->db->bind(':children_count', $data['children_count'] ?? 0);

    if ($this->db->execute()) {
        if ($returnId) {
            return $this->db->lastInsertId();
        }
        return true;
    }
    return false;
}
public function update($id, $data) {
    $sql = "UPDATE users SET 
                name = :name, 
                email = :email, 
                role = :role, 
                status = :status,
                mobile_number = :mobile_number, 
                postal_address = :postal_address,
                organizational_position = :organizational_position, 
                national_id_code = :national_id_code,
                base_salary = :base_salary, 
                hire_date = :hire_date,
                marital_status = :marital_status, 
                children_count = :children_count,
                updated_at = CURRENT_TIMESTAMP 
            WHERE id = :id";
            
    $this->db->query($sql);
    
    // Bind کردن تمام پارامترها
    $this->db->bind(':name', $data['name']);
    $this->db->bind(':email', $data['email']);
    $this->db->bind(':role', $data['role']);
    $this->db->bind(':status', $data['status']);
    $this->db->bind(':mobile_number', $data['mobile_number']);
    $this->db->bind(':postal_address', $data['postal_address']);
    $this->db->bind(':organizational_position', $data['organizational_position']);
    $this->db->bind(':national_id_code', $data['national_id_code']);
    $this->db->bind(':base_salary', $data['base_salary']);
    $this->db->bind(':hire_date', $data['hire_date']);
    $this->db->bind(':marital_status', $data['marital_status']);
    $this->db->bind(':children_count', $data['children_count']);
    $this->db->bind(':id', $id);

    return $this->db->execute();
}
    public function delete($id) {
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
public function updateLastActivity($userId) {
    $this->db->query("UPDATE users SET last_activity = NOW() WHERE id = :user_id");
    $this->db->bind(':user_id', $userId);
    return $this->db->execute();
}
public function getAllEmployees() {
    $this->db->query("SELECT id, name FROM users WHERE role = 'employee'");
    return $this->db->fetchAll();
}
public function getFilteredUsers($filters) {
        $sql = "SELECT * FROM users WHERE 1=1";

        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE :search OR email LIKE :search)";
        }
        if (!empty($filters['role'])) {
            $sql .= " AND role = :role";
        }
        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND created_at >= :start_date";
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND created_at <= :end_date";
        }

        $sql .= " ORDER BY created_at DESC";
        $this->db->query($sql);

        // Bind parameters
        if (!empty($filters['search'])) {
            $this->db->bind(':search', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['role'])) {
            $this->db->bind(':role', $filters['role']);
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
        
        return $this->db->fetchAll();
    }
}