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
    <link href="<?php echo APP_URL; ?>/assets/css/styles.css?v=3.5.0" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css" rel="stylesheet"/>
    
    <style>
/* ==========================================================================
   Nuxt 3 & Modern SaaS Commercial Masterpiece Design System for Hamakar ERP
   ========================================================================== */

:root {
    --primary: #4f46e5;
    --primary-hover: #4338ca;
    --primary-light: #eef2ff;
    --primary-border: #c7d2fe;
    --primary-glow: rgba(79, 70, 229, 0.16);

    --secondary: #0f172a;
    --secondary-light: #1e293b;
    --accent: #06b6d4;
    
    --success: #10b981;
    --success-light: #ecfdf5;
    --success-border: #a7f3d0;
    
    --warning: #f59e0b;
    --warning-light: #fffbeb;
    --warning-border: #fde68a;
    
    --danger: #ef4444;
    --danger-light: #fef2f2;
    --danger-border: #fecaca;
    
    --info: #0284c7;
    --info-light: #f0f9ff;
    --info-border: #bae6fd;

    --bg-body: #f8fafc;
    --bg-surface: #ffffff;
    --bg-subtle: #f1f5f9;

    --text-main: #0f172a;
    --text-muted: #64748b;
    --text-light: #94a3b8;

    --border-color: #e2e8f0;
    --border-subtle: #f1f5f9;
    --border-hover: #cbd5e1;

    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 18px;
    --radius-xl: 24px;
    --radius-full: 9999px;

    --shadow-subtle: 0 1px 3px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02);
    --shadow-card: 0 4px 20px -2px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
    --shadow-hover: 0 12px 28px -4px rgba(15, 23, 42, 0.08), 0 4px 8px -2px rgba(15, 23, 42, 0.03);
    --shadow-focus: 0 0 0 4px rgba(79, 70, 229, 0.12);
}

/* Global Font & Reset */
body {
    background-color: var(--bg-body) !important;
    color: var(--text-main) !important;
    font-family: 'Vazirmatn', sans-serif !important;
    font-feature-settings: "ss01", "ss02";
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    line-height: 1.6;
    margin: 0;
    padding: 0;
}

a {
    text-decoration: none;
    color: var(--primary);
    transition: color 0.2s;
}

a:hover {
    color: var(--primary-hover);
    text-decoration: none;
}

/* ==========================================================================
   1. Nuxt 3 Cards & Surfaces
   ========================================================================== */
.card, .card-modern, .stat-card {
    background: var(--bg-surface) !important;
    border: 1px solid var(--border-color) !important;
    border-radius: var(--radius-lg) !important;
    box-shadow: var(--shadow-card) !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    overflow: hidden;
}

.card:hover {
    box-shadow: var(--shadow-hover) !important;
    border-color: var(--border-hover) !important;
}

.card-header {
    background: var(--bg-surface) !important;
    border-bottom: 1px solid var(--border-subtle) !important;
    padding: 1.15rem 1.4rem !important;
    font-weight: 800 !important;
    font-size: 0.95rem !important;
    color: var(--text-main) !important;
}

.card-body {
    padding: 1.4rem !important;
}

.card-footer {
    background: #fafbfc !important;
    border-top: 1px solid var(--border-subtle) !important;
    padding: 1rem 1.4rem !important;
}

/* ==========================================================================
   2. Nuxt 3 Tables & Data Grids
   ========================================================================== */
.table-responsive, .table-responsive-modern {
    background: var(--bg-surface);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-card);
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    margin-bottom: 1.5rem;
}

.table {
    margin-bottom: 0 !important;
    width: 100%;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    vertical-align: middle;
}

.table thead, .table thead tr {
    background: #f8fafc !important;
}

.table thead th {
    background: #f8fafc !important;
    color: #475569 !important;
    font-weight: 700 !important;
    font-size: 0.8rem !important;
    letter-spacing: -0.2px !important;
    text-transform: uppercase !important;
    padding: 1rem 1.15rem !important;
    border-bottom: 1.5px solid var(--border-color) !important;
    border-top: none !important;
    white-space: nowrap !important;
}

.table tbody td {
    padding: 1rem 1.15rem !important;
    font-size: 0.885rem !important;
    color: var(--text-main) !important;
    border-bottom: 1px solid var(--border-subtle) !important;
    background: var(--bg-surface);
    transition: background-color 0.15s ease;
    vertical-align: middle;
}

