<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
ob_start();
?>

<section class="container mt-4 mb-4">
    <h1><?= lang('nav.products') ?></h1>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="/products" method="get" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="<?= lang('common.search') ?>..." value="<?= e($_GET['search'] ?? '') ?>">

                <select name="type" class="form-control">
                    <option value=""><?= lang('product.all_types') ?></option>
                    <option value="product" <?= ($_GET['type'] ?? '') === 'product' ? 'selected' : '' ?>><?= lang('product.products') ?></option>
                    <option value="service" <?= ($_GET['type'] ?? '') === 'service' ? 'selected' : '' ?>><?= lang('product.services') ?></option>
                    <option value="unique_service" <?= ($_GET['type'] ?? '') === 'unique_service' ? 'selected' : '' ?>><?= lang('product.unique_services') ?></option>
                </select>

                <button type="submit" class="btn btn-primary"><?= lang('common.filter') ?></button>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    <?php if (!empty($products)): ?>
        <div class="grid grid-4">
            <?php foreach ($products as $product): ?>
                <div class="card product-card">
                    <?php if ($product['primary_image']): ?>
                        <img src="<?= e($product['primary_image']) ?>" alt="<?= e($product['title']) ?>" class="card-img">
                    <?php else: ?>
                        <div class="card-img" style="background: var(--light-gray); display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 3rem; color: var(--gray);">📦</span>
                        </div>
                    <?php endif; ?>

                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="/product/<?= e($product['slug']) ?>"><?= e($product['title']) ?></a>
                        </h3>

                        <p class="card-text"><?= e(mb_substr($product['description'], 0, 100)) ?>...</p>

                        <div class="product-price">
                            €<?= number_format($product['price'], 2) ?>
                        </div>

                        <div class="product-meta">
                            <span class="product-seller">
                                👤 <?= e($product['seller_name']) ?>
                            </span>
                            <?php if ($product['location_name']): ?>
                                <span class="product-location">
                                    📍 <?= e($product['location_name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="mt-2">
                            <a href="/product/<?= e($product['slug']) ?>" class="btn btn-primary" style="width: 100%;">
                                <?= lang('product.view_product') ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="text-center mt-4">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?><?= !empty($filters['search']) ? '&search=' . urlencode($filters['search']) : '' ?><?= !empty($filters['type']) ? '&type=' . urlencode($filters['type']) : '' ?>"
                       class="btn <?= $i === $currentPage ? 'btn-primary' : 'btn-outline' ?> btn-sm">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info">
            <?= lang('product.no_products') ?>
        </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
$title = lang('nav.products') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
