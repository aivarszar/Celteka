<?php
ob_start();
?>

<div class="container order-detail-container">
    <div class="page-header">
        <div>
            <a href="/orders" class="back-link">← <?= Lang::get('back_to_orders') ?></a>
            <h1><?= Lang::get('order') ?> #<?= htmlspecialchars($order['id']) ?></h1>
        </div>
        <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
            <?= Lang::get('status_' . $order['status']) ?>
        </span>
    </div>

    <div class="order-details">
        <div class="detail-section">
            <h2><?= Lang::get('order_information') ?></h2>
            <div class="info-grid">
                <div class="info-item">
                    <label><?= Lang::get('order_number') ?>:</label>
                    <span>#<?= htmlspecialchars($order['id']) ?></span>
                </div>
                <div class="info-item">
                    <label><?= Lang::get('order_date') ?>:</label>
                    <span><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></span>
                </div>
                <div class="info-item">
                    <label><?= Lang::get('status') ?>:</label>
                    <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
                        <?= Lang::get('status_' . $order['status']) ?>
                    </span>
                </div>
                <?php if (!empty($order['payment_method'])): ?>
                    <div class="info-item">
                        <label><?= Lang::get('payment_method') ?>:</label>
                        <span><?= htmlspecialchars($order['payment_method']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-section">
            <h2><?= Lang::get('product_details') ?></h2>
            <div class="product-info">
                <h3><?= htmlspecialchars($order['product_title'] ?? Lang::get('product')) ?></h3>
                <div class="quantity-price">
                    <span><?= Lang::get('quantity') ?>: <?= htmlspecialchars($order['quantity']) ?></span>
                    <span class="price">€<?= number_format($order['price_per_unit'], 2) ?> / <?= Lang::get('unit') ?></span>
                </div>
            </div>
        </div>

        <?php if (!empty($order['delivery_address']) || !empty($order['delivery_method'])): ?>
            <div class="detail-section">
                <h2><?= Lang::get('delivery_information') ?></h2>
                <div class="info-grid">
                    <?php if (!empty($order['delivery_method'])): ?>
                        <div class="info-item full-width">
                            <label><?= Lang::get('delivery_method') ?>:</label>
                            <span><?= htmlspecialchars($order['delivery_method']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($order['delivery_address'])): ?>
                        <div class="info-item full-width">
                            <label><?= Lang::get('delivery_address') ?>:</label>
                            <span><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($order['notes'])): ?>
                        <div class="info-item full-width">
                            <label><?= Lang::get('notes') ?>:</label>
                            <span><?= nl2br(htmlspecialchars($order['notes'])) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="detail-section">
            <h2><?= Lang::get('order_summary') ?></h2>
            <div class="order-summary">
                <div class="summary-row">
                    <span><?= Lang::get('subtotal') ?>:</span>
                    <span>€<?= number_format($order['total_amount'], 2) ?></span>
                </div>
                <div class="summary-row total">
                    <span><?= Lang::get('total') ?>:</span>
                    <span>€<?= number_format($order['total_amount'], 2) ?></span>
                </div>
            </div>
        </div>

        <?php if ($order['status'] === 'pending' || $order['status'] === 'confirmed'): ?>
            <div class="order-actions">
                <form action="/order/<?= $order['id'] ?>/cancel" method="POST" onsubmit="return confirm('<?= Lang::get('confirm_cancel_order') ?>');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger"><?= Lang::get('cancel_order') ?></button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .order-detail-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e0e0e0;
    }

    .back-link {
        display: block;
        color: #2196F3;
        text-decoration: none;
        margin-bottom: 0.5rem;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .page-header h1 {
        margin: 0;
    }

    .status-badge {
        padding: 0.5rem 1rem;
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

    .order-details {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .detail-section {
        padding: 2rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .detail-section:last-child {
        border-bottom: none;
    }

    .detail-section h2 {
        margin-top: 0;
        margin-bottom: 1.5rem;
        color: #333;
        font-size: 1.25rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-item.full-width {
        grid-column: 1 / -1;
    }

    .info-item label {
        font-weight: 600;
        color: #666;
        font-size: 0.875rem;
    }

    .info-item span {
        color: #333;
    }

    .product-info h3 {
        margin-top: 0;
        margin-bottom: 1rem;
        color: #2196F3;
    }

    .quantity-price {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #666;
    }

    .quantity-price .price {
        font-weight: 600;
        color: #333;
        font-size: 1.125rem;
    }

    .order-summary {
        max-width: 400px;
        margin-left: auto;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .summary-row.total {
        font-size: 1.25rem;
        font-weight: bold;
        border-top: 2px solid #333;
        border-bottom: none;
        margin-top: 0.5rem;
        padding-top: 1rem;
        color: #2196F3;
    }

    .order-actions {
        padding: 2rem;
        background: #f5f5f5;
        text-align: right;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .order-summary {
            max-width: 100%;
        }

        .quantity-price {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = Lang::get('order') . ' #' . $order['id'] . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
