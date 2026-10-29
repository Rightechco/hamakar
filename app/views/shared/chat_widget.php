<?php
// app/views/shared/chat_widget.php - پیام‌رسان درون‌سازمانی فوق پرمیوم هماکار ERP
global $auth;
$currentUser = $auth->user();
?>
<style>
/* ==========================================================================
   Hamakar Enterprise Realtime Chat Widget (Nuxt 3 & Suha Native Design)
   ========================================================================== */
.chat-widget-toggler {
    position: fixed;
    bottom: 22px;
    right: 22px;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
    color: #ffffff !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(79, 70, 229, 0.4);
    cursor: pointer;
    z-index: 1045;
    border: 3px solid #ffffff;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    outline: none;
}
.chat-widget-toggler:hover {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 12px 30px rgba(79, 70, 229, 0.55);
}
.chat-widget-toggler:active {
    transform: scale(0.94);
}
.chat-widget-toggler i {
    font-size: 1.45rem;
}
.chat-unread-dot {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 14px;
    height: 14px;
    background: #ef4444;
    border-radius: 50%;
    border: 2px solid #ffffff;
    display: none;
    animation: pulseDot 2s infinite ease-in-out;
}

/* Chat Window Container */
.hamakar-chat-window {
    position: fixed;
    bottom: 90px;
    right: 24px;
    width: 375px;
    max-width: calc(100vw - 28px);
    height: 540px;
    max-height: calc(100vh - 120px);
    background: #ffffff;
    border-radius: 24px;
    box-shadow: 0 18px 50px rgba(15, 23, 42, 0.22), 0 0 0 1px rgba(226, 232, 240, 0.8);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 1050;
    transform: translateY(20px) scale(0.95);
    opacity: 0;
    pointer-events: none;
    visibility: hidden;
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}
.hamakar-chat-window.open {
    transform: translateY(0) scale(1);
    opacity: 1;
    pointer-events: auto;
    visibility: visible;
}

/* Chat Header */
.hamakar-chat-header {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
    color: #ffffff;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}
.chat-header-title {
    display: flex;
    align-items: center;
    gap: 10px;
    overflow: hidden;
}
.chat-header-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.95rem;
    flex-shrink: 0;
    border: 1.5px solid rgba(255, 255, 255, 0.4);
}
.chat-header-info {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    text-align: right;
}
.chat-header-name {
    font-size: 0.92rem;
    font-weight: 800;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.3;
}
.chat-header-status {
    font-size: 0.7rem;
    color: #cbd5e1;
    display: flex;
    align-items: center;
    gap: 4px;
}
.chat-header-status i {
    font-size: 0.5rem;
}
.chat-header-actions button {
    background: rgba(255, 255, 255, 0.12);
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    color: #ffffff;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
}
.chat-header-actions button:hover {
    background: rgba(255, 255, 255, 0.25);
}

/* Chat Body (Contacts vs Messages) */
.hamakar-chat-body {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
    background: #f8fafc;
}

