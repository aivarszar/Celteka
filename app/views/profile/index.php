<?php
ob_start();
?>

<div class="container profile-container">
    <div class="profile-header">
        <h1><?= lang('profile.my_profile') ?></h1>
        <div class="profile-actions">
            <a href="/profile/edit" class="btn btn-primary"><?= lang('profile.edit_profile') ?></a>
            <a href="/profile/change-password" class="btn btn-secondary"><?= lang('profile.change_password') ?></a>
        </div>
    </div>

    <div class="profile-content">
        <div class="profile-info">
            <h2><?= lang('profile.personal_information') ?></h2>

            <div class="info-group">
                <label><?= lang('common.name') ?>:</label>
                <p><?= e($user['full_name'] ?? '') ?></p>
            </div>

            <div class="info-group">
                <label><?= lang('common.email') ?>:</label>
                <p><?= e($user['email']) ?></p>
            </div>

            <div class="info-group">
                <label><?= lang('common.phone') ?>:</label>
                <p><?= e($user['phone'] ?? lang('profile.not_specified')) ?></p>
            </div>

            <div class="info-group">
                <label><?= lang('common.role') ?>:</label>
                <p>
                    <?php if ($user['role'] === 'seller'): ?>
                        <span class="badge badge-seller"><?= lang('common.seller') ?></span>
                    <?php else: ?>
                        <span class="badge badge-buyer"><?= lang('common.buyer') ?></span>
                    <?php endif; ?>
                </p>
            </div>

            <?php if (!empty($user['address']) || !empty($user['city']) || !empty($user['postal_code'])): ?>
                <h3><?= lang('profile.address_information') ?></h3>

                <?php if (!empty($user['address'])): ?>
                    <div class="info-group">
                        <label><?= lang('profile.address') ?>:</label>
                        <p><?= e($user['address']) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['city'])): ?>
                    <div class="info-group">
                        <label><?= lang('profile.city') ?>:</label>
                        <p><?= e($user['city']) ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['postal_code'])): ?>
                    <div class="info-group">
                        <label><?= lang('profile.postal_code') ?>:</label>
                        <p><?= e($user['postal_code']) ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="info-group">
                <label><?= lang('profile.member_since') ?>:</label>
                <p><?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
            </div>
        </div>

        <div class="profile-stats">
            <h2><?= lang('profile.statistics') ?></h2>

            <div class="stats-grid">
                <?php if ($user['role'] === 'seller'): ?>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['products_count'] ?></div>
                        <div class="stat-label"><?= lang('common.products') ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['orders_count'] ?></div>
                        <div class="stat-label"><?= lang('common.sales') ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['reviews_count'] ?></div>
                        <div class="stat-label"><?= lang('common.reviews') ?></div>
                    </div>
                <?php else: ?>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['orders_count'] ?></div>
                        <div class="stat-label"><?= lang('common.orders') ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $stats['reviews_count'] ?></div>
                        <div class="stat-label"><?= lang('common.reviews') ?></div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($user['role'] === 'seller'): ?>
                <div class="quick-actions">
                    <h3><?= lang('quick_actions') ?></h3>
                    <a href="/seller/products" class="btn btn-outline"><?= lang('my_products') ?></a>
                    <a href="/seller/products/create" class="btn btn-outline"><?= lang('add_product') ?></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
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

    .profile-actions {
        display: flex;
        gap: 1rem;
    }

    .profile-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
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
        margin-bottom: 2rem;
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

    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .quick-actions h3 {
        margin-bottom: 0.5rem;
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
    }
</style>

<?php
$content = ob_get_clean();
$title = lang('my_profile') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
