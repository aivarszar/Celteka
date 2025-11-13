<?php ob_start(); ?>

<div class="container cart-container">
    <h1><?= lang('cart.my_cart') ?? 'Mans grozs' ?></h1>

    <?php if (empty($cartItems)): ?>
        <div class="empty-cart">
            <div class="empty-icon">🛒</div>
            <h2><?= lang('cart.empty') ?? 'Jūsu grozs ir tukšs' ?></h2>
            <p><?= lang('cart.empty_message') ?? 'Pievienojiet produktus, lai turpinātu iepirkšanos' ?></p>
            <a href="/products" class="btn btn-primary"><?= lang('cart.continue_shopping') ?? 'Turpināt iepirkties' ?></a>
        </div>
    <?php else: ?>
        <div class="cart-content">
            <div class="cart-items">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <div class="item-image">
                            <?php if (!empty($item['product']['primary_image'])): ?>
                                <img src="<?= e($item['product']['primary_image']) ?>" alt="<?= e($item['product']['title']) ?>">
                            <?php else: ?>
                                <div class="image-placeholder">📦</div>
                            <?php endif; ?>
                        </div>
                        <div class="item-details">
                            <h3><a href="/product/<?= e($item['product']['slug']) ?>"><?= e($item['product']['title']) ?></a></h3>
                            <p class="item-price">€<?= number_format($item['product']['price'], 2) ?></p>
                            <p class="item-seller"><?= lang('common.seller') ?? 'Pārdevējs' ?>: <?= e($item['product']['seller_name']) ?></p>
                        </div>
                        <div class="item-quantity">
                            <form action="/cart/update/<?= $item['product']['id'] ?>" method="post" class="quantity-form">
                                <?= csrf_field() ?>
                                <label for="quantity-<?= $item['product']['id'] ?>"><?= lang('common.quantity') ?? 'Daudzums' ?>:</label>
                                <input type="number"
                                       id="quantity-<?= $item['product']['id'] ?>"
                                       name="quantity"
                                       value="<?= $item['quantity'] ?>"
                                       min="1"
                                       <?php if ($item['product']['type'] === 'product'): ?>
                                           max="<?= $item['product']['stock_quantity'] ?>"
                                       <?php endif; ?>
                                       class="quantity-input">
                                <button type="submit" class="btn btn-sm btn-secondary"><?= lang('cart.update') ?? 'Atjaunināt' ?></button>
                            </form>
                        </div>
                        <div class="item-subtotal">
                            <strong>€<?= number_format($item['subtotal'], 2) ?></strong>
                        </div>
                        <div class="item-remove">
                            <form action="/cart/remove/<?= $item['product']['id'] ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-remove" title="<?= lang('common.remove') ?? 'Noņemt' ?>">🗑️</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h2><?= lang('cart.summary') ?? 'Kopsavilkums' ?></h2>
                <div class="summary-row">
                    <span><?= lang('cart.subtotal') ?? 'Starpsumma' ?>:</span>
                    <span class="summary-value">€<?= number_format($total, 2) ?></span>
                </div>
                <div class="summary-row total-row">
                    <span><?= lang('cart.total') ?? 'Kopā' ?>:</span>
                    <span class="summary-value">€<?= number_format($total, 2) ?></span>
                </div>
                <a href="/checkout" class="btn btn-primary btn-block"><?= lang('cart.proceed_checkout') ?? 'Turpināt uz apmaksu' ?></a>
                <a href="/products" class="btn btn-outline btn-block"><?= lang('cart.continue_shopping') ?? 'Turpināt iepirkties' ?></a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.cart-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.empty-cart {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.cart-content {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
}

.cart-items {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1.5rem;
}

.cart-item {
    display: grid;
    grid-template-columns: 100px 1fr 150px 100px 50px;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #e0e0e0;
    align-items: center;
}

.cart-item:last-child {
    border-bottom: none;
}

.item-image img, .image-placeholder {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
}

.image-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    font-size: 2rem;
}

.item-details h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
}

.item-details a {
    color: #333;
    text-decoration: none;
}

.item-details a:hover {
    color: #2196F3;
}

.item-price {
    font-size: 1.25rem;
    color: #2196F3;
    font-weight: bold;
    margin: 0.25rem 0;
}

.item-seller {
    color: #666;
    font-size: 0.875rem;
    margin: 0;
}

.quantity-form {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.quantity-input {
    width: 70px;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.item-subtotal {
    text-align: right;
    font-size: 1.25rem;
}

.btn-remove {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.5rem;
    opacity: 0.6;
    transition: opacity 0.2s;
}

.btn-remove:hover {
    opacity: 1;
}

.cart-summary {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1.5rem;
    height: fit-content;
    position: sticky;
    top: 2rem;
}

.cart-summary h2 {
    margin-top: 0;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e0e0e0;
}

.total-row {
    font-size: 1.25rem;
    font-weight: bold;
    border-bottom: none;
    margin-top: 0.5rem;
    padding-top: 1rem;
    border-top: 2px solid #333;
}

.btn-block {
    width: 100%;
    margin-top: 1rem;
}

@media (max-width: 992px) {
    .cart-content {
        grid-template-columns: 1fr;
    }

    .cart-summary {
        position: static;
    }

    .cart-item {
        grid-template-columns: 80px 1fr;
        gap: 1rem;
    }

    .item-quantity, .item-subtotal, .item-remove {
        grid-column: 2;
    }
}
</style>

<?php
$content = ob_get_clean();
$title = (lang('cart.my_cart') ?? 'Mans grozs') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
