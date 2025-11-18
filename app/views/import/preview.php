<?php ob_start(); ?>

<div class="container preview-container">
    <div class="page-header">
        <h1>🔍 Importēšanas priekšskatījums</h1>
        <p>Pārbaudiet un rediģējiet importētos datus pirms saglabāšanas</p>
    </div>

    <form action="/import/confirm" method="post" class="preview-form">
        <?= csrf_field() ?>

        <div class="preview-content">
            <!-- Main content -->
            <div class="preview-main">
                <!-- Attēli -->
                <?php if (!empty($product['images'])): ?>
                    <div class="card mb-3">
                        <div class="card-header">
                            <h2>📸 Attēli</h2>
                        </div>
                        <div class="card-body">
                            <div class="images-grid">
                                <?php foreach ($product['images'] as $index => $imageUrl): ?>
                                    <div class="image-item">
                                        <img src="<?= e($imageUrl) ?>" alt="Produkta attēls <?= $index + 1 ?>">
                                        <?php if ($index === 0): ?>
                                            <span class="badge-primary">Galvenais</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <small class="form-text">Attēli tiks saglabāti kā URL saites uz avota vietni</small>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Produkta dati -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h2>📝 Produkta informācija</h2>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="title">Nosaukums <span class="required">*</span></label>
                            <input type="text"
                                   id="title"
                                   name="title"
                                   class="form-control"
                                   value="<?= e($product['title']) ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="description">Apraksts <span class="required">*</span></label>
                            <textarea id="description"
                                      name="description"
                                      class="form-control"
                                      rows="6"
                                      required><?= e($product['description']) ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="price">Cena (EUR) <span class="required">*</span></label>
                                <input type="number"
                                       id="price"
                                       name="price"
                                       class="form-control"
                                       value="<?= $product['price'] ?>"
                                       step="0.01"
                                       min="0"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="category_id">Kategorija <span class="required">*</span></label>
                                <select id="category_id" name="category_id" class="form-control" required>
                                    <?php
                                    $categories = db()->fetchAll("SELECT * FROM categories WHERE is_active = 1 ORDER BY name", []);
                                    foreach ($categories as $category):
                                    ?>
                                        <option value="<?= $category['id'] ?>">
                                            <?= e($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_active" checked>
                                <span>Publicēt uzreiz (produkts būs redzams)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Avota informācija -->
                <?php if (!empty($product['source_url'])): ?>
                    <div class="card mb-3">
                        <div class="card-header">
                            <h2>🔗 Avots</h2>
                        </div>
                        <div class="card-body">
                            <div class="source-info">
                                <strong>Importēts no:</strong><br>
                                <a href="<?= e($product['source_url']) ?>" target="_blank" rel="noopener">
                                    <?= e($product['source_url']) ?>
                                </a>
                                <p class="mt-2">
                                    <small>Avota URL tiks saglabāts produkta meta datos</small>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Darbības -->
                <div class="form-actions">
                    <a href="/import/cancel" class="btn btn-outline">Atcelt</a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        ✅ Apstiprināt un saglabāt
                    </button>
                </div>
            </div>

            <!-- Sidebar ar kopsavilkumu -->
            <div class="preview-sidebar">
                <div class="card">
                    <div class="card-header">
                        <h3>📊 Kopsavilkums</h3>
                    </div>
                    <div class="card-body">
                        <div class="summary-item">
                            <label>Nosaukums:</label>
                            <span><?= e($product['title']) ?></span>
                        </div>

                        <div class="summary-item">
                            <label>Cena:</label>
                            <span class="price">€<?= number_format($product['price'], 2) ?></span>
                        </div>

                        <div class="summary-item">
                            <label>Attēli:</label>
                            <span><?= count($product['images'] ?? []) ?> gab.</span>
                        </div>

                        <div class="summary-item">
                            <label>Apraksta garums:</label>
                            <span><?= mb_strlen($product['description']) ?> simboli</span>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h3>ℹ️ Piezīmes</h3>
                    </div>
                    <div class="card-body">
                        <ul class="notes-list">
                            <li>Varat rediģēt visus laukus pirms saglabāšanas</li>
                            <li>Izvēlieties atbilstošu kategoriju</li>
                            <li>Pārbaudiet cenu un aprakstu</li>
                            <li>Attēli paliks kā saites uz avotu</li>
                        </ul>
                    </div>
                </div>

                <div class="alert alert-warning mt-3">
                    <strong>⚠️ Svarīgi:</strong><br>
                    Importējiet tikai savus sludinājumus vai ar īpašnieka atļauju!
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.preview-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.page-header {
    margin-bottom: 2rem;
    text-align: center;
}

.page-header h1 {
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #666;
}

.preview-content {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
}

.preview-main {
    min-width: 0;
}

.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}

.image-item {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
    border-radius: 8px;
    border: 2px solid #e0e0e0;
}

.image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.badge-primary {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: #2196F3;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.required {
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #2196F3;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
}

textarea.form-control {
    resize: vertical;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.checkbox-label input {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.source-info {
    padding: 1rem;
    background: #f5f5f5;
    border-radius: 8px;
}

.source-info a {
    color: #2196F3;
    word-break: break-all;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: space-between;
    margin-top: 2rem;
}

.preview-sidebar {
    position: sticky;
    top: 2rem;
    height: fit-content;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e0e0e0;
}

.summary-item:last-child {
    border-bottom: none;
}

.summary-item label {
    font-weight: 600;
    color: #666;
}

.summary-item .price {
    font-size: 1.25rem;
    font-weight: bold;
    color: #2196F3;
}

.notes-list {
    padding-left: 1.5rem;
    line-height: 1.8;
}

.notes-list li {
    margin: 0.5rem 0;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
}

.alert-warning {
    background: #fff3cd;
    border: 1px solid #ffc107;
    color: #856404;
}

@media (max-width: 1200px) {
    .preview-content {
        grid-template-columns: 1fr;
    }

    .preview-sidebar {
        position: static;
    }

    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
$content = ob_get_clean();
$title = 'Importēšanas priekšskatījums - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
