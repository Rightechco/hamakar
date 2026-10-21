<?php
// app/views/admin/training/reports/analysis.php - نسخه اولترا پرمیوم
$report = $report ?? [];
$currentYear = $currentYear ?? jdate('Y');
?>

<style>
    .report-card-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .report-box {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .report-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .list-item-custom {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.85rem 1.1rem;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }

    .list-item-custom:hover {
        background: #ffffff;
        border-color: #c7d2fe;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.08);
    }

    .badge-count {
        background: #e0e7ff;
        color: #4338ca;
        border: 1px solid #c7d2fe;
        border-radius: 50px;
        padding: 0.35rem 0.85rem;
        font-weight: 800;
        font-size: 0.8rem;
    }

    .badge-count-success {
        background: #d1fae5;
        color: #059669;
        border: 1px solid #a7f3d0;
        border-radius: 50px;
        padding: 0.35rem 0.85rem;
        font-weight: 800;
        font-size: 0.8rem;
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">گزارش تحلیلی نیازسنجی آموزشی - سال <?php echo sanitize($currentYear); ?></h1>
        <p class="text-muted small mb-0">تحلیل داده‌های آماری، نمودارهای ۳۶۰ درجه و شناسایی شکاف‌های مهارتی سازمان</p>
    </div>

    <div class="d-flex align-items-center gap-2 no-print">
        <!-- فیلتر انتخاب سال -->
        <form action="<?php echo APP_URL; ?>/index.php" method="GET" class="d-flex align-items-center gap-2">
            <input type="hidden" name="page" value="admin">
            <input type="hidden" name="action" value="training_needs_analysis">
            <select name="year" class="form-select form-select-sm rounded-3 fw-bold text-secondary" onchange="this.form.submit()">
                <?php 
                    $thisYear = (int)jdate('Y');
                    for ($y = $thisYear; $y >= $thisYear - 3; $y--): 
                ?>
                    <option value="<?php echo $y; ?>" <?php echo ((string)$currentYear === (string)$y) ? 'selected' : ''; ?>>سال <?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
        </form>

        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-3 d-inline-flex align-items-center gap-1">
            <i class="fas fa-print"></i>
            <span>چاپ گزارش</span>
        </button>
    </div>
</div>

<!-- ردیف اول: نمودارهای میله‌ای و دوناتی -->
<div class="row g-4 mb-4">
    <!-- نمودار نقاط ضعف پرتکرار -->
    <div class="col-lg-6">
        <div class="report-box h-100">
            <div class="report-card-header d-flex align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-chart-column text-indigo-600" style="color:#4f46e5;"></i>
                    <span>نقاط ضعف و شکاف‌های مهارتی پرتکرار</span>
                </h6>
                <span class="badge bg-light text-muted border small font-monospace">فراوانی</span>
            </div>
            <div class="card-body p-4">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="weaknessChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- نمودار دوره‌های پیشنهادی -->
    <div class="col-lg-6">
        <div class="report-box h-100">
            <div class="report-card-header d-flex align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-chart-pie text-emerald-600" style="color:#10b981;"></i>
                    <span>توزیع دوره‌های آموزشی پیشنهادی</span>
                </h6>
                <span class="badge bg-light text-muted border small font-monospace">درصد نیازمندی</span>
            </div>
            <div class="card-body p-4">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="courseChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ردیف دوم: نمودار راداری ارزیابی ۳۶۰ درجه -->
<div class="row mb-4">
    <div class="col-12">
        <div class="report-box">
            <div class="report-card-header d-flex align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-spider text-indigo-600" style="color:#4f46e5;"></i>
                    <span>تحلیل گروهی مهارت‌ها و ارزیابی ۳۶۰ درجه (مقایسه خودارزیابی، مدیر و همکاران)</span>
                </h6>
                <span class="badge bg-light text-muted border small">بازه ۱ تا ۵</span>
            </div>
            <div class="card-body p-4">
                <div style="position: relative; height: 320px; width: 100%;">
                    <canvas id="groupAnalysisChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ردیف سوم: جدول و لیست تفکیکی تحلیل‌ها -->
<div class="row g-4">
    <!-- لیست نقاط ضعف -->
    <div class="col-lg-6">
        <div class="report-box h-100">
            <div class="report-card-header">
                <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-list-check text-danger"></i>
                    <span>جزییات فراوانی نقاط ضعف اعلام‌شده</span>
                </h6>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($report['weakness_analysis'])): ?>
                    <div class="d-flex flex-column">
                        <?php foreach ($report['weakness_analysis'] as $item): ?>
                            <div class="list-item-custom d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark small"><?php echo sanitize($item->weaknesses ?? ''); ?></span>
                                <span class="badge-count font-monospace"><?php echo number_format($item->count ?? 0); ?> نفر</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fs-2 mb-2 d-block opacity-40"></i>
                        <span class="small fw-bold">هیچ داده‌ای جهت تحلیل نقاط ضعف ثبت نشده است.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- لیست دوره‌های پیشنهادی -->
    <div class="col-lg-6">
        <div class="report-box h-100">
            <div class="report-card-header">
                <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-graduation-cap text-success"></i>
                    <span>جزییات فراوانی دوره‌های پیشنهادی مدیریت</span>
                </h6>
            </div>
            <div class="card-body p-4">
                <?php if (!empty($report['course_analysis'])): ?>
                    <div class="d-flex flex-column">
                        <?php foreach ($report['course_analysis'] as $item): ?>
                            <div class="list-item-custom d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark small"><?php echo sanitize($item->course_suggestions ?? ''); ?></span>
                                <span class="badge-count-success font-monospace"><?php echo number_format($item->count ?? 0); ?> تقاضا</span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fs-2 mb-2 d-block opacity-40"></i>
                        <span class="small fw-bold">هیچ پیشنهادی جهت دوره‌های آموزشی ثبت نشده است.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Engine -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ۱. داده‌های نمودار نقاط ضعف
        const weaknessData = <?php echo json_encode(array_column($report['weakness_analysis'] ?? [], 'count')); ?>;
        const weaknessLabels = <?php echo json_encode(array_column($report['weakness_analysis'] ?? [], 'weaknesses')); ?>;
        
        // ۲. داده‌های نمودار دوره‌های پیشنهادی
        const courseData = <?php echo json_encode(array_column($report['course_analysis'] ?? [], 'count')); ?>;
        const courseLabels = <?php echo json_encode(array_column($report['course_analysis'] ?? [], 'course_suggestions')); ?>;

        // ۳. داده‌های نمودار گروهی راداری
        const groupAnalysis = <?php echo json_encode($report['group_analysis'] ?? []); ?>;

        // --- ۱. کانفیگ نمودار میله‌ای نقاط ضعف ---
        const weaknessCanvas = document.getElementById('weaknessChart');
        if (weaknessCanvas) {
            const weaknessCtx = weaknessCanvas.getContext('2d');
            const weaknessGradient = weaknessCtx.createLinearGradient(0, 0, 0, 260);
            weaknessGradient.addColorStop(0, 'rgba(79, 70, 229, 0.85)');
            weaknessGradient.addColorStop(1, 'rgba(79, 70, 229, 0.2)');

            new Chart(weaknessCtx, {
                type: 'bar',
                data: {
                    labels: weaknessLabels,
                    datasets: [{
                        label: 'تعداد فراوانی',
                        data: weaknessData,
                        backgroundColor: weaknessGradient,
                        borderColor: '#4f46e5',
                        borderWidth: 1.5,
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { rtl: true, font: { family: 'Vazirmatn' } }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { color: '#64748b', font: { family: 'Vazirmatn' }, precision: 0 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', font: { family: 'Vazirmatn' } }
                        }
                    }
                }
            });
        }

        // --- ۲. کانفیگ نمودار دوناتی دوره‌ها ---
        const courseCanvas = document.getElementById('courseChart');
        if (courseCanvas) {
            const courseCtx = courseCanvas.getContext('2d');
            new Chart(courseCtx, {
                type: 'doughnut',
                data: {
                    labels: courseLabels,
                    datasets: [{
                        data: courseData,
                        backgroundColor: [
                            '#4f46e5', '#10b981', '#06b6d4', '#f59e0b', '#ef4444',
                            '#8b5cf6', '#64748b', '#ec4899'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            rtl: true,
                            labels: { font: { family: 'Vazirmatn', size: 11 }, boxWidth: 12 }
                        },
                        tooltip: { rtl: true, font: { family: 'Vazirmatn' } }
                    },
                    cutout: '65%'
                }
            });
        }
        
        // --- ۳. کانفیگ نمودار راداری ارزیابی ۳۶۰ درجه ---
        const groupCanvas = document.getElementById('groupAnalysisChart');
        if (groupCanvas) {
            const groupLabels = groupAnalysis.map(item => item.skill_category || 'مهارت عمومی');
            const selfScores = groupAnalysis.map(item => item.self_score || 0);
            const managerScores = groupAnalysis.map(item => item.manager_score || 0);
            const peerScores = groupAnalysis.map(item => item.peer_score || 0);
            
            const groupCtx = groupCanvas.getContext('2d');
            new Chart(groupCtx, {
                type: 'radar',
                data: {
                    labels: groupLabels.length > 0 ? groupLabels : ['مهارت فنی', 'ارتباطات', 'حل مسئله', 'کار تیمی'],
                    datasets: [
                        {
                            label: 'خودارزیابی کارمند',
                            data: selfScores.length > 0 ? selfScores : [4, 3.5, 4, 4.5],
                            backgroundColor: 'rgba(79, 70, 229, 0.15)',
                            borderColor: '#4f46e5',
                            pointBackgroundColor: '#4f46e5',
                            pointBorderColor: '#fff',
                            borderWidth: 2
                        },
                        {
                            label: 'ارزیابی مستقیم مدیر',
                            data: managerScores.length > 0 ? managerScores : [3, 4, 3.5, 4],
                            backgroundColor: 'rgba(16, 185, 129, 0.15)',
                            borderColor: '#10b981',
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#fff',
                            borderWidth: 2
                        },
                        {
                            label: 'ارزیابی همکاران (Peer)',
                            data: peerScores.length > 0 ? peerScores : [3.5, 4.2, 3.8, 4.8],
                            backgroundColor: 'rgba(6, 182, 212, 0.15)',
                            borderColor: '#06b6d4',
                            pointBackgroundColor: '#06b6d4',
                            pointBorderColor: '#fff',
                            borderWidth: 2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            rtl: true,
                            labels: { font: { family: 'Vazirmatn' } }
                        },
                        tooltip: { rtl: true, font: { family: 'Vazirmatn' } }
                    },
                    scales: {
                        r: {
                            angleLines: { color: '#e2e8f0' },
                            grid: { color: '#f1f5f9' },
                            pointLabels: { font: { family: 'Vazirmatn', size: 12, weight: 'bold' }, color: '#334155' },
                            suggestedMin: 0,
                            suggestedMax: 5,
                            ticks: { stepSize: 1, font: { family: 'Vazirmatn' } }
                        }
                    }
                }
            });
        }
    });
</script>