<?php
// app/views/layouts/admin_layout.php - نسخه تجاری نیتیو هماکار ERP
global $auth;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="https://hamakar.ir/manifest.webmanifest">
    <link rel="apple-touch-icon" href="https://hamakar.ir/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="https://hamakar.ir/images/favicon-32x32.png">

    <title><?php echo sanitize($title ?? 'پنل مدیریت'); ?> - هماکار ERP</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo APP_URL; ?>/assets/css/styles.css?v=4.1.0" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css" rel="stylesheet"/>
    
    <style>
/* ==========================================================================
   Comprehensive Nuxt 3 & Suha Native Commercial Masterpiece for Hamakar
   ========================================================================== */

:root {
    --primary: #4f46e5;
    --primary-hover: #4338ca;
    --primary-light: #eef2ff;
    --primary-border: #c7d2fe;
    --primary-glow: rgba(79, 70, 229, 0.16);

    --sidebar-bg: #0f172a;
    --sidebar-text: #94a3b8;
    --sidebar-icon: #64748b;
    --sidebar-hover-bg: rgba(255, 255, 255, 0.06);
    --sidebar-active-bg: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
    --sidebar-active-text: #ffffff;
    --sidebar-heading: #64748b;

    --navbar-bg: rgba(255, 255, 255, 0.94);
    --bg-body: #f8fafc;
    --bg-surface: #ffffff;
    --bg-subtle: #f1f5f9;

    --text-main: #0f172a;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --border-subtle: #f1f5f9;

    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 18px;
    --radius-xl: 24px;
    --radius-full: 9999px;

    --shadow-subtle: 0 1px 3px rgba(15, 23, 42, 0.04);
    --shadow-card: 0 4px 20px -2px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
    --shadow-hover: 0 12px 28px -4px rgba(15, 23, 42, 0.08);
}

html {
    overflow-x: hidden !important;
    width: 100% !important;
    max-width: 100vw !important;
    position: relative !important;
    margin: 0 !important;
    padding: 0 !important;
    box-sizing: border-box !important;
    -webkit-text-size-adjust: 100% !important;
}

*, *::before, *::after {
    box-sizing: inherit !important;
}

body {
    background-color: var(--bg-body) !important;
    color: var(--text-main) !important;
    font-family: 'Vazirmatn', sans-serif !important;
    font-feature-settings: "ss01", "ss02";
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    line-height: 1.6;
    margin: 0 !important;
    padding: 0 !important;
    overflow-x: hidden !important;
    width: 100% !important;
    max-width: 100vw !important;
    position: relative !important;
    min-height: 100vh !important;
}

a {
    text-decoration: none;
    color: var(--primary);
}

/* ==========================================================================
   1. Master Layout Shell (Sidebar, Content & Overlay)
   ========================================================================== */
.wrapper {
    display: flex;
    width: 100%;
    min-height: 100vh;
    align-items: stretch;
    position: relative;
}

#sidebar {
    min-width: 260px;
    max-width: 260px;
    background: var(--sidebar-bg);
    color: #fff;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    z-index: 1030;
    box-shadow: 4px 0 24px rgba(15, 23, 42, 0.08);
    display: flex;
    flex-direction: column;
}

#sidebar.toggled {
    margin-right: -260px;
}

#content-wrapper {
    width: 100%;
    min-width: 0;
    display: flex;
    flex-direction: column;
    background-color: var(--bg-body);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

main {
    padding: 1.75rem 2rem 100px;
    flex: 1;
}

.content-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 1055;
    display: none;
}

