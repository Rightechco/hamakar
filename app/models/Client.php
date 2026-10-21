<?php
// app/models/Client.php - نسخه نهایی و کاملاً بهینه‌شده با پشتیبانی از مشتریان و تامین‌کنندگان

class Client { 
    private $db; 

    public function __construct() {
        $this->db = new Database(); 
    }

    /**
     * متد کمکی برای دریافت یک رکورد متناسب با متدهای موجود در کلاس Database
     */
    private function dbFetch() {
        if (method_exists($this->db, 'single')) {
            return $this->db->single();
        }
        return $this->db->fetch();
    }

    /**
     * متد کمکی برای دریافت همه رکوردهای متناسب با متدهای موجود در کلاس Database
     */
    private function dbFetchAll() {
        if (method_exists($this->db, 'resultSet')) {
            return $this->db->resultSet();
        }
        return $this->db->fetchAll();
    }

    /**
     * getAll
     * تمام مشتریان و تامین‌کنندگان را از دیتابیس دریافت می‌کند.
     */
    public function getAll() {
        $this->db->query('SELECT * FROM clients ORDER BY name ASC');
        return $this->dbFetchAll();
    }

    /**
     * getAllClients
     * تمام مشتریان را از دیتابیس دریافت می‌کند.
     */
    public function getAllClients() {
        $this->db->query('SELECT id, name, contact_person, email, phone, address, user_id, company_name, user_type, client_type, national_code, birth_date, profile_image, national_card_image, company_logo_image, created_at, updated_at FROM clients ORDER BY created_at DESC');
        return $this->dbFetchAll();
    }

    /**
     * findById
     * مشتری یا تامین‌کننده را بر اساس ID پیدا می‌کند.
     */
    public function findById($id) {
        $this->db->query('SELECT * FROM clients WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->dbFetch();
    }

    /**
     * findByEmail
     * مشتری یا تامین‌کننده را بر اساس آدرس ایمیل پیدا می‌کند.
     */
    public function findByEmail($email) {
        $this->db->query('SELECT id, name, email FROM clients WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->dbFetch();
    }

    /**
     * findByUserId
     * مشتری یا تامین‌کننده را بر اساس user_id پیدا می‌کند.
     */
    public function findByUserId($userId) {
        $this->db->query('SELECT * FROM clients WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        return $this->dbFetch();
    }

    /**
     * countAll
     * تعداد کل مشتریان و تامین‌کنندگان را دریافت می‌کند.
     */
    public function countAll() {
        $this->db->query('SELECT COUNT(*) as count FROM clients');
        $result = $this->dbFetch();
        return $result ? (int)$result->count : 0;
    }
    
    /**
     * create
     * یک مشتری یا تامین‌کننده جدید در دیتابیس ایجاد می‌کند.
     */
    public function create($data) {
        $this->db->query('INSERT INTO clients (name, contact_person, company_name, user_type, client_type, national_code, birth_date, profile_image, national_card_image, company_logo_image, economic_code, registration_number, email, phone, address, user_id) VALUES (:name, :contact_person, :company_name, :user_type, :client_type, :national_code, :birth_date, :profile_image, :national_card_image, :company_logo_image, :economic_code, :registration_number, :email, :phone, :address, :user_id)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':contact_person', $data['contact_person'] ?? null);
        $this->db->bind(':company_name', $data['company_name'] ?? null);
        $this->db->bind(':user_type', $data['user_type'] ?? 'real');
        $this->db->bind(':client_type', $data['client_type'] ?? 'customer');
        $this->db->bind(':national_code', $data['national_code'] ?? null);
        $this->db->bind(':birth_date', $data['birth_date'] ?? null);
        $this->db->bind(':profile_image', $data['profile_image'] ?? null);
        $this->db->bind(':national_card_image', $data['national_card_image'] ?? null);
        $this->db->bind(':company_logo_image', $data['company_logo_image'] ?? null);
        $this->db->bind(':economic_code', $data['economic_code'] ?? null);
        $this->db->bind(':registration_number', $data['registration_number'] ?? null);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':user_id', $data['user_id'] ?? null);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * update
     * اطلاعات یک مشتری موجود را بروزرسانی می‌کند.
     */
    public function update($id, $data) {
        $sql = 'UPDATE clients SET name = :name, contact_person = :contact_person, company_name = :company_name, user_type = :user_type, client_type = :client_type, national_code = :national_code, birth_date = :birth_date, profile_image = :profile_image, national_card_image = :national_card_image, company_logo_image = :company_logo_image, economic_code = :economic_code, registration_number = :registration_number, email = :email, phone = :phone, address = :address, user_id = :user_id, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':contact_person', $data['contact_person'] ?? null);
        $this->db->bind(':company_name', $data['company_name'] ?? null);
        $this->db->bind(':user_type', $data['user_type'] ?? 'real');
        $this->db->bind(':client_type', $data['client_type'] ?? 'customer');
        $this->db->bind(':national_code', $data['national_code'] ?? null);
        $this->db->bind(':birth_date', $data['birth_date'] ?? null);
        $this->db->bind(':profile_image', $data['profile_image'] ?? null);
        $this->db->bind(':national_card_image', $data['national_card_image'] ?? null);
        $this->db->bind(':company_logo_image', $data['company_logo_image'] ?? null);
        $this->db->bind(':economic_code', $data['economic_code'] ?? null);
        $this->db->bind(':registration_number', $data['registration_number'] ?? null);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':user_id', $data['user_id'] ?? null);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * delete
     * یک مشتری را بر اساس ID حذف می‌کند.
     */
    public function delete($id) {
        $this->db->query('DELETE FROM clients WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * getFilteredClients
     * دریافت لیست مشتریان به‌صورت فیلترشده (پشتیبانی هوشمند از ورودی آرایه‌ای یا ورودی‌های تک‌تک)
     */
    public function getFilteredClients($search = '', $user_type = '', $start_date = '', $end_date = '', $client_type = '') {
        $filters = [];

        // اگر ورودی اول یک آرایه باشد (مانند $filters = ['search' => '...'])
        if (is_array($search)) {
            $filters = $search;
        } else {
            $filters = [
                'search' => $search,
                'user_type' => $user_type,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'client_type' => $client_type
            ];
        }

        $sql = "SELECT * FROM clients WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE :search OR company_name LIKE :search OR email LIKE :search OR phone LIKE :search OR national_code LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['user_type'])) {
            $sql .= " AND user_type = :user_type";
            $params[':user_type'] = $filters['user_type'];
        }
        if (!empty($filters['client_type'])) {
            $sql .= " AND client_type = :client_type";
            $params[':client_type'] = $filters['client_type'];
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND DATE(created_at) >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND DATE(created_at) <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        $sql .= " ORDER BY created_at DESC";
        $this->db->query($sql);

        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }
        
        return $this->dbFetchAll();
    }

    /**
     * getAllCustomers
     * دریافت لیست مشتریان معمولی (client_type = 'customer')
     */
    public function getAllCustomers() {
        $this->db->query("SELECT id, name, company_name, phone, address FROM clients WHERE client_type = 'customer' ORDER BY created_at DESC");
        return $this->dbFetchAll();
    }
    
    /**
     * getAllVendors
     * دریافت لیست تامین‌کنندگان (client_type = 'vendor')
     */
    public function getAllVendors() {
        $this->db->query("SELECT id, name, company_name, phone, address FROM clients WHERE client_type = 'vendor' ORDER BY created_at DESC");
        return $this->dbFetchAll();
    }
}