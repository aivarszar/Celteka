<?php
require_once __DIR__ . '/../../helpers/AuthHelper.php';
ob_start();
$isEdit = $action === 'edit' && $product;
?>

<section class="container mt-4 mb-4">
    <h1><?= $isEdit ? lang('product.edit_product') : lang('product.add_product') ?></h1>

    <div class="card mt-3">
        <div class="card-body">
            <form action="<?= $isEdit ? '/seller/products/' . $product['id'] : '/seller/products' ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label class="form-label"><?= lang('product.title') ?> *</label>
                    <input type="text" name="title" class="form-control" value="<?= $isEdit ? e($product['title']) : '' ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= lang('product.description') ?> *</label>
                    <textarea name="description" class="form-control" required><?= $isEdit ? e($product['description']) : '' ?></textarea>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label"><?= lang('product.price') ?> (€) *</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="<?= $isEdit ? $product['price'] : '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= lang('product.type') ?> *</label>
                        <select name="type" class="form-control" required id="productType">
                            <option value="product" <?= $isEdit && $product['type'] === 'product' ? 'selected' : '' ?>>
                                <?= lang('product.type_product') ?>
                            </option>
                            <option value="service" <?= $isEdit && $product['type'] === 'service' ? 'selected' : '' ?>>
                                <?= lang('product.type_service') ?>
                            </option>
                            <option value="unique_service" <?= $isEdit && $product['type'] === 'unique_service' ? 'selected' : '' ?>>
                                <?= lang('product.type_unique') ?>
                            </option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group" id="stockField">
                        <label class="form-label"><?= lang('product.stock') ?></label>
                        <input type="number" name="stock_quantity" class="form-control" value="<?= $isEdit ? $product['stock_quantity'] : '0' ?>">
                        <small>Atstājiet 0, ja nav noliktavā</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= lang('product.category') ?> *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Izvēlieties kategoriju</option>
                            <option value="1" <?= $isEdit && $product['category_id'] == 1 ? 'selected' : '' ?>>Pārtika</option>
                            <option value="2" <?= $isEdit && $product['category_id'] == 2 ? 'selected' : '' ?>>Amatniecība</option>
                            <option value="3" <?= $isEdit && $product['category_id'] == 3 ? 'selected' : '' ?>>Pakalpojumi</option>
                        </select>
                        <small>Ja vajadzīgā kategorija nav pieejama, sazinieties ar administratoru</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= lang('product.location') ?></label>
                    <select name="location_id" class="form-control">
                        <option value="">Izvēlieties lokāciju</option>
                        <optgroup label="Rīgas reģions">
                            <option value="7" <?= $isEdit && $product['location_id'] == 7 ? 'selected' : '' ?>>Rīga</option>
                            <option value="8" <?= $isEdit && $product['location_id'] == 8 ? 'selected' : '' ?>>Jūrmala</option>
                        </optgroup>
                        <optgroup label="Vidzeme">
                            <option value="11" <?= $isEdit && $product['location_id'] == 11 ? 'selected' : '' ?>>Valmiera</option>
                            <option value="12" <?= $isEdit && $product['location_id'] == 12 ? 'selected' : '' ?>>Cēsis</option>
                        </optgroup>
                        <optgroup label="Kurzeme">
                            <option value="15" <?= $isEdit && $product['location_id'] == 15 ? 'selected' : '' ?>>Liepāja</option>
                            <option value="16" <?= $isEdit && $product['location_id'] == 16 ? 'selected' : '' ?>>Ventspils</option>
                        </optgroup>
                        <optgroup label="Zemgale">
                            <option value="19" <?= $isEdit && $product['location_id'] == 19 ? 'selected' : '' ?>>Jelgava</option>
                            <option value="20" <?= $isEdit && $product['location_id'] == 20 ? 'selected' : '' ?>>Bauska</option>
                        </optgroup>
                        <optgroup label="Latgale">
                            <option value="23" <?= $isEdit && $product['location_id'] == 23 ? 'selected' : '' ?>>Daugavpils</option>
                            <option value="24" <?= $isEdit && $product['location_id'] == 24 ? 'selected' : '' ?>>Rēzekne</option>
                        </optgroup>
                    </select>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" <?= (!$isEdit || $product['is_active']) ? 'checked' : '' ?>>
                        Aktīvs (redzams pircējiem)
                    </label>
                </div>

                <?php if ($isEdit && !empty($images)): ?>
                    <div class="form-group">
                        <label class="form-label">Pašreizējie attēli</label>
                        <div class="d-flex gap-2">
                            <?php foreach ($images as $image): ?>
                                <div style="position: relative;">
                                    <img src="<?= e($image['image_path']) ?>" alt="Product" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
                                    <?php if ($image['is_primary']): ?>
                                        <span style="position: absolute; top: 5px; right: 5px; background: var(--primary); color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;">Galvenais</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?= $isEdit ? lang('common.save') : 'Pievienot produktu' ?>
                    </button>
                    <a href="/seller/products" class="btn btn-secondary"><?= lang('common.cancel') ?></a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
// Toggle stock field based on product type
document.getElementById('productType').addEventListener('change', function() {
    const stockField = document.getElementById('stockField');
    if (this.value === 'product') {
        stockField.style.display = 'block';
    } else {
        stockField.style.display = 'none';
    }
});

// Initial check
if (document.getElementById('productType').value !== 'product') {
    document.getElementById('stockField').style.display = 'none';
}
</script>

<?php
$content = ob_get_clean();
$title = ($isEdit ? lang('product.edit_product') : lang('product.add_product')) . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
