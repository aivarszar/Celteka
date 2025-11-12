<?php
require_once __DIR__ . '/../helpers/AuthHelper.php';
ob_start();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>🏪 <?= lang('app.name') ?></h1>
        <p><?= lang('app.tagline') ?></p>

        <form action="/search" method="get" class="d-flex gap-2 justify-center" style="max-width: 600px; margin: 0 auto;">
            <input type="text" name="q" class="form-control" placeholder="<?= lang('common.search') ?>..." required>
            <button type="submit" class="btn btn-primary"><?= lang('common.search') ?></button>
        </form>
    </div>
</section>

<!-- Latest Products -->
<section class="container mt-4">
    <h2>Jaunākie produkti un pakalpojumi</h2>

    <?php if (!empty($latestProducts)): ?>
        <div class="grid grid-4 mt-3">
            <?php foreach ($latestProducts as $product): ?>
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
    <?php else: ?>
        <div class="alert alert-info mt-3">
            <?= lang('product.no_products') ?>
        </div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="/products" class="btn btn-outline btn-lg">Skatīt visus produktus</a>
    </div>
</section>

<!-- Features Section -->
<section class="container mt-4 mb-4">
    <h2 class="text-center mb-3">Kāpēc izvēlēties mūs?</h2>

    <div class="grid grid-3">
        <div class="card text-center">
            <div class="card-body">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🌾</div>
                <h3>Vietējie ražotāji</h3>
                <p>Tieša saikne ar vietējiem ražotājiem un amatniekiem</p>
            </div>
        </div>

        <div class="card text-center">
            <div class="card-body">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🚚</div>
                <h3>Elastīga piegāde</h3>
                <p>Dažādi piegādes veidi - paņemšana, pārdevēja vai centralizēta piegāde</p>
            </div>
        </div>

        <div class="card text-center">
            <div class="card-body">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⭐</div>
                <h3>Divpusējās atsauksmes</h3>
                <p>Gan pircēji, gan pārdevēji var atstāt atsauksmes viens par otru</p>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$title = lang('app.name') . ' - ' . lang('app.tagline');
require __DIR__ . '/layout.php';
?>
