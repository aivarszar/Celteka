<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
ob_start();
?>

<section class="container mt-4 mb-4">
    <div class="grid grid-2">
        <!-- Product Images -->
        <div>
            <div class="card">
                <?php if (!empty($images)): ?>
                    <img src="<?= e($images[0]['image_path']) ?>" alt="<?= e($product['title']) ?>" class="card-img" style="height: 400px;">
                    <?php if (count($images) > 1): ?>
                        <div class="p-2 d-flex gap-1">
                            <?php foreach ($images as $image): ?>
                                <img src="<?= e($image['image_path']) ?>" alt="<?= e($product['title']) ?>"
                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; cursor: pointer;">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="card-img" style="height: 400px; background: var(--light-gray); display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 6rem; color: var(--gray);">📦</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Details -->
        <div>
            <div class="card">
                <div class="card-body">
                    <h1><?= e($product['title']) ?></h1>

                    <div class="product-price mt-2 mb-2">
                        €<?= number_format($product['price'], 2) ?>
                    </div>

                    <div class="mb-3">
                        <?php
                        $typeLabels = [
                            'product' => '📦 ' . lang('product.type_product'),
                            'service' => '🔧 ' . lang('product.type_service'),
                            'unique_service' => '⭐ ' . lang('product.type_unique'),
                        ];
                        ?>
                        <span style="background: var(--light-gray); padding: 0.5rem 1rem; border-radius: 20px;">
                            <?= $typeLabels[$product['type']] ?? $product['type'] ?>
                        </span>

                        <?php if ($product['type'] === 'product'): ?>
                            <span style="background: <?= $product['stock_quantity'] > 0 ? 'var(--success)' : 'var(--danger)' ?>; color: white; padding: 0.5rem 1rem; border-radius: 20px; margin-left: 0.5rem;">
                                <?= $product['stock_quantity'] > 0 ? lang('product.in_stock') : lang('product.out_of_stock') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <h3><?= lang('product.description') ?></h3>
                        <p><?= nl2br(e($product['description'])) ?></p>
                    </div>

                    <?php if ($product['location_name']): ?>
                        <div class="mb-3">
                            <strong>📍 <?= lang('product.location') ?>:</strong>
                            <?= e($product['location_name']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <strong>👁️ <?= lang('product.views') ?>:</strong>
                        <?= $product['views_count'] ?>
                    </div>

                    <hr>

                    <!-- Seller Info -->
                    <div class="mb-3">
                        <h3><?= lang('product.seller') ?></h3>
                        <div class="d-flex justify-between align-center">
                            <div>
                                <strong><?= e($product['seller_name']) ?></strong>
                                <div>
                                    <span class="rating">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <?= $i < round($sellerRating) ? '⭐' : '☆' ?>
                                        <?php endfor; ?>
                                    </span>
                                    <small>(<?= $sellerReviewCount ?> <?= lang('common.reviews') ?>)</small>
                                </div>
                            </div>
                            <a href="/user/<?= $product['seller_id'] ?>" class="btn btn-outline btn-sm">
                                <?= lang('product.view_profile') ?>
                            </a>
                        </div>
                    </div>

                    <hr>

                    <!-- Actions -->
                    <?php if (Session::isLoggedIn() && Session::getUserId() != $product['seller_id']): ?>
                        <form action="/cart/add/<?= $product['id'] ?>" method="post">
                            <?= csrf_field() ?>
                            <?php if ($product['type'] === 'product'): ?>
                                <div class="form-group">
                                    <label><?= lang('order.quantity') ?>:</label>
                                    <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>" class="form-control">
                                </div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-primary" style="width: 100%;" <?= ($product['type'] === 'product' && $product['stock_quantity'] <= 0) ? 'disabled' : '' ?>>
                                🛒 <?= lang('order.add_to_cart') ?>
                            </button>
                        </form>
                    <?php elseif (Session::isLoggedIn() && Session::getUserId() == $product['seller_id']): ?>
                        <a href="/seller/products/<?= $product['id'] ?>/edit" class="btn btn-primary" style="width: 100%;">
                            ✏️ <?= lang('product.edit_product') ?>
                        </a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-primary" style="width: 100%;">
                            <?= lang('product.login_to_order') ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$title = e($product['title']) . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
