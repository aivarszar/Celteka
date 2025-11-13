<?php ob_start(); ?>

<div class="container admin-container">
    <div class="admin-header">
        <h1>⭐ <?= lang('review.reviews') ?></h1>
        <p><?= lang('admin.manage') ?> <?= lang('review.reviews') ?></p>
    </div>

    <?php if (!empty($reviews)): ?>
        <div class="reviews-grid">
            <?php foreach ($reviews as $review): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="review-info">
                            <strong><?= e($review['reviewer_name']) ?></strong>
                            <span class="review-arrow">→</span>
                            <strong><?= e($review['reviewed_name']) ?></strong>
                        </div>
                        <div class="review-rating">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $review['rating']): ?>
                                    <span class="star filled">★</span>
                                <?php else: ?>
                                    <span class="star">☆</span>
                                <?php endif; ?>
                            <?php endfor ?>
                            <span class="rating-value"><?= $review['rating'] ?>/5</span>
                        </div>
                    </div>

                    <div class="review-meta">
                        <span class="review-type-badge review-type-<?= e($review['review_type']) ?>">
                            <?php if ($review['review_type'] === 'buyer_to_seller'): ?>
                                <?= lang('review.buyer_review') ?>
                            <?php else: ?>
                                <?= lang('review.seller_review') ?>
                            <?php endif; ?>
                        </span>
                        <span class="review-order">
                            <?= lang('order.order') ?> #<?= e($review['order_number']) ?>
                        </span>
                        <span class="review-date">
                            <?= date('d.m.Y H:i', strtotime($review['created_at'])) ?>
                        </span>
                    </div>

                    <?php if (!empty($review['comment'])): ?>
                        <div class="review-comment">
                            <p><?= nl2br(e($review['comment'])) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="review-actions">
                        <a href="/order/<?= $review['order_id'] ?>" class="btn btn-sm btn-secondary">
                            👁️ <?= lang('order.view_details') ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Statistics Summary -->
        <div class="stats-summary">
            <?php
            $totalReviews = count($reviews);
            $avgRating = array_sum(array_column($reviews, 'rating')) / $totalReviews;
            $buyerReviews = count(array_filter($reviews, fn($r) => $r['review_type'] === 'buyer_to_seller'));
            $sellerReviews = count(array_filter($reviews, fn($r) => $r['review_type'] === 'seller_to_buyer'));

            $ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
            foreach ($reviews as $review) {
                $ratingCounts[$review['rating']]++;
            }
            ?>
            <div class="summary-card">
                <div class="summary-icon">⭐</div>
                <div class="summary-content">
                    <div class="summary-value"><?= $totalReviews ?></div>
                    <div class="summary-label"><?= lang('review.reviews') ?></div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon">📊</div>
                <div class="summary-content">
                    <div class="summary-value"><?= number_format($avgRating, 2) ?></div>
                    <div class="summary-label"><?= lang('review.rating') ?></div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon">🛒</div>
                <div class="summary-content">
                    <div class="summary-value"><?= $buyerReviews ?></div>
                    <div class="summary-label"><?= lang('review.buyer_review') ?></div>
                </div>
            </div>
            <div class="summary-card">
                <div class="summary-icon">🏪</div>
                <div class="summary-content">
                    <div class="summary-value"><?= $sellerReviews ?></div>
                    <div class="summary-label"><?= lang('review.seller_review') ?></div>
                </div>
            </div>
        </div>

        <!-- Rating Distribution -->
        <div class="rating-distribution">
            <h3>Vērtējumu sadalījums</h3>
            <?php foreach ([5, 4, 3, 2, 1] as $rating): ?>
                <?php
                $count = $ratingCounts[$rating];
                $percentage = $totalReviews > 0 ? ($count / $totalReviews * 100) : 0;
                ?>
                <div class="rating-bar">
                    <div class="rating-label"><?= $rating ?> ★</div>
                    <div class="rating-progress">
                        <div class="rating-progress-fill" style="width: <?= $percentage ?>%"></div>
                    </div>
                    <div class="rating-count"><?= $count ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-data"><?= lang('review.no_reviews') ?></div>
    <?php endif; ?>
</div>

<style>
.reviews-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.review-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.review-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.review-arrow {
    color: #999;
    font-weight: bold;
}

.review-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.star {
    font-size: 1.25rem;
    color: #ddd;
}

.star.filled {
    color: #ffc107;
}

.rating-value {
    margin-left: 0.5rem;
    font-weight: 600;
    color: #333;
}

.review-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    font-size: 0.875rem;
    color: #666;
}

.review-type-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.75rem;
}

.review-type-buyer_to_seller {
    background: #e3f2fd;
    color: #1976d2;
}

.review-type-seller_to_buyer {
    background: #fff3e0;
    color: #e65100;
}

.review-order {
    color: #999;
}

.review-date {
    color: #999;
}

.review-comment {
    background: #f9f9f9;
    padding: 1rem;
    border-radius: 8px;
    border-left: 3px solid #667eea;
}

.review-comment p {
    margin: 0;
    color: #333;
    line-height: 1.6;
}

.review-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: auto;
}

.stats-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.summary-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.summary-icon {
    font-size: 2.5rem;
}

.summary-value {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
}

.summary-label {
    font-size: 0.875rem;
    color: #666;
    margin-top: 0.25rem;
}

.rating-distribution {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.rating-distribution h3 {
    margin-bottom: 1.5rem;
    color: #333;
}

.rating-bar {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.rating-label {
    width: 50px;
    font-weight: 600;
    color: #666;
}

.rating-progress {
    flex: 1;
    height: 24px;
    background: #f0f0f0;
    border-radius: 12px;
    overflow: hidden;
}

.rating-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #ffc107 0%, #ff9800 100%);
    transition: width 0.3s ease;
}

.rating-count {
    width: 40px;
    text-align: right;
    font-weight: 600;
    color: #666;
}

.no-data {
    text-align: center;
    color: #999;
    padding: 2rem;
    background: white;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .reviews-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
$content = ob_get_clean();
$title = lang('review.reviews') . ' - ' . lang('admin.admin_panel') . ' - ' . lang('app.name');
require __DIR__ . '/../layout.php';
?>
