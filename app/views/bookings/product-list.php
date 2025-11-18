<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
ob_start();
?>

<div class="container product-bookings-container">
    <div class="page-header">
        <div>
            <a href="/seller/products" class="back-link">← Atpakaļ uz produktiem</a>
            <h1>Pieteikšanās: <?= e($product['title']) ?></h1>
        </div>
    </div>

    <!-- Statistika -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $stats['total_bookings'] ?></div>
            <div class="stat-label">Kopā pieteikšanās</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['pending_count'] ?></div>
            <div class="stat-label">Neapstrādātas</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['confirmed_count'] ?></div>
            <div class="stat-label">Apstiprinātas</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= $stats['completed_count'] ?></div>
            <div class="stat-label">Pabeigtas</div>
        </div>
    </div>

    <!-- Kapacitātes informācija -->
    <?php if (isset($meta['capacity'])): ?>
        <div class="capacity-info">
            <h3>Kapacitāte</h3>
            <div class="capacity-bar">
                <?php
                $capacity = intval($meta['capacity']);
                $activeBookings = intval($stats['active_quantity']);
                $percentage = $capacity > 0 ? min(($activeBookings / $capacity) * 100, 100) : 0;
                $remaining = max($capacity - $activeBookings, 0);
                ?>
                <div class="capacity-progress">
                    <div class="capacity-fill" style="width: <?= $percentage ?>%"></div>
                </div>
                <div class="capacity-stats">
                    <span><?= $activeBookings ?> / <?= $capacity ?> aizņemtas vietas</span>
                    <span class="remaining"><?= $remaining ?> brīvas</span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Pakalpojuma informācija -->
    <?php if ($product['type'] === 'service' || $product['type'] === 'unique_service'): ?>
        <div class="service-info">
            <h3>Pakalpojuma detaļas</h3>
            <div class="info-grid">
                <?php if (isset($meta['route_from'])): ?>
                    <div class="info-item">
                        <span class="label">Maršruts no:</span>
                        <span><?= e($meta['route_from']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($meta['route_to'])): ?>
                    <div class="info-item">
                        <span class="label">Maršruts līdz:</span>
                        <span><?= e($meta['route_to']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($meta['service_date'])): ?>
                    <div class="info-item">
                        <span class="label">Datums:</span>
                        <span><?= date('d.m.Y', strtotime($meta['service_date'])) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($meta['service_time'])): ?>
                    <div class="info-item">
                        <span class="label">Laiks:</span>
                        <span><?= date('H:i', strtotime($meta['service_time'])) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Pieteikumu saraksts -->
    <div class="bookings-list">
        <h2>Pieteikumi</h2>

        <?php if (empty($bookings)): ?>
            <div class="empty-state">
                <p>Pagaidām nav nevienas pieteikšanās šim produktam.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pircējs</th>
                            <th>Kontakti</th>
                            <th>Daudzums</th>
                            <th>Datums</th>
                            <th>Statuss</th>
                            <th>Darbības</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr class="booking-row status-<?= e($booking['status']) ?>">
                                <td>#<?= $booking['id'] ?></td>
                                <td>
                                    <strong><?= e($booking['buyer_name']) ?></strong>
                                </td>
                                <td>
                                    <div class="contact-info">
                                        <div><?= e($booking['buyer_email']) ?></div>
                                        <?php if ($booking['buyer_phone']): ?>
                                            <div><?= e($booking['buyer_phone']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td><?= $booking['quantity'] ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($booking['created_at'])) ?></td>
                                <td>
                                    <span class="status-badge status-<?= e($booking['status']) ?>">
                                        <?= lang('booking.status_' . $booking['status']) ?? ucfirst($booking['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($booking['status'] === 'pending'): ?>
                                            <form action="/bookings/<?= $booking['id'] ?>/confirm" method="POST" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-success btn-xs" title="Apstiprināt">✓</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($booking['status'] === 'confirmed'): ?>
                                            <form action="/bookings/<?= $booking['id'] ?>/complete" method="POST" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-primary btn-xs" title="Pabeigt">✓✓</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                            <form action="/bookings/<?= $booking['id'] ?>/cancel" method="POST" style="display: inline;" onsubmit="return confirm('Vai tiešām atcelt?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-danger btn-xs" title="Atcelt">✕</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php if ($booking['notes']): ?>
                                <tr class="notes-row">
                                    <td></td>
                                    <td colspan="6">
                                        <strong>Piezīmes:</strong> <?= nl2br(e($booking['notes'])) ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.product-bookings-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.page-header {
    margin-bottom: 2rem;
}

.back-link {
    display: inline-block;
    color: #2196F3;
    text-decoration: none;
    margin-bottom: 0.5rem;
}

.back-link:hover {
    text-decoration: underline;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2196F3;
}

.stat-label {
    color: #666;
    margin-top: 0.5rem;
}

.capacity-info,
.service-info {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.capacity-info h3,
.service-info h3 {
    margin-top: 0;
    margin-bottom: 1rem;
}

.capacity-bar {
    margin-top: 1rem;
}

.capacity-progress {
    height: 30px;
    background: #e0e0e0;
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.capacity-fill {
    height: 100%;
    background: linear-gradient(90deg, #4caf50, #2196F3);
    transition: width 0.3s ease;
}

.capacity-stats {
    display: flex;
    justify-content: space-between;
    font-size: 0.875rem;
    color: #666;
}

.capacity-stats .remaining {
    font-weight: bold;
    color: #4caf50;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-item .label {
    font-weight: 600;
    color: #666;
    font-size: 0.875rem;
}

.bookings-list {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.bookings-list h2 {
    margin-top: 0;
    margin-bottom: 1.5rem;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #666;
}

.table-responsive {
    overflow-x: auto;
}

.bookings-table {
    width: 100%;
    border-collapse: collapse;
}

.bookings-table th,
.bookings-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

.bookings-table th {
    background: #f5f5f5;
    font-weight: 600;
    color: #333;
}

.bookings-table tbody tr:hover {
    background: #f9f9f9;
}

.contact-info {
    font-size: 0.875rem;
}

.contact-info div {
    margin: 0.25rem 0;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-confirmed {
    background: #cce5ff;
    color: #004085;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-xs {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.notes-row td {
    background: #f9f9f9;
    font-size: 0.875rem;
    color: #555;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .bookings-table {
        font-size: 0.875rem;
    }

    .bookings-table th,
    .bookings-table td {
        padding: 0.5rem;
    }
}
</style>

<?php
$content = ob_get_clean();
$title = 'Pieteikšanās: ' . $product['title'] . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
