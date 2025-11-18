<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
ob_start();
?>

<section class="container mt-4 mb-4">
    <div class="d-flex justify-between align-center mb-3">
        <h1><?= lang('nav.my_products') ?></h1>
        <a href="/seller/products/create" class="btn btn-primary">+ <?= lang('product.add_product') ?></a>
    </div>

    <?php if (!empty($products)): ?>
        <div class="card">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: var(--light-gray);">
                    <tr>
                        <th style="padding: 1rem; text-align: left;"><?= lang('product.title') ?></th>
                        <th style="padding: 1rem; text-align: left;"><?= lang('product.price') ?></th>
                        <th style="padding: 1rem; text-align: left;"><?= lang('product.type') ?></th>
                        <th style="padding: 1rem; text-align: left;"><?= lang('product.stock') ?> / <?= lang('booking.bookings') ?? 'Pieteikšanās' ?></th>
                        <th style="padding: 1rem; text-align: left;"><?= lang('common.created_at') ?></th>
                        <th style="padding: 1rem; text-align: center;"><?= lang('common.actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr style="border-top: 1px solid var(--light-gray);">
                            <td style="padding: 1rem;">
                                <strong><?= e($product['title']) ?></strong>
                                <?php if (!$product['is_active']): ?>
                                    <span style="color: var(--danger); font-size: 0.875rem;">(<?= lang('product.inactive') ?>)</span>
                                <?php endif; ?>
                                <?php if (!empty($product['description'])): ?>
                                    <br><small style="color: #666; font-size: 0.875rem;">
                                        <?= e(mb_substr($product['description'], 0, 100)) ?><?= mb_strlen($product['description']) > 100 ? '...' : '' ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">€<?= number_format($product['price'], 2) ?></td>
                            <td style="padding: 1rem;">
                                <?php
                                $typeLabels = [
                                    'product' => lang('product.type_product'),
                                    'service' => lang('product.type_service'),
                                    'unique_service' => lang('product.type_unique'),
                                ];
                                echo $typeLabels[$product['type']] ?? $product['type'];
                                ?>
                            </td>
                            <td style="padding: 1rem;">
                                <?php if ($product['type'] === 'product'): ?>
                                    <!-- Parādīt krājumu produktiem -->
                                    <?= $product['stock_quantity'] ?>
                                <?php else: ?>
                                    <!-- Parādīt pieteikšanās skaitu pakalpojumiem -->
                                    <?php
                                    $stats = $bookingStats[$product['id']] ?? null;
                                    if ($stats && $stats['total_bookings'] > 0):
                                    ?>
                                        <a href="/bookings/product/<?= $product['id'] ?>" style="text-decoration: none; color: #2196F3;">
                                            👥 <?= $stats['total_bookings'] ?>
                                            (<?= $stats['pending_count'] ?> jauni)
                                        </a>
                                    <?php else: ?>
                                        <span style="color: #999;">Nav pieteikšanās</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 1rem;">
                                <?= date('d.m.Y', strtotime($product['created_at'])) ?>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <a href="/product/<?= e($product['slug']) ?>" class="btn btn-sm" title="<?= lang('common.view') ?>">👁️</a>
                                <a href="/seller/products/<?= $product['id'] ?>/edit" class="btn btn-sm" title="<?= lang('common.edit') ?>">✏️</a>
                                <?php if ($product['type'] !== 'product'): ?>
                                    <a href="/bookings/product/<?= $product['id'] ?>" class="btn btn-sm" title="<?= lang('booking.view_bookings') ?? 'Skatīt pieteikšanās' ?>">📋</a>
                                <?php endif; ?>
                                <form action="/seller/products/<?= $product['id'] ?>/delete" method="post" style="display: inline;" onsubmit="return confirmDelete()">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm" style="background: none; border: none; cursor: pointer;" title="<?= lang('common.delete') ?>">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <?= lang('product.no_products_yet') ?>
            <a href="/seller/products/create"><?= lang('product.add_product') ?></a>
        </div>
    <?php endif; ?>
</section>

<?php
$content = ob_get_clean();
$title = lang('nav.my_products') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
