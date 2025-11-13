<?php ob_start(); ?>

<div class="container admin-container">
    <div class="admin-header">
        <h1>🔧 <?= lang('admin.admin_panel') ?></h1>
        <p><?= lang('app.name') ?> - Administratora panelis</p>
    </div>

    <!-- Platform Statistics -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <div class="stat-value"><?= $stats['total_users'] ?? 0 ?></div>
                <div class="stat-label"><?= lang('admin.users') ?></div>
                <div class="stat-detail">
                    <small>👨‍💼 <?= $stats['admin_count'] ?? 0 ?> admin | 
                    🏪 <?= $stats['seller_count'] ?? 0 ?> seller | 
                    🛒 <?= $stats['buyer_count'] ?? 0 ?> buyer</small>
                </div>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon">📦</div>
            <div class="stat-content">
                <div class="stat-value"><?= $stats['total_products'] ?? 0 ?></div>
                <div class="stat-label"><?= lang('admin.products') ?></div>
                <div class="stat-detail"><small><?= lang('common.active') ?></small></div>
            </div>
        </div>

        <div class="stat-card orange">
            <div class="stat-icon">📋</div>
            <div class="stat-content">
                <div class="stat-value"><?= $stats['total_orders'] ?? 0 ?></div>
                <div class="stat-label"><?= lang('admin.orders') ?></div>
                <div class="stat-detail">
                    <small>⏳ <?= $stats['pending_orders'] ?? 0 ?> pending | 
                    ✅ <?= $stats['completed_orders'] ?? 0 ?> completed</small>
                </div>
            </div>
        </div>

        <div class="stat-card purple">
            <div class="stat-icon">💰</div>
            <div class="stat-content">
                <div class="stat-value">€<?= number_format($stats['total_revenue'] ?? 0, 2) ?></div>
                <div class="stat-label"><?= lang('order.total_revenue') ?></div>
                <div class="stat-detail"><small><?= lang('order.completed_orders') ?></small></div>
            </div>
        </div>

        <div class="stat-card pink">
            <div class="stat-icon">⭐</div>
            <div class="stat-content">
                <div class="stat-value"><?= $stats['total_reviews'] ?? 0 ?></div>
                <div class="stat-label"><?= lang('common.reviews') ?></div>
                <div class="stat-detail"><small><?= lang('review.reviews') ?></small></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-section">
        <h2>Ātras darbības</h2>
        <div class="quick-actions-grid">
            <a href="/admin/users" class="action-card">
                <span class="action-icon">👥</span>
                <span class="action-label"><?= lang('admin.manage') ?> <?= lang('admin.users') ?></span>
            </a>
            <a href="/admin/products" class="action-card">
                <span class="action-icon">📦</span>
                <span class="action-label"><?= lang('admin.manage') ?> <?= lang('admin.products') ?></span>
            </a>
            <a href="/admin/orders" class="action-card">
                <span class="action-icon">📋</span>
                <span class="action-label"><?= lang('admin.manage') ?> <?= lang('admin.orders') ?></span>
            </a>
            <a href="/admin/reviews" class="action-card">
                <span class="action-icon">⭐</span>
                <span class="action-label">Pārvaldīt atsauksmes</span>
            </a>
            <a href="/admin/migrate" class="action-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <span class="action-icon">🔄</span>
                <span class="action-label">Lomu migrācija</span>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="activity-section">
        <div class="activity-column">
            <h3>Pēdējie lietotāji</h3>
            <div class="activity-list">
                <?php if (!empty($stats['recent_users'])): ?>
                    <?php foreach ($stats['recent_users'] as $user): ?>
                        <div class="activity-item">
                            <div class="activity-icon">👤</div>
                            <div class="activity-info">
                                <strong><?= e($user['full_name']) ?></strong>
                                <small><?= e($user['email']) ?></small>
                            </div>
                            <div class="activity-date">
                                <small><?= date('d.m.Y', strtotime($user['created_at'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-data">Nav datu</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="activity-column">
            <h3>Pēdējie pasūtījumi</h3>
            <div class="activity-list">
                <?php if (!empty($stats['recent_orders'])): ?>
                    <?php foreach ($stats['recent_orders'] as $order): ?>
                        <div class="activity-item">
                            <div class="activity-icon">📦</div>
                            <div class="activity-info">
                                <strong>#<?= $order['id'] ?> - <?= e($order['buyer_name']) ?></strong>
                                <small class="status-<?= e($order['status']) ?>">
                                    €<?= number_format($order['total_amount'], 2) ?> - <?= lang('status_' . $order['status']) ?>
                                </small>
                            </div>
                            <div class="activity-date">
                                <small><?= date('d.m.Y', strtotime($order['created_at'])) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-data">Nav datu</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.admin-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.admin-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid #667eea;
}

.admin-header h1 {
    margin-bottom: 0.5rem;
    color: #333;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    border-left: 4px solid;
}

.stat-card.blue { border-color: #667eea; }
.stat-card.green { border-color: #43e97b; }
.stat-card.orange { border-color: #fa709a; }
.stat-card.purple { border-color: #a18cd1; }
.stat-card.pink { border-color: #fbc2eb; }

.stat-icon {
    font-size: 3rem;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #666;
    margin-top: 0.25rem;
}

.stat-detail {
    margin-top: 0.5rem;
    color: #999;
}

.quick-actions-section, .activity-section {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.quick-actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    transition: transform 0.2s;
}

.action-card:hover {
    transform: translateY(-4px);
}

.action-icon {
    font-size: 2.5rem;
}

.action-label {
    font-weight: 600;
    text-align: center;
}

.activity-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.activity-column h3 {
    margin-bottom: 1rem;
    color: #333;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem;
    background: #f9f9f9;
    border-radius: 8px;
}

.activity-icon {
    font-size: 1.5rem;
}

.activity-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.activity-info small {
    color: #666;
    font-size: 0.875rem;
}

.activity-date {
    text-align: right;
}

.no-data {
    text-align: center;
    color: #999;
    padding: 2rem;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .activity-section {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
$content = ob_get_clean();
$title = lang('admin.admin_panel') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