.table tbody tr:hover td {
    background-color: #f8fafc !important;
}

.table tbody tr:last-child td {
    border-bottom: none !important;
}

.font-monospace {
    font-family: 'Consolas', 'Monaco', monospace !important;
    letter-spacing: 0.5px;
}

/* ==========================================================================
   3. Nuxt 3 Inputs & Form Controls
   ========================================================================== */
.form-control, .form-select, input[type="text"], input[type="password"], input[type="email"], input[type="number"], select, textarea {
    background-color: #f8fafc !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: var(--radius-md) !important;
    padding: 0.65rem 1rem !important;
    font-size: 0.885rem !important;
    font-family: inherit !important;
    color: var(--text-main) !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    box-shadow: none !important;
}

.form-control:focus, .form-select:focus, input:focus, select:focus, textarea:focus {
    background-color: #ffffff !important;
    border-color: var(--primary) !important;
    box-shadow: var(--shadow-focus) !important;
    outline: none !important;
}

.form-label, label {
    font-size: 0.82rem !important;
    font-weight: 700 !important;
    color: #334155 !important;
    margin-bottom: 0.45rem !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.4rem !important;
}

#filter-form, .filter-box, form.p-3.bg-light {
    background: #ffffff !important;
    border: 1px solid var(--border-color) !important;
    border-radius: var(--radius-lg) !important;
    box-shadow: var(--shadow-subtle) !important;
    padding: 1.25rem 1.4rem !important;
    margin-bottom: 1.5rem !important;
}

/* ==========================================================================
   4. Nuxt 3 Badges & Pills
   ========================================================================== */
.badge {
    border-radius: var(--radius-full) !important;
    font-weight: 700 !important;
    font-size: 0.74rem !important;
    padding: 0.35rem 0.8rem !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.35rem !important;
    letter-spacing: -0.2px !important;
}

.badge.bg-success, .badge.badge-success {
    background: var(--success-light) !important;
    color: #065f46 !important;
    border: 1px solid var(--success-border) !important;
}

.badge.bg-warning, .badge.badge-warning {
    background: var(--warning-light) !important;
    color: #92400e !important;
    border: 1px solid var(--warning-border) !important;
}

.badge.bg-danger, .badge.badge-danger {
    background: var(--danger-light) !important;
    color: #991b1b !important;
    border: 1px solid var(--danger-border) !important;
}

.badge.bg-primary, .badge.badge-primary, .badge.bg-info, .badge.badge-info {
    background: var(--primary-light) !important;
    color: #3730a3 !important;
    border: 1px solid var(--primary-border) !important;
}

.badge.bg-secondary, .badge.bg-light {
    background: #f1f5f9 !important;
    color: #475569 !important;
    border: 1px solid var(--border-color) !important;
}

/* ==========================================================================
   5. Nuxt 3 Buttons
   ========================================================================== */
.btn {
    border-radius: var(--radius-md) !important;
    font-weight: 700 !important;
    font-size: 0.84rem !important;
    padding: 0.55rem 1.15rem !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.45rem !important;
}

.btn:active {
    transform: scale(0.97) !important;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%) !important;
    border: none !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px var(--primary-glow) !important;
}

.btn-primary:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 18px rgba(79, 70, 229, 0.32) !important;
}

.btn-success {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%) !important;
    border: none !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.22) !important;
}

.btn-outline-primary {
    background: var(--primary-light) !important;
    color: var(--primary) !important;
    border: 1.5px solid var(--primary-border) !important;
}

.btn-outline-primary:hover {
    background: var(--primary) !important;
    color: #ffffff !important;
    border-color: var(--primary) !important;
}

/* ==========================================================================
   6. Suha Native Slide-up Bottom Sheet Drawer (Commercial Masterpiece)
   ========================================================================== */
.suha-sheet-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 2000;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.suha-sheet-backdrop.open {
    opacity: 1;
    visibility: visible;
}

.suha-bottom-sheet {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #ffffff;
    border-radius: 28px 28px 0 0;
    box-shadow: 0 -12px 45px rgba(15, 23, 42, 0.28);
    z-index: 2001;
    transform: translateY(105%);
    transition: transform 0.38s cubic-bezier(0.32, 0.72, 0, 1);
    max-height: 86vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-top: 1px solid rgba(255, 255, 255, 0.8);
}