.sidebar-header { 
    padding: 22px 20px; 
    text-align: center; 
    font-size: 19px; 
    font-weight: 800; 
    color: #fff; 
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    letter-spacing: -0.3px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.sidebar-header i { color: #4f46e5; }

.sidebar-nav {
    padding: 15px 0;
    list-style: none;
    margin: 0;
}

.sidebar-nav .nav-item .nav-link {
    display: flex; 
    align-items: center; 
    padding: 10px 18px;
    color: var(--sidebar-text); 
    border-radius: 12px;
    margin: 3px 12px; 
    text-decoration: none; 
    font-weight: 600;
    font-size: 0.88rem;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.sidebar-nav .nav-link i {
    width: 20px;
    margin-left: 14px;
    font-size: 1rem;
    color: var(--sidebar-icon);
}

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
    color: var(--sidebar-heading); 
    text-transform: uppercase; 
    letter-spacing: 0.5px;
    font-weight: 800;
}

.sidebar-submenu { 
    background-color: rgba(0,0,0,0.2); 
    padding-left: 0; 
    list-style: none; 
    margin: 4px 12px; 
    border-radius: 10px; 
    overflow: hidden; 
}

.sidebar-submenu .submenu-item { 
    display: block; 
    padding: 9px 20px 9px 15px; 
    color: var(--sidebar-text); 
    text-decoration: none; 
    font-size: 0.84rem; 
    font-weight: 500; 
    transition: all 0.15s; 
}

.sidebar-submenu .submenu-item:hover { 
    background-color: rgba(255,255,255,0.06); 
    color: #fff; 
}

/* CRITICAL MOBILE SIDEBAR & VIEWPORT ISOLATION */
@media (max-width: 768px) {
    #sidebar {
        position: fixed !important;
        top: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        height: 100vh !important;
        width: 280px !important;
        max-width: 85vw !important;
        margin-right: 0 !important;
        transform: translateX(105%) !important;
        visibility: hidden !important;
        pointer-events: none !important;
        z-index: 1060 !important;
        box-shadow: -8px 0 30px rgba(0,0,0,0.35) !important;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    .sidebar-nav {
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        padding-bottom: 120px !important;
        flex: 1 1 auto !important;
    }
    
    #sidebar.toggled {
        transform: translateX(0) !important;
        visibility: visible !important;
        pointer-events: auto !important;
    }

    .content-overlay.active {
        display: block !important;
    }

    main {
        padding: 0.85rem 0.5rem 100px !important;
    }
}

/* ==========================================================================
   2. Nuxt 3 Cards, KPI Widgets & Surfaces
   ========================================================================== */
.card, .card-modern, .stat-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 18px !important;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04) !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    overflow: hidden;
    margin-bottom: 1.25rem;
}

.card:hover {
    box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.08) !important;
    border-color: #cbd5e1 !important;
}

.card-header {
    background: #ffffff !important;
    border-bottom: 1px solid #f1f5f9 !important;
    padding: 1.15rem 1.4rem !important;
    font-weight: 800 !important;
    font-size: 0.95rem !important;
    color: #0f172a !important;
}

.card-body {
    padding: 1.4rem !important;
}

.nuxt-kpi-card {
    background: #ffffff !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 20px !important;
    padding: 1.35rem 1.4rem !important;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04) !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    position: relative;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.nuxt-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.08) !important;
    border-color: #c7d2fe !important;
}

.nuxt-kpi-icon {
    width: 48px !important;
    height: 48px !important;
    border-radius: 14px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 1.35rem !important;
    flex-shrink: 0;
}

.nuxt-kpi-value {
    font-size: 1.75rem !important;
    font-weight: 900 !important;
    color: #0f172a !important;
    line-height: 1.2 !important;
    letter-spacing: -0.5px !important;
}

.nuxt-kpi-label {
    font-size: 0.85rem !important;
    color: #64748b !important;
    font-weight: 700 !important;
    margin-bottom: 0.35rem !important;
}

/* ==========================================================================
   3. Nuxt 3 Tables & Data Grids
   ========================================================================== */
.table-responsive, .table-responsive-modern {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04);
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
    border-bottom: 1.5px solid #e2e8f0 !important;
    border-top: none !important;
    white-space: nowrap !important;
}

.table tbody td {
    padding: 1rem 1.15rem !important;
    font-size: 0.885rem !important;
    color: #0f172a !important;
    border-bottom: 1px solid #f1f5f9 !important;
    background: #ffffff;
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
   4. Form Controls & Buttons
   ========================================================================== */
.form-control, .form-select, input[type="text"], input[type="password"], input[type="email"], input[type="number"], select, textarea {
    background-color: #f8fafc !important;
    border: 1.5px solid #cbd5e1 !important;
    border-radius: 12px !important;
    padding: 0.65rem 1rem !important;
    font-size: 0.885rem !important;
    font-family: inherit !important;
    color: #0f172a !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    box-shadow: none !important;
}

.form-control:focus, .form-select:focus, input:focus, select:focus, textarea:focus {
    background-color: #ffffff !important;
    border-color: #4f46e5 !important;
    box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12) !important;
    outline: none !important;
}

