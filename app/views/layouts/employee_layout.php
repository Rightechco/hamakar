<?php
// app/views/layouts/employee_layout.php
global $auth;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo sanitize($title ?? 'پنل کارمندی'); ?> - رایان تکرو</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" />

    <style>
        body { font-family: 'Vazirmatn', sans-serif; background-color: #f1f5f9; }
        .sidebar-item { transition: all 0.2s ease; }
        .sidebar-item:hover { background-color: rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="antialiased text-slate-800">

<div class="flex min-h-screen">
    <!-- Employee Sidebar -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col border-l border-slate-800">
        <div class="p-5 bg-slate-950/50 border-b border-slate-800 flex items-center justify-center gap-3">
            <i class="fas fa-id-badge text-indigo-400 text-xl"></i>
            <h1 class="font-bold text-white text-lg">پنل کارمندی</h1>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <a href="index.php?page=employee&action=dashboard" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-white bg-indigo-600/30 border border-indigo-500/30">
                <i class="fas fa-tachometer-alt text-indigo-400"></i>
                <span>داشبورد</span>
            </a>
            <a href="index.php?page=employee&action=leave_requests" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-400 hover:text-white">
                <i class="fas fa-calendar-alt"></i>
                <span>درخواست مرخصی</span>
            </a>
            <a href="index.php?page=logout" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-rose-400 hover:text-rose-300">
                <i class="fas fa-sign-out-alt"></i>
                <span>خروج</span>
            </a>
        </nav>
    </aside>

    <!-- Main Workspace -->
    <main class="flex-1 p-6 md:p-10">
        <div class="max-w-7xl mx-auto space-y-6">
            <?php FlashMessage::display(); ?>
            <?php echo $content; ?>
        </div>
    </main>
</div>

</body>
</html>