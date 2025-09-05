<?php
// views/admin/settings/api_keys.php - نسخه نهایی با طراحی حرفه‌ای

// متغیرها از کنترلر به ویو پاس داده می‌شوند: $apiKeys
?>

<h1 class="mb-4">مدیریت کلیدهای API</h1>

<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-plus-circle me-2"></i>افزودن کلید API جدید</h6>
            </div>
            <div class="card-body">
                <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=storeApiKey" method="POST">
                    <div class="mb-3">
                        <label for="provider" class="form-label">پلتفرم:</label>
                        <select name="provider" id="provider" class="form-control" required>
                            <option value="google">Google Maps API</option>
                            <option value="neshan">Neshan API</option>
                            <option value="balad">Balad API</option>
                            <option value="hunter">Hunter.io API</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="api_key" class="form-label">کلید API:</label>
                        <input type="text" name="api_key" id="api_key" class="form-control" placeholder="کلید API را اینجا وارد کنید..." required>
                    </div>
                    <div class="mb-3">
                        <label for="daily_limit" class="form-label">محدودیت روزانه:</label>
                        <input type="number" name="daily_limit" id="daily_limit" class="form-control" value="1000" required>
                        <small class="form-text text-muted">تعداد مجاز درخواست در روز. برای Google Places API این مقدار را 11764 قرار دهید.</small>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">وضعیت:</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="active">فعال</option>
                            <option value="inactive">غیرفعال</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-lg btn-primary w-100" onclick="return confirm('آیا از ذخیره این کلید مطمئن هستید؟')">
                        <i class="fas fa-save me-2"></i>ذخیره کلید
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-dark text-white py-3">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-key me-2"></i>کلیدهای API موجود</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>پلتفرم</th>
                                <th>کلید API</th>
                                <th>مصرف امروز</th>
                                <th>محدودیت روزانه</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($apiKeys)): ?>
                                <?php foreach ($apiKeys as $key): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($key->provider); ?></td>
                                        <td><code><?php echo substr(htmlspecialchars($key->api_key), 0, 10) . '...'; ?></code></td>
                                        <td><?php echo htmlspecialchars($key->daily_usage); ?></td>
                                        <td><?php echo htmlspecialchars($key->daily_limit); ?></td>
                                        <td>
                                            <?php
                                            $badgeClass = ($key->status === 'active') ? 'bg-success' : 'bg-danger';
                                            echo "<span class='badge {$badgeClass}'>" . htmlspecialchars($key->status) . "</span>";
                                            ?>
                                        </td>
                                        <td>
                                            <form action="<?php echo APP_URL; ?>/index.php?page=admin&action=deleteApiKey&id=<?php echo $key->id; ?>" method="POST" style="display:inline;">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('آیا مطمئن هستید؟')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">هیچ کلید API یافت نشد.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>