<?php ob_start(); ?>

<div class="container seller-orders-container">
    <div class="page-header">
        <h1>📋 Mani pasūtījumi (kā pārdevējs)</h1>
        <p>Pasūtījumi, kuros jūs esat pārdevējs</p>
    </div>

    <?php if (!empty($orders)): ?>
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <h3>Pasūtījums #<?= e($order['order_number']) ?></h3>
                            <span class="order-date"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></span>
                        </div>
                        <div class="order-status">
                            <?php
                            $statusClasses = [
                                'pending' => 'status-pending',
                                'confirmed' => 'status-confirmed',
                                'processing' => 'status-processing',
                                'ready' => 'status-ready',
                                'delivering' => 'status-delivering',
                                'completed' => 'status-completed',
                                'cancelled' => 'status-cancelled'
                            ];
                            $statusClass = $statusClasses[$order['status']] ?? 'status-pending';
                            $statusLabel = lang('order.status_' . $order['status']);
                            ?>
                            <span class="status-badge <?= $statusClass ?>">
                                <?= $statusLabel ?>
                            </span>
                        </div>
                    </div>

                    <div class="order-details">
                        <div class="detail-row">
                            <span class="detail-label">Pircējs:</span>
                            <span class="detail-value"><strong><?= e($order['buyer_name'] ?? 'Nav norādīts') ?></strong></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Summa:</span>
                            <span class="detail-value"><strong>€<?= number_format($order['total_amount'], 2) ?></strong></span>
                        </div>
                    </div>

                    <div class="order-actions">
                        <a href="/order/<?= $order['id'] ?>" class="btn btn-primary">
                            Skatīt detaļas
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-orders">
            <div class="no-orders-icon">📭</div>
            <h2>Nav pasūtījumu</h2>
            <p>Jums vēl nav pasūtījumu kā pārdevējam.</p>
            <a href="/seller/products" class="btn btn-primary">Pārvaldīt produktus</a>
        </div>
    <?php endif; ?>
</div>

<style>
.seller-orders-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    margin-bottom: 0.5rem;
    color: #333;
}

.page-header p {
    color: #666;
    margin: 0;
}

.orders-list {
    display: grid;
    gap: 1.5rem;
}

.order-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1.5rem;
    transition: box-shadow 0.3s;
}

.order-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e0e0e0;
}

.order-info h3 {
    margin: 0 0 0.25rem 0;
    color: #333;
}

.order-date {
    font-size: 0.875rem;
    color: #666;
}

.status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-confirmed {
    background: #cfe2ff;
    color: #084298;
}

.status-processing {
    background: #e7f3ff;
    color: #0c5cb3;
}

.status-ready {
    background: #d1ecf1;
    color: #0c5460;
}

.status-delivering {
    background: #d4edda;
    color: #155724;
}

.status-completed {
    background: #28a745;
    color: white;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.order-details {
    display: grid;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.detail-label {
    color: #666;
}

.detail-value {
    color: #333;
}

.order-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}

.no-orders {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.no-orders-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.no-orders h2 {
    color: #333;
    margin-bottom: 0.5rem;
}

.no-orders p {
    color: #666;
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .order-header {
        flex-direction: column;
        gap: 1rem;
    }

    .order-actions {
        flex-direction: column;
    }

    .order-actions .btn {
        width: 100%;
    }
}
</style>

<?php
$content = ob_get_clean();
$title = 'Mani pasūtījumi - Pārdevējs - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
