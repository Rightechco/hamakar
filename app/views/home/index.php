<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سامانه هماکار | سیستم جامع مدیریت ارتباط با مشتریان (CRM) و اتوماسیون اداری</title>
    
    <!-- Bootstrap RTL 5.3 & FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4338ca;
            --secondary: #06b6d4;
            --accent: #10b981;
            --dark-bg: #0b0f19;
            --dark-card: #151d2a;
            --light-bg: #f8fafc;
            --border-light: rgba(226, 232, 240, 0.8);
        }

        /* غیرفعال‌سازی و حذف قطعی سایدبار در لندینگ پیج */
        #sidebar, .premium-sidebar, aside {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }

        body {
            font-family: 'Vazirmatn', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
            line-height: 1.8;
            margin: 0 !important;
            padding: 0 !important;
            width: 100vw;
        }

        /* ساختار تمام صفحه‌ای (Full Width Layout) */
        .section-full {
            width: 100% !important;
            max-width: 100% !important;
            padding-left: clamp(1.5rem, 5vw, 5rem) !important;
            padding-right: clamp(1.5rem, 5vw, 5rem) !important;
        }

        .text-justify-custom { text-align: justify; text-justify: inter-word; }
        
        .gradient-text {
            background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        }

        /* --- Navbar Ultra Premium --- */
        .navbar-premium {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            padding: 16px 0;
            transition: all 0.3s ease;
        }
        
        .nav-link {
            font-weight: 700;
            color: #0f172a !important;
            margin: 0 12px;
            font-size: 0.95rem;
            position: relative;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -6px;
            right: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #6366f1, #06b6d4);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 4px;
        }
        
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }

        /* --- Hero Section --- */
        .hero-section {
            padding: 180px 0 110px;
            background: radial-gradient(circle at 85% 15%, rgba(99, 102, 241, 0.12) 0%, transparent 45%),
                        radial-gradient(circle at 15% 85%, rgba(6, 182, 212, 0.12) 0%, transparent 45%),
                        #ffffff;
            position: relative;
            overflow: hidden;
        }

        .hero-badge {
            background: rgba(99, 102, 241, 0.08);
            border: 1px solid rgba(99, 102, 241, 0.2);
            color: #4338ca;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .hero-img-box {
            position: relative;
            z-index: 2;
        }
        
        .hero-img {
            border-radius: 32px;
            box-shadow: 0 30px 70px -15px rgba(15, 23, 42, 0.2);
            border: 8px solid rgba(255, 255, 255, 0.9);
            transition: all 0.5s ease;
        }

        .hero-img:hover {
            transform: translateY(-8px) scale(1.01);
            box-shadow: 0 40px 80px -15px rgba(99, 102, 241, 0.3);
        }

        /* --- Glow Blobs --- */
        .glow-blob {
            position: absolute;
            filter: blur(100px);
            z-index: 0;
            opacity: 0.5;
            pointer-events: none;
            animation: floatGlow 12s ease-in-out infinite alternate;
        }
        .glow-blob-1 { top: 10%; left: 5%; width: 500px; height: 500px; background: #c7d2fe; }
        .glow-blob-2 { bottom: 10%; right: 5%; width: 450px; height: 450px; background: #bae6fd; }

        @keyframes floatGlow {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(40px, -40px) scale(1.1); }
        }

        /* --- Buttons Ultra --- */
        .btn-gradient-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            color: #ffffff !important;
            border: none;
            padding: 16px 42px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 1rem;
            box-shadow: 0 12px 30px -5px rgba(99, 102, 241, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-gradient-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 40px -5px rgba(99, 102, 241, 0.55);
        }

        /* --- Feature Cards Ultra --- */
        .feature-card-ultra {
            background: #ffffff;
            border-radius: 28px;
            padding: 36px 30px;
            height: 100%;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.03);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .feature-card-ultra:hover {
            transform: translateY(-10px);
            box-shadow: 0 25px 50px -12px rgba(99, 102, 241, 0.15);
            border-color: #c7d2fe;
        }

        .icon-box-ultra {
            width: 70px;
            height: 70px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
            margin-bottom: 24px;
            background: #e0e7ff;
            color: #4338ca;
            transition: all 0.35s ease;
        }

        .feature-card-ultra:hover .icon-box-ultra {
            background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
            color: #ffffff;
            transform: scale(1.1) rotate(6deg);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        }

        /* --- Pricing Cards Ultra --- */
        .pricing-section-full {
            background: #f8fafc;
            position: relative;
        }

        .plan-card-ultra {
            background: #ffffff;
            border-radius: 32px;
            padding: 40px 32px;
            border: 1px solid #e2e8f0;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .plan-card-ultra:hover {
            border-color: #6366f1;
            box-shadow: 0 30px 60px -15px rgba(0,0,0,0.1);
            transform: translateY(-8px);
        }

        .plan-card-ultra.featured {
            border: 2.5px solid #6366f1;
            box-shadow: 0 30px 60px -15px rgba(99, 102, 241, 0.25);
            background: linear-gradient(180deg, #ffffff 0%, #f5f3ff 100%);
        }

        .price-number {
            font-size: 2.75rem;
            font-weight: 900;
            color: #0f172a;
            font-family: monospace, 'Vazirmatn', sans-serif;
        }

        .check-pill {
            color: #059669;
            background: #d1fae5;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            flex-shrink: 0;
        }

        /* --- Modal Styling --- */
        .modal-content-ultra {
            border-radius: 32px;
            border: none;
            box-shadow: 0 30px 70px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        /* --- Footer Dark Ultra --- */
        footer {
            background: #0b0f19;
            color: #94a3b8;
            padding-top: 90px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        footer h5 { color: #ffffff; font-weight: 800; margin-bottom: 28px; }
        footer a { color: #94a3b8; text-decoration: none; transition: all 0.25s ease; }
        footer a:hover { color: #ffffff; padding-right: 6px; }
    </style>
</head>
<body>

    <!-- Glow Background Effects -->
    <div class="glow-blob glow-blob-1"></div>
    <div class="glow-blob glow-blob-2"></div>

    <!-- Navigation Bar Full Width -->
    <nav class="navbar navbar-expand-lg navbar-premium fixed-top w-100">
        <div class="section-full d-flex align-items-center justify-content-between">
            <a class="navbar-brand fw-extrabold fs-3 d-flex align-items-center gap-2.5" href="#">
                <div class="rounded-3 gradient-bg p-2 text-white d-flex align-items-center justify-content-center shadow-sm" style="width:42px; height:42px;">
                    <i class="fas fa-briefcase fs-5"></i>
                </div>
                <div>
                    <span style="color: #6366f1; font-weight: 900;">هما</span><span style="color: #0f172a; font-weight: 900;">کار</span>
                </div>
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active" href="#home">خانه</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">امکانات سیستم</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pricing">تعرفه‌ها و اشتراک</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">سوالات متداول</a></li>
                </ul>
                
                <div class="d-flex align-items-center gap-3">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="index.php?page=client&action=dashboard" class="btn-gradient-primary btn-sm px-4">
                            <i class="fas fa-user-circle"></i>
                            <span>ورود به پنل کاربری</span>
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=login" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-bold">ورود اعضا</a>
                        <a href="#pricing" class="btn-gradient-primary btn-sm px-4">
                            <i class="fas fa-bolt"></i>
                            <span>خرید لایسنس</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section Full Width -->
    <section id="home" class="hero-section section-full">
        <div class="row align-items-center g-5 my-auto">
            <div class="col-lg-6 order-2 order-lg-1 animate__animated animate__fadeInRight">
                <div class="hero-badge mb-4">
                    <i class="fas fa-sparkles text-warning"></i>
                    <span>پلتفرم جامع مدیریت سازمان، مالی و CRM</span>
                </div>
                
                <h1 class="display-3 fw-black mb-4 text-dark" style="font-weight: 900; line-height: 1.3;">
                    سامانه هوشمند و یکپارچه <br>
                    <span class="gradient-text">مدیریت مالی، پروژه‌ها و مشتریان</span>
                </h1>

                <p class="lead text-secondary mb-5 text-justify-custom" style="font-size: 1.15rem; line-height: 2;">
                    هماکار پلتفرمی جامع و قدرتمند برای اتوماسیون کامل شرکت‌ها و کسب‌وکارها است. فرآیندهای مالی، فاکتورهای رسمی، قراردادها، مدیریت پروژه‌ها و چک‌لیست‌ها، حضور و غیاب، حقوق و دستمزد و پشتیبانی تیکتینگ را در یک سیستم هوشمند مدیریت کنید.
                </p>

                <div class="d-flex gap-3 flex-wrap">
                    <a href="#pricing" class="btn-gradient-primary">
                        <span>خرید و فعال‌سازی لایسنس</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <a href="#features" class="btn btn-white btn-lg rounded-pill shadow-sm text-dark border px-4.5 fw-bold d-inline-flex align-items-center gap-2">
                        <i class="fas fa-list-check text-primary"></i>
                        <span>مشاهده امکانات سیستم</span>
                    </a>
                </div>
                
                <!-- آمار و شاخص‌های کلیدی -->
                <div class="row mt-5 pt-4 border-top border-secondary border-opacity-10 g-4">
                    <div class="col-auto">
                        <h3 class="fw-black mb-0 text-dark font-monospace">۱۰۰٪</h3>
                        <small class="text-muted fw-bold">یکپارچگی کامل ابزارها</small>
                    </div>
                    <div class="col-auto border-end border-secondary border-opacity-10"></div>
                    <div class="col-auto">
                        <h3 class="fw-black mb-0 text-dark font-monospace">۲۴/۷</h3>
                        <small class="text-muted fw-bold">پشتیبانی و تیکتینگ آنلاین</small>
                    </div>
                    <div class="col-auto border-end border-secondary border-opacity-10"></div>
                    <div class="col-auto">
                        <h3 class="fw-black mb-0 text-dark font-monospace">دقیق</h3>
                        <small class="text-muted fw-bold">صدور خودکار سند حسابداری</small>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 order-1 order-lg-2 text-center animate__animated animate__fadeInLeft">
                <div class="hero-img-box">
                    <img src="/public/uploads/Search-ads-2-1024x1014.webp" alt="داشبورد هماکار" class="img-fluid hero-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section Full Width -->
    <section id="features" class="section-full py-5 position-relative bg-white">
        <div class="py-5">
            <div class="text-center mb-5 animate__animated animate__fadeInUp">
                <span class="text-indigo-600 fw-extrabold text-uppercase small letter-spacing-2" style="color:#6366f1;">امکانات اصلی هماکار</span>
                <h2 class="fw-black mt-2 display-5">اتوماسیون یکپارچه <span class="gradient-text">مدیریت سازمان</span></h2>
                <p class="text-muted mt-2 fs-6">تمام ماژول‌های مورد نیاز برای ارتقای بهره‌وری سازمان در یک سامانه</p>
            </div>

            <div class="row g-4">
                <div class="col-xl-4 col-md-6">
                    <div class="feature-card-ultra">
                        <div class="icon-box-ultra"><i class="fas fa-users-gear"></i></div>
                        <div>
                            <h4 class="fw-bold mb-3 fs-5 text-dark">مدیریت مشتریان و پرونده CRM</h4>
                            <p class="text-muted text-justify-custom small mb-0">پرونده آنلاین اشخاص حقیقی و حقوقی، مدیریت رابطین، مدارک ثبتی، اتصال به حساب کاربری و ثبت تاریخچه تمام لاگ‌ها و تماس‌ها.</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="feature-card-ultra">
                        <div class="icon-box-ultra"><i class="fas fa-file-signature"></i></div>
                        <div>
                            <h4 class="fw-bold mb-3 fs-5 text-dark">مدیریت قراردادهای رسمی</h4>
                            <p class="text-muted text-justify-custom small mb-0">تنظیم قراردادهای حقوقی خدمات، محاسبه پیش‌پرداخت، سررسید تسویه نهایی، تمدیدهای دوره ای و صدور خودکار نسخه رسمی A4 جهت چاپ.</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="feature-card-ultra">
                        <div class="icon-box-ultra"><i class="fas fa-file-invoice-dollar"></i></div>
                        <div>
                            <h4 class="fw-bold mb-3 fs-5 text-dark">صدور فاکتور و سیستم حسابداری</h4>
                            <p class="text-muted text-justify-custom small mb-0">صدور فاکتورهای رسمی و غیررسمی، محاسبه ارزش‌افزوده، صدور خودکار اسناد حسابداری دوبل، ثبت دریافت‌ها، دفتر روزنامه و کل.</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="feature-card-ultra">
                        <div class="icon-box-ultra"><i class="fas fa-diagram-project"></i></div>
                        <div>
                            <h4 class="fw-bold mb-3 fs-5 text-dark">مدیریت پروژه و چک‌لیست‌ها</h4>
                            <p class="text-muted text-justify-custom small mb-0">تعریف پروژه‌ها، تخصیص اعضا، تعریف وظایف و چک‌لیست‌ها، محاسبه درصد پیشرفت فیزیکی و زمان‌بندی دقیق جهت کنترل خروجی تیم.</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="feature-card-ultra">
                        <div class="icon-box-ultra"><i class="fas fa-user-clock"></i></div>
                        <div>
                            <h4 class="fw-bold mb-3 fs-5 text-dark">حضور و غیاب و حقوق دستمزد</h4>
                            <p class="text-muted text-justify-custom small mb-0">ثبت تردد و مرخصی‌ها، محاسبه کارکرد پرسنل، ارزیابی نرخ موفقیت کارمندان بر اساس تسک‌ها و صدور خودکار فیش حقوقی و سند هزینه.</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6">
                    <div class="feature-card-ultra">
                        <div class="icon-box-ultra"><i class="fas fa-headset"></i></div>
                        <div>
                            <h4 class="fw-bold mb-3 fs-5 text-dark">سیستم تیکتینگ و پشتیبانی</h4>
                            <p class="text-muted text-justify-custom small mb-0">ارتباط مستمر کارفرمایان با بخش‌های پشتیبانی، ارسال فایل‌های پیوست، تغییر وضعیت هوشمند تیکت‌ها و ارتقای کیفیت خدمات مشتریان.</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-md-6">
                    <div class="feature-card-ultra d-flex flex-row align-items-center gap-4">
                        <div class="icon-box-ultra flex-shrink-0 mb-0"><i class="fas fa-warehouse"></i></div>
                        <div>
                            <h4 class="fw-bold mb-2 fs-5 text-dark">انبارداری و مدیریت کالا</h4>
                            <p class="text-muted mb-0 text-justify-custom small">تعریف محصولات و خدمات، دسته‌بندی‌ها، انبارها، ثبت سند خرید و فروش و گزارش کامل گردش کالا.</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-md-6">
                    <div class="feature-card-ultra d-flex flex-row align-items-center gap-4 border-primary" style="background: #f0fdf4;">
                        <div class="icon-box-ultra flex-shrink-0 mb-0 bg-white text-emerald-600" style="color:#059669;"><i class="fas fa-comment-sms"></i></div>
                        <div>
                            <h4 class="fw-bold mb-2 text-dark fs-5">اطلاع‌رسانی پیامکی خودکار</h4>
                            <p class="text-muted mb-0 text-justify-custom small">ارسال خودکار یادآوری پرداخت فاکتور، سررسید تمدید قراردادها و پیامک‌های مناسبتی به مشتریان.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section Full Width -->
    <section id="pricing" class="pricing-section-full section-full py-5">
        <div class="py-5">
            <div class="text-center mb-5">
                <span class="text-indigo-600 fw-extrabold text-uppercase small letter-spacing-2" style="color:#6366f1;">تعرفه‌ها و اشتراک سامانه</span>
                <h2 class="fw-black mt-2 display-5">پلن‌های <span class="gradient-text">اشتراک هماکار</span></h2>
                <p class="text-muted mt-2 fs-6">انتخاب اشتراک بر اساس ظرفیت سازمان و نیازهای کاری شما</p>
            </div>

            <div class="row justify-content-center align-items-stretch g-4">
                <?php 
                $plans = $data['plans'] ?? [];
                if(empty($plans)): 
                ?>
                    <div class="col-12 text-center py-5">
                        <div class="alert alert-light border shadow-sm d-inline-block px-5 py-4 rounded-4">در حال حاضر پلنی تعریف نشده است.</div>
                    </div>
                <?php else: ?>
                    <?php foreach($plans as $plan): 
                        $isFeatured = $plan->is_featured ?? false;
                    ?>
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="plan-card-ultra <?php echo $isFeatured ? 'featured' : ''; ?>">
                            <?php if($isFeatured): ?>
                                <div class="badge bg-danger position-absolute top-0 start-0 m-4 rounded-pill px-3 py-2 fw-bold">محبوب‌ترین پلن</div>
                            <?php endif; ?>

                            <div>
                                <div class="text-center pb-4 border-bottom">
                                    <h4 class="fw-bold text-dark mb-3 fs-4"><?php echo htmlspecialchars($plan->title); ?></h4>
                                    <div class="price-number mb-2">
                                        <?php echo number_format($plan->price); ?> 
                                        <span class="fs-6 text-muted font-normal">تومان</span>
                                    </div>
                                    <span class="badge bg-indigo-50 text-indigo-700 border border-indigo-200 rounded-pill px-3 py-1.5 fw-bold" style="background:#e0e7ff; color:#4338ca;">
                                        <?php echo $plan->days; ?> روز اعتبار لایسنس
                                    </span>
                                </div>

                                <div class="py-4">
                                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="fas fa-check check-pill"></i>
                                            <span class="small">ظرفیت ثبت <strong><?php echo number_format($plan->max_records); ?></strong> مشتری و پرونده</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="fas fa-check check-pill"></i>
                                            <span class="small">دسترسی همزمان <strong><?php echo $plan->max_bots; ?></strong> کاربر / کاربر ارشد</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="fas fa-check check-pill"></i>
                                            <span class="small">صدور نامحدود فاکتور و سند حسابداری</span>
                                        </li>
                                        <li class="d-flex align-items-center gap-2">
                                            <i class="fas fa-check check-pill"></i>
                                            <span class="small">سیستم تیکتینگ و پشتیبانی آنلاین</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="pt-3 mt-auto">
                                <button type="button" 
                                        onclick="openPurchaseModal(<?php echo $plan->id; ?>, '<?php echo htmlspecialchars($plan->title); ?>', '<?php echo number_format($plan->price); ?>')"
                                        class="btn <?php echo $isFeatured ? 'btn-gradient-primary' : 'btn-outline-dark'; ?> w-100 py-3 rounded-pill fw-bold justify-content-center">
                                    <span><?php echo $isFeatured ? 'خرید آنی و شروع کار' : 'انتخاب این پلن'; ?></span>
                                    <i class="fas fa-credit-card ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- FAQ Section Full Width -->
    <section id="faq" class="section-full py-5 bg-white">
        <div class="py-5">
            <div class="text-center mb-5">
                <span class="text-indigo-600 fw-extrabold text-uppercase small letter-spacing-2" style="color:#6366f1;">پاسخ به سوالات شما</span>
                <h2 class="fw-black mt-2 display-5">سوالات <span class="gradient-text">متداول</span></h2>
            </div>

            <div class="row justify-content-center">
                <div class="col-xl-9 col-lg-10">
                    <div class="accordion accordion-flush rounded-4 shadow-sm border overflow-hidden p-2" id="faqAccordion">
                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-bold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    آیا سامانه هماکار امکان صدور اسناد حسابداری دوبل را به صورت خودکار دارد؟
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small lh-lg">
                                    بله، پس از ثبت فاکتور یا دریافت وجه، اسناد حسابداری مربوط به بدهکار و بستانکار بودن حساب‌ها به‌طور هوشمند در کدینگ حسابداری ثبت می‌شوند.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    آیا مشتریان ما می‌توانند به پنل اختصاصی خود دسترسی داشته باشند؟
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small lh-lg">
                                    بله، با متصل کردن حساب کاربری، مشتریان می‌توانند لایسنس‌ها، قراردادها، وضعیت فاکتورها، پروژه‌ها و تیکت‌های پشتیبانی خود را مشاهده کنند.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold text-dark fs-6" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    فعال‌سازی لایسنس پس از خرید چقدر زمان می‌برد؟
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small lh-lg">
                                    بلافاصله پس از پرداخت موفق درگاه بانکی، لایسنس حساب کاربری شما صادر و تمام بخش‌های مربوط به پلن شما فعال خواهند شد.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Full Width -->
    <footer class="section-full">
        <div class="pb-5">
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div class="rounded-3 gradient-bg p-2 text-white d-flex align-items-center justify-content-center" style="width:42px; height:42px;">
                            <i class="fas fa-briefcase fs-5"></i>
                        </div>
                        <h5 class="fw-extrabold fs-3 m-0">
                            <span style="color: #6366f1">هما</span><span class="text-white">کار</span>
                        </h5>
                    </div>
                    <p class="text-secondary text-justify-custom small mb-4 lh-lg">
                        سامانه هماکار پلتفرمی جامع برای مدیریت کارفرمایان، صدور فاکتور و قرارداد، کنترل پروژه‌ها، اتوماسیون اداری و سیستم‌های مالی شرکت‌ها است.
                    </p>
                </div>

                <div class="col-lg-2 col-6">
                    <h5>دسترسی سریع</h5>
                    <ul class="list-unstyled d-grid gap-2.5 small">
                        <li><a href="#home">صفحه اصلی</a></li>
                        <li><a href="#features">امکانات هماکار</a></li>
                        <li><a href="#pricing">تعرفه‌های اشتراک</a></li>
                        <li><a href="index.php?page=login">ورود اعضا</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-6">
                    <h5>ماژول‌ها</h5>
                    <ul class="list-unstyled d-grid gap-2.5 small">
                        <li><a href="#features">مدیریت مشتریان</a></li>
                        <li><a href="#features">مدیریت قراردادها</a></li>
                        <li><a href="#features">حسابداری و فاکتور</a></li>
                        <li><a href="#features">مدیریت پروژه</a></li>
                    </ul>
                </div>

                <div class="col-lg-4">
                    <h5>ارتباط با پشتیبانی</h5>
                    <ul class="list-unstyled d-grid gap-3 text-secondary small">
                        <li class="d-flex align-items-center gap-2"><i class="fas fa-phone text-indigo-400 fs-6"></i> ۰۴۱۳۴۴۰۱۱۷۹</li>
                        <li class="d-flex align-items-center gap-2"><i class="fas fa-envelope text-indigo-400 fs-6"></i> info@rayantakro.com</li>
                        <li class="d-flex align-items-start gap-2"><i class="fas fa-map-marker-alt text-indigo-400 mt-1 fs-6"></i> تبریز، بلوار آزادی</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="border-top border-secondary border-opacity-25 py-4 text-center bg-black bg-opacity-30">
            <small class="text-secondary font-monospace">طراحی و توسعه برای سامانه هماکار © ۲۰۲۶</small>
        </div>
    </footer>

    <!-- Modal ثبت نام و خرید مستقیم -->
    <div class="modal fade" id="purchaseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-content-ultra">
                <div class="modal-header border-0 pb-0 p-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="modalPlanTitle">
                        <i class="fas fa-shopping-cart text-primary"></i>
                        <span>خرید و فعال‌سازی اشتراک هماکار</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 pt-2">
                    <p class="text-muted small mb-4">جهت ایجاد حساب کاربری و صدور لایسنس، اطلاعات زیر را وارد کنید.</p>
                    
                    <form action="index.php?page=payment&action=direct_purchase" method="POST">
                        <input type="hidden" name="plan_id" id="modalPlanId">
                        
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control rounded-4" id="floatingName" name="name" placeholder="نام شما" required>
                            <label for="floatingName">نام و نام خانوادگی / نام شرکت</label>
                        </div>

                        <div class="form-floating mb-3">
                            <input type="tel" class="form-control rounded-4 font-monospace" id="floatingMobile" name="mobile" placeholder="09123456789" pattern="09[0-9]{9}" required>
                            <label for="floatingMobile">شماره همراه (نام کاربری ورود)</label>
                        </div>

                        <div class="form-floating mb-4">
                            <input type="password" class="form-control rounded-4" id="floatingPassword" name="password" placeholder="Password" required>
                            <label for="floatingPassword">رمز عبور دلخواه جهت ورود به پنل</label>
                        </div>

                        <div class="bg-light p-3 rounded-4 mb-4 d-flex justify-content-between align-items-center border border-dashed">
                            <span class="text-muted small fw-bold">مبلغ قابل پرداخت:</span>
                            <span class="fw-extrabold text-indigo-600 fs-5" style="color:#4338ca;"><span id="modalPrice">0</span> تومان</span>
                        </div>

                        <button type="submit" class="btn-gradient-primary w-100 py-3 justify-content-center">
                            <span>ثبت نام و پرداخت آنلاین</span>
                            <i class="fas fa-credit-card"></i>
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">قبلاً ثبت‌نام کرده‌اید؟ <a href="index.php?page=login" class="text-primary text-decoration-none fw-bold">وارد پنل شوید</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function openPurchaseModal(id, title, price) {
            document.getElementById('modalPlanId').value = id;
            document.getElementById('modalPlanTitle').innerHTML = '<i class="fas fa-shopping-cart text-primary"></i> خرید اشتراک: ' + title;
            document.getElementById('modalPrice').innerText = price;
            
            var myModal = new bootstrap.Modal(document.getElementById('purchaseModal'));
            myModal.show();
        }
    </script>
</body>
</html>