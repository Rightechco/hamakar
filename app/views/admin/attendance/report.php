<h1 class="mb-4">گزارش حضور و غیاب کارمندان</h1>
<div class="card shadow mb-4">
    <div class="card-body">
        <form method="POST">
            </form>
    </div>
</div>
<div class="card shadow">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>کارمند</th>
                    <th>تاریخ</th>
                    <th>ساعت ورود</th>
                    <th>ساعت خروج</th>
                    <th>مجموع ساعت (دقیقه)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($records as $rec): ?>
                <tr>
                    <td><?php echo sanitize($rec->employee_name); ?></td>
                    <td><?php echo jdate('Y/m/d', strtotime($rec->clock_in)); ?></td>
                    <td><?php echo jdate('H:i', strtotime($rec->clock_in)); ?></td>
                    <td><?php echo $rec->clock_out ? jdate('H:i', strtotime($rec->clock_out)) : '---'; ?></td>
                    <td><?php echo $rec->total_duration ?? '---'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>