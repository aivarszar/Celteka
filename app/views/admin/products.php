<?php ob_start(); ?>

<div class="container admin-container">
    <div class="admin-header">
        <h1>📦 <?= lang('admin.products') ?></h1>
        <p><?= lang('admin.manage') ?> <?= lang('admin.products') ?></p>
    </div>

    <?php if (!empty($products)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= lang('product.title') ?></th>
                        <th><?= lang('product.seller') ?></th>
                        <th><?= lang('product.category') ?></th>
                        <th><?= lang('product.type') ?></th>
                        <th><?= lang('product.price') ?></th>
                        <th><?= lang('product.stock') ?></th>
                        <th><?= lang('common.status') ?></th>
                        <th><?= lang('common.created_at') ?></th>
                        <th><?= lang('common.actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td>
                                <strong><?= e($product['title']) ?></strong>
                                <?php if (!empty($product['images'])): ?>
                                    <?php $images = json_decode($product['images'], true); ?>
                                    <?php if (!empty($images[0])): ?>
                                        <br><img src="/<?= e($images[0]) ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; margin-top: 0.25rem;">
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td><?= e($product['seller_name']) ?></td>
                            <td>
                                <span class="category-badge">
                                    <?= e($product['category_name']) ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $typeLabels = [
                                    'product' => lang('product.type_product'),
                                    'service' => lang('product.type_service'),
                                    'unique_service' => lang('product.type_unique')
                                ];
                                ?>
                                <span class="type-badge type-<?= e($product['type']) ?>">
                                    <?= $typeLabels[$product['type']] ?? e($product['type']) ?>
                                </span>
                            </td>
                            <td><strong>€<?= number_format($product['price'], 2) ?></strong></td>
                            <td class="text-center">
                                <?php if ($product['type'] === 'product'): ?>
                                    <?= $product['stock'] ?>
                                <?php else: ?>
                                    <span style="color: #999;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($product['is_active']): ?>
                                    <span class="status-badge status-active"><?= lang('common.active') ?></span>
                                <?php else: ?>
                                    <span class="status-badge status-inactive"><?= lang('common.inactive') ?></span>
                                <?php endif; ?>
                            </td>
                            <td><small><?= date('d.m.Y', strtotime($product['created_at'])) ?></small></td>
                            <td>
                                <a href="/product/<?= e($product['slug']) ?>" class="btn btn-sm btn-primary" target="_blank">
                                    👁️ <?= lang('common.view') ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-data"><?= lang('product.no_products') ?></div>
    <?php endif; ?>
</div>

<style>
.table-responsive {
    overflow-x: auto;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

.category-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    background: #e3f2fd;
    color: #1976d2;
}

.type-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.type-product {
    background: #c8e6c9;
    color: #2e7d32;
}

.type-service {
    background: #fff9c4;
    color: #f57f17;
}

.type-unique_service {
    background: #f8bbd0;
    color: #c2185b;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.875rem;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
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
$title = lang('admin.products') . ' - ' . lang('admin.admin_panel') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