.btn {
    border-radius: 12px !important;
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
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important;
    border: none !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.22) !important;
}

.btn-primary:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 18px rgba(79, 70, 229, 0.32) !important;
}

.btn-outline-primary {
    background: #eef2ff !important;
    color: #4f46e5 !important;
    border: 1.5px solid #c7d2fe !important;
}

.badge {
    border-radius: 9999px !important;
    font-weight: 700 !important;
    font-size: 0.74rem !important;
    padding: 0.35rem 0.8rem !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.35rem !important;
}

/* ==========================================================================
   5. Suha Native Slide-up Bottom Sheet Drawer (Commercial Masterpiece)
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
    border: 1.5px solid #e2e8f0 !important;
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
   6. Suha Mobile App Bottom Navigation Bar
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

/* ==========================================================================
   Comprehensive Zero-Overflow & Native App Mobile Overrides
   ========================================================================== */
#hamakar-offline-widget {
    position: fixed !important;
    bottom: 20px;
    left: 20px;
    z-index: 1035;
    max-width: calc(100vw - 32px) !important;
    box-sizing: border-box !important;
}

@media (max-width: 768px) {
    .admin-suha-bottom-nav { display: flex !important; }
    
    .top-navbar {
        padding: 0.5rem 0.75rem !important;
        height: 58px !important;
    }

    /* Bootstrap Row Zero Bleed */
    .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
        max-width: 100% !important;
    }
    .row > * {
        padding-left: 0.35rem !important;
        padding-right: 0.35rem !important;
        max-width: 100% !important;
    }
    .container-fluid, .container {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    main {
        padding: 0.85rem 0.5rem 100px !important;
    }

    /* Card & Surfaces Containment */
    .card, .moadian-card, .settings-card, .card-modern {
        margin-left: 0 !important;
        margin-right: 0 !important;
        max-width: 100% !important;
        border-radius: 16px !important;
    }
    .card-body, .moadian-card, .settings-card {
        padding: 1rem 0.75rem !important;
    }
    .card-header {
        padding: 0.85rem 0.75rem !important;
    }

    /* Silky Smooth Horizontal Swipe Pills for Tabs */
    .nav-pills, .nav-tabs, .nav-pills-moadian, .nav-pills-settings {
        display: flex !important;
        flex-wrap: nowrap !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
        scrollbar-width: none !important;
        -ms-overflow-style: none !important;
        gap: 0.4rem !important;
        padding: 0.4rem !important;
        border-radius: 14px !important;
        max-width: 100% !important;
        white-space: nowrap !important;
    }
    .nav-pills::-webkit-scrollbar, .nav-tabs::-webkit-scrollbar,
    .nav-pills-moadian::-webkit-scrollbar, .nav-pills-settings::-webkit-scrollbar {
        display: none !important;
        height: 0 !important;
    }
    .nav-pills .nav-item, .nav-tabs .nav-item {
        flex: 0 0 auto !important;
    }
    .nav-pills .nav-link, .nav-tabs .nav-link {
        font-size: 0.78rem !important;
        padding: 0.5rem 0.85rem !important;
        border-radius: 10px !important;
    }

    /* Header Actions Auto-Wrap */
    .dashboard-header-actions, .page-header-actions, .header-actions {
        width: 100% !important;
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 0.45rem !important;
    }
    .dashboard-header-actions .btn, .page-header-actions .btn, .header-actions .btn {
        flex: 1 1 calc(50% - 0.45rem) !important;
        min-width: 110px !important;
        padding: 0.5rem 0.4rem !important;
        font-size: 0.76rem !important;
        text-align: center !important;
        white-space: nowrap !important;
    }

    .badge {
        max-width: 100% !important;
        white-space: normal !important;
        text-align: center !important;
        line-height: 1.35 !important;
    }

    /* Tables Auto-Scroll */
    table.table:not(.table-responsive table):not(.datepicker-table) {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }

    /* Floating Widgets */
    #hamakar-offline-widget {
        bottom: 78px !important;
        left: 10px !important;
        right: auto !important;
        padding: 5px 10px !important;
        font-size: 0.7rem !important;
        gap: 6px !important;
        max-width: calc(100vw - 20px) !important;
    }
    #hamakar-offline-widget span#network-status-text {
        max-width: 100px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .chat-widget-toggler { bottom: 78px !important; right: 12px !important; }
    .chat-window {
        bottom: 145px !important;
        right: 10px !important;
        left: 10px !important;
        width: auto !important;
        max-width: calc(100vw - 20px) !important;
    }
}

    </style>
