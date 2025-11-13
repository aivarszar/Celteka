<?php ob_start(); ?>

<div class="container admin-container">
    <div class="admin-header">
        <h1>📋 <?= lang('admin.orders') ?></h1>
        <p><?= lang('admin.manage') ?> <?= lang('admin.orders') ?></p>
    </div>

    <?php if (!empty($orders)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= lang('order.order_number') ?></th>
                        <th><?= lang('order.buyer') ?></th>
                        <th><?= lang('order.seller') ?></th>
                        <th><?= lang('common.items') ?></th>
                        <th><?= lang('order.total') ?></th>
                        <th><?= lang('order.status') ?></th>
                        <th><?= lang('order.order_date') ?></th>
                        <th><?= lang('common.actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['id'] ?></td>
                            <td>
                                <strong>#<?= e($order['order_number']) ?></strong>
                            </td>
                            <td>
                                <strong><?= e($order['buyer_name']) ?></strong>
                            </td>
                            <td>
                                <strong><?= e($order['seller_name']) ?></strong>
                            </td>
                            <td class="text-center">
                                <span class="items-count"><?= $order['items_count'] ?></span>
                            </td>
                            <td>
                                <strong style="color: #28a745;">€<?= number_format($order['total_amount'], 2) ?></strong>
                            </td>
                            <td>
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
                            </td>
                            <td>
                                <small><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></small>
                            </td>
                            <td>
                                <a href="/order/<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                                    👁️ <?= lang('order.view_details') ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Statistics Summary -->
        <div class="stats-summary">
            <?php
            $totalOrders = count($orders);
            $totalRevenue = array_sum(array_column($orders, 'total_amount'));
            $statusCounts = [];
            foreach ($orders as $order) {
                $status = $order['status'];
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
            }
            ?>
            <div class="summary-card">
                <div class="summary-icon">📊</div>
                <div class="summary-content">
                    <div class="summary-value"><?= $totalOrders ?></div>
                    <div class="summary-label"><?= lang('order.total_orders') ?></div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon">💰</div>
                <div class="summary-content">
                    <div class="summary-value">€<?= number_format($totalRevenue, 2) ?></div>
                    <div class="summary-label"><?= lang('order.total_revenue') ?></div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon">⏳</div>
                <div class="summary-content">
                    <div class="summary-value"><?= $statusCounts['pending'] ?? 0 ?></div>
                    <div class="summary-label"><?= lang('order.pending_orders') ?></div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon">✅</div>
                <div class="summary-content">
                    <div class="summary-value"><?= $statusCounts['completed'] ?? 0 ?></div>
                    <div class="summary-label"><?= lang('order.completed_orders') ?></div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="no-data"><?= lang('order.no_orders') ?></div>
    <?php endif; ?>
</div>

<style>
.table-responsive {
    overflow-x: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
}

.admin-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.admin-table th,
.admin-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.admin-table tbody tr:hover {
    background: #f9f9f9;
}

.text-center {
    text-align: center !important;
}

.items-count {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: #e3f2fd;
    border-radius: 12px;
    font-weight: 600;
    color: #1976d2;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
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

.stats-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.summary-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.summary-icon {
    font-size: 2.5rem;
}

.summary-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
}

.summary-label {
    font-size: 0.875rem;
    color: #666;
    margin-top: 0.25rem;
}

.no-data {
    text-align: center;
    color: #999;
    padding: 2rem;
    background: white;
    border-radius: 8px;
}
</style>

<?php
$content = ob_get_clean();
$title = lang('admin.orders') . ' - ' . lang('admin.admin_panel') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
