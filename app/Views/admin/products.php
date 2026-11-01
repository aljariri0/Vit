<?php $escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); ?>
<section class="section-wrap dashboard-page admin-page">
    <div class="dashboard-heading">
        <div><p class="eyebrow">YOUR COLLECTION</p><h1>Products<span class="brand-dot">.</span></h1><p>Keep your Shifa edit fresh and thoughtful.</p></div>
        <a class="button button-dark" href="<?= $escape($base) ?>/admin/product">Add a product <span>＋</span></a>
    </div>
    <form class="order-filters product-search" method="get" action="<?= $escape($base) ?>/admin/products" role="search"><label for="admin-product-search">Find a product</label><input id="admin-product-search" name="q" value="<?= $escape($search ?? '') ?>" placeholder="Product, brand or category"><button class="button button-dark">Search</button><?php if (!empty($search)): ?><a class="text-link" href="<?= $escape($base) ?>/admin/products">Clear search</a><?php endif; ?><span><?= count($products) ?> results</span></form>
    <div class="data-card">
        <div class="table-head products-admin-head"><span>Product</span><span>Category</span><span>Price</span><span>Stock</span><span>Visibility</span><span></span></div>
        <?php foreach ($products as $product): ?>
            <div class="table-row products-admin-head">
                <b><?= $escape($product['name']) ?></b>
                <span><?= $escape($product['category_name'] ?? '—') ?></span>
                <span><?= number_format((float) $product['price'], 2) ?> USD</span>
                <span><?= (int) $product['stock'] ?></span>
                <span><i class="status-pill <?= (int) $product['is_active'] ? 'status-live' : 'status-cancelled' ?>"><?= (int) $product['is_active'] ? 'Live' : 'Hidden' ?></i></span>
                <span class="row-actions">
                    <a href="<?= $escape($base) ?>/admin/product?id=<?= (int) $product['id'] ?>">Edit</a>
                    <form action="<?= $escape($base) ?>/admin/product/delete" method="post" onsubmit="return confirm('Hide this product from the shop?')">
                        <input type="hidden" name="csrf_token" value="<?= $escape($csrf_token) ?>">
                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                        <button>Hide</button>
                    </form>
                </span>
            </div>
            <details style="padding:12px 16px 16px;border-bottom:1px solid var(--line)">
                <summary style="cursor:pointer;color:var(--forest);font-size:12px;font-weight:700">
                    Review Notes · <?= (int) $product['approved_review_count'] ?> approved written reviews
                </summary>
                <div style="display:flex;gap:24px;align-items:flex-start;justify-content:space-between;padding-top:12px">
                    <div style="font-size:12px;line-height:1.7;color:#59645b">
                        <?php if (!empty($product['review_summary'])): ?>
                            <?= nl2br($escape($product['review_summary'])) ?>
                            <small style="display:block;color:#879083;margin-top:7px">
                                Based on <?= (int) $product['review_summary_review_count'] ?> reviews · Updated <?= $escape(date('M j, Y g:i a', strtotime($product['review_summary_generated_at']))) ?>
                                <?php if ((int) $product['approved_review_count'] > (int) $product['review_summary_review_count']): ?> · New reviews available<?php endif; ?>
                            </small>
                        <?php else: ?>
                            <span>No Review Notes yet. Generate them from approved written reviews.</span>
                        <?php endif; ?>
                    </div>
                    <form action="<?= $escape($base) ?>/admin/product/review-notes" method="post">
                        <input type="hidden" name="csrf_token" value="<?= $escape($csrf_token) ?>">
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <button class="mini-action" <?= (int) $product['approved_review_count'] === 0 ? 'disabled' : '' ?>>Generate notes</button>
                    </form>
                </div>
            </details>
        <?php endforeach; ?>
        <?php if (!$products): ?><p class="empty-inline">Your collection is ready for its first product.</p><?php endif; ?>
    </div>
</section>
