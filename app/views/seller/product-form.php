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
                        <small><?= lang('product.stock_help') ?></small>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= lang('product.category') ?> *</label>
                        <select name="category_id" class="form-control" required>
                            <option value=""><?= lang('product.select_category') ?></option>
                            <option value="1" <?= $isEdit && $product['category_id'] == 1 ? 'selected' : '' ?>><?= lang('product.category_food') ?></option>
                            <option value="2" <?= $isEdit && $product['category_id'] == 2 ? 'selected' : '' ?>><?= lang('product.category_crafts') ?></option>
                            <option value="3" <?= $isEdit && $product['category_id'] == 3 ? 'selected' : '' ?>><?= lang('product.category_services') ?></option>
                        </select>
                        <small><?= lang('product.contact_admin_for_category') ?></small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label"><?= lang('product.location') ?></label>
                    <select name="location_id" class="form-control">
                        <option value=""><?= lang('product.select_location') ?></option>
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

                <!-- Maršruta un laika lauki (parādās tikai pakalpojumiem) -->
                <div id="serviceFields" style="display: none;">
                    <hr style="margin: 2rem 0;">
                    <h3><?= lang('product.route_and_schedule') ?? 'Maršruts un laiks' ?></h3>

                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label"><?= lang('product.route_from') ?? 'No (sākuma punkts)' ?></label>
                            <input type="text" name="route_from" class="form-control"
                                   value="<?= $isEdit && isset($meta['route_from']) ? e($meta['route_from']) : '' ?>"
                                   placeholder="<?= lang('product.route_from_placeholder') ?? 'Piemēram: Rīga, Brīvības iela 1' ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?= lang('product.route_to') ?? 'Līdz (galapunkts)' ?></label>
                            <input type="text" name="route_to" class="form-control"
                                   value="<?= $isEdit && isset($meta['route_to']) ? e($meta['route_to']) : '' ?>"
                                   placeholder="<?= lang('product.route_to_placeholder') ?? 'Piemēram: Jūrmala' ?>">
                        </div>
                    </div>

                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label"><?= lang('product.service_date') ?? 'Datums' ?></label>
                            <input type="date" name="service_date" class="form-control"
                                   value="<?= $isEdit && isset($meta['service_date']) ? e($meta['service_date']) : '' ?>"
                                   min="<?= date('Y-m-d') ?>">
                            <small><?= lang('product.service_date_help') ?? 'Kad notiks pakalpojums' ?></small>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><?= lang('product.service_time') ?? 'Laiks' ?></label>
                            <input type="time" name="service_time" class="form-control"
                                   value="<?= $isEdit && isset($meta['service_time']) ? e($meta['service_time']) : '' ?>">
                            <small><?= lang('product.service_time_help') ?? 'Sākuma laiks' ?></small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= lang('product.capacity') ?? 'Vietu skaits' ?></label>
                        <input type="number" name="capacity" class="form-control" min="1"
                               value="<?= $isEdit && isset($meta['capacity']) ? e($meta['capacity']) : '1' ?>">
                        <small><?= lang('product.capacity_help') ?? 'Maksimālais pieteikumu/vietu skaits' ?></small>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= lang('product.route_notes') ?? 'Papildu informācija par maršrutu' ?></label>
                        <textarea name="route_notes" class="form-control" rows="3"
                                  placeholder="<?= lang('product.route_notes_placeholder') ?? 'Piemēram: Pieturvietas, nosacījumi, utt.' ?>"><?= $isEdit && isset($meta['route_notes']) ? e($meta['route_notes']) : '' ?></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" <?= (!$isEdit || $product['is_active']) ? 'checked' : '' ?>>
                        <?= lang('product.active_visible') ?>
                    </label>
                </div>

                <?php if ($isEdit && !empty($images)): ?>
                    <div class="form-group">
                        <label class="form-label"><?= lang('product.current_images') ?></label>
                        <div class="d-flex gap-2">
                            <?php foreach ($images as $image): ?>
                                <div style="position: relative;">
                                    <img src="<?= e($image['image_path']) ?>" alt="Product" style="width: 100px; height: 100px; object-fit: cover; border-radius: 4px;">
                                    <?php if ($image['is_primary']): ?>
                                        <span style="position: absolute; top: 5px; right: 5px; background: var(--primary); color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;"><?= lang('product.primary_image') ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <?= $isEdit ? lang('common.save') : lang('product.add_product') ?>
                    </button>
                    <a href="/seller/products" class="btn btn-secondary"><?= lang('common.cancel') ?></a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
// Toggle stock field and service fields based on product type
document.getElementById('productType').addEventListener('change', function() {
    const stockField = document.getElementById('stockField');
    const serviceFields = document.getElementById('serviceFields');

    if (this.value === 'product') {
        stockField.style.display = 'block';
        serviceFields.style.display = 'none';
    } else {
        stockField.style.display = 'none';
        // Parādīt maršruta laukus pakalpojumiem
        if (this.value === 'service' || this.value === 'unique_service') {
            serviceFields.style.display = 'block';
        } else {
            serviceFields.style.display = 'none';
        }
    }
});

// Initial check
const initialType = document.getElementById('productType').value;
if (initialType !== 'product') {
    document.getElementById('stockField').style.display = 'none';
}
if (initialType === 'service' || initialType === 'unique_service') {
    document.getElementById('serviceFields').style.display = 'block';
}
</script>

<?php
$content = ob_get_clean();
$title = ($isEdit ? lang('product.edit_product') : lang('product.add_product')) . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
