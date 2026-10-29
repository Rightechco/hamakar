<?php
// app/controllers/AdminController.php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// فراخوانی فایل‌ها
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Contract.php';
require_once __DIR__ . '/../models/Invoice.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/Report.php';
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../models/TicketReply.php';
require_once __DIR__ . '/../config/sms.php';
require_once __DIR__ . '/../lib/SmsService.php';
require_once __DIR__ . '/../lib/JalaliDate.php';
require_once __DIR__ . '/../models/Attendance.php';
require_once __DIR__ . '/../models/LeaveRequest.php';
require_once __DIR__ . '/../models/Account.php';
require_once __DIR__ . '/../models/JournalVoucher.php';
require_once __DIR__ . '/../models/JournalEntry.php';
require_once __DIR__ . '/../models/Expense.php';
require_once __DIR__ . '/../models/FinancialReport.php';
require_once __DIR__ . '/../models/Budget.php';
require_once __DIR__ . '/../models/FixedAsset.php';
require_once __DIR__ . '/../models/ClientLog.php';
require_once __DIR__ . '/../models/ClientContact.php';
require_once __DIR__ . '/../models/Announcement.php';
require_once __DIR__ . '/../models/TrainingNeed.php';
require_once __DIR__ . '/../models/TrainingCourse.php';
require_once __DIR__ . '/../models/SkillAssessment.php';
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/MarketingLead.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Purchase.php';
require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Service.php';
require_once __DIR__ . '/../models/InvoiceItem.php';

class AdminController {
    private $auth;
    private $userModel, $clientModel, $contractModel, $invoiceModel, $paymentModel, $projectModel, $taskModel, $reportModel, $ticketModel, $ticketReplyModel, $smsService, $attendanceModel, $leaveRequestModel, $accountModel, $journalVoucherModel, $journalEntryModel, $expenseModel, $financialReportModel, $budgetModel, $fixedAssetModel, $clientLogModel, $clientContactModel, $announcementModel, $trainingNeedModel, $trainingCourseModel, $skillAssessmentModel, $categoryModel,$payrollSettingModel, $serviceModel, $payrollModel;
    private $db;
    private $marketingLeadModel;
    private $productModel, $purchaseModel, $saleModel, $invoiceItemModel;

    public function __construct() {
        $this->auth = new Auth();
        $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
        
        $this->userModel = new User();
        $this->clientModel = new Client();
        $this->contractModel = new Contract();
        $this->invoiceModel = new Invoice();
        $this->paymentModel = new Payment();
        $this->projectModel = new Project();
        $this->taskModel = new Task();
        $this->reportModel = new Report();
        $this->ticketModel = new Ticket();
        $this->ticketReplyModel = new TicketReply();
        $this->smsService = new SmsService();
        $this->attendanceModel = new Attendance();
        $this->leaveRequestModel = new LeaveRequest();
        $this->accountModel = new Account();
        $this->journalVoucherModel = new JournalVoucher();
        $this->journalEntryModel = new JournalEntry();
        $this->expenseModel = new Expense();
        $this->financialReportModel = new FinancialReport();
        $this->budgetModel = new Budget();
        $this->fixedAssetModel = new FixedAsset();
        $this->clientLogModel = new ClientLog();
        $this->clientContactModel = new ClientContact();
        $this->announcementModel = new Announcement();
        $this->trainingNeedModel = new TrainingNeed();
        $this->trainingCourseModel = new TrainingCourse();
        $this->skillAssessmentModel = new SkillAssessment();
        $this->db = new Database(); 
        $this->marketingLeadModel = new MarketingLead();
        $this->productModel = new Product(); 
        $this->purchaseModel = new Purchase();
        $this->saleModel = new Sale();
        $this->categoryModel = new Category();
        $this->serviceModel = new Service();
        $this->invoiceItemModel = new InvoiceItem();
        $this->payrollSettingModel = new PayrollSetting();
        $this->payrollModel = new Payroll();
        
    }

     
    public function dashboard() {
        $usersCount = $this->userModel->countAll();
        $clientsCount = $this->clientModel->countAll();
        $contractsCount = $this->contractModel->countAll();
        $invoicesCount = $this->invoiceModel->countAll();
        
        $user = $this->auth->user();
        $announcements = $this->announcementModel->getAnnouncementsForRole($user->role);

        view('admin/dashboard', [
            'layout' => 'admin_layout',
            'title' => 'Admin Dashboard',
            'usersCount' => $usersCount,
            'clientsCount' => $clientsCount,
            'contractsCount' => $contractsCount,
            'invoicesCount' => $invoicesCount,
            'announcements' => $announcements
        ]);
    }
    
    public function users() {
        $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
        
        $filters = [
            'role' => sanitize($_GET['role'] ?? null),
            'status' => sanitize($_GET['status'] ?? null),
            'start_date' => $this->convertJalaliToGregorianString($_GET['start_date'] ?? null),
            'end_date' => $this->convertJalaliToGregorianString($_GET['end_date'] ?? null),
            'search' => sanitize($_GET['search'] ?? null),
        ];

        $users = $this->userModel->getFilteredUsers($filters);
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($users);
            return;
        }

        view('admin/users/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت کاربران',
            'users' => $users,
            'filters' => $filters
        ]);
    }
    public function exportUsersToExcel() {
        $this->auth->restrict(['admin', 'accountant']);
        $users = $this->userModel->getAllUsers();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'نام');
        $sheet->setCellValue('B1', 'ایمیل');
        $sheet->setCellValue('C1', 'نقش');
        $sheet->setCellValue('D1', 'وضعیت');
        $sheet->setCellValue('E1', 'تاریخ ثبت');

        $rowNumber = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $rowNumber, $user->name);
            $sheet->setCellValue('B' . $rowNumber, $user->email);
            $sheet->setCellValue('C' . $rowNumber, $user->role);
            $sheet->setCellValue('D' . $rowNumber, $user->status);
            $sheet->setCellValue('E' . $rowNumber, htmlspecialchars(jdate('Y/m/d', strtotime($user->created_at))));
            $rowNumber++;
        }

        ob_clean();
        ob_end_flush();
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="users_export.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function showImportUsersForm() {
        $this->auth->restrict(['admin']);
        view('admin/users/import', [
            'layout' => 'admin_layout',
            'title' => 'ورود کاربران از اکسل'
        ]);
    }
    public function importUsers() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
            $file = $_FILES['excel_file']['tmp_name'];

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                $importedCount = 0;

                for ($row = 2; $row <= $highestRow; $row++) { // شروع از ردیف دوم (بعد از هدر)
                    $data = [
                        'name'     => $sheet->getCell('A' . $row)->getValue(),
                        'email'    => $sheet->getCell('B' . $row)->getValue(),
                        'password' => $sheet->getCell('C' . $row)->getValue() ?? 'password', // کلمه عبور پیش‌فرض
                        'role'     => $sheet->getCell('D' . $row)->getValue() ?? 'employee', // نقش پیش‌فرض
                    ];

                    if (!empty($data['email']) && !empty($data['name'])) {
                        if (!$this->userModel->findByEmail($data['email'])) { // بررسی عدم تکراری بودن ایمیل
                            if ($this->userModel->create($data)) {
                                $importedCount++;
                            }
                        }
                    }
                }
                
                FlashMessage::set('message', "تعداد $importedCount کاربر با موفقیت از فایل اکسل وارد شد.");

            } catch (Exception $e) {
                FlashMessage::set('message', 'خطا در خواندن فایل اکسل: ' . $e->getMessage(), 'error');
            }

            redirect(APP_URL . '/index.php?page=admin&action=users');
        }
    }

    public function createUser() {
        view('admin/users/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'Create User',
            'user' => null
        ]);
    }

    public function storeUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'password' => sanitize($_POST['password'] ?? ''),
                'password_confirmation' => sanitize($_POST['password_confirmation'] ?? ''),
                'role' => sanitize($_POST['role'] ?? 'client')
            ];

            $validator = Validator::make($data);
            $isValid = $validator->validate([
                'name' => 'required|min:3',
                'email' => 'required|email',
                'password' => 'required|min:6|confirmed',
                'role' => 'required'
            ]);

            if ($isValid) {
                if ($this->userModel->findByEmail($data['email'])) {
                    FlashMessage::set('message', 'ایمیل وارد شده قبلاً استفاده شده است.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=users_create');
                }

                if ($this->userModel->create($data)) {
                    FlashMessage::set('message', 'کاربر با موفقیت ایجاد شد.');
                    redirect(APP_URL . '/index.php?page=admin&action=users');
                } else {
                    FlashMessage::set('message', 'خطا در ایجاد کاربر.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=users_create');
                }
            } else {
                FlashMessage::set('message', implode('<br>', array_merge(...array_values($validator->errors()))), 'error');
                redirect(APP_URL . '/index.php?page=admin&action=users_create');
            }
        } else {
            redirect(APP_URL . '/index.php?page=admin&action=users_create');
        }
    }

    public function editUser($id) {
        $user = $this->userModel->findById($id);
        if (!$user) {
            FlashMessage::set('message', 'کاربر مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=users');
        }
        view('admin/users/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'Edit User',
            'user' => $user
        ]);
    }

    public function updateUser($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'email' => sanitize($_POST['email'] ?? ''),
                'role' => sanitize($_POST['role'] ?? 'client'),
                'status' => sanitize($_POST['status'] ?? 'active')
            ];

            $validator = Validator::make($data);
            $isValid = $validator->validate([
                'name' => 'required|min:3',
                'email' => 'required|email',
                'role' => 'required',
                'status' => 'required'
            ]);

            if ($isValid) {
                $existingUser = $this->userModel->findByEmail($data['email']);
                if ($existingUser && $existingUser->id != $id) {
                    FlashMessage::set('message', 'ایمیل وارد شده قبلاً توسط کاربر دیگری استفاده شده است.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=users_edit&id=' . $id);
                }

                if ($this->userModel->update($id, $data)) {
                    FlashMessage::set('message', 'کاربر با موفقیت به روزرسانی شد.');
                    redirect(APP_URL . '/index.php?page=admin&action=users');
                } else {
                    FlashMessage::set('message', 'خطا در به روزرسانی کاربر.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=users_edit&id=' . $id);
                }
            } else {
                FlashMessage::set('message', implode('<br>', array_merge(...array_values($validator->errors()))), 'error');
                redirect(APP_URL . '/index.php?page=admin&action=users_edit&id=' . $id);
            }
        } else {
            redirect(APP_URL . '/index.php?page=admin&action=users');
        }
    }

    public function deleteUser($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->userModel->delete($id)) {
                FlashMessage::set('message', 'کاربر با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف کاربر.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=users');
    }
    
    public function clients() { 
        $clients = $this->clientModel->getAllClients(); 
        $users = $this->userModel->getAllUsers(); 
        $userNames = [];
        foreach($users as $user) {
            $userNames[$user->id] = $user->name;
        }

        view('admin/clients/index', [ 
            'layout' => 'admin_layout',
            'title' => 'مدیریت مشتریان', 
            'clients' => $clients, 
            'userNames' => $userNames 
        ]);
    }

    public function createClient() { 
        $users = $this->userModel->getAllUsers(); 
        view('admin/clients/create_edit', [ 
            'layout' => 'admin_layout',
            'title' => 'افزودن مشتری جدید',
            'client' => null,
            'users' => $users 
        ]);
    }

     public function storeClient() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'contact_person' => sanitize($_POST['contact_person'] ?? null),
                'email' => sanitize($_POST['email'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'address' => sanitize($_POST['address'] ?? ''),
                'user_id' => null, // موقتاً خالی
                'company_name' => sanitize($_POST['company_name'] ?? null),
                'user_type' => sanitize($_POST['user_type'] ?? 'real'),
                'client_type' => sanitize($_POST['client_type'] ?? 'customer'),
                'national_code' => sanitize($_POST['national_code'] ?? null),
                'birth_date' => $this->convertJalaliToGregorianString($_POST['birth_date_jalali'] ?? ''),
                'economic_code' => sanitize($_POST['economic_code'] ?? null),
                'registration_number' => sanitize($_POST['registration_number'] ?? null),
                'profile_image' => null,
                'national_card_image' => null,
                'company_logo_image' => null,
            ];
            
            $uploadErrors = [];
            if (!empty($_FILES['profile_image']['name'])) {
                $uploadResult = $this->uploadFile($_FILES['profile_image'], 'clients_images');
                if ($uploadResult['success']) { $data['profile_image'] = $uploadResult['path']; }
                else { $uploadErrors[] = 'خطا در آپلود تصویر پروفایل: ' . $uploadResult['message']; }
            }
            if (!empty($_FILES['national_card_image']['name'])) {
                $uploadResult = $this->uploadFile($_FILES['national_card_image'], 'clients_images');
                if ($uploadResult['success']) { $data['national_card_image'] = $uploadResult['path']; }
                else { $uploadErrors[] = 'خطا در آپلود تصویر کارت ملی: ' . $uploadResult['message']; }
            }
            if (!empty($_FILES['company_logo_image']['name'])) {
                $uploadResult = $this->uploadFile($_FILES['company_logo_image'], 'clients_images');
                if ($uploadResult['success']) { $data['company_logo_image'] = $uploadResult['path']; }
                else { $uploadErrors[] = 'خطا در آپلود لوگوی شرکت: ' . $uploadResult['message']; }
            }
            if (!empty($uploadErrors)) {
                 FlashMessage::set('message', implode('<br>', $uploadErrors), 'error');
                 redirect_back();
                 return;
            }

            $validationRules = [
                'name' => 'required|min:3',
                'email' => 'required|email',
                'phone' => 'required',
                'address' => 'required',
            ];
            $validator = Validator::make($data);
            $isValid = $validator->validate($validationRules);

            if (!$isValid) {
                FlashMessage::set('message', implode('<br>', array_merge(...array_values($validator->errors()))), 'error');
                redirect_back();
                return;
            }

            if ($this->clientModel->findByEmail($data['email'])) {
                FlashMessage::set('message', 'ایمیل وارد شده قبلاً برای مشتری دیگری استفاده شده است.', 'error');
                redirect_back();
                return;
            }

            $contactUserId = null;
            if ($data['user_type'] === 'legal') {
                // ✅ منطق جدید برای مشتری حقوقی: ایجاد کاربر از روی نام و شماره تماس شرکت
                $contactName = $data['contact_person'] ?? $data['company_name'];
                $contactMobile = $data['phone'];
                $contactEmail = $data['email'];

                if (!empty($contactName) && !empty($contactMobile)) {
                    $existingUser = $this->userModel->findByMobileNumber($contactMobile);
                    if (!$existingUser) {
                        $contactUserId = $this->userModel->create([
                            'name' => $contactName,
                            'email' => $contactEmail,
                            'password' => 'password',
                            'role' => 'client',
                            'status' => 'active',
                            'mobile_number' => $contactMobile,
                        ], true);
                    } else {
                        $contactUserId = $existingUser->id;
                    }
                }
            } else { // منطق مشتری حقیقی
                $contactName = $data['name'];
                $contactMobile = $data['phone'];
                $contactEmail = $data['email'];

                if (!empty($contactName) && !empty($contactMobile)) {
                    $existingUser = $this->userModel->findByMobileNumber($contactMobile);
                    if (!$existingUser) {
                        $contactUserId = $this->userModel->create([
                            'name' => $contactName,
                            'email' => $contactEmail,
                            'password' => 'password',
                            'role' => 'client',
                            'status' => 'active',
                            'mobile_number' => $contactMobile,
                        ], true);
                    } else {
                        $contactUserId = $existingUser->id;
                    }
                }
            }
            $data['user_id'] = $contactUserId;
            
            $clientId = $this->clientModel->create($data);
            
            if ($clientId) {
                if ($data['user_type'] === 'legal' && isset($_POST['contacts'])) {
                    foreach ($_POST['contacts'] as $contact) {
                        if (empty($contact['name']) || empty($contact['phone'])) continue;
                        
                        $existingUser = $this->userModel->findByMobileNumber(sanitize($contact['phone']));
                        if (!$existingUser) {
                           $contactUserId = $this->userModel->create([
                                'name' => sanitize($contact['name']),
                                'email' => sanitize($contact['email'] ?? ''),
                                'mobile_number' => sanitize($contact['phone']),
                                'role' => 'client_contact',
                                'status' => 'active',
                                'password' => 'password',
                            ], true);
                        } else {
                            $contactUserId = $existingUser->id;
                        }

                        if ($contactUserId) {
                            $this->clientContactModel->create([
                                'client_id' => $clientId,
                                'user_id' => $contactUserId,
                                'position' => sanitize($contact['position'] ?? null)
                            ]);
                        }
                    }
                }

                FlashMessage::set('message', 'مشتری با موفقیت ایجاد شد.');
                redirect(APP_URL . '/index.php?page=admin&action=clients');
            } else {
                FlashMessage::set('message', 'خطا در ایجاد مشتری.', 'error');
                redirect_back();
            }
        } else {
            redirect(APP_URL . '/index.php?page=admin&action=clients_create');
        }
    }

    public function editClient($id) {
        $client = $this->clientModel->findById($id);
        
        if (!$client) {
            FlashMessage::set('message', 'مشتری مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=clients');
            return;
        }

        // برای مشتریان حقوقی، اطلاعات شخص مسئول را از جدول users می‌خوانیم
        $client->contact_mobile_number = null;
        if ($client->user_type === 'legal' && $client->user_id) {
            $linkedUser = $this->userModel->findById($client->user_id);
            if ($linkedUser) {
                $client->contact_mobile_number = $linkedUser->mobile_number;
            }
        }
        
        $contacts = $client->user_type === 'legal' ? $this->clientContactModel->getContactsByClientId($id) : [];

        view('admin/clients/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'ویرایش مشتری',
            'client' => $client,
            'users' => $this->userModel->getAllUsers(),
            'contacts' => $contacts
        ]);
    }

      public function updateClient($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $client = $this->clientModel->findById($id);
            if (!$client) {
                FlashMessage::set('message', 'مشتری یافت نشد.', 'error');
                redirect(APP_URL . '/index.php?page=admin&action=clients');
                return;
            }

            $data = [
                'name' => sanitize($_POST['name'] ?? ''),
                'contact_person' => sanitize($_POST['contact_person'] ?? null),
                'email' => sanitize($_POST['email'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'address' => sanitize($_POST['address'] ?? ''),
                'user_id' => !empty($_POST['user_id']) ? sanitize($_POST['user_id']) : null,
                'company_name' => sanitize($_POST['company_name'] ?? null),
                'user_type' => sanitize($_POST['user_type'] ?? 'real'),
                'client_type' => sanitize($_POST['client_type'] ?? 'customer'),
                'national_code' => sanitize($_POST['national_code'] ?? null),
                'birth_date' => $this->convertJalaliToGregorianString($_POST['birth_date_jalali'] ?? ''),
                'economic_code' => sanitize($_POST['economic_code'] ?? null),
                'registration_number' => sanitize($_POST['registration_number'] ?? null),
                'profile_image' => $client->profile_image,
                'national_card_image' => $client->national_card_image,
                'company_logo_image' => $client->company_logo_image,
            ];

        // Handle file uploads
        $uploadErrors = [];
        if (!empty($_FILES['profile_image']['name'])) {
            $uploadResult = $this->uploadFile($_FILES['profile_image'], 'clients_images');
            if ($uploadResult['success']) { $data['profile_image'] = $uploadResult['path']; }
            else { $uploadErrors[] = 'خطا در آپلود تصویر پروفایل: ' . $uploadResult['message']; }
        }
        if (!empty($_FILES['national_card_image']['name'])) {
            $uploadResult = $this->uploadFile($_FILES['national_card_image'], 'clients_images');
            if ($uploadResult['success']) { $data['national_card_image'] = $uploadResult['path']; }
            else { $uploadErrors[] = 'خطا در آپلود تصویر کارت ملی: ' . $uploadResult['message']; }
        }
        if (!empty($_FILES['company_logo_image']['name'])) {
            $uploadResult = $this->uploadFile($_FILES['company_logo_image'], 'clients_images');
            if ($uploadResult['success']) { $data['company_logo_image'] = $uploadResult['path']; }
            else { $uploadErrors[] = 'خطا در آپلود لوگوی شرکت: ' . $uploadResult['message']; }
        }
        if (!empty($uploadErrors)) {
             FlashMessage::set('message', implode('<br>', $uploadErrors), 'error');
             redirect(APP_URL . '/index.php?page=admin&action=clients_edit&id=' . $id);
             return;
        }

        // Validate basic fields
       $validationRules = [
                'name' => 'required|min:3',
                'email' => 'required|email',
                'phone' => 'required',
                'address' => 'required',
            ];
            $validator = Validator::make($data);
            $isValid = $validator->validate($validationRules);

            if (!$isValid) {
                FlashMessage::set('message', implode('<br>', array_merge(...array_values($validator->errors()))), 'error');
                redirect_back();
                return;
            }

            // ✅ منطق به‌روزرسانی کاربر مرتبط برای مشتری حقیقی
            if ($data['user_type'] === 'real') {
                $contactName = $data['name'];
                $contactMobile = $data['phone'];
                $contactEmail = $data['email'];
                $contactUserId = null;

                $existingUser = $this->userModel->findByMobileNumber($contactMobile);
                if (!$existingUser) {
                    $contactUserId = $this->userModel->create([
                        'name' => $contactName,
                        'email' => $contactEmail,
                        'password' => 'password',
                        'role' => 'client',
                        'status' => 'active',
                        'mobile_number' => $contactMobile,
                    ], true);
                } else {
                    $contactUserId = $existingUser->id;
                }
                $data['user_id'] = $contactUserId;

            } else {
                // ✅ برای مشتری حقوقی، user_id از نام و شماره تماس شخص اصلی ایجاد می‌شود.
                $contactName = $data['contact_person'] ?? $data['company_name'];
                $contactMobile = $data['phone'];
                $contactEmail = $data['email'];

                $linkedUser = $this->userModel->findByMobileNumber($contactMobile);
                if (!$linkedUser) {
                    $contactUserId = $this->userModel->create([
                        'name' => $contactName,
                        'email' => $contactEmail,
                        'password' => 'password',
                        'role' => 'client',
                        'status' => 'active',
                        'mobile_number' => $contactMobile,
                    ], true);
                } else {
                    $contactUserId = $linkedUser->id;
                    $this->userModel->update($contactUserId, [
                        'name' => $contactName,
                        'email' => $contactEmail,
                        'mobile_number' => $contactMobile,
                        'role' => 'client',
                        'status' => 'active'
                    ]);
                }
                 $data['user_id'] = $contactUserId;
            }
            
            if ($this->clientModel->update($id, $data)) {
                if ($data['user_type'] === 'legal') {
                    $submittedContacts = $_POST['contacts'] ?? [];
                    $existingContacts = $this->clientContactModel->getContactsByClientId($id);
                    $existingContactIds = array_column($existingContacts, 'id');
                    $submittedContactIds = [];

                    foreach ($submittedContacts as $contact) {
                        $contactId = sanitize($contact['id']);
                        $contactUserId = sanitize($contact['user_id']);

                        if (!empty($contactId)) {
                            $submittedContactIds[] = $contactId;
                            // به‌روزرسانی رابط موجود
                            $this->clientContactModel->update($contactId, [
                                'position' => sanitize($contact['position'])
                            ]);
                            $this->userModel->update($contactUserId, [
                                'name' => sanitize($contact['name']),
                                'email' => sanitize($contact['email']),
                                'mobile_number' => sanitize($contact['phone'])
                            ]);
                        } else {
                            // ایجاد رابط جدید
                            $newContactUserId = $this->userModel->create([
                                'name' => sanitize($contact['name']),
                                'email' => sanitize($contact['email']),
                                'mobile_number' => sanitize($contact['phone']),
                                'password' => 'password',
                                'role' => 'client_contact',
                                'status' => 'active',
                            ], true);
                            if ($newContactUserId) {
                                $this->clientContactModel->create([
                                    'client_id' => $id,
                                    'user_id' => $newContactUserId,
                                    'position' => sanitize($contact['position'])
                                ]);
                            }
                        }
                    }

                    // حذف رابط‌های پاک شده
                    $contactsToDelete = array_diff($existingContactIds, $submittedContactIds);
                    foreach ($contactsToDelete as $contactId) {
                         $contact = $this->clientContactModel->findById($contactId);
                         if ($contact) {
                            $this->clientContactModel->delete($contactId);
                            $this->userModel->delete($contact->user_id);
                         }
                    }
                } else {
                    // اگر نوع به حقیقی تغییر کرد، تمام رابط‌های حقوقی قبلی را حذف کن
                    $this->clientContactModel->deleteAllByClientId($id);
                }

                FlashMessage::set('message', 'مشتری با موفقیت به‌روزرسانی شد.');
                redirect(APP_URL . '/index.php?page=admin&action=clients');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی مشتری.', 'error');
                redirect_back();
            }
        }
    }
    
    public function deleteClient($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->clientModel->delete($id)) {
                FlashMessage::set('message', 'مشتری با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف مشتری.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=clients');
    }

    public function viewClientLogs($id) {
        $client = $this->clientModel->findById($id);
        if (!$client) {
            FlashMessage::set('message', 'مشتری مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=clients');
        }
        $logs = $this->clientLogModel->getLogsByClientId($id);
        $contacts = $client->user_type == 'legal' ? $this->clientContactModel->getContactsByClientId($id) : [];

        view('admin/clients/logs', [
            'layout' => 'admin_layout',
            'title' => 'لاگ های مشتری',
            'client' => $client,
            'logs' => $logs,
            'contacts' => $contacts
        ]);
    }

    public function storeClientLog() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'client_id' => sanitize($_POST['client_id'] ?? ''),
                'log_type' => sanitize($_POST['log_type'] ?? ''),
                'description' => sanitize($_POST['description'] ?? ''),
                'log_date' => sanitize($_POST['log_date'] ?? date('Y-m-d H:i:s')),
                'user_id' => $this->auth->user()->id,
                'contact_user_id' => sanitize($_POST['contact_id'] ?? null)
            ];

            $validator = Validator::make($data);
            $isValid = $validator->validate([
                'client_id' => 'required',
                'log_type' => 'required',
                'description' => 'required',
            ]);

            if ($isValid) {
                if ($this->clientLogModel->create($data)) {
                    FlashMessage::set('message', 'لاگ مشتری با موفقیت ثبت شد.');
                } else {
                    FlashMessage::set('message', 'خطا در ثبت لاگ مشتری.', 'error');
                }
            } else {
                FlashMessage::set('message', implode('<br>', array_values($validator->errors())), 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=clients_logs&id=' . $data['client_id']);
        }
    }

    private function uploadFile($file, $uploadDir) {
        $targetDir = "public/uploads/{$uploadDir}/";
        if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }
        $fileName = basename($file["name"]);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $fileType;
        $targetFile = $targetDir . $newFileName;
        $uploadOk = 1;

        if ($file["size"] > 500000) { // 500KB
            return ['success' => false, 'message' => 'حجم فایل بسیار بزرگ است.'];
        }
        if($fileType != "jpg" && $fileType != "png" && $fileType != "jpeg" && $fileType != "gif" ) {
            return ['success' => false, 'message' => 'فقط فرمت های JPG, JPEG, PNG و GIF مجاز هستند.'];
        }
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return ['success' => true, 'path' => $newFileName];
        } else {
            return ['success' => false, 'message' => 'خطا در آپلود فایل.'];
        }
    }

    private function convertJalaliToGregorianString($jalaliDate) {
        if (empty($jalaliDate)) {
            return null;
        }

        $englishDate = $this->convertPersianNumbersToEnglish($jalaliDate);

        if (strpos($englishDate, '/') === false) {
            return null;
        }
        
        list($j_year, $j_month, $j_day) = explode('/', $englishDate);
        
        $gregorian_array = jalali_to_gregorian((int)$j_year, (int)$j_month, (int)$j_day);
        
        return implode('-', $gregorian_array);
    }
    
    private function convertPersianNumbersToEnglish($string) {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($persian, $english, $string);
    }

    public function contracts() {
        $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
        
        $filters = [
            'client_id' => sanitize($_GET['client_id'] ?? null),
            'status' => sanitize($_GET['status'] ?? null),
            'start_date' => $this->convertJalaliToGregorianString($_GET['start_date'] ?? null),
            'end_date' => $this->convertJalaliToGregorianString($_GET['end_date'] ?? null),
            'search' => sanitize($_GET['search'] ?? null),
        ];
        
        $contracts = $this->contractModel->getFilteredContracts($filters);
        $clients = $this->clientModel->getAllClients();
        $clientNames = [];
        foreach($clients as $client) {
            $clientNames[$client->id] = $client->name;
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode($contracts);
            return;
        }

        view('admin/contracts/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت قراردادها',
            'contracts' => $contracts,
            'clientNames' => $clientNames,
            'clients' => $clients,
            'filters' => $filters,
            'serviceTypes' => $this->getServiceTypes()
        ]);
    }

    private function getServiceTypes() {
        return [
            'wordpress_website_design' => 'طراحی سایت وردپرسی',
            'dedicated_website_design' => 'طراحی سایت اختصاصی',
            'branding_marketing' => 'برندینگ و بازاریابی',
            'social_media_management' => 'مدیریت شبکه های اجتماعی',
            'seo_services' => 'خدمات سئو',
            'content_production' => 'خدمات تولید محتوا',
            'server_renewal' => 'تمدید سرور',
            'support_renewal' => 'تمدید پشتیبانی',
        ];
    }

    public function createContract() {
        $this->auth->restrict(['admin', 'accountant']);
        $clients = $this->clientModel->getAllClients();
        view('admin/contracts/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'Create Contract',
            'contract' => null,
            'clients' => $clients,
            'serviceTypes' => $this->getServiceTypes()
        ]);
    }
    public function storeContract() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contractData = [
                'client_id' => sanitize($_POST['client_id']),
                'title' => sanitize($_POST['title']),
                'service_type' => sanitize($_POST['service_type']),
                'description' => sanitize($_POST['description']),
                'total_amount' => sanitize($_POST['total_amount']),
                'prepayment_amount' => sanitize($_POST['prepayment_amount'] ?? 0),
                'start_date' => $this->convertJalaliToGregorianString($_POST['start_date'] ?? null),
                'end_date' => $this->convertJalaliToGregorianString($_POST['end_date'] ?? null),
                'final_payment_due_date' => $this->convertJalaliToGregorianString($_POST['final_payment_due_date'] ?? null),
                'renewal_type' => sanitize($_POST['renewal_type']),
                'next_renewal_date' => $this->convertJalaliToGregorianString($_POST['next_renewal_date'] ?? null),
                'status' => sanitize($_POST['status']),
            ];

            $contractId = $this->contractModel->create($contractData);

            if ($contractId) {
                
        $client = $this->clientModel->findById($contractData['client_id']);
        if ($client && !empty($client->phone)) {
            $message = "مشتری گرامی، قرارداد شما با عنوان '{$contractData['title']}' با موفقیت در سیستم ثبت گردید.\nرایان تکرو";
            $this->smsService->sendDirectSms($client->phone, $message);
        }
                $totalAmount = (float)$contractData['total_amount'];
                $prepaymentAmount = (float)$contractData['prepayment_amount'];

                if ($prepaymentAmount > 0) {
                    $prepaymentInvoiceData = [
                        'invoice_number' => $this->generateNextInvoiceNumber($this->invoiceModel->getLastInvoiceNumber()),
                        'client_id' => $contractData['client_id'],
                        'contract_id' => $contractId,
                        'issue_date' => $contractData['start_date'],
                        'due_date' => $contractData['start_date'],
                        'subtotal' => $prepaymentAmount,
                        'vat_rate' => 0,
                        'description' => 'پیش پرداخت قرارداد: ' . $contractData['title'],
                        'status' => 'pending',
                    ];
                    $this->createInvoiceAndJournal($prepaymentInvoiceData);
                }

                $remainingAmount = $totalAmount - $prepaymentAmount;
                if ($remainingAmount > 0) {
                    $finalInvoiceData = [
                        'invoice_number' => $this->generateNextInvoiceNumber($this->invoiceModel->getLastInvoiceNumber(true)),
                        'client_id' => $contractData['client_id'],
                        'contract_id' => $contractId,
                        'issue_date' => $contractData['start_date'],
                        'due_date' => $contractData['final_payment_due_date'],
                        'subtotal' => $remainingAmount,
                        'vat_rate' => 9,
                        'description' => 'تسویه نهایی قرارداد: ' . $contractData['title'],
                        'status' => 'pending',
                    ];
                    $this->createInvoiceAndJournal($finalInvoiceData);
                }

                FlashMessage::set('message', 'قرارداد و فاکتورهای مربوطه با موفقیت ایجاد شدند.');
                redirect(APP_URL . '/index.php?page=admin&action=contracts');
                
            } else {
                FlashMessage::set('message', 'خطا در ایجاد قرارداد.', 'error');
                redirect_back();
            }
        }
    }
    

    public function editContract($id) {
        $this->auth->restrict(['admin', 'accountant']);
        $contract = $this->contractModel->findById($id);
        $clients = $this->clientModel->getAllClients();

        if (!$contract) {
            FlashMessage::set('message', 'قرارداد مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=contracts');
        }

        $start_date_jalali = ($contract->start_date) ? jdate('Y/m/d', strtotime($contract->start_date)) : '';
        $end_date_jalali = ($contract->end_date) ? jdate('Y/m/d', strtotime($contract->end_date)) : '';
        $next_renewal_date_jalali = ($contract->next_renewal_date) ? jdate('Y/m/d', strtotime($contract->next_renewal_date)) : '';

        view('admin/contracts/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'Edit Contract',
            'contract' => $contract,
            'clients' => $this->clientModel->getAllClients(),
            'serviceTypes' => $this->getServiceTypes(),
            'start_date_jalali' => $start_date_jalali,
            'end_date_jalali' => $end_date_jalali,
            'next_renewal_date_jalali' => $next_renewal_date_jalali
        ]);
    }

    public function updateContract($id) {
        $this->auth->restrict(['admin', 'accountant']); 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $data = [
                'client_id' => sanitize($_POST['client_id']),
                'title' => sanitize($_POST['title']),
                'service_type' => sanitize($_POST['service_type']),
                'description' => sanitize($_POST['description']),
                'total_amount' => sanitize($_POST['total_amount']),
                'renewal_type' => sanitize($_POST['renewal_type']),
                'status' => sanitize($_POST['status']),

                'start_date' => $this->convertJalaliToGregorianString($_POST['start_date'] ?? null),
                'end_date' => $this->convertJalaliToGregorianString($_POST['end_date'] ?? null),
                'next_renewal_date' => $this->convertJalaliToGregorianString($_POST['next_renewal_date'] ?? null),
            ];
            
            $validator = Validator::make($data);
            $isValid = $validator->validate([ /* ... validation rules ... */ ]);

            if ($isValid) {
                if ($this->contractModel->update($id, $data)) {
                    FlashMessage::set('message', 'قرارداد با موفقیت به‌روزرسانی شد.');
                    redirect(APP_URL . '/index.php?page=admin&action=contracts');
                } else {
                    FlashMessage::set('message', 'خطا در به‌روزرسانی قرارداد.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=contracts_edit&id=' . $id);
                }
            } else {
                FlashMessage::set('message', implode('<br>', array_merge(...array_values($validator->errors()))), 'error');
                redirect(APP_URL . '/index.php?page=admin&action=contracts_edit&id=' . $id);
            }
        }
    }

    public function deleteContract($id) {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->contractModel->delete($id)) {
                FlashMessage::set('message', 'قرارداد با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف قرارداد.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=contracts');
    }
    
    public function viewContract($id) {
        $contract = $this->contractModel->findById($id);
        if (!$contract) {
            FlashMessage::set('message', 'قرارداد مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=contracts');
        }
        $client = $this->clientModel->findById($contract->client_id); 

        view('admin/contracts/view_contract', [
            'layout' => 'guest_layout',
            'title' => 'جزئیات قرارداد',
            'contract' => $contract,
            'customer' => $client, 
            'serviceTypes' => $this->getServiceTypes(),
            'companyInfo' => [
                'name' => 'شرکت رایان تکرو',
                'address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه 3',
                'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
                'email' => 'info@rayantakro.com',
                'logo_path' => APP_URL . '/assets/img/company_logo.png',
                'signature_path' => APP_URL . '/assets/img/company_signature.png',
                'seal_path' => APP_URL . '/assets/img/company_seal.png',
            ]
        ]);
    }

    