.suha-bottom-sheet.open {
    transform: translateY(0);
}

.suha-sheet-handle {
    width: 48px;
    height: 5px;
    background: #cbd5e1;
    border-radius: 10px;
    margin: 14px auto 8px;
    cursor: pointer;
    transition: background 0.2s;
}

.suha-sheet-handle:hover {
    background: #94a3b8;
}

.suha-sheet-header {
    padding: 0.85rem 1.5rem 0.65rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #f1f5f9;
}

.suha-sheet-header h5 {
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    letter-spacing: -0.3px;
}

.suha-sheet-close {
    background: #f1f5f9;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #475569;
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    outline: none;
}

.suha-sheet-close:hover {
    background: #e2e8f0;
    color: #0f172a;
    transform: scale(1.05);
}

.suha-sheet-search {
    padding: 0.75rem 1.5rem;
    background: #fafbfc;
    border-bottom: 1px solid #f1f5f9;
}

.suha-sheet-search input {
    width: 100%;
    padding: 0.75rem 1.15rem;
    border: 1.5px solid #cbd5e1;
    border-radius: 14px;
    font-size: 0.88rem;
    font-family: inherit;
    background: #ffffff;
    color: #0f172a;
    outline: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}

.suha-sheet-search input:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
}

.suha-sheet-body {
    padding: 1.15rem 1.5rem 2.5rem;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.95rem;
}

@media (max-width: 480px) {
    .suha-sheet-body {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
}

.suha-module-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 18px !important;
    padding: 1rem 1.1rem !important;
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 0.85rem !important;
    text-decoration: none !important;
    color: #0f172a !important;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03) !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    position: relative;
    overflow: hidden;
}

.suha-module-card:hover, .suha-module-card:active {
    background: #f8fafc !important;
    border-color: #c7d2fe !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(79, 70, 229, 0.08) !important;
    text-decoration: none !important;
    color: #4f46e5 !important;
}

.suha-module-icon {
    width: 46px !important;
    height: 46px !important;
    border-radius: 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 1.25rem !important;
    flex-shrink: 0 !important;
}

.suha-module-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    text-align: right;
}

