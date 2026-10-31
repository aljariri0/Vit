<?php
$omegaImage = $base . '/assets/products/Solgar/Omega%20and%20Fish%20Oil/Solgar-Triple-Strength-Omega-3-950mg.png';
$vitaminImage = $base . '/assets/products/Solgar/Vitamins/Solgar-Vitamin-B12-1000mcg.png';
$omegaLink = $base . '/shop?q=omega';
?>
<div class="home-sections">
  <section class="home-panel home-hero" aria-labelledby="home-title">
    <div class="home-panel-inner">
      <div class="home-copy"><p class="eyebrow">SHIFA / EVERYDAY WELLNESS</p><h1 id="home-title">A little care.<br><span>A better<br>everyday.</span></h1><p>Discover your next ritual.<br>Make it part of your day.</p><a class="pill-link" href="<?= $e($omegaLink) ?>">Explore omega-3 <span aria-hidden="true">↗</span></a><div class="home-caption">SMALL RITUALS. EVERYDAY WELLNESS.</div></div>
      <div class="home-visual"><div class="home-halo" aria-hidden="true"></div><img src="<?= $e($omegaImage) ?>" alt="Solgar Triple Strength Omega-3 950 mg" fetchpriority="high"><span class="softgel home-gel-one" aria-hidden="true"></span><span class="softgel home-gel-two" aria-hidden="true"></span><span class="home-image-label">THE OMEGA COLLECTION</span></div>
    </div>
  </section>
  <section class="home-panel home-routine" id="story" aria-labelledby="routine-title">
    <div class="home-panel-inner">
      <div class="home-routine-art" aria-hidden="true"><div class="home-rings"><span class="softgel"></span></div><span class="home-art-note">A LITTLE EVERYDAY CARE</span></div>
      <div class="home-copy"><p class="eyebrow">01 / MAKE IT YOURS</p><h2 id="routine-title">Your routine.<br>Your own pace.</h2><p>Find the essentials that fit your life.<br>Start with a little everyday care.</p><a class="pill-link" href="<?= $e($base) ?>/shop">Find your essentials <span aria-hidden="true">↗</span></a><div class="shopping-guide"><details><summary>How can I find a product?</summary><p>Search by product or nutrient, browse a category, or choose a brand from our catalogue.</p></details><details><summary>Where can I check product details?</summary><p>Open an available product to see its description, ingredients, label warnings and stock information. Always follow the product label.</p></details><details><summary>Where are my orders?</summary><p>Sign in to your account to view your order history and current order status.</p><a href="<?= $e($base) ?>/account/orders">View your orders →</a></details></div></div>
    </div>
  </section>
  <section class="home-panel home-vitamins" aria-labelledby="vitamins-title">
    <div class="home-panel-inner">
      <div class="home-copy"><p class="eyebrow">02 / A CLOSER LOOK</p><h2 id="vitamins-title">Little things.<br>Daily essentials.</h2><p>Explore vitamins, minerals and more.<br>Discover the next addition to your routine.</p><a class="pill-link" href="<?= $e($base) ?>/shop?q=vitamin">Explore vitamins <span aria-hidden="true">↗</span></a><div class="nutrient-links" aria-label="Shop by nutrient"><?php foreach (['Vitamin D', 'Vitamin C', 'Vitamin B12', 'Magnesium', 'Zinc'] as $nutrient): ?><a href="<?= $e($base) ?>/shop?q=<?= $e(rawurlencode($nutrient)) ?>"><?= $e($nutrient) ?> <span aria-hidden="true">↗</span></a><?php endforeach; ?></div></div>
      <div class="home-visual"><img src="<?= $e($vitaminImage) ?>" alt="Solgar Vitamin B12" loading="lazy"><span class="home-image-label">YOUR EVERYDAY ESSENTIALS</span></div>
    </div>
  </section>
  <section class="home-panel home-care" id="shop-by-brand" aria-labelledby="brands-title">
    <div class="home-panel-inner">
      <div class="home-visual"><img src="<?= $e($omegaImage) ?>" alt="Solgar Omega-3 supplement" loading="lazy"><span class="home-image-label">EXPLORE THE BRANDS IN OUR CATALOGUE</span></div>
      <div class="home-copy"><p class="eyebrow">03 / FIND YOUR FAVOURITES</p><h2 id="brands-title">Your brands.<br>One destination.</h2><p>Explore our catalogue by brand.<br>A simpler way to find your everyday essentials.</p><div class="brand-directory"><?php foreach ($brands as $brand): ?><a href="<?= $e($base) ?>/shop?brand=<?= (int) $brand['id'] ?>"><span><?= $e($brand['name']) ?></span><span aria-hidden="true">↗</span></a><?php endforeach; ?></div><a class="text-link" href="<?= $e($base) ?>/shop">Browse the full collection →</a></div>
    </div>
  </section>
  <section id="shop-by-need" class="home-panel home-categories" aria-labelledby="categories-title">
    <div class="home-wide-inner"><div class="section-heading"><div><p class="eyebrow">04 / FIND YOUR EVERYDAY</p><h2 id="categories-title">Feel-good finds.<br>All in one place.</h2></div><a class="text-link" href="<?= $e($base) ?>/shop">View all products →</a></div>
      <div class="needs-grid"><?php foreach ($categories as $i => $category): ?><a class="need-card need-<?= $i % 4 ?>" href="<?= $e($base) ?>/shop?category=<?= (int) $category['id'] ?>"><span class="need-symbol" aria-hidden="true"><?= ['☼','✳','♡','✧'][$i % 4] ?></span><h3><?= $e($category['name']) ?></h3><span>EXPLORE ↗</span></a><?php endforeach; ?><?php if (!$categories): ?><a class="need-card" href="<?= $e($base) ?>/shop"><h3>Everyday essentials</h3><span>EXPLORE ↗</span></a><?php endif; ?></div>
    </div>
  </section>
  <section id="featured" class="home-panel home-products" aria-labelledby="products-title"><div class="home-wide-inner"><div class="section-heading"><div><p class="eyebrow">05 / THE LATEST FROM SHIFA</p><h2 id="products-title">Meet your daily essentials</h2></div><a class="text-link" href="<?= $e($base) ?>/shop">Shop all →</a></div><div class="product-grid"><?php foreach ($products as $product): require __DIR__ . '/../partials/product-card.php'; endforeach; ?></div><?php if (!$products): ?><div class="empty-state"><span aria-hidden="true">✳</span><h3>A little goodness is on its way.</h3><p>Our collection is being prepared. Available products will appear here soon.</p><a class="button button-dark" href="<?= $e($base) ?>/shop">Browse categories →</a></div><?php endif; ?></div></section>
</div>