public function invoices() {
    $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
    
    // دریافت فیلترها از درخواست
    $filters = [
        'client_id' => sanitize($_GET['client_id'] ?? null),
        'status' => sanitize($_GET['status'] ?? null),
        'start_date' => $this->convertJalaliToGregorianString($_GET['start_date'] ?? null),
        'end_date' => $this->convertJalaliToGregorianString($_GET['end_date'] ?? null),
        'search' => sanitize($_GET['search'] ?? null),
        'invoice_type' => sanitize($_GET['invoice_type'] ?? null),
        'official_type' => (isset($_GET['official_type']) && $_GET['official_type'] !== '') ? (int)$_GET['official_type'] : null,
    ];
    
    // ✅ استفاده از متد getAllCombinedInvoices برای دریافت داده‌های کامل
    $invoices = $this->invoiceModel->getAllCombinedInvoices(); 

    // فیلتر کردن داده‌ها بر اساس پارامترهای GET
    $filteredInvoices = array_filter($invoices, function($invoice) use ($filters) {
        $clientMatch = empty($filters['client_id']) || $invoice->client_id == $filters['client_id'];
        $statusMatch = empty($filters['status']) || $invoice->status == $filters['status'];
        $typeMatch = empty($filters['invoice_type']) || $invoice->type == $filters['invoice_type'];
        $officialMatch = !isset($filters['official_type']) || 
                         (isset($invoice->is_official) ? ((int)$invoice->is_official === $filters['official_type']) : ($filters['official_type'] === 1));
        
        $searchMatch = empty($filters['search']) || 
                       (isset($invoice->invoice_number) && strpos($invoice->invoice_number, $filters['search']) !== false) || 
                       (isset($invoice->client_name) && strpos($invoice->client_name, $filters['search']) !== false);

        // تبدیل تاریخ‌ها برای فیلتر
        $issueDateTimestamp = strtotime($invoice->date);
        $startMatch = empty($filters['start_date']) || $issueDateTimestamp >= strtotime($filters['start_date']);
        $endMatch = empty($filters['end_date']) || $issueDateTimestamp <= strtotime($filters['end_date']);

        return $clientMatch && $statusMatch && $typeMatch && $officialMatch && $searchMatch && $startMatch && $endMatch;
    });

    // حالا $filteredInvoices شامل داده‌های فیلترشده است و view می‌تواند از آن استفاده کند.
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(array_values($filteredInvoices));
        return;
    }
    
    $clients = $this->clientModel->getAllClients();

    view('admin/invoices/index', [
        'layout' => 'admin_layout',
        'title' => 'مدیریت فاکتورها',
        'invoices' => $filteredInvoices, // ✅ ارسال داده‌های فیلترشده
        'clients' => $clients,
        'filters' => $filters,
        'invoiceTypes' => ['' => 'همه', 'product' => 'فاکتور محصول', 'contract' => 'فاکتور قرارداد', 'service' => 'فاکتور خدمات']
    ]);
}


    public function createInvoice() {
    $this->auth->restrict(['admin', 'accountant']);
    $clients = $this->clientModel->getAllClients();
    $contracts = $this->contractModel->getAllContracts();
    $services = $this->serviceModel->getAll();
    $products = $this->productModel->getAll();
    $lastInvoiceNumber = $this->invoiceModel->getLastInvoiceNumber();
    $newInvoiceNumber = $this->generateNextInvoiceNumber($lastInvoiceNumber);

    view('admin/invoices/create_edit', [
        'layout' => 'admin_layout',
        'title' => 'صدور فاکتور جدید',
        'invoice' => null,
        'clients' => $clients,
        'contracts' => $contracts,
        'services' => $services,
        'products' => $products,
        'newInvoiceNumber' => $newInvoiceNumber
    ]);
}

   public function editInvoice($id) {
    $this->auth->restrict(['admin', 'accountant']);
    $invoice = $this->invoiceModel->findById($id);
    if (!$invoice) {
        FlashMessage::set('message', 'فاکتور مورد نظر یافت نشد.', 'error');
        redirect(APP_URL . '/index.php?page=admin&action=invoices');
        return;
    }

    // ✅ دریافت اطلاعات مورد نیاز از مدل‌ها
    $clients = $this->clientModel->getAllClients();
    $contracts = $this->contractModel->getContractsByClientId($invoice->client_id);
    $products = $this->productModel->getAll();
    $services = $this->serviceModel->getAll();
    $invoiceItems = $this->invoiceModel->getItemsByInvoiceId($id);

    // تبدیل تاریخ میلادی به شمسی
    $issue_date_jalali = !empty($invoice->issue_date) ? jdate('Y/m/d', strtotime($invoice->issue_date)) : '';
    $due_date_jalali = !empty($invoice->due_date) ? jdate('Y/m/d', strtotime($invoice->due_date)) : '';

    view('admin/invoices/create_edit', [
        'layout' => 'admin_layout',
        'title' => 'ویرایش فاکتور',
        'invoice' => $invoice,
        'clients' => $clients,
        'contracts' => $contracts,
        'products' => $products, // ✅ اکنون تعریف شده است
        'services' => $services, // ✅ اکنون تعریف شده است
        'invoiceItems' => $invoiceItems, // ✅ اکنون تعریف شده است
        'issue_date_jalali' => $issue_date_jalali,
        'due_date_jalali' => $due_date_jalali
    ]);
}
    
    public function updateInvoice($id) {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $subtotal = (float)sanitize($_POST['subtotal'] ?? 0);
            $vatRate = (float)sanitize($_POST['vat_rate'] ?? 9);
            $vatAmount = ($subtotal * $vatRate) / 100;
            $totalAmount = $subtotal + $vatAmount;

            $data = [
                'invoice_number' => sanitize($_POST['invoice_number'] ?? ''),
                'client_id' => sanitize($_POST['client_id'] ?? ''),
                'contract_id' => !empty($_POST['contract_id']) ? (int)sanitize($_POST['contract_id']) : null,
                'issue_date' => $this->convertJalaliToGregorianString($_POST['issue_date']),
                'due_date' => $this->convertJalaliToGregorianString($_POST['due_date']),
                'subtotal' => $subtotal,
                'vat_amount' => $vatAmount,
                'total_amount' => $totalAmount,
                'description' => sanitize($_POST['description'] ?? ''),
                'status' => sanitize($_POST['status'] ?? 'pending'),
            ];

            if ($this->invoiceModel->update($id, $data)) {
                FlashMessage::set('message', 'فاکتور با موفقیت به‌روزرسانی شد.');
                redirect(APP_URL . '/index.php?page=admin&action=invoices');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی فاکتور.', 'error');
                redirect(APP_URL . '/index.php?page=admin&action=invoices_edit&id=' . $id);
            }
        }
    }
    
    public function deleteInvoice($id) {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->invoiceModel->delete($id)) { /* ... */ }
        }
        redirect(APP_URL . '/index.php?page=admin&action=invoices');
    }
    
   public function viewInvoice($id) {
        $invoice = $this->invoiceModel->findByIdWithClient($id);
        if (!$invoice) {
            FlashMessage::set('message', 'فاکتور مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=invoices');
            return;
        }
        
        $payments = $this->paymentModel->getAllPaymentsByInvoiceId($id);
        
        // ✅ واکشی آیتم‌های فاکتور از طریق مدل InvoiceItem
        $items = $this->invoiceItemModel->getItemsByInvoiceId($id);

        view('admin/invoices/view_invoice', [
            'layout' => 'guest_layout',
            'title' => 'جزئیات فاکتور',
            'invoice' => $invoice,
            'payments' => $payments,
            'items' => $items, // ✅ ارسال آیتم‌های فاکتور به ویو
            'companyInfo' => [
                'name' => 'شرکت رایان تکرو',
                'address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه 3',
                'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
                'email' => 'info@rayantakro.com',
                'logo_path' => APP_URL . '/assets/img/company_logo.png',
                'signature_path' => APP_URL . '/assets/img/company_signature.png',
                'seal_path' => APP_URL . '/assets/img/company_seal.png',
            ]
        ]);
    }
  
    public function addPaymentToInvoice() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'invoice_id' => sanitize($_POST['invoice_id'] ?? ''),
                'amount' => sanitize($_POST['amount'] ?? ''),
                'payment_date' => !empty($_POST['payment_date']) ? $this->convertJalaliToGregorianString($_POST['payment_date']) : date('Y-m-d'),
            ];

            if ($this->paymentModel->create($data)) {
                $this->invoiceModel->updatePaymentStatus($data['invoice_id'], $data['amount']);
                
                $bankAccount = $this->accountModel->findByCode('BK-1101');
                $accountsReceivable = $this->accountModel->findByCode('AR-1103');

                if (!$bankAccount || !$accountsReceivable) {
                    FlashMessage::set('message', 'پرداخت ثبت شد اما سند حسابداری به دلیل عدم تعریف حساب‌های پیش‌فرض (بانک/دریافتنی) صادر نشد.', 'warning');
                    redirect(APP_URL . '/index.php?page=admin&action=view_invoice&id=' . $data['invoice_id']);
                    return;
                }

                $invoice = $this->invoiceModel->findById($data['invoice_id']);
                $voucherData = [
                    'voucher_date' => $data['payment_date'],
                    'description' => 'بابت دریافت وجه فاکتور شماره ' . ($invoice->invoice_number ?? $data['invoice_id']),
                    'user_id' => $this->auth->user()->id,
                ];
                $voucherId = $this->journalVoucherModel->create($voucherData);

                if ($voucherId) {
                    $this->journalEntryModel->create($voucherId, ['account_id' => $bankAccount->id, 'debit' => $data['amount'], 'credit' => 0]);
                    $this->journalEntryModel->create($voucherId, ['account_id' => $accountsReceivable->id, 'debit' => 0, 'credit' => $data['amount']]);
                }
                
                FlashMessage::set('message', 'پرداخت با موفقیت ثبت و سند آن صادر شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت پرداخت.', 'error');
            }
            
            redirect(APP_URL . '/index.php?page=admin&action=view_invoice&id=' . $data['invoice_id']);
        }
    }
    private function generateNextInvoiceNumber($lastInvoiceNumber) {
        if (empty($lastInvoiceNumber)) {
            return 'INV-000001';
        }
        $prefix = 'INV-';
        if (strpos($lastInvoiceNumber, $prefix) === 0) {
            $numberPart = substr($lastInvoiceNumber, strlen($prefix));
            if (is_numeric($numberPart)) {
                $nextNumber = intval($numberPart) + 1;
                return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            }
        }
        return 'INV-000001';
    }

    public function projects() {
    $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
    
    // دریافت فیلترها از درخواست
    $filters = [
        'search' => sanitize($_GET['search'] ?? null),
        'category_id' => sanitize($_GET['category_id'] ?? null),
        'status' => sanitize($_GET['status'] ?? null),
    ];
    
    // فراخوانی متدهای مدل برای دریافت اطلاعات مورد نیاز
    $projects = $this->projectModel->getFilteredProjects($filters); // ✅ متد فیلتر شده جدید
    $categories = $this->categoryModel->getAll();
    $projectSummary = $this->projectModel->getProjectStatusSummary(); 

    // بررسی نوع درخواست برای پاسخ AJAX
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($projects);
        return;
    }

    // ارسال داده‌ها به ویو
    view('admin/projects/index', [
        'layout' => 'admin_layout',
        'title' => 'مدیریت پروژه‌ها',
        'projects' => $projects,
        'categories' => $categories,
        'projectSummary' => $projectSummary,
        'filters' => $filters
    ]);
}
    public function createProject() {
        $clients = $this->clientModel->getAllClients();
        $contracts = $this->contractModel->getAllContracts();

        view('admin/projects/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'ایجاد پروژه جدید',
            'project' => null,
            'clients' => $clients,
            'contracts' => $contracts,
            'start_date_jalali' => '',
            'end_date_jalali' => ''
        ]);
    }

    public function storeProject() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $startDateJalali = !empty($_POST['start_date']) ? sanitize($_POST['start_date']) : null;
            $dueDateJalali = !empty($_POST['due_date']) ? sanitize($_POST['due_date']) : null;

            $data = [
                'name' => sanitize($_POST['name']),
                'description' => sanitize($_POST['description']),
                'client_id' => !empty($_POST['client_id']) ? sanitize($_POST['client_id']) : null,
                
                'start_date' => $startDateJalali ? $this->convertJalaliToGregorianString($startDateJalali) : null,
                'due_date' => $dueDateJalali ? $this->convertJalaliToGregorianString($dueDateJalali) : null,
                
                'contract_id' => !empty($_POST['contract_id']) ? sanitize($_POST['contract_id']) : null,
                'budget' => !empty($_POST['budget']) ? sanitize($_POST['budget']) : 0,
                
                'status' => sanitize($_POST['status']),
            ];

            if ($this->projectModel->create($data)) {
                FlashMessage::set('message', 'پروژه با موفقیت ایجاد شد.');
                redirect(APP_URL . '/index.php?page=admin&action=projects');
            } else {
                FlashMessage::set('message', 'خطا در ایجاد پروژه.', 'error');
                redirect_back();
            }
        }
    }
    
    public function viewProject($id) {
    $this->auth->restrict(['admin']);
    $project = $this->projectModel->findById($id);
    if (!$project) {
        FlashMessage::set('message', 'پروژه مورد نظر یافت نشد.', 'error');
        redirect(APP_URL . '/index.php?page=admin&action=projects');
        return;
    }

    $members = $this->projectModel->getProjectMembers($id);
    $tasks = $this->taskModel->getTasksByProjectId($id);
    $employees = $this->userModel->getAllEmployees();
    
    // دریافت اطلاعیه‌های مربوط به این پروژه
    $announcements = $this->announcementModel->getAnnouncementsForProject($id);

    $total_tasks = count($tasks);
    $completed_tasks = 0;
    
    foreach ($tasks as $task) {
        $task->checklist_items = $this->taskModel->getChecklistItemsByTaskId($task->id);
        if ($task->status === 'done') {
            $completed_tasks++;
        }
    }

    $progress = ($total_tasks > 0) ? round(($completed_tasks / $total_tasks) * 100) : 0;
    
    // تبدیل تاریخ‌ها به شمسی
    if ($project->start_date) {
        $project->start_date_jalali = jdate('Y/m/d', strtotime($project->start_date));
    }
    if ($project->due_date) {
        $project->due_date_jalali = jdate('Y/m/d', strtotime($project->due_date));
    }

    view('admin/projects/view', [
        'layout' => 'admin_layout',
        'title' => 'مشاهده پروژه: ' . sanitize($project->name),
        'project' => $project,
        'members' => $members,
        'tasks' => $tasks,
        'all_users' => $employees,
        'progress' => $progress,
        'announcements' => $announcements // ارسال اطلاعیه‌ها به ویو
    ]);
}

    public function createTask($projectId) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'project_id' => $projectId,
            'title' => sanitize($_POST['title']),
            'description' => sanitize($_POST['description'] ?? ''),
            'assigned_to_user_id' => !empty($_POST['assigned_to_user_id']) ? sanitize($_POST['assigned_to_user_id']) : null,
            // Corrected code to handle an empty due_date field properly
            'due_date' => !empty($_POST['due_date']) ? $this->convertJalaliToGregorianString($_POST['due_date']) : null,
            
            // New fields
            'due_in_days' => !empty($_POST['due_in_days']) ? sanitize($_POST['due_in_days']) : null,
            'due_in_hours' => !empty($_POST['due_in_hours']) ? sanitize($_POST['due_in_hours']) : null,
            'notes' => sanitize($_POST['notes'] ?? ''),
            
            'priority' => sanitize($_POST['priority'] ?? 'medium'),
            'status' => 'todo',
        ];

        if ($this->taskModel->createTask($data)) {
            FlashMessage::set('message', 'وظیفه جدید با موفقیت ایجاد شد.');
        } else {
            FlashMessage::set('message', 'خطا در ایجاد وظیفه.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=view_project&id=' . $projectId);
    }
}


    public function editProject($id) {
        $project = $this->projectModel->findById($id);
        if (!$project) {
            FlashMessage::set('message', 'پروژه مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=projects');
            return;
        }

        $clients = $this->clientModel->getAllClients();
        $contracts = $this->contractModel->getAllContracts();
        $start_date_jalali = !empty($project->start_date) ? jdate('Y/m/d', strtotime($project->start_date)) : '';
        $end_date_jalali = !empty($project->due_date) ? jdate('Y/m/d', strtotime($project->due_date)) : '';

        view('admin/projects/create_edit', [
            'layout' => 'admin_layout',
            'title' => 'ویرایش پروژه',
            'project' => $project,
            'clients' => $clients,
            'contracts' => $contracts,
            'start_date_jalali' => $start_date_jalali,
            'end_date_jalali' => $end_date_jalali,
        ]);
    }

    public function updateProject($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $startDateJalali = !empty($_POST['start_date']) ? sanitize($_POST['start_date']) : null;
            $dueDateJalali = !empty($_POST['due_date']) ? sanitize($_POST['due_date']) : null;

            $data = [
                'name' => sanitize($_POST['name']),
                'description' => sanitize($_POST['description']),
                'client_id' => !empty($_POST['client_id']) ? sanitize($_POST['client_id']) : null,
                
                'start_date' => $startDateJalali ? $this->convertJalaliToGregorianString($startDateJalali) : null,
                'due_date' => $dueDateJalali ? $this->convertJalaliToGregorianString($dueDateJalali) : null,
                
                'contract_id' => !empty($_POST['contract_id']) ? sanitize($_POST['contract_id']) : null,
                'budget' => !empty($_POST['budget']) ? sanitize($_POST['budget']) : 0,
                'status' => sanitize($_POST['status']),
            ];

            if ($this->projectModel->update($id, $data)) {
                FlashMessage::set('message', 'پروژه با موفقیت به‌روزرسانی شد.');
                redirect(APP_URL . '/index.php?page=admin&action=projects');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی پروژه.', 'error');
                redirect(APP_URL . '/index.php?page=admin&action=projects_edit&id=' . $id);
            }
        }
    }

    public function deleteProject($id) {
        if ($this->projectModel->delete($id)) {
            FlashMessage::set('message', 'پروژه با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف پروژه.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=projects');
    }
   
    public function sendInvoiceReminder($id) {
        $invoice = $this->invoiceModel->findByIdWithClient($id);
        
        if ($invoice && !empty($invoice->client_phone)) {
            if (empty($invoice->due_date) || $invoice->due_date == '0000-00-00') {
                FlashMessage::set('message', 'تاریخ سررسید برای این فاکتور ثبت نشده است.', 'error');
                redirect(APP_URL . '/index.php?page=admin&action=invoices');
                return;
            }

            $contractTitle = !empty($invoice->contract_title) ? $invoice->contract_title : 'خدمات کلی';
            $amount = number_format($invoice->total_amount);
            $dueDate = jdate('Y/m/d', strtotime($invoice->due_date));
            $message = "مشتری گرامی،\nیادآوری پرداخت فاکتور مربوط به «{$contractTitle}» به مبلغ {$amount} تومان در تاریخ {$dueDate}.\nبا تشکر.\nرایان تکرو";

            $isSent = $this->smsService->sendDirectSms($invoice->client_phone, $message);
            
            if ($isSent) {
                FlashMessage::set('message', 'یادآوری پرداخت برای فاکتور با موفقیت ارسال شد.');
            } else {
                FlashMessage::set('message', 'خطا در ارسال پیامک. لطفاً لاگ‌ها را بررسی کنید.', 'error');
            }

        } else {
            FlashMessage::set('message', 'اطلاعات فاکتور یا شماره تماس مشتری یافت نشد.', 'error');
        }
        
        redirect(APP_URL . '/index.php?page=admin&action=invoices');
    }

    public function sendContractReminder($id) {
        $contract = $this->contractModel->findById($id);
        
        if ($contract) {
            $client = $this->clientModel->findById($contract->client_id);

            if ($client && !empty($client->phone)) {
                if (empty($contract->next_renewal_date) || $contract->next_renewal_date == '0000-00-00') {
                    FlashMessage::set('message', 'تاریخ تمدید برای این قرارداد ثبت نشده است.', 'error');
                    redirect(APP_URL . '/index.php?page=admin&action=contracts');
                    return;
                }
                
                $amount = number_format($contract->total_amount);
                $renewalDate = jdate('Y/m/d', strtotime($contract->next_renewal_date));
                $message = "مشتری گرامی،\nیادآوری تمدید قرارداد «{$contract->title}» به مبلغ {$amount} تومان در تاریخ {$renewalDate}.\nلطفاً جهت هماهنگی تماس بگیرید.\nرایان تکرو";

                $isSent = $this->smsService->sendDirectSms($client->phone, $message);

                if ($isSent) {
                    FlashMessage::set('message', "یادآوری تمدید برای «{$contract->title}» با موفقیت ارسال شد.");
                } else {
                     FlashMessage::set('message', 'خطا در ارسال پیامک. لطفاً لاگ‌ها را بررسی کنید.', 'error');
                }

            } else {
                 FlashMessage::set('message', 'شماره تماس مشتری برای این قرارداد یافت نشد.', 'error');
            }
        } else {
            FlashMessage::set('message', 'قرارداد مورد نظر یافت نشد.', 'error');
        }

        redirect(APP_URL . '/index.php?page=admin&action=contracts');
    }

    public function reports() {
        $this->auth->restrict(['admin']);
        $invoiceSummary = $this->reportModel->getInvoiceFinancialSummary();
        $projectSummaryRaw = $this->reportModel->getProjectStatusSummary();
        $userTaskSummary = $this->reportModel->getUserTaskSummary();
        $recentClients = $this->reportModel->getRecentClients();
        
        $projectSummary = [
            'not_started' => 0,
            'in_progress' => 0,
            'finished' => 0,
            'on_hold' => 0,
            'canceled' => 0,
        ];
        if (is_array($projectSummaryRaw)) {
            foreach($projectSummaryRaw as $row) {
                if (isset($projectSummary[$row->status])) {
                    $projectSummary[$row->status] = $row->count;
                }
            }
        }

        view('admin/reports/index', [
            'layout' => 'admin_layout',
            'title' => 'داشبورد گزارشات',
            'invoiceSummary' => $invoiceSummary,
            'projectSummary' => $projectSummary,
            'userTaskSummary' => $userTaskSummary,
            'recentClients' => $recentClients
        ]);
    }

    public function tickets() {
        $this->auth->restrict(['admin']);
        $tickets = $this->ticketModel->getAllForAdmin();
        view('admin/tickets/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت تیکت‌ها',
            'tickets' => $tickets
        ]);
    }

    public function viewTicket($id) {
        $ticket = $this->ticketModel->findByIdWithDetails($id);
        if(!$ticket) {
            redirect_to_404();
            return;
        }

        $replies = $this->ticketReplyModel->getRepliesByTicketId($id);

        view('shared/tickets/view', [
            'layout' => 'admin_layout',
            'title' => 'مشاهده تیکت: ' . sanitize($ticket->subject),
            'ticket' => $ticket,
            'replies' => $replies
        ]);
    }

    public function storeTicketReply($ticketId) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['body'])) {
            $user = $this->auth->user();

            $replyId = $this->ticketReplyModel->create([
                'ticket_id' => $ticketId,
                'user_id' => $user->id,
                'body' => $_POST['body']
            ]);

            if ($replyId) {
                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                    $uploadDir = 'uploads/attachments/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $fileName = uniqid() . '-' . basename($_FILES['attachment']['name']);
                    $uploadFile = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadFile)) {
                        $this->ticketReplyModel->createAttachment($replyId, $_FILES['attachment']['name'], $uploadFile, $_FILES['attachment']['size']);
                    }
                }
                
                $this->ticketModel->updateStatus($ticketId, 'answered');
                FlashMessage::set('message', 'پاسخ شما با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت پاسخ.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=view_ticket&id=' . $ticketId);
        } else {
            FlashMessage::set('message', 'متن پاسخ نمی‌تواند خالی باشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=view_ticket&id=' . $ticketId);
        }
    }
    
    public function attendanceReport() {
        $this->auth->restrict(['admin']);
        $filters = [
            'start_date' => !empty($_POST['start_date']) ? jalali_to_gregorian($_POST['start_date']) : null,
            'end_date' => !empty($_POST['end_date']) ? jalali_to_gregorian($_POST['end_date']) : null,
        ];

        $records = $this->attendanceModel->getAllRecords($filters);
        
        view('admin/attendance/report', [
            'layout' => 'admin_layout',
            'title' => 'گزارش حضور و غیاب',
            'records' => $records,
            'filters' => $filters
        ]);
    }

    public function manageLeaveRequests() {
        $this->auth->restrict(['admin']);
        $pendingRequests = $this->leaveRequestModel->getAllPending();
        view('admin/leave/manage', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت درخواست‌های مرخصی',
            'requests' => $pendingRequests
        ]);
    }

    public function processLeaveRequest($id) {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = sanitize($_POST['status']);
            $adminNotes = sanitize($_POST['admin_notes']);
            $adminId = $this->auth->user()->id;

            if ($this->leaveRequestModel->processRequest($id, $status, $adminNotes, $adminId)) {
                FlashMessage::set('message', 'درخواست با موفقیت بررسی شد.');
            } else {
                FlashMessage::set('message', 'خطا در بررسی درخواست.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=manage_leave_requests');
        }
    }

    public function smsPanel() {
        $this->auth->restrict(['admin']);
        $allOccasions = Occasions::getAllOccasions();
        
        view('admin/sms/panel', [
            'layout' => 'admin_layout',
            'title' => 'پنل ارسال پیامک',
            'allOccasions' => $allOccasions
        ]);
    }

    public function sendCustomBulkSms() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message_body'])) {
            $messageText = sanitize($_POST['message_body']);
            $messageBodyWithUnsubscribe = $messageText . "\nلغو11";

            $clients = $this->clientModel->getAllClients();
            $phoneNumbers = [];
            foreach ($clients as $client) {
                if (!empty($client->phone)) {
                    $phoneNumbers[] = $client->phone;
                }
            }

            if (!empty($phoneNumbers)) {
                $isSent = $this->smsService->sendDirectSms($phoneNumbers, $messageBodyWithUnsubscribe);
                if ($isSent) {
                    FlashMessage::set('message', "پیامک سفارشی برای ارسال به " . count($phoneNumbers) . " مشتری در صف قرار گرفت.");
                } else {
                    FlashMessage::set('message', 'خطا در ارسال پیامک گروهی. لطفاً لاگ‌های سرور را بررسی کنید.', 'error');
                }
            } else {
                FlashMessage::set('message', 'هیچ مشتری با شماره تلفن معتبر یافت نشد.', 'warning');
            }
            redirect(APP_URL . '/index.php?page=admin&action=sms_panel');
        }
    }

    public function sendOccasionalSms() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['occasion_message'])) {
            redirect(APP_URL . '/index.php?page=admin&action=sms_panel');
            return;
        }

        $occasionMessageTemplate = $_POST['occasion_message'];
        $clients = $this->clientModel->getAllClients();
        $successCount = 0;

        foreach ($clients as $client) {
            if (!empty($client->phone)) {
                $finalMessage = str_replace('{client_name}', $client->name, $occasionMessageTemplate);
                if ($this->smsService->sendDirectSms($client->phone, $finalMessage)) {
                    $successCount++;
                }
            }
        }
        
        FlashMessage::set('message', "پیامک مناسبتی با موفقیت به {$successCount} مشتری ارسال شد.");
        redirect(APP_URL . '/index.php?page=admin&action=sms_panel');
    }


    public function sendSelectedOccasionSms() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['occasion_key'])) {
            $occasionKey = sanitize($_POST['occasion_key']);
            $allOccasions = Occasions::getAllOccasions();

            if (isset($allOccasions[$occasionKey])) {
                $occasion = $allOccasions[$occasionKey];
                FlashMessage::set('message', "پیامک مناسبتی '{$occasion['name']}' با موفقیت ارسال شد.");
            } else {
                FlashMessage::set('message', 'مناسبت انتخاب شده معتبر نیست.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=sms_panel');
        }
    }

    public function payrolls() {
        $this->auth->restrict(['admin']);
        $payrollModel = new Payroll();
        $payrolls = $payrollModel->getAllPayrolls();
        
        view('admin/payrolls/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت حقوق و دستمزد',
            'payrolls' => $payrolls
        ]);
    }

    public function createPayroll() {
        $this->auth->restrict(['admin']);
        $userModel = new User();
        $employees = $userModel->getAllEmployees();
        
        view('admin/payrolls/create', [
            'layout' => 'admin_layout',
            'title' => 'صدور فیش حقوقی جدید',
            'employees' => $employees
        ]);
    }

    public function storePayroll() {
        $this->auth->restrict(['admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payrollModel = new Payroll();
            $userId = sanitize($_POST['user_id']);
            $year = sanitize($_POST['year']);
            $month = sanitize($_POST['month']);

            // محاسبه درصد پیشرفت کارمند
            $progressReport = $this->calculateEmployeePerformance($userId, $year, $month);
            $performanceRate = $progressReport['success_rate'];
            
            // دریافت تنظیمات حقوق و دستمزد برای سال جاری
            $settings = $this->payrollSettingModel->getSettingsByYear($year);

            $earnings = $_POST['earnings'] ?? [];
            $deductions = $_POST['deductions'] ?? [];
            
            // اعمال پاداش یا جریمه بر اساس عملکرد
            $performanceAdjustment = 0;
            if ($performanceRate >= 95) {
                // پاداش
                $bonus = $settings->bonus_rate_percentage ?? 0;
                $performanceAdjustment = ($settings->base_salary_monthly * $bonus) / 100;
                $earnings[] = ['type' => 'earning', 'description' => 'پاداش عملکرد', 'amount' => $performanceAdjustment];
            } elseif ($performanceRate >= 60 && $performanceRate < 70) {
                // جریمه
                $penalty = $settings->penalty_rate_percentage ?? 0;
                $performanceAdjustment = ($settings->base_salary_monthly * $penalty) / 100;
                $deductions[] = ['type' => 'deduction', 'description' => 'جریمه عملکرد', 'amount' => $performanceAdjustment];
            } elseif ($performanceRate < 50) {
                // جریمه سنگین‌تر
                $penalty = ($settings->penalty_rate_percentage ?? 0) * 2;
                $performanceAdjustment = ($settings->base_salary_monthly * $penalty) / 100;
                $deductions[] = ['type' => 'deduction', 'description' => 'جریمه عملکرد ضعیف', 'amount' => $performanceAdjustment];
            }

            $gross_earnings = array_sum(array_column($earnings, 'amount'));
            $total_deductions = array_sum(array_column($deductions, 'amount'));
            $net_pay = $gross_earnings - $total_deductions;

            $payrollData = [
                'user_id' => $userId,
                'pay_period_year' => $year,
                'pay_period_month' => $month,
                'gross_earnings' => $gross_earnings,
                'total_deductions' => $total_deductions,
                'net_pay' => $net_pay,
                'status' => 'finalized',
                'notes' => sanitize($_POST['notes'] ?? null)
            ];
            
            $itemsData = [];
            foreach($earnings as $item) {
                if(!empty($item['description']) && !empty($item['amount'])) {
                    $itemsData[] = ['type' => 'earning', 'description' => sanitize($item['description']), 'amount' => sanitize($item['amount'])];
                }
            }
            foreach($deductions as $item) {
                 if(!empty($item['description']) && !empty($item['amount'])) {
                    $itemsData[] = ['type' => 'deduction', 'description' => sanitize($item['description']), 'amount' => sanitize($item['amount'])];
                }
            }

            $payrollId = $payrollModel->createPayroll($payrollData, $itemsData);

            if ($payrollId) {
                $tax_amount = 0; $employee_insurance_amount = 0;
                foreach ($deductions as $item) {
                    if (mb_strpos($item['description'], 'مالیات') !== false) $tax_amount = (float)$item['amount'];
                    if (mb_strpos($item['description'], 'بیمه') !== false) $employee_insurance_amount = (float)$item['amount'];
                }

                $employer_insurance_amount = ($gross_earnings > 0) ? round($gross_earnings * 0.23) : 0;
                $total_insurance_payable = $employee_insurance_amount + $employer_insurance_amount;

                $salaryExpAccount = $this->accountModel->findByCode('EX-5101');
                $insuranceExpAccount = $this->accountModel->findByCode('EX-5102');
                $taxPayableAccount = $this->accountModel->findByCode('LI-2101');
                $insurancePayableAccount = $this->accountModel->findByCode('LI-2102');
                $bankAccount = $this->accountModel->findByCode('BK-1101');

                if (!$salaryExpAccount || !$insuranceExpAccount || !$taxPayableAccount || !$insurancePayableAccount || !$bankAccount) {
                    FlashMessage::set('message', 'فیش حقوقی ثبت شد اما سند حسابداری به دلیل عدم تعریف کامل حساب‌های پیش‌فرض حقوق و دستمزد صادر نشد.', 'warning');
                    redirect(APP_URL . '/index.php?page=admin&action=payrolls');
                    return;
                }

                $employee = $this->userModel->findById($_POST['user_id']);
                $voucherDesc = "سند هزینه حقوق " . jdate_words(['mm' => $_POST['month']])['mm'] . " " . $_POST['year'] . " برای " . $employee->name;

                $voucherId = $this->journalVoucherModel->create([
                    'voucher_date' => date('Y-m-d'),
                    'description' => $voucherDesc,
                    'user_id' => $this->auth->user()->id
                ]);

                if ($voucherId) {
                    $this->journalEntryModel->create($voucherId, ['account_id' => $salaryExpAccount->id, 'debit' => $gross_earnings, 'credit' => 0, 'entity_type' => 'employee', 'entity_id' => $employee->id]);
                    $this->journalEntryModel->create($voucherId, ['account_id' => $insuranceExpAccount->id, 'debit' => $employer_insurance_amount, 'credit' => 0]);
                    
                    $this->journalEntryModel->create($voucherId, ['account_id' => $bankAccount->id, 'debit' => 0, 'credit' => $net_pay]);
                    $this->journalEntryModel->create($voucherId, ['account_id' => $taxPayableAccount->id, 'debit' => 0, 'credit' => $tax_amount]);
                    $this->journalEntryModel->create($voucherId, ['account_id' => $insurancePayableAccount->id, 'debit' => 0, 'credit' => $total_insurance_payable]);
                }

                FlashMessage::set('message', 'فیش حقوقی و سند حسابداری آن با موفقیت صادر شد.');
                redirect(APP_URL . '/index.php?page=admin&action=payrolls');

            } else {
                FlashMessage::set('message', 'خطا در صدور فیش حقوقی.', 'error');
                redirect_back();
            }
        }
    }


    public function viewPayslip($id) {
        $payrollModel = new Payroll();
        $payroll = $payrollModel->findPayrollByIdWithDetails($id);

        if (!$payroll) {
            redirect_to_404();
        }
        
        view('shared/payslip/view', [
            'layout' => 'admin_layout',
            'title' => 'مشاهده فیش حقوقی',
            'payroll' => $payroll,
            'companyInfo' => [
                'name' => 'شرکت رایان تکرو',
                'address' => 'تبریز، بلوار آزادی، جنب شهرداری منطقه 3',
                'phone' => '۰۴۱۳۴۴۰۱۱۷۹',
                'logo_path' => APP_URL . '/assets/img/company_logo.png',
            ]
        ]);
    }

    public function payrollSettings() {
        $this->auth->restrict(['admin']);
        $settingModel = new PayrollSetting();
        $settings = $settingModel->getAllSettings();
        view('admin/payrolls/settings', [
            'layout' => 'admin_layout',
            'title' => 'تنظیمات سالانه حقوق و دستمزد',
            'settings' => $settings
        ]);
    }

    public function storePayrollSettings() {
        $this->auth->restrict(['admin']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settingModel = new PayrollSetting();
            
            $data = [
                'setting_year' => sanitize($_POST['year']),
                'base_salary_monthly' => sanitize($_POST['base_salary_monthly']),
                'work_days_in_month' => sanitize($_POST['work_days_in_month']),
                'housing_allowance' => sanitize($_POST['housing_allowance']),
                'family_allowance' => sanitize($_POST['family_allowance']),
                'seniority_per_year' => sanitize($_POST['seniority_per_year']),
            ];

            if ($settingModel->createOrUpdate($data)) {
                FlashMessage::set('message', 'تنظیمات با موفقیت ذخیره شد.');
            } else {
                FlashMessage::set('message', 'خطا در ذخیره تنظیمات.', 'error');
            }
            redirect_back();
        }
    }
    public function detailedReports() {
        $filters = [
            'invoice_status' => sanitize($_POST['invoice_status'] ?? ''),
            'project_status' => sanitize($_POST['project_status'] ?? ''),
            'start_date' => !empty($_POST['start_date']) ? jalali_to_gregorian($_POST['start_date']) : null,
            'end_date' => !empty($_POST['end_date']) ? jalali_to_gregorian($_POST['end_date']) : null,
        ];

        $reportModel = new Report();
        $reports = [
            'invoices' => $reportModel->getInvoicesReport($filters),
            'projects' => $reportModel->getProjectsReport($filters),
            'employees' => $reportModel->getEmployeeActivityReport()
        ];

        view('admin/reports/detailed', [
            'layout' => 'admin_layout',
            'title' => 'گزارشات جامع',
            'filters' => $filters,
            'reports' => $reports
        ]);
    }
    public function deletePayroll($id) {
        $payrollModel = new Payroll();
        if ($payrollModel->delete($id)) {
            FlashMessage::set('message', 'فیش حقوقی با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف فیش حقوقی.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=payrolls');
    }

    public function showImportForm() {
        $this->auth->restrict(['admin', 'accountant']);
        view('admin/clients/import', [
            'layout' => 'admin_layout',
            'title' => 'ورود مشتریان از اکسل'
        ]);
    }

    public function importClients() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
            $file = $_FILES['excel_file']['tmp_name'];

            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
                $sheet = $spreadsheet->getActiveSheet();
                $highestRow = $sheet->getHighestRow();
                $importedCount = 0;

                for ($row = 1; $row <= $highestRow; $row++) { 
                    $data = [
                        'name'         => $sheet->getCell('A' . $row)->getValue(),
                        'company_name' => $sheet->getCell('B' . $row)->getValue(),
                        'email'        => $sheet->getCell('C' . $row)->getValue(),
                        'phone'        => $sheet->getCell('D' . $row)->getValue(),
                        'address'      => $sheet->getCell('E' . $row)->getValue(),
                    ];

                    if (!empty($data['email'])) {
                        if ($this->clientModel->create($data)) {
                            $importedCount++;
                        }
                    }
                }
                
                FlashMessage::set('message', "تعداد $importedCount مشتری با موفقیت از فایل اکسل وارد شد.");

            } catch (Exception $e) {
                FlashMessage::set('message', 'خطا در خواندن فایل اکسل: ' . $e->getMessage(), 'error');
            }

            redirect(APP_URL . '/index.php?page=admin&action=clients');
        }
    }

    public function exportClients() {
        $this->auth->restrict(['admin', 'accountant']);
        $clients = $this->clientModel->getAllClients();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'نام');
        $sheet->setCellValue('B1', 'نام شرکت');
        $sheet->setCellValue('C1', 'ایمیل');
        $sheet->setCellValue('D1', 'تلفن');
        $sheet->setCellValue('E1', 'آدرس');

        $rowNumber = 2;
        foreach ($clients as $client) {
            $sheet->setCellValue('A' . $rowNumber, $client->name);
            $sheet->setCellValue('B' . $rowNumber, $client->company_name);
            $sheet->setCellValue('C' . $rowNumber, $client->email);
            $sheet->setCellValue('D' . $rowNumber, $client->phone);
            $sheet->setCellValue('E' . $rowNumber, $client->address);
            $rowNumber++;
        }

        ob_clean();
        ob_end_flush();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="clients_export.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function accountingAccounts() {
    $this->auth->restrict(['admin', 'accountant']);    
    $accounts = $this->accountModel->getAllHierarchical();
    view('admin/accounting/accounts/index', [
        'layout' => 'admin_layout',
        'title' => 'کدینگ حسابداری',
        'accounts' => $accounts
    ]);
}
    public function createVoucherForm() {
    $this->auth->restrict(['admin', 'accountant']);
    $accounts = $this->accountModel->getAll();
    view('admin/accounting/vouchers/create', [
        'layout' => 'admin_layout',
        'title' => 'ثبت سند جدید',
        'accounts' => $accounts
    ]);
}

    public function storeVoucher() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $totalDebit = 0;
            $totalCredit = 0;
            if (!empty($_POST['entries'])) {
                foreach ($_POST['entries'] as $entry) {
                    $totalDebit += (float)($entry['debit'] ?? 0);
                    $totalCredit += (float)($entry['credit'] ?? 0);
                }
            }

            if (abs($totalDebit - $totalCredit) > 0.01 || $totalDebit === 0) {
                FlashMessage::set('message', 'سند تراز نیست یا مبلغ آن صفر است!', 'error');
                redirect_back();
                return;
            }

            $voucherData = [
                'voucher_date' => $this->convertJalaliToGregorianString($_POST['voucher_date']),
                'description' => sanitize($_POST['description']),
                'user_id' => $this->auth->user()->id,
            ];
            $voucherId = $this->journalVoucherModel->create($voucherData);

            if ($voucherId) {
                foreach ($_POST['entries'] as $entry) {
                    if (!empty($entry['account_id'])) {
                        $this->journalEntryModel->create($voucherId, $entry);
                    }
                }
                FlashMessage::set('message', 'سند حسابداری با موفقیت ثبت شد.');
                redirect(APP_URL . '/index.php?page=admin&action=create_voucher_form');
            } else {
                FlashMessage::set('message', 'خطا در ثبت سند.', 'error');
                redirect_back();
            }
        }
    }
    public function accountingExpenses() {
        $this->auth->restrict(['admin', 'accountant']);
        view('admin/accounting/expenses/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت هزینه‌ها',
            'expense_accounts' => $this->accountModel->getAccountsByType('expense'),
            'payment_accounts' => $this->accountModel->getAccountsByType('asset')
        ]);
    }

    public function storeExpense() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $expenseData = [
                'expense_date' => jalali_to_gregorian($_POST['expense_date']),
                'amount' => sanitize($_POST['amount']),
                'description' => sanitize($_POST['description']),
                'vendor' => sanitize($_POST['vendor'] ?? null),
                'expense_account_id' => sanitize($_POST['expense_account_id']),
                'payment_account_id' => sanitize($_POST['payment_account_id']),
            ];
            $this->expenseModel->create($expenseData);

            $voucherData = [
                'voucher_date' => $expenseData['expense_date'],
                'description' => 'ثبت هزینه: ' . $expenseData['description'],
                'user_id' => $this->auth->user()->id,
            ];
            $voucherId = $this->journalVoucherModel->create($voucherData);
            if ($voucherId) {
                $this->journalEntryModel->create($voucherId, [
                    'account_id' => $expenseData['expense_account_id'],
                    'debit' => $expenseData['amount'],
                    'credit' => 0
                ]);
                $this->journalEntryModel->create($voucherId, [
                    'account_id' => $expenseData['payment_account_id'],
                    'debit' => 0,
                    'credit' => $expenseData['amount']
                ]);
            }
            
            FlashMessage::set('message', 'هزینه با موفقیت ثبت و سند حسابداری آن صادر شد.');
            redirect(APP_URL . '/index.php?page=admin&action=accounting_expenses');
        }
    }


    public function financialReports() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'گزارشات مالی'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($startDate && $endDate) {
                $data['trialBalance'] = $this->financialReportModel->getTrialBalance($startDate, $endDate);
                
                $pnlData = $this->financialReportModel->getProfitAndLossData($startDate, $endDate);
                
                $totalIncome = 0; $totalExpenses = 0;
                $incomes = []; $expenses = [];
                foreach ($pnlData as $item) {
                    if ($item->type === 'income') {
                        $incomes[] = $item;
                        $totalIncome += $item->balance;
                    } else {
                        $item->balance = abs($item->balance);
                        $expenses[] = $item;
                        $totalExpenses += $item->balance;
                    }
                }
                $netProfit = $totalIncome - $totalExpenses;
                
                $data['profitAndLoss'] = [
                    'incomes' => $incomes, 'expenses' => $expenses,
                    'totalIncome' => $totalIncome, 'totalExpenses' => $totalExpenses,
                    'netProfit' => $netProfit
                ];

                $bsData = $this->financialReportModel->getBalanceSheetData($endDate);
                $assets = []; $liabilities = []; $equity = [];
                $totalAssets = 0; $totalLiabilities = 0; $totalEquity = 0;

                foreach ($bsData as $item) {
                    if ($item->type === 'asset') {
                        $assets[] = $item;
                        $totalAssets += $item->balance;
                    } elseif ($item->type === 'liability') {
                        $item->balance = abs($item->balance);
                        $liabilities[] = $item;
                        $totalLiabilities += $item->balance;
                    } elseif ($item->type === 'equity') {
                        $item->balance = abs($item->balance);
                        $equity[] = $item;
                        $totalEquity += $item->balance;
                    }
                }
                
                $data['balanceSheet'] = [
                    'assets' => $assets, 'liabilities' => $liabilities, 'equity' => $equity,
                    'totalAssets' => $totalAssets, 'totalLiabilities' => $totalLiabilities,
                    'totalEquity' => $totalEquity, 'netProfitForPeriod' => $netProfit,
                ];
            }

            $data['startDateJalali'] = $_POST['start_date'];
            $data['endDateJalali'] = $_POST['end_date'];
        }

        view('admin/accounting/reports/index', $data);
    }

    public function createAccountForm() {
        $this->auth->restrict(['admin', 'accountant']);
        $accounts = $this->accountModel->getAll();
        view('admin/accounting/accounts/create', [
            'layout' => 'admin_layout',
            'title' => 'افزودن حساب جدید',
            'accounts' => $accounts
        ]);
    }

    public function storeAccount() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'parent_id' => sanitize($_POST['parent_id']),
                'code' => sanitize($_POST['code']),
                'name' => sanitize($_POST['name']),
                'type' => sanitize($_POST['type']),
            ];

            if ($this->accountModel->create($data)) {
                FlashMessage::set('message', 'حساب جدید با موفقیت ایجاد شد.');
                redirect(APP_URL . '/index.php?page=admin&action=accounting_accounts');
            } else {
                FlashMessage::set('message', 'خطا در ایجاد حساب.', 'error');
                redirect_back();
            }
        }
    }

    public function accountLedger() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'دفتر کل حساب',
            'allAccounts' => $this->accountModel->getAll()
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accountId = $_POST['account_id'];
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($accountId && $startDate && $endDate) {
                $data['ledgerData'] = $this->financialReportModel->getAccountLedger($accountId, $startDate, $endDate);
                $selectedAccount = $this->accountModel->findById($accountId);
                $data['selectedAccountName'] = $selectedAccount ? $selectedAccount->name : '';
                $data['selectedAccountId'] = $accountId;
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }

        view('admin/accounting/ledger', $data);
    }

    public function generalJournal() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'دفتر روزنامه'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($startDate && $endDate) {
                $data['journalData'] = $this->financialReportModel->getGeneralJournal($startDate, $endDate);
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }

        view('admin/accounting/general_journal', $data);
    }

    public function vatReport() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'گزارش مالیات بر ارزش افزوده'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);
            $filterType = sanitize($_POST['filter_type'] ?? 'official');

            if ($startDate && $endDate) {
                $data['reportData'] = $this->financialReportModel->getVatReportData($startDate, $endDate, $filterType);
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
                $data['filterType'] = $filterType;
            }
        }

        view('admin/accounting/vat_report', $data);
    }

    public function closeFiscalYear() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            $incomeSummaryAccount = $this->accountModel->findByCode('EQ-3100');
            $retainedEarningsAccount = $this->accountModel->findByCode('EQ-3101');
            if (!$incomeSummaryAccount || !$retainedEarningsAccount) {
                FlashMessage::set('message', 'خطا: حساب‌های سیستمی (خلاصه سود و زیان / سود انباشته) تعریف نشده‌اند.', 'error');
                redirect_back();
                return;
            }

            $closingData = $this->financialReportModel->getClosingEntriesData($endDate);
            
            $voucherId = $this->journalVoucherModel->create([
                'voucher_date' => $endDate,
                'description' => 'سند اختتامیه برای سال مالی منتهی به ' . $_POST['end_date'],
                'user_id' => $this->auth->user()->id
            ]);

            if (!$voucherId) {
                FlashMessage::set('message', 'خطا در ایجاد سند اختتامیه.', 'error');
                redirect_back();
                return;
            }

            $totalIncome = 0;
            $totalExpenses = 0;

            foreach ($closingData['income_accounts'] as $account) {
                $this->journalEntryModel->create($voucherId, ['account_id' => $account->account_id, 'debit' => $account->balance, 'credit' => 0]);
                $totalIncome += $account->balance;
            }
            if ($totalIncome > 0) {
                $this->journalEntryModel->create($voucherId, ['account_id' => $incomeSummaryAccount->id, 'debit' => 0, 'credit' => $totalIncome]);
            }

            foreach ($closingData['expense_accounts'] as $account) {
                $this->journalEntryModel->create($voucherId, ['account_id' => $account->account_id, 'debit' => 0, 'credit' => $account->balance]);
                $totalExpenses += $account->balance;
            }
            if ($totalExpenses > 0) {
                $this->journalEntryModel->create($voucherId, ['account_id' => $incomeSummaryAccount->id, 'debit' => $totalExpenses, 'credit' => 0]);
            }
            
            $netProfit = $totalIncome - $totalExpenses;
            if ($netProfit > 0) {
                $this->journalEntryModel->create($voucherId, ['account_id' => $incomeSummaryAccount->id, 'debit' => $netProfit, 'credit' => 0]);
                $this->journalEntryModel->create($voucherId, ['account_id' => $retainedEarningsAccount->id, 'debit' => 0, 'credit' => $netProfit]);
            } else {
                $this->journalEntryModel->create($voucherId, ['account_id' => $incomeSummaryAccount->id, 'debit' => 0, 'credit' => abs($netProfit)]);
                $this->journalEntryModel->create($voucherId, ['account_id' => $retainedEarningsAccount->id, 'debit' => abs($netProfit), 'credit' => 0]);
            }

            FlashMessage::set('message', 'سال مالی با موفقیت بسته شد و سند اختتامیه صادر گردید.');
            redirect(APP_URL . '/index.php?page=admin&action=general_journal');

        } else {
            view('admin/accounting/closing/index', [
                'layout' => 'admin_layout',
                'title' => 'بستن سال مالی'
            ]);
        }
    }

    public function deleteVoucher($id) {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->journalVoucherModel->delete($id)) {
                FlashMessage::set('message', 'سند حسابداری با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف سند. لطفاً لاگ‌ها را بررسی کنید.', 'error');
            }
            redirect_back();
        }
    }

    public function subledgerReport() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'دفتر تفصیلی',
            'allAccounts' => $this->accountModel->getAll(),
            'allClients' => $this->clientModel->getAllClients()
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accountId = $_POST['account_id'];
            $entityId = $_POST['entity_id'];
            $entityType = 'client';
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($accountId && $entityId && $startDate && $endDate) {
                $data['ledgerData'] = $this->financialReportModel->getSubledger($accountId, $entityType, $entityId, $startDate, $endDate);
                
                $selectedAccount = $this->accountModel->findById($accountId);
                $selectedEntity = $this->clientModel->findById($entityId);

                $data['selectedAccountName'] = $selectedAccount ? $selectedAccount->name : '';
                $data['selectedEntityName'] = $selectedEntity ? $selectedEntity->name : '';
                $data['selectedAccountId'] = $accountId;
                $data['selectedEntityId'] = $entityId;
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }

        view('admin/accounting/subledger', $data);
    }

    public function reverseVoucher($id) {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->journalVoucherModel->reverse($id, $this->auth->user()->id)) {
                FlashMessage::set('message', 'سند معکوس با موفقیت صادر شد.');
            } else {
                FlashMessage::set('message', 'خطا در صدور سند معکوس.', 'error');
            }
            redirect_back();
        }
    }

    public function budgeting() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'account_id' => $_POST['account_id'],
                'period_year' => $_POST['period_year'],
                'period_month' => $_POST['period_month'],
                'budget_amount' => $_POST['budget_amount']
            ];
            if ($this->budgetModel->saveOrUpdate($data)) {
                FlashMessage::set('message', 'بودجه با موفقیت ذخیره شد.');
            } else {
                FlashMessage::set('message', 'خطا در ذخیره بودجه.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=budgeting&year=' . $data['period_year']);
        }

        $currentYear = $_GET['year'] ?? jdate('Y');
        $data = [
            'layout' => 'admin_layout',
            'title' => 'بودجه‌بندی',
            'expenseAccounts' => $this->accountModel->getAccountsByType('expense'),
            'budgets' => $this->budgetModel->getBudgetsByYear($currentYear),
            'currentYear' => $currentYear
        ];
        view('admin/accounting/budgeting', $data);
    }

    public function deleteBudget($id) {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->budgetModel->delete($id)) {
                FlashMessage::set('message', 'ردیف بودجه حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف.', 'error');
            }
            redirect_back();
        }
    }

    public function budgetReport() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'گزارش بودجه'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $year = $_POST['year'];
            $month = $_POST['month'];

            $data['reportData'] = $this->financialReportModel->getBudgetVsActualReport($year, $month);
            $data['selectedYear'] = $year;
            $data['selectedMonth'] = $month;
        }

        view('admin/accounting/budget_report', $data);
    }

    public function bankReconciliation() {
        $this->auth->restrict(['admin', 'accountant']);
        $data = [
            'layout' => 'admin_layout',
            'title' => 'مغایرت‌گیری بانکی',
            'bankAccounts' => $this->accountModel->getAccountsByType('asset')
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_reconciliation'])) {
            $accountId = $_POST['account_id'];
            $statementDate = $this->convertJalaliToGregorianString($_POST['statement_date']);
            $statementBalance = $_POST['statement_balance'];

            if ($accountId && $statementDate && isset($statementBalance)) {
                $unreconciled_tx = $this->financialReportModel->getUnreconciledTransactions($accountId, $statementDate);
                
                $data['deposits'] = array_filter($unreconciled_tx, function($tx) {
                    return $tx->debit > 0;
                });
                $data['payments'] = array_filter($unreconciled_tx, function($tx) {
                    return $tx->credit > 0;
                });
                
                $data['selectedAccountId'] = $accountId;
                $data['statementDate'] = $statementDate;
                $data['statementBalance'] = $statementBalance;
                $data['statementDateJalali'] = $_POST['statement_date'];
            }
        }
        
        view('admin/accounting/reconciliation', $data);
    }

    public function processReconciliation() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reconciliationData = [
                'account_id' => $_POST['account_id'],
                'statement_date' => $_POST['statement_date'],
                'statement_balance' => $_POST['statement_balance'],
                'user_id' => $this->auth->user()->id
            ];

            $reconciliationId = (new Reconciliation())->create($reconciliationData);

            if ($reconciliationId) {
                $entryIds = $_POST['entry_ids'] ?? [];
                (new JournalEntry())->markAsReconciled($entryIds, $reconciliationId);
                
                FlashMessage::set('message', 'مغایرت‌گیری با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت عملیات مغایرت‌گیری.', 'error');
            }

            redirect(APP_URL . '/index.php?page=admin&action=bank_reconciliation');
        }
    }
    public function manageFixedAssets() {
        $this->auth->restrict(['admin', 'accountant']);
        $data = [
            'layout' => 'admin_layout',
            'title' => 'مدیریت دارایی‌های ثابت',
            'assetAccounts' => $this->accountModel->getAccountsByType('asset'),
            'expenseAccounts' => $this->accountModel->getAccountsByType('expense'),
            'fixedAssets' => $this->fixedAssetModel->getAll(),
        ];
        view('admin/accounting/fixed_assets/index', $data);
    }

    public function storeFixedAsset() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'asset_name' => sanitize($_POST['asset_name']),
                'asset_code' => sanitize($_POST['asset_code']),
                'purchase_date' => $this->convertJalaliToGregorianString($_POST['purchase_date']),
                'purchase_cost' => sanitize($_POST['purchase_cost']),
                'salvage_value' => sanitize($_POST['salvage_value']),
                'useful_life_years' => sanitize($_POST['useful_life_years']),
                'asset_account_id' => sanitize($_POST['asset_account_id']),
                'expense_account_id' => sanitize($_POST['expense_account_id']),
                'accumulated_depreciation_account_id' => sanitize($_POST['accumulated_depreciation_account_id']),
            ];

            if ($this->fixedAssetModel->create($data)) {
                FlashMessage::set('message', 'دارایی جدید با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت دارایی.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=fixed_assets');
        }
    }

    public function deleteFixedAsset($id) {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->fixedAssetModel->delete($id)) {
                FlashMessage::set('message', 'دارایی با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف.', 'error');
            }
            redirect_back();
        }
    }

    public function showDepreciationForm() {
        $this->auth->restrict(['admin', 'accountant']);
        view('admin/accounting/fixed_assets/run_depreciation', [
            'layout' => 'admin_layout',
            'title' => 'اجرای عملیات استهلاک'
        ]);
    }

    public function runDepreciation() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect_back();

        $upToDateStr = $this->convertJalaliToGregorianString($_POST['depreciation_date']);
        $upToDate = new DateTime($upToDateStr);

        $assets = $this->fixedAssetModel->getAllForDepreciation();
        $entries = [];
        $totalDepreciation = 0;

        foreach ($assets as $asset) {
            $depreciableCost = (float)$asset->purchase_cost - (float)$asset->salvage_value;
            if ($depreciableCost <= 0 || (int)$asset->useful_life_years <= 0) continue;
            $monthlyDepreciation = $depreciableCost / ((int)$asset->useful_life_years * 12);
            
            $startDate = new DateTime($asset->last_depreciation_date ?? $asset->purchase_date);
            
            $interval = $startDate->diff($upToDate);
            $monthsToDepreciate = ($interval->y * 12) + $interval->m;

            if ($monthsToDepreciate > 0) {
                $depreciationAmount = $monthsToDepreciate * $monthlyDepreciation;
                
                $totalAccumulated = $this->financialReportModel->getAccountLedger($asset->accumulated_depreciation_account_id, '1970-01-01', $upToDateStr)['running_balance_raw'];
                if (($totalAccumulated + $depreciationAmount) > $depreciableCost) {
                    $depreciationAmount = $depreciableCost - $totalAccumulated;
                }

                if ($depreciationAmount > 0) {
                    $entries[] = ['account_id' => $asset->expense_account_id, 'debit' => $depreciationAmount, 'credit' => 0];
                    $entries[] = ['account_id' => $asset->accumulated_depreciation_account_id, 'debit' => 0, 'credit' => $depreciationAmount];
                    $totalDepreciation += $depreciationAmount;
                    $this->fixedAssetModel->updateLastDepreciationDate($asset->id, $upToDateStr);
                }
            }
        }

        if ($totalDepreciation > 0) {
            $voucherId = $this->journalVoucherModel->create([
                'voucher_date' => $upToDateStr,
                'description' => 'سند استهلاک دوره منتهی به ' . $_POST['depreciation_date'],
                'user_id' => $this->auth->user()->id
            ]);
            foreach ($entries as $entry) {
                $this->journalEntryModel->create($voucherId, $entry);
            }
            FlashMessage::set('message', 'سند استهلاک با موفقیت صادر شد.');
        } else {
            FlashMessage::set('message', 'هیچ استهلاک جدیدی برای محاسبه در این دوره وجود نداشت.', 'info');
        }
        
        redirect(APP_URL . '/index.php?page=admin&action=general_journal');
    }
    public function cashFlowReport() {
        $data = [
            'layout' => 'admin_layout',
            'title' => 'صورت جریان وجوه نقد'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($startDate && $endDate) {
                $cashFlowData = $this->financialReportModel->getCashFlowData($startDate, $endDate);
                
                $inflows = array_filter($cashFlowData['transactions'], function($tx) {
                    return $tx->debit > 0;
                });
                $outflows = array_filter($cashFlowData['transactions'], function($tx) {
                    return $tx->credit > 0;
                });
                
                $totalInflow = array_sum(array_column($inflows, 'debit'));
                $totalOutflow = array_sum(array_column($outflows, 'credit'));
                $netCashFlow = $totalInflow - $totalOutflow;

                $data['reportData'] = [
                    'opening_balance' => $cashFlowData['opening_balance'],
                    'inflows' => $inflows,
                    'outflows' => $outflows,
                    'total_inflow' => $totalInflow,
                    'total_outflow' => $totalOutflow,
                    'net_cash_flow' => $netCashFlow,
                    'ending_balance' => $cashFlowData['opening_balance'] + $netCashFlow
                ];
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }

        view('admin/accounting/cash_flow_report', $data);
    }

    
    public function expenseAnalysisReport() {
        $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);

        $data = [
            'layout' => 'admin_layout',
            'title' => 'آنالیز هزینه‌ها'
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);

            if ($startDate && $endDate) {
                $data['reportData'] = $this->financialReportModel->getExpenseAnalysis($startDate, $endDate);
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }

        view('admin/accounting/expense_analysis', $data);
    }

    public function exportInvoicesToExcel() {
        $this->auth->restrict(['admin', 'accountant']);
        $invoices = $this->invoiceModel->getAllInvoices();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'شماره فاکتور');
        $sheet->setCellValue('B1', 'مشتری');
        $sheet->setCellValue('C1', 'مبلغ کل');
        $sheet->setCellValue('D1', 'تاریخ صدور');
        $sheet->setCellValue('E1', 'وضعیت');

        $rowNumber = 2;
        foreach ($invoices as $invoice) {
            $sheet->setCellValue('A' . $rowNumber, $invoice->invoice_number);
            $sheet->setCellValue('B' . $rowNumber, $invoice->client_name);
            $sheet->setCellValue('C' . $rowNumber, $invoice->total_amount);
            $sheet->setCellValue('D' . $rowNumber, htmlspecialchars(jdate('Y/m/d', strtotime($invoice->issue_date))));
            $sheet->setCellValue('E' . $rowNumber, $invoice->status);
            $rowNumber++;
        }

        ob_clean();
        ob_end_flush();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="invoices_export.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportContractsToExcel() {
        $this->auth->restrict(['admin', 'accountant']);
        $contracts = $this->contractModel->getAllContracts();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'عنوان');
        $sheet->setCellValue('B1', 'مشتری');
        $sheet->setCellValue('C1', 'مبلغ کل');
        $sheet->setCellValue('D1', 'تاریخ شروع');
        $sheet->setCellValue('E1', 'وضعیت');

        $rowNumber = 2;
        foreach ($contracts as $contract) {
            $sheet->setCellValue('A' . $rowNumber, $contract->title);
            $sheet->setCellValue('B' . $rowNumber, $contract->client_name);
            $sheet->setCellValue('C' . $rowNumber, $contract->total_amount);
            $sheet->setCellValue('D' . $rowNumber, htmlspecialchars(jdate('Y/m/d', strtotime($contract->start_date))));
            $sheet->setCellValue('E' . $rowNumber, $contract->status);
            $rowNumber++;
        }
        
        ob_clean();
        ob_end_flush();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="contracts_export.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    public function announcementsIndex() {
        $this->auth->restrict(['admin']);
        $announcements = $this->announcementModel->getAll();
        view('admin/announcements/index', ['layout' => 'admin_layout', 'title' => 'مدیریت اطلاعیه‌ها', 'announcements' => $announcements]);
    }

    public function announcementsCreate() {
    $this->auth->restrict(['admin']);
    $projects = $this->projectModel->getAllProjects();
    view('admin/announcements/create_edit', [
        'layout' => 'admin_layout',
        'title' => 'ایجاد اطلاعیه جدید',
        'announcement' => null,
        'projects' => $projects // لیست پروژه‌ها برای نمایش در فرم
    ]);
}
    
    public function announcementsStore() {
    $this->auth->restrict(['admin']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'title' => sanitize($_POST['title']),
            'body' => $_POST['body'],
            'target_roles' => implode(',', $_POST['target_roles'] ?? ['all']),
            'project_id' => sanitize($_POST['project_id']) ?? null // دریافت شناسه پروژه از فرم
        ];
        if ($this->announcementModel->create($data)) {
            FlashMessage::set('message', 'اطلاعیه با موفقیت ایجاد شد.');
        } else {
            FlashMessage::set('message', 'خطا در ایجاد اطلاعیه.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=announcements_index');
    }
}

    public function announcementsEdit($id) {
    $this->auth->restrict(['admin']);
    $announcement = $this->announcementModel->findById($id);
    if (!$announcement) {
        FlashMessage::set('message', 'اطلاعیه مورد نظر یافت نشد.', 'error');
        redirect(APP_URL . '/index.php?page=admin&action=announcements_index');
    }
    $projects = $this->projectModel->getAllProjects();
    view('admin/announcements/create_edit', [
        'layout' => 'admin_layout',
        'title' => 'ویرایش اطلاعیه',
        'announcement' => $announcement,
        'projects' => $projects // لیست پروژه‌ها برای نمایش در فرم
    ]);
}
    
   public function announcementsUpdate($id) {
    $this->auth->restrict(['admin']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'title' => sanitize($_POST['title']),
            'body' => $_POST['body'],
            'target_roles' => implode(',', $_POST['target_roles'] ?? ['all']),
            'project_id' => sanitize($_POST['project_id']) ?? null // دریافت شناسه پروژه از فرم
        ];
        if ($this->announcementModel->update($id, $data)) {
            FlashMessage::set('message', 'اطلاعیه با موفقیت به‌روزرسانی شد.');
        } else {
            FlashMessage::set('message', 'خطا در به‌روزرسانی اطلاعیه.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=announcements_index');
    }
}

    
    public function announcementsDelete($id) {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->announcementModel->delete($id)) {
                FlashMessage::set('message', 'اطلاعیه با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف اطلاعیه.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=announcements_index');
    }
    
    public function manageTrainingCourses() {
        $this->auth->restrict(['admin']);
        $courses = $this->trainingCourseModel->getAll();
        view('admin/training/courses/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت دوره‌های آموزشی',
            'courses' => $courses
        ]);
    }
    
    public function storeTrainingCourse() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'course_title' => sanitize($_POST['course_title']),
                'description' => sanitize($_POST['description']),
                'target_audience' => implode(',', $_POST['target_audience'] ?? [])
            ];
            if ($this->trainingCourseModel->create($data)) {
                FlashMessage::set('message', 'دوره آموزشی با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت دوره آموزشی.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=manage_training_courses');
        }
    }
    
    public function trainingNeedsAnalysis() {
        $this->auth->restrict(['admin']);
        $currentYear = $_GET['year'] ?? jdate('Y');
        
        $reportData = [
            'weakness_analysis' => [],
            'course_analysis' => [],
            'group_analysis' => []
        ];
        
        $reportData['weakness_analysis'] = $this->trainingCourseModel->getAnalysisReport($currentYear)['weakness_analysis'];
        $reportData['course_analysis'] = $this->trainingCourseModel->getAnalysisReport($currentYear)['course_analysis'];
        
        $reportData['group_analysis'] = $this->trainingNeedModel->getGroupAssessmentAnalysis($currentYear);

        view('admin/training/reports/analysis', [
            'layout' => 'admin_layout',
            'title' => 'گزارش تحلیلی نیازسنجی',
            'report' => $reportData,
            'currentYear' => $currentYear
        ]);
    }
    
    public function showAssessmentForm($userId) {
        $this->auth->restrict(['admin', 'employee']);
    }
    
    public function storeAssessmentScore() {
        $this->auth->restrict(['admin', 'employee']);
    }

    public function trainingNeeds() {
        $this->auth->restrict(['admin']);
        $pendingNeeds = $this->trainingNeedModel->getAllPending();
        view('admin/training/needs/index', [
            'layout' => 'admin_layout',
            'title' => 'نیازسنجی آموزشی',
            'pendingNeeds' => $pendingNeeds
        ]);
    }
    
    public function viewTrainingNeed($id) {
        $this->auth->restrict(['admin']);
        $trainingNeed = $this->trainingNeedModel->findById($id);
        if (!$trainingNeed) {
            FlashMessage::set('message', 'نیازسنجی مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=training_needs');
        }
        $courses = $this->trainingCourseModel->getAll();
        view('admin/training/needs/view', [
            'layout' => 'admin_layout',
            'title' => 'بررسی نیازسنجی',
            'trainingNeed' => $trainingNeed,
            'courses' => $courses
        ]);
    }
    
    public function processTrainingNeed($id) {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'development_areas' => sanitize($_POST['development_areas']),
                'course_suggestions' => implode(',', $_POST['course_suggestions'] ?? []),
                'status' => sanitize($_POST['status']),
                'manager_id' => $this->auth->user()->id,
            ];
            if ($this->trainingNeedModel->update($id, $data)) {
                FlashMessage::set('message', 'نیازسنجی با موفقیت بررسی شد.');
            } else {
                FlashMessage::set('message', 'خطا در بررسی نیازسنجی.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=training_needs');
        }
    }
    public function manageSkillAssessments() {
        $this->auth->restrict(['admin']);
        $employees = $this->userModel->getAllEmployees();
        $skills = $this->getAssessmentSkills();
        view('admin/training/assessments/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت آزمون‌های مهارتی',
            'employees' => $employees,
            'skills' => $skills
        ]);
    }
    
    public function sendPeerAssessmentRequest() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $employeeId = sanitize($_POST['employee_id']);
            $peerIds = $_POST['peer_ids'] ?? [];
            $skillsToAssess = $_POST['skills_to_assess'] ?? [];
            
            FlashMessage::set('message', 'درخواست ارزیابی برای همکاران با موفقیت ارسال شد.');
            redirect(APP_URL . '/index.php?page=admin&action=manage_skill_assessments');
        }
    }

    private function getAssessmentSkills() {
        return [
            'technical_skills' => 'مهارت‌های فنی',
            'communication' => 'مهارت‌های ارتباطی',
            'problem_solving' => 'حل مسئله',
            'teamwork' => 'کار تیمی'
        ];
    }
    
    public function showApiSettings() {
        $this->auth->restrict(['admin']);
        
        $sql = "SELECT * FROM api_keys";
        $this->db->query($sql);
        $keys = $this->db->fetchAll();

        view('admin/settings/api_keys', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت کلیدهای API',
            'apiKeys' => $keys
        ]);
    }
    
    public function storeApiKey() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'provider' => sanitize($_POST['provider']),
                'api_key' => sanitize($_POST['api_key']),
                'daily_limit' => sanitize($_POST['daily_limit']),
                'status' => sanitize($_POST['status']),
                'last_reset_date' => date('Y-m-d')
            ];
            
            $sql = "INSERT INTO api_keys (provider, api_key, daily_limit, status, last_reset_date) VALUES (:provider, :api_key, :daily_limit, :status, :last_reset_date)";
            
            $this->db->query($sql);
            $this->db->bind(':provider', $data['provider']);
            $this->db->bind(':api_key', $data['api_key']);
            $this->db->bind(':daily_limit', $data['daily_limit']);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':last_reset_date', $data['last_reset_date']);
            
            if ($this->db->execute()) {
                FlashMessage::set('message', 'کلید API با موفقیت ذخیره شد.');
            } else {
                FlashMessage::set('message', 'خطا در ذخیره کلید API.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=showApiSettings');
        }
    }
    
    public function deleteApiKey($id) {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sql = "DELETE FROM api_keys WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            if ($this->db->execute()) {
                FlashMessage::set('message', 'کلید API با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف کلید API.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=showApiSettings');
    }
    
    private function searchAndExtractClients($keyword, $province, $api_provider) {
        $locations = [
            'آذربایجان شرقی' => ['lat' => 38.07, 'lng' => 46.29, 'radius' => 50000],
            'آذربایجان غربی' => ['lat' => 37.55, 'lng' => 45.07, 'radius' => 50000],
            'اردبیل' => ['lat' => 38.24, 'lng' => 48.29, 'radius' => 50000],
            'اصفهان' => ['lat' => 32.65, 'lng' => 51.67, 'radius' => 50000],
            'البرز' => ['lat' => 35.84, 'lng' => 50.99, 'radius' => 50000],
            'ایلام' => ['lat' => 33.63, 'lng' => 46.42, 'radius' => 50000],
            'بوشهر' => ['lat' => 28.92, 'lng' => 50.83, 'radius' => 50000],
            'تهران' => ['lat' => 35.6892, 'lng' => 51.3890, 'radius' => 50000],
            'چهارمحال و بختیاری' => ['lat' => 32.32, 'lng' => 50.81, 'radius' => 50000],
            'خراسان جنوبی' => ['lat' => 32.86, 'lng' => 59.21, 'radius' => 50000],
            'خراسان رضوی' => ['lat' => 36.26, 'lng' => 59.61, 'radius' => 50000],
            'خراسان شمالی' => ['lat' => 37.47, 'lng' => 57.32, 'radius' => 50000],
            'خوزستان' => ['lat' => 31.31, 'lng' => 48.67, 'radius' => 50000],
            'زنجان' => ['lat' => 36.67, 'lng' => 48.48, 'radius' => 50000],
            'سمنان' => ['lat' => 35.57, 'lng' => 53.39, 'radius' => 50000],
            'سیستان و بلوچستان' => ['lat' => 29.49, 'lng' => 60.85, 'radius' => 50000],
            'فارس' => ['lat' => 29.62, 'lng' => 52.53, 'radius' => 50000],
            'قزوین' => ['lat' => 36.26, 'lng' => 50.00, 'radius' => 50000],
            'قم' => ['lat' => 34.64, 'lng' => 50.87, 'radius' => 50000],
            'کردستان' => ['lat' => 35.31, 'lng' => 47.00, 'radius' => 50000],
            'کرمان' => ['lat' => 30.29, 'lng' => 57.06, 'radius' => 50000],
            'کرمانشاه' => ['lat' => 34.32, 'lng' => 47.07, 'radius' => 50000],
            'کهگیلویه و بویراحمد' => ['lat' => 30.66, 'lng' => 51.58, 'radius' => 50000],
            'گلستان' => ['lat' => 36.83, 'lng' => 54.43, 'radius' => 50000],
            'گیلان' => ['lat' => 37.27, 'lng' => 49.58, 'radius' => 50000],
            'لرستان' => ['lat' => 33.46, 'lng' => 48.35, 'radius' => 50000],
            'مازندران' => ['lat' => 36.56, 'lng' => 53.05, 'radius' => 50000],
            'مرکزی' => ['lat' => 34.09, 'lng' => 49.69, 'radius' => 50000],
            'هرمزگان' => ['lat' => 27.18, 'lng' => 56.28, 'radius' => 50000],
            'همدان' => ['lat' => 34.79, 'lng' => 48.51, 'radius' => 50000],
            'یزد' => ['lat' => 31.89, 'lng' => 54.36, 'radius' => 50000],
        ];

        if (!isset($locations[$province])) {
            return [];
        }

        $locationData = $locations[$province];
        $results = [];

        $activeKey = $this->getActiveApiKey($api_provider);
        if (!$activeKey) {
            FlashMessage::set('message', "هیچ کلید API فعالی برای پلتفرم {$api_provider} وجود ندارد.", 'error');
            return [];
        }
        $apiKey = $activeKey->api_key;
        $lat = $locationData['lat'];
        $lng = $locationData['lng'];


        if ($api_provider === 'google') {
            $url = "https://maps.googleapis.com/maps/api/place/textsearch/json?query=" . urlencode($keyword) . "&location=" . $lat . "," . $lng . "&radius=" . $locationData['radius'] . "&key=" . $apiKey;
            $response = @file_get_contents($url);
            $data = json_decode($response, true);
            $this->incrementApiKeyUsage($activeKey->id);

            if (isset($data['results'])) {
                foreach ($data['results'] as $place) {
                    $results[] = [
                        'name' => $place['name'] ?? null,
                        'email' => $this->getHunterIoEmail($place['website'] ?? null),
                        'phone' => $place['formatted_phone_number'] ?? null,
                        'address' => $place['formatted_address'] ?? null,
                        'company_name' => $place['name'] ?? null,
                        'user_type' => 'legal',
                        'website' => $place['website'] ?? null,
                        'source' => $api_provider
                    ];
                }
            }
        } elseif ($api_provider === 'neshan') {
            $url = "https://api.neshan.org/v1/search?term=" . urlencode($keyword) . "&lat=" . $lat . "&lng=" . $lng;

            $headers = [
                "Api-Key: " . $apiKey,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            curl_close($ch);
            
            $this->incrementApiKeyUsage($activeKey->id);
            $data = json_decode($response, true);

            if (isset($data['items'])) {
                foreach ($data['items'] as $item) {
                    $results[] = [
                        'name' => $item['title'] ?? null,
                        'email' => $this->getHunterIoEmail($item['website'] ?? null),
                        'phone' => $item['phone'] ?? null,
                        'address' => $item['address'] ?? null,
                        'company_name' => $item['title'] ?? null,
                        'user_type' => 'legal',
                        'website' => $item['website'] ?? null,
                        'source' => $api_provider
                    ];
                }
            }
        } elseif ($api_provider === 'balad') {
            $url = "https://developers.balad.tech/search/..." . urlencode($keyword) . "...&api_key=" . $apiKey;
            
            FlashMessage::set('message', "پلتفرم بلد هنوز پیاده‌سازی نشده است.");
            return [];
        }

        return $results;
    }
    
    private function getHunterIoEmail($domain) {
        if (empty($domain)) {
            return null;
        }
        $apiKey = 'YOUR_HUNTER_API_KEY';
        $url = "https://api.hunter.io/v2/domain-search?domain=" . urlencode($domain) . "&api_key=" . $apiKey;
        
        if (ini_get('allow_url_fopen')) {
            $response = @file_get_contents($url);
            if ($response === FALSE) {
                error_log("Error fetching data from Hunter.io for domain: " . $domain);
                return null;
            }
            $data = json_decode($response, true);
            
            if (isset($data['data']['emails'][0]['value'])) {
                return $data['data']['emails'][0]['value'];
            }
        } else {
            error_log("Warning: allow_url_fopen is disabled. Cannot use file_get_contents for API calls.");
        }
        return null;
    }
    private function saveLeadsToDb($leads, $provider) {
        $importedCount = 0;
        foreach ($leads as $lead) {
            if (!empty($lead['email'])) { 
                $sql = "INSERT INTO marketing_leads (name, company_name, email, phone, address, website, source) VALUES (:name, :company_name, :email, :phone, :address, :website, :source)";
                $this->db->query($sql);
                $this->db->bind(':name', $lead['name'] ?? null);
                $this->db->bind(':company_name', $lead['company_name'] ?? null);
                $this->db->bind(':email', $lead['email'] ?? null);
                $this->db->bind(':phone', $lead['phone'] ?? null);
                $this->db->bind(':address', $lead['address'] ?? null);
                $this->db->bind(':website', $lead['website'] ?? null);
                $this->db->bind(':source', $provider);

                try {
                    $this->db->execute();
                    $importedCount++;
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000') {
                        continue;
                    }
                }
            }
        }
        return $importedCount;
    }
    
   public function showMarketingForm() {
        $this->auth->restrict(['admin']);
        
        // دریافت شماره صفحات برای صفحه‌بندی (Pagination)
        $maps_page = sanitize($_GET['maps_page'] ?? 1);
        $niaz_page = sanitize($_GET['niaz_page'] ?? 1);
        $neshan_page = sanitize($_GET['neshan_page'] ?? 1);
        // --- اضافه شده برای دیوار و شیپور ---
        $divar_page = sanitize($_GET['divar_page'] ?? 1);
        $sheypoor_page = sanitize($_GET['sheypoor_page'] ?? 1);

        $records_per_page = 20;

        // لیست استان‌ها (حفظ شده طبق درخواست)
        $provinces = [
            'آذربایجان شرقی', 'آذربایجان غربی', 'اردبیل', 'اصفهان', 'البرز', 'ایلام',
            'بوشهر', 'تهران', 'چهارمحال و بختیاری', 'خراسان جنوبی', 'خراسان رضوی',
            'خراسان شمالی', 'خوزستان', 'زنجان', 'سمنان', 'سیستان و بلوچستان',
            'فارس', 'قزوین', 'قم', 'کردستان', 'کرمان', 'کرمانشاه', 'کهگیلویه و بویراحمد',
            'گلستان', 'گیلان', 'لرستان', 'مازندران', 'مرکزی', 'هرمزگان', 'همدان',
            'یزد',
        ];

        // 1. دریافت داده‌های گوگل مپس
        $maps_leads = $this->marketingLeadModel->getMapsLeadsPaginated($maps_page);
        $maps_total_records = $this->marketingLeadModel->countMapsLeads();

        // 2. دریافت داده‌های نیاز روز
        $niazerooz_leads = $this->marketingLeadModel->getNiazroozLeadsPaginated($niaz_page);
        $niazerooz_total_records = $this->marketingLeadModel->countNiazroozLeads();

        // 3. دریافت داده‌های نشان
        $neshan_leads = $this->marketingLeadModel->getNeshanLeadsPaginated($neshan_page);
        $neshan_total_records = $this->marketingLeadModel->countNeshanLeads();

        // 4. دریافت داده‌های دیوار (جدید)
        $divar_leads = $this->marketingLeadModel->getDivarLeadsPaginated($divar_page);
        $divar_total_records = $this->marketingLeadModel->countDivarLeads();

        // 5. دریافت داده‌های شیپور (جدید)
        $sheypoor_leads = $this->marketingLeadModel->getSheypoorLeadsPaginated($sheypoor_page);
        $sheypoor_total_records = $this->marketingLeadModel->countSheypoorLeads();

        // ارسال تمام داده‌ها به ویو
        view('admin/marketing/research_form', [
            'layout' => 'admin_layout',
            'title' => 'تحقیقات بازاریابی و جمع‌آوری لید',
            'provinces' => $provinces,
            
            // داده‌های گوگل مپس
            'maps_leads' => $maps_leads,
            'maps_total_records' => $maps_total_records,
            'maps_current_page' => $maps_page,
            
            // داده‌های نیاز روز
            'niazerooz_leads' => $niazerooz_leads,
            'niazerooz_total_records' => $niazerooz_total_records,
            'niazerooz_current_page' => $niaz_page,
            
            // داده‌های نشان
            'neshan_leads' => $neshan_leads,
            'neshan_total_records' => $neshan_total_records,
            'neshan_current_page' => $neshan_page,

            // داده‌های دیوار
            'divar_leads' => $divar_leads,
            'divar_total_records' => $divar_total_records,
            'divar_current_page' => $divar_page,

            // داده‌های شیپور
            'sheypoor_leads' => $sheypoor_leads,
            'sheypoor_total_records' => $sheypoor_total_records,
            'sheypoor_current_page' => $sheypoor_page,

            'records_per_page' => $records_per_page,
        ]);
    }
    
    public function deleteMapLead($id) {
        $this->auth->restrict(['admin']);
        if ($this->marketingLeadModel->deleteMapsLead($id)) {
            FlashMessage::set('message', 'لید با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف لید.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=showMarketingForm');
    }

    public function deleteNiazroozLead($id) {
        $this->auth->restrict(['admin']);
        if ($this->marketingLeadModel->deleteNiazroozLead($id)) {
            FlashMessage::set('message', 'لید با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف لید.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=showMarketingForm');
    }

    public function deleteNeshanLead($id) {
        $this->auth->restrict(['admin']);
        if ($this->marketingLeadModel->deleteNeshanLead($id)) {
            FlashMessage::set('message', 'لید با موفقیت حذف شد.');
        } else {
            FlashMessage::set('message', 'خطا در حذف لید.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=showMarketingForm');
    }

    public function products() {
        $this->auth->restrict(['admin', 'accountant']);
        $products = $this->productModel->getAll();
        view('admin/inventory/products/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت محصولات',
            'products' => $products
        ]);
    }
    
    public function storeProduct() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => sanitize($_POST['name']),
                'sku' => sanitize($_POST['sku']),
                'purchase_price' => sanitize($_POST['purchase_price']),
                'sale_price' => sanitize($_POST['sale_price']),
            ];
            
            if ($this->productModel->create($data)) {
                FlashMessage::set('message', 'محصول جدید با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت محصول.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=products');
        }
    }
    
    public function purchases() {
        $this->auth->restrict(['admin', 'accountant']);
        $purchases = $this->purchaseModel->getAllPurchases();
        view('admin/inventory/purchases/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت خریدها',
            'purchases' => $purchases
        ]);
    }
    
    public function createPurchase() {
        $this->auth->restrict(['admin', 'accountant']);
        $products = $this->productModel->getAll();
        $vendors = $this->clientModel->getAllClients();
        view('admin/inventory/purchases/create', [
            'layout' => 'admin_layout',
            'title' => 'ثبت خرید جدید',
            'products' => $products,
            'vendors' => $vendors
        ]);
    }
    
    public function storePurchase() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'vendor_id' => sanitize($_POST['vendor_id']),
                'purchase_date' => $this->convertJalaliToGregorianString($_POST['purchase_date']),
                'status' => 'pending',
                'items' => $_POST['items']
            ];
            $totalAmount = 0;
            foreach ($data['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['price'];
            }
            $data['total_amount'] = $totalAmount;
            
            $purchaseId = $this->purchaseModel->createPurchase($data);
            
            if ($purchaseId) {
                $inventoryAccount = $this->accountModel->findByCode('AS-1201');
                $accountsPayable = $this->accountModel->findByCode('LI-2104');

                if ($inventoryAccount && $accountsPayable) {
                    $voucherId = $this->journalVoucherModel->create([
                        'voucher_date' => $data['purchase_date'],
                        'description' => 'سند خرید کالا به فاکتور شماره ' . $purchaseId,
                        'user_id' => $this->auth->user()->id
                    ]);
                    if ($voucherId) {
                        $this->journalEntryModel->create($voucherId, ['account_id' => $inventoryAccount->id, 'debit' => $totalAmount, 'credit' => 0]);
                        $this->journalEntryModel->create($voucherId, ['account_id' => $accountsPayable->id, 'debit' => 0, 'credit' => $totalAmount]);
                    }
                }
                
                FlashMessage::set('message', 'فاکتور خرید با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت فاکتور خرید.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=purchases');
        }
    }
    
    public function sales() {
        $this->auth->restrict(['admin', 'accountant']);
        $sales = $this->saleModel->getAllSales();
        view('admin/inventory/sales/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت فروش‌ها',
            'sales' => $sales
        ]);
    }

    public function createSale() {
        $this->auth->restrict(['admin', 'accountant']);
        $products = $this->productModel->getAll();
        $clients = $this->clientModel->getAllClients();
        view('admin/inventory/sales/create', [
            'layout' => 'admin_layout',
            'title' => 'ثبت فروش جدید',
            'products' => $products,
            'clients' => $clients
        ]);
    }
    
    public function storeSale() {
        $this->auth->restrict(['admin', 'accountant']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'client_id' => sanitize($_POST['client_id']),
                'sale_date' => $this->convertJalaliToGregorianString($_POST['sale_date']),
                'status' => 'pending',
                'items' => $_POST['items']
            ];
            
            $totalAmount = 0;
            $cogsAmount = 0;
            foreach ($data['items'] as $item) {
                $product = $this->productModel->findById($item['product_id']);
                $totalAmount += $item['quantity'] * $item['price'];
                $cogsAmount += $item['quantity'] * $product->purchase_price;
            }
            $data['total_amount'] = $totalAmount;
            
            $saleId = $this->saleModel->createSale($data);
            
            if ($saleId) {
                $accountsReceivable = $this->accountModel->findByCode('AR-1103');
                $salesRevenue = $this->accountModel->findByCode('SR-4101');
                $cogsAccount = $this->accountModel->findByCode('EX-5103');
                $inventoryAccount = $this->accountModel->findByCode('AS-1201');
                
                if ($accountsReceivable && $salesRevenue && $cogsAccount && $inventoryAccount) {
                    $voucherId = $this->journalVoucherModel->create([
                        'voucher_date' => $data['sale_date'],
                        'description' => 'سند فروش کالا به فاکتور شماره ' . $saleId,
                        'user_id' => $this->auth->user()->id
                    ]);
                    if ($voucherId) {
                        $this->journalEntryModel->create($voucherId, ['account_id' => $accountsReceivable->id, 'debit' => $totalAmount, 'credit' => 0]);
                        $this->journalEntryModel->create($voucherId, ['account_id' => $salesRevenue->id, 'debit' => 0, 'credit' => $totalAmount]);
                        
                        $this->journalEntryModel->create($voucherId, ['account_id' => $cogsAccount->id, 'debit' => $cogsAmount, 'credit' => 0]);
                        $this->journalEntryModel->create($voucherId, ['account_id' => $inventoryAccount->id, 'debit' => 0, 'credit' => $cogsAmount]);
                    }
                }
                
                FlashMessage::set('message', 'فاکتور فروش با موفقیت ثبت شد.');
            } else {
                FlashMessage::set('message', 'خطا در ثبت فاکتور فروش.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=sales');
        }
    }
    
    public function profitAndLossReport() {
        $this->auth->restrict(['admin', 'accountant', 'accountant_viewer']);
        $data = [
            'layout' => 'admin_layout',
            'title' => 'گزارش سود و زیان'
        ];
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $startDate = $this->convertJalaliToGregorianString($_POST['start_date']);
            $endDate = $this->convertJalaliToGregorianString($_POST['end_date']);
    
            if ($startDate && $endDate) {
                $data['reportData'] = $this->financialReportModel->getFullProfitAndLossReport($startDate, $endDate);
                $data['startDateJalali'] = $_POST['start_date'];
                $data['endDateJalali'] = $_POST['end_date'];
            }
        }
    
        view('admin/accounting/profit_and_loss', $data);
    }
    public function addChecklistItem() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'task_id' => sanitize($_POST['task_id']),
            'item_text' => sanitize($_POST['item_text']),
        ];

        // از مدل Task برای افزودن آیتم استفاده می‌کنیم
        $taskId = $data['task_id'];
        if ($this->taskModel->addChecklistItem($data['task_id'], $data['item_text'])) {
            FlashMessage::set('message', 'آیتم چک‌لیست با موفقیت اضافه شد.');
        } else {
            FlashMessage::set('message', 'خطا در افزودن آیتم چک‌لیست.', 'error');
        }

        redirect(APP_URL . '/index.php?page=admin&action=view_project&id=' . $this->taskModel->getProjectIdByTaskId($taskId));
    }
}

