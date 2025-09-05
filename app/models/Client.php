<?php
// app/models/Client.php
// این مدل برای تعامل با جدول 'clients' در پایگاه داده استفاده می شود.
// شامل متدهای CRUD برای مدیریت کارفرمایان است.

class Client { 
    private $db; 

    public function __construct() {
        $this->db = new Database(); 
    }

    /**
     * getAllClients
     * تمام کارفرمایان را از دیتابیس دریافت می کند.
     * شامل ستون company_name و user_id و contact_person است.
     *
     * @return array آرایه ای از اشیاء کارفرما.
     */
    public function getAllClients() {
        $this->db->query('SELECT id, name, contact_person, email, phone, address, user_id, company_name, user_type, national_code, birth_date, profile_image, national_card_image, company_logo_image, created_at, updated_at FROM clients ORDER BY created_at DESC');
        return $this->db->fetchAll();
    }

    /**
     * findById
     * کارفرما را بر اساس ID پیدا می کند.
     *
     * @param int $id شناسه کارفرما.
     * @return object|false شیء کارفرما یا false در صورت عدم یافتن.
     */
    public function findById($id) {
        $this->db->query('SELECT * FROM clients WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    
    /**
     * findByEmail
     * کارفرما را بر اساس آدرس ایمیل پیدا می کند.
     * برای بررسی یکتا بودن ایمیل قبل از ایجاد/به روزرسانی استفاده می شود.
     *
     * @param string $email آدرس ایمیل کارفرما.
     * @return object|false شیء کارفرما یا false در صورت عدم یافتن.
     */
    public function findByEmail($email) {
        $this->db->query('SELECT id, name, email FROM clients WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->fetch();
    }

    /**
     * findByUserId
     * کارفرما را بر اساس user_id (شناسه کاربر مرتبط) پیدا می کند.
     * برای دسترسی پنل کارفرمایی مشتریان استفاده می شود.
     *
     * @param int $userId شناسه کاربر مرتبط در جدول users.
     * @return object|false شیء کارفرما یا false در صورت عدم یافتن.
     */
    public function findByUserId($userId) {
        $this->db->query('SELECT * FROM clients WHERE user_id = :user_id');
        $this->db->bind(':user_id', $userId);
        return $this->db->fetch();
    }

    /**
     * countAll
     * تعداد کل کارفرمایان را دریافت می کند.
     *
     * @return int تعداد کارفرمایان.
     */
    public function countAll() {
        $this->db->query('SELECT COUNT(*) as count FROM clients');
        $result = $this->db->fetch();
        return $result ? $result->count : 0;
    }

    /**
     * create
     * یک کارفرما جدید در دیتابیس ایجاد می کند.
     * شامل user_id و company_name و contact_person است.
     *
     * @param array $data آرایه ای شامل اطلاعات کارفرما (name, email, phone, address, user_id, company_name, contact_person).
     * @return bool true در صورت موفقیت، false در صورت خطا.
     */
    public function create($data) {
        $this->db->query('INSERT INTO clients (name, contact_person, company_name, user_type, national_code, birth_date, profile_image, national_card_image, company_logo_image, email, phone, address, user_id) VALUES (:name, :contact_person, :company_name, :user_type, :national_code, :birth_date, :profile_image, :national_card_image, :company_logo_image, :email, :phone, :address, :user_id)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':contact_person', $data['contact_person'] ?? null);
        $this->db->bind(':company_name', $data['company_name'] ?? null);
        $this->db->bind(':user_type', $data['user_type']);
        $this->db->bind(':national_code', $data['national_code'] ?? null);
        $this->db->bind(':birth_date', $data['birth_date'] ?? null);
        $this->db->bind(':profile_image', $data['profile_image'] ?? null);
        $this->db->bind(':national_card_image', $data['national_card_image'] ?? null);
        $this->db->bind(':company_logo_image', $data['company_logo_image'] ?? null);
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
     * اطلاعات مشتری موجود را به روزرسانی می کند.
     *
     * @param int $id شناسه مشتری.
     * @param array $data آرایه ای شامل اطلاعات جدید مشتری.
     * @return bool true در صورت موفقیت، false در صورت خطا.
     */
    public function update($id, $data) {
        $sql = 'UPDATE clients SET name = :name, contact_person = :contact_person, company_name = :company_name, user_type = :user_type, national_code = :national_code, birth_date = :birth_date, profile_image = :profile_image, national_card_image = :national_card_image, company_logo_image = :company_logo_image, email = :email, phone = :phone, address = :address, user_id = :user_id, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':contact_person', $data['contact_person'] ?? null);
        $this->db->bind(':company_name', $data['company_name'] ?? null);
        $this->db->bind(':user_type', $data['user_type']);
        $this->db->bind(':national_code', $data['national_code'] ?? null);
        $this->db->bind(':birth_date', $data['birth_date'] ?? null);
        $this->db->bind(':profile_image', $data['profile_image'] ?? null);
        $this->db->bind(':national_card_image', $data['national_card_image'] ?? null);
        $this->db->bind(':company_logo_image', $data['company_logo_image'] ?? null);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':user_id', $data['user_id'] ?? null);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    /**
     * delete
     * یک کارفرما را از دیتابیس حذف می کند.
     *
     * @param int $id شناسه کارفرما.
     * @return bool true در صورت موفقیت، false در صورت خطا.
     */
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
        if (!empty($filters['start_date'])) {
            $this->db->bind(':start_date', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $this->db->bind(':end_date', $filters['end_date']);
        }
        
        return $this->db->fetchAll();
    }
}