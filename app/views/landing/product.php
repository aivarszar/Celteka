<?php ob_start(); ?>

<div class="landing-page">
    <div class="landing-hero">
        <div class="container">
            <div class="hero-content">
                <div class="product-images">
                    <?php if (!empty($images)): ?>
                        <div class="main-image">
                            <img src="<?= e($images[0]['image_path']) ?>" alt="<?= e($product['title']) ?>">
                        </div>
                        <?php if (count($images) > 1): ?>
                            <div class="thumbnail-images">
                                <?php foreach (array_slice($images, 1, 3) as $image): ?>
                                    <img src="<?= e($image['image_path']) ?>" alt="<?= e($product['title']) ?>">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="image-placeholder">
                            <span>📦</span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="product-info">
                    <h1><?= e($product['title']) ?></h1>

                    <?php if ($product['type'] === 'unique_service'): ?>
                        <div class="alert alert-warning">
                            <strong>⚠️ Unikāls pakalpojums</strong><br>
                            Šis pakalpojums ir pieejams tikai vienreiz un pēc tam kļūst neaktīvs.
                        </div>
                    <?php endif; ?>

                    <div class="price">
                        <span class="price-label">Cena:</span>
                        <span class="price-value">€<?= number_format($product['price'], 2) ?></span>
                    </div>

                    <div class="product-description">
                        <?= nl2br(e($product['description'])) ?>
                    </div>

                    <?php if (!empty($meta)): ?>
                        <div class="product-meta">
                            <h3>Papildu informācija</h3>
                            <ul>
                                <?php foreach ($meta as $key => $value): ?>
                                    <?php if ($key !== 'landing_slug'): ?>
                                        <li><strong><?= e(ucfirst(str_replace('_', ' ', $key))) ?>:</strong> <?= e($value) ?></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div class="seller-info">
                        <p><strong>Pārdevējs:</strong> <?= e($product['seller_name']) ?></p>
                        <?php if ($product['location_name']): ?>
                            <p><strong>Atrašanās vieta:</strong> <?= e($product['location_name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="cta-buttons">
                        <form action="/cart/add/<?= $product['id'] ?>" method="post" class="add-to-cart-form">
                            <?= csrf_field() ?>
                            <?php if ($product['type'] === 'product'): ?>
                                <label for="quantity">Daudzums:</label>
                                <input type="number"
                                       id="quantity"
                                       name="quantity"
                                       value="1"
                                       min="1"
                                       max="<?= $product['stock_quantity'] ?>"
                                       class="quantity-input">
                            <?php else: ?>
                                <input type="hidden" name="quantity" value="1">
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary btn-lg">
                                🛒 Pievienot grozam
                            </button>
                        </form>

                        <a href="/products" class="btn btn-outline">
                            📋 Skatīt visus produktus
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="landing-footer">
        <div class="container">
            <p>
                Interesē vairāk piedāvājumu?
                <a href="/products">Skatīt visu katalog

u</a>
            </p>
        </div>
    </div>
</div>

<style>
.landing-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.landing-hero {
    flex: 1;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 3rem 0;
}

.hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.product-images {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.main-image img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 12px;
}

.thumbnail-images {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
}

.thumbnail-images img {
    width: 100%;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
}

.image-placeholder {
    width: 100%;
    height: 400px;
    background: #f5f5f5;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 6rem;
}

.product-info h1 {
    margin-top: 0;
    color: #333;
    font-size: 2rem;
}

.price {
    margin: 1.5rem 0;
    padding: 1rem;
    background: #f5f5f5;
    border-radius: 8px;
    display: flex;
    align-items: baseline;
    gap: 1rem;
}

.price-label {
    font-size: 1rem;
    color: #666;
}

.price-value {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2196F3;
}

.product-description {
    margin: 1.5rem 0;
    line-height: 1.6;
    color: #555;
}

.product-meta {
    margin: 1.5rem 0;
    padding: 1rem;
    background: #f9f9f9;
    border-radius: 8px;
}

.product-meta h3 {
    margin-top: 0;
}

.product-meta ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.product-meta li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e0e0e0;
}

.product-meta li:last-child {
    border-bottom: none;
}

.seller-info {
    margin: 1.5rem 0;
    padding: 1rem;
    background: #e3f2fd;
    border-radius: 8px;
}

.seller-info p {
    margin: 0.5rem 0;
}

.cta-buttons {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 2rem;
}

.add-to-cart-form {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.quantity-input {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    width: 100px;
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.25rem;
}

.landing-footer {
    background: #333;
    color: white;
    padding: 2rem 0;
    text-align: center;
}

.landing-footer a {
    color: #2196F3;
    text-decoration: none;
}

.landing-footer a:hover {
    text-decoration: underline;
}

@media (max-width: 992px) {
    .hero-content {
        grid-template-columns: 1fr;
    }

    .main-image img, .image-placeholder {
        height: 300px;
    }
}
</style>

<?php
$content = ob_get_clean();
$title = e($product['title']) . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
