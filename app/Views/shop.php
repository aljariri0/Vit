<h3 class="fw-bold mb-3">All Vitamins & Proteins</h3>

<!-- Filter Bar by Name, Goal/Category, and Price -->
<form action="" method="GET" class="card-clean p-3 mb-4">
    <input type="hidden" name="page" value="shop">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Supplement or Brand</label>
            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Whey Isolate, Vitamin D3, Creatine...">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold">Health Goal / Category</label>
            <select name="category_id" class="form-select form-select-sm">
                <option value="">All Categories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-semibold">Max Price ($)</label>
            <input type="number" name="max_price" class="form-control form-control-sm" placeholder="100">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-vite btn-sm w-100 py-2">Apply Filters</button>
        </div>
    </div>
</form>

<div class="row g-4">
    <?php foreach ($products as $p): ?>
        <div class="col-sm-6 col-lg-3">
            <div class="card-clean h-100 p-3 d-flex flex-column">
                <img src="<?= e($p['image']) ?>" class="img-fluid rounded-3 mb-3" style="height: 180px; object-fit: cover;">
                <small class="text-muted fw-semibold"><?= e($p['brand_name']) ?></small>
                <h6 class="fw-bold mt-1"><?= e($p['name']) ?></h6>
                <div class="small text-warning mb-2">⭐ <?= $p['rating_avg'] ?> (<?= number_format($p['rating_count']) ?>)</div>
                <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                    <span class="fw-bold fs-5 text-vite-green">$<?= final_price($p['price'], $p['discount_percent']) ?></span>
                    <a href="?page=product&id=<?= $p['id'] ?>" class="btn btn-vite btn-sm">View Details</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>