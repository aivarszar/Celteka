<!DOCTYPE html>
<html lang="<?= htmlspecialchars($config['app']['locale']) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Lang::get('my_profile') ?> - <?= htmlspecialchars($config['app']['name']) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php require_once ROOT_DIR . '/app/views/layout.php'; ?>

    <div class="container profile-container">
        <div class="profile-header">
            <h1><?= Lang::get('my_profile') ?></h1>
            <div class="profile-actions">
                <a href="/profile/edit" class="btn btn-primary"><?= Lang::get('edit_profile') ?></a>
                <a href="/profile/change-password" class="btn btn-secondary"><?= Lang::get('change_password') ?></a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="profile-content">
            <div class="profile-info">
                <h2><?= Lang::get('personal_information') ?></h2>

                <div class="info-group">
                    <label><?= Lang::get('name') ?>:</label>
                    <p><?= htmlspecialchars($user['name']) ?></p>
                </div>

                <div class="info-group">
                    <label><?= Lang::get('email') ?>:</label>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                </div>

                <div class="info-group">
                    <label><?= Lang::get('phone') ?>:</label>
                    <p><?= htmlspecialchars($user['phone'] ?? Lang::get('not_specified')) ?></p>
                </div>

                <div class="info-group">
                    <label><?= Lang::get('role') ?>:</label>
                    <p>
                        <?php if ($user['role'] === 'seller'): ?>
                            <span class="badge badge-seller"><?= Lang::get('seller') ?></span>
                        <?php else: ?>
                            <span class="badge badge-buyer"><?= Lang::get('buyer') ?></span>
                        <?php endif; ?>
                    </p>
                </div>

                <?php if (!empty($user['address']) || !empty($user['city']) || !empty($user['postal_code'])): ?>
                    <h3><?= Lang::get('address_information') ?></h3>

                    <?php if (!empty($user['address'])): ?>
                        <div class="info-group">
                            <label><?= Lang::get('address') ?>:</label>
                            <p><?= htmlspecialchars($user['address']) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($user['city'])): ?>
                        <div class="info-group">
                            <label><?= Lang::get('city') ?>:</label>
                            <p><?= htmlspecialchars($user['city']) ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($user['postal_code'])): ?>
                        <div class="info-group">
                            <label><?= Lang::get('postal_code') ?>:</label>
                            <p><?= htmlspecialchars($user['postal_code']) ?></p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="info-group">
                    <label><?= Lang::get('member_since') ?>:</label>
                    <p><?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>

            <div class="profile-stats">
                <h2><?= Lang::get('statistics') ?></h2>

                <div class="stats-grid">
                    <?php if ($user['role'] === 'seller'): ?>
                        <div class="stat-card">
                            <div class="stat-number"><?= $stats['products_count'] ?></div>
                            <div class="stat-label"><?= Lang::get('products') ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $stats['orders_count'] ?></div>
                            <div class="stat-label"><?= Lang::get('sales') ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $stats['reviews_count'] ?></div>
                            <div class="stat-label"><?= Lang::get('reviews') ?></div>
                        </div>
                    <?php else: ?>
                        <div class="stat-card">
                            <div class="stat-number"><?= $stats['orders_count'] ?></div>
                            <div class="stat-label"><?= Lang::get('orders') ?></div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?= $stats['reviews_count'] ?></div>
                            <div class="stat-label"><?= Lang::get('reviews') ?></div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($user['role'] === 'seller'): ?>
                    <div class="quick-actions">
                        <h3><?= Lang::get('quick_actions') ?></h3>
                        <a href="/seller/products" class="btn btn-outline"><?= Lang::get('my_products') ?></a>
                        <a href="/seller/products/create" class="btn btn-outline"><?= Lang::get('add_product') ?></a>
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
</body>
</html>
