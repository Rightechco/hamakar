<?php
class InvoiceController extends Controller {
    
    private $db;

    public function __construct() {
        $this->db = new Database;
        // بررسی لاگین بودن کاربر
        global $auth;
        if (isset($auth) && !$auth->check()) {
            redirect('index.php?page=login');
        }
    }

    // لیست فاکتورها
    public function index() {
        $this->invoices();
    }

    public function invoices() {
        // دریافت فاکتورها (مثال)
        $this->db->query("SELECT * FROM invoices ORDER BY id DESC");
        $invoices = $this->db->fetchAll();
        
        $this->view('admin/invoices/index', ['invoices' => $invoices]);
    }

    // ایجاد فاکتور جدید
    public function createInvoice() {
        // دریافت لیست کاربران برای انتخاب در فاکتور
        $this->db->query("SELECT * FROM users");
        $users = $this->db->fetchAll();
        
        $this->view('admin/invoices/create', ['users' => $users]);
    }

    // ذخیره فاکتور
    public function storeInvoice() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // منطق ذخیره سازی...
            // $this->db->query("INSERT INTO invoices ...");
            
            FlashMessage::set('فاکتور با موفقیت ایجاد شد');
            redirect('index.php?page=admin&action=invoices');
        }
    }

    // ویرایش فاکتور
    public function editInvoice($id) {
        $this->db->query("SELECT * FROM invoices WHERE id = :id");
        $this->db->bind(':id', $id);
        $invoice = $this->db->single();

        $this->view('admin/invoices/edit', ['invoice' => $invoice]);
    }

    // بروزرسانی فاکتور
    public function updateInvoice($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // منطق آپدیت...
            redirect('index.php?page=admin&action=invoices');
        }
    }

    // حذف فاکتور
    public function deleteInvoice($id) {
        $this->db->query("DELETE FROM invoices WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();
        
        FlashMessage::set('فاکتور حذف شد', 'warning');
        redirect('index.php?page=admin&action=invoices');
    }
    
    // مشاهده فاکتور
    public function viewInvoice($id) {
        $this->db->query("SELECT * FROM invoices WHERE id = :id");
        $this->db->bind(':id', $id);
        $invoice = $this->db->single();
        
        $this->view('admin/invoices/view', ['invoice' => $invoice]);
    }
}