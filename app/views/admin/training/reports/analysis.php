<?php
// app/views/admin/training/reports/analysis.php
$report = $report ?? [];
$currentYear = $currentYear ?? jdate('Y');
?>
<style>
    .report-card {
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: none;
    }
</style>
<h1 class="mb-4">گزارش تحلیلی نیازسنجی آموزشی - سال <?php echo $currentYear; ?></h1>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm report-card">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">نقاط ضعف پرتکرار</h6></div>
            <div class="card-body">
                <canvas id="weaknessChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm report-card">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">دوره‌های پیشنهادی پرتکرار</h6></div>
            <div class="card-body">
                <canvas id="courseChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4 report-card">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">تحلیل گروهی مهارت‌ها (بر اساس نقش)</h6>
    </div>
    <div class="card-body">
        <canvas id="groupAnalysisChart"></canvas>
    </div>
</div>

<div class="card shadow-sm mb-4 report-card">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">لیست کامل تحلیل</h6>
    </div>
    <div class="card-body">
        <h5 class="mt-4">تحلیل نقاط ضعف:</h5>
        <ul class="list-group list-group-flush">
            <?php if (!empty($report['weakness_analysis'])): ?>
                <?php foreach ($report['weakness_analysis'] as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo sanitize($item->weaknesses); ?>
                        <span class="badge bg-primary rounded-pill"><?php echo $item->count; ?></span>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item text-center">هیچ داده‌ای برای تحلیل وجود ندارد.</li>
            <?php endif; ?>
        </ul>
        
        <h5 class="mt-4">تحلیل دوره‌های پیشنهادی:</h5>
        <ul class="list-group list-group-flush">
            <?php if (!empty($report['course_analysis'])): ?>
                <?php foreach ($report['course_analysis'] as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo sanitize($item->course_suggestions); ?>
                        <span class="badge bg-success rounded-pill"><?php echo $item->count; ?></span>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item text-center">هیچ داده‌ای برای تحلیل وجود ندارد.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const weaknessData = <?php echo json_encode(array_column($report['weakness_analysis'] ?? [], 'count')); ?>;
        const weaknessLabels = <?php echo json_encode(array_column($report['weakness_analysis'] ?? [], 'weaknesses')); ?>;
        
        const courseData = <?php echo json_encode(array_column($report['course_analysis'] ?? [], 'count')); ?>;
        const courseLabels = <?php echo json_encode(array_column($report['course_analysis'] ?? [], 'course_suggestions')); ?>;

        const groupAnalysis = <?php echo json_encode($report['group_analysis'] ?? []); ?>;

        // Weakness Chart
        const weaknessCtx = document.getElementById('weaknessChart').getContext('2d');
        new Chart(weaknessCtx, {
            type: 'bar',
            data: {
                labels: weaknessLabels,
                datasets: [{
                    label: 'تعداد تکرار',
                    data: weaknessData,
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Course Suggestions Chart
        const courseCtx = document.getElementById('courseChart').getContext('2d');
        new Chart(courseCtx, {
            type: 'pie',
            data: {
                labels: courseLabels,
                datasets: [{
                    data: courseData,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#858796', '#5a5c69', '#3b5998'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });
        
        // Group Analysis Chart (Radar)
        const groupLabels = groupAnalysis.map(item => item.skill_category);
        const selfScores = groupAnalysis.map(item => item.self_score);
        const managerScores = groupAnalysis.map(item => item.manager_score);
        const peerScores = groupAnalysis.map(item => item.peer_score);
        
        const groupCtx = document.getElementById('groupAnalysisChart').getContext('2d');
        new Chart(groupCtx, {
            type: 'radar',
            data: {
                labels: groupLabels,
                datasets: [
                    {
                        label: 'خودارزیابی',
                        data: selfScores,
                        backgroundColor: 'rgba(78, 115, 223, 0.2)',
                        borderColor: '#4e73df',
                        pointBackgroundColor: '#4e73df',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#4e73df'
                    },
                    {
                        label: 'ارزیابی مدیر',
                        data: managerScores,
                        backgroundColor: 'rgba(28, 200, 138, 0.2)',
                        borderColor: '#1cc88a',
                        pointBackgroundColor: '#1cc88a',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#1cc88a'
                    },
                    {
                        label: 'ارزیابی همکاران',
                        data: peerScores,
                        backgroundColor: 'rgba(54, 185, 204, 0.2)',
                        borderColor: '#36b9cc',
                        pointBackgroundColor: '#36b9cc',
                        pointBorderColor: '#fff',
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#36b9cc'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    r: {
                        angleLines: {
                            display: false
                        },
                        suggestedMin: 0,
                        suggestedMax: 5
                    }
                },
                elements: {
                    line: {
                        borderWidth: 2
                    }
                }
            }
        });
    });
</script>
