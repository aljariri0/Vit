<h3 class="fw-bold mb-3">Admin Dashboard</h3>
<div class="row g-3 mb-4 text-center">
    <div class="col-md-3"><div class="card-clean p-3 bg-vite-green text-white"><h6>Total Income</h6><h3 class="fw-bold mb-0">$6,420</h3></div></div>
    <div class="col-md-3"><div class="card-clean p-3"><h6>Orders</h6><h3 class="fw-bold mb-0">84</h3></div></div>
    <div class="col-md-3"><div class="card-clean p-3"><h6>Categories</h6><h3 class="fw-bold mb-0">4</h3></div></div>
    <div class="col-md-3"><div class="card-clean p-3"><h6>Customers</h6><h3 class="fw-bold mb-0">215</h3></div></div>
</div>

<ul class="nav nav-pills mb-3 gap-2">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#adm-prod">Products</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#adm-cat">Categories</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#adm-ord">Orders & Revenue</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#adm-usr">Users & Reviews</button></li>
</ul>

<div class="tab-content card-clean p-4">
    <div class="tab-pane fade show active" id="adm-prod">
        <form class="row g-2 mb-3">
            <div class="col-md-3"><input class="form-control form-control-sm" placeholder="Supplement Name"></div>
            <div class="col-md-2"><input class="form-control form-control-sm" placeholder="Price ($)"></div>
            <div class="col-md-2"><input class="form-control form-control-sm" placeholder="Discount %"></div>
            <div class="col-md-2"><input class="form-control form-control-sm" placeholder="Stock"></div>
            <div class="col-md-3"><button class="btn btn-vite btn-sm w-100">+ Add Supplement</button></div>
        </form>
        <table class="table table-sm align-middle">
            <thead><tr><th>ID</th><th>Supplement</th><th>Brand</th><th>Price</th><th>Stock</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr><td><?= $p['id'] ?></td><td><?= e($p['name']) ?></td><td><?= e($p['brand_name']) ?></td><td>$<?= $p['price'] ?></td><td><?= $p['stock'] ?></td><td><button class="btn btn-sm btn-outline-danger">Delete</button></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="tab-pane fade" id="adm-cat">
        <table class="table table-sm">
            <thead><tr><th>ID</th><th>Category</th><th>Description</th></tr></thead>
            <tbody>
                <?php foreach ($categories as $c): ?>
                <tr><td><?= $c['id'] ?></td><td><?= e($c['name']) ?></td><td><?= e($c['description']) ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="tab-pane fade" id="adm-ord">
        <h6 class="fw-bold">Daily Revenue (`v_daily_revenue`)</h6>
        <table class="table table-sm mb-3">
            <thead><tr><th>Date</th><th>Orders</th><th>Revenue</th></tr></thead>
            <tbody><tr><td>2026-10-28</td><td>18</td><td class="text-vite-green fw-bold">$980.00</td></tr></tbody>
        </table>
    </div>

    <div class="tab-pane fade" id="adm-usr">
        <table class="table table-sm">
            <thead><tr><th>User</th><th>Email</th><th>Comment Status</th><th>Action</th></tr></thead>
            <tbody><tr><td>Omar A.</td><td>omar@example.com</td><td>Allowed</td><td><button class="btn btn-sm btn-outline-warning">Block Comments</button></td></tr></tbody>
        </table>
    </div>
</div>
