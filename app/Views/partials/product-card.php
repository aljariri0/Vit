<?php
$productImage = (string) ($product['image_url'] ?? '');
$productHref = $base . '/product?id=' . (int) $product['id'];
$productPrice = (float) $product['price'] * (1 - (float) ($product['discount_percent'] ?? 0) / 100);
$ingredientText = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($product['ingredients'] ?? ''))));
$sizeLabel = (string) ($product['sizes'][0] ?? '');
$truncate = static fn (string $value, int $width): string => function_exists('mb_strimwidth')
    ? mb_strimwidth($value, 0, $width, '…', 'UTF-8')
    : (strlen($value) > $width ? substr($value, 0, $width - 3) . '...' : $value);
?>
<article class="product-card wellness-product-card">
  <a class="product-visual" href="<?= $e($productHref) ?>" aria-label="View <?= $e($product['name']) ?>">
    <?php if ($productImage !== ''): ?><img src="<?= $e($productImage) ?>" alt="<?= $e($product['name']) ?>" loading="lazy"><?php else: ?><span class="product-orb orb-<?= (int) $product['id'] % 5 ?>" aria-hidden="true"><i>✳</i></span><?php endif; ?>
    <?php if ((float) ($product['discount_percent'] ?? 0) > 0): ?><span class="product-tag">Save <?= (int) $product['discount_percent'] ?>%</span><?php endif; ?>
    <?php if (!empty($product['category_name'])): ?><span class="product-category-tag"><?= $e($product['category_name']) ?></span><?php endif; ?>
  </a>
  <div class="product-copy"><div><p class="eyebrow"><?= $e($product['category_name'] ?? 'Shifa essentials') ?></p><h3><a href="<?= $e($productHref) ?>"><?= $e($product['name']) ?></a></h3></div><strong><?= number_format($productPrice, 2) ?> <small>JOD</small></strong></div>
  <?php if ($ingredientText !== ''): ?><p class="product-ingredient"><?= $e($truncate($ingredientText, 78)) ?></p><?php else: ?><p class="product-desc"><?= $e($truncate(trim(strip_tags((string) ($product['description'] ?? 'A considered everyday essential.'))), 95)) ?></p><?php endif; ?>
  <div class="product-card-meta"><?php if ($sizeLabel !== ''): ?><span><?= $e($sizeLabel) ?></span><?php endif; ?><?php if ((int) $product['stock'] > 0): ?><span class="stock-live">In stock</span><?php else: ?><span class="stock-out">Out of stock</span><?php endif; ?></div>
  <form action="<?= $e($base) ?>/cart/add" method="post" class="quick-add" data-ajax-add>
    <input type="hidden" name="csrf_token" value="<?= $e($csrf_token) ?>"><input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>"><input type="hidden" name="quantity" value="1">
    <?php if (!empty($product['sizes'])): ?><select name="size" aria-label="Choose size for <?= $e($product['name']) ?>"><?php foreach ($product['sizes'] as $size): ?><option value="<?= $e($size) ?>"><?= $e($size) ?></option><?php endforeach; ?></select><?php else: ?><input type="hidden" name="size" value=""><?php endif; ?>
    <button type="submit" <?= (int) $product['stock'] < 1 ? 'disabled' : '' ?>><span data-button-label><?= (int) $product['stock'] > 0 ? 'Add to bag' : 'Unavailable' ?></span><span class="add-icon" aria-hidden="true">+</span><span class="button-spinner" aria-hidden="true"></span></button>
  </form>
</article>
