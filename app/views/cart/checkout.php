<?php ob_start(); ?>

<div class="container checkout-container">
    <h1><?= lang('cart.checkout') ?? 'Pasūtījuma noformēšana' ?></h1>

    <div class="checkout-content">
        <!-- Order Summary -->
        <div class="checkout-main">
            <form action="/checkout" method="post" id="checkout-form">
                <?= csrf_field() ?>

                <!-- Cart Items -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h2><?= lang('cart.order_items') ?? 'Pasūtījuma preces' ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="checkout-items">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="checkout-item">
                                    <div class="item-image">
                                        <?php if (!empty($item['product']['primary_image'])): ?>
                                            <img src="<?= e($item['product']['primary_image']) ?>" alt="<?= e($item['product']['title']) ?>">
                                        <?php else: ?>
                                            <div class="image-placeholder">📦</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="item-info">
                                        <h4><?= e($item['product']['title']) ?></h4>
                                        <p class="item-seller"><?= lang('common.seller') ?? 'Pārdevējs' ?>: <?= e($item['product']['seller_name']) ?></p>
                                        <p class="item-quantity"><?= lang('common.quantity') ?? 'Daudzums' ?>: <?= $item['quantity'] ?> × €<?= number_format($item['product']['price'], 2) ?></p>
                                    </div>
                                    <div class="item-total">
                                        <strong>€<?= number_format($item['subtotal'], 2) ?></strong>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Delivery Method -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h2><?= lang('cart.delivery_method') ?? 'Piegādes veids' ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="delivery-methods">
                            <?php foreach ($deliveryMethods as $method): ?>
                                <label class="delivery-method-option">
                                    <input type="radio"
                                           name="delivery_method_id"
                                           value="<?= $method['id'] ?>"
                                           <?= isset($deliveryMethods[0]) && $method['id'] === $deliveryMethods[0]['id'] ? 'checked' : '' ?>
                                           required>
                                    <div class="method-details">
                                        <div class="method-icon">
                                            <?php
                                            $icons = [
                                                'pickup' => '🏪',
                                                'seller_delivery' => '🚚',
                                                'routed_delivery' => '📦'
                                            ];
                                            echo $icons[$method['type']] ?? '📦';
                                            ?>
                                        </div>
                                        <div class="method-info">
                                            <strong><?= e($method['name']) ?></strong>
                                            <p><?= e($method['description']) ?></p>
                                            <?php if ($method['base_price'] > 0): ?>
                                                <span class="method-price">+€<?= number_format($method['base_price'], 2) ?></span>
                                            <?php else: ?>
                                                <span class="method-price-free"><?= lang('cart.free') ?? 'Bezmaksas' ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Delivery Address -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h2><?= lang('cart.delivery_address') ?? 'Piegādes adrese' ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="delivery_address"><?= lang('cart.address') ?? 'Adrese' ?>:</label>
                            <textarea id="delivery_address"
                                      name="delivery_address"
                                      class="form-control"
                                      rows="3"
                                      placeholder="<?= lang('cart.address_placeholder') ?? 'Ievadiet pilnu adresi' ?>"><?= e(Session::get('old_input.delivery_address', '')) ?></textarea>
                            <small class="form-text"><?= lang('cart.address_note') ?? 'Pārdevējs sazināsies ar jums, lai precizētu piegādes detaļas' ?></small>
                        </div>

                        <div class="form-group">
                            <label for="delivery_notes"><?= lang('cart.delivery_notes') ?? 'Piezīmes' ?>:</label>
                            <textarea id="delivery_notes"
                                      name="delivery_notes"
                                      class="form-control"
                                      rows="2"
                                      placeholder="<?= lang('cart.notes_placeholder') ?? 'Papildu informācija par piegādi' ?>"><?= e(Session::get('old_input.delivery_notes', '')) ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h2><?= lang('cart.payment_method') ?? 'Maksājuma veids' ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-3">
                            <strong>ℹ️ <?= lang('cart.payment_info_title') ?? 'Maksājumu apstrāde' ?></strong><br>
                            <?= lang('cart.payment_info') ?? 'Platforma nenodrošina maksājumu starpniecību. Norēķins notiek tieši ar pārdevēju pēc vienošanās.' ?>
                        </div>

                        <div class="payment-methods">
                            <label class="payment-method-option">
                                <input type="radio" name="payment_method" value="cash" checked>
                                <div class="method-details">
                                    <div class="method-icon">💵</div>
                                    <div class="method-info">
                                        <strong><?= lang('cart.payment_cash') ?? 'Skaidra nauda' ?></strong>
                                        <p><?= lang('cart.payment_cash_desc') ?? 'Maksājums skaidrā naudā piegādes brīdī' ?></p>
                                    </div>
                                </div>
                            </label>

                            <label class="payment-method-option">
                                <input type="radio" name="payment_method" value="bank_transfer">
                                <div class="method-details">
                                    <div class="method-icon">🏦</div>
                                    <div class="method-info">
                                        <strong><?= lang('cart.payment_transfer') ?? 'Bankas pārskaitījums' ?></strong>
                                        <p><?= lang('cart.payment_transfer_desc') ?? 'Pārskaitījums uz pārdevēja bankas kontu' ?></p>
                                    </div>
                                </div>
                            </label>

                            <label class="payment-method-option">
                                <input type="radio" name="payment_method" value="card">
                                <div class="method-details">
                                    <div class="method-icon">💳</div>
                                    <div class="method-info">
                                        <strong><?= lang('cart.payment_card') ?? 'Karšu maksājums' ?></strong>
                                        <p><?= lang('cart.payment_card_desc') ?? 'Maksājums ar bankas karti (ja pārdevējs atbalsta)' ?></p>
                                    </div>
                                </div>
                            </label>

                            <label class="payment-method-option">
                                <input type="radio" name="payment_method" value="other">
                                <div class="method-details">
                                    <div class="method-icon">🤝</div>
                                    <div class="method-info">
                                        <strong><?= lang('cart.payment_other') ?? 'Cits veids' ?></strong>
                                        <p><?= lang('cart.payment_other_desc') ?? 'Vienoties ar pārdevēju' ?></p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="card mb-3">
                    <div class="card-body">
                        <label class="checkbox-label">
                            <input type="checkbox" name="agree_terms" required>
                            <span>
                                <?= lang('cart.agree_terms') ?? 'Piekrītu' ?>
                                <a href="/terms" target="_blank"><?= lang('cart.terms') ?? 'lietošanas noteikumiem' ?></a>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="/cart" class="btn btn-outline"><?= lang('common.back') ?? 'Atpakaļ' ?></a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <?= lang('cart.place_order') ?? 'Apstiprināt pasūtījumu' ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="checkout-sidebar">
            <div class="card order-summary">
                <div class="card-header">
                    <h3><?= lang('cart.order_summary') ?? 'Pasūtījuma kopsavilkums' ?></h3>
                </div>
                <div class="card-body">
                    <div class="summary-row">
                        <span><?= lang('cart.items') ?? 'Preces' ?> (<?= count($cartItems) ?>):</span>
                        <span>€<?= number_format($total, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span><?= lang('cart.delivery') ?? 'Piegāde' ?>:</span>
                        <span id="delivery-price">€0.00</span>
                    </div>
                    <div class="summary-row total-row">
                        <strong><?= lang('cart.total') ?? 'Kopā' ?>:</strong>
                        <strong id="grand-total">€<?= number_format($total, 2) ?></strong>
                    </div>

                    <div class="summary-note">
                        <small>
                            <strong>📋 <?= lang('cart.note') ?? 'Piezīme' ?>:</strong><br>
                            <?= lang('cart.seller_contact') ?? 'Pārdevējs sazināsies ar jums, lai apspriestu pasūtījuma detaļas un precizētu piegādes laiku.' ?>
                        </small>
                    </div>
                </div>
            </div>

            <div class="secure-checkout">
                <div class="secure-icon">🔒</div>
                <div>
                    <strong><?= lang('cart.secure_checkout') ?? 'Droša noformēšana' ?></strong>
                    <p><?= lang('cart.secure_desc') ?? 'Jūsu dati ir aizsargāti' ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.checkout-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.checkout-content {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 2rem;
    margin-top: 2rem;
}

.checkout-main {
    min-width: 0;
}

.card-header h2, .card-header h3 {
    margin: 0;
    font-size: 1.25rem;
}

.checkout-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.checkout-item {
    display: grid;
    grid-template-columns: 80px 1fr auto;
    gap: 1rem;
    padding: 1rem;
    background: #f9f9f9;
    border-radius: 8px;
    align-items: center;
}

.item-image img, .image-placeholder {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.image-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e0e0e0;
    font-size: 2rem;
}

.item-info h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
}

