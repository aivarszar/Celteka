<?php
ob_start();
?>

<div class="container profile-container">
    <div class="profile-header">
        <div>
            <h1><?= e($user['full_name'] ?? 'Lietotājs') ?></h1>
            <?php if ($user['role'] === 'seller'): ?>
                <span class="badge badge-seller"><?= lang('common.seller') ?? 'Pārdevējs' ?></span>
            <?php else: ?>
                <span class="badge badge-buyer"><?= lang('common.buyer') ?? 'Pircējs' ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-info">
            <h2><?= lang('profile.about') ?? 'Informācija' ?></h2>

            <?php if (!empty($user['city'])): ?>
                <div class="info-group">
                    <label><?= lang('profile.city') ?? 'Pilsēta' ?>:</label>
                    <p><?= e($user['city']) ?></p>
                </div>
            <?php endif; ?>

            <div class="info-group">
                <label><?= lang('profile.member_since') ?? 'Reģistrēts' ?>:</label>
                <p><?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
            </div>
        </div>

        <div class="profile-stats">
            <h2><?= lang('profile.statistics') ?? 'Statistika' ?></h2>

            <div class="stats-grid">
                <?php if ($user['role'] === 'seller'): ?>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['products_count'] ?></div>
                        <div class="stat-label"><?= lang('common.products') ?? 'Produkti' ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['orders_count'] ?></div>
                        <div class="stat-label"><?= lang('common.sales') ?? 'Pārdošanas' ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['reviews_count'] ?></div>
                        <div class="stat-label"><?= lang('common.reviews') ?? 'Atsauksmes' ?></div>
                    </div>
                <?php else: ?>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['orders_count'] ?></div>
                        <div class="stat-label"><?= lang('common.orders') ?? 'Pasūtījumi' ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['reviews_count'] ?></div>
                        <div class="stat-label"><?= lang('common.reviews') ?? 'Atsauksmes' ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($user['role'] === 'seller' && !empty($products)): ?>
        <div class="seller-products">
            <h2><?= lang('seller.products_title') ?? 'Pārdevēja produkti' ?></h2>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php if (!empty($product['primary_image'])): ?>
                            <img src="<?= e($product['primary_image']) ?>" alt="<?= e($product['title']) ?>" class="product-image">
                        <?php else: ?>
                            <div class="product-image-placeholder">📦</div>
                        <?php endif; ?>
                        <div class="product-info">
                            <h3><?= e($product['title']) ?></h3>
                            <p class="product-price">€<?= number_format($product['price'], 2) ?></p>
                            <a href="/product/<?= e($product['slug']) ?>" class="btn btn-sm btn-outline">
                                <?= lang('common.view') ?? 'Skatīt' ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .profile-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e0e0e0;
    }

    .profile-header h1 {
        margin: 0 0 0.5rem 0;
    }

    .profile-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .profile-info, .profile-stats {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .info-group {
        margin-bottom: 1.5rem;
    }

    .info-group label {
        display: block;
        font-weight: 600;
        color: #555;
        margin-bottom: 0.5rem;
    }

    .info-group p {
        margin: 0;
        color: #333;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .badge-seller {
        background: #4CAF50;
        color: white;
    }

    .badge-buyer {
        background: #2196F3;
        color: white;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
    }

    .stat-card {
        text-align: center;
        padding: 1.5rem;
        background: #f5f5f5;
        border-radius: 8px;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #2196F3;
    }

    .stat-label {
        color: #666;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .seller-products {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .seller-products h2 {
        margin-top: 0;
        margin-bottom: 1.5rem;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .product-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .product-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .product-image, .product-image-placeholder {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .product-image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f5f5;
        font-size: 3rem;
    }

    .product-info {
        padding: 1rem;
    }

    .product-info h3 {
        margin: 0 0 0.5rem 0;
        font-size: 1rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .product-price {
        color: #2196F3;
        font-weight: bold;
        font-size: 1.25rem;
        margin: 0.5rem 0;
    }

    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .profile-content {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = e($user['full_name'] ?? 'Lietotājs') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
