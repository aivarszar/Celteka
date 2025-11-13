<?php
ob_start();
?>

<div class="container orders-container">
    <div class="page-header">
        <h1><?= Lang::get('orders') ?></h1>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <h2><?= Lang::get('no_orders') ?></h2>
            <p><?= Lang::get('no_orders_message') ?></p>
            <a href="/products" class="btn btn-primary"><?= Lang::get('browse_products') ?></a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-number">
                            <strong><?= Lang::get('order') ?> #<?= htmlspecialchars($order['id']) ?></strong>
                            <span class="order-date"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></span>
                        </div>
                        <div class="order-status">
                            <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
                                <?= Lang::get('status_' . $order['status']) ?>
                            </span>
                        </div>
                    </div>

                    <div class="order-body">
                        <div class="order-product">
                            <h3>
                                <?php if (!empty($order['product_title'])): ?>
                                    <?= htmlspecialchars($order['product_title']) ?>
                                    <?php if ($order['items_count'] > 1): ?>
                                        <span class="items-badge">+<?= ($order['items_count'] - 1) ?> <?= Lang::get('more_items') ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?= $order['items_count'] ?> <?= Lang::get('products') ?>
                                <?php endif; ?>
                            </h3>
                            <div class="order-details">
                                <?php if (!empty($order['buyer_name'])): ?>
                                    <p><strong><?= Lang::get('buyer') ?>:</strong> <?= htmlspecialchars($order['buyer_name']) ?></p>
                                    <p><strong><?= Lang::get('email') ?>:</strong> <?= htmlspecialchars($order['buyer_email']) ?></p>
                                <?php elseif (!empty($order['seller_name'])): ?>
                                    <p><strong><?= Lang::get('seller') ?>:</strong> <?= htmlspecialchars($order['seller_name']) ?></p>
                                <?php endif; ?>
                                <p><strong><?= Lang::get('items') ?>:</strong> <?= htmlspecialchars($order['items_count']) ?></p>
                            </div>
                        </div>

                        <div class="order-price">
                            <div class="price-amount">€<?= number_format($order['total_amount'], 2) ?></div>
                        </div>
                    </div>

                    <div class="order-footer">
                        <a href="/order/<?= $order['id'] ?>" class="btn btn-outline btn-sm">
                            <?= Lang::get('view_details') ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .orders-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .page-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e0e0e0;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .empty-state svg {
        color: #999;
        margin-bottom: 1rem;
    }

    .empty-state h2 {
        color: #333;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #666;
        margin-bottom: 2rem;
    }

    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .order-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background: #f5f5f5;
        border-bottom: 1px solid #e0e0e0;
    }

    .order-number strong {
        display: block;
        color: #333;
        font-size: 1.1rem;
    }

    .order-date {
        color: #666;
        font-size: 0.875rem;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-pending { background: #fff3cd; color: #856404; }
    .status-confirmed { background: #d1ecf1; color: #0c5460; }
    .status-processing { background: #d1ecf1; color: #0c5460; }
    .status-ready { background: #cce5ff; color: #004085; }
    .status-delivering { background: #cce5ff; color: #004085; }
    .status-completed { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    .order-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem;
    }

    .order-product h3 {
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .items-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        background: #e3f2fd;
        color: #1976D2;
        border-radius: 4px;
        font-weight: 600;
    }

    .order-product a {
        color: #2196F3;
        text-decoration: none;
    }

    .order-product a:hover {
        text-decoration: underline;
    }

    .order-details {
        font-size: 0.875rem;
        color: #666;
    }

    .order-details p {
        margin: 0.25rem 0;
    }

    .order-price {
        text-align: right;
    }

    .price-amount {
        font-size: 1.5rem;
        font-weight: bold;
        color: #2196F3;
    }

    .order-footer {
        padding: 1rem 1.5rem;
        background: #f5f5f5;
        border-top: 1px solid #e0e0e0;
        text-align: right;
    }

    @media (max-width: 768px) {
        .order-body {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .order-price {
            width: 100%;
            text-align: left;
        }

        .order-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = Lang::get('orders') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
