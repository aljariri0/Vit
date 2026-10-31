<?php
$productImage = (string) ($product['image_url'] ?? '');
$productHref = $base . '/product?id=' . (int) $product['id'];
$productPrice = (float) $product['price'] * (1 - (float) ($product['discount_percent'] ?? 0) / 100);
?>
<article class="product-card tilt-card">
  <a class="product-visual" href="<?= $e($productHref) ?>">
    <?php if ($productImage !== ''): ?><img src="<?= $e($productImage) ?>" alt="<?= $e($product['name']) ?>" loading="lazy"><?php else: ?><span class="product-orb orb-<?= (int) $product['id'] % 5 ?>"><i>✳</i></span><?php endif; ?>
    <?php if ((float) ($product['discount_percent'] ?? 0) > 0): ?><span class="product-tag">−<?= (int) $product['discount_percent'] ?>%</span><?php endif; ?>
    <span class="product-arrow">↗</span>
  </a>
  <div class="product-copy"><div><p class="eyebrow">DAILY RITUAL · SHIFA SELECT</p><h3><a href="<?= $e($productHref) ?>"><?= $e($product['name']) ?></a></h3></div><strong><?= number_format($productPrice, 2) ?> <small>JOD</small></strong></div>
  <p class="product-desc"><?= $e(substr(strip_tags((string) ($product['description'] ?? 'A considered essential for your everyday routine.')), 0, 76)) ?><?= strlen(strip_tags((string) ($product['description'] ?? 'A considered essential for your everyday routine.'))) > 76 ? '…' : '' ?></p>
  <form action="<?= $e($base) ?>/cart/add" method="post" class="quick-add"><input type="hidden" name="csrf_token" value="<?= $e($csrf_token) ?>"><input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>"><input type="hidden" name="quantity" value="1"><?php if (!empty($product['sizes'])): ?><select name="size" aria-label="Choose size"><?php foreach ($product['sizes'] as $size): ?><option value="<?= $e($size) ?>"><?= $e($size) ?></option><?php endforeach; ?></select><?php else: ?><input type="hidden" name="size" value=""><?php endif; ?><button type="submit">Add to bag <span>+</span></button></form>
</article>
