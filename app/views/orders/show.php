<?php
ob_start();
?>

<div class="container order-detail-container">
    <div class="page-header">
        <div>
            <a href="/orders" class="back-link">← <?= lang('back_to_orders') ?></a>
            <h1><?= lang('order') ?> #<?= htmlspecialchars($order['id']) ?></h1>
        </div>
        <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
            <?= lang('status_' . $order['status']) ?>
        </span>
    </div>

    <div class="order-details">
        <div class="detail-section">
            <h2><?= lang('order_information') ?></h2>
            <div class="info-grid">
                <div class="info-item">
                    <label><?= lang('order_number') ?>:</label>
                    <span>#<?= htmlspecialchars($order['id']) ?></span>
                </div>
                <div class="info-item">
                    <label><?= lang('order_date') ?>:</label>
                    <span><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></span>
                </div>
                <div class="info-item">
                    <label><?= lang('status') ?>:</label>
                    <span class="status-badge status-<?= htmlspecialchars($order['status']) ?>">
                        <?= lang('status_' . $order['status']) ?>
                    </span>
                </div>
                <?php if (!empty($order['payment_method'])): ?>
                    <div class="info-item">
                        <label><?= lang('payment_method') ?>:</label>
                        <span><?= htmlspecialchars($order['payment_method']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="detail-section">
            <h2><?= lang('product_details') ?></h2>
            <div class="product-info">
                <h3><?= htmlspecialchars($order['product_title'] ?? lang('product')) ?></h3>
                <div class="quantity-price">
                    <span><?= lang('quantity') ?>: <?= htmlspecialchars($order['quantity']) ?></span>
                    <span class="price">€<?= number_format($order['price_per_unit'], 2) ?> / <?= lang('unit') ?></span>
                </div>
            </div>
        </div>

        <?php if (!empty($order['delivery_address']) || !empty($order['delivery_method'])): ?>
            <div class="detail-section">
                <h2><?= lang('delivery_information') ?></h2>
                <div class="info-grid">
                    <?php if (!empty($order['delivery_method'])): ?>
                        <div class="info-item full-width">
                            <label><?= lang('delivery_method') ?>:</label>
                            <span><?= htmlspecialchars($order['delivery_method']) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($order['delivery_address'])): ?>
                        <div class="info-item full-width">
                            <label><?= lang('delivery_address') ?>:</label>
                            <span><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($order['notes'])): ?>
                        <div class="info-item full-width">
                            <label><?= lang('notes') ?>:</label>
                            <span><?= nl2br(htmlspecialchars($order['notes'])) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="detail-section">
            <h2><?= lang('order_summary') ?></h2>
            <div class="order-summary">
                <div class="summary-row">
                    <span><?= lang('subtotal') ?>:</span>
                    <span>€<?= number_format($order['total_amount'], 2) ?></span>
                </div>
                <div class="summary-row total">
                    <span><?= lang('total') ?>:</span>
                    <span>€<?= number_format($order['total_amount'], 2) ?></span>
                </div>
            </div>
        </div>

        <?php if ($order['status'] === 'pending' || $order['status'] === 'confirmed'): ?>
            <div class="order-actions">
                <form action="/order/<?= $order['id'] ?>/cancel" method="POST" onsubmit="return confirm('<?= lang('confirm_cancel_order') ?>');">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger"><?= lang('cancel_order') ?></button>
                </form>
            </div>
        <?php endif; ?>

        <?php
        // Parādīt atsauksmju sadaļu, ja pasūtījums ir pabeigts
        if ($order['status'] === 'completed'):
            $userId = AuthHelper::getUserId();

            // Noteikt, vai lietotājs ir pircējs vai pārdevējs
            $isBuyer = ($order['buyer_id'] == $userId);
            $reviewType = $isBuyer ? 'buyer_to_seller' : 'seller_to_buyer';
            $reviewedName = $isBuyer ? $order['seller_name'] : $order['buyer_name'];

            // Pārbaudīt vai atsauksme jau eksistē
            require_once ROOT_DIR . '/app/controllers/ReviewController.php';
            $reviewController = new ReviewController();
            $existingReview = db()->fetch(
                "SELECT * FROM reviews WHERE order_id = :order_id AND reviewer_id = :reviewer_id AND review_type = :review_type",
                [
                    'order_id' => $order['id'],
                    'reviewer_id' => $userId,
                    'review_type' => $reviewType
                ]
            );
        ?>
            <div class="detail-section review-section">
                <h2><?= lang('review.leave_review') ?></h2>

                <?php if ($existingReview): ?>
                    <div class="review-submitted">
                        <div class="alert alert-success">
                            <strong><?= lang('review.already_submitted') ?></strong>
                        </div>
                        <div class="existing-review">
                            <div class="review-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?= $i <= $existingReview['rating'] ? 'filled' : '' ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <p class="review-comment"><?= e($existingReview['comment']) ?></p>
                            <p class="review-date"><?= date('d.m.Y H:i', strtotime($existingReview['created_at'])) ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p><?= lang('review.rate_experience') ?> <strong><?= e($reviewedName) ?></strong></p>

                    <form action="/review/order/<?= $order['id'] ?>" method="POST" class="review-form">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="rating"><?= lang('review.rating') ?> <span class="required">*</span></label>
                            <div class="star-rating">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" required>
                                    <label for="star<?= $i ?>" title="<?= $i ?> <?= lang('review.stars') ?>">★</label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="comment"><?= lang('review.comment') ?> <span class="required">*</span></label>
                            <textarea
                                name="comment"
                                id="comment"
                                rows="4"
                                required
                                maxlength="1000"
                                placeholder="<?= lang('review.comment_placeholder') ?>"
                            ></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary"><?= lang('review.submit') ?></button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .order-detail-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e0e0e0;
    }

    .back-link {
        display: block;
        color: #2196F3;
        text-decoration: none;
        margin-bottom: 0.5rem;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .page-header h1 {
        margin: 0;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-pending { background: #fff3cd; color: #856404; }
    .status-confirmed { background: #d1ecf1; color: #0c5460; }
    .status-processing { background: #d1ecf1; color: #0c5460; }
    .status-ready { background: #cce5ff; color: #004085; }
    .status-delivering { background: #cce5ff; color: #004085; }
    .status-completed { background: #d4edda; color: #155724; }
    .status-cancelled { background: #f8d7da; color: #721c24; }

    .order-details {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .detail-section {
        padding: 2rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .detail-section:last-child {
        border-bottom: none;
    }

    .detail-section h2 {
        margin-top: 0;
        margin-bottom: 1.5rem;
        color: #333;
        font-size: 1.25rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-item.full-width {
        grid-column: 1 / -1;
    }

    .info-item label {
        font-weight: 600;
        color: #666;
        font-size: 0.875rem;
    }

    .info-item span {
        color: #333;
    }

    .product-info h3 {
        margin-top: 0;
        margin-bottom: 1rem;
        color: #2196F3;
    }

    .quantity-price {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #666;
    }

    .quantity-price .price {
        font-weight: 600;
        color: #333;
        font-size: 1.125rem;
    }

    .order-summary {
        max-width: 400px;
        margin-left: auto;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .summary-row.total {
        font-size: 1.25rem;
        font-weight: bold;
        border-top: 2px solid #333;
        border-bottom: none;
        margin-top: 0.5rem;
        padding-top: 1rem;
        color: #2196F3;
    }

    .order-actions {
        padding: 2rem;
        background: #f5f5f5;
        text-align: right;
    }

    /* Review Section */
    .review-section {
        background: #f9f9f9;
    }

    .review-submitted {
        padding: 1rem;
    }

    .alert {
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .existing-review {
        padding: 1rem;
        background: white;
        border-radius: 4px;
    }

    .review-rating {
        margin-bottom: 1rem;
    }

    .review-rating .star {
        font-size: 1.5rem;
        color: #ddd;
    }

    .review-rating .star.filled {
        color: #ffc107;
    }

    .review-comment {
        color: #333;
        margin-bottom: 0.5rem;
        line-height: 1.6;
    }

    .review-date {
        color: #999;
        font-size: 0.875rem;
    }

    .review-form {
        max-width: 600px;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #333;
    }

    .required {
        color: #dc3545;
    }

    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 0.25rem;
    }

    .star-rating input[type="radio"] {
        display: none;
    }

    .star-rating label {
        font-size: 2rem;
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }

    .star-rating input[type="radio"]:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #ffc107;
    }

    .review-form textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: inherit;
        font-size: 1rem;
        resize: vertical;
    }

    .review-form textarea:focus {
        outline: none;
        border-color: #2196F3;
        box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: 1rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .order-summary {
            max-width: 100%;
        }

        .quantity-price {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>

<?php
$content = ob_get_clean();
$title = lang('order') . ' #' . $order['id'] . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