.item-seller, .item-quantity {
    margin: 0.25rem 0;
    font-size: 0.875rem;
    color: #666;
}

.item-total {
    font-size: 1.25rem;
    text-align: right;
}

.delivery-methods, .payment-methods {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.delivery-method-option, .payment-method-option {
    display: flex;
    align-items: flex-start;
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.delivery-method-option:hover, .payment-method-option:hover {
    border-color: #2196F3;
    background: #f5f9ff;
}

.delivery-method-option input, .payment-method-option input {
    margin-top: 0.25rem;
}

.delivery-method-option input:checked ~ .method-details,
.payment-method-option input:checked ~ .method-details {
    opacity: 1;
}

.delivery-method-option, .payment-method-option {
    position: relative;
}

.delivery-method-option:has(input:checked), .payment-method-option:has(input:checked) {
    border-color: #2196F3;
    background: #f5f9ff;
}

.method-details {
    display: flex;
    gap: 1rem;
    margin-left: 0.5rem;
    flex: 1;
}

.method-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.method-info {
    flex: 1;
}

.method-info strong {
    display: block;
    margin-bottom: 0.25rem;
}

.method-info p {
    margin: 0.25rem 0;
    font-size: 0.875rem;
    color: #666;
}

.method-price {
    color: #2196F3;
    font-weight: bold;
}

.method-price-free {
    color: #4CAF50;
    font-weight: bold;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    font-family: inherit;
}

.form-text {
    display: block;
    margin-top: 0.5rem;
    color: #666;
    font-size: 0.875rem;
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

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: space-between;
    margin-top: 2rem;
}

.checkout-sidebar {
    position: sticky;
    top: 2rem;
    height: fit-content;
}

.order-summary {
    margin-bottom: 1.5rem;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e0e0e0;
}

.total-row {
    font-size: 1.25rem;
    border-bottom: none;
    margin-top: 0.5rem;
    padding-top: 1rem;
    border-top: 2px solid #333;
}

.summary-note {
    margin-top: 1rem;
    padding: 1rem;
    background: #fff3cd;
    border-radius: 8px;
}

.secure-checkout {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #e8f5e9;
    border-radius: 8px;
}

.secure-icon {
    font-size: 2rem;
}

.secure-checkout strong {
    display: block;
    color: #2e7d32;
}

.secure-checkout p {
    margin: 0.25rem 0 0 0;
    font-size: 0.875rem;
    color: #558b2f;
}

@media (max-width: 1200px) {
    .checkout-content {
        grid-template-columns: 1fr;
    }

    .checkout-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .checkout-item {
        grid-template-columns: 60px 1fr;
    }

    .item-total {
        grid-column: 2;
        text-align: left;
        margin-top: 0.5rem;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .form-actions .btn {
        width: 100%;
    }
}
</style>

<script>
// Update delivery price and grand total when delivery method changes
document.addEventListener('DOMContentLoaded', function() {
    const deliveryMethodInputs = document.querySelectorAll('input[name="delivery_method_id"]');
    const deliveryPriceEl = document.getElementById('delivery-price');
    const grandTotalEl = document.getElementById('grand-total');
    const subtotal = <?= $total ?>;

    const deliveryPrices = {
        <?php foreach ($deliveryMethods as $method): ?>
        <?= $method['id'] ?>: <?= $method['base_price'] ?>,
        <?php endforeach; ?>
    };

    function updateTotal() {
        const selectedMethod = document.querySelector('input[name="delivery_method_id"]:checked');
        if (selectedMethod) {
            const deliveryPrice = deliveryPrices[selectedMethod.value] || 0;
            const total = subtotal + deliveryPrice;

            deliveryPriceEl.textContent = '€' + deliveryPrice.toFixed(2);
            grandTotalEl.textContent = '€' + total.toFixed(2);
        }
    }

    deliveryMethodInputs.forEach(input => {
        input.addEventListener('change', updateTotal);
    });

    updateTotal();
});
</script>

<?php
$content = ob_get_clean();
$title = (lang('cart.checkout') ?? 'Pasūtījuma noformēšana') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
