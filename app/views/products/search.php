<?php ob_start(); ?>

<div class="container mt-4 mb-4">
    <!-- Search Header -->
    <div class="search-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">Meklēšanas rezultāti</h1>
        <p class="search-info" style="font-size: 1.1rem; color: var(--gray);">
            <?php if ($totalCount > 0): ?>
                Meklēšanas vaicājumam <strong>"<?= e($query) ?>"</strong> atrasti <strong><?= $totalCount ?></strong>
                <?php if ($totalCount == 1): ?>
                    rezultāts
                <?php elseif ($totalCount < 10 || $totalCount % 10 == 0 || ($totalCount % 10 >= 2 && $totalCount % 10 <= 9 && ($totalCount < 10 || $totalCount > 20))): ?>
                    rezultāti
                <?php else: ?>
                    rezultāts
                <?php endif; ?>
            <?php else: ?>
                Meklēšanas vaicājumam <strong>"<?= e($query) ?>"</strong> rezultāti nav atrasti
            <?php endif; ?>
        </p>

        <!-- Refine Search Form -->
        <form action="/search" method="get" class="d-flex gap-2" style="max-width: 600px; margin-top: 1rem;">
            <input type="text" name="q" class="form-control" placeholder="Meklēt produktus un pakalpojumus..." value="<?= e($query) ?>" required>
            <button type="submit" class="btn btn-primary">Meklēt</button>
        </form>
    </div>

    <?php if ($totalCount > 0): ?>
        <!-- Products Grid -->
        <div class="grid grid-4 mt-3">
            <?php foreach ($products as $product): ?>
                <div class="card product-card">
                    <?php if ($product['primary_image']): ?>
                        <img src="<?= e($product['primary_image']) ?>" alt="<?= e($product['title']) ?>" class="card-img">
                    <?php else: ?>
                        <div class="card-img" style="background: var(--light-gray); display: flex; align-items: center; justify-content: center; height: 200px;">
                            <span style="font-size: 3rem; color: var(--gray);">
                                <?= $product['type'] === 'service' ? '🔧' : '📦' ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="/product/<?= e($product['slug']) ?>"><?= e($product['title']) ?></a>
                        </h3>

                        <p class="card-text"><?= e(mb_substr($product['description'], 0, 100)) ?><?= mb_strlen($product['description']) > 100 ? '...' : '' ?></p>

                        <div class="product-price" style="font-size: 1.5rem; font-weight: bold; color: var(--primary); margin: 1rem 0;">
                            €<?= number_format($product['price'], 2) ?>
                        </div>

                        <div class="product-meta" style="font-size: 0.9rem; color: var(--gray); margin-bottom: 1rem;">
                            <span class="product-seller" style="display: block; margin-bottom: 0.5rem;">
                                👤 <?= e($product['seller_name']) ?>
                            </span>
                            <?php if ($product['location_name']): ?>
                                <span class="product-location" style="display: block;">
                                    📍 <?= e($product['location_name']) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="mt-2">
                            <a href="/product/<?= e($product['slug']) ?>" class="btn btn-primary" style="width: 100%;">
                                Skatīt produktu
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination mt-4" style="display: flex; justify-content: center; gap: 0.5rem;">
                <?php if ($currentPage > 1): ?>
                    <a href="/search?q=<?= urlencode($query) ?>&page=<?= $currentPage - 1 ?>" class="btn btn-outline">‹ Iepriekšējā</a>
                <?php endif; ?>

                <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                    <?php if ($i == $currentPage): ?>
                        <span class="btn btn-primary"><?= $i ?></span>
                    <?php else: ?>
                        <a href="/search?q=<?= urlencode($query) ?>&page=<?= $i ?>" class="btn btn-outline"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="/search?q=<?= urlencode($query) ?>&page=<?= $currentPage + 1 ?>" class="btn btn-outline">Nākamā ›</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- No Results Found -->
        <div class="card mt-3">
            <div class="card-body text-center" style="padding: 3rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🔍</div>
                <h2>Nav atrasts neviens produkts</h2>
                <p style="font-size: 1.1rem; color: var(--gray); margin: 1.5rem 0;">
                    Diemžēl jūsu meklēšanas vaicājumam "<strong><?= e($query) ?></strong>" netika atrasts neviens atbilstošs produkts vai pakalpojums.
                </p>

                <div style="max-width: 600px; margin: 2rem auto;">
                    <h3 style="margin-bottom: 1rem;">Ieteikumi labākai meklēšanai:</h3>
                    <ul style="text-align: left; padding-left: 2rem; line-height: 1.8;">
                        <li>Pārbaudiet, vai visi vārdi ir uzrakstīti pareizi</li>
                        <li>Izmēģiniet citus atslēgvārdus vai sinonīmus</li>
                        <li>Izmantojiet vispārīgākus meklēšanas terminus</li>
                        <li>Samaziniet meklēšanas vārdu skaitu</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <a href="/products" class="btn btn-primary btn-lg">Skatīt visus produktus</a>
                    <a href="/" class="btn btn-outline btn-lg">Atpakaļ uz sākumlapu</a>
                </div>
            </div>
        </div>

        <!-- Alternative Suggestions -->
        <div class="card mt-3">
            <div class="card-body">
                <h3>Populārākās kategorijas</h3>
                <div class="grid grid-3 mt-3">
                    <a href="/products?category=food" class="card text-center" style="text-decoration: none;">
                        <div class="card-body">
                            <div style="font-size: 2.5rem;">🥬</div>
                            <p style="margin-top: 0.5rem; font-weight: 600;">Pārtika</p>
                        </div>
                    </a>
                    <a href="/products?category=crafts" class="card text-center" style="text-decoration: none;">
                        <div class="card-body">
                            <div style="font-size: 2.5rem;">🎨</div>
                            <p style="margin-top: 0.5rem; font-weight: 600;">Amatniecība</p>
                        </div>
                    </a>
                    <a href="/products?category=services" class="card text-center" style="text-decoration: none;">
                        <div class="card-body">
                            <div style="font-size: 2.5rem;">🔧</div>
                            <p style="margin-top: 0.5rem; font-weight: 600;">Pakalpojumi</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$title = 'Meklēšana: ' . e($query) . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
