<?php
$escape = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin overview | Vitemines</title>
    <style>
        body { margin: 0; padding: 2rem; background: #f5f7f5; color: #17251d; font: 16px system-ui, sans-serif; }
        main { max-width: 1100px; margin: auto; }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; }
        .card, section { background: #fff; border-radius: 10px; padding: 1rem; margin: 1rem 0; }
        table { width: 100%; border-collapse: collapse; } th, td { padding: .65rem; border-bottom: 1px solid #e5e7eb; text-align: left; }
        nav { display: flex; gap: 1rem; margin: 1rem 0; }
    </style>
</head>
<body>
<main>
    <h1>Store overview</h1>
    <nav>
        <a href="<?= $escape($basePath) ?>/admin/payments">Payments</a>
        <a href="<?= $escape($basePath) ?>/admin/reviews">Review moderation</a>
    </nav>
    <div class="cards">
        <div class="card">Products: <strong><?= $escape($totalProducts) ?></strong></div>
        <div class="card">Customers: <strong><?= $escape($totalCustomers) ?></strong></div>
        <div class="card">Average rating: <strong><?= $escape($avgRating) ?></strong></div>
        <div class="card">Brands: <strong><?= $escape($totalBrands) ?></strong></div>
    </div>
    <section>
        <h2>Low stock</h2>
        <table><thead><tr><th>Product</th><th>Units</th></tr></thead><tbody>
        <?php foreach ($lowStockProducts as $product): ?>
            <tr><td><?= $escape($product['name']) ?></td><td><?= $escape($product['stock']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (!$lowStockProducts): ?><tr><td colspan="2">No low stock products.</td></tr><?php endif; ?>
        </tbody></table>
    </section>
    <section>
        <h2>Recent products</h2>
        <table><thead><tr><th>Name</th><th>Price</th><th>Stock</th></tr></thead><tbody>
        <?php foreach ($recentProducts as $product): ?>
            <tr><td><?= $escape($product['name']) ?></td><td><?= $escape(number_format((float) $product['price'], 2)) ?> JOD</td><td><?= $escape($product['stock']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (!$recentProducts): ?><tr><td colspan="3">No products yet.</td></tr><?php endif; ?>
        </tbody></table>
    </section>
    <section>
        <h2>Products by category</h2>
        <ul><?php foreach ($categoriesStats as $category): ?>
            <li><?= $escape($category['name']) ?>: <?= $escape($category['product_count']) ?></li>
        <?php endforeach; ?></ul>
    </section>
</main>
</body>
</html>
