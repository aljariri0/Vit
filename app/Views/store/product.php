<?php
$images = $product['image_urls'] ?? [];
$price = (float) $product['price'] * (1 - (float) $product['discount_percent'] / 100);
$stock = (int) $product['stock'];
?>
<nav class="product-breadcrumb section-wrap" aria-label="Breadcrumb"><a href="<?= $e($base) ?>">Home</a><span>/</span><a href="<?= $e($base) ?>/shop">Shop</a><?php if (!empty($product['category_name'])): ?><span>/</span><a href="<?= $e($base) ?>/shop?category=<?= (int) $product['category_id'] ?>"><?= $e($product['category_name']) ?></a><?php endif; ?></nav>
<section class="wellness-pdp section-wrap">
  <div class="pdp-gallery">
    <div class="pdp-main-image">
      <?php if (!empty($images)): ?><img id="product-main-image" src="<?= $e($images[0]) ?>" alt="<?= $e($product['name']) ?>" fetchpriority="high"><?php else: ?><div class="detail-orb"><span>✳</span><small><?= $e($product['name']) ?></small></div><?php endif; ?>
      <?php if ((float) $product['discount_percent'] > 0): ?><span class="pdp-sale-badge">Save <?= (int) $product['discount_percent'] ?>%</span><?php endif; ?>
      <?php if (!empty($product['ingredients'])): ?><span class="formula-badge"><span>✓</span> Ingredient details listed</span><?php endif; ?>
    </div>
    <?php if (count($images) > 1): ?><div class="pdp-thumbnails" aria-label="Product images"><?php foreach ($images as $index => $image): ?><button type="button" class="pdp-thumbnail <?= $index === 0 ? 'active' : '' ?>" data-product-image="<?= $e($image) ?>" aria-label="Show product image <?= $index + 1 ?>" aria-pressed="<?= $index === 0 ? 'true' : 'false' ?>"><img src="<?= $e($image) ?>" alt="" loading="lazy"></button><?php endforeach; ?></div><?php endif; ?>
    <div class="pdp-gallery-caption"><span>SHIFA · PRODUCT DETAILS</span><span><?= count($images) ?> <?= count($images) === 1 ? 'image' : 'images' ?></span></div>
  </div>
  <div class="pdp-buy-box">
    <p class="eyebrow"><?= $e($product['category_name'] ?? 'SHIFA ESSENTIALS') ?></p>
    <h1><?= $e($product['name']) ?></h1>
    <div class="pdp-price-row"><strong><?= number_format($price, 2) ?> <small>JOD</small></strong><?php if ((float) $product['discount_percent'] > 0): ?><del><?= number_format((float) $product['price'], 2) ?> JOD</del><?php endif; ?><span class="pdp-stock <?= $stock > 0 ? 'available' : 'unavailable' ?>"><i></i><?= $stock > 0 ? 'In stock' : 'Out of stock' ?></span></div>
    <p class="pdp-description"><?= nl2br($e($product['description'] ?? 'Product details have not been added yet.')) ?></p>
    <div class="pdp-purchase-type"><span class="purchase-dot"></span><span><b>One-time purchase</b><small>Pay once, no recurring orders</small></span><strong><?= number_format($price, 2) ?> JOD</strong></div>
    <?php if ($stock > 0): ?><form action="<?= $e($base) ?>/cart/add" method="post" class="pdp-add-form" data-ajax-add><input type="hidden" name="csrf_token" value="<?= $e($csrf_token) ?>"><input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>"><div class="pdp-form-controls"><label>Quantity<input type="number" name="quantity" value="1" min="1" max="<?= $stock ?>"></label><?php if (!empty($product['sizes'])): ?><label>Size<select name="size"><?php foreach ($product['sizes'] as $size): ?><option value="<?= $e($size) ?>"><?= $e($size) ?></option><?php endforeach; ?></select></label><?php else: ?><input type="hidden" name="size" value=""><?php endif; ?></div><button class="button button-dark pdp-add-button" type="submit"><span data-button-label>Add to bag</span><span aria-hidden="true">↗</span><span class="button-spinner" aria-hidden="true"></span></button></form><?php else: ?><button class="button button-disabled pdp-add-button" type="button" disabled>Currently unavailable</button><?php endif; ?>
    <div class="pdp-delivery-note"><span>⌁</span><p><b>Free delivery over 50 JOD</b><small>Delivery is calculated at checkout based on your address.</small></p></div>
    <div class="pdp-accordions">
      <?php if (!empty($product['ingredients'])): ?><details class="detail-accordion" open><summary>Ingredients & formula <span>＋</span></summary><p><?= nl2br($e($product['ingredients'])) ?></p></details><?php endif; ?>
      <?php if (!empty($product['warnings'])): ?><details class="detail-accordion"><summary>Warnings & product notes <span>＋</span></summary><p><?= nl2br($e($product['warnings'])) ?></p></details><?php endif; ?>
      <details class="detail-accordion"><summary>Product information <span>＋</span></summary><p><?= nl2br($e($product['description'] ?? 'No additional product information has been provided.')) ?></p></details>
    </div>
  </div>
</section>
