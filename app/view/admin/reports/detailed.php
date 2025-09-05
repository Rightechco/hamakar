<?php
// app/views/admin/reports/detailed.php
?>
<h1 class="mb-4">گزارشات جامع و دقیق</h1>

<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">فیلتر گزارشات</h6>
    </div>
    <div class="card-body">
        <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=detailed_reports" method="POST">
            <div class="row">
                <div class="col-md-3">
                    <label>وضعیت فاکتور</label>
                    <select name="invoice_status" class="form-select">
                        <option value="pending" <?php echo ($filters['invoice_status'] == 'pending') ? 'selected' : ''; ?>>در انتظار</option>
                        <option value="paid" <?php echo ($filters['invoice_status'] == 'paid') ? 'selected' : ''; ?>>پرداخت شده</option>
                        <option value="overdue" <?php echo ($filters['invoice_status'] == 'overdue') ? 'selected' : ''; ?>>سررسید گذشته</option>
                        <option value="canceled" <?php echo ($filters['invoice_status'] == 'canceled') ? 'selected' : ''; ?>>لغو شده</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>وضعیت پروژه</label>
                     <select name="project_status" class="form-select">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="not_started" <?php echo ($filters['project_status'] == 'not_started') ? 'selected' : ''; ?>>شروع نشده</option>
                        <option value="in_progress" <?php echo ($filters['project_status'] == 'in_progress') ? 'selected' : ''; ?>>در حال انجام</option>
                        <option value="finished" <?php echo ($filters['project_status'] == 'finished') ? 'selected' : ''; ?>>تکمیل شده</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>از تاریخ</label>
                    <input type="text" name="start_date" class="form-control persian-datepicker" value="<?php echo !empty($filters['start_date']) ? jdate('Y/m/d', strtotime($filters['start_date'])) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label>تا تاریخ</label>
                    <input type="text" name="end_date" class="form-control persian-datepicker" value="<?php echo !empty($filters['end_date']) ? jdate('Y/m/d', strtotime($filters['end_date'])) : ''; ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">اعمال فیلتر</button>
        </form>
    </div>
</div>

<ul class="nav nav-tabs" id="reportTabs" role="tablist">
  <li class="nav-item" role="presentation"><button class="nav-link active" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices" type="button">گزارش فاکتورها</button></li>
  <li class="nav-item" role="presentation"><button class="nav-link" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button">گزارش پروژه‌ها</button></li>
  <li class="nav-item" role="presentation"><button class="nav-link" id="employees-tab" data-bs-toggle="tab" data-bs-target="#employees" type="button">گزارش کارمندان</button></li>
</ul>

<div class="tab-content" id="reportTabsContent">
    <div class="tab-pane fade show active" id="invoices" role="tabpanel">
        <div class="card shadow">
            <div class="card-body">
                <table class="table table-striped">
                    <thead><tr><th>شماره فاکتور</th><th>مشتری</th><th>مبلغ</th><th>تاریخ صدور</th><th>تاریخ سررسید</th></tr></thead>
                    <tbody>
                        <?php foreach($reports['invoices'] as $invoice): ?>
                            <tr>
                                <td><?php echo sanitize($invoice->invoice_number); ?></td>
                                <td><?php echo sanitize($invoice->client_name); ?></td>
                                <td><?php echo number_format($invoice->total_amount); ?> تومان</td>
                                <td><?php echo jdate('Y/m/d', strtotime($invoice->issue_date)); ?></td>
                                <td><?php echo jdate('Y/m/d', strtotime($invoice->due_date)); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="projects" role="tabpanel">
        </div>
    <div class="tab-pane fade" id="employees" role="tabpanel">
        </div>
</div>