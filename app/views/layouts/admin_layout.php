<?php
// app/views/layouts/admin_layout.php - نسخه نهایی و اصلاح شده
global $auth;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($title ?? 'پنل مدیریت'); ?> - رایان تکرو</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo APP_URL; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo APP_URL; ?>/assets/css/styles.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css" rel="stylesheet"/>
    
    <style>
        :root {
            --sidebar-bg: #2c3e50;
            --content-bg: #f8f9fa;
            --navbar-bg: #ffffff;
            --text-color: #343a40;
            --text-muted: #6c757d;
            --primary-color: #0d6efd;
            --border-color: #dee2e6;
            --shadow-sm: 0 .125rem .25rem rgba(0,0,0,.075);
            --shadow: 0 .5rem 1rem rgba(0,0,0,.15);
            --font-family: 'Vazirmatn', sans-serif;
            --sidebar-text-color: #bdc3c7;
            --sidebar-icon-color: #7f8c8d;
            --sidebar-hover-bg: #34495e;
            --sidebar-active-bg: #34495e;
            --sidebar-active-text: #fff;
            --sidebar-heading-color: #95a5a6;
        }
        body {
            background-color: var(--content-bg);
            font-family: var(--font-family);
            color: var(--text-color);
            transition: margin-right 0.3s;
            line-height: 1.6;
        }
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: var(--sidebar-bg);
            color: #fff;
            transition: all 0.3s;
            z-index: 1030;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        #sidebar.toggled {
            margin-right: -250px;
        }
        #content-wrapper {
            width: 100%;
            transition: all 0.3s;
        }
        main {
            padding: 2rem;
        }
        .sidebar-header { padding: 20px; text-align: center; font-size: 22px; font-weight: 700; color: #fff; border-bottom: 1px solid #34495e;}
        .sidebar-header i { margin-left: 10px; }
        .sidebar-nav { padding: 15px 0; }
        .sidebar-nav .nav-item .nav-link {
            display: flex; align-items: center; padding: 12px 20px;
            color: var(--sidebar-text-color); border-radius: 0;
            margin-bottom: 5px; text-decoration: none; font-weight: 500;
        }
        .sidebar-nav .nav-link i { width: 20px; margin-left: 15px; font-size: 1rem; color: var(--sidebar-icon-color); }
        .sidebar-nav .nav-link:hover { background-color: var(--sidebar-hover-bg); color: #fff; }
        .sidebar-nav .nav-link.active { background-color: var(--sidebar-active-bg); color: var(--sidebar-active-text); }
        .sidebar-heading { padding: 1rem 1.5rem 0.5rem; font-size: 0.8rem; color: var(--sidebar-heading-color); text-transform: uppercase; letter-spacing: 0.5px; }
        .sidebar-submenu { background-color: rgba(0,0,0,0.1); padding-left: 30px; list-style: none; }
        .sidebar-submenu .submenu-item { display: block; padding: 10px 20px; color: var(--sidebar-text-color); text-decoration: none; font-size: 0.9rem; }
        .sidebar-submenu .submenu-item:hover { background-color: rgba(0,0,0,0.2); color: #fff; }
        .top-navbar {
            background: var(--navbar-bg); padding: 0.75rem 1.5rem;
            display: flex; justify-content: space-between; align-items: center;
            box-shadow: var(--shadow-sm);
        }
        .content-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 1020; display: none;
        }
        @media (max-width: 768px) {
            #sidebar {
                margin-right: -250px;
                position: fixed;
                height: 100%;
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
</body>
</html>
