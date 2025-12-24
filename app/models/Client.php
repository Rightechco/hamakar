<?php
// app/models/Client.php - نسخه نهایی و کامل با قابلیت تامین‌کنندگان

class Client { 
    private $db; 

    public function __construct() {
        $this->db = new Database(); 
    }

    /**
     * getAll
     * تمام مشتریان و تامین‌کنندگان را از دیتابیس دریافت می‌کند.
     *
     * @return array آرایه‌ای از اشیاء مشتری.
     */
    public function getAll() {
        $this->db->query('SELECT * FROM clients ORDER BY name ASC');
        return $this->db->fetchAll();
    }

    /**
     * getAllClients
     * تمام مشتریان (با client_type = 'customer') را از دیتابیس دریافت می‌کند.
     *
     * @return array آرایه‌ای از اشیاء مشتری.
     */
    public function getAllClients() {
        // فیلد client_type به SELECT اضافه شده است
        $this->db->query('SELECT id, name, contact_person, email, phone, address, user_id, company_name, user_type, client_type, national_code, birth_date, profile_image, national_card_image, company_logo_image, created_at, updated_at FROM clients ORDER BY created_at DESC');
        return $this->db->fetchAll();
    }


   

    /**
     * findById
     * مشتری یا تامین‌کننده را بر اساس ID پیدا می کند.
     *
     * @param int $id شناسه مشتری.
     * @return object|false شیء مشتری یا false در صورت عدم یافتن.
     */
    public function findById($id) {
        $this->db->query('SELECT * FROM clients WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    
    /**
     * findByEmail
     * مشتری یا تامین‌کننده را بر اساس آدرس ایمیل پیدا می کند.
     * برای بررسی یکتا بودن ایمیل قبل از ایجاد/به روزرسانی استفاده می شود.
     *
     * @param string $email آدرس ایمیل مشتری.
     * @return object|false شیء مشتری یا false در صورت عدم یافتن.
     */
    public function findByEmail($email) {
        $this->db->query('SELECT id, name, email FROM clients WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->fetch();
    }

    /**
     * findByUserId
     * مشتری یا تامین‌کننده را بر اساس user_id (شناسه کاربر مرتبط) پیدا می کند.
     * برای دسترسی پنل کارفرمایی مشتریان استفاده می شود.
     *
     * @param int $userId شناسه کاربر مرتبط در جدول users.
     * @return object|false شیء مشتری یا false در صورت عدم یافتن.
     */
    public function findByUserId($userId) {
        $this->db->query('SELECT * FROM clients WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        return $this->db->fetch();
    }

    /**
     * countAll
     * تعداد کل مشتریان و تامین‌کنندگان را دریافت می کند.
     *
     * @return int تعداد کل.
     */
    public function countAll() {
        $this->db->query('SELECT COUNT(*) as count FROM clients');
        $result = $this->db->fetch();
        return $result ? $result->count : 0;
    }
    
    /**
     * create
     * یک مشتری یا تامین‌کننده جدید در دیتابیس ایجاد می کند.
     *
     * @param array $data آرایه ای شامل اطلاعات کامل.
     * @return int|false شناسه رکورد جدید یا false.
     */
    public function create($data) {
        $this->db->query('INSERT INTO clients (name, contact_person, company_name, user_type, client_type, national_code, birth_date, profile_image, national_card_image, company_logo_image, economic_code, registration_number, email, phone, address, user_id) VALUES (:name, :contact_person, :company_name, :user_type, :client_type, :national_code, :birth_date, :profile_image, :national_card_image, :company_logo_image, :economic_code, :registration_number, :email, :phone, :address, :user_id)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':contact_person', $data['contact_person'] ?? null);
        $this->db->bind(':company_name', $data['company_name'] ?? null);
        $this->db->bind(':user_type', $data['user_type']);
        $this->db->bind(':client_type', $data['client_type'] ?? 'customer'); // مقدار پیش‌فرض
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

    public function update($id, $data) {
        $sql = 'UPDATE clients SET name = :name, contact_person = :contact_person, company_name = :company_name, user_type = :user_type, client_type = :client_type, national_code = :national_code, birth_date = :birth_date, profile_image = :profile_image, national_card_image = :national_card_image, company_logo_image = :company_logo_image, economic_code = :economic_code, registration_number = :registration_number, email = :email, phone = :phone, address = :address, user_id = :user_id, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':contact_person', $data['contact_person'] ?? null);
        $this->db->bind(':company_name', $data['company_name'] ?? null);
        $this->db->bind(':user_type', $data['user_type']);
        $this->db->bind(':client_type', $data['client_type'] ?? 'customer'); // مقدار پیش‌فرض
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

    
    public function delete($id) {
        $this->db->query('DELETE FROM clients WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
     public function getFilteredClients($filters) {
        $sql = "SELECT * FROM clients WHERE 1=1";

        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE :search OR company_name LIKE :search OR email LIKE :search)";
        }
        if (!empty($filters['user_type'])) {
            $sql .= " AND user_type = :user_type";
        }
        if (!empty($filters['client_type'])) {
            $sql .= " AND client_type = :client_type";
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
        if (!empty($filters['user_type'])) {
            $this->db->bind(':user_type', $filters['user_type']);
        }
        if (!empty($filters['client_type'])) {
            $this->db->bind(':client_type', $filters['client_type']);
        }
        if (!empty($filters['start_date'])) {
            $this->db->bind(':start_date', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $this->db->bind(':end_date', $filters['end_date']);
        }
        
        return $this->db->fetchAll();
    }
    public function getAllCustomers() {
        $this->db->query("SELECT id, name, company_name, phone, address FROM clients WHERE client_type = 'customer' ORDER BY created_at DESC");
        return $this->db->fetchAll();
    }
    
    /**
     * getAllVendors
     * تمام تامین کنندگان را از دیتابیس دریافت می کند.
     * @return array
     */
    public function getAllVendors() {
        $this->db->query("SELECT id, name, company_name, phone, address FROM clients WHERE client_type = 'vendor' ORDER BY created_at DESC");
        return $this->db->fetchAll();
    }
}