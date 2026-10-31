<?php
$intentionCopy = [
    'mineral' => ['Mineral balance', 'Minerals such as magnesium, zinc and calcium support everyday functions including muscle, nerve and bone health.'],
    'multivitamin' => ['Everyday coverage', 'A multivitamin can help fill nutrition gaps when a varied diet does not cover all your daily needs.'],
    'vitamin' => ['Targeted nutrients', 'Choose a vitamin around your routine and needs; vitamin D, for example, helps the body absorb calcium.'],
    'omega' => ['Heart & mind', 'Omega-3 fats are essential fats that support normal heart and brain function as part of a balanced diet.'],
    'fish oil' => ['Heart & mind', 'Fish oil is a source of omega-3 fats, which support normal heart and brain function as part of a balanced diet.'],
    'probiotic' => ['Gut routine', 'Probiotic products contain live microorganisms and may support a healthy gut alongside a balanced diet.'],
    'specialty' => ['Focused support', 'Explore ingredients for specific wellness routines, and check the label to see whether a product suits your needs.'],
];
$categoryInfo = static function (string $name) use ($intentionCopy): array {
    $key = strtolower($name);
    foreach ($intentionCopy as $match => $copy) {
        if (str_contains($key, $match)) return $copy;
    }
    return ['A thoughtful daily ritual', 'Explore carefully selected nutrients and ingredients to find what fits your everyday routine.'];
};
?>
<section class="hero-section hero-immersive">
  <div class="hero-copy">
    <p class="eyebrow"><span class="eyebrow-line"></span> YOUR WELLNESS, IN YOUR OWN ELEMENT</p>
    <h1>Make room<br>for feeling <em>good.</em></h1>
    <p class="hero-description">Small, thoughtful rituals. Trusted everyday essentials. A little more care, made to fit the life you already live.</p>
    <div class="hero-actions"><a href="<?= $e($base) ?>/shop" class="button button-dark">Find your daily ritual <span>↗</span></a><a class="text-link" href="#intentions">Explore by intention <span>↓</span></a></div>
    <div class="hero-proof"><div class="proof-avatars"><i>✳</i><i>♡</i><i>☼</i></div><span>Wellness that feels like you<br><b>one good day at a time</b></span></div>
  </div>
  <div class="hero-art hero-art-new" aria-label="Shifa wellness essentials">
    <div class="hero-glow"></div><div class="hero-ribbon ribbon-a"></div><div class="hero-ribbon ribbon-b"></div>
    <div class="hero-spark spark-a">✦</div><div class="hero-spark spark-b">✳</div><div class="hero-spark spark-c">✧</div>
    <div class="hero-pedestal"></div>
    <div class="hero-bottle bottle-back"><span>SHIFA<br><small>daily glow</small></span></div>
    <div class="hero-bottle bottle-front"><span>SHIFA<br><small>feel good daily</small></span></div>
    <div class="hero-stamp"><span>THE<br>DAILY<br>EDIT</span><i>✳</i></div>
    <div class="floating-note">a little care<br><b>goes a long way</b><span>↗</span></div>
  </div>
