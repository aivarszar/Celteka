<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
ob_start();
?>

<div class="container bookings-container">
    <h1>Manas pieteikšanās</h1>

    <!-- Pircēja pieteikšanās -->
    <section class="bookings-section">
        <h2>Manas pieteikšanās produktiem</h2>

        <?php if (empty($buyerBookings)): ?>
            <div class="empty-state">
                <p>Jūs vēl neesat pieteicies nevienam produktam.</p>
                <a href="/products" class="btn btn-primary">Skatīt produktus</a>
            </div>
        <?php else: ?>
            <div class="bookings-grid">
                <?php foreach ($buyerBookings as $booking): ?>
                    <div class="booking-card">
                        <div class="booking-header">
                            <h3><?= e($booking['product_title']) ?></h3>
                            <span class="status-badge status-<?= e($booking['status']) ?>">
                                <?= lang('booking.status_' . $booking['status']) ?? ucfirst($booking['status']) ?>
                            </span>
                        </div>

                        <div class="booking-info">
                            <div class="info-row">
                                <span class="label">Pārdevējs:</span>
                                <span><?= e($booking['seller_name']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="label">Daudzums:</span>
                                <span><?= e($booking['quantity']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="label">Cena:</span>
                                <span class="price">€<?= number_format($booking['product_price'], 2) ?></span>
                            </div>
                            <?php if ($booking['booking_date']): ?>
                                <div class="info-row">
                                    <span class="label">Datums:</span>
                                    <span><?= date('d.m.Y', strtotime($booking['booking_date'])) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($booking['booking_time']): ?>
                                <div class="info-row">
                                    <span class="label">Laiks:</span>
                                    <span><?= date('H:i', strtotime($booking['booking_time'])) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="info-row">
                                <span class="label">Izveidots:</span>
                                <span><?= date('d.m.Y H:i', strtotime($booking['created_at'])) ?></span>
                            </div>
                        </div>

                        <?php if ($booking['notes']): ?>
                            <div class="booking-notes">
                                <strong>Piezīmes:</strong>
                                <p><?= nl2br(e($booking['notes'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="booking-actions">
                            <a href="/products/<?= $booking['product_id'] ?>" class="btn btn-secondary btn-sm">
                                Skatīt produktu
                            </a>
                            <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                <form action="/bookings/<?= $booking['id'] ?>/cancel" method="POST" style="display: inline;" onsubmit="return confirm('Vai tiešām vēlaties atcelt pieteikšanos?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Atcelt</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Pārdevēja saņemtās pieteikšanās -->
    <?php if (!empty($sellerBookings)): ?>
        <section class="bookings-section">
            <h2>Saņemtās pieteikšanās (kā pārdevējs)</h2>

            <div class="bookings-grid">
                <?php foreach ($sellerBookings as $booking): ?>
                    <div class="booking-card">
                        <div class="booking-header">
                            <h3><?= e($booking['product_title']) ?></h3>
                            <span class="status-badge status-<?= e($booking['status']) ?>">
                                <?= lang('booking.status_' . $booking['status']) ?? ucfirst($booking['status']) ?>
                            </span>
                        </div>

                        <div class="booking-info">
                            <div class="info-row">
                                <span class="label">Pircējs:</span>
                                <span><?= e($booking['buyer_name']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="label">E-pasts:</span>
                                <span><?= e($booking['buyer_email']) ?></span>
                            </div>
                            <?php if ($booking['buyer_phone']): ?>
                                <div class="info-row">
                                    <span class="label">Tālrunis:</span>
                                    <span><?= e($booking['buyer_phone']) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="info-row">
                                <span class="label">Daudzums:</span>
                                <span><?= e($booking['quantity']) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="label">Izveidots:</span>
                                <span><?= date('d.m.Y H:i', strtotime($booking['created_at'])) ?></span>
                            </div>
                        </div>

                        <?php if ($booking['notes']): ?>
                            <div class="booking-notes">
                                <strong>Pircēja piezīmes:</strong>
                                <p><?= nl2br(e($booking['notes'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="booking-actions">
                            <a href="/bookings/product/<?= $booking['product_id'] ?>" class="btn btn-secondary btn-sm">
                                Visas pieteikšanās
                            </a>
                            <?php if ($booking['status'] === 'pending'): ?>
                                <form action="/bookings/<?= $booking['id'] ?>/confirm" method="POST" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-success btn-sm">Apstiprināt</button>
                                </form>
                            <?php endif; ?>
                            <?php if ($booking['status'] === 'confirmed'): ?>
                                <form action="/bookings/<?= $booking['id'] ?>/complete" method="POST" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-primary btn-sm">Pabeigt</button>
                                </form>
                            <?php endif; ?>
                            <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                <form action="/bookings/<?= $booking['id'] ?>/cancel" method="POST" style="display: inline;" onsubmit="return confirm('Vai tiešām vēlaties atcelt pieteikšanos?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Atcelt</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<style>
.bookings-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.bookings-section {
    margin-bottom: 3rem;
}

.bookings-section h2 {
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e0e0e0;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    background: #f5f5f5;
    border-radius: 8px;
}

.empty-state p {
    margin-bottom: 1.5rem;
    color: #666;
}

.bookings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
}

.booking-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: box-shadow 0.2s;
}

.booking-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.booking-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e0e0e0;
}

.booking-header h3 {
    margin: 0;
    font-size: 1.125rem;
    color: #2196F3;
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

.booking-info {
    margin-bottom: 1rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-row .label {
    font-weight: 600;
    color: #666;
}

.info-row .price {
    font-weight: bold;
    color: #2196F3;
}

.booking-notes {
    background: #f9f9f9;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.booking-notes strong {
    display: block;
    margin-bottom: 0.5rem;
}

.booking-notes p {
    margin: 0;
    color: #555;
    line-height: 1.5;
}

.booking-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .bookings-grid {
        grid-template-columns: 1fr;
    }

    .booking-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .booking-actions {
        flex-direction: column;
    }

    .booking-actions form,
    .booking-actions .btn {
        width: 100%;
    }
}
</style>

<?php
$content = ob_get_clean();
$title = 'Manas pieteikšanās - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