.suha-module-title {
    font-size: 0.9rem !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.suha-module-desc {
    font-size: 0.72rem !important;
    color: #64748b !important;
    line-height: 1.4 !important;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ==========================================================================
   7. Suha Mobile App Bottom Navigation Bar
   ========================================================================== */
.admin-suha-bottom-nav {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 68px;
    background: rgba(255, 255, 255, 0.96);
    backdrop-filter: blur(20px) saturate(180%);
    -webkit-backdrop-filter: blur(20px) saturate(180%);
    border-top: 1px solid rgba(226, 232, 240, 0.9);
    box-shadow: 0 -4px 25px rgba(15, 23, 42, 0.08);
    z-index: 1040;
    align-items: center;
    justify-content: space-around;
    padding: 0 0.5rem;
    padding-bottom: max(6px, env(safe-area-inset-bottom));
}

.admin-suha-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: none;
    border: none;
    text-decoration: none !important;
    color: #64748b !important;
    gap: 3px;
    font-size: 0.72rem;
    font-weight: 700;
    cursor: pointer;
    padding: 0.4rem 0;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    -webkit-tap-highlight-color: transparent;
    position: relative;
}

.admin-suha-item i {
    font-size: 1.25rem;
    transition: transform 0.2s ease, color 0.2s ease;
}

.admin-suha-item.active, .admin-suha-item:active {
    color: #4f46e5 !important;
}

.admin-suha-item:active i {
    transform: scale(0.9);
}

.admin-suha-item.active i {
    color: #4f46e5 !important;
}

/* Floating Action Center Button for Modules */
.admin-suha-fab {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
    color: #ffffff !important;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 22px rgba(79, 70, 229, 0.38);
    transform: translateY(-16px);
    border: 3.5px solid #ffffff;
    text-decoration: none !important;
    flex-shrink: 0;
    margin: 0 0.25rem;
    transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    animation: pulseFab 3s infinite ease-in-out;
    cursor: pointer;
}

.admin-suha-fab i {
    font-size: 1.2rem;
    color: #ffffff !important;
}

.admin-suha-fab span {
    font-size: 0.58rem;
    font-weight: 800;
    color: #ffffff !important;
    line-height: 1;
    margin-top: 2px;
}

.admin-suha-fab:active {
    transform: translateY(-12px) scale(0.92);
}

@keyframes pulseFab {
    0%, 100% { box-shadow: 0 8px 22px rgba(79, 70, 229, 0.38); }
    50% { box-shadow: 0 10px 28px rgba(6, 182, 212, 0.55); }
}

@media (max-width: 768px) {
    .admin-suha-bottom-nav { display: flex !important; }
    main { padding: 1.25rem 1rem 90px !important; }
    .chat-widget-toggler { bottom: 80px !important; }
    .chat-window { bottom: 150px !important; }
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


<!-- Suha Native Mobile Bottom App Navigation (Commercial Masterpiece) -->


<!-- Suha Admin Mobile Bottom Sheet Drawer for all 16 Modules (Ultra-Premium Commercial) -->



<!-- Suha Native Mobile Bottom App Navigation (Commercial Masterpiece) -->
<nav class="admin-suha-bottom-nav">
    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=dashboard" class="admin-suha-item <?php echo (($action ?? '') === 'dashboard') ? 'active' : ''; ?>">
        <i class="fas fa-house"></i>
        <span>پیشخوان</span>
    </a>
    <a href="<?php echo APP_URL; ?>/index.php?page=admin&action=moadianInvoices" class="admin-suha-item <?php echo (($action ?? '') === 'moadianInvoices') ? 'active' : ''; ?>">
        <i class="fas fa-file-invoice-dollar"></i>
        <span>مودیان</span>
    </a>
    
    <!-- Center Floating Action Button for All Modules -->
    <button type="button" class="admin-suha-fab" onclick="toggleAdminModulesDrawer()" title="زیرسیستم‌ها و ماژول‌ها">
        <i class="fas fa-cubes"></i>
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

<!-- Suha Admin Mobile Bottom Sheet Drawer for all 16 Modules (Ultra-Premium Commercial) -->
<div class="suha-sheet-backdrop" id="admin-sheet-backdrop" onclick="toggleAdminModulesDrawer()"></div>
<div class="suha-bottom-sheet" id="admin-modules-drawer">
    <div class="suha-sheet-handle" onclick="toggleAdminModulesDrawer()"></div>
    <div class="suha-sheet-header">
        <h5><i class="fas fa-cubes text-primary"></i> همه ماژول‌های سامانه هماکار</h5>
        <button type="button" class="suha-sheet-close" onclick="toggleAdminModulesDrawer()" aria-label="بستن">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="suha-sheet-search">
        <input type="text" id="admin-module-filter-input" placeholder="جستجوی سریع ماژول..." oninput="filterAdminModulesGrid(this.value)">
    </div>
    <div class="suha-sheet-body" id="admin-sheet-grid">
        
        <a href="index.php?page=admin&action=dashboard" class="suha-module-card" data-title="پیشخوان و مدیریت">
            <div class="suha-module-icon" style="background:#eef2ff; color:#4f46e5;"><i class="fas fa-home"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">پیشخوان اصلی</div>
                <div class="suha-module-desc">گزارشات و KPI‌های زنده</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=moadianInvoices" class="suha-module-card" data-title="سامانه مودیان مالیاتی">
            <div class="suha-module-icon" style="background:#fef2f2; color:#ef4444;"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">سامانه مودیان</div>
                <div class="suha-module-desc">کارپوشه مالیاتی رسمی</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=treasuryCheques" class="suha-module-card" data-title="چک صیادی و خزانه‌داری">
            <div class="suha-module-icon" style="background:#ecfdf5; color:#10b981;"><i class="fas fa-money-check-dollar"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">چک و خزانه‌داری</div>
                <div class="suha-module-desc">راس‌گیری و استعلام صیادی</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=treasuryRasgiri" class="suha-module-card" data-title="ماشین‌حساب راس‌گیری چک">
            <div class="suha-module-icon" style="background:#fffbeb; color:#d97706;"><i class="fas fa-calculator"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">راس‌گیری چک‌ها</div>
                <div class="suha-module-desc">محاسبه میانگین سررسید</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=contractGuarantees" class="suha-module-card" data-title="تضامین و وثایق">
            <div class="suha-module-icon" style="background:#eff6ff; color:#3b82f6;"><i class="fas fa-shield-halved"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">تضامین و وثایق</div>
                <div class="suha-module-desc">ضمانت‌نامه‌های بانکی</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=boms" class="suha-module-card" data-title="فرمول ساخت BOM">
            <div class="suha-module-icon" style="background:#fffbeb; color:#d97706;"><i class="fas fa-sitemap"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">فرمول ساخت (BOM)</div>
                <div class="suha-module-desc">درخت محصول و بهای تمام‌شده</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=productionOrders" class="suha-module-card" data-title="دستورات تولید">
            <div class="suha-module-icon" style="background:#f5f3ff; color:#7c3aed;"><i class="fas fa-industry"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">دستورات تولید</div>
                <div class="suha-module-desc">رهگیری خطوط و کارگاه</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=procurementRequisitions" class="suha-module-card" data-title="درخواست‌های خرید و تدارکات">
            <div class="suha-module-icon" style="background:#ecfeff; color:#0891b2;"><i class="fas fa-cart-flatbed"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">خرید و تدارکات</div>
                <div class="suha-module-desc">سفارشات خرید و تدارکات</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=aiAnalytics" class="suha-module-card" data-title="دستیار هوش مصنوعی">
            <div class="suha-module-icon" style="background:#fae8ff; color:#a21caf;"><i class="fas fa-brain"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">هوش مصنوعی AI</div>
                <div class="suha-module-desc">تحلیل سود و پیش‌بینی</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=accounting" class="suha-module-card" data-title="حسابداری مالی دوبل">
            <div class="suha-module-icon" style="background:#eef2ff; color:#4f46e5;"><i class="fas fa-receipt"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">حسابداری مالی</div>
                <div class="suha-module-desc">دفاتر روزنامه، کل و تراز</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=invoices" class="suha-module-card" data-title="فاکتورها و فروش">
            <div class="suha-module-icon" style="background:#fdf2f8; color:#db2777;"><i class="fas fa-file-invoice"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">فاکتورها</div>
                <div class="suha-module-desc">فاکتورهای فروش و رسمی</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=contracts" class="suha-module-card" data-title="قراردادها و پیمان‌ها">
            <div class="suha-module-icon" style="background:#eff6ff; color:#2563eb;"><i class="fas fa-file-contract"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">قراردادها</div>
                <div class="suha-module-desc">صورت‌وضعیت و تعهدات</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=warehouses" class="suha-module-card" data-title="انبارداری">
            <div class="suha-module-icon" style="background:#f0fdf4; color:#16a34a;"><i class="fas fa-warehouse"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">انبارداری</div>
                <div class="suha-module-desc">کاردکس کالا، رسید و حواله</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=customers" class="suha-module-card" data-title="مشتریان و CRM">
            <div class="suha-module-icon" style="background:#fdf2f8; color:#be185d;"><i class="fas fa-users"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">مشتریان و CRM</div>
                <div class="suha-module-desc">باشگاه مشتریان و فروش</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=hr" class="suha-module-card" data-title="منابع انسانی و پرسنلی">
            <div class="suha-module-icon" style="background:#fff7ed; color:#c2410c;"><i class="fas fa-user-tie"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">منابع انسانی HR</div>
                <div class="suha-module-desc">احکام، کارمندان و فیش</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=reports" class="suha-module-card" data-title="گزارشات و چاپ A4">
            <div class="suha-module-icon" style="background:#f8fafc; color:#334155;"><i class="fas fa-print"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">گزارشات جامع</div>
                <div class="suha-module-desc">چاپ رسمی و خروجی اکسل</div>
            </div>
        </a>

    </div>
</div>

<script>
function toggleAdminModulesDrawer() {
    const backdrop = document.getElementById('admin-sheet-backdrop');
    const drawer = document.getElementById('admin-modules-drawer');
    if (!backdrop || !drawer) return;
    
    // Haptic feedback
    if (window.navigator && window.navigator.vibrate) {
        window.navigator.vibrate(12);
    }
    
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
        }, 250);
    }
}

function filterAdminModulesGrid(query) {
    const q = (query || '').trim().toLowerCase();
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