</section>
<section class="trust-row"><div><span>✳</span><b>Mindfully selected</b><small>Thoughtful everyday essentials</small></div><div><span>⌁</span><b>Simple routines</b><small>Find what works for your day</small></div><div><span>♡</span><b>Care you can trust</b><small>Here for your wellbeing</small></div><div><span>◌</span><b>Delivered with care</b><small>Wellness, at your doorstep</small></div></section>
<section id="intentions" class="section-wrap category-section intention-section"><div class="section-heading"><div><p class="eyebrow">A GOOD PLACE TO BEGIN</p><h2>Shop by <em>intention</em></h2></div><a class="text-link" href="<?= $e($base) ?>/shop">Browse everything ↗</a></div><p class="intention-intro">Explore what each range can bring to your routine. The right choice depends on your diet, health and everyday needs.</p><div class="category-grid intention-grid"><?php foreach ($categories as $i => $category): [$intentTitle, $intentBenefit] = $categoryInfo((string) $category['name']); ?><a class="category-tile category-<?= $i % 4 ?>" href="<?= $e($base) ?>/shop?category=<?= (int) $category['id'] ?>"><span class="category-index">0<?= $i + 1 ?> / SHIFA GUIDE</span><span class="category-icon"><?= ['◌','✦','☼','✳'][$i % 4] ?></span><h3><?= $e($category['name']) ?></h3><p><?= $e($intentBenefit) ?></p><span class="category-link"><?= $e($intentTitle) ?> <b>↗</b></span></a><?php endforeach; ?><?php if (!$categories): ?><a class="category-tile category-0" href="<?= $e($base) ?>/shop"><span class="category-icon">✦</span><h3>Everyday essentials</h3><p>Explore thoughtfully selected nutrition for your everyday routine.</p><span class="category-link">Browse the shop ↗</span></a><?php endif; ?></div><p class="wellness-note">Supplements are not a substitute for a varied diet. Check product guidance and consult a healthcare professional when needed.</p></section>
<section class="featured-section bestseller-section"><div class="section-wrap"><div class="section-heading"><div><p class="eyebrow"><?= !empty($has_sales_data) ? 'LOVED BY THE SHIFA COMMUNITY' : 'A PLACE TO START' ?></p><h2>Little things, <em>big difference.</em></h2></div><a class="text-link" href="<?= $e($base) ?>/shop">Discover all <span>↗</span></a></div><p class="bestseller-intro"><?= !empty($has_sales_data) ? 'The three most purchased essentials, picked from completed and in progress orders.' : 'Explore three picks from our current collection; popular picks will appear here as orders come in.' ?></p>
  <div class="bestseller-list"><?php foreach ($best_sellers as $rank => $product): $productPrice = (float) $product['price'] * (1 - (float) ($product['discount_percent'] ?? 0) / 100); $productHref = $base . '/product?id=' . (int) $product['id']; $productDescription = trim(strip_tags((string) ($product['description'] ?? ''))); ?>
    <article class="bestseller-row"><div class="bestseller-copy"><p class="eyebrow">0<?= $rank + 1 ?> · <?= !empty($has_sales_data) ? number_format((int) ($product['units_sold'] ?? 0)) . ' SOLD' : 'SHIFA PICK' ?></p><h3><a href="<?= $e($productHref) ?>"><?= $e($product['name']) ?></a></h3><p><?= $e($productDescription !== '' ? $productDescription : 'A considered essential to bring a little more care to your everyday routine.') ?></p><div class="bestseller-actions"><strong><?= number_format($productPrice, 2) ?> <small>JOD</small></strong><a class="button button-dark" href="<?= $e($productHref) ?>">Explore product <span>↗</span></a></div></div><a class="bestseller-image" href="<?= $e($productHref) ?>" aria-label="View <?= $e($product['name']) ?>"><?php if (!empty($product['image_url'])): ?><img src="<?= $e($product['image_url']) ?>" alt="<?= $e($product['name']) ?>" loading="lazy"><?php else: ?><span class="product-orb orb-<?= (int) $product['id'] % 5 ?>"><i>✳</i></span><?php endif; ?><span class="bestseller-image-mark">SHIFA · EVERYDAY CARE</span></a></article>
  <?php endforeach; ?><?php if (!$best_sellers): ?><div class="empty-state"><span>✳</span><h3>Your next ritual starts here</h3><p>Products will appear as soon as they’re added to the collection.</p><a class="button button-dark" href="<?= $e($base) ?>/shop">Explore the shop</a></div><?php endif; ?></div>
</div></section>
<section id="story" class="story-section"><div class="story-shape"><span>SHIFA<br>✳</span><i>✧</i></div><div><p class="eyebrow">A LITTLE MORE YOU</p><h2>Wellbeing isn't<br>a finish line. <em>It's a feeling.</em></h2><p>We believe feeling good starts with small things that fit into your real life. A slower morning. A little more care. A ritual that feels like yours.</p><a href="<?= $e($base) ?>/shop" class="text-link">Find your ritual <span>↗</span></a></div></section>