/* View 1: Contacts List */
.chat-contacts-view {
    display: flex;
    flex-direction: column;
    height: 100%;
}
.chat-search-bar {
    padding: 10px 14px;
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
}
.chat-search-bar input {
    width: 100%;
    padding: 8px 12px;
    border-radius: 12px;
    border: 1.5px solid #cbd5e1;
    font-size: 0.82rem;
    outline: none;
}
.chat-search-bar input:focus {
    border-color: #4f46e5;
}
.chat-users-list {
    flex: 1;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 8px;
}
.chat-user-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 14px;
    background: #ffffff;
    margin-bottom: 6px;
    cursor: pointer;
    border: 1px solid #f1f5f9;
    transition: all 0.2s;
}
.chat-user-item:hover {
    background: #eef2ff;
    border-color: #c7d2fe;
    transform: translateX(-2px);
}
.chat-user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #4f46e5;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.9rem;
    position: relative;
    flex-shrink: 0;
}
.chat-user-status-dot {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid #ffffff;
}
.chat-user-status-dot.online { background: #10b981; }
.chat-user-status-dot.offline { background: #94a3b8; }
.chat-user-meta {
    flex: 1;
    overflow: hidden;
    text-align: right;
}
.chat-user-name {
    font-size: 0.86rem;
    font-weight: 700;
    color: #0f172a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.chat-user-role {
    font-size: 0.72rem;
    color: #64748b;
}

/* View 2: Conversation View */
.chat-conversation-view {
    display: none;
    flex-direction: column;
    height: 100%;
}
.chat-messages-container {
    flex: 1;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 14px 14px 8px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.chat-msg-row {
    display: flex;
    flex-direction: column;
    max-width: 82%;
}
.chat-msg-row.sent {
    align-self: flex-start; /* in RTL, flex-start is right */
}
.chat-msg-row.received {
    align-self: flex-end; /* in RTL, flex-end is left */
}
.chat-bubble {
    padding: 10px 14px;
    border-radius: 18px;
    font-size: 0.85rem;
    line-height: 1.5;
    word-break: break-word;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.chat-msg-row.sent .chat-bubble {
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    color: #ffffff;
    border-bottom-right-radius: 4px;
}
.chat-msg-row.received .chat-bubble {
    background: #ffffff;
    color: #0f172a;
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 4px;
}
.chat-msg-time {
    font-size: 0.65rem;
    color: #94a3b8;
    margin-top: 3px;
    padding: 0 4px;
}
.chat-msg-file {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    border-radius: 10px;
    margin-top: 6px;
    background: rgba(0,0,0,0.12);
    text-decoration: none !important;
    color: inherit;
    font-size: 0.78rem;
}
.chat-msg-file:hover {
    background: rgba(0,0,0,0.2);
}
.chat-msg-img {
    max-width: 100%;
    border-radius: 12px;
    margin-top: 6px;
    cursor: pointer;
}

/* Chat Footer / Input Bar */
.chat-input-bar {
    padding: 10px 14px;
    background: #ffffff;
    border-top: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.chat-file-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.95rem;
    flex-shrink: 0;
}
.chat-file-btn:hover {
    background: #e2e8f0;
    color: #4f46e5;
}
.chat-text-input {
    flex: 1;
    padding: 8px 12px;
    border-radius: 12px;
    border: 1.5px solid #cbd5e1;
    font-size: 0.85rem;
    outline: none;
    background: #f8fafc;
}
.chat-text-input:focus {
    background: #ffffff;
    border-color: #4f46e5;
}
.chat-send-btn {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #4f46e5;
    color: #ffffff;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.9rem;
    flex-shrink: 0;
}
.chat-send-btn:hover {
    background: #4338ca;
    transform: scale(1.05);
}
.chat-file-preview-bar {
    display: none;
    padding: 6px 14px;
    background: #eef2ff;
    border-top: 1px solid #c7d2fe;
    align-items: center;
    justify-content: space-between;
    font-size: 0.75rem;
    color: #4f46e5;
}

@media (max-width: 768px) {
    .chat-widget-toggler {
        bottom: 78px !important;
        right: 14px !important;
        width: 50px !important;
        height: 50px !important;
    }
    .chat-widget-toggler i { font-size: 1.25rem !important; }
    .hamakar-chat-window {
        bottom: 142px !important;
        right: 10px !important;
        left: 10px !important;
        width: auto !important;
        max-width: calc(100vw - 20px) !important;
        height: 65vh !important;
        border-radius: 20px !important;
    }
}
</style>

<!-- Floating Chat Trigger Button -->
<button type="button" class="chat-widget-toggler" id="chat-toggler" onclick="toggleHamakarChat()" title="پیام‌رسان سازمانی هماکار">
    <i class="fas fa-comments"></i>
    <span class="chat-unread-dot" id="chat-unread-dot"></span>
</button>

<!-- Slide-Up Floating Chat Window -->
<div class="hamakar-chat-window" id="hamakar-chat-window">
    <!-- Header -->
    <div class="hamakar-chat-header">
        <div class="chat-header-title">
            <button type="button" id="chat-back-btn" style="display:none; background:none; border:none; color:#fff; font-size:1.1rem; padding:0 4px; cursor:pointer;" onclick="showContactsView()" title="بازگشت به مخاطبین">
                <i class="fas fa-arrow-right"></i>
            </button>
            <div class="chat-header-avatar" id="chat-header-avatar">
                <i class="fas fa-users"></i>
            </div>
            <div class="chat-header-info">
                <div class="chat-header-name" id="chat-header-name">پیام‌رسان سازمانی هماکار</div>
                <div class="chat-header-status" id="chat-header-status">
                    <i class="fas fa-circle text-success"></i>
                    <span>آماده گفتگو</span>
                </div>
            </div>
        </div>
        <div class="chat-header-actions">
            <button type="button" onclick="toggleHamakarChat()" title="بستن پنجره">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Body Container -->
    <div class="hamakar-chat-body">
        
        <!-- View 1: Contacts List -->
        <div class="chat-contacts-view" id="chat-contacts-view">
            <div class="chat-search-bar">
                <input type="text" id="chat-user-search" placeholder="جستجوی همکاران یا کاربران..." oninput="filterChatUsers(this.value)">
            </div>
            <div class="chat-users-list" id="chat-users-list">
                <div class="text-center py-4 text-muted small">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2 text-primary"></i>
                    <div>در حال دریافت لیست کاربران...</div>
                </div>
            </div>
        </div>

        <!-- View 2: Active Conversation View -->
        <div class="chat-conversation-view" id="chat-conversation-view">
            <div class="chat-messages-container" id="chat-messages-container">
                <!-- Messages populated via JS -->
            </div>

            <!-- File attachment preview indicator -->
            <div class="chat-file-preview-bar" id="chat-file-preview-bar">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <i class="fas fa-paperclip"></i>
                    <span id="chat-preview-filename" class="text-truncate"></span>
                </div>
                <button type="button" class="btn-close btn-close-sm" onclick="clearChatFileAttachment()"></button>
            </div>

            <!-- Message Input Bar -->
            <div class="chat-input-bar">
                <label for="chat-file-input" class="chat-file-btn" title="پیوست تصویر یا سند">
                    <i class="fas fa-paperclip"></i>
                </label>
                <input type="file" id="chat-file-input" style="display:none;" onchange="handleChatFileSelect(this)">
                
                <input type="text" id="chat-message-input" class="chat-text-input" placeholder="پیام خود را بنویسید..." onkeydown="if(event.key==='Enter') sendChatMessage()">
                
                <button type="button" class="chat-send-btn" onclick="sendChatMessage()" title="ارسال پیام">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
let activeChatPartnerId = null;
let activeChatPartnerName = '';
let activeChatLastId = 0;
let chatPollInterval = null;
let allChatUsers = [];

function toggleHamakarChat() {
    const win = document.getElementById('hamakar-chat-window');
    if (!win) return;
    
    if (win.classList.contains('open')) {
        win.classList.remove('open');
        if (chatPollInterval) clearInterval(chatPollInterval);
    } else {
        win.classList.add('open');
        document.getElementById('chat-unread-dot').style.display = 'none';
        if (!activeChatPartnerId) {
            loadChatUsers();
        } else {
            startChatPolling();
        }
    }
}

function showContactsView() {
    activeChatPartnerId = null;
    if (chatPollInterval) clearInterval(chatPollInterval);
    document.getElementById('chat-contacts-view').style.display = 'flex';
    document.getElementById('chat-conversation-view').style.display = 'none';
    document.getElementById('chat-back-btn').style.display = 'none';
    document.getElementById('chat-header-name').innerText = 'پیام‌رسان سازمانی هماکار';
    document.getElementById('chat-header-status').innerHTML = '<i class="fas fa-circle text-success"></i><span>آماده گفتگو</span>';
    document.getElementById('chat-header-avatar').innerHTML = '<i class="fas fa-users"></i>';
    loadChatUsers();
}

async function loadChatUsers() {
    const container = document.getElementById('chat-users-list');
    try {
        const res = await fetch('index.php?page=ajax&action=get_users');
        const data = await res.json();
        if (data.status === 'success' && data.users) {
            allChatUsers = data.users;
            renderChatUsers(allChatUsers);
        } else {
            container.innerHTML = '<div class="text-center py-4 text-muted small">کاربری یافت نشد.</div>';
        }
    } catch (e) {
        container.innerHTML = '<div class="text-center py-4 text-danger small">خطا در بارگذاری کاربران.</div>';
    }
}

function renderChatUsers(users) {
    const container = document.getElementById('chat-users-list');
    if (!users || users.length === 0) {
        container.innerHTML = '<div class="text-center py-4 text-muted small">هیچ کاربری با این مشخصات یافت نشد.</div>';
        return;
    }
    let html = '';
    users.forEach(u => {
        const isOnline = u.status_color === 'success';
        const initial = (u.name || 'U').trim().charAt(0);
        html += `
            <div class="chat-user-item" onclick="openChatWithUser(${u.id}, '${escapeHtml(u.name)}', '${isOnline ? 'آنلاین' : 'آفلاین'}')">
                <div class="chat-user-avatar">
                    <span>${initial}</span>
                    <span class="chat-user-status-dot ${isOnline ? 'online' : 'offline'}"></span>
                </div>
                <div class="chat-user-meta">
                    <div class="chat-user-name">${escapeHtml(u.name)}</div>
                    <div class="chat-user-role">${escapeHtml(u.role || 'کاربر سیستم')} • <span class="${isOnline ? 'text-success fw-bold' : 'text-muted'}">${u.online_status}</span></div>
                </div>
                <i class="fas fa-chevron-left text-muted small"></i>
            </div>
        `;
    });
    container.innerHTML = html;
}

function filterChatUsers(q) {
    const query = (q || '').trim().toLowerCase();
    const filtered = allChatUsers.filter(u => (u.name || '').toLowerCase().includes(query) || (u.email || '').toLowerCase().includes(query));
    renderChatUsers(filtered);
}

function openChatWithUser(userId, userName, statusText) {
    activeChatPartnerId = userId;
    activeChatPartnerName = userName;
    activeChatLastId = 0;

    document.getElementById('chat-contacts-view').style.display = 'none';
    document.getElementById('chat-conversation-view').style.display = 'flex';
    document.getElementById('chat-back-btn').style.display = 'inline-block';
    document.getElementById('chat-header-name').innerText = userName;
    document.getElementById('chat-header-avatar').innerText = userName.charAt(0);
    document.getElementById('chat-header-status').innerHTML = `
        <i class="fas fa-circle ${statusText === 'آنلاین' ? 'text-success' : 'text-secondary'}"></i>
        <span>${statusText}</span>
    `;

    document.getElementById('chat-messages-container').innerHTML = `
        <div class="text-center py-5 text-muted small">
            <i class="fas fa-spinner fa-spin me-1 text-primary"></i> در حال بارگذاری گفتگو...
        </div>
    `;

    fetchConversationMessages(true);
    startChatPolling();
}

async function fetchConversationMessages(scrollBottom = false) {
    if (!activeChatPartnerId) return;
    try {
        const res = await fetch(`index.php?page=ajax&action=fetch_messages&partner_id=${activeChatPartnerId}&last_id=${activeChatLastId}`);
        const data = await res.json();
        if (data.status === 'success' && data.messages) {
            const container = document.getElementById('chat-messages-container');
            if (activeChatLastId === 0) {
                container.innerHTML = '';
                if (data.messages.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-5 text-muted small">
                            <i class="far fa-comment-dots fa-3x mb-2 text-primary opacity-50 d-block"></i>
                            هنوز پیامی رد و بدل نشده است.<br>اولین پیام را ارسال نمایید!
                        </div>
                    `;
                }
            }

            data.messages.forEach(msg => {
                if (msg.id > activeChatLastId) {
                    activeChatLastId = msg.id;
                    appendMessageBubble(msg);
                }
            });

            if (scrollBottom || data.messages.length > 0) {
                container.scrollTop = container.scrollHeight;
            }
        }
    } catch(e) {
        console.warn('Error fetching messages:', e);
    }
}

function appendMessageBubble(msg) {
    const container = document.getElementById('chat-messages-container');
    const isSent = msg.is_sender;
    const row = document.createElement('div');
    row.className = `chat-msg-row ${isSent ? 'sent' : 'received'}`;

    let fileHtml = '';
    if (msg.file_path) {
        const isImg = /\.(jpg|jpeg|png|gif|webp)$/i.test(msg.file_path);
        if (isImg) {
            fileHtml = `<a href="${msg.file_path}" target="_blank"><img src="${msg.file_path}" class="chat-msg-img" alt="پیوست تصویر"></a>`;
        } else {
            fileHtml = `
                <a href="${msg.file_path}" target="_blank" class="chat-msg-file">
                    <i class="fas fa-file-arrow-down fa-lg"></i>
                    <span class="text-truncate">${escapeHtml(msg.file_name || 'دانلود فایل پیوست')}</span>
                </a>
            `;
        }
    }

    const timeStr = msg.created_at ? msg.created_at.slice(11, 16) : '';

    row.innerHTML = `
        <div class="chat-bubble">
            ${msg.message_text ? `<div>${escapeHtml(msg.message_text)}</div>` : ''}
            ${fileHtml}
        </div>
        <div class="chat-msg-time ${isSent ? 'text-end' : 'text-start'}">${timeStr}</div>
    `;

    container.appendChild(row);
}

function handleChatFileSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('chat-preview-filename').innerText = file.name;
        document.getElementById('chat-file-preview-bar').style.display = 'flex';
    }
}

function clearChatFileAttachment() {
    const input = document.getElementById('chat-file-input');
    if (input) input.value = '';
    document.getElementById('chat-file-preview-bar').style.display = 'none';
    document.getElementById('chat-preview-filename').innerText = '';
}

async function sendChatMessage() {
    const textInput = document.getElementById('chat-message-input');
    const fileInput = document.getElementById('chat-file-input');
    const text = (textInput.value || '').trim();
    const hasFile = fileInput.files && fileInput.files[0];

    if (!text && !hasFile) return;
    if (!activeChatPartnerId) return;

    const formData = new FormData();
    formData.append('receiver_id', activeChatPartnerId);
    formData.append('message_text', text);
    if (hasFile) {
        formData.append('attachment', fileInput.files[0]);
    }

    textInput.value = '';
    clearChatFileAttachment();

    try {
        const res = await fetch('index.php?page=ajax&action=send_message', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.status === 'success') {
            fetchConversationMessages(true);
        } else {
            alert(data.message || 'خطا در ارسال پیام');
        }
    } catch(e) {
        alert('خطا در ارتباط با سرور.');
    }
}

function startChatPolling() {
    if (chatPollInterval) clearInterval(chatPollInterval);
    chatPollInterval = setInterval(() => {
        if (document.getElementById('hamakar-chat-window').classList.contains('open') && activeChatPartnerId) {
            fetchConversationMessages(false);
        }
    }, 3000);
}

function escapeHtml(text) {
    if (!text) return '';
    return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
</script>
