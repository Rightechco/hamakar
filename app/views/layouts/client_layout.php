<?php 
// app/views/layouts/client_layout.php - نسخه اولترا پرمیوم، کامل و هوشمند
global $auth; 
$pageTitle = isset($title) ? htmlspecialchars($title) : 'پنل کاربری';
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f172a">
    <title><?php echo $pageTitle; ?> | سامانه مدیریت هماکار</title>
    
    <!-- Bootstrap RTL 5.3.3 & FontAwesome 6.5.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom Local Styles (در صورت وجود) -->
    <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/css/styles.css')): ?>
        <link href="<?php echo defined('APP_URL') ? APP_URL : ''; ?>/assets/css/styles.css" rel="stylesheet">
    <?php endif; ?>

    <style>
        :root {
            --app-bg: #f8fafc;
            --brand-primary: #4f46e5;
            --brand-primary-hover: #4338ca;
            --brand-dark: #0f172a;
            --brand-card-bg: #ffffff;
            --brand-border: #e2e8f0;
            --app-text: #0f172a;
            --app-muted: #64748b;
        }

        body {
            font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background-color: var(--app-bg);
            color: var(--app-text);
            margin: 0;
            padding: 0;
            direction: rtl !important;
            text-align: right !important;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* Wrapper & Layout Structure */
        .app-wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
        }

        #content-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: var(--app-bg);
        }

        .main-content-area {
            flex: 1;
            padding: 1.75rem;
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Mobile Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 1035;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Print Media Styles */
        @media print {
            .sidebar, .top-navbar, .sidebar-panel, .no-print, .btn, footer {
                display: none !important;
            }
            #content-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .main-content-area {
                padding: 0 !important;
                max-width: 100% !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
        }
    </style>
</head>
<body>

<div class="app-wrapper">
    <!-- Overlay for Mobile Sidebar -->
    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <!-- Sidebar Inclusion -->
    <?php 
        $sidebarPath = __DIR__ . '/../shared/sidebar.php';
        if (file_exists($sidebarPath)) {
            include_once $sidebarPath;
        }
    ?>
    
    <!-- Main Content Area Wrapper -->
    <div id="content-wrapper">
        
        <!-- Navbar Inclusion -->
        <?php 
            $navbarPath = __DIR__ . '/../shared/navbar.php';
            if (file_exists($navbarPath)) {
                include_once $navbarPath;
            }
        ?>
        
        <!-- Main Body Content -->
        <main class="main-content-area">
            <!-- Flash Messages Display -->
            <?php 
                if (class_exists('FlashMessage') && method_exists('FlashMessage', 'display')) {
                    FlashMessage::display(); 
                }
            ?>
            
            <!-- Dynamic Content View Injection -->
            <?php echo $content ?? ''; ?>
        </main>
    </div>
</div>

<!-- Essential JS Libraries -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>

<!-- Global Premium Layout Mechanics Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Mobile Sidebar Toggle Mechanics
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mobileToggleBtns = document.querySelectorAll('[data-bs-toggle="sidebar"], #mobile-toggle-btn');
    const closeSidebarBtn = document.getElementById('close-sidebar-btn');

    function openSidebar() {
        if (sidebar) sidebar.classList.add('mobile-open');
        if (overlay) overlay.classList.add('active');
    }

    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.remove('active');
    }

    mobileToggleBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (sidebar && sidebar.classList.contains('mobile-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    });

    if (overlay) overlay.addEventListener('click', closeSidebar);
    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);

    // 2. SweetAlert2 Delete Confirmation Global Handler
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(function(form) {
        form.removeAttribute('onsubmit');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'آیا از انجام این عملیات اطمینان دارید؟',
                text: "تغییرات انجام‌شده قابل بازگشت نخواهند بود!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'بله، انجام شود',
                cancelButtonText: 'انصراف',
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0',
                    confirmButton: 'btn btn-danger rounded-3 px-4 py-2 fw-bold me-2',
                    cancelButton: 'btn btn-secondary rounded-3 px-4 py-2 fw-bold'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // 3. Initialize Bootstrap Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>

</body>
</html>