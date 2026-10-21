<?php
// app/views/admin/inventory/products/index.php - نسخه اولترا پرمیوم و مدرن
global $auth;
$products =$products ?? [];
$categories =$categories ?? [];
$filters =$filters ?? [];
?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h1 class="h3 fw-bold text-dark mb-1">مدیریت محصولات و انبار</h1>
        <p class="text-muted small mb-0">ساماندهی کالاها، قیمت‌گذاری، دسته‌بندی و نظارت بر موجودی انبار</p>
    </div>
    
    <div class="d-flex align-items-center gap-2 no-print">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1">
            <i class="fas fa-print"></i>
            <span>چاپ لیست</span>
        </button>
        <?php if ($auth->hasRole(['admin', 'accountant'])): ?>
            <a href="<?php echo APP_URL; ?>/index.php?page=products&action=create" class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1">
                <i class="fas fa-plus"></i>
                <span>افزودن محصول جدید</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white py-3 border-bottom no-print">
        <h6 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="fas fa-filter text-primary"></i>
            <span>فیلتر و جستجوی پیشرفته محصولات</span>
        </h6>
    </div>
    
    <div class="card-body p-3 bg-light no-print border-bottom">
        <form id="filter-form" action="<?php echo APP_URL; ?>/index.php" method="GET">
            <input type="hidden" name="page" value="products">
            <input type="hidden" name="action" value="index">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="search" class="form-label text-muted small fw-bold">جستجو:</label>
                    <input type="text" class="form-control form-control-sm rounded-3" id="search" name="search" placeholder="نام محصول یا کد کالا (SKU)..." value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                </div>
                <div class="col-md-5">
                    <label for="category_id" class="form-label text-muted small fw-bold">دسته‌بندی:</label>
                    <select class="form-select form-select-sm rounded-3" id="category_id" name="category_id">
                        <option value="">همه دسته‌بندی‌ها</option>
                        <?php foreach($categories as$category): ?>
                            <option value="<?php echo $category->id; ?>" <?php echo (isset($filters['category_id']) && $filters['category_id'] ==$category->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="button" id="apply-filter-btn" class="btn btn-primary btn-sm rounded-3 w-100" title="اعمال فیلتر"><i class="fas fa-search"></i></button>
                    <a href="<?php echo APP_URL; ?>/index.php?page=products&action=index" class="btn btn-outline-secondary btn-sm rounded-3" title="بازنشانی"><i class="fas fa-undo"></i></a>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary small">
                    <tr>
                        <th class="ps-3" style="width: 70px;">تصویر</th>
                        <th>نام محصول</th>
                        <th>کد کالا (SKU)</th>
                        <th>دسته‌بندی</th>
                        <th>قیمت خرید (تومان)</th>
                        <th>قیمت فروش (تومان)</th>
                        <th>موجودی</th>
                        <th class="text-center no-print" style="width: 140px;">عملیات</th>
                    </tr>
                </thead>
                <tbody id="products-table-body">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as$product): ?>
                            <tr>
                                <td class="ps-3">
                                    <?php if (!empty($product->image_path)): ?>
                                        <img src="<?php echo APP_URL; ?>/public/uploads/products/<?php echo htmlspecialchars($product->image_path); ?>" alt="<?php echo htmlspecialchars($product->name); ?>" class="rounded-3 border object-fit-cover" style="width: 45px; height: 45px;">
                                    <?php else: ?>
                                        <img src="<?php echo APP_URL; ?>/assets/img/no-image.png" alt="No Image" class="rounded-3 border object-fit-cover" style="width: 45px; height: 45px;">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block"><?php echo sanitize($product->name); ?></span>
                                    <small class="text-muted d-block text-truncate" style="max-width: 220px;"><?php echo sanitize($product->description ?? ''); ?></small>
                                </td>
                                <td class="font-monospace small"><?php echo sanitize($product->sku); ?></td>
                                <td><span class="badge bg-light text-dark border"><?php echo sanitize($product->category_name ?? 'بدون دسته‌بندی'); ?></span></td>
                                <td class="font-monospace"><?php echo number_format($product->purchase_price); ?></td>
                                <td class="font-monospace fw-bold text-success"><?php echo number_format($product->sale_price); ?></td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1">
                                        <?php echo number_format($product->inventory); ?> <small><?php echo sanitize($product->unit_name ?? 'عدد'); ?></small>
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=inventory&action=product_flow_report&id=<?php echo $product->id; ?>" class="btn btn-outline-secondary" title="گزارش گردش کالا">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/index.php?page=products&action=edit&id=<?php echo $product->id; ?>" class="btn btn-outline-primary" title="ویرایش محصول">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=products&action=delete&id=<?php echo $product->id; ?>" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این محصول اطمینان دارید؟');">
                                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-box-open fs-2 mb-2 d-block opacity-50"></i>
                                هیچ محصولی یافت نشد.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filter-form');
        const searchInput = document.getElementById('search');
        const categorySelect = document.getElementById('category_id');
        const applyFilterBtn = document.getElementById('apply-filter-btn');
        const tableBody = document.getElementById('products-table-body');
        const url = '<?php echo APP_URL; ?>/index.php';

        const applyFilters = () => {
            const formData = new FormData(form);
            const params = new URLSearchParams(formData);
            
            fetch(url + '?' + params.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(async response => {
                const contentType = response.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    throw new Error("پاسخ دریافتی از سرور فرمت JSON ندارد.");
                }
                return response.json();
            })
            .then(data => {
                tableBody.innerHTML = '';
                if (data && data.length > 0) {
                    data.forEach(product => {
                        const productImage = product.image_path 
                            ? `<?php echo APP_URL; ?>/public/uploads/products/${product.image_path}` 
                            : `<?php echo APP_URL; ?>/assets/img/no-image.png`;
                        
                        const row = `
                            <tr>
                                <td class="ps-3">
                                    <img src="${productImage}" alt="${product.name}" class="rounded-3 border object-fit-cover" style="width: 45px; height: 45px;">
                                </td>
                                <td>
                                    <span class="fw-bold text-dark d-block">${product.name}</span>
                                    <small class="text-muted d-block text-truncate" style="max-width: 220px;">${product.description || ''}</small>
                                </td>
                                <td class="font-monospace small">${product.sku}</td>
                                <td><span class="badge bg-light text-dark border">${product.category_name || 'بدون دسته‌بندی'}</span></td>
                                <td class="font-monospace">${new Intl.NumberFormat().format(product.purchase_price)}</td>
                                <td class="font-monospace fw-bold text-success">${new Intl.NumberFormat().format(product.sale_price)}</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1">
                                        ${new Intl.NumberFormat().format(product.inventory)} <small>${product.unit_name || 'عدد'}</small>
                                    </span>
                                </td>
                                <td class="text-center no-print">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="${url}?page=inventory&action=product_flow_report&id=${product.id}" class="btn btn-outline-secondary" title="گزارش گردش کالا">
                                            <i class="fas fa-chart-line"></i>
                                        </a>
                                        <a href="${url}?page=products&action=edit&id=${product.id}" class="btn btn-outline-primary" title="ویرایش محصول">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="${url}?page=products&action=delete&id=${product.id}" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این محصول اطمینان دارید؟');">
                                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                } else {
                    tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-5"><i class="fas fa-box-open fs-2 mb-2 d-block opacity-50"></i>هیچ محصولی یافت نشد.</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4"><i class="fas fa-exclamation-circle me-1"></i> خطا در بارگذاری اطلاعات محصولات.</td></tr>`;
            });
        };

        searchInput.addEventListener('input', () => {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(applyFilters, 500);
        });
        categorySelect.addEventListener('change', applyFilters);
        applyFilterBtn.addEventListener('click', applyFilters);
    });
</script>