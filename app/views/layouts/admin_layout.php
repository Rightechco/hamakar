<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'پنل مدیریت'; ?> | سامانه هماکار</title>
    
    <!-- CDN های اصلی استایل و فونت -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css" rel="stylesheet"/>

    <style>
        :root {
            --sidebar-width: 270px;
            --brand-primary: #4f46e5;
            --brand-dark: #0f172a;
        }

        body {
            font-family: 'Vazirmatn', sans-serif !important;
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            direction: rtl;
            text-align: right;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* Main Content Area Mechanics */
        .content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .top-navbar {
            height: 65px;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .main-container {
            padding: 1.75rem;
            flex: 1;
        }

        @media print {
            .premium-sidebar, .top-navbar, .no-print { display: none !important; }
            .content-area { width: 100% !important; margin: 0 !important; padding: 0 !important; }
            body { background: #ffffff !important; }
        }
    </style>
</head>
<body>

<div class="admin-wrapper">
    
    <!-- فراخوانی سایدبار جامع و کامل سیستم -->
    <?php 
    if (file_exists(__DIR__ . '/../shared/sidebar.php')) {
        require_once __DIR__ . '/../shared/sidebar.php';
    }
    ?>

    <!-- Main Content Area -->
    <div class="content-area">
        
        <!-- Navbar Top -->
        <header class="top-navbar no-print">
            <div class="d-flex align-items-center gap-3">
                <button type="button" id="mobile-toggle-btn" class="btn btn-sm btn-light border d-md-none">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="fw-bold text-dark fs-6"><?php echo $title ?? 'مدیریت هماکار'; ?></span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="dropdown">
                    <button class="btn btn-light border rounded-pill px-3 py-1.5 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="width:28px; height:28px; font-size: 0.75rem;">
                            U
                        </div>
                        <span class="small fw-bold text-dark">کاربر سیستم</span>
                        <i class="fas fa-chevron-down text-muted small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end rounded-3 shadow-sm border-0 mt-2">
                        <li><a class="dropdown-item small py-2" href="<?php echo APP_URL; ?>/index.php?page=client&action=dashboard"><i class="fas fa-user-circle me-2 text-primary"></i>پنل کاربری</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item small py-2 text-danger" href="<?php echo APP_URL; ?>/index.php?page=logout"><i class="fas fa-sign-out-alt me-2"></i>خروج</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Dynamic Main Content -->
        <main class="main-container">
            <!-- Flash Message Display -->
            <?php if (class_exists('FlashMessage')) { FlashMessage::display(); } ?>

            <?php echo $content ?? ''; ?>
        </main>
    </div>
</div>

<!-- JS CDN ها -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>

<!-- SweetAlert2 جهت تایید حذف در تمام فرم‌ها -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // باز/بسته کردن سایدبار در موبایل
    const mobileBtn = document.getElementById('mobile-toggle-btn');
    const closeBtn = document.getElementById('close-sidebar-btn');
    const sidebar = document.getElementById('sidebar');

    if (mobileBtn && sidebar) {
        mobileBtn.addEventListener('click', function() {
            sidebar.classList.add('mobile-open');
        });
    }
    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
        });
    }

    // تایید فرم‌های حذف
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(function(form) {
        form.removeAttribute('onsubmit');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'آیا از حذف این مورد اطمینان دارید؟',
                text: "این عملیات غیرقابل بازگشت خواهد بود!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'بله، حذف شود',
                cancelButtonText: 'انصراف',
                customClass: {
                    popup: 'rounded-4 shadow-lg',
                    confirmButton: 'btn btn-danger rounded-3 px-4 py-2 fw-bold me-2',
                    cancelButton: 'btn btn-secondary rounded-3 px-4 py-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>

</body>
</html>