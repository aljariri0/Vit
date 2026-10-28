<div class="card-clean p-4 mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-md-5 text-center">
            <img src="<?= e($product['image']) ?>" class="img-fluid rounded-4" style="max-height: 360px; object-fit: cover;">
        </div>
        <div class="col-md-7">
            <span class="badge bg-success-subtle text-success mb-2"><?= e($product['brand_name']) ?> • <?= e($product['category_name']) ?></span>
            <h2 class="fw-bold"><?= e($product['name']) ?></h2>

            <div class="text-warning mb-3">
                <i class="fa-solid fa-star"></i> <strong><?= $product['rating_avg'] ?></strong>
                <span class="text-muted small">(<?= number_format($product['rating_count']) ?> customer reviews) | Package Weight: <?= $product['weight_kg'] ?> kg</span>
            </div>

            <div class="mb-3">
                <span class="fs-2 fw-bold text-vite-green">$<?= final_price($product['price'], $product['discount_percent']) ?></span>
                <?php if ($product['discount_percent'] > 0): ?>
                    <del class="text-muted ms-2">$<?= number_format($product['price'], 2) ?></del>
                    <span class="badge bg-danger ms-2">Save $<?= saved_amount($product['price'], $product['discount_percent']) ?></span>
                <?php endif; ?>
            </div>

            <p class="text-secondary"><?= e($product['description']) ?></p>

            <!-- Add to Cart Form (`cart_items` schema: size = tub size, color = flavor) -->
            <form action="?page=cart" method="POST" class="row g-3 mt-2">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div class="col-sm-5">
                    <label class="form-label small fw-bold">Container Size / Count</label>
                    <select name="size" class="form-select">
                        <?php foreach ($product['sizes'] as $s): ?>
                            <option value="<?= e($s) ?>"><?= e($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label small fw-bold">Flavor / Variant</label>
                    <select name="color" class="form-select">
                        <?php foreach ($product['flavors'] as $f): ?>
                            <option value="<?= e($f) ?>"><?= e($f) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-sm-3">
                    <label class="form-label small fw-bold">Qty</label>
                    <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="form-control">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-vite btn-lg w-100">
                        <i class="fa-solid fa-cart-plus me-2"></i>Add Supplement to Cart
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reviews & Star Ratings Section -->
<div class="row g-4">
    <div class="col-md-6">
        <div class="card-clean p-4 h-100">
            <h5 class="fw-bold mb-3">Verified Buyer Reviews</h5>
            <div class="border-bottom pb-2 mb-2">
                <div class="d-flex justify-content-between"><strong>Omar A.</strong><span class="text-warning">⭐ 5/5</span></div>
                <p class="small text-muted mb-0">Mixes easily with cold water, zero bloating, and great chocolate taste.</p>
            </div>
            <div>
                <div class="d-flex justify-content-between"><strong>Lina M.</strong><span class="text-warning">⭐ 5/5</span></div>
                <p class="small text-muted mb-0">Fast delivery and authentic sealed tub with batch verification code.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <form action="/reviews/store" method="POST" class="card-clean p-4">
            <?= csrf_field() ?>
            <h5 class="fw-bold mb-3">Write a Customer Review</h5>
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <select name="star_rating" class="form-select mb-2">
                <option value="5">5 Stars - Excellent</option>
                <option value="4">4 Stars - Very Good</option>
                <option value="3">3 Stars - Average</option>
                <option value="2">2 Stars - Poor</option>
                <option value="1">1 Star - Bad</option>
            </select>
            <textarea name="comment" class="form-control mb-3" rows="2" placeholder="Share your experience with taste, mixability, or results..." required></textarea>
            <button class="btn btn-vite px-4">Submit Review</button>
        </form>
    </div>
</div>