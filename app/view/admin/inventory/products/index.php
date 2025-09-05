<?php
// app/views/admin/inventory/products/index.php - نمای جدید با فیلتر، جستجو و AJAX
global $auth;
$products = $products ?? [];
$categories = $categories ?? [];
$filters = $filters ?? [];
?>

<style>
    .product-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 5px;
    }
    .product-table-card {
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .product-name {
        font-weight: 600;
        color: #333;
    }
    .text-muted.small {
        font-size: 0.8rem;
    }
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

<h1 class="mb-4">مدیریت محصولات</h1>

<div class="card product-table-card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0">لیست محصولات</h5>
        <div class="d-flex align-items-center">
            <?php if ($auth->hasRole(['admin', 'accountant'])): ?>
                <a href="<?php echo APP_URL; ?>/index.php?page=products&action=create" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> افزودن محصول جدید
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <form id="filter-form" class="mb-4 p-3 border rounded-3 bg-light" action="<?php echo APP_URL; ?>/index.php" method="GET">
            <input type="hidden" name="page" value="products">
            <input type="hidden" name="action" value="index">
            <div class="d-flex flex-wrap align-items-end gap-3">
                <div class="flex-grow-1">
                    <label for="search" class="form-label text-muted small">جستجو:</label>
                    <input type="text" class="form-control form-control-sm" id="search" name="search" placeholder="نام یا کد کالا" value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>">
                </div>
                <div class="flex-grow-1">
                    <label for="category_id" class="form-label text-muted small">دسته‌بندی:</label>
                    <select class="form-select form-select-sm" id="category_id" name="category_id">
                        <option value="">همه دسته‌بندی‌ها</option>
                        <?php foreach($categories as $category): ?>
                            <option value="<?php echo $category->id; ?>" <?php echo (isset($filters['category_id']) && $filters['category_id'] == $category->id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" id="apply-filter-btn" class="btn btn-secondary btn-sm">اعمال فیلتر</button>
                    <a href="<?php echo APP_URL; ?>/index.php?page=products&action=index" class="btn btn-outline-secondary btn-sm">پاک کردن فیلتر</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>تصویر</th>
                        <th>نام محصول</th>
                        <th>کد کالا (SKU)</th>
                        <th>دسته‌بندی</th>
                        <th>قیمت خرید</th>
                        <th>قیمت فروش</th>
                        <th>موجودی</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody id="products-table-body">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($product->image_path)): ?>
                                        <img src="<?php echo APP_URL; ?>/public/uploads/products/<?php echo htmlspecialchars($product->image_path); ?>" alt="<?php echo htmlspecialchars($product->name); ?>" class="product-image">
                                    <?php else: ?>
                                        <img src="<?php echo APP_URL; ?>/assets/img/no-image.png" alt="No Image" class="product-image">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="product-name"><?php echo sanitize($product->name); ?></span>
                                    <small class="d-block text-muted"><?php echo sanitize($product->description ?? ''); ?></small>
                                </td>
                                <td><?php echo sanitize($product->sku); ?></td>
                                <td><?php echo sanitize($product->category_name ?? 'بدون دسته‌بندی'); ?></td>
                                <td><?php echo number_format($product->purchase_price); ?></td>
                                <td><?php echo number_format($product->sale_price); ?></td>
                                <td><?php echo number_format($product->inventory); ?> (<?php echo sanitize($product->unit ?? 'عدد'); ?>)</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo APP_URL; ?>/index.php?page=products&action=edit&id=<?php echo $product->id; ?>" class="btn btn-sm btn-info" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo APP_URL; ?>/index.php?page=products&action=delete&id=<?php echo $product->id; ?>" method="POST" class="d-inline-block">
                                            <button type="submit" class="btn btn-sm btn-danger" data-confirm-delete title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">هیچ محصولی یافت نشد.</td>
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
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(product => {
                        const productImage = product.image_path 
                            ? `<?php echo APP_URL; ?>/public/uploads/products/${product.image_path}` 
                            : `<?php echo APP_URL; ?>/assets/img/no-image.png`;
                        
                        const row = `
                            <tr>
                                <td>
                                    <img src="${productImage}" alt="${product.name}" class="product-image">
                                </td>
                                <td>
                                    <span class="product-name">${product.name}</span>
                                    <small class="d-block text-muted">${product.description || ''}</small>
                                </td>
                                <td>${product.sku}</td>
                                <td>${product.category_name || 'بدون دسته‌بندی'}</td>
                                <td>${new Intl.NumberFormat().format(product.purchase_price)}</td>
                                <td>${new Intl.NumberFormat().format(product.sale_price)}</td>
                                <td>${new Intl.NumberFormat().format(product.inventory)} (${product.unit || 'عدد'})</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="${url}?page=products&action=edit&id=${product.id}" class="btn btn-sm btn-info" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="${url}?page=products&action=delete&id=${product.id}" method="POST" class="d-inline-block">
                                            <button type="submit" class="btn btn-sm btn-danger" data-confirm-delete title="حذف">
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
                    tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4">هیچ محصولی یافت نشد.</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                tableBody.innerHTML = `<tr><td colspan="8" class="text-center text-danger py-4">خطا در بارگذاری اطلاعات.</td></tr>`;
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