</head>
<body>

<div class="wrapper">
    <?php include_once __DIR__ . '/../shared/sidebar.php'; ?>
    <div id="content-wrapper">
        <div class="content-overlay" id="content-overlay" onclick="toggleSidebarMenu()"></div>
        <?php include_once __DIR__ . '/../shared/navbar.php'; ?>
        <main>
            <?php FlashMessage::display(); ?>
            <?php echo $content; ?>
        </main>
    </div>
</div>

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

                <a href="index.php?page=admin&action=licensing" class="suha-module-card" data-title="مدیریت لایسنس، قفل سخت افزاری و پلن ها">
            <div class="suha-module-icon" style="background:#fef3c7; color:#d97706;"><i class="fas fa-certificate"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">لایسنس و پلن‌ها</div>
                <div class="suha-module-desc">قفل سخت‌افزاری و فروش ماژولار</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=system_settings" class="suha-module-card" data-title="تنظیمات سامانه">
            <div class="suha-module-icon" style="background:#f1f5f9; color:#475569;"><i class="fas fa-sliders-h"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">تنظیمات سامانه</div>
                <div class="suha-module-desc">هویت، مالی و حالت آفلاین</div>
            </div>
        </a>

        <a href="index.php?page=admin&action=backups" class="suha-module-card" data-title="پشتیبان‌گیری فیزیکی و هاست">
            <div class="suha-module-icon" style="background:#ecfdf5; color:#059669;"><i class="fas fa-database"></i></div>
            <div class="suha-module-info">
                <div class="suha-module-title">پشتیبان‌گیری</div>
                <div class="suha-module-desc">دانلود فیزیکی و ابری هاست</div>
            </div>
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>

<script>
function toggleSidebarMenu() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('content-overlay');
    if (!sidebar) return;
    
    sidebar.classList.toggle('toggled');
    if (overlay) {
        if (sidebar.classList.contains('toggled')) {
            overlay.classList.add('active');
        } else {
            overlay.classList.remove('active');
        }
    }
}

function toggleAdminModulesDrawer() {
    const backdrop = document.getElementById('admin-sheet-backdrop');
    const drawer = document.getElementById('admin-modules-drawer');
    if (!backdrop || !drawer) return;
    
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


// Universal Persian Datepicker Auto-Initializer across all modules
$(document).ready(function() {
    function initPersianPickers() {
        if (typeof $.fn.persianDatepicker === 'function') {
            $('.persian-datepicker, .persian-date, input.persianDatepicker, input[type="date"], input[name*="date"], input[id*="date"], input[placeholder*="تاریخ"]').each(function() {
                var $this = $(this);
                if (!$this.hasClass('pdate-initialized')) {
                    if ($this.attr('type') === 'date') {
                        $this.attr('type', 'text');
                    }
                    $this.addClass('pdate-initialized');
                    $this.persianDatepicker({
                        format: 'YYYY/MM/DD',
                        autoClose: true,
                        initialValue: false,
                        calendar: {
                            persian: {
                                locale: 'fa'
                            }
                        }
                    });
                }
            });
        }
    }
    initPersianPickers();
    $(document).ajaxComplete(function() {
        initPersianPickers();
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const toggler = document.getElementById('sidebarToggleTop');
    if (toggler) {
        toggler.addEventListener('click', toggleSidebarMenu);
    }
    // Auto-wrap any unwrapped tables for silky smooth horizontal scrolling with zero overflow
    document.querySelectorAll('table:not(.table-responsive table):not(.datepicker-table)').forEach(function(tbl) {
        if (!tbl.closest('.table-responsive')) {
            var wrap = document.createElement('div');
            wrap.className = 'table-responsive mb-3';
            tbl.parentNode.insertBefore(wrap, tbl);
            wrap.appendChild(tbl);
        }
    });
});
</script>
<script src="<?php echo APP_URL; ?>/assets/js/offline-engine.js"></script>
<?php include __DIR__ . '/../shared/chat_widget.php'; ?>
</body>
</html>
