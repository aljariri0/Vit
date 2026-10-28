<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitemines | Vitamins, Proteins & Sports Nutrition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --vite-green: #15803d;      /* iHerb / Protine natural green */
            --vite-dark: #0f291e;       /* Deep botanical dark green */
            --vite-cream: #f6f8f5;      /* Soft clean wellness background */
            --vite-accent: #dcfce7;     /* Soft pill badge green */
        }
        body { background-color: var(--vite-cream); color: #1f2937; font-family: 'Segoe UI', system-ui, sans-serif; }
        .bg-vite-dark { background-color: var(--vite-dark); }
        .bg-vite-green { background-color: var(--vite-green); }
        .text-vite-green { color: var(--vite-green); }
        .btn-vite { background-color: var(--vite-green); color: #fff; border-radius: 50px; font-weight: 600; }
        .btn-vite:hover { background-color: #166534; color: #fff; }
        .card-clean { border: 1px solid #e5e7eb; border-radius: 16px; transition: transform .2s ease, box-shadow .2s ease; background: #fff; }
        .card-clean:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,0.06); }
        .badge-discount { background-color: #dc2626; color: #fff; border-radius: 8px; font-weight: 600; }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

<!-- iHerb & Myvitamins Style Top Promo Bar -->
<div class="bg-vite-dark text-white text-center py-2 small">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
        <span><i class="fa-solid fa-shield-heart text-success me-1"></i> 100% Authentic Lab-Tested Supplements</span>
        <span>🔥 <strong>20% OFF</strong> First Protein & Vitamin Order — Use Code: <strong class="text-warning">VITE20</strong></span>
        <span class="d-none d-md-inline">Free Delivery on Orders Over $40</span>
    </div>
</div>

<!-- Clean Sticky Navbar -->
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 text-vite-green" href="?page=home">
            <i class="fa-solid fa-leaf me-1"></i>VITEMINES
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                <li class="nav-item"><a class="nav-link fw-semibold" href="?page=home">Home</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="?page=shop">Shop All</a></li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-vite-green" href="?page=assistant">
                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i>AI Wellness Coach
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link text-muted" href="?page=admin">Admin</a></li>
            </ul>

            <!-- iHerb-style Search Bar -->
            <form action="" method="GET" class="d-flex me-3 my-2 my-lg-0" style="min-width: 260px;">
                <input type="hidden" name="page" value="shop">
                <div class="input-group input-group-sm">
                    <input type="search" name="q" class="form-control rounded-start-pill ps-3" placeholder="Search whey, creatine, D3...">
                    <button class="btn btn-vite rounded-end-pill px-3" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>

            <!-- Action Icons -->
            <div class="d-flex align-items-center gap-2">
                <a href="?page=cart" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-bag-shopping me-1"></i>Cart (1)
                </a>
                <a href="?page=profile" class="btn btn-outline-secondary btn-sm rounded-circle" title="My Profile & Orders">
                    <i class="fa-regular fa-user"></i>
                </a>
                <a href="?page=auth" class="btn btn-vite btn-sm px-3">Login</a>
            </div>
        </div>
    </div>
</nav>

<!-- Main View Content -->
<main class="container my-4 flex-grow-1">
    <?= $content ?>
</main>

<!-- iHerb Trust Footer -->
<footer class="bg-white border-top pt-5 pb-4 mt-5 text-muted small">
    <div class="container">
        <div class="row g-4 pb-4 border-bottom text-center text-md-start">
            <div class="col-md-3">
                <h6 class="fw-bold text-dark"><i class="fa-solid fa-certificate text-vite-green me-2"></i>Authentic Products</h6>
                <p class="mb-0">100% genuine brands sourced directly from certified manufacturers.</p>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold text-dark"><i class="fa-solid fa-flask-vial text-vite-green me-2"></i>Lab-Tested Purity</h6>
                <p class="mb-0">Every batch is verified for potency, macros, and heavy-metal safety.</p>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold text-dark"><i class="fa-solid fa-temperature-arrow-down text-vite-green me-2"></i>Climate-Controlled</h6>
                <p class="mb-0">Stored in cool, humidity-controlled warehouses to preserve freshness.</p>
            </div>
            <div class="col-md-3">
                <h6 class="fw-bold text-dark"><i class="fa-solid fa-robot text-vite-green me-2"></i>AI Dosage & Size Match</h6>
                <p class="mb-0">Personalized protein and vitamin recommendations for your body weight.</p>
            </div>
        </div>
        <div class="d-flex flex-wrap justify-content-between align-items-center pt-3">
            <span>&copy; <?= date('Y') ?> Vitemines Wellness & Sports Nutrition.</span>
            <span>GMP Certified • ISO Quality • Cash on Delivery / Visa / PayPal</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>