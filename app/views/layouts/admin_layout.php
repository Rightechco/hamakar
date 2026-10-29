<?php
// app/views/layouts/admin_layout.php - نسخه نهایی و اصلاح شده
global $auth;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Suha & Nuxt Mobile App & PWA Settings -->
    <meta name="theme-color" content="#2c3e50">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="https://hamakar.ir/manifest.webmanifest">
    <link rel="apple-touch-icon" href="https://hamakar.ir/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="https://hamakar.ir/images/favicon-32x32.png">

    <title><?php echo sanitize($title ?? 'پنل مدیریت'); ?> - رایان تکرو</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo APP_URL; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo APP_URL; ?>/assets/css/styles.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css" rel="stylesheet"/>
    
    <style>
        
        :root {
            --sidebar-bg: #0f172a;
            --content-bg: #f8fafc;
            --navbar-bg: rgba(255, 255, 255, 0.94);
            --text-color: #0f172a;
            --text-muted: #64748b;
            --primary-color: #4f46e5;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.04);
            --shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05);
            --font-family: 'Vazirmatn', sans-serif;
            --sidebar-text-color: #94a3b8;
            --sidebar-icon-color: #64748b;
            --sidebar-hover-bg: rgba(255, 255, 255, 0.05);
            --sidebar-active-bg: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            --sidebar-active-text: #ffffff;
            --sidebar-heading-color: #64748b;
        }
        body {
            background-color: var(--content-bg);
            font-family: var(--font-family);
            color: var(--text-color);
            transition: margin-right 0.3s;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: var(--sidebar-bg);
            color: #fff;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1030;
            box-shadow: 4px 0 24px rgba(15, 23, 42, 0.08);
            border-left: 1px solid rgba(255, 255, 255, 0.05);
        }
        #sidebar.toggled {
            margin-right: -260px;
        }
        #content-wrapper {
            width: 100%;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        main {
            padding: 1.75rem 2rem 85px;
        }
        .sidebar-header { 
            padding: 22px 20px; 
            text-align: center; 
            font-size: 20px; 
            font-weight: 800; 
            color: #fff; 
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            letter-spacing: -0.3px;
        }
        .sidebar-header i { margin-left: 10px; color: #4f46e5; }
        .sidebar-nav { padding: 15px 0; }
        .sidebar-nav .nav-item .nav-link {
            display: flex; 
            align-items: center; 
            padding: 10px 18px;
            color: var(--sidebar-text-color); 
            border-radius: 12px;
            margin: 3px 12px; 
            text-decoration: none; 
            font-weight: 600;
            font-size: 0.88rem;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .sidebar-nav .nav-link i { width: 20px; margin-left: 14px; font-size: 1rem; color: var(--sidebar-icon-color); }
        .sidebar-nav .nav-link:hover { 
            background-color: var(--sidebar-hover-bg); 
            color: #fff; 
            transform: translateX(-2px);
        }
        .sidebar-nav .nav-link.active { 
            background: var(--sidebar-active-bg); 
            color: var(--sidebar-active-text); 
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
        }
        .sidebar-heading { 
            padding: 1.25rem 1.5rem 0.4rem; 
            font-size: 0.74rem; 
            color: var(--sidebar-heading-color); 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            font-weight: 800;
        }
        .sidebar-submenu { background-color: rgba(0,0,0,0.2); padding-left: 0; list-style: none; margin: 4px 12px; border-radius: 10px; overflow: hidden; }
        .sidebar-submenu .submenu-item { display: block; padding: 9px 20px 9px 15px; color: var(--sidebar-text-color); text-decoration: none; font-size: 0.84rem; font-weight: 500; transition: all 0.15s; }
        .sidebar-submenu .submenu-item:hover { background-color: rgba(255,255,255,0.06); color: #fff; }
        .top-navbar {
            background: var(--navbar-bg); 
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 0.85rem 1.75rem;
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.03);
        }
        .content-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.5); 
            backdrop-filter: blur(3px);
            z-index: 1020; display: none;
        }
        @media (max-width: 768px) {
            #sidebar {
                margin-right: -260px;
                position: fixed;
                height: 100%;
            }
            #sidebar.toggled {
                margin-right: 0;
            }
            body.sidebar-toggled #content-wrapper {
                margin-right: 260px;
            }
            .content-overlay {
                display: none;
            }
            #sidebar.toggled ~ #content-wrapper .content-overlay {
                display: block;
            }
        }

            #sidebar.toggled {
                margin-right: 0;
            }
            body.sidebar-toggled #content-wrapper {
                margin-right: 250px;
            }
            .content-overlay {
                display: none;
            }
            #sidebar.toggled ~ #content-wrapper .content-overlay {
                display: block;
            }
        }
        .chat-widget-toggler{position:fixed;bottom:20px;left:20px;width:60px;height:60px;background-color:#2c3e50;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;cursor:pointer;box-shadow:0 4px 12px rgba(0,0,0,0.2);z-index:1050;transition: all 0.3s ease;}
        .chat-widget-toggler:hover{transform: scale(1.1);}
        .chat-window{position:fixed;bottom:90px;left:20px;width:350px;max-width:90%;height:500px;background:#fff;border-radius:15px;box-shadow:0 5px 25px rgba(0,0,0,0.15);display:flex;flex-direction:column;transform:scale(0.8) translateY(20px);opacity:0;visibility:hidden;transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);z-index:1040;}
        .chat-window.open{transform:scale(1) translateY(0);opacity:1;visibility:visible;}
        .chat-header{padding:15px;background:#f8f9fa;border-bottom:1px solid #dee2e6;font-weight:bold;border-radius:15px 15px 0 0;display:flex;align-items:center;}
        .chat-header .back-button{cursor:pointer;margin-left:15px;font-size:18px;color:#6c757d;}
        .chat-body{flex-grow:1;padding:10px;overflow-y:auto;background-color:#f4f6f9;}
        .chat-footer{padding:10px;border-top:1px solid #dee2e6;background-color:#f8f9fa;}
        .chat-user-list{padding:0;list-style:none;margin:0;}
        .chat-user-list-item{display:flex;align-items:center;padding:12px 15px;cursor:pointer;border-bottom:1px solid #f1f1f1;}
        .chat-user-list-item:last-child{border-bottom:none;}
        .chat-user-list-item:hover{background-color:#f1f3f5;}
        .chat-user-list-item img{width:45px;height:45px;border-radius:50%;margin-left:15px;}
        .chat-user-list-item .user-info{display:flex;flex-direction:column;}
        .chat-user-list-item .user-name{font-weight:600;color:#212529;}
        .chat-user-list-item .status-indicator{width:8px;height:8px;border-radius:50%;display:inline-block;margin-right:6px;}
        .chat-user-list-item .status-text{font-size:12px;}
        .chat-message{margin-bottom:15px;display:flex;flex-direction:column;}
        .chat-message.sender{align-items:flex-end;}
        .chat-message.receiver{align-items:flex-start;}
        .chat-message .message-bubble{display:inline-block;max-width:80%;padding:10px 15px;border-radius:15px;line-height:1.5;}
        .chat-message.sender .message-bubble{background-color:#3498db;color:white;border-bottom-right-radius:3px;}
        .chat-message.receiver .message-bubble{background-color:#e9ecef;color:#212529;border-bottom-left-radius:3px;}
    
        /* Nuxt-style surfaces and micro-elevations */
        .card {
            border-radius: 16px !important;
            border: 1px solid #eef2f6 !important;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.035) !important;
            transition: all 0.25s ease;
        }
        .card-header {
            border-top-right-radius: 16px !important;
            border-top-left-radius: 16px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        .table-responsive {
            border-radius: 14px;
            -webkit-overflow-scrolling: touch;
        }

        /* Suha Mobile App Bottom Nav for Admin */
        .admin-suha-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 64px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-top: 1px solid #e2e8f0;
            box-shadow: 0 -4px 20px rgba(15, 23, 42, 0.06);
            z-index: 1040;
            align-items: center;
            justify-content: space-around;
            padding: 0 0.5rem;
            padding-bottom: env(safe-area-inset-bottom);
        }
        .admin-suha-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            text-decoration: none;
            color: #64748b;
            gap: 2px;
            font-size: 0.72rem;
            font-weight: 700;
            cursor: pointer;
            padding: 0.35rem 0;
            transition: all 0.2s;
            -webkit-tap-highlight-color: transparent;
        }
        .admin-suha-item i {
            font-size: 1.15rem;
        }
        .admin-suha-item.active, .admin-suha-item:active {
            color: #0d6efd;
        }
        @media (max-width: 768px) {
            .admin-suha-bottom-nav { display: flex !important; }
            main { padding: 1.25rem 1rem 82px !important; }
            .chat-widget-toggler { bottom: 74px !important; }
            .chat-window { bottom: 140px !important; }
        }

    </style>
</head>
<body>

<div class="wrapper">
    <?php include_once __DIR__ . '/../shared/sidebar.php'; ?>
    <div id="content-wrapper">
        <div class="content-overlay" id="content-overlay"></div>
        <?php include_once __DIR__ . '/../shared/navbar.php'; ?>
        <main>
            <?php FlashMessage::display(); ?>
            <?php echo $content; ?>
        </main>
    </div>
</div>

<div class="chat-widget-toggler" id="chat-toggler"><i class="fas fa-comments"></i></div>
<div class="chat-window" id="chat-window">
    <div id="user-list-view" style="height: 100%; display: flex; flex-direction: column;">
        <div class="chat-header">انتخاب کاربر برای چت</div>
        <div class="chat-body" id="user-list-body" style="padding:0;">
            <ul class="chat-user-list" id="chat-user-list"></ul>
        </div>
    </div>
    <div id="conversation-view" style="display: none; height: 100%; flex-direction: column;">
        <div class="chat-header">
            <i class="fas fa-arrow-right back-button" id="back-to-users"></i>
            <span id="chat-partner-name"></span>
        </div>
        <div class="chat-body" id="chat-body-conversation"></div>
        <div class="chat-footer">
            <form id="chat-form" autocomplete="off">
                <input type="hidden" id="receiver-id-input">
                <div class="input-group">
                    <input type="text" id="chat-message-input" class="form-control" placeholder="پیام خود را بنویسید..." required>
                    <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
<script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>


<script type="text/javascript">
    $(document).ready(function() {
        $(".persian-datepicker").pDatepicker({
            format: 'YYYY/MM/DD',
            autoClose: true,
            initialValue: false // برای اینکه به طور پیش‌فرض خالی باشد
        });
    });
</script>
<script>
    $(document).ready(function () {
    // --- مدیریت سایدبار ---
    const sidebar = document.getElementById('sidebar');
    const sidebarToggler = document.getElementById('sidebarToggleTop');
    const overlay = document.getElementById('content-overlay');

    function toggleSidebar() {
        sidebar.classList.toggle('toggled');
        if (window.innerWidth < 768) {
             overlay.style.display = sidebar.classList.contains('toggled') ? 'block' : 'none';
        }
    }

    if (sidebarToggler) {
        sidebarToggler.addEventListener('click', toggleSidebar);
    }
    if (overlay) {
        overlay.addEventListener('click', toggleSidebar);
    }

    // --- منطق ویجت چت آنلاین ---
    const $chatWindow = $('#chat-window');
    const $chatToggler = $('#chat-toggler');
    const $conversationView = $('#conversation-view');
    const $userListView = $('#user-list-view');
    const $conversationBody = $('#chat-body-conversation');
    const $chatForm = $('#chat-form');
    const $messageInput = $('#chat-message-input');
    const $backToUsersButton = $('#back-to-users');
    const $userListContainer = $('#chat-user-list');

    let currentPartnerId = null;
    let lastMessageId = 0;
    let userListInterval = null;
    let messageInterval = null;

    function showUserList() {
        $conversationView.hide();
        $userListView.show();
        currentPartnerId = null;
        
        if(messageInterval) clearInterval(messageInterval);
        messageInterval = null;

        loadUsers();
        if (!userListInterval) {
            userListInterval = setInterval(loadUsers, 20000);
        }
    }

    function showConversation(userId, userName) {
        if(userListInterval) clearInterval(userListInterval);
        userListInterval = null;
        
        currentPartnerId = userId;
        lastMessageId = 0;
        $('#chat-partner-name').text(sanitizeHTML(userName));
        $('#receiver-id-input').val(userId);
        $conversationBody.html('');
        
        $userListView.hide();
        $conversationView.css('display', 'flex');

        fetchMessages();
        if (!messageInterval) {
            messageInterval = setInterval(fetchMessages, 3000);
        }
    }

    function loadUsers() {
        $.getJSON('<?php echo APP_URL; ?>/index.php?page=ajax&action=get_users', function(response) {
            if (response.status === 'success' && response.users) {
                $userListContainer.html('');
                response.users.forEach(user => {
                    const userNameSanitized = sanitizeHTML(user.name);
                    const userHtml = `<li class="chat-user-list-item" data-id="${user.id}" data-name="${userNameSanitized}">
                                        <img src="https://i.pravatar.cc/40?u=${user.id}" alt="${userNameSanitized}">
                                        <div class="user-info">
                                            <span class="user-name">${userNameSanitized}</span>
                                            <div class="status-text text-${user.status_color}">
                                                <span class="status-indicator bg-${user.status_color}"></span>
                                                ${sanitizeHTML(user.online_status)}
                                            </div>
                                        </div>
                                      </li>`;
                    $userListContainer.append(userHtml);
                });
            }
        });
    }

    function fetchMessages(isAfterSend = false) {
        if (!currentPartnerId) return;

        $.getJSON(`<?php echo APP_URL; ?>/index.php?page=ajax&action=fetch_messages&partner_id=${currentPartnerId}&last_id=${lastMessageId}`, function(response) {
            if (response.status === 'success' && response.messages.length > 0) {
                response.messages.forEach(msg => {
                    const messageClass = msg.is_sender ? 'sender' : 'receiver';
                    const senderName = msg.is_sender ? '' : `<strong>${sanitizeHTML(msg.sender_name)}</strong><br>`;
                    const messageHtml = `<div class="chat-message ${messageClass}">
                                            <div class="message-bubble">
                                                ${senderName}${sanitizeHTML(msg.message_text)}
                                            </div>
                                         </div>`;
                    $conversationBody.append(messageHtml);
                    lastMessageId = msg.id;
                });
                
                if (isAfterSend || $conversationBody.is(':visible')) {
                    $conversationBody.scrollTop($conversationBody[0].scrollHeight);
                }
            }
        });
    }

    function sendMessage(e) {
        e.preventDefault();
        const messageText = $messageInput.val().trim();
        if (messageText === '' || !currentPartnerId) return;
        
        $.post('<?php echo APP_URL; ?>/index.php?page=ajax&action=send_message', {
            message_text: messageText,
            receiver_id: currentPartnerId
        }).done(function() {
            $messageInput.val('');
            fetchMessages(true);
        });
    }

    function sanitizeHTML(str) {
        var temp = document.createElement('div');
        temp.textContent = str;
        return temp.innerHTML;
    }

    $chatToggler.on('click', function() {
        $chatWindow.toggleClass('open');
        if ($chatWindow.hasClass('open')) {
            showUserList();
        } else {
            if (userListInterval) clearInterval(userListInterval);
            if (messageInterval) clearInterval(messageInterval);
            userListInterval = null;
            messageInterval = null;
        }
    });
    
    $userListContainer.on('click', '.chat-user-list-item', function() {
        showConversation($(this).data('id'), $(this).data('name'));
    });

    $backToUsersButton.on('click', showUserList);
    $chatForm.on('submit', sendMessage);
});
</script>

<!-- Suha Native Mobile Bottom App Navigation for Admin -->
<nav class="admin-suha-bottom-nav">
    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=dashboard" class="admin-suha-item <?php echo (($action ?? '') === 'dashboard') ? 'active' : ''; ?>">
        <i class="fas fa-house"></i>
        <span>پیشخوان</span>
    </a>
    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=moadianInvoices" class="admin-suha-item <?php echo (($action ?? '') === 'moadianInvoices') ? 'active' : ''; ?>">
        <i class="fas fa-file-invoice-dollar"></i>
        <span>مودیان</span>
    </a>
    <button type="button" class="admin-suha-item" onclick="toggleAdminModulesDrawer()">
        <i class="fas fa-bars-staggered"></i>
        <span>ماژول‌ها</span>
    </button>
    <button type="button" class="admin-suha-item" onclick="const c = document.getElementById('chat-toggler'); if(c) c.click();">
        <i class="fas fa-comments"></i>
        <span>چت</span>
    </button>
    <a href="<?php echo APP_URL; ?>/index.php?page=logout" class="admin-suha-item">
        <i class="fas fa-arrow-right-from-bracket"></i>
        <span>خروج</span>
    </a>
</nav>


<!-- Suha Admin Mobile Bottom Sheet Drawer for all 16 Modules -->
<div class="suha-sheet-backdrop" id="admin-sheet-backdrop" onclick="toggleAdminModulesDrawer()"></div>
<div class="suha-bottom-sheet" id="admin-modules-drawer">
    <div class="suha-sheet-handle" onclick="toggleAdminModulesDrawer()"></div>
    <div class="suha-sheet-header">
        <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-cubes text-primary me-2"></i>همه ماژول‌های سامانه هماکار</h5>
        <button type="button" class="suha-sheet-close" onclick="toggleAdminModulesDrawer()" aria-label="بستن">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="suha-sheet-search">
        <input type="text" id="admin-module-filter-input" placeholder="جستجوی سریع زیرسیستم..." oninput="filterAdminModulesGrid(this.value)">
    </div>
    <div class="suha-sheet-body" id="admin-sheet-grid">
        <a href="index.php?page=admin&action=dashboard" class="suha-module-card" data-title="پیشخوان و مدیریت">
            <div class="suha-module-icon" style="background:#eef2ff; color:#4f46e5;"><i class="fas fa-home"></i></div>
            <div class="suha-module-title">پیشخوان اصلی</div>
            <div class="suha-module-desc">گزارشات و KPI‌های زنده</div>
        </a>
        <a href="index.php?page=admin&action=moadianInvoices" class="suha-module-card" data-title="سامانه مودیان مالیاتی">
            <div class="suha-module-icon" style="background:#fef2f2; color:#ef4444;"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="suha-module-title">سامانه مودیان</div>
            <div class="suha-module-desc">کارپوشه مالیاتی رسمی</div>
        </a>
        <a href="index.php?page=admin&action=treasuryCheques" class="suha-module-card" data-title="چک صیادی و خزانه‌داری">
            <div class="suha-module-icon" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-money-check-dollar"></i></div>
            <div class="suha-module-title">چک و خزانه‌داری</div>
            <div class="suha-module-desc">راس‌گیری و استعلام صیادی</div>
        </a>
        <a href="index.php?page=admin&action=contractGuarantees" class="suha-module-card" data-title="تضامین و وثایق">
            <div class="suha-module-icon" style="background:#eff6ff; color:#3b82f6;"><i class="fas fa-shield-halved"></i></div>
            <div class="suha-module-title">تضامین و وثایق</div>
            <div class="suha-module-desc">ضمانت‌نامه‌های بانکی</div>
        </a>
        <a href="index.php?page=admin&action=boms" class="suha-module-card" data-title="فرمول ساخت BOM">
            <div class="suha-module-icon" style="background:#fffbeb; color:#d97706;"><i class="fas fa-sitemap"></i></div>
            <div class="suha-module-title">فرمول ساخت (BOM)</div>
            <div class="suha-module-desc">درخت محصول و بهای تمام‌شده</div>
        </a>
        <a href="index.php?page=admin&action=productionOrders" class="suha-module-card" data-title="دستورات تولید">
            <div class="suha-module-icon" style="background:#f5f3ff; color:#7c3aed;"><i class="fas fa-industry"></i></div>
            <div class="suha-module-title">دستورات تولید</div>
            <div class="suha-module-desc">رهگیری خطوط و کارگاه</div>
        </a>
        <a href="index.php?page=admin&action=procurementRequisitions" class="suha-module-card" data-title="درخواست‌های خرید و تدارکات">
            <div class="suha-module-icon" style="background:#ecfeff; color:#0891b2;"><i class="fas fa-cart-flatbed"></i></div>
            <div class="suha-module-title">خرید و تدارکات</div>
            <div class="suha-module-desc">سفارشات خرید و تدارکات</div>
        </a>
        <a href="index.php?page=admin&action=aiAnalytics" class="suha-module-card" data-title="دستیار هوش مصنوعی">
            <div class="suha-module-icon" style="background:#fae8ff; color:#a21caf;"><i class="fas fa-brain"></i></div>
            <div class="suha-module-title">هوش مصنوعی AI</div>
            <div class="suha-module-desc">تحلیل سود و پیش‌بینی</div>
        </a>
        <a href="index.php?page=admin&action=accounting" class="suha-module-card" data-title="حسابداری مالی دوبل">
            <div class="suha-module-icon" style="background:#eef2ff; color:#4f46e5;"><i class="fas fa-calculator"></i></div>
            <div class="suha-module-title">حسابداری مالی</div>
            <div class="suha-module-desc">دفاتر روزنامه، کل و تراز</div>
        </a>
        <a href="index.php?page=admin&action=invoices" class="suha-module-card" data-title="فاکتورها و فروش">
            <div class="suha-module-icon" style="background:#fdf2f8; color:#db2777;"><i class="fas fa-receipt"></i></div>
            <div class="suha-module-title">فاکتورها</div>
            <div class="suha-module-desc">فاکتورهای فروش و رسمی</div>
        </a>
        <a href="index.php?page=admin&action=contracts" class="suha-module-card" data-title="قراردادها و پیمان‌ها">
            <div class="suha-module-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-file-contract"></i></div>
            <div class="suha-module-title">قراردادها</div>
            <div class="suha-module-desc">صورت‌وضعیت و تعهدات</div>
        </a>
        <a href="index.php?page=admin&action=warehouses" class="suha-module-card" data-title="انبارداری">
            <div class="suha-module-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-warehouse"></i></div>
            <div class="suha-module-title">انبارداری</div>
            <div class="suha-module-desc">کاردکس کالا، رسید و حواله</div>
        </a>
    </div>
</div>

<script>
function toggleAdminModulesDrawer() {
    const backdrop = document.getElementById('admin-sheet-backdrop');
    const drawer = document.getElementById('admin-modules-drawer');
    if (drawer.classList.contains('open')) {
        drawer.classList.remove('open');
        backdrop.classList.remove('open');
        document.body.style.overflow = '';
    } else {
        drawer.classList.add('open');
        backdrop.classList.add('open');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            const input = document.getElementById('admin-module-filter-input');
            if (input) input.focus();
        }, 200);
    }
}

function filterAdminModulesGrid(query) {
    const q = query.trim().toLowerCase();
    const cards = document.querySelectorAll('#admin-sheet-grid .suha-module-card');
    cards.forEach(c => {
        const title = (c.getAttribute('data-title') || '').toLowerCase();
        const text = c.textContent.toLowerCase();
        if (!q || title.includes(q) || text.includes(q)) {
            c.style.display = 'flex';
        } else {
            c.style.display = 'none';
        }
    });
}
</script>

</body>
</html>
