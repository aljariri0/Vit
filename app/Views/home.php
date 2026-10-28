<!-- Protine-Inspired Split Hero Section -->
<div class="p-4 p-md-5 mb-5 rounded-4 bg-vite-dark text-white shadow-sm">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="badge bg-success mb-2 px-3 py-2 rounded-pill">CLEAN SPORTS NUTRITION & DAILY WELLNESS</span>
            <h1 class="display-5 fw-bold mb-3">Fuel Your Muscle Recovery & Daily Immunity</h1>
            <p class="lead text-light opacity-75 mb-4">
                Science-backed Whey Isolates, Micronized Creatine, and Daily Multivitamins. Not sure what stack fits your body weight and training goal?
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="?page=shop" class="btn btn-vite btn-lg px-4">Shop Bestsellers</a>
                <a href="?page=assistant" class="btn btn-outline-light btn-lg rounded-pill px-4">
                    <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Ask AI Wellness Coach
                </a>
            </div>
        </div>
        <div class="col-lg-5 text-center">
            <img src="https://images.unsplash.com/photo-1593095948071-474c5cc2989d?w=600" class="img-fluid rounded-4 shadow" style="max-height: 300px; object-fit: cover;" alt="Protein & Vitamins">
        </div>
    </div>
</div>

<!-- Myvitamins "Shop By Health Goal" Category Cards -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold mb-0">Shop by Health Goal</h4>
    <a href="?page=shop" class="text-vite-green text-decoration-none fw-semibold">View All Categories &rarr;</a>
</div>
<div class="row g-3 mb-5">
    <?php foreach ($categories as $c): ?>
        <div class="col-6 col-md-3">
            <a href="?page=shop&category_id=<?= $c['id'] ?>" class="card-clean p-3 d-block text-decoration-none text-dark h-100">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" style="width:48px; height:48px; background: var(--vite-accent);">
                    <i class="fa-solid <?= e($c['icon']) ?> text-vite-green fs-5"></i>
                </div>
                <h6 class="fw-bold mb-1"><?= e($c['name']) ?></h6>
                <small class="text-muted"><?= e($c['description']) ?></small>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<!-- iHerb & Myvitamins Style Bestsellers Grid -->
<h4 class="fw-bold mb-3">Trending Supplements & Deals</h4>
<div class="row g-4">
    <?php foreach ($products as $p): ?>
        <div class="col-sm-6 col-lg-3">
            <div class="card-clean h-100 p-3 d-flex flex-column position-relative">
                <?php if ($p['discount_percent'] > 0): ?>
                    <span class="badge-discount position-absolute top-0 start-0 m-3 px-2 py-1 small">
                        Save <?= (int)$p['discount_percent'] ?>%
                    </span>
                <?php endif; ?>

                <a href="?page=product&id=<?= $p['id'] ?>" class="text-center mb-3">
                    <img src="<?= e($p['image']) ?>" class="img-fluid rounded-3" style="height: 180px; object-fit: cover;" alt="<?= e($p['name']) ?>">
                </a>

                <small class="text-uppercase text-muted fw-semibold" style="font-size: 0.75rem;"><?= e($p['brand_name']) ?></small>
                <a href="?page=product&id=<?= $p['id'] ?>" class="text-dark text-decoration-none fw-bold mt-1 mb-1">
                    <?= e($p['name']) ?>
                </a>
                <small class="text-muted mb-2"><?= e($p['sizes'][0]) ?></small>

                <!-- iHerb Style Rating & Review Count -->
                <div class="small text-warning mb-3">
                    <i class="fa-solid fa-star"></i> <strong><?= $p['rating_avg'] ?></strong>
                    <span class="text-muted">(<?= number_format($p['rating_count']) ?>)</span>
                </div>

                <!-- Price + Myvitamins "Quick Buy" Button -->
                <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                    <div>
                        <span class="fw-bold fs-5 text-vite-green">$<?= final_price($p['price'], $p['discount_percent']) ?></span>
                        <?php if ($p['discount_percent'] > 0): ?>
                            <del class="text-muted small ms-1">$<?= number_format($p['price'], 2) ?></del>
                        <?php endif; ?>
                    </div>
                    <a href="?page=product&id=<?= $p['id'] ?>" class="btn btn-vite btn-sm px-3">Quick Buy</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>