public function addProjectMember() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $projectId = sanitize($_POST['project_id']);
        $userId = sanitize($_POST['user_id']);
        $role = sanitize($_POST['role']);

        if ($this->projectModel->addMember($projectId, $userId, $role)) {
            FlashMessage::set('message', 'عضو با موفقیت به پروژه اضافه شد.');
        } else {
            FlashMessage::set('message', 'خطا در اضافه کردن عضو.', 'error');
        }

        redirect(APP_URL . '/index.php?page=admin&action=view_project&id=' . $projectId);
    }
}
public function storeInvoice() {
    $this->auth->restrict(['admin', 'accountant']);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $invoiceType = sanitize($_POST['invoice_type'] ?? 'contract');
            $isOfficial = isset($_POST['is_official']) ? (int)$_POST['is_official'] : 1;
            
            $invoiceData = [
                'invoice_number' => sanitize($_POST['invoice_number'] ?? ''),
                'client_id' => sanitize($_POST['client_id'] ?? ''),
                'issue_date' => $this->convertJalaliToGregorianString($_POST['issue_date']),
                'due_date' => $this->convertJalaliToGregorianString($_POST['due_date']),
                'description' => sanitize($_POST['description'] ?? ''),
                'status' => sanitize($_POST['status'] ?? 'pending'),
                'invoice_type' => $invoiceType,
                'is_official' => $isOfficial,
                'economic_code' => sanitize($_POST['economic_code'] ?? ''),
                'national_id' => sanitize($_POST['national_id'] ?? ''),
                'pattern_type' => (int)($_POST['pattern_type'] ?? 1),
                'vat_rate' => $isOfficial ? (float)sanitize($_POST['vat_rate'] ?? 10) : 0,
                'subtotal' => 0,
            ];

            $result = false;
            
            switch ($invoiceType) {
                case 'contract':
                    $invoiceData['contract_id'] = !empty($_POST['contract_id']) ? sanitize($_POST['contract_id']) : null;
                    $invoiceData['subtotal'] = sanitize($_POST['contract_subtotal'] ?? 0);
                    $result = $this->createInvoiceAndJournal($invoiceData);
                    break;
                
                case 'service':
                    $serviceItems = $_POST['service_items'] ?? [];
                    $subtotal = 0;
                    $invoiceItemsData = [];
                    if (isset($serviceItems['service_id']) && is_array($serviceItems['service_id'])) {
                        foreach ($serviceItems['service_id'] as $key => $serviceId) {
                            $quantity = sanitize($serviceItems['quantity'][$key] ?? 0);
                            $price = sanitize($serviceItems['price'][$key] ?? 0);
                            $subtotal += $quantity * $price;
                            $invoiceItemsData[] = ['service_id' => $serviceId, 'quantity' => $quantity, 'price' => $price];
                        }
                    }
                    $invoiceData['subtotal'] = $subtotal;
                    $result = $this->createInvoiceAndJournal($invoiceData, $invoiceItemsData);
                    break;
                
                // ✅ Add a new case for 'mixed' invoice type
                case 'product':
                case 'mixed':
                    $productItems = $_POST['product_items'] ?? [];
                    $serviceItems = $_POST['service_items'] ?? [];
                    
                    $subtotal = 0;
                    $cogsAmount = 0;
                    $invoiceItemsData = [];
                    
                    // Process product items
                    if (isset($productItems['product_id']) && is_array($productItems['product_id'])) {
                        foreach ($productItems['product_id'] as $key => $productId) {
                            if (empty($productId)) continue;
                            $product = $this->productModel->findById($productId);
                            $quantity = sanitize($productItems['quantity'][$key] ?? 0);
                            $price = sanitize($productItems['price'][$key] ?? 0);
                            
                            $subtotal += $quantity * $price;
                            if ($product) {
                                $cogsAmount += $quantity * $product->purchase_price;
                            }
                            $invoiceItemsData[] = [
                                'product_id' => $productId,
                                'quantity' => $quantity,
                                'price' => $price,
                            ];
                        }
                    }

                    // Process service items (only for 'mixed' type)
                    if ($invoiceType === 'mixed' && isset($serviceItems['service_id']) && is_array($serviceItems['service_id'])) {
                         foreach ($serviceItems['service_id'] as $key => $serviceId) {
                            $quantity = sanitize($serviceItems['quantity'][$key] ?? 0);
                            $price = sanitize($serviceItems['price'][$key] ?? 0);
                            $subtotal += $quantity * $price;
                            $invoiceItemsData[] = ['service_id' => $serviceId, 'quantity' => $quantity, 'price' => $price];
                        }
                    }

                    if ($subtotal > 0) {
                        $invoiceData['subtotal'] = $subtotal;
                        $result = $this->createProductInvoiceAndJournal($invoiceData, $invoiceItemsData, $cogsAmount);
                    } else {
                        FlashMessage::set('message', 'مبلغ فاکتور نمی‌تواند صفر باشد.', 'error');
                        redirect_back();
                        return;
                    }
                    break;

                default:
                    FlashMessage::set('message', 'نوع فاکتور نامعتبر است.', 'error');
                    redirect_back();
                    return;
            }

            if ($result && is_array($result) && isset($result['invoiceId'])) {
                $invoiceId = $result['invoiceId'];
                // ارسال خودکار به مودیان در صورت انتخاب کاربر
                if ($isOfficial && !empty($_POST['send_to_moadian'])) {
                    require_once __DIR__ . '/../models/TaxpayerInvoice.php';
                    $moadianModel = new TaxpayerInvoice();
                    $mId = $moadianModel->createFromInvoice($invoiceId, (int)($_POST['pattern_type'] ?? 1));
                    if ($mId) {
                        $mRes = $moadianModel->transmitToTaxAuthority($mId);
                        if ($mRes['success']) {
                            $result['moadianStatus'] = 'sent';
                        }
                    }
                }
                $message = 'فاکتور (' . ($isOfficial ? 'رسمی مودیان' : 'غیر رسمی داخلی') . ') با موفقیت ایجاد و سند آن صادر شد.';
                if (!empty($result['moadianStatus'])) {
                    $message .= ' همچنین به صورت مستقیم به سامانه مودیان ارسال و تایید گردید.';
                }
                if ($result['smsStatus'] === 'success') {
                    $message .= ' پیامک اطلاع‌رسانی با موفقیت ارسال شد.';
                } elseif ($result['smsStatus'] === 'warning') {
                    $message .= ' اما در ارسال پیامک اطلاع‌رسانی مشکلی پیش آمد.';
                } elseif ($result['smsStatus'] === 'error') {
                    $message .= ' اما در ارسال پیامک اطلاع‌رسانی خطای سیستمی رخ داد. (لاگ‌ها را بررسی کنید)';
                } elseif ($result['smsStatus'] === 'info_no_phone') {
                    $message .= ' (شماره تماس مشتری یافت نشد، پیامک ارسال نشد.)';
                }
                FlashMessage::set('message', $message);
                redirect(APP_URL . '/index.php?page=admin&action=invoices');
            } else {
                FlashMessage::set('message', 'خطا در ثبت فاکتور. لطفاً دوباره تلاش کنید.', 'error');
                redirect_back();
            }
        } catch (Exception $e) {
             FlashMessage::set('message', 'خطای سیستمی: ' . $e->getMessage(), 'error');
             error_log('Invoice creation error: ' . $e->getMessage());
             redirect_back();
        }
    }
}
    private function createInvoiceAndJournal($invoiceData, $invoiceItemsData = []) {
    $accountsReceivable = $this->accountModel->findByCode('AR-1103');
    $salesRevenue = $this->accountModel->findByCode('SR-4101');

    if (!$accountsReceivable) {
        die('خطای حیاتی: حساب "حساب‌های دریافتنی" با کد AR-1103 در کدینگ حسابداری شما یافت نشد.');
    }
    if (!$salesRevenue) {
        die('خطای حیاتی: حساب "درآمد فروش" با کد SR-4101 در کدینگ حسابداری شما یافت نشد.');
    }

    $subtotal = (float)($invoiceData['subtotal'] ?? 0);
    $vatRate = (float)($invoiceData['vat_rate'] ?? 0);
    $vatAmount = ($subtotal * $vatRate) / 100;
    $totalAmount = $subtotal + $vatAmount;

    $invoiceData['vat_amount'] = $vatAmount;
    $invoiceData['total_amount'] = $totalAmount;

    $invoiceModelData = [
        'invoice_number' => $invoiceData['invoice_number'],
        'client_id' => $invoiceData['client_id'],
        'contract_id' => $invoiceData['contract_id'] ?? null,
        'invoice_type' => $invoiceData['invoice_type'] ?? 'contract',
        'is_official' => $invoiceData['is_official'] ?? 1,
        'category_id' => $invoiceData['category_id'] ?? null,
        'issue_date' => $invoiceData['issue_date'],
        'due_date' => $invoiceData['due_date'],
        'subtotal' => $invoiceData['subtotal'],
        'vat_amount' => $invoiceData['vat_amount'],
        'total_amount' => $invoiceData['total_amount'],
        'description' => $invoiceData['description'],
        'status' => $invoiceData['status'],
        'economic_code' => $invoiceData['economic_code'] ?? null,
        'national_id' => $invoiceData['national_id'] ?? null,
        'pattern_type' => $invoiceData['pattern_type'] ?? 1,
    ];

    $invoiceId = $this->invoiceModel->create($invoiceModelData);
    if (!$invoiceId) {
        return false;
    }

    if (!empty($invoiceItemsData)) {
        // ✅ اصلاح اینجا: استفاده از createItems
        $this->invoiceItemModel->createItems($invoiceId, $invoiceItemsData);
    }

    $smsSentStatus = $this->sendInvoiceSms($invoiceData, $invoiceId);

    $description = 'بابت: ' . $invoiceData['description'];
    $voucherData = [
        'voucher_date' => $invoiceData['issue_date'],
        'description' => $description,
        'user_id' => $this->auth->user()->id,
    ];
    $voucherId = $this->journalVoucherModel->create($voucherData);

    if ($voucherId) {
        $this->journalEntryModel->create($voucherId, ['account_id' => $accountsReceivable->id, 'debit' => $totalAmount, 'credit' => 0]);
        $this->journalEntryModel->create($voucherId, ['account_id' => $salesRevenue->id, 'debit' => 0, 'credit' => $subtotal]);

        if ($vatAmount > 0) {
            $vatPayable = $this->accountModel->findByCode('LI-2103');
            if ($vatPayable) {
                $this->journalEntryModel->create($voucherId, ['account_id' => $vatPayable->id, 'debit' => 0, 'credit' => $vatAmount]);
            }
        }
    } else {
        error_log("Error creating journal voucher for invoice " . $invoiceId);
    }

    return ['invoiceId' => $invoiceId, 'smsStatus' => $smsSentStatus];
}

    private function createProductInvoiceAndJournal($invoiceData, $invoiceItems, $cogsAmount) {
    $accountsReceivable = $this->accountModel->findByCode('AR-1103');
    $salesRevenue = $this->accountModel->findByCode('SR-4101');
    $cogsAccount = $this->accountModel->findByCode('EX-5103');
    $inventoryAccount = $this->accountModel->findByCode('AS-1201');

    if (!$accountsReceivable || !$salesRevenue || !$cogsAccount || !$inventoryAccount) {
        die('خطای حیاتی: یکی از حساب‌های سیستمی مورد نیاز برای فاکتور محصول یافت نشد.');
    }

    $subtotal = (float)($invoiceData['subtotal'] ?? 0);
    $vatRate = (float)($invoiceData['vat_rate'] ?? 0);
    $vatAmount = ($subtotal * $vatRate) / 100;
    $totalAmount = $subtotal + $vatAmount;

    $invoiceData['vat_amount'] = $vatAmount;
    $invoiceData['total_amount'] = $totalAmount;

    $invoiceModelData = [
        'invoice_number' => $invoiceData['invoice_number'],
        'client_id' => $invoiceData['client_id'],
        'contract_id' => $invoiceData['contract_id'] ?? null,
        'invoice_type' => $invoiceData['invoice_type'] ?? 'product',
        'category_id' => $invoiceData['category_id'] ?? null,
        'issue_date' => $invoiceData['issue_date'],
        'due_date' => $invoiceData['due_date'],
        'subtotal' => $invoiceData['subtotal'],
        'vat_amount' => $invoiceData['vat_amount'],
        'total_amount' => $invoiceData['total_amount'],
        'description' => $invoiceData['description'],
        'status' => $invoiceData['status'],
    ];

    $invoiceId = $this->invoiceModel->create($invoiceModelData);
    if (!$invoiceId) {
        return false;
    }

    if (!empty($invoiceItems)) {
        // ✅ اصلاح اینجا: استفاده از createItems
        $this->invoiceItemModel->createItems($invoiceId, $invoiceItems);
    }
    
    $smsSentStatus = $this->sendInvoiceSms($invoiceData, $invoiceId);

    $description = 'بابت: ' . $invoiceData['description'];
    $voucherData = [
        'voucher_date' => $invoiceData['issue_date'],
        'description' => $description,
        'user_id' => $this->auth->user()->id,
    ];
    $voucherId = $this->journalVoucherModel->create($voucherData);

    if ($voucherId) {
        $this->journalEntryModel->create($voucherId, ['account_id' => $accountsReceivable->id, 'debit' => $totalAmount, 'credit' => 0]);
        $this->journalEntryModel->create($voucherId, ['account_id' => $salesRevenue->id, 'debit' => 0, 'credit' => $subtotal]);

        if ($vatAmount > 0) {
            $vatPayable = $this->accountModel->findByCode('LI-2103');
            if ($vatPayable) {
                $this->journalEntryModel->create($voucherId, ['account_id' => $vatPayable->id, 'debit' => 0, 'credit' => $vatAmount]);
            }
        }

        $this->journalEntryModel->create($voucherId, ['account_id' => $cogsAccount->id, 'debit' => $cogsAmount, 'credit' => 0]);
        $this->journalEntryModel->create($voucherId, ['account_id' => $inventoryAccount->id, 'debit' => 0, 'credit' => $cogsAmount]);
    } else {
        error_log("Error creating journal voucher for invoice " . $invoiceId);
    }

    return ['invoiceId' => $invoiceId, 'smsStatus' => $smsSentStatus];
}
    
