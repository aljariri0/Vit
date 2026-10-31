<?php
$intentions = [
    'mineral' => ['Daily balance', 'Minerals such as calcium, magnesium and zinc support essential functions including bones, muscles and nerves.', '✳'],
    'multivitamin' => ['Everyday coverage', 'A blend of vitamins and minerals can help fill nutrition gaps when your diet needs a little support.', '◌'],
    'vitamin' => ['Targeted nutrients', 'Explore individual vitamins for specific nutrition needs, such as vitamin D for calcium absorption.', '☼'],
    'omega' => ['Heart & mind', 'Omega-3 fats are essential fats that support normal heart and brain function as part of a balanced diet.', '♡'],
    'fish oil' => ['Heart & mind', 'Fish oil is a source of omega-3 fats, which support normal heart and brain function.', '♡'],
    'probiotic' => ['Gut routine', 'Probiotic products contain live microorganisms and may complement a balanced diet and healthy routine.', '⌁'],
    'specialty' => ['Focused support', 'Discover selected ingredients for your routine and check the label to see whether they fit your needs.', '✦'],
];
$intentFor = static function (string $name) use ($intentions): array {
    $name = strtolower($name);
    foreach ($intentions as $key => $details) {
        if (str_contains($name, $key)) return $details;
    }
    return ['A thoughtful daily ritual', 'Explore carefully selected nutrients and ingredients to find what fits your everyday routine.', '✧'];
};
?>
<section class="home-hero">
  <div class="hero-sky"></div>
  <div class="hero-topline"><span>SHIFA / WELLNESS, YOUR WAY</span><span>Thoughtful essentials · Everyday care</span></div>
  <div class="hero-products" aria-label="Featured Shifa products">
    <?php foreach (array_slice($best_sellers, 0, 3) as $i => $product): ?><a class="hero-product hero-product-<?= $i + 1 ?>" href="<?= $e($base) ?>/product?id=<?= (int) $product['id'] ?>" aria-label="Discover <?= $e($product['name']) ?>"><?php if (!empty($product['image_url'])): ?><img src="<?= $e($product['image_url']) ?>" alt="<?= $e($product['name']) ?>"><?php else: ?><span>SHIFA<br>DAILY CARE</span><?php endif; ?></a><?php endforeach; ?>
  </div>
  <div class="hero-bottom"><div><p class="eyebrow">A BETTER DAY STARTS SMALL</p><h1>Bringing health<br>to your <em>doorstep.</em></h1><p class="hero-description">Find thoughtful vitamins and everyday essentials to support the routines that make you feel your best.</p></div><a class="button button-dark" href="<?= $e($base) ?>/shop">Explore Shifa <span>↗</span></a></div>
  <span class="hero-index">01 <i></i> 03</span>
</section>
<section class="home-intentions section-wrap" id="intentions">
  <div class="home-section-heading"><div><p class="eyebrow">FIND WHAT FITS YOUR DAY</p><h2>Care for your<br><em>kind of everyday.</em></h2></div><p>Start with what matters to you. Explore the purpose behind each range, then find the essentials that fit your routine.</p></div>
  <div class="intention-cards"><?php foreach ($categories as $i => $category): [$intentTitle, $intentCopy, $intentIcon] = $intentFor((string) $category['name']); ?><a class="intention-card intention-card-<?= $i % 4 ?>" href="<?= $e($base) ?>/shop?category=<?= (int) $category['id'] ?>"><span class="intention-icon"><?= $intentIcon ?></span><h3><?= $e($category['name']) ?></h3><p><?= $e($intentCopy) ?></p><span class="intention-foot"><b><?= $e($intentTitle) ?></b><i>↗</i></span></a><?php endforeach; ?><?php if (!$categories): ?><a class="intention-card intention-card-0" href="<?= $e($base) ?>/shop"><span class="intention-icon">✦</span><h3>Everyday essentials</h3><p>Explore thoughtful nutrition for your everyday routine.</p><span class="intention-foot"><b>Browse the shop</b><i>↗</i></span></a><?php endif; ?></div>
  <p class="home-note">Supplements complement a varied diet and healthy lifestyle. Check product guidance and speak with a healthcare professional if you have questions.</p>
</section>
<section class="home-best-sellers"><div class="section-wrap"><div class="home-section-heading"><div><p class="eyebrow"><?= !empty($has_sales_data) ? 'THE SHIFA COMMUNITY LOVES' : 'A GOOD PLACE TO START' ?></p><h2>Little things.<br><em>Big difference.</em></h2></div><a class="text-link" href="<?= $e($base) ?>/shop">Shop all products <span>↗</span></a></div><div class="home-product-grid">
  <?php foreach ($best_sellers as $rank => $product): $productPrice = (float) $product['price'] * (1 - (float) ($product['discount_percent'] ?? 0) / 100); $productDescription = trim(strip_tags((string) ($product['description'] ?? ''))); ?>
    <article class="home-product-card"><div class="home-product-copy"><p class="eyebrow"><?= !empty($has_sales_data) ? 'BESTSELLER · ' . number_format((int) ($product['units_sold'] ?? 0)) . ' SOLD' : 'SHIFA PICK' ?></p><h3><a href="<?= $e($base) ?>/product?id=<?= (int) $product['id'] ?>"><?= $e($product['name']) ?></a></h3><p><?= $e($productDescription !== '' ? $productDescription : 'A considered essential for your everyday routine.') ?></p><strong><?= number_format($productPrice, 2) ?> <small>JOD</small></strong><a href="<?= $e($base) ?>/product?id=<?= (int) $product['id'] ?>" class="home-product-link">Discover <span>↗</span></a></div><a class="home-product-image" href="<?= $e($base) ?>/product?id=<?= (int) $product['id'] ?>" aria-label="View <?= $e($product['name']) ?>"><?php if (!empty($product['image_url'])): ?><img src="<?= $e($product['image_url']) ?>" alt="<?= $e($product['name']) ?>" loading="lazy"><?php else: ?><span>SHIFA<br>DAILY CARE</span><?php endif; ?></a></article>
  <?php endforeach; ?><?php if (!$best_sellers): ?><div class="empty-state"><h3>Your next ritual starts here</h3><p>Products will appear as soon as they’re added to the collection.</p><a class="button button-dark" href="<?= $e($base) ?>/shop">Explore the shop</a></div><?php endif; ?>
  </div><p class="home-sales-note"><?= !empty($has_sales_data) ? 'Ranked by units sold across paid, processing, shipped and delivered orders.' : 'Bestsellers will be ranked here once orders are placed.' ?></p></div></section>
<section class="home-story" id="story"><div class="story-image"><span>SHIFA</span><i>✳</i></div><div><p class="eyebrow">SMALL RITUALS, BETTER DAYS</p><h2>Wellness that<br>fits <em>real life.</em></h2><p>We believe feeling good starts with small things that fit into your day. A little more care, one simple ritual at a time.</p><a class="button button-dark" href="<?= $e($base) ?>/shop">Find your ritual <span>↗</span></a></div></section>
