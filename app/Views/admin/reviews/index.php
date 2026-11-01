<?php $escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); ?>
<section class="section-wrap dashboard-page admin-page">
    <div class="dashboard-heading">
        <div>
            <p class="eyebrow">COMMUNITY CARE</p>
            <h1>Customer reviews<span class="brand-dot">.</span></h1>
            <p>Review comments, approve them for the storefront, and use approved comments in each product’s AI Review Notes.</p>
        </div>
    </div>
    <div class="data-card">
        <div class="table-head reviews-admin-head"><span>Product</span><span>Customer</span><span>Rating</span><span>Review</span><span>Status / actions</span></div>
        <?php foreach ($reviews as $review): ?>
            <div class="table-row reviews-admin-head">
                <b><?= $escape($review['product_name']) ?></b>
                <span><?= $escape($review['user_name']) ?></span>
                <span class="rating-stars">★ <?= $escape($review['star_rating']) ?>/5</span>
                <span><?= nl2br($escape($review['comment'] ?? 'Rating only')) ?></span>
                <span class="row-actions review-status-actions">
                    <strong class="review-status review-status-<?= $escape($review['status']) ?>"><?= $escape(ucfirst($review['status'])) ?></strong>
                    <?php if ($review['status'] === 'pending'): ?>
                        <?php foreach (['approve' => 'Approve', 'reject' => 'Decline'] as $action => $label): ?>
                            <form method="post" action="<?= $escape($base_path) ?>/admin/reviews/<?= $escape($action) ?>">
                                <input type="hidden" name="review_id" value="<?= (int) $review['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $escape($csrf_token) ?>">
                                <button><?= $escape($label) ?></button>
                            </form>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </span>
            </div>
        <?php endforeach; ?>
        <?php if (!$reviews): ?><p class="empty-inline">No customer reviews yet.</p><?php endif; ?>
    </div>
</section>