private function sendInvoiceSms($invoiceData, $invoiceId) {
        $client = $this->clientModel->findById($invoiceData['client_id']);
        $smsSentStatus = 'info_no_phone';
        
        if ($client && !empty($client->phone)) {
            $invoiceNumber = $invoiceData['invoice_number'];
            $amountFormatted = number_format($invoiceData['total_amount']);
            $paymentLink = APP_URL . '/index.php?page=client&action=invoice_view&id=' . $invoiceId;
            $message = "مشتری گرامی،\\nفاکتور جدیدی به شماره {$invoiceNumber} به مبلغ {$amountFormatted} تومان برای شما صادر گردید.\\nبرای مشاهده و پرداخت، به لینک زیر مراجعه کنید:\\n{$paymentLink}\\nبا تشکر، رایان تکرو";
            
            try {
                $isSent = $this->smsService->sendDirectSms($client->phone, $message);
                $smsSentStatus = $isSent ? 'success' : 'warning';
            } catch (Exception $e) {
                $smsSentStatus = 'error';
                error_log("SMS sending error for invoice " . $invoiceId . ": " . $e->getMessage());
            }
        }
        return $smsSentStatus;
    }
    public function clients_create() {
        $data['pageTitle'] = 'افزودن مشتری جدید';
        $data['users'] = $this->userModel->getUsersByRole('client');
        $data['client'] = null; // برای جلوگیری از خطای Undefined variable
        view('admin/clients/create_edit', $data);
    }
    public function clients_edit($id) {
        $client = $this->clientModel->findById($id);
        
        if ($client) {
            // برای مشتریان حقوقی، اطلاعات شخص مسئول را از جدول users می‌خوانیم
            $client->contact_mobile_number = null;
            if ($client->user_type == 'legal' && $client->user_id) {
                $linkedUser = $this->userModel->findById($client->user_id);
                if ($linkedUser) {
                    $client->contact_mobile_number = $linkedUser->mobile_number;
                }
            }
            
            $data = [
                'pageTitle' => 'ویرایش مشتری',
                'client' => $client,
                'users' => $this->userModel->getUsersByRole('client'),
                'contacts' => $this->clientContactModel->getContactsByClientId($id)
            ];
            
            view('admin/clients/create_edit', $data);
        } else {
            FlashMessage::set('message', 'مشتری مورد نظر یافت نشد.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=clients');
        }
    }
     public function updateClientLog() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $logId = sanitize($_POST['log_id']);
            $clientId = sanitize($_POST['client_id']);

            $data = [
                'log_type' => sanitize($_POST['log_type']),
                'description' => sanitize($_POST['description']),
                'contact_user_id' => sanitize($_POST['contact_id'] ?? null),
            ];

            // اگر فایل جدیدی آپلود شده، ابتدا فایل قبلی را حذف می‌کنیم
            if (isset($_FILES['new_attachment']) && $_FILES['new_attachment']['error'] === 0) {
                $this->clientLogModel->deleteAttachment($logId);
                
                $uploadResult = $this->uploadFile($_FILES['new_attachment'], 'client_logs');
                if ($uploadResult['success']) {
                    $this->clientLogModel->createAttachment($logId, $_FILES['new_attachment']['name'], $uploadResult['path'], $_FILES['new_attachment']['size']);
                } else {
                    FlashMessage::set('message', 'لاگ به‌روزرسانی شد اما ' . $uploadResult['message'], 'warning');
                    redirect(APP_URL . '/index.php?page=admin&action=clients_logs&id=' . $clientId);
                    return;
                }
            }

            if ($this->clientLogModel->update($logId, $data)) {
                FlashMessage::set('message', 'لاگ مشتری با موفقیت به‌روزرسانی شد.');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی لاگ مشتری.', 'error');
            }

            // ✅ تغییر مسیر به صفحه لاگ‌های همان مشتری
            redirect(APP_URL . '/index.php?page=admin&action=clients_logs&id=' . $clientId);
        }
    }
    public function deleteLogAttachment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_id'])) {
            $logId = sanitize($_POST['log_id']);
            $result = $this->clientLogModel->deleteAttachment($logId);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => $result, 'message' => $result ? 'فایل با موفقیت حذف شد.' : 'خطا در حذف فایل.']);
            exit;
        }
    }

   public function deleteClientLog() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $logId = sanitize($_POST['log_id']); // شناسه لاگ
            $clientId = sanitize($_POST['client_id']); // شناسه مشتری
            
            if ($this->clientLogModel->delete($logId)) {
                FlashMessage::set('message', 'لاگ مشتری با موفقیت حذف شد.');
            } else {
                FlashMessage::set('message', 'خطا در حذف لاگ مشتری.', 'error');
            }
            // ✅ تغییر مسیر به صفحه لاگ‌های همان مشتری
            redirect(APP_URL . '/index.php?page=admin&action=clients_logs&id=' . $clientId);
        }
    }
    
    
    /**
     * نمایش گزارش جامع عملکرد کارمند به همراه نمودار
     */
    public function employeePerformanceReport($employeeId) {
    $this->auth->restrict(['admin']);
    $employee = $this->userModel->findById($employeeId);

    if (!$employee) {
        FlashMessage::set('message', 'کارمند مورد نظر یافت نشد.', 'error');
        redirect_back();
    }

    // --- تنظیمات فیلتر زمان ---
    $currentYear = sanitize($_GET['year'] ?? jdate('Y'));
    $currentMonth = sanitize($_GET['month'] ?? jdate('n'));

    // --- محاسبه میانگین عملکرد گذشته (3 ماه قبل) ---
    $averagePerformance = $this->calculateAveragePastPerformance($employeeId, $currentYear, $currentMonth);

    // --- گزارش‌های اصلی ماه جاری ---
    $performance = $this->calculateEmployeePerformance($employeeId, $currentYear, $currentMonth);

    // --- تنظیم تاریخ‌های گرگورین برای کوئری ---
    $start_gregorian = $this->convertJalaliToGregorianString("{$currentYear}/{$currentMonth}/01");

    // ✅ منطق جدید برای محاسبه روز آخر ماه شمسی
    $jmonthDays = ($currentMonth <= 6) ? 31 : (($currentMonth < 12) ? 30 : ($this->is_leap_jalali((int)$currentYear) ? 30 : 29));
    $end_gregorian = $this->convertJalaliToGregorianString("{$currentYear}/{$currentMonth}/" . $jmonthDays);

    $attendanceRecords = $this->attendanceModel->getAllRecords([
        'user_id' => $employeeId,
        'start_date' => $start_gregorian,
        'end_date' => $end_gregorian
    ]);

    $attendanceSummary = $this->attendanceModel->getAttendanceSummary($employeeId, $start_gregorian, $end_gregorian);

    $tasks = $this->taskModel->getTasksByUserIdAndDateRange($employeeId, $start_gregorian, $end_gregorian);

    view('admin/payrolls/employee_performance_report', [
        'layout' => 'admin_layout',
        'title' => 'گزارش عملکرد: ' . $employee->name,
        'employee' => $employee,
        'performance' => $performance,
        'attendanceRecords' => $attendanceRecords,
        'attendanceSummary' => $attendanceSummary,
        'tasks' => $tasks,
        'currentYear' => $currentYear,
        'currentMonth' => $currentMonth,
        'averagePerformance' => $averagePerformance
    ]);
}
    
    private function calculateEmployeePerformance($userId, $year, $month) {
    // Explicitly cast year and month to integers
    $year = (int)$year;
    $month = (int)$month;

    // A more robust way to get the Gregorian date range for the Jalali month
    $start_gregorian_array = jalali_to_gregorian($year, $month, 1);
    $start_gregorian = implode('-', $start_gregorian_array);

    // Get the number of days in the current Jalali month
    $lastDayOfMonth = ($month <= 6) ? 31 : (($month < 12) ? 30 : ($this->is_leap_jalali($year) ? 30 : 29));
    $end_gregorian_array = jalali_to_gregorian($year, $month, $lastDayOfMonth);
    $end_gregorian = implode('-', $end_gregorian_array);

    // Retrieve all tasks within this precise Gregorian date range.
    $tasks = $this->taskModel->getTasksByUserIdAndDateRange($userId, $start_gregorian, $end_gregorian);
    
    $totalTasks = count($tasks);
    $completedTasks = 0;
    
    foreach ($tasks as $task) {
        if ($task->status === 'done') {
            $completedTasks++;
        }
    }
    
    // Calculate the success rate.
    $successRate = $totalTasks > 0 ? (int)round(($completedTasks / $totalTasks) * 100) : 0;
    
    return [
        'total_tasks' => $totalTasks,
        'completed_tasks' => $completedTasks,
        'success_rate' => $successRate,
    ];
}
    
    /**
     * محاسبه میانگین عملکرد (نرخ موفقیت در تسک‌ها) برای 3 ماه گذشته.
     */
   private function calculateAveragePastPerformance($userId, $currentYear, $currentMonth) {
    $totalRates = 0;
    $count = 0;
    $jYear = (int)$currentYear;
    $jMonth = (int)$currentMonth;

    for ($i = 1; $i <= 3; $i++) {
        $targetMonth = $jMonth - $i;
        $targetYear = $jYear;

        // Correctly handle month and year wrap-around
        while ($targetMonth <= 0) {
            $targetMonth += 12;
            $targetYear--;
        }

        $report = $this->calculateEmployeePerformance($userId, $targetYear, $targetMonth);

        // Only include months with tasks in the average calculation
        if ($report['total_tasks'] > 0) {
            $totalRates += $report['success_rate'];
            $count++;
        }
    }

    // Return the average or 0 if no tasks were found in the last 3 months
    return $count > 0 ? round($totalRates / $count) : 0;
}
    /**
     * متد AJAX برای دریافت خودکار حقوق و پاداش/جریمه
     */
    public function getAutomatedPayrollData() {
    header('Content-Type: application/json');
    $userId = $_GET['user_id'] ?? 0;
    $year = $_GET['year'] ?? jdate('Y');
    $month = $_GET['month'] ?? jdate('n');

    $employee = $this->userModel->findById($userId);
    $settings = $this->payrollSettingModel->getSettingsByYear($year);

    if (!$employee || !$settings) {
        echo json_encode(['status' => 'error', 'message' => 'اطلاعات کارمند یا تنظیمات سالانه حقوق یافت نشد.']);
        exit;
    }

    $performanceReport = $this->calculateEmployeePerformance($userId, $year, $month);
    $performanceRate = $performanceReport['success_rate'];
        
        $earnings = [];
        $gross_earnings = (float)$settings->base_salary_monthly;
        
        $earnings[] = ['description' => 'حقوق پایه', 'amount' => $settings->base_salary_monthly];
        $earnings[] = ['description' => 'حق مسکن', 'amount' => $settings->housing_allowance];

        if ($employee->children_count > 0) {
            $childAllowance = $settings->family_allowance * $employee->children_count;
            $earnings[] = ['description' => 'حق اولاد', 'amount' => $childAllowance];
            $gross_earnings += $childAllowance;
        }

        if ($employee->hire_date) {
            $hireDate = new DateTime($employee->hire_date);
            $today = new DateTime();
            $yearsOfService = $hireDate->diff($today)->y;
            if ($yearsOfService > 0) {
                // محاسبه پایه سنوات به صورت ماهانه (فرضی)
                $seniorityPay = $yearsOfService * $settings->seniority_per_year * $settings->work_days_in_month;
                $earnings[] = ['description' => 'پایه سنوات', 'amount' => $seniorityPay];
                $gross_earnings += $seniorityPay;
            }
        }
        
        $performanceAdjustment = 0;
        $performanceType = '';
        if ($performanceRate >= 95) {
            $bonus = (float)($settings->bonus_rate_percentage ?? 0);
            $performanceAdjustment = ($settings->base_salary_monthly * $bonus) / 100;
            $performanceType = 'پاداش عملکرد عالی';
        } elseif ($performanceRate >= 70 && $performanceRate < 95) {
            // بدون تغییر در حقوق
        } elseif ($performanceRate >= 60 && $performanceRate < 70) {
            $penalty = (float)($settings->penalty_rate_percentage ?? 0);
            $performanceAdjustment = ($settings->base_salary_monthly * $penalty) / 100;
            $performanceType = 'جریمه عملکرد متوسط';
        } elseif ($performanceRate < 60) {
            $penalty = (float)($settings->penalty_rate_percentage ?? 0) * 2;
            $performanceAdjustment = ($settings->base_salary_monthly * $penalty) / 100;
            $performanceType = 'جریمه عملکرد ضعیف';
        }

        if ($performanceAdjustment > 0) {
             if (strpos($performanceType, 'پاداش') !== false) {
                 $earnings[] = ['description' => $performanceType . ' (' . $performanceRate . '%)', 'amount' => $performanceAdjustment];
                 $gross_earnings += $performanceAdjustment;
             } else {
                 $deductions[] = ['description' => $performanceType . ' (' . $performanceRate . '%)', 'amount' => $performanceAdjustment];
             }
        }

        $insurance_amount = $gross_earnings * 0.07;
        $deductions[] = ['description' => 'بیمه تامین اجتماعی (سهم کارمند)', 'amount' => $insurance_amount];
        
        $tax_amount = ($gross_earnings > 10000000) ? ($gross_earnings - 10000000) * 0.10 : 0; 
        $deductions[] = ['description' => 'مالیات بر درآمد', 'amount' => $tax_amount];


        echo json_encode([
            'status' => 'success',
            'earnings' => $earnings,
            'deductions' => $deductions,
            'performance_rate' => $performanceRate,
            'performance_text' => 'نرخ موفقیت در تسک‌های پروژه: ' . $performanceRate . '%'
        ]);
        exit;
    }
    public function employeeList() {
        $this->auth->restrict(['admin']);
        // اطمینان از اینکه فقط کارمندان و ادمین‌های با hire_date نمایش داده شوند
        $employees = $this->userModel->getAllEmployees();
        
        view('admin/users/employee_list', [
            'layout' => 'admin_layout',
            'title' => 'لیست کارمندان و گزارش عملکرد',
            'employees' => $employees
        ]);
    }
    public function updateInvoiceStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
            $status = sanitize($_POST['status']);
            if ($this->invoiceModel->updateStatus($id, $status)) {
                FlashMessage::set('message', 'وضعیت فاکتور با موفقیت به‌روزرسانی شد.');
            } else {
                FlashMessage::set('message', 'خطا در به‌روزرسانی وضعیت فاکتور.', 'error');
            }
        } else {
            FlashMessage::set('message', 'درخواست نامعتبر.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=view_invoice&id=' . $id);
    }
    
    private function is_leap_jalali($jy) {
        $mod = ($jy % 33);
        if ($mod == 1 || $mod == 5 || $mod == 9 || $mod == 13 || $mod == 17 || $mod == 22 || $mod == 26 || $mod == 30) {
            return true;
        }
        return false;
    }

    // =========================================================================
    // ۱. سامانه مودیان مالیاتی کشور (Comprehensive Taxpayer System)
    // =========================================================================
    public function moadianInvoices() {
        require_once __DIR__ . '/../models/TaxpayerInvoice.php';
        $moadianModel = new TaxpayerInvoice();
        $status = $_GET['status'] ?? null;
        $subject = $_GET['subject'] ?? null;
        $invoices = $moadianModel->getAll(200, $status, $subject);
        $stats = $moadianModel->getSummaryStats();
        $settings = $moadianModel->getSettings();

        // دریافت کلید API فعال برای نمایش در تب اتصال به نرم‌افزار حسابداری
        $this->db->query("SELECT * FROM api_keys WHERE status = 'active' ORDER BY id ASC LIMIT 1");
        $apiKey = $this->db->fetch();

        view('admin/moadian/index', [
            'layout' => 'admin_layout',
            'title' => 'سامانه مودیان مالیاتی و فاکتورهای رسمی',
            'invoices' => $invoices,
            'stats' => $stats,
            'settings' => $settings,
            'apiKey' => $apiKey,
            'currentStatus' => $status,
            'currentSubject' => $subject
        ]);
    }

    public function sendToMoadian() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            FlashMessage::set('message', 'شناسه فاکتور مشخص نشده است.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=moadian_invoices#settings-pane');
        }
        require_once __DIR__ . '/../models/TaxpayerInvoice.php';
        $moadianModel = new TaxpayerInvoice();
        $res = $moadianModel->transmitToTaxAuthority($id);
        if ($res['success']) {
            FlashMessage::set('message', $res['message'] . ' (شناسه یکتای مالیاتی: ' . $res['taxid'] . ' | کد پیگیری: ' . $res['uid'] . ')', 'success');
        } else {
            FlashMessage::set('message', $res['message'], 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=moadian_invoices#settings-pane');
    }

    public function inquiryMoadian() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            FlashMessage::set('message', 'شناسه فاکتور مودیان مشخص نشده است.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=moadian_invoices#settings-pane');
        }
        require_once __DIR__ . '/../models/TaxpayerInvoice.php';
        $moadianModel = new TaxpayerInvoice();
        $res = $moadianModel->inquiryStatus($id);
        if ($res['success']) {
            FlashMessage::set('message', 'استعلام تایید شد: ' . $res['message'] . ' (وضعیت: ' . $res['status_title'] . ' | شناسه یکتا: ' . $res['taxid'] . ')', 'success');
        } else {
            FlashMessage::set('message', 'نتیجه استعلام: ' . $res['message'], 'warning');
        }
        redirect(APP_URL . '/index.php?page=admin&action=moadian_invoices#settings-pane');
    }

    public function cancelMoadian() {
        $id = $_GET['id'] ?? null;
        $reason = $_POST['reason'] ?? $_GET['reason'] ?? 'ابطال بنا به درخواست مودی';
        if (!$id) {
            FlashMessage::set('message', 'شناسه فاکتور جهت ابطال مشخص نشده است.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=moadian_invoices#settings-pane');
        }
        require_once __DIR__ . '/../models/TaxpayerInvoice.php';
        $moadianModel = new TaxpayerInvoice();
        $res = $moadianModel->cancelInvoice($id, $reason);
        if ($res['success']) {
            FlashMessage::set('message', $res['message'] . ' (شناسه ابطالی: ' . $res['revocation_taxid'] . ')', 'success');
        } else {
            FlashMessage::set('message', $res['message'], 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=moadian_invoices#settings-pane');
    }

    public function saveMoadianSettings() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/TaxpayerInvoice.php';
            $moadianModel = new TaxpayerInvoice();
            if ($moadianModel->saveSettings($_POST)) {
                FlashMessage::set('message', 'تنظیمات و کلیدهای سامانه مودیان با موفقیت ذخیره شد.', 'success');
            } else {
                FlashMessage::set('message', 'خطا در ذخیره تنظیمات سامانه مودیان.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=moadian_invoices#settings-pane');
        }
    }

    public function sendInvoiceToMoadian() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            FlashMessage::set('message', 'شناسه فاکتور مشخص نشده است.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=invoices');
        }
        require_once __DIR__ . '/../models/TaxpayerInvoice.php';
        $moadianModel = new TaxpayerInvoice();
        $mId = $moadianModel->createFromInvoice($id);
        if ($mId) {
            $res = $moadianModel->transmitToTaxAuthority($mId);
            FlashMessage::set('message', 'فاکتور رسمی با موفقیت به سامانه مودیان ارسال و تایید شد. (شناسه یکتای مالیاتی: ' . $res['taxid'] . ')', 'success');
        } else {
            FlashMessage::set('message', 'خطا در ثبت فاکتور در کارپوشه مودیان.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=invoices');
    }

    public function printMoadian($id) {
        require_once __DIR__ . '/../models/TaxpayerInvoice.php';
        $moadianModel = new TaxpayerInvoice();
        $invoice = $moadianModel->getById($id);
        if (!$invoice) {
            die('صورتحساب مودیان یافت نشد.');
        }
        $settings = $moadianModel->getSettings();
        $packet = $moadianModel->buildMoadianJsonPacket($id);
        view('admin/moadian/print', [
            'layout' => false,
            'title' => 'صورتحساب رسمی الکترونیکی مودیان - ' . $invoice->taxid,
            'invoice' => $invoice,
            'settings' => $settings,
            'packet' => $packet
        ]);
    }

    public function showApiDocs() {
        $this->auth->restrict(['admin', 'accountant']);
        $this->db->query("SELECT * FROM api_keys WHERE status = 'active' ORDER BY id ASC LIMIT 1");
        $apiKey = $this->db->fetch();

        view('admin/settings/api_docs', [
            'layout' => 'admin_layout',
            'title' => 'مستندات و وب‌سرویس اتصال نرم‌افزارهای حسابداری (API)',
            'apiKey' => $apiKey
        ]);
    }

    // =========================================================================
    // ۲. خزانه‌داری پیشرفته، چک‌ها و راس‌گیری وزنی (Treasury & Cheques & Rasgiri)
    // =========================================================================
    public function treasuryCheques() {
        require_once __DIR__ . '/../models/Cheque.php';
        $chequeModel = new Cheque();
        $type = $_GET['type'] ?? null;
        $status = $_GET['status'] ?? null;
        $cheques = $chequeModel->getAll($type, $status);

        view('admin/treasury/cheques', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت چک‌های صیادی و خزانه‌داری',
            'cheques' => $cheques,
            'currentType' => $type,
            'currentStatus' => $status
        ]);
    }

    public function createCheque() {
        $clients = $this->clientModel->getAll();
        view('admin/treasury/create_cheque', [
            'layout' => 'admin_layout',
            'title' => 'ثبت چک صیادی جدید',
            'clients' => $clients
        ]);
    }

    public function storeCheque() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/Cheque.php';
            $chequeModel = new Cheque();
            $id = $chequeModel->create($_POST);
            if ($id) {
                FlashMessage::set('message', 'چک صیادی با موفقیت در سیستم خزانه‌داری ثبت شد.', 'success');
            } else {
                FlashMessage::set('message', 'خطا در ثبت اطلاعات چک.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=treasury_cheques');
    }

    public function changeChequeStatus() {
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? 'cleared';
        $transferredTo = $_POST['transferred_to'] ?? null;

        if ($id) {
            require_once __DIR__ . '/../models/Cheque.php';
            $chequeModel = new Cheque();
            $chequeModel->updateStatus($id, $status, $transferredTo);
            FlashMessage::set('message', 'وضعیت چک با موفقیت بروزرسانی شد.', 'success');
        }
        redirect(APP_URL . '/index.php?page=admin&action=treasury_cheques');
    }

    public function treasuryRasgiri() {
        require_once __DIR__ . '/../models/Cheque.php';
        $chequeModel = new Cheque();
        $allCheques = $chequeModel->getAll('received', 'pending');
        
        $rasResult = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['selected_cheques'])) {
            $selectedIds = $_POST['selected_cheques'];
            $items = [];
            foreach ($allCheques as $ch) {
                if (in_array($ch->id, $selectedIds)) {
                    $items[] = ['amount' => $ch->amount, 'date' => $ch->due_date];
                }
            }
            $rasResult = Cheque::calculateRas($items, $_POST['base_date'] ?? date('Y/m/d'));
        }

        view('admin/treasury/rasgiri', [
            'layout' => 'admin_layout',
            'title' => 'محاسبه‌گر راس‌گیری وزنی چک‌ها و فاکتورها',
            'cheques' => $allCheques,
            'rasResult' => $rasResult
        ]);
    }

    // =========================================================================
    // ۳. برنامه‌ریزی تولید، فرمول ساخت (BOM) و بهای تمام‌شده (Manufacturing & BOM)
    // =========================================================================
    public function boms() {
        require_once __DIR__ . '/../models/Bom.php';
        $bomModel = new Bom();
        $boms = $bomModel->getAll();

        view('admin/manufacturing/boms', [
            'layout' => 'admin_layout',
            'title' => 'فرمول‌های ساخت کالا (BOM)',
            'boms' => $boms
        ]);
    }

    public function createBom() {
        require_once __DIR__ . '/../models/Product.php';
        require_once __DIR__ . '/../models/Unit.php';
        $productModel = new Product();
        $unitModel = new Unit();

        $products = $productModel->getAll();
        $units = $unitModel->getAll();

        view('admin/manufacturing/create_bom', [
            'layout' => 'admin_layout',
            'title' => 'تعریف فرمول ساخت جدید (BOM)',
            'products' => $products,
            'units' => $units
        ]);
    }

    public function storeBom() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/Bom.php';
            $bomModel = new Bom();

            $items = [];
            if (!empty($_POST['raw_materials'])) {
                foreach ($_POST['raw_materials'] as $index => $rawId) {
                    if (!empty($rawId)) {
                        $items[] = [
                            'raw_material_id' => $rawId,
                            'quantity' => $_POST['raw_quantities'][$index] ?? 1,
                            'scrap_percent' => $_POST['scrap_percents'][$index] ?? 0,
                            'unit_id' => $_POST['raw_units'][$index] ?? null
                        ];
                    }
                }
            }

            $bomId = $bomModel->create($_POST, $items);
            if ($bomId) {
                FlashMessage::set('message', 'فرمول ساخت با موفقیت ذخیره شد.', 'success');
            } else {
                FlashMessage::set('message', 'خطا در ثبت فرمول ساخت.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=boms');
    }

    public function productionOrders() {
        require_once __DIR__ . '/../models/ProductionOrder.php';
        $poModel = new ProductionOrder();
        $orders = $poModel->getAll();

        view('admin/manufacturing/orders', [
            'layout' => 'admin_layout',
            'title' => 'دستورات تولید و بهای تمام‌شده',
            'orders' => $orders
        ]);
    }

    public function createProductionOrder() {
        require_once __DIR__ . '/../models/Bom.php';
        require_once __DIR__ . '/../models/Warehouse.php';
        $bomModel = new Bom();
        $whModel = new Warehouse();

        view('admin/manufacturing/create_order', [
            'layout' => 'admin_layout',
            'title' => 'صدور دستور تولید جدید',
            'boms' => $bomModel->getAll(),
            'warehouses' => $whModel->getAll()
        ]);
    }

    public function storeProductionOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/ProductionOrder.php';
            $poModel = new ProductionOrder();
            $id = $poModel->create($_POST);
            if ($id) {
                FlashMessage::set('message', 'دستور تولید با موفقیت صادر شد.', 'success');
            } else {
                FlashMessage::set('message', 'خطا در ثبت دستور تولید.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=production_orders');
    }

    public function completeProductionOrder() {
        $id = $_GET['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            require_once __DIR__ . '/../models/ProductionOrder.php';
            $poModel = new ProductionOrder();
            $actualQty = (float)($_POST['actual_qty'] ?? 1);
            $laborCost = (int)($_POST['actual_labor_cost'] ?? 0);
            $overheadCost = (int)($_POST['actual_overhead_cost'] ?? 0);

            $res = $poModel->completeOrder($id, $actualQty, $laborCost, $overheadCost);
            if ($res) {
                FlashMessage::set('message', 'عملیات تولید تکمیل و کالاها به انبار منتقل شدند. بهای تمام‌شده محاسبه گردید.', 'success');
            } else {
                FlashMessage::set('message', 'خطا در نهایی‌سازی تولید.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=production_orders');
    }

    // =========================================================================
    // ۴. تدارکات و زنجیره تأمین (Procurement & SCM)
    // =========================================================================
    public function procurementRequisitions() {
        require_once __DIR__ . '/../models/PurchaseRequisition.php';
        $reqModel = new PurchaseRequisition();
        $status = $_GET['status'] ?? null;
        $requisitions = $reqModel->getAll($status);

        view('admin/procurement/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت درخواست‌های خرید و تدارکات',
            'requisitions' => $requisitions,
            'currentStatus' => $status
        ]);
    }

    public function createRequisition() {
        require_once __DIR__ . '/../models/Product.php';
        require_once __DIR__ . '/../models/Warehouse.php';
        $productModel = new Product();
        $whModel = new Warehouse();

        view('admin/procurement/create', [
            'layout' => 'admin_layout',
            'title' => 'ثبت درخواست خرید جدید',
            'products' => $productModel->getAll(),
            'warehouses' => $whModel->getAll()
        ]);
    }

    public function storeRequisition() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/PurchaseRequisition.php';
            $reqModel = new PurchaseRequisition();
            $_POST['requester_id'] = Auth::user()->id ?? null;
            $id = $reqModel->create($_POST);
            if ($id) {
                FlashMessage::set('message', 'درخواست خرید با موفقیت ثبت شد.', 'success');
            } else {
                FlashMessage::set('message', 'خطا در ثبت درخواست خرید.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=procurement_requisitions');
    }

    public function approveRequisition() {
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? 'approved';
        if ($id) {
            require_once __DIR__ . '/../models/PurchaseRequisition.php';
            $reqModel = new PurchaseRequisition();
            $reqModel->updateStatus($id, $status, Auth::user()->id ?? null);
            FlashMessage::set('message', 'وضعیت درخواست خرید با موفقیت بروزرسانی شد.', 'success');
        }
        redirect(APP_URL . '/index.php?page=admin&action=procurement_requisitions');
    }

    // =========================================================================
    // ۵. تضامین و ضمانت‌نامه‌های قراردادها (Contract Guarantees)
    // =========================================================================
    public function contractGuarantees() {
        require_once __DIR__ . '/../models/ContractGuarantee.php';
        $cgModel = new ContractGuarantee();
        $contractId = $_GET['contract_id'] ?? null;
        $guarantees = $cgModel->getAll($contractId);

        $contracts = $this->contractModel->getAll();

        view('admin/contracts/guarantees', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت تضامین و ضمانت‌نامه‌های بانکی',
            'guarantees' => $guarantees,
            'contracts' => $contracts,
            'currentContractId' => $contractId
        ]);
    }

    public function storeGuarantee() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/ContractGuarantee.php';
            $cgModel = new ContractGuarantee();
            $id = $cgModel->create($_POST);
            if ($id) {
                FlashMessage::set('message', 'ضمانت‌نامه / تضامین با موفقیت ثبت شد.', 'success');
            } else {
                FlashMessage::set('message', 'خطا در ثبت ضمانت‌نامه.', 'error');
            }
        }
        redirect(APP_URL . '/index.php?page=admin&action=contract_guarantees');
    }

    public function updateGuaranteeStatus() {
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? 'released';
        if ($id) {
            require_once __DIR__ . '/../models/ContractGuarantee.php';
            $cgModel = new ContractGuarantee();
            $cgModel->updateStatus($id, $status);
            FlashMessage::set('message', 'وضعیت ضمانت‌نامه با موفقیت بروز شد.', 'success');
        }
        redirect(APP_URL . '/index.php?page=admin&action=contract_guarantees');
    }

    // =========================================================================
    // ۶. دستیار هوش مصنوعی و تحلیل‌گر سازمانی (Hamakar AI Analytics & OCR)
    // =========================================================================
    public function aiAnalytics() {
        require_once __DIR__ . '/../lib/AiAnalyticsService.php';
        $aiService = new AiAnalyticsService();
        $insights = $aiService->getExecutiveInsights();

        $ocrResult = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['invoice_scan'])) {
            $ocrResult = $aiService->simulateInvoiceOcr($_FILES['invoice_scan']['name']);
        }

        view('admin/ai/analytics', [
            'layout' => 'admin_layout',
            'title' => 'دستیار هوش مصنوعی و تحلیل مالی سازمانی',
            'insights' => $insights,
            'ocrResult' => $ocrResult
        ]);
    }

    // =========================================================================
    // ۷. موتور اتوماسیون گردش کار شرطی (Workflow Automation Engine)
    // =========================================================================
    public function workflowRules() {
        require_once __DIR__ . '/../models/WorkflowRule.php';
        $wfModel = new WorkflowRule();
        $rules = $wfModel->getAll();

        view('admin/settings/workflows', [
            'layout' => 'admin_layout',
            'title' => 'موتور اتوماسیون گردش کار و قوانین شرطی',
            'rules' => $rules
        ]);
    }

    public function storeWorkflowRule() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/WorkflowRule.php';
            $wfModel = new WorkflowRule();
            $wfModel->create($_POST);
            FlashMessage::set('message', 'قانون اتوماسیون با موفقیت ایجاد گردید.', 'success');
        }
        redirect(APP_URL . '/index.php?page=admin&action=workflow_rules');
    }

    public function toggleWorkflowRule() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            require_once __DIR__ . '/../models/WorkflowRule.php';
            $wfModel = new WorkflowRule();
            $wfModel->toggle($id);
            FlashMessage::set('message', 'وضعیت قانون اتوماسیون تغییر یافت.', 'success');
        }
        redirect(APP_URL . '/index.php?page=admin&action=workflow_rules');
    }


    // =========================================================================
    // ۳. مدیریت جامع تنظیمات سامانه و پشتیبان‌گیری فیزیکی و ابری (System Settings & Backup)
    // =========================================================================
    public function systemSettings() {
        $this->auth->restrict(['admin']);
        require_once __DIR__ . '/../models/SystemSetting.php';
        $settingModel = new SystemSetting();
        $settings = $settingModel->getAll();

        view('admin/settings/system_settings', [
            'layout' => 'admin_layout',
            'title' => 'تنظیمات یکپارچه سامانه هماکار',
            'settings' => $settings
        ]);
    }

    public function saveSystemSettings() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/SystemSetting.php';
            $settingModel = new SystemSetting();
            if ($settingModel->saveBulk($_POST)) {
                FlashMessage::set('message', 'تنظیمات سامانه با موفقیت به‌روزرسانی گردید.', 'success');
            } else {
                FlashMessage::set('message', 'خطا در ذخیره‌سازی تنظیمات.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=system_settings');
        }
    }

    public function backupManager() {
        $this->auth->restrict(['admin']);
        require_once __DIR__ . '/../models/BackupManager.php';
        $backupManager = new BackupManager();
        $backups = $backupManager->getAllBackups();

        $totalSize = array_sum(array_map(function($b) { return (int)($b->filesize ?? 0); }, $backups));

        view('admin/settings/backups', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت پشتیبان‌گیری فیزیکی و هاست ابری',
            'backups' => $backups,
            'totalSize' => $totalSize
        ]);
    }

    public function createCloudBackup() {
        $this->auth->restrict(['admin']);
        require_once __DIR__ . '/../models/BackupManager.php';
        $backupManager = new BackupManager();
        $res = $backupManager->createCloudBackup('manual', $this->auth->user()->name ?? 'مدیر سامانه');
        if ($res['success']) {
            FlashMessage::set('message', $res['message'], 'success');
        } else {
            FlashMessage::set('message', $res['message'], 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=backups');
    }

    public function downloadPhysicalBackup() {
        $this->auth->restrict(['admin']);
        require_once __DIR__ . '/../models/BackupManager.php';
        $backupManager = new BackupManager();
        $backupManager->downloadPhysicalBackup();
    }

    public function downloadCloudBackup($id) {
        $this->auth->restrict(['admin']);
        require_once __DIR__ . '/../models/BackupManager.php';
        $backupManager = new BackupManager();
        $backupManager->downloadBackupById($id);
    }

    public function restoreBackup($id) {
        $this->auth->restrict(['admin']);
        require_once __DIR__ . '/../models/BackupManager.php';
        $backupManager = new BackupManager();
        $res = $backupManager->restoreBackupById($id);
        if ($res['success']) {
            FlashMessage::set('message', $res['message'], 'success');
        } else {
            FlashMessage::set('message', $res['message'], 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=backups');
    }

    public function restoreUploadedBackup() {
        $this->auth->restrict(['admin']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['backup_file'])) {
            $file = $_FILES['backup_file'];
            if ($file['error'] === UPLOAD_ERR_OK && is_uploaded_file($file['tmp_name'])) {
                $content = file_get_contents($file['tmp_name']);
                require_once __DIR__ . '/../models/BackupManager.php';
                $backupManager = new BackupManager();
                $res = $backupManager->restoreFromSql($content);
                if ($res['success']) {
                    FlashMessage::set('message', $res['message'], 'success');
                } else {
                    FlashMessage::set('message', $res['message'], 'error');
                }
            } else {
                FlashMessage::set('message', 'خطا در بارگذاری فایل پشتیبان.', 'error');
            }
            redirect(APP_URL . '/index.php?page=admin&action=backups');
        }
    }

    public function deleteCloudBackup($id) {
        $this->auth->restrict(['admin']);
        require_once __DIR__ . '/../models/BackupManager.php';
        $backupManager = new BackupManager();
        if ($backupManager->deleteBackupById($id)) {
            FlashMessage::set('message', 'فایل نسخه پشتیبان با موفقیت از هاست حذف شد.', 'success');
        } else {
            FlashMessage::set('message', 'خطا در حذف فایل پشتیبان.', 'error');
        }
        redirect(APP_URL . '/index.php?page=admin&action=backups');
    }

    public function testMoadianConnection() {
        $this->auth->restrict(['admin']);
        header('Content-Type: application/json; charset=utf-8');
        require_once __DIR__ . '/../models/TaxpayerInvoice.php';
        $moadianModel = new TaxpayerInvoice();
        $res = $moadianModel->testConnectionAndKeys();
        echo json_encode(['status' => 'success', 'data' => $res]);
        exit();
    }

    public function generateMoadianKeys() {
        $this->auth->restrict(['admin']);
        header('Content-Type: application/json; charset=utf-8');
        require_once __DIR__ . '/../models/TaxpayerInvoice.php';
        $moadianModel = new TaxpayerInvoice();
        $res = $moadianModel->generateStandardRsaKeys();
        echo json_encode(['status' => 'success', 'data' => $res]);
        exit();
    }


    public function licensing() {
        $this->auth->restrict(['admin']);
        require_once __DIR__ . '/../core/LicenseManager.php';
        
        $licenseDir = __DIR__ . '/../views/admin/license';
        if (!is_dir($licenseDir)) {
            @mkdir($licenseDir, 0777, true);
        }
        $indexPath = $licenseDir . '/index.php';
        if (!file_exists($indexPath)) {
            // Write view file
            $viewContent = base64_decode('PD9waHAKLy8gYXBwL3ZpZXdzL2FkbWluL2xpY2Vuc2UvaW5kZXgucGhwIC0g2b7ZhtmEINmF2K/bjNix24zYqiDZhNin24zYs9mG2LPYjCDZgtmB2YQg2LPYrtiq4oCM2KfZgdiy2KfYsduMINmIINmB2LHZiNi0INm+2YTZhiDYqNmHINm+2YTZhiDZh9mF2Kfaqdin2LEgRVJQCiRsaWNlbnNlID0gJGxpY2Vuc2UgPz8gbnVsbDsKJG1hY2hpbmVGaW5nZXJwcmludCA9ICRtYWNoaW5lRmluZ2VycHJpbnQgPz8gJ0hLLVVOS05PV04nOwokcGxhbnMgPSAkcGxhbnMgPz8gW107CiRtb2R1bGVzID0gJG1vZHVsZXMgPz8gW107Cj8+Cgo8c3R5bGU+Ci5saWNlbnNlLWtwaS1jYXJkIHsKICAgIGJhY2tncm91bmQ6ICNmZmZmZmY7CiAgICBib3JkZXI6IDFweCBzb2xpZCAjZTJlOGYwOwogICAgYm9yZGVyLXJhZGl1czogMjBweDsKICAgIHBhZGRpbmc6IDEuMjVyZW0gMS40cmVtOwogICAgYm94LXNoYWRvdzogMCA0cHggMjBweCByZ2JhKDE1LCAyMywgNDIsIDAuMDQpOwogICAgdHJhbnNpdGlvbjogYWxsIDAuMjVzIGVhc2U7Cn0KLmxpY2Vuc2Uta3BpLWNhcmQ6aG92ZXIgewogICAgYm94LXNoYWRvdzogMCAxMHB4IDI1cHggcmdiYSgxNSwgMjMsIDQyLCAwLjA4KTsKfQouaHctZmluZ2VycHJpbnQtYm94IHsKICAgIGJhY2tncm91bmQ6ICMwZjE3MmE7CiAgICBjb2xvcjogIzM4YmRmODsKICAgIGZvbnQtZmFtaWx5OiAnQ29uc29sYXMnLCBtb25vc3BhY2U7CiAgICBmb250LXNpemU6IDEuMDVyZW07CiAgICBsZXR0ZXItc3BhY2luZzogMS41cHg7CiAgICBwYWRkaW5nOiAxMHB4IDE4cHg7CiAgICBib3JkZXItcmFkaXVzOiAxMnB4OwogICAgZGlzcGxheTogaW5saW5lLWZsZXg7CiAgICBhbGlnbi1pdGVtczogY2VudGVyOwogICAgZ2FwOiAxMnB4OwogICAgYm9yZGVyOiAxcHggc29saWQgcmdiYSg1NiwgMTg5LCAyNDgsIDAuMyk7CiAgICBib3gtc2hhZG93OiAwIDAgMjBweCByZ2JhKDU2LCAxODksIDI0OCwgMC4xNSk7Cn0KLm5hdi1waWxscy1saWNlbnNlIC5uYXYtbGluayB7CiAgICBib3JkZXItcmFkaXVzOiAxMnB4OwogICAgcGFkZGluZzogMTBweCAyMnB4OwogICAgZm9udC13ZWlnaHQ6IDcwMDsKICAgIGNvbG9yOiAjNjQ3NDhiOwogICAgYm9yZGVyOiAxcHggc29saWQgdHJhbnNwYXJlbnQ7CiAgICB0cmFuc2l0aW9uOiBhbGwgMC4ycyBlYXNlOwp9Ci5uYXYtcGlsbHMtbGljZW5zZSAubmF2LWxpbmsuYWN0aXZlIHsKICAgIGJhY2tncm91bmQ6ICM0ZjQ2ZTU7CiAgICBjb2xvcjogI2ZmZmZmZjsKICAgIGJveC1zaGFkb3c6IDAgNHB4IDE0cHggcmdiYSg3OSwgNzAsIDIyOSwgMC4zKTsKfQoucGxhbi1mZWF0dXJlLWNhcmQgewogICAgYm9yZGVyOiAxLjVweCBzb2xpZCAjZTJlOGYwOwogICAgYm9yZGVyLXJhZGl1czogMjBweDsKICAgIHRyYW5zaXRpb246IGFsbCAwLjI1cyBlYXNlOwogICAgaGVpZ2h0OiAxMDAlOwogICAgZGlzcGxheTogZmxleDsKICAgIGZsZXgtZGlyZWN0aW9uOiBjb2x1bW47CiAgICBqdXN0aWZ5LWNvbnRlbnQ6IHNwYWNlLWJldHdlZW47Cn0KLnBsYW4tZmVhdHVyZS1jYXJkLmFjdGl2ZS1wbGFuIHsKICAgIGJvcmRlci1jb2xvcjogIzRmNDZlNTsKICAgIGJveC1zaGFkb3c6IDAgOHB4IDMwcHggcmdiYSg3OSwgNzAsIDIyOSwgMC4xNSk7CiAgICBiYWNrZ3JvdW5kOiAjZmRmZGZlOwp9Ci5wbGFuLWZlYXR1cmUtY2FyZDpob3ZlciB7CiAgICB0cmFuc2Zvcm06IHRyYW5zbGF0ZVkoLTRweCk7CiAgICBib3gtc2hhZG93OiAwIDEycHggMjhweCByZ2JhKDE1LCAyMywgNDIsIDAuMDgpOwp9Ci5tb2R1bGUtYmFkZ2UtcGlsbCB7CiAgICBwYWRkaW5nOiA2cHggMTJweDsKICAgIGJvcmRlci1yYWRpdXM6IDEwcHg7CiAgICBmb250LXNpemU6IDAuNzhyZW07CiAgICBmb250LXdlaWdodDogNjAwOwogICAgZGlzcGxheTogaW5saW5lLWZsZXg7CiAgICBhbGlnbi1pdGVtczogY2VudGVyOwogICAgZ2FwOiA2cHg7CiAgICBtYXJnaW46IDNweDsKfQoubW9kdWxlLWJhZGdlLXBpbGwuZW5hYmxlZCB7CiAgICBiYWNrZ3JvdW5kOiAjZWNmZGY1OwogICAgY29sb3I6ICMwNTk2Njk7CiAgICBib3JkZXI6IDFweCBzb2xpZCAjYTdmM2QwOwp9Ci5tb2R1bGUtYmFkZ2UtcGlsbC5kaXNhYmxlZCB7CiAgICBiYWNrZ3JvdW5kOiAjZjFmNWY5OwogICAgY29sb3I6ICM5NGEzYjg7CiAgICBib3JkZXI6IDFweCBzb2xpZCAjZTJlOGYwOwp9Cjwvc3R5bGU+Cgo8ZGl2IGNsYXNzPSJjb250YWluZXItZmx1aWQgcHktMyI+CiAgICA8IS0tIFRvcCBIZWFkZXIgLS0+CiAgICA8ZGl2IGNsYXNzPSJkLWZsZXgganVzdGlmeS1jb250ZW50LWJldHdlZW4gYWxpZ24taXRlbXMtY2VudGVyIG1iLTQgZmxleC13cmFwIGdhcC0zIj4KICAgICAgICA8ZGl2PgogICAgICAgICAgICA8ZGl2IGNsYXNzPSJkLWZsZXggYWxpZ24taXRlbXMtY2VudGVyIGdhcC0yIG1iLTIgZmxleC13cmFwIj4KICAgICAgICAgICAgICAgIDxzcGFuIGNsYXNzPSJiYWRnZSBiZy1wcmltYXJ5IGJnLW9wYWNpdHktMTAgdGV4dC1wcmltYXJ5IGJvcmRlciBib3JkZXItcHJpbWFyeSBib3JkZXItb3BhY2l0eS0yNSBweC0zIHB5LTEgcm91bmRlZC1waWxsIGZ3LWJvbGQiPgogICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXMgZmEtY2VydGlmaWNhdGUgbWUtMSI+PC9pPtiz24zYs9iq2YUg2LXYr9mI2LEg2YTYp9uM2LPZhtizINmIINmC2YHZhCDYs9iu2KrigIzYp9mB2LLYp9ix24wKICAgICAgICAgICAgICAgIDwvc3Bhbj4KICAgICAgICAgICAgICAgIDxzcGFuIGNsYXNzPSJiYWRnZSBiZy1zdWNjZXNzIGJnLW9wYWNpdHktMTAgdGV4dC1zdWNjZXNzIGJvcmRlciBib3JkZXItc3VjY2VzcyBib3JkZXItb3BhY2l0eS0yNSBweC0zIHB5LTEgcm91bmRlZC1waWxsIGZ3LWJvbGQiPgogICAgICAgICAgICAgICAgICAgINmG2LPYrtmHINiq2KzYp9ix24wg27Qu27Au27Ag2YHYudin2YQKICAgICAgICAgICAgICAgIDwvc3Bhbj4KICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgIDxoMyBjbGFzcz0iZnctYm9sZCBtYi0xIHRleHQtZGFyayI+CiAgICAgICAgICAgICAgICA8aSBjbGFzcz0iZmFzIGZhLXNoaWVsZC1oYWx2ZWQgdGV4dC1wcmltYXJ5IG1lLTIiPjwvaT7Zhdiv24zYsduM2Kog2YTYp9uM2LPZhtiz2Iwg2YLZgdmEINiz2K7YquKAjNin2YHYstin2LHbjCDZiCDZvtmE2YbigIzZh9in24wg2KrYrNin2LHbjAogICAgICAgICAgICA8L2gzPgogICAgICAgICAgICA8c3BhbiBjbGFzcz0idGV4dC1tdXRlZCBzbWFsbCI+2YHYsdmI2LQg2b7ZhNmGINio2Ycg2b7ZhNmG2Iwg2KfZhtiq2K7Yp9ioINmF2KfamNmI2YQg2KjZhyDZhdin2pjZiNmE2Iwg2LXYr9mI2LEg2YTYp9uM2LPZhtizINix2YXYstmG2q/Yp9ix24wg2LTYr9mHIFJTQSDZiCDZgtmB2YQg2KfYrtiq2LXYp9i124wg2LPYsdmI2LE8L3NwYW4+CiAgICAgICAgPC9kaXY+CiAgICAgICAgCiAgICAgICAgPCEtLSDZgtmB2YQg2LPYrtiq4oCM2KfZgdiy2KfYsduMINiz2LHZiNixIC0tPgogICAgICAgIDxkaXYgY2xhc3M9InRleHQtc3RhcnQiPgogICAgICAgICAgICA8c21hbGwgY2xhc3M9InRleHQtbXV0ZWQgZC1ibG9jayBmdy1ib2xkIG1iLTEiPti02YbYp9iz2Ycg24zaqdiq2KfbjCDZgtmB2YQg2LPYrtiq4oCM2KfZgdiy2KfYsduMINiz2LHZiNixIChNYWNoaW5lIElEKTo8L3NtYWxsPgogICAgICAgICAgICA8ZGl2IGNsYXNzPSJody1maW5nZXJwcmludC1ib3giPgogICAgICAgICAgICAgICAgPGkgY2xhc3M9ImZhcyBmYS1taWNyb2NoaXAiPjwvaT4KICAgICAgICAgICAgICAgIDxzcGFuIGlkPSJtYWNoaW5lX2h3X2lkIj48P3BocCBlY2hvIGh0bWxzcGVjaWFsY2hhcnMoJG1hY2hpbmVGaW5nZXJwcmludCk7ID8+PC9zcGFuPgogICAgICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPSJidXR0b24iIGNsYXNzPSJidG4gYnRuLWxpbmsgYnRuLXNtIHRleHQtaW5mbyBwLTAiIG9uY2xpY2s9Im5hdmlnYXRvci5jbGlwYm9hcmQud3JpdGVUZXh0KCc8P3BocCBlY2hvICRtYWNoaW5lRmluZ2VycHJpbnQ7ID8+Jyk7IGFsZXJ0KCfYtNmG2KfYs9mHINmC2YHZhCDYs9iu2KrigIzYp9mB2LLYp9ix24wg2qnZvtuMINi02K86IDw/cGhwIGVjaG8gJG1hY2hpbmVGaW5nZXJwcmludDsgPz4nKTsiIHRpdGxlPSLaqdm+24wg2LTZhtin2LPZhyDYs9iu2KrigIzYp9mB2LLYp9ix24wiPgogICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXIgZmEtY29weSI+PC9pPgogICAgICAgICAgICAgICAgPC9idXR0b24+CiAgICAgICAgICAgIDwvZGl2PgogICAgICAgIDwvZGl2PgogICAgPC9kaXY+CgogICAgPD9waHAgRmxhc2hNZXNzYWdlOjpkaXNwbGF5KCk7ID8+CgogICAgPCEtLSBLUEkgQ2FyZHMgLS0+CiAgICA8ZGl2IGNsYXNzPSJyb3cgZy0zIG1iLTQiPgogICAgICAgIDxkaXYgY2xhc3M9ImNvbC02IGNvbC1sZy0zIj4KICAgICAgICAgICAgPGRpdiBjbGFzcz0ibGljZW5zZS1rcGktY2FyZCI+CiAgICAgICAgICAgICAgICA8c21hbGwgY2xhc3M9InRleHQtbXV0ZWQgZC1ibG9jayBmdy1ib2xkIj7Zhdin2YTaqSDZhNin24zYs9mG2LMgLyDYtNix2qnYqjwvc21hbGw+CiAgICAgICAgICAgICAgICA8aDUgY2xhc3M9ImZ3LWJvbGQgbWItMSB0ZXh0LWRhcmsgdGV4dC10cnVuY2F0ZSIgdGl0bGU9Ijw/cGhwIGVjaG8gaHRtbHNwZWNpYWxjaGFycygkbGljZW5zZS0+bGljZW5zZWUgPz8gJ9mG2KfZhdi02K7YtScpOyA/PiI+CiAgICAgICAgICAgICAgICAgICAgPD9waHAgZWNobyBodG1sc3BlY2lhbGNoYXJzKCRsaWNlbnNlLT5saWNlbnNlZSA/PyAn2YbYs9iu2Ycg2K/ZhdmIJyk7ID8+CiAgICAgICAgICAgICAgICA8L2g1PgogICAgICAgICAgICAgICAgPHNwYW4gY2xhc3M9ImJhZGdlIGJnLXByaW1hcnkgYmctb3BhY2l0eS0xMCB0ZXh0LXByaW1hcnkgcm91bmRlZC1waWxsIj4KICAgICAgICAgICAgICAgICAgICA8P3BocCBlY2hvIGh0bWxzcGVjaWFsY2hhcnMoJGxpY2Vuc2UtPnBsYW5fbmFtZSA/PyAn2b7ZhNmGINmG2KfZhdi02K7YtScpOyA/PgogICAgICAgICAgICAgICAgPC9zcGFuPgogICAgICAgICAgICA8L2Rpdj4KICAgICAgICA8L2Rpdj4KICAgICAgICA8ZGl2IGNsYXNzPSJjb2wtNiBjb2wtbGctMyI+CiAgICAgICAgICAgIDxkaXYgY2xhc3M9ImxpY2Vuc2Uta3BpLWNhcmQiPgogICAgICAgICAgICAgICAgPHNtYWxsIGNsYXNzPSJ0ZXh0LW11dGVkIGQtYmxvY2sgZnctYm9sZCI+2YjYtti524zYqiDYp9i52KrYqNin2LEg2YTYp9uM2LPZhtizPC9zbWFsbD4KICAgICAgICAgICAgICAgIDxoNSBjbGFzcz0iZnctYm9sZCBtYi0xIHRleHQtc3VjY2VzcyI+CiAgICAgICAgICAgICAgICAgICAgPD9waHAgZWNobyAhZW1wdHkoJGxpY2Vuc2UtPmlzX2xpZmV0aW1lKSA/ICfZhdin2K/Yp9mF4oCM2KfZhNi52YXYsSAoTGlmZXRpbWUpJyA6ICgkbGljZW5zZS0+cmVtYWluaW5nX2RheXMgLiAnINix2YjYsiDYqNin2YLbjOKAjNmF2KfZhtiv2YcnKTsgPz4KICAgICAgICAgICAgICAgIDwvaDU+CiAgICAgICAgICAgICAgICA8c21hbGwgY2xhc3M9InRleHQtbXV0ZWQiPgogICAgICAgICAgICAgICAgICAgINin2YbZgti22Kc6IDw/cGhwIGVjaG8gIWVtcHR5KCRsaWNlbnNlLT5pc19saWZldGltZSkgPyAn2KjYr9mI2YYg2YXYrdiv2YjYr9uM2Kog2LLZhdin2YbbjCcgOiAoJGxpY2Vuc2UtPmV4cGlyZXNfYXQgPyBkYXRlKCdZL20vZCcsIHN0cnRvdGltZSgkbGljZW5zZS0+ZXhwaXJlc19hdCkpIDogJ9mG2KfZhdit2K/ZiNivJyk7ID8+CiAgICAgICAgICAgICAgICA8L3NtYWxsPgogICAgICAgICAgICA8L2Rpdj4KICAgICAgICA8L2Rpdj4KICAgICAgICA8ZGl2IGNsYXNzPSJjb2wtNiBjb2wtbGctMyI+CiAgICAgICAgICAgIDxkaXYgY2xhc3M9ImxpY2Vuc2Uta3BpLWNhcmQiPgogICAgICAgICAgICAgICAgPHNtYWxsIGNsYXNzPSJ0ZXh0LW11dGVkIGQtYmxvY2sgZnctYm9sZCI+2LPZgtmBINqp2KfYsdio2LHYp9mGINmH2YXYstmF2KfZhjwvc21hbGw+CiAgICAgICAgICAgICAgICA8aDUgY2xhc3M9ImZ3LWJvbGQgbWItMSB0ZXh0LWRhcmsiPgogICAgICAgICAgICAgICAgICAgIDw/cGhwIGVjaG8gKCRsaWNlbnNlLT5tYXhfdXNlcnMgPj0gOTAwKSA/ICfaqdin2LHYqNix2KfZhiDZhtin2YXYrdiv2YjYrycgOiAoJGxpY2Vuc2UtPm1heF91c2VycyAuICcg2qnYp9ix2KjYsScpOyA/PgogICAgICAgICAgICAgICAgPC9oNT4KICAgICAgICAgICAgICAgIDxzbWFsbCBjbGFzcz0idGV4dC1tdXRlZCI+2YXYrNmI2LIg2KfYs9iq2YHYp9iv2Ycg2LTYqNqp2YfigIzYp9uMPC9zbWFsbD4KICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgPC9kaXY+CiAgICAgICAgPGRpdiBjbGFzcz0iY29sLTYgY29sLWxnLTMiPgogICAgICAgICAgICA8ZGl2IGNsYXNzPSJsaWNlbnNlLWtwaS1jYXJkIj4KICAgICAgICAgICAgICAgIDxzbWFsbCBjbGFzcz0idGV4dC1tdXRlZCBkLWJsb2NrIGZ3LWJvbGQiPtiq2LnYr9in2K8g2YXYp9qY2YjZhOKAjNmH2KfbjCDZgdi52KfZhDwvc21hbGw+CiAgICAgICAgICAgICAgICA8aDUgY2xhc3M9ImZ3LWJvbGQgbWItMSB0ZXh0LWluZGlnbyIgc3R5bGU9ImNvbG9yOiM0ZjQ2ZTU7Ij4KICAgICAgICAgICAgICAgICAgICA8P3BocCBlY2hvIGNvdW50KCRsaWNlbnNlLT5tb2R1bGVzID8/IFtdKTsgPz4g2KfYsiA8P3BocCBlY2hvIGNvdW50KCRtb2R1bGVzKTsgPz4g2YXYp9qY2YjZhAogICAgICAgICAgICAgICAgPC9oNT4KICAgICAgICAgICAgICAgIDxzbWFsbCBjbGFzcz0idGV4dC1tdXRlZCI+2YXYp9qY2YjZhOKAjNmH2KfbjCDYqtis2KfYsduMINiu2LHbjNiv2KfYsduM4oCM2LTYr9mHPC9zbWFsbD4KICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgPC9kaXY+CiAgICA8L2Rpdj4KCiAgICA8IS0tIE5hdmlnYXRpb24gVGFicyAtLT4KICAgIDx1bCBjbGFzcz0ibmF2IG5hdi1waWxscyBuYXYtcGlsbHMtbGljZW5zZSBtYi00IGdhcC0yIGJnLXdoaXRlIHAtMiByb3VuZGVkLTQgYm9yZGVyIHNoYWRvdy1zbSIgcm9sZT0idGFibGlzdCI+CiAgICAgICAgPGxpIGNsYXNzPSJuYXYtaXRlbSI+CiAgICAgICAgICAgIDxidXR0b24gY2xhc3M9Im5hdi1saW5rIGFjdGl2ZSIgZGF0YS1icy10b2dnbGU9InBpbGwiIGRhdGEtYnMtdGFyZ2V0PSIjdGFiLXN0YXR1cyIgdHlwZT0iYnV0dG9uIj4KICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXMgZmEtY2hlY2stc2hpZWxkIG1lLTEiPjwvaT4g2YjYtti524zYqiDZhNin24zYs9mG2LMg2Ygg2YHYudin2YTigIzYs9in2LLbjAogICAgICAgICAgICA8L2J1dHRvbj4KICAgICAgICA8L2xpPgogICAgICAgIDxsaSBjbGFzcz0ibmF2LWl0ZW0iPgogICAgICAgICAgICA8YnV0dG9uIGNsYXNzPSJuYXYtbGluayIgZGF0YS1icy10b2dnbGU9InBpbGwiIGRhdGEtYnMtdGFyZ2V0PSIjdGFiLWdlbmVyYXRvciIgdHlwZT0iYnV0dG9uIj4KICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXMgZmEta2V5IG1lLTEiPjwvaT4g2YXZiNmE2K8g2YTYp9uM2LPZhtizINiq2KzYp9ix24wg2Ygg2YLZgdmEINiz2K7YquKAjNin2YHYstin2LHbjCAo2YjbjNqY2Ycg2YHYsdmI2LTZhtiv2YcpCiAgICAgICAgICAgIDwvYnV0dG9uPgogICAgICAgIDwvbGk+CiAgICAgICAgPGxpIGNsYXNzPSJuYXYtaXRlbSI+CiAgICAgICAgICAgIDxidXR0b24gY2xhc3M9Im5hdi1saW5rIiBkYXRhLWJzLXRvZ2dsZT0icGlsbCIgZGF0YS1icy10YXJnZXQ9IiN0YWItcGxhbnMiIHR5cGU9ImJ1dHRvbiI+CiAgICAgICAgICAgICAgICA8aSBjbGFzcz0iZmFzIGZhLWxheWVyLWdyb3VwIG1lLTEiPjwvaT4g2qnYp9iq2KfZhNmI2q8g2b7ZhNmG4oCM2YfYpyDZiCDYqti52LHZgdmHINmB2LHZiNi0CiAgICAgICAgICAgIDwvYnV0dG9uPgogICAgICAgIDwvbGk+CiAgICA8L3VsPgoKICAgIDwhLS0gVGFiIENvbnRlbnRzIC0tPgogICAgPGRpdiBjbGFzcz0idGFiLWNvbnRlbnQiPgogICAgICAgIDwhLS0gVEFCIDE6INmI2LbYuduM2Kog2YTYp9uM2LPZhtizINmIINmB2LnYp9mE4oCM2LPYp9iy24wg2LPYsduM2LkgLS0+CiAgICAgICAgPGRpdiBjbGFzcz0idGFiLXBhbmUgZmFkZSBzaG93IGFjdGl2ZSIgaWQ9InRhYi1zdGF0dXMiIHJvbGU9InRhYnBhbmVsIj4KICAgICAgICAgICAgPGRpdiBjbGFzcz0icm93IGctNCI+CiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJjb2wtbGctOCI+CiAgICAgICAgICAgICAgICAgICAgPCEtLSDZhdin2KrYsduM2LMg2YXYp9qY2YjZhOKAjNmH2KfbjCDZgdi52KfZhCDYr9ixINin24zZhiDZhNin24zYs9mG2LMgLS0+CiAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iY2FyZCBib3JkZXItMCByb3VuZGVkLTQgc2hhZG93LXNtIHAtNCBtYi00Ij4KICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iZC1mbGV4IGp1c3RpZnktY29udGVudC1iZXR3ZWVuIGFsaWduLWl0ZW1zLWNlbnRlciBtYi0zIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxoNSBjbGFzcz0iZnctYm9sZCBtYi0wIHRleHQtZGFyayI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGkgY2xhc3M9ImZhcyBmYS1jdWJlcyB0ZXh0LXByaW1hcnkgbWUtMiI+PC9pPtmI2LbYuduM2Kog2K/Ys9iq2LHYs9uMINio2Ycg2YXYp9qY2YjZhOKAjNmH2KfbjCDYs9in2YXYp9mG2YcKICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvaDU+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8c3BhbiBjbGFzcz0iYmFkZ2UgYmctaW5mbyBiZy1vcGFjaXR5LTEwIHRleHQtaW5mbyBib3JkZXIgYm9yZGVyLWluZm8gYm9yZGVyLW9wYWNpdHktMjUgcHgtMyBweS0xIHJvdW5kZWQtcGlsbCI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAg2KfZhtiq2K7Yp9ioINmF2KfamNmI2YQg2KjZhyDZhdin2pjZiNmECiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L3NwYW4+CiAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICAgICAgICAgICAgICA8cCBjbGFzcz0ic21hbGwgdGV4dC1tdXRlZCBtYi00Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICAgINmF2KfamNmI2YTigIzZh9in24wg2LPYqNiyINix2YbaryDYr9ixINmE2KfbjNiz2YbYsyDZgdi52KfZhCDYtNmF2Kcg2YLYsdin2LEg2K/Yp9ix2YbYryDZiCDZgtin2KjZhCDYp9iz2KrZgdin2K/ZhyDZh9iz2KrZhtivLiDZhdin2pjZiNmE4oCM2YfYp9uMINiu2Kfaqdiz2KrYsduMINmC2YHZhCDYtNiv2YfigIzYp9mG2K8g2Ygg2K/YsSDZvtmE2YbigIzZh9in24wg2KjYp9mE2KfYqtixINuM2Kcg2KjZhyDYtdmI2LHYqiDYqtqp4oCM2YXYp9qY2YjZhCDZgtin2KjZhCDZgdi52KfZhOKAjNiz2KfYstuMINmF24zigIzYqNin2LTZhtivLgogICAgICAgICAgICAgICAgICAgICAgICA8L3A+CgogICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJyb3cgZy0zIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGZvcmVhY2ggKCRtb2R1bGVzIGFzICRtS2V5ID0+ICRtSW5mbyk6IAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICRpc0VuYWJsZWQgPSBpbl9hcnJheSgkbUtleSwgJGxpY2Vuc2UtPm1vZHVsZXMgPz8gW10pOwogICAgICAgICAgICAgICAgICAgICAgICAgICAgPz4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJjb2wtbWQtNiBjb2wtbGctNCI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9InAtMyByb3VuZGVkLTMgYm9yZGVyIGgtMTAwIDw/cGhwIGVjaG8gJGlzRW5hYmxlZCA/ICdiZy13aGl0ZSBib3JkZXItc3VjY2VzcyBib3JkZXItb3BhY2l0eS01MCcgOiAnYmctbGlnaHQgYm9yZGVyLWRhc2hlZCc7ID8+Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImQtZmxleCBhbGlnbi1pdGVtcy1jZW50ZXIganVzdGlmeS1jb250ZW50LWJldHdlZW4gbWItMiI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iZC1mbGV4IGFsaWduLWl0ZW1zLWNlbnRlciBnYXAtMiI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9InJvdW5kZWQtMiBwLTIgPD9waHAgZWNobyAkaXNFbmFibGVkID8gJ2JnLXN1Y2Nlc3MgYmctb3BhY2l0eS0xMCB0ZXh0LXN1Y2Nlc3MnIDogJ2JnLXNlY29uZGFyeSBiZy1vcGFjaXR5LTEwIHRleHQtbXV0ZWQnOyA/PiI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8aSBjbGFzcz0iPD9waHAgZWNobyAkbUluZm9bJ2ljb24nXTsgPz4iPjwvaT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxzcGFuIGNsYXNzPSJmdy1ib2xkIHNtYWxsIHRleHQtZGFyayI+PD9waHAgZWNobyBodG1sc3BlY2lhbGNoYXJzKCRtSW5mb1snbmFtZSddKTsgPz48L3NwYW4+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPD9waHAgaWYgKCRpc0VuYWJsZWQpOiA/PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8aSBjbGFzcz0iZmFzIGZhLWNoZWNrLWNpcmNsZSB0ZXh0LXN1Y2Nlc3MiIHRpdGxlPSLZgdi52KfZhCDYr9ixINmE2KfbjNiz2YbYsyI+PC9pPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGVsc2U6ID8+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXMgZmEtbG9jayB0ZXh0LW11dGVkIiB0aXRsZT0i2YLZgdmEINin2LPYqiI+PC9pPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGVuZGlmOyA/PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cCBjbGFzcz0idGV4dC1tdXRlZCBzbWFsbCBtYi0wIiBzdHlsZT0iZm9udC1zaXplOjAuNzVyZW07IGxpbmUtaGVpZ2h0OjEuNDsiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGVjaG8gaHRtbHNwZWNpYWxjaGFycygkbUluZm9bJ2Rlc2MnXSk7ID8+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L3A+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPD9waHAgZW5kZm9yZWFjaDsgPz4KICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICA8L2Rpdj4KCiAgICAgICAgICAgICAgICA8IS0tINmB2LnYp9mE4oCM2LPYp9iy24wg2YTYp9uM2LPZhtizINis2K/bjNivIC0tPgogICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iY29sLWxnLTQiPgogICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImNhcmQgYm9yZGVyLTAgcm91bmRlZC00IHNoYWRvdy1zbSBwLTQgaC0xMDAgYmctd2hpdGUiPgogICAgICAgICAgICAgICAgICAgICAgICA8aDUgY2xhc3M9ImZ3LWJvbGQgbWItMyB0ZXh0LWRhcmsiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPGkgY2xhc3M9ImZhcyBmYS1maWxlLXNpZ25hdHVyZSB0ZXh0LXN1Y2Nlc3MgbWUtMiI+PC9pPtmB2LnYp9mE4oCM2LPYp9iy24wg2YTYp9uM2LPZhtizINis2K/bjNivCiAgICAgICAgICAgICAgICAgICAgICAgIDwvaDU+CiAgICAgICAgICAgICAgICAgICAgICAgIDxwIGNsYXNzPSJzbWFsbCB0ZXh0LW11dGVkIG1iLTMiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAg2K/YsSDYtdmI2LHYqtuMINqp2Ycg2qnYryDZhNin24zYs9mG2LMg2KzYr9uM2K8g24zYpyDZgdin24zZhCDZhNin24zYs9mG2LMg2KfYrtiq2LXYp9i124wgKDxjb2RlPi5saWM8L2NvZGU+KSDYp9iyINi02LHaqdiqINmH2YXYp9qp2KfYsSDYr9ix24zYp9mB2Kog2YbZhdmI2K/Zh+KAjNin24zYr9iMINin2LIg2KfbjNmGINio2K7YtCDYp9mC2K/Yp9mFINmB2LHZhdin24zbjNivOgogICAgICAgICAgICAgICAgICAgICAgICA8L3A+CgogICAgICAgICAgICAgICAgICAgICAgICA8Zm9ybSBtZXRob2Q9IlBPU1QiIGFjdGlvbj0iPD9waHAgZWNobyBBUFBfVVJMOyA/Pi9pbmRleC5waHA/cGFnZT1hZG1pbiZhY3Rpb249YWN0aXZhdGVfbGljZW5zZSIgZW5jdHlwZT0ibXVsdGlwYXJ0L2Zvcm0tZGF0YSI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJtYi0zIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8bGFiZWwgY2xhc3M9ImZvcm0tbGFiZWwgc21hbGwgZnctYm9sZCB0ZXh0LW11dGVkIj7YqNin2LHar9iw2KfYsduMINmB2KfbjNmEINmE2KfbjNiz2YbYsyAoTGljZW5zZSBGaWxlKTo8L2xhYmVsPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxpbnB1dCB0eXBlPSJmaWxlIiBuYW1lPSJsaWNlbnNlX2ZpbGUiIGNsYXNzPSJmb3JtLWNvbnRyb2wgZm9ybS1jb250cm9sLXNtIiBhY2NlcHQ9Ii5saWMsLnR4dCI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj4KCiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJ0ZXh0LWNlbnRlciB0ZXh0LW11dGVkIHNtYWxsIG15LTIiPtuM2Kcg2YjYp9ix2K8g2qnYsdiv2YYg2YXYqtmGINmE2KfbjNiz2YbYszo8L2Rpdj4KCiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJtYi0zIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8bGFiZWwgY2xhc3M9ImZvcm0tbGFiZWwgc21hbGwgZnctYm9sZCB0ZXh0LW11dGVkIj7Ysdi02KrZhyDZhNin24zYs9mG2LMg2LHZhdiy2Ybar9in2LHbjCDYtNiv2YcgKEhLTElDKTo8L2xhYmVsPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDx0ZXh0YXJlYSBuYW1lPSJsaWNlbnNlX2tleSIgcm93cz0iNiIgY2xhc3M9ImZvcm0tY29udHJvbCBmb3JtLWNvbnRyb2wtc20gZm9udC1tb25vc3BhY2UiIHBsYWNlaG9sZGVyPSJIS0xJQy1leUpzYVdObGJuTmxaU0k2Li4uIiBzdHlsZT0iZGlyZWN0aW9uOmx0cjsgZm9udC1zaXplOjAuNzVyZW07Ij48L3RleHRhcmVhPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPSJzdWJtaXQiIGNsYXNzPSJidG4gYnRuLXByaW1hcnkgdy0xMDAgcHktMi41IHJvdW5kZWQtMyBmdy1ib2xkIHNoYWRvdy1zbSI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGkgY2xhc3M9ImZhcyBmYS11bmxvY2stYWx0IG1lLTEiPjwvaT4g2KfYudiq2KjYp9ix2LPZhtis24wg2Ygg2YHYudin2YTigIzYs9in2LLbjCDYotmG24wKICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvYnV0dG9uPgogICAgICAgICAgICAgICAgICAgICAgICA8L2Zvcm0+CgogICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJtdC00IHAtMyByb3VuZGVkLTMgYmctbGlnaHQgYm9yZGVyIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9InNtYWxsIGZ3LWJvbGQgdGV4dC1kYXJrIG1iLTEiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXMgZmEtc2hpZWxkLWFsdCB0ZXh0LXByaW1hcnkgbWUtMSI+PC9pPiDYp9mF2YbbjNiqINmIINmC2YHZhCDYs9iu2KrigIzYp9mB2LLYp9ix24w6CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwIGNsYXNzPSJzbWFsbCB0ZXh0LW11dGVkIG1iLTAiIHN0eWxlPSJmb250LXNpemU6MC43NXJlbTsiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgINmE2KfbjNiz2YbYs+KAjNmH2KfbjCDZh9mF2Kfaqdin2LEg2KjYpyDYp9mF2LbYp9uMINiv24zYrNuM2KrYp9mEINmG2KfZhdiq2YLYp9ix2YYgUlNBLTIwNDgg2LHZhdiy2Ybar9in2LHbjCDYtNiv2Ycg2Ygg2KjZhyDYtdmI2LHYqiDYotmB2YTYp9uM2YYg2KjYr9mI2YYg2YbbjNin2LIg2KjZhyDYp9iq2LXYp9mEINiv2KfYptmFINin24zZhtiq2LHZhtiqINin2LnYqtio2KfYsdiz2YbYrNuMINmF24zigIzYtNmI2YbYry4g2YfYsSDZhNin24zYs9mG2LMg2YHZgti3INio2LEg2LHZiNuMINiz2K7YquKAjNin2YHYstin2LEg2KrYp9uM24zYr9i02K/ZhyDZgdi52KfZhCDYrtmI2KfZh9ivINmF2KfZhtivLgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9wPgogICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICA8L2Rpdj4KICAgICAgICA8L2Rpdj4KCiAgICAgICAgPCEtLSBUQUIgMjog2YXZiNmE2K8g2YTYp9uM2LPZhtizINiq2KzYp9ix24wg2Ygg2YLZgdmEINiz2K7YquKAjNin2YHYstin2LHbjCAo2YjbjNqY2Ycg2YXYr9uM2LEg2qnZhCAvINmB2LHZiNi02YbYr9mHKSAtLT4KICAgICAgICA8ZGl2IGNsYXNzPSJ0YWItcGFuZSBmYWRlIiBpZD0idGFiLWdlbmVyYXRvciIgcm9sZT0idGFicGFuZWwiPgogICAgICAgICAgICA8ZGl2IGNsYXNzPSJyb3cgZy00Ij4KICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImNvbC1sZy03Ij4KICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJjYXJkIGJvcmRlci0wIHJvdW5kZWQtNCBzaGFkb3ctc20gcC00Ij4KICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iZC1mbGV4IGp1c3RpZnktY29udGVudC1iZXR3ZWVuIGFsaWduLWl0ZW1zLWNlbnRlciBtYi0zIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxoNSBjbGFzcz0iZnctYm9sZCBtYi0wIHRleHQtZGFyayI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGkgY2xhc3M9ImZhcyBmYS1jb2dzIHRleHQtcHJpbWFyeSBtZS0yIj48L2k+2YXZiNmE2K8g2Ygg2LXYp9iv2LHaqdmG2YbYr9mHINmE2KfbjNiz2YbYsyDYqtis2KfYsduMIChMaWNlbnNlIEdlbmVyYXRvcikKICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvaDU+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8c3BhbiBjbGFzcz0iYmFkZ2UgYmctd2FybmluZyBiZy1vcGFjaXR5LTEwIHRleHQtZGFyayBib3JkZXIgYm9yZGVyLXdhcm5pbmcgYm9yZGVyLW9wYWNpdHktMjUgcHgtMi41IHB5LTEgcm91bmRlZC1waWxsIHNtYWxsIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICDYp9io2LLYp9ixINiz2KfYstmG2K/ZhyAvINmF2K/bjNixINin2LHYtNivCiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L3NwYW4+CiAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICAgICAgICAgICAgICA8cCBjbGFzcz0ic21hbGwgdGV4dC1tdXRlZCBtYi00Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICAgINio2Kcg2KfYs9iq2YHYp9iv2Ycg2KfYsiDYp9uM2YYg2YHYsdmFINmF24zigIzYqtmI2KfZhtuM2K8g2KjYsdin24wg2K7YsduM2K/Yp9ix2KfZhtiMINi02LHaqdiq4oCM2YfYpyDZiCDaqdin2LHYrtin2YbYrNin2KrYjCDZhNin24zYs9mG2LPigIzZh9in24wg2KrYrNin2LHbjCDZhdi52KrYqNixINio2Kcg2YLZgdmEINiz2K7YquKAjNin2YHYstin2LHbjCDYp9iu2KrYtdin2LXbjCDZiCDZhdin2pjZiNmE4oCM2YfYp9uMINin2YbYqtiu2KfYqNuMINi12KfYr9ixINmG2YXYp9uM24zYry4KICAgICAgICAgICAgICAgICAgICAgICAgPC9wPgoKICAgICAgICAgICAgICAgICAgICAgICAgPGZvcm0gaWQ9ImxpY2Vuc2VHZW5Gb3JtIiBvbnN1Ym1pdD0iZ2VuZXJhdGVDb21tZXJjaWFsTGljZW5zZShldmVudCkiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0icm93IGctMyI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iY29sLW1kLTYiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8bGFiZWwgY2xhc3M9ImZvcm0tbGFiZWwgc21hbGwgZnctYm9sZCB0ZXh0LW11dGVkIj7Zhtin2YUg2K7YsduM2K/Yp9ixIC8g2LTYsdqp2Kog2YXYtNiq2LHbjDo8L2xhYmVsPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8aW5wdXQgdHlwZT0idGV4dCIgaWQ9Imdlbl9saWNlbnNlZSIgY2xhc3M9ImZvcm0tY29udHJvbCBmb3JtLWNvbnRyb2wtc20iIHJlcXVpcmVkIHBsYWNlaG9sZGVyPSLZhdir2KfZhDog2LTYsdqp2Kog2LXZhtin24zYuSDZgdmI2YTYp9ivINiq2KjYsduM2LIiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImNvbC1tZC02Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGxhYmVsIGNsYXNzPSJmb3JtLWxhYmVsIHNtYWxsIGZ3LWJvbGQgdGV4dC1tdXRlZCI+2qnYryDZgtmB2YQg2LPYrtiq4oCM2KfZgdiy2KfYsduMINmF2LTYqtix24wgKE1hY2hpbmUgRmluZ2VycHJpbnQpOjwvbGFiZWw+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImlucHV0LWdyb3VwIGlucHV0LWdyb3VwLXNtIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxpbnB1dCB0eXBlPSJ0ZXh0IiBpZD0iZ2VuX2hhcmR3YXJlX2lkIiBjbGFzcz0iZm9ybS1jb250cm9sIGZvbnQtbW9ub3NwYWNlIiBwbGFjZWhvbGRlcj0iSEstWFhYWC1YWFhYLVhYWFgtWFhYWCIgdmFsdWU9Ijw/cGhwIGVjaG8gaHRtbHNwZWNpYWxjaGFycygkbWFjaGluZUZpbmdlcnByaW50KTsgPz4iPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiBjbGFzcz0iYnRuIGJ0bi1vdXRsaW5lLXNlY29uZGFyeSIgdHlwZT0iYnV0dG9uIiBvbmNsaWNrPSJkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZ2VuX2hhcmR3YXJlX2lkJykudmFsdWU9J0FOWSciIHRpdGxlPSLYqNiv2YjZhiDZgtmB2YQg2LPYrtiq4oCM2KfZgdiy2KfYsduMICjZgtin2KjZhCDZhti12Kgg2LHZiNuMINmH2LEg2LPYsdmI2LEpIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICDYotiy2KfYryAoQU5ZKQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9idXR0b24+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8c21hbGwgY2xhc3M9InRleHQtbXV0ZWQiIHN0eWxlPSJmb250LXNpemU6MC43MnJlbTsiPtqp2K8g2K/YsduM2KfZgdiq24wg2KfYsiDYs9ix2YjYsSDZhdi02KrYsduMINis2YfYqiDYrNmE2Yjar9uM2LHbjCDYp9iyINqp2b7bjCDZhtix2YXigIzYp9mB2LLYp9ixPC9zbWFsbD4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj4KCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iY29sLW1kLTYiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8bGFiZWwgY2xhc3M9ImZvcm0tbGFiZWwgc21hbGwgZnctYm9sZCB0ZXh0LW11dGVkIj7ZvtmE2YYg2KrYrNin2LHbjCDZhdio2YbYpzo8L2xhYmVsPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8c2VsZWN0IGlkPSJnZW5fcGxhbl9rZXkiIGNsYXNzPSJmb3JtLXNlbGVjdCBmb3JtLXNlbGVjdC1zbSIgb25jaGFuZ2U9Im9uUGxhblNlbGVjdGlvbkNoYW5nZSgpIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGZvcmVhY2ggKCRwbGFucyBhcyAkcEtleSA9PiAkcEluZm8pOiA/PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxvcHRpb24gdmFsdWU9Ijw/cGhwIGVjaG8gJHBLZXk7ID8+IiA8P3BocCBlY2hvICgkcEtleSA9PT0gJ2NvbW1lcmNpYWwnKSA/ICdzZWxlY3RlZCcgOiAnJzsgPz4+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGVjaG8gaHRtbHNwZWNpYWxjaGFycygkcEluZm9bJ25hbWUnXSk7ID8+ICg8P3BocCBlY2hvIG51bWJlcl9mb3JtYXQoJHBJbmZvWydwcmljZV90b21hbiddKTsgPz4g2KrZiNmF2KfZhikKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L29wdGlvbj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGVuZGZvcmVhY2g7ID8+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvc2VsZWN0PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PgoKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJjb2wtbWQtMyI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxsYWJlbCBjbGFzcz0iZm9ybS1sYWJlbCBzbWFsbCBmdy1ib2xkIHRleHQtbXV0ZWQiPtmF2K/YqiDYstmF2KfZhiDYp9i52KrYqNin2LE6PC9sYWJlbD4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHNlbGVjdCBpZD0iZ2VuX3ZhbGlkaXR5IiBjbGFzcz0iZm9ybS1zZWxlY3QgZm9ybS1zZWxlY3Qtc20iPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPG9wdGlvbiB2YWx1ZT0iMzAiPtuxINmF2KfZh9mHICjYotiy2YXYp9uM2LTbjCk8L29wdGlvbj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxvcHRpb24gdmFsdWU9IjkwIj7bsyDZhdin2YfZhzwvb3B0aW9uPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPG9wdGlvbiB2YWx1ZT0iMTgwIj7btiDZhdin2YfZhzwvb3B0aW9uPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPG9wdGlvbiB2YWx1ZT0iMzY1IiBzZWxlY3RlZD7bsSDYs9in2YTZhyAo2KfYs9iq2KfZhtiv2KfYsdivKTwvb3B0aW9uPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPG9wdGlvbiB2YWx1ZT0iNzMwIj7bsiDYs9in2YTZhzwvb3B0aW9uPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPG9wdGlvbiB2YWx1ZT0iMTgyNSI+27Ug2LPYp9mE2Yc8L29wdGlvbj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxvcHRpb24gdmFsdWU9ImxpZmV0aW1lIj7Zhdin2K/Yp9mF4oCM2KfZhNi52YXYsSAoTGlmZXRpbWUpPC9vcHRpb24+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvc2VsZWN0PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PgoKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJjb2wtbWQtMyI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxsYWJlbCBjbGFzcz0iZm9ybS1sYWJlbCBzbWFsbCBmdy1ib2xkIHRleHQtbXV0ZWQiPtiz2YLZgSDaqdin2LHYqNix2KfZhiDZh9mF2LLZhdin2YY6PC9sYWJlbD4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGlucHV0IHR5cGU9Im51bWJlciIgaWQ9Imdlbl91c2VycyIgY2xhc3M9ImZvcm0tY29udHJvbCBmb3JtLWNvbnRyb2wtc20iIHZhbHVlPSI1IiBtaW49IjEiIG1heD0iOTk5Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj4KCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPCEtLSDYp9mG2KrYrtin2Kgg2YXYp9qY2YjZhCDYqNmHINmF2KfamNmI2YQg2K/ZhNiu2YjYp9mHIC0tPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImNvbC0xMiBtdC0zIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iZC1mbGV4IGp1c3RpZnktY29udGVudC1iZXR3ZWVuIGFsaWduLWl0ZW1zLWNlbnRlciBtYi0yIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxsYWJlbCBjbGFzcz0iZm9ybS1sYWJlbCBzbWFsbCBmdy1ib2xkIHRleHQtbXV0ZWQgbWItMCI+2KfZhtiq2K7Yp9ioINmF2KfamNmI2YQg2KjZhyDZhdin2pjZiNmEIChNb2R1bGFyIENoZWNrbGlzdCk6PC9sYWJlbD4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImQtZmxleCBnYXAtMiI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPSJidXR0b24iIGNsYXNzPSJidG4gYnRuLXhzIGJ0bi1vdXRsaW5lLXByaW1hcnkgcHktMCBweC0yIiBzdHlsZT0iZm9udC1zaXplOjAuNzJyZW07IiBvbmNsaWNrPSJzZWxlY3RBbGxNb2R1bGVzKHRydWUpIj7Yp9mG2KrYrtin2Kgg2YfZhdmHPC9idXR0b24+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPSJidXR0b24iIGNsYXNzPSJidG4gYnRuLXhzIGJ0bi1vdXRsaW5lLXNlY29uZGFyeSBweS0wIHB4LTIiIHN0eWxlPSJmb250LXNpemU6MC43MnJlbTsiIG9uY2xpY2s9InNlbGVjdEFsbE1vZHVsZXMoZmFsc2UpIj7ZhNi62Ygg2YfZhdmHPC9idXR0b24+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9InAtMyByb3VuZGVkLTMgYm9yZGVyIGJnLWxpZ2h0Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9InJvdyBnLTIiIGlkPSJtb2R1bGVzX2NoZWNrbGlzdF9jb250YWluZXIiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGZvcmVhY2ggKCRtb2R1bGVzIGFzICRtS2V5ID0+ICRtSW5mbyk6ID8+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImNvbC02IGNvbC1tZC00Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImZvcm0tY2hlY2siPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxpbnB1dCBjbGFzcz0iZm9ybS1jaGVjay1pbnB1dCBtb2R1bGUtY2hlY2tib3giIHR5cGU9ImNoZWNrYm94IiB2YWx1ZT0iPD9waHAgZWNobyAkbUtleTsgPz4iIGlkPSJtb2RfPD9waHAgZWNobyAkbUtleTsgPz4iIGRhdGEtcHJpY2U9Ijw/cGhwIGVjaG8gJG1JbmZvWydwcmljZV9hZGRvbiddOyA/PiI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGxhYmVsIGNsYXNzPSJmb3JtLWNoZWNrLWxhYmVsIHNtYWxsIGZ3LWJvbGQiIGZvcj0ibW9kXzw/cGhwIGVjaG8gJG1LZXk7ID8+Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGkgY2xhc3M9Ijw/cGhwIGVjaG8gJG1JbmZvWydpY29uJ107ID8+IG1lLTEgdGV4dC1wcmltYXJ5Ij48L2k+PD9waHAgZWNobyBodG1sc3BlY2lhbGNoYXJzKCRtSW5mb1snbmFtZSddKTsgPz4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2xhYmVsPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGVuZGZvcmVhY2g7ID8+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImNvbC0xMiBtdC00IHRleHQtZW5kIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPSJzdWJtaXQiIGNsYXNzPSJidG4gYnRuLXByaW1hcnkgcHgtNCBweS0yIHJvdW5kZWQtMyBmdy1ib2xkIHNoYWRvdy1zbSIgaWQ9ImJ0bi1nZW5lcmF0ZS1saWNlbnNlIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXMgZmEtc3RhbXAgbWUtMSI+PC9pPiDYtdiv2YjYsSDZhNin24zYs9mG2LMg2LHZhdiy2Ybar9in2LHbjCDYtNiv2YcgUlNBCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvYnV0dG9uPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICAgICAgICAgIDwvZm9ybT4KICAgICAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgICAgIDwvZGl2PgoKICAgICAgICAgICAgICAgIDwhLS0g2K7YsdmI2KzbjCDZhNin24zYs9mG2LMg2KrZiNmE24zYryDYtNiv2YcgLS0+CiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJjb2wtbGctNSI+CiAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iY2FyZCBib3JkZXItMCByb3VuZGVkLTQgc2hhZG93LXNtIHAtNCBoLTEwMCBiZy13aGl0ZSI+CiAgICAgICAgICAgICAgICAgICAgICAgIDxoNiBjbGFzcz0iZnctYm9sZCBtYi0zIHRleHQtZGFyayI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8aSBjbGFzcz0iZmFzIGZhLXRlcm1pbmFsIHRleHQtcHJpbWFyeSBtZS0yIj48L2k+2K7YsdmI2KzbjCDZhNin24zYs9mG2LMg2KrYrNin2LHbjCDYtdin2K/YsSDYtNiv2YcKICAgICAgICAgICAgICAgICAgICAgICAgPC9oNj4KCiAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgaWQ9Imdlbl9vdXRwdXRfcGxhY2Vob2xkZXIiIGNsYXNzPSJ0ZXh0LWNlbnRlciBweS01IHRleHQtbXV0ZWQiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPGkgY2xhc3M9ImZhcyBmYS1jZXJ0aWZpY2F0ZSBmYS0zeCBtYi0zIHRleHQtc2Vjb25kYXJ5IG9wYWNpdHktNTAiPjwvaT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxwIGNsYXNzPSJzbWFsbCBtYi0wIj7ZvtizINin2LIg2KraqdmF24zZhCDZgdix2YUg2LHZiNio2LHZiCDZiCDaqdmE24zaqSDYqNixINix2YjbjCDYtdiv2YjYsSDZhNin24zYs9mG2LPYjCDZhdi02K7Ytdin2Kog2LHZhdiy2Ybar9in2LHbjCDYtNiv2Ycg2Ygg2YTbjNmG2qkg2K/Yp9mG2YTZiNivINmB2KfbjNmEINmE2KfbjNiz2YbYsyDYr9ixINin24zZhiDZgtiz2YXYqiDZhtmF2KfbjNin2YYg2K7ZiNin2YfYryDYtNivLjwvcD4KICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CgogICAgICAgICAgICAgICAgICAgICAgICA8ZGl2IGlkPSJnZW5fb3V0cHV0X3Jlc3VsdCIgc3R5bGU9ImRpc3BsYXk6bm9uZTsiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iYWxlcnQgYWxlcnQtc3VjY2VzcyByb3VuZGVkLTMgcC0zIG1iLTMiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9ImQtZmxleCBhbGlnbi1pdGVtcy1jZW50ZXIgZ2FwLTIgZnctYm9sZCBzbWFsbCB0ZXh0LXN1Y2Nlc3MgbWItMSI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXMgZmEtY2hlY2stY2lyY2xlIj48L2k+INmE2KfbjNiz2YbYsyDYsdiz2YXbjCDYqNinINmF2YjZgdmC24zYqiDYp9mF2LbYpyDZiCDYtdin2K/YsSDYtNivIQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxzcGFuIGNsYXNzPSJzbWFsbCB0ZXh0LW11dGVkIGQtYmxvY2siIGlkPSJnZW5fcmVzX2luZm8iPjwvc3Bhbj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvZGl2PgoKICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxsYWJlbCBjbGFzcz0iZm9ybS1sYWJlbCBzbWFsbCBmdy1ib2xkIHRleHQtbXV0ZWQiPtqp2K8g2YTYp9uM2LPZhtizICjYrNmH2Kog2KfYsdiz2KfZhCDYqNmHINmF2LTYqtix24wpOjwvbGFiZWw+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8dGV4dGFyZWEgaWQ9Imdlbl9yZXNfa2V5IiByb3dzPSI2IiBjbGFzcz0iZm9ybS1jb250cm9sIGZvcm0tY29udHJvbC1zbSBmb250LW1vbm9zcGFjZSBtYi0zIiByZWFkb25seSBzdHlsZT0iZGlyZWN0aW9uOmx0cjsgZm9udC1zaXplOjAuNzVyZW07IGJhY2tncm91bmQ6I2Y4ZmFmYzsiPjwvdGV4dGFyZWE+CgogICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iZC1mbGV4IGdhcC0yIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8YnV0dG9uIHR5cGU9ImJ1dHRvbiIgY2xhc3M9ImJ0biBidG4tb3V0bGluZS1wcmltYXJ5IGJ0bi1zbSBmbGV4LWZpbGwgcm91bmRlZC0zIiBvbmNsaWNrPSJjb3B5R2VuZXJhdGVkTGljZW5zZSgpIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGkgY2xhc3M9ImZhciBmYS1jb3B5IG1lLTEiPjwvaT4g2qnZvtuMINqp2K8g2YTYp9uM2LPZhtizCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9idXR0b24+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPSJidXR0b24iIGNsYXNzPSJidG4gYnRuLXByaW1hcnkgYnRuLXNtIGZsZXgtZmlsbCByb3VuZGVkLTMiIG9uY2xpY2s9ImRvd25sb2FkTGljZW5zZUZpbGUoKSI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXMgZmEtZG93bmxvYWQgbWUtMSI+PC9pPiDYr9in2YbZhNmI2K8g2YHYp9uM2YQgKC5saWMpCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9idXR0b24+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgPC9kaXY+CgogICAgICAgIDwhLS0gVEFCIDM6INqp2KfYqtin2YTZiNqvINm+2YTZhuKAjNmH2Kcg2Ygg2KrYudix2YHZhyDZgdix2YjYtCAtLT4KICAgICAgICA8ZGl2IGNsYXNzPSJ0YWItcGFuZSBmYWRlIiBpZD0idGFiLXBsYW5zIiByb2xlPSJ0YWJwYW5lbCI+CiAgICAgICAgICAgIDxkaXYgY2xhc3M9InRleHQtY2VudGVyIG1heC13LTcwMCBteC1hdXRvIG1iLTUiPgogICAgICAgICAgICAgICAgPHNwYW4gY2xhc3M9ImJhZGdlIGJnLXByaW1hcnkgYmctb3BhY2l0eS0xMCB0ZXh0LXByaW1hcnkgYm9yZGVyIGJvcmRlci1wcmltYXJ5IGJvcmRlci1vcGFjaXR5LTI1IHB4LTMgcHktMSByb3VuZGVkLXBpbGwgZnctYm9sZCBtYi0yIj4KICAgICAgICAgICAgICAgICAgICDYrNiv2YjZhCDYqti52LHZgdmHINmB2LHZiNi0INiq2KzYp9ix24wKICAgICAgICAgICAgICAgIDwvc3Bhbj4KICAgICAgICAgICAgICAgIDxoNCBjbGFzcz0iZnctYm9sZCB0ZXh0LWRhcmsgbWItMiI+2b7ZhNmG4oCM2YfYp9uMINiq2KzYp9ix24wg2Ygg2KjYs9iq2YfigIzZh9in24wg2YbYsdmF4oCM2KfZgdiy2KfYsduMINmH2YXYp9qp2KfYsSBFUlA8L2g0PgogICAgICAgICAgICAgICAgPHAgY2xhc3M9InRleHQtbXV0ZWQgc21hbGwiPgogICAgICAgICAgICAgICAgICAgINm+2qnbjNis4oCM2YfYp9uMINin2LPYqtin2YbYr9in2LHYryDYqNinINiq2YjYrNmHINio2Ycg2YXZgtuM2KfYsyDaqdiz2KjigIzZiNqp2KfYsdiMINiq2LnYr9in2K8g2qnYp9ix2KjYsdin2YYg2Ygg2YbbjNin2LLZh9in24wg2YXYp9mE24wg2Ygg2LXZhti52KrbjCDYotmF2KfYr9mH4oCM2LPYp9iy24wg2LTYr9mH4oCM2KfZhtivLgogICAgICAgICAgICAgICAgPC9wPgogICAgICAgICAgICA8L2Rpdj4KCiAgICAgICAgICAgIDxkaXYgY2xhc3M9InJvdyBnLTQgbWItNCI+CiAgICAgICAgICAgICAgICA8P3BocCBmb3JlYWNoICgkcGxhbnMgYXMgJHBLZXkgPT4gJHApOiAKICAgICAgICAgICAgICAgICAgICAkaXNDdXJyZW50ID0gKCRsaWNlbnNlICYmICRsaWNlbnNlLT5wbGFuX2tleSA9PT0gJHBLZXkpOwogICAgICAgICAgICAgICAgPz4KICAgICAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJjb2wtbWQtNiBjb2wteGwtMyI+CiAgICAgICAgICAgICAgICAgICAgICAgIDxkaXYgY2xhc3M9InBsYW4tZmVhdHVyZS1jYXJkIHAtNCBiZy13aGl0ZSA8P3BocCBlY2hvICRpc0N1cnJlbnQgPyAnYWN0aXZlLXBsYW4nIDogJyc7ID8+Ij4KICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxkaXY+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iZC1mbGV4IGp1c3RpZnktY29udGVudC1iZXR3ZWVuIGFsaWduLWl0ZW1zLWNlbnRlciBtYi0zIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHNwYW4gY2xhc3M9ImJhZGdlIHJvdW5kZWQtcGlsbCBweC0zIHB5LTEgZnctYm9sZCIgc3R5bGU9ImJhY2tncm91bmQ6PD9waHAgZWNobyAkcFsnY29sb3InXTsgPz4yMDsgY29sb3I6PD9waHAgZWNobyAkcFsnY29sb3InXTsgPz47IGJvcmRlcjoxcHggc29saWQgPD9waHAgZWNobyAkcFsnY29sb3InXTsgPz40MDsiPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPD9waHAgZWNobyBodG1sc3BlY2lhbGNoYXJzKCRwWydiYWRnZSddKTsgPz4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9zcGFuPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8P3BocCBpZiAoJGlzQ3VycmVudCk6ID8+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8c3BhbiBjbGFzcz0iYmFkZ2UgYmctc3VjY2VzcyByb3VuZGVkLXBpbGwgcHgtMi41IHB5LTEiIHN0eWxlPSJmb250LXNpemU6MC43cmVtOyI+2b7ZhNmGINmB2LnYp9mEINi02YXYpzwvc3Bhbj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPD9waHAgZW5kaWY7ID8+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGg1IGNsYXNzPSJmdy1ib2xkIHRleHQtZGFyayBtYi0xIj48P3BocCBlY2hvIGh0bWxzcGVjaWFsY2hhcnMoJHBbJ25hbWUnXSk7ID8+PC9oNT4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8cCBjbGFzcz0ic21hbGwgdGV4dC1tdXRlZCBtYi0zIiBzdHlsZT0iZm9udC1zaXplOjAuOHJlbTsgbWluLWhlaWdodDo0OHB4OyI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGVjaG8gaHRtbHNwZWNpYWxjaGFycygkcFsnZGVzYyddKTsgPz4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L3A+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0iYm9yZGVyLXRvcCBib3JkZXItYm90dG9tIHB5LTMgbWItMyI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxzcGFuIGNsYXNzPSJ0ZXh0LW11dGVkIHNtYWxsIj7Yqti52LHZgdmHINiz2KfZhNin2YbZhzo8L3NwYW4+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxoNCBjbGFzcz0iZnctYm9sZCB0ZXh0LWRhcmsgbWItMCBtdC0xIj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGVjaG8gKCRwWydwcmljZV90b21hbiddID4gMCkgPyAobnVtYmVyX2Zvcm1hdCgkcFsncHJpY2VfdG9tYW4nXSkgLiAnIDxzbWFsbCBzdHlsZT0iZm9udC1zaXplOjAuNzVyZW07Ij7YqtmI2YXYp9mGPC9zbWFsbD4nKSA6ICfZhdit2KfYs9io2Ycg2LPZgdin2LHYtNuMJzsgPz4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9oND4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHNtYWxsIGNsYXNzPSJ0ZXh0LW11dGVkIiBzdHlsZT0iZm9udC1zaXplOjAuN3JlbTsiPtmF2KzZiNiyIDw/cGhwIGVjaG8gJHBbJ21heF91c2VycyddOyA/PiDaqdin2LHYqNixINmH2YXYstmF2KfZhjwvc21hbGw+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHVsIGNsYXNzPSJsaXN0LXVuc3R5bGVkIHNtYWxsIG1iLTQgcHMtMCI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGZvcmVhY2ggKCRtb2R1bGVzIGFzICRtS2V5ID0+ICRtSW5mbyk6IAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJGluUGxhbiA9IGluX2FycmF5KCRtS2V5LCAkcFsnbW9kdWxlcyddKTsKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPz4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxsaSBjbGFzcz0iZC1mbGV4IGFsaWduLWl0ZW1zLWNlbnRlciBnYXAtMiBtYi0yIDw/cGhwIGVjaG8gJGluUGxhbiA/ICd0ZXh0LWRhcmsgZnctYm9sZCcgOiAndGV4dC1tdXRlZCBvcGFjaXR5LTUwJzsgPz4iPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXMgPD9waHAgZWNobyAkaW5QbGFuID8gJ2ZhLWNoZWNrIHRleHQtc3VjY2VzcycgOiAnZmEtdGltZXMgdGV4dC1tdXRlZCc7ID8+IiBzdHlsZT0iZm9udC1zaXplOjAuNzVyZW07Ij48L2k+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPHNwYW4+PD9waHAgZWNobyBodG1sc3BlY2lhbGNoYXJzKCRtSW5mb1snbmFtZSddKTsgPz48L3NwYW4+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2xpPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8P3BocCBlbmRmb3JlYWNoOyA/PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDwvdWw+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj4KCiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8ZGl2PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/cGhwIGlmICgkaXNDdXJyZW50KTogPz4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPGJ1dHRvbiBjbGFzcz0iYnRuIGJ0bi1vdXRsaW5lLXN1Y2Nlc3Mgdy0xMDAgcm91bmRlZC0zIHB5LTIgZnctYm9sZCIgZGlzYWJsZWQ+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8aSBjbGFzcz0iZmFzIGZhLWNoZWNrIG1lLTEiPjwvaT4g2b7ZhNmGINmB2LnZhNuMINiz2KfZhdin2YbZhwogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2J1dHRvbj4KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8P3BocCBlbHNlOiA/PgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8YSBocmVmPSI8P3BocCBlY2hvIEFQUF9VUkw7ID8+L2luZGV4LnBocD9wYWdlPWFkbWluJmFjdGlvbj11cGdyYWRlX3BsYW4mbW9kdWxlPWFsbCIgY2xhc3M9ImJ0biBidG4tcHJpbWFyeSB3LTEwMCByb3VuZGVkLTMgcHktMiBmdy1ib2xkIHNoYWRvdy1zbSI+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICDYp9mG2KrYrtin2Kgg2Ygg2KfYsdiq2YLYp9uMINm+2YTZhgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2E+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgPD9waHAgZW5kaWY7ID8+CiAgICAgICAgICAgICAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICA8P3BocCBlbmRmb3JlYWNoOyA/PgogICAgICAgICAgICA8L2Rpdj4KICAgICAgICA8L2Rpdj4KICAgIDwvZGl2Pgo8L2Rpdj4KCjxzY3JpcHQ+CnZhciBwbGFuc0NhdGFsb2cgPSA8P3BocCBlY2hvIGpzb25fZW5jb2RlKCRwbGFucyk7ID8+Owp2YXIgZ2VuZXJhdGVkTGljZW5zZVBheWxvYWQgPSBudWxsOwp2YXIgZ2VuZXJhdGVkTGljZW5zZUtleSA9ICcnOwoKLy8g2KrYutuM24zYsSDYrtmI2K/aqdin2LEg2obaqeKAjNio2KfaqdizINmF2KfamNmI2YTigIzZh9inINmH2Ybar9in2YUg2KfZhtiq2K7Yp9ioINm+2YTZhiDYr9ixINmF2YjZhNivINmE2KfbjNiz2YbYswpmdW5jdGlvbiBvblBsYW5TZWxlY3Rpb25DaGFuZ2UoKSB7CiAgICB2YXIgcGxhbktleSA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdnZW5fcGxhbl9rZXknKS52YWx1ZTsKICAgIHZhciBwbGFuID0gcGxhbnNDYXRhbG9nW3BsYW5LZXldOwogICAgaWYgKHBsYW4gJiYgcGxhbi5tb2R1bGVzKSB7CiAgICAgICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLm1vZHVsZS1jaGVja2JveCcpLmZvckVhY2goZnVuY3Rpb24oY2IpIHsKICAgICAgICAgICAgY2IuY2hlY2tlZCA9IHBsYW4ubW9kdWxlcy5pbmNsdWRlcyhjYi52YWx1ZSk7CiAgICAgICAgfSk7CiAgICAgICAgaWYgKHBsYW4ubWF4X3VzZXJzKSB7CiAgICAgICAgICAgIGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdnZW5fdXNlcnMnKS52YWx1ZSA9IHBsYW4ubWF4X3VzZXJzOwogICAgICAgIH0KICAgIH0KfQoKZnVuY3Rpb24gc2VsZWN0QWxsTW9kdWxlcyhjaGVjaykgewogICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLm1vZHVsZS1jaGVja2JveCcpLmZvckVhY2goZnVuY3Rpb24oY2IpIHsKICAgICAgICBjYi5jaGVja2VkID0gY2hlY2s7CiAgICB9KTsKfQoKLy8g2YXZgtiv2KfYsdiv2YfbjCDYp9mI2YTbjNmHINiq24zaqeKAjNmH2Kcg2YXYt9in2KjZgiDZvtmE2YYg2b7bjNi04oCM2YHYsdi2CmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCBmdW5jdGlvbigpIHsKICAgIG9uUGxhblNlbGVjdGlvbkNoYW5nZSgpOwp9KTsKCi8vINin2LHYs9in2YQg2K/Ysdiu2YjYp9iz2Kog2KrZiNmE24zYryDZhNin24zYs9mG2LMg2KjZhyDaqdmG2KrYsdmE2LEKYXN5bmMgZnVuY3Rpb24gZ2VuZXJhdGVDb21tZXJjaWFsTGljZW5zZShlKSB7CiAgICBlLnByZXZlbnREZWZhdWx0KCk7CiAgICB2YXIgYnRuID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2J0bi1nZW5lcmF0ZS1saWNlbnNlJyk7CiAgICBidG4uZGlzYWJsZWQgPSB0cnVlOwogICAgYnRuLmlubmVySFRNTCA9ICc8aSBjbGFzcz0iZmFzIGZhLXNwaW5uZXIgZmEtc3BpbiBtZS0xIj48L2k+INiv2LEg2K3Yp9mEINiq2YjZhNuM2K8g2Ygg2KfZhdi22KfbjCDYr9uM2KzbjNiq2KfZhC4uLic7CgogICAgdmFyIHNlbGVjdGVkTW9kdWxlcyA9IFtdOwogICAgZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnLm1vZHVsZS1jaGVja2JveDpjaGVja2VkJykuZm9yRWFjaChmdW5jdGlvbihjYikgewogICAgICAgIHNlbGVjdGVkTW9kdWxlcy5wdXNoKGNiLnZhbHVlKTsKICAgIH0pOwoKICAgIHZhciB2YWxTZWxlY3QgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZ2VuX3ZhbGlkaXR5JykudmFsdWU7CiAgICB2YXIgaXNMaWZldGltZSA9ICh2YWxTZWxlY3QgPT09ICdsaWZldGltZScpOwogICAgdmFyIGRheXMgPSBpc0xpZmV0aW1lID8gMzY1MCA6IHBhcnNlSW50KHZhbFNlbGVjdCk7CgogICAgdmFyIGRhdGEgPSB7CiAgICAgICAgbGljZW5zZWVfbmFtZTogZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2dlbl9saWNlbnNlZScpLnZhbHVlLAogICAgICAgIGhhcmR3YXJlX2lkOiBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZ2VuX2hhcmR3YXJlX2lkJykudmFsdWUsCiAgICAgICAgcGxhbl9rZXk6IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdnZW5fcGxhbl9rZXknKS52YWx1ZSwKICAgICAgICB2YWxpZGl0eV9kYXlzOiBkYXlzLAogICAgICAgIGlzX2xpZmV0aW1lOiBpc0xpZmV0aW1lID8gMSA6IDAsCiAgICAgICAgbWF4X3VzZXJzOiBwYXJzZUludChkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZ2VuX3VzZXJzJykudmFsdWUpLAogICAgICAgIG1vZHVsZXM6IHNlbGVjdGVkTW9kdWxlcwogICAgfTsKCiAgICB0cnkgewogICAgICAgIHZhciByZXMgPSBhd2FpdCBmZXRjaCgnPD9waHAgZWNobyBBUFBfVVJMOyA/Pi9pbmRleC5waHA/cGFnZT1hZG1pbiZhY3Rpb249Z2VuZXJhdGVfbGljZW5zZScsIHsKICAgICAgICAgICAgbWV0aG9kOiAnUE9TVCcsCiAgICAgICAgICAgIGhlYWRlcnM6IHsgJ0NvbnRlbnQtVHlwZSc6ICdhcHBsaWNhdGlvbi9qc29uJyB9LAogICAgICAgICAgICBib2R5OiBKU09OLnN0cmluZ2lmeShkYXRhKQogICAgICAgIH0pOwogICAgICAgIHZhciByZXN1bHQgPSBhd2FpdCByZXMuanNvbigpOwogICAgICAgIGlmIChyZXN1bHQuc3VjY2VzcykgewogICAgICAgICAgICBnZW5lcmF0ZWRMaWNlbnNlS2V5ID0gcmVzdWx0LmxpY2Vuc2Vfa2V5OwogICAgICAgICAgICBnZW5lcmF0ZWRMaWNlbnNlUGF5bG9hZCA9IHJlc3VsdC5wYXlsb2FkOwoKICAgICAgICAgICAgZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2dlbl9vdXRwdXRfcGxhY2Vob2xkZXInKS5zdHlsZS5kaXNwbGF5ID0gJ25vbmUnOwogICAgICAgICAgICBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZ2VuX291dHB1dF9yZXN1bHQnKS5zdHlsZS5kaXNwbGF5ID0gJ2Jsb2NrJzsKCiAgICAgICAgICAgIGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKCdnZW5fcmVzX2luZm8nKS5pbm5lclRleHQgPSAn2LXYp9iv2LEg2LTYr9mHINio2LHYp9uMOiAnICsgcmVzdWx0LnBheWxvYWQubGljZW5zZWUgKyAnIHwg2b7ZhNmGOiAnICsgcmVzdWx0LnBheWxvYWQucGxhbl9uYW1lICsgJyB8INmF2KfamNmI2YTigIzZh9inOiAnICsgcmVzdWx0LnBheWxvYWQubW9kdWxlcy5sZW5ndGggKyAnINmF2YjYsdivJzsKICAgICAgICAgICAgZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ2dlbl9yZXNfa2V5JykudmFsdWUgPSByZXN1bHQubGljZW5zZV9rZXk7CiAgICAgICAgfSBlbHNlIHsKICAgICAgICAgICAgYWxlcnQoJ9iu2LfYpyDYr9ixINi12K/ZiNixINmE2KfbjNiz2YbYszogJyArIChyZXN1bHQubWVzc2FnZSB8fCAn2K7Yt9in24wg2YbYp9mF2LTYrti1JykpOwogICAgICAgIH0KICAgIH0gY2F0Y2goZXJyKSB7CiAgICAgICAgYWxlcnQoJ9iu2LfYpyDYr9ixINin2LHYqtio2KfYtyDYqNinINiz2LHZiNixOiAnICsgZXJyLm1lc3NhZ2UpOwogICAgfSBmaW5hbGx5IHsKICAgICAgICBidG4uZGlzYWJsZWQgPSBmYWxzZTsKICAgICAgICBidG4uaW5uZXJIVE1MID0gJzxpIGNsYXNzPSJmYXMgZmEtc3RhbXAgbWUtMSI+PC9pPiDYtdiv2YjYsSDZhNin24zYs9mG2LMg2LHZhdiy2Ybar9in2LHbjCDYtNiv2YcgUlNBJzsKICAgIH0KfQoKZnVuY3Rpb24gY29weUdlbmVyYXRlZExpY2Vuc2UoKSB7CiAgICB2YXIgdGEgPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZCgnZ2VuX3Jlc19rZXknKTsKICAgIHRhLnNlbGVjdCgpOwogICAgbmF2aWdhdG9yLmNsaXBib2FyZC53cml0ZVRleHQodGEudmFsdWUpOwogICAgYWxlcnQoJ9qp2K8g2YTYp9uM2LPZhtizINio2Kcg2YXZiNmB2YLbjNiqINiv2LEg2qnZhNuM2b7igIzYqNmI2LHYryDaqdm+24wg2q/Ysdiv24zYry4nKTsKfQoKZnVuY3Rpb24gZG93bmxvYWRMaWNlbnNlRmlsZSgpIHsKICAgIGlmICghZ2VuZXJhdGVkTGljZW5zZUtleSkgcmV0dXJuOwogICAgdmFyIGJsb2IgPSBuZXcgQmxvYihbZ2VuZXJhdGVkTGljZW5zZUtleV0sIHsgdHlwZTogJ3RleHQvcGxhaW47Y2hhcnNldD11dGYtOCcgfSk7CiAgICB2YXIgdXJsID0gVVJMLmNyZWF0ZU9iamVjdFVSTChibG9iKTsKICAgIHZhciBhID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnYScpOwogICAgYS5ocmVmID0gdXJsOwogICAgYS5kb3dubG9hZCA9ICdoYW1ha2FyLmxpYyc7CiAgICBkb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKGEpOwogICAgYS5jbGljaygpOwogICAgZG9jdW1lbnQuYm9keS5yZW1vdmVDaGlsZChhKTsKfQo8L3NjcmlwdD4K');
            @file_put_contents($indexPath, $viewContent);
        }
        $upgradePath = $licenseDir . '/upgrade.php';
        if (!file_exists($upgradePath)) {
            $viewContent = base64_decode('PD9waHAKLy8gYXBwL3ZpZXdzL2FkbWluL2xpY2Vuc2UvdXBncmFkZS5waHAgLSDYtdmB2K3ZhyDYrNiw2KfYqCDYp9ix2KrZgtin24wg2b7ZhNmGINmIINmB2LnYp9mE4oCM2LPYp9iy24wg2YXYp9qY2YjZhCDYp9iu2KrYtdin2LXbjAokbW9kdWxlS2V5ID0gJG1vZHVsZUtleSA/PyAnJzsKJG1vZHVsZUluZm8gPSAkbW9kdWxlSW5mbyA/PyBudWxsOwokcGxhbnMgPSAkcGxhbnMgPz8gW107CiRhY3RpdmVMaWNlbnNlID0gJGFjdGl2ZUxpY2Vuc2UgPz8gbnVsbDsKPz4KCjxkaXYgY2xhc3M9ImNvbnRhaW5lciBweS00IHRleHQtY2VudGVyIiBzdHlsZT0ibWF4LXdpZHRoOiA4NjBweDsiPgogICAgPGRpdiBjbGFzcz0iY2FyZCBib3JkZXItMCByb3VuZGVkLTQgc2hhZG93LXNtIHAtNCBwLW1kLTUgYmctd2hpdGUgbWItNCI+CiAgICAgICAgPGRpdiBjbGFzcz0ibWItNCI+CiAgICAgICAgICAgIDxkaXYgY2xhc3M9InJvdW5kZWQtY2lyY2xlIGJnLXdhcm5pbmcgYmctb3BhY2l0eS0xMCB0ZXh0LXdhcm5pbmcgZC1pbmxpbmUtZmxleCBhbGlnbi1pdGVtcy1jZW50ZXIganVzdGlmeS1jb250ZW50LWNlbnRlciBtYi0zIiBzdHlsZT0id2lkdGg6IDg0cHg7IGhlaWdodDogODRweDsiPgogICAgICAgICAgICAgICAgPGkgY2xhc3M9ImZhcyBmYS1sb2NrIGZhLTJ4Ij48L2k+CiAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICA8aDMgY2xhc3M9ImZ3LWJvbGQgdGV4dC1kYXJrIG1iLTIiPtin24zZhiDYqNiu2LQg2YbbjNin2LIg2KjZhyDYp9ix2KrZgtin24wg2b7ZhNmGINmE2KfbjNiz2YbYsyDYr9in2LHYrzwvaDM+CiAgICAgICAgICAgIDxwIGNsYXNzPSJ0ZXh0LW11dGVkIHNtYWxsIG1heC13LTYwMCBteC1hdXRvIj4KICAgICAgICAgICAgICAgINmF2KfamNmI2YQgPHN0cm9uZz48P3BocCBlY2hvIGh0bWxzcGVjaWFsY2hhcnMoJG1vZHVsZUluZm9bJ25hbWUnXSA/PyAn2KfZhtiq2K7Yp9io24wnKTsgPz48L3N0cm9uZz4g2K/YsSDZvtmE2YYg2YHYudmE24wg2YTYp9uM2LPZhtizINi02YXYpyDZgdi52KfZhCDZhtmF24zigIzYqNin2LTYry4g2LTZhdinINmF24zigIzYqtmI2KfZhtuM2K8g2KjYpyDYp9ix2KrZgtin24wg2b7ZhNmGINuM2Kcg2YHYudin2YTigIzYs9in2LLbjCDYqtqp4oCM2YXYp9qY2YjZhCDYqNmHINiq2YXYp9mF24wg2YLYp9io2YTbjNiq4oCM2YfYp9uMINii2YYg2K/Ys9iq2LHYs9uMINm+24zYr9inINqp2YbbjNivLgogICAgICAgICAgICA8L3A+CiAgICAgICAgPC9kaXY+CgogICAgICAgIDw/cGhwIGlmICgkbW9kdWxlSW5mbyk6ID8+CiAgICAgICAgICAgIDxkaXYgY2xhc3M9InAtMyByb3VuZGVkLTMgYmctbGlnaHQgYm9yZGVyIG1iLTQgdGV4dC1zdGFydCI+CiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJkLWZsZXggYWxpZ24taXRlbXMtY2VudGVyIGdhcC0yIGZ3LWJvbGQgdGV4dC1kYXJrIG1iLTEiPgogICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPSI8P3BocCBlY2hvICRtb2R1bGVJbmZvWydpY29uJ107ID8+IHRleHQtcHJpbWFyeSI+PC9pPgogICAgICAgICAgICAgICAgICAgIDxzcGFuPtmC2KfYqNmE24zYquKAjNmH2KfbjCDZhdin2pjZiNmEIDw/cGhwIGVjaG8gaHRtbHNwZWNpYWxjaGFycygkbW9kdWxlSW5mb1snbmFtZSddKTsgPz46PC9zcGFuPgogICAgICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgICAgICAgICA8cCBjbGFzcz0ic21hbGwgdGV4dC1tdXRlZCBtYi0wIj48P3BocCBlY2hvIGh0bWxzcGVjaWFsY2hhcnMoJG1vZHVsZUluZm9bJ2Rlc2MnXSk7ID8+PC9wPgogICAgICAgICAgICA8L2Rpdj4KICAgICAgICA8P3BocCBlbmRpZjsgPz4KCiAgICAgICAgPGRpdiBjbGFzcz0icm93IGctMyBqdXN0aWZ5LWNvbnRlbnQtY2VudGVyIG1iLTQiPgogICAgICAgICAgICA8ZGl2IGNsYXNzPSJjb2wtbWQtNiI+CiAgICAgICAgICAgICAgICA8ZGl2IGNsYXNzPSJwLTQgcm91bmRlZC00IGJvcmRlciBiZy13aGl0ZSBoLTEwMCB0ZXh0LWNlbnRlciI+CiAgICAgICAgICAgICAgICAgICAgPGkgY2xhc3M9ImZhcyBmYS1oZWFkc2V0IGZhLTJ4IHRleHQtcHJpbWFyeSBtYi0yIj48L2k+CiAgICAgICAgICAgICAgICAgICAgPGg2IGNsYXNzPSJmdy1ib2xkIHRleHQtZGFyayBtYi0xIj7YqtmF2KfYsyDYqNinINmI2KfYrdivINmB2LHZiNi0INmIINm+2LTYqtuM2KjYp9mG24w8L2g2PgogICAgICAgICAgICAgICAgICAgIDxwIGNsYXNzPSJzbWFsbCB0ZXh0LW11dGVkIG1iLTMiPtis2YfYqiDYr9ix24zYp9mB2Kog2YXYtNin2YjYsdmHINix2KfbjNqv2KfZhiDZiCDZgdi52KfZhOKAjNiz2KfYstuMINmB2YjYsduMINmE2KfbjNiz2YbYsyDYqNinINqp2KfYsdi02YbYp9iz2KfZhiDZhdinINiq2YXYp9izINio2q/bjNix24zYry48L3A+CiAgICAgICAgICAgICAgICAgICAgPGEgaHJlZj0idGVsOjA0MTM0NDAxMTc5IiBjbGFzcz0iYnRuIGJ0bi1vdXRsaW5lLXByaW1hcnkgcm91bmRlZC1waWxsIHB4LTQgZnctYm9sZCI+CiAgICAgICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXMgZmEtcGhvbmUgbWUtMSI+PC9pPiDbsNu027Et27PbtNu027Dbsdux27fbuQogICAgICAgICAgICAgICAgICAgIDwvYT4KICAgICAgICAgICAgICAgIDwvZGl2PgogICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgPGRpdiBjbGFzcz0iY29sLW1kLTYiPgogICAgICAgICAgICAgICAgPGRpdiBjbGFzcz0icC00IHJvdW5kZWQtNCBib3JkZXIgYmctd2hpdGUgaC0xMDAgdGV4dC1jZW50ZXIiPgogICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXMgZmEtY2VydGlmaWNhdGUgZmEtMnggdGV4dC1zdWNjZXNzIG1iLTIiPjwvaT4KICAgICAgICAgICAgICAgICAgICA8aDYgY2xhc3M9ImZ3LWJvbGQgdGV4dC1kYXJrIG1iLTEiPtmE2KfbjNiz2YbYsyDYrNiv24zYryDYr9ix24zYp9mB2Kog2qnYsdiv2YfigIzYp9uM2K/YnzwvaDY+CiAgICAgICAgICAgICAgICAgICAgPHAgY2xhc3M9InNtYWxsIHRleHQtbXV0ZWQgbWItMyI+2qnYryDZhNin24zYs9mG2LMg2KfYrtiq2LXYp9i124wg2K7ZiNivINix2Kcg2K/YsSDYqNiu2LQg2YHYudin2YTigIzYs9in2LLbjCDZiNin2LHYryDZhtmF2YjYr9mHINiq2Kcg2YXYp9qY2YjZhCDYqNmE2KfZgdin2LXZhNmHINio2KfYsiDYtNmI2K8uPC9wPgogICAgICAgICAgICAgICAgICAgIDxhIGhyZWY9Ijw/cGhwIGVjaG8gQVBQX1VSTDsgPz4vaW5kZXgucGhwP3BhZ2U9YWRtaW4mYWN0aW9uPWxpY2Vuc2luZyIgY2xhc3M9ImJ0biBidG4tcHJpbWFyeSByb3VuZGVkLXBpbGwgcHgtNCBmdy1ib2xkIHNoYWRvdy1zbSI+CiAgICAgICAgICAgICAgICAgICAgICAgIDxpIGNsYXNzPSJmYXMgZmEta2V5IG1lLTEiPjwvaT4g2YHYudin2YTigIzYs9in2LLbjCDZhNin24zYs9mG2LMKICAgICAgICAgICAgICAgICAgICA8L2E+CiAgICAgICAgICAgICAgICA8L2Rpdj4KICAgICAgICAgICAgPC9kaXY+CiAgICAgICAgPC9kaXY+CgogICAgICAgIDxkaXYgY2xhc3M9ImJvcmRlci10b3AgcHQtMyI+CiAgICAgICAgICAgIDxhIGhyZWY9Ijw/cGhwIGVjaG8gQVBQX1VSTDsgPz4vaW5kZXgucGhwP3BhZ2U9YWRtaW4mYWN0aW9uPWRhc2hib2FyZCIgY2xhc3M9InNtYWxsIHRleHQtbXV0ZWQgdGV4dC1kZWNvcmF0aW9uLW5vbmUiPgogICAgICAgICAgICAgICAgPGkgY2xhc3M9ImZhcyBmYS1hcnJvdy1yaWdodCBtZS0xIj48L2k+INio2KfYstqv2LTYqiDYqNmHINm+24zYtNiu2YjYp9mGINmF2K/bjNix24zYqgogICAgICAgICAgICA8L2E+CiAgICAgICAgPC9kaXY+CiAgICA8L2Rpdj4KPC9kaXY+Cg==');
            @file_put_contents($upgradePath, $viewContent);
        }
        
        $activeLicense = LicenseManager::getActiveLicense();
        $machineFingerprint = LicenseManager::getMachineFingerprint();
        $plans = LicenseManager::$PLANS;
        $modules = LicenseManager::$MODULES;

        view('admin/license/index', [
            'layout' => 'admin_layout',
            'title' => 'مدیریت لایسنس، قفل سخت‌افزاری و پلن‌های تجاری',
            'license' => $activeLicense,
            'machineFingerprint' => $machineFingerprint,
            'plans' => $plans,
            'modules' => $modules
        ]);
    }

    public function activateLicense() {
        $this->auth->restrict(['admin']);
        require_once __DIR__ . '/../core/LicenseManager.php';

        $licenseKey = '';
        if (!empty($_FILES['license_file']['tmp_name'])) {
            $licenseKey = trim(file_get_contents($_FILES['license_file']['tmp_name']));
        } elseif (!empty($_POST['license_key'])) {
            $licenseKey = trim($_POST['license_key']);
        }

        if (empty($licenseKey)) {
            FlashMessage::set('message', 'کد لایسنس یا فایل معتبر ارائه نشده است.', 'error');
            redirect(APP_URL . '/index.php?page=admin&action=licensing');
        }

        $res = LicenseManager::activateLicense($licenseKey);
        if ($res['valid']) {
            FlashMessage::set('message', 'لایسنس با موفقیت اعتبارسنجی و در سامانه فعال گردید.', 'success');
        } else {
            FlashMessage::set('message', 'خطا در فعال‌سازی: ' . $res['message'], 'error');
        }

        redirect(APP_URL . '/index.php?page=admin&action=licensing');
    }

    public function generateLicense() {
        $this->auth->restrict(['admin']);
        header('Content-Type: application/json; charset=utf-8');
        require_once __DIR__ . '/../core/LicenseManager.php';

        $raw = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (!$data) $data = $_POST;

        $res = LicenseManager::generateLicense($data);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function upgradePlan() {
        require_once __DIR__ . '/../core/LicenseManager.php';
        $moduleKey = sanitize($_GET['module'] ?? '');
        $plans = LicenseManager::$PLANS;
        $modules = LicenseManager::$MODULES;
        $activeLicense = LicenseManager::getActiveLicense();

        view('admin/license/upgrade', [
            'layout' => 'admin_layout',
            'title' => 'ارتقای پلن تجاری هماکار',
            'moduleKey' => $moduleKey,
            'moduleInfo' => $modules[$moduleKey] ?? null,
            'plans' => $plans,
            'activeLicense' => $activeLicense
        ]);
    }

    public function getHardwareFingerprint() {
        $this->auth->restrict(['admin']);
        header('Content-Type: application/json; charset=utf-8');
        require_once __DIR__ . '/../core/LicenseManager.php';
        echo json_encode([
            'status' => 'success',
            'hardware_id' => LicenseManager::getMachineFingerprint()
        ]);
        exit();
    }

}
