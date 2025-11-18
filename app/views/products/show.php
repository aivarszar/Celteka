<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
ob_start();
?>

<section class="container mt-4 mb-4">
    <div class="grid grid-2">
        <!-- Product Images -->
        <div>
            <div class="card">
                <?php if (!empty($images)): ?>
                    <img src="<?= e($images[0]['image_path']) ?>" alt="<?= e($product['title']) ?>" class="card-img" style="height: 400px;">
                    <?php if (count($images) > 1): ?>
                        <div class="p-2 d-flex gap-1">
                            <?php foreach ($images as $image): ?>
                                <img src="<?= e($image['image_path']) ?>" alt="<?= e($product['title']) ?>"
                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; cursor: pointer;">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="card-img" style="height: 400px; background: var(--light-gray); display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 6rem; color: var(--gray);">📦</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Details -->
        <div>
            <div class="card">
                <div class="card-body">
                    <h1><?= e($product['title']) ?></h1>

                    <div class="product-price mt-2 mb-2">
                        €<?= number_format($product['price'], 2) ?>
                    </div>

                    <div class="mb-3">
                        <?php
                        $typeLabels = [
                            'product' => '📦 ' . lang('product.type_product'),
                            'service' => '🔧 ' . lang('product.type_service'),
                            'unique_service' => '⭐ ' . lang('product.type_unique'),
                        ];
                        ?>
                        <span style="background: var(--light-gray); padding: 0.5rem 1rem; border-radius: 20px;">
                            <?= $typeLabels[$product['type']] ?? $product['type'] ?>
                        </span>

                        <?php if ($product['type'] === 'product'): ?>
                            <span style="background: <?= $product['stock_quantity'] > 0 ? 'var(--success)' : 'var(--danger)' ?>; color: white; padding: 0.5rem 1rem; border-radius: 20px; margin-left: 0.5rem;">
                                <?= $product['stock_quantity'] > 0 ? lang('product.in_stock') : lang('product.out_of_stock') ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <h3><?= lang('product.description') ?></h3>
                        <p><?= nl2br(e($product['description'])) ?></p>
                    </div>

                    <?php if ($product['location_name']): ?>
                        <div class="mb-3">
                            <strong>📍 <?= lang('product.location') ?>:</strong>
                            <?= e($product['location_name']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <strong>👁️ <?= lang('product.views') ?>:</strong>
                        <?= $product['views_count'] ?>
                    </div>

                    <!-- Pakalpojuma informācija (maršruts un laiks) -->
                    <?php if ($product['type'] === 'service' || $product['type'] === 'unique_service'): ?>
                        <?php if (!empty($meta['route_from']) || !empty($meta['route_to']) || !empty($meta['service_date'])): ?>
                            <div class="service-details mb-3" style="background: #f9f9f9; padding: 1rem; border-radius: 8px;">
                                <h3 style="margin-top: 0;"><?= lang('product.route_and_schedule') ?? 'Maršruts un laiks' ?></h3>
                                <div class="service-info-grid">
                                    <?php if (!empty($meta['route_from'])): ?>
                                        <div class="service-info-item">
                                            <strong>📍 <?= lang('product.route_from') ?? 'No' ?>:</strong>
                                            <?= e($meta['route_from']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($meta['route_to'])): ?>
                                        <div class="service-info-item">
                                            <strong>📍 <?= lang('product.route_to') ?? 'Līdz' ?>:</strong>
                                            <?= e($meta['route_to']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($meta['service_date'])): ?>
                                        <div class="service-info-item">
                                            <strong>📅 <?= lang('product.service_date') ?? 'Datums' ?>:</strong>
                                            <?= date('d.m.Y', strtotime($meta['service_date'])) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($meta['service_time'])): ?>
                                        <div class="service-info-item">
                                            <strong>🕐 <?= lang('product.service_time') ?? 'Laiks' ?>:</strong>
                                            <?= date('H:i', strtotime($meta['service_time'])) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($meta['capacity'])): ?>
                                        <div class="service-info-item">
                                            <strong>👥 <?= lang('product.capacity') ?? 'Kapacitāte' ?>:</strong>
                                            <?php
                                            $capacity = intval($meta['capacity']);
                                            $booked = $bookingStats ? intval($bookingStats['active_quantity']) : 0;
                                            $available = max(0, $capacity - $booked);
                                            ?>
                                            <span class="capacity-display" style="font-weight: bold; color: <?= $available > 0 ? '#4caf50' : '#f44336' ?>;">
                                                <?= $available ?> / <?= $capacity ?> <?= lang('product.available') ?? 'pieejamas' ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($meta['route_notes'])): ?>
                                        <div class="service-info-item" style="margin-top: 0.5rem;">
                                            <strong>ℹ️ <?= lang('product.route_notes') ?? 'Papildu informācija' ?>:</strong>
                                            <p style="margin: 0.5rem 0 0 0; color: #555;"><?= nl2br(e($meta['route_notes'])) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <hr>

                    <!-- Seller Info -->
                    <div class="mb-3">
                        <h3><?= lang('product.seller') ?></h3>
                        <div class="d-flex justify-between align-center">
                            <div>
                                <strong><?= e($product['seller_name']) ?></strong>
                                <div>
                                    <span class="rating">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <?= $i < round($sellerRating) ? '⭐' : '☆' ?>
                                        <?php endfor; ?>
                                    </span>
                                    <small>(<?= $sellerReviewCount ?> <?= lang('common.reviews') ?>)</small>
                                </div>
                            </div>
                            <a href="/user/<?= $product['seller_id'] ?>" class="btn btn-outline btn-sm">
                                <?= lang('product.view_profile') ?>
                            </a>
                        </div>
                    </div>

                    <hr>

                    <!-- Actions -->
                    <?php if (Session::isLoggedIn() && Session::getUserId() != $product['seller_id']): ?>
                        <?php if ($product['type'] === 'product'): ?>
                            <!-- Parasts produkts - pievienot grozam -->
                            <form action="/cart/add/<?= $product['id'] ?>" method="post">
                                <?= csrf_field() ?>
                                <div class="form-group">
                                    <label><?= lang('order.quantity') ?>:</label>
                                    <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>" class="form-control">
                                </div>

                                <button type="submit" class="btn btn-primary" style="width: 100%;" <?= $product['stock_quantity'] <= 0 ? 'disabled' : '' ?>>
                                    🛒 <?= lang('order.add_to_cart') ?>
                                </button>
                            </form>
                        <?php else: ?>
                            <!-- Pakalpojums - pieteikšanās -->
                            <?php
                            $canBook = true;
                            $bookingMessage = '';

                            // Pārbaudīt vai jau ir pieteicies
                            if ($hasBooked) {
                                $canBook = false;
                                $bookingMessage = lang('booking.already_booked') ?? 'Jūs jau esat pieteicies šim pakalpojumam';
                            }

                            // Pārbaudīt kapacitāti
                            if ($canBook && !empty($meta['capacity'])) {
                                $capacity = intval($meta['capacity']);
                                $booked = $bookingStats ? intval($bookingStats['active_quantity']) : 0;
                                if ($booked >= $capacity) {
                                    $canBook = false;
                                    $bookingMessage = lang('booking.no_capacity') ?? 'Visas vietas ir aizņemtas';
                                }
                            }
                            ?>

                            <?php if ($canBook): ?>
                                <form action="/bookings/book/<?= $product['id'] ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="form-group">
                                        <label><?= lang('booking.quantity') ?? 'Vietu skaits' ?>:</label>
                                        <input type="number" name="quantity" value="1" min="1" max="10" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label><?= lang('booking.notes') ?? 'Piezīmes (neobligāti)' ?>:</label>
                                        <textarea name="notes" class="form-control" rows="3" placeholder="<?= lang('booking.notes_placeholder') ?? 'Papildu informācija vai jautājumi' ?>"></textarea>
                                    </div>

                                    <div style="padding: 0.75rem; background: #e3f2fd; border-radius: 4px; margin-bottom: 1rem; font-size: 0.875rem;">
                                        ℹ️ Pēc pieteikšanās pārdevējs pārskatīs jūsu pieprasījumu un apstiprinās pieteikšanos.
                                    </div>

                                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                                        ✓ <?= lang('booking.book_now') ?? 'Pieteikties' ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-warning" style="padding: 1rem; background: #fff3cd; border-radius: 8px; margin-bottom: 1rem;">
                                    <?= e($bookingMessage) ?>
                                </div>
                                <?php if ($hasBooked): ?>
                                    <a href="/bookings" class="btn btn-secondary" style="width: 100%;">
                                        Skatīt manas pieteikšanās
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php elseif (Session::isLoggedIn() && Session::getUserId() == $product['seller_id']): ?>
                        <a href="/seller/products/<?= $product['id'] ?>/edit" class="btn btn-primary" style="width: 100%; margin-bottom: 0.5rem;">
                            ✏️ <?= lang('product.edit_product') ?>
                        </a>
                        <?php if ($product['type'] !== 'product'): ?>
                            <a href="/bookings/product/<?= $product['id'] ?>" class="btn btn-secondary" style="width: 100%;">
                                👥 Skatīt pieteikšanās (<?= $bookingStats ? $bookingStats['total_bookings'] : 0 ?>)
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="/login" class="btn btn-primary" style="width: 100%;">
                            <?= lang('product.login_to_order') ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
$title = e($product['title']) . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
