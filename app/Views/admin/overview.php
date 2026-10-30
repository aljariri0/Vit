<!-- 1. الـ Cards العلوية (المؤشرات الرئيسية) -->
<div class="row">
    <!-- Total Products -->
    <div class="card-metric">
        <h3>Total products</h3>
        <h2><?= $totalProducts; ?></h2>
        <p>In your wellness collection</p>
    </div>

    <!-- Customers -->
    <div class="card-metric">
        <h3>Customers</h3>
        <h2><?= $totalCustomers; ?></h2>
        <p>Growing your community</p>
    </div>

    <!-- Customer Rating -->
    <div class="card-metric">
        <h3>Customer rating</h3>
        <h2><?= $avgRating; ?> / 5</h2>
        <p>Customer reviews</p>
    </div>

    <!-- Partner Brands -->
    <div class="card-metric">
        <h3>Partner brands</h3>
        <h2><?= $totalBrands; ?></h2>
        <p>Across your catalog</p>
    </div>
</div>


<!-- 2. قسم التنبيهات (Needs a little care / Low Stock) -->
<div class="low-stock-section">
    <h3>Needs a little care</h3>
    <p><strong><?= count($lowStockProducts); ?></strong> low-stock products</p>
    <p>Items with fewer than 10 units in stock.</p>

    <ul>
        <?php foreach ($lowStockProducts as $product): ?>
            <li>
                <span><?= htmlspecialchars($product['name']); ?></span>
                <span class="badge"><?= $product['stock']; ?> left</span>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="inventory.php">Review inventory →</a>
</div>


<!-- 3. جدول أحدث المنتجات (Your product collection) -->
<div class="recent-products-section">
    <h3>Your product collection</h3>
    <table>
        <thead>
            <tr>
                <th>PRODUCT</th>
                <th>PRICE</th>
                <th>STOCK</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentProducts as $prod): ?>
                <tr>
                    <td><?= htmlspecialchars($prod['name']); ?></td>
                    <td><?= htmlspecialchars($prod['price']); ?> JOD</td>
                    <td><?= htmlspecialchars($prod['stock']); ?></td>
                    <td><span class="badge-active">Active</span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<!-- 4. إحصائيات التصنيفات (Collection by category) -->
<div class="category-stats-section">
    <h3>Collection by category</h3>
    <?php foreach ($categoriesStats as $cat): ?>
        <div class="cat-bar">
            <span><?= htmlspecialchars($cat['name']); ?></span>
            <span><?= $cat['product_count']; ?> products</span>
        </div>
    <?php endforeach; ?>
</div>