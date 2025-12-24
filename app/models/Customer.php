<?php
// public_html/app/models/Customer.php
// این مدل برای تعامل با جدول 'customers' در پایگاه داده استفاده می شود.

class Customer {
    private $db; // یک نمونه از کلاس Database برای تعامل با دیتابیس

    public function __construct() {
        $this->db = new Database(); // مقداردهی اولیه اتصال به دیتابیس
    }

    /**
     * getAllCustomers
     * تمام مشتریان را از دیتابیس دریافت می کند.
     *
     * @return array آرایه ای از اشیاء مشتری.
     */
    public function getAllCustomers() {
        $this->db->query('SELECT id, name, email, phone, address, created_at, updated_at FROM customers ORDER BY created_at DESC');
        return $this->db->fetchAll();
    }

    /**
     * findById
     * مشتری را بر اساس ID پیدا می کند.
     *
     * @param int $id شناسه مشتری.
     * @return object|false شیء مشتری یا false در صورت عدم یافتن.
     */
    public function findById($id) {
        $this->db->query('SELECT id, name, email, phone, address, created_at, updated_at FROM customers WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }

    /**
     * findByEmail
     * مشتری را بر اساس آدرس ایمیل پیدا می کند.
     * برای بررسی یکتا بودن ایمیل قبل از ایجاد/به روزرسانی استفاده می شود.
     *
     * @param string $email آدرس ایمیل مشتری.
     * @return object|false شیء مشتری یا false در صورت عدم یافتن.
     */
    public function findByEmail($email) {
        $this->db->query('SELECT id, name, email FROM customers WHERE email = :email'); // فقط فیلدهای لازم را انتخاب کنید
        $this->db->bind(':email', $email);
        return $this->db->fetch();
    }

    /**
     * create
     * یک مشتری جدید در دیتابیس ایجاد می کند.
     *
     * @param array $data آرایه ای شامل اطلاعات مشتری (name, email, phone, address).
     * @return bool true در صورت موفقیت، false در صورت خطا.
     */
    public function create($data) {
        $this->db->query('INSERT INTO customers (name, email, phone, address) VALUES (:name, :email, :phone, :address)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);
        return $this->db->execute();
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
        $sql = 'UPDATE customers SET name = :name, email = :email, phone = :phone, address = :address, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * delete
     * یک مشتری را از دیتابیس حذف می کند.
     *
     * @param int $id شناسه مشتری.
     * @return bool true در صورت موفقیت، false در صورت خطا.
     */
    public function delete($id) {
        $this->db->query('DELETE FROM customers WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}