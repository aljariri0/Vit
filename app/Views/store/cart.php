<section class="section-wrap cart-page">
    <div class="shop-heading"><p class="eyebrow">A FEW GOOD THINGS</p><h1>Your bag<span class="brand-dot">.</span></h1></div>
    <?php if (!$items): ?>
        <div class="empty-state"><span>◌</span><h2>Your bag is taking a quiet moment.</h2><p>Explore the collection and find something that feels right.</p><a class="button button-dark" href="<?= $e($base) ?>/shop">Explore the shop <span>View</span></a></div>
    <?php else: ?>
        <div class="cart-layout">
            <div class="cart-list">
                <?php foreach ($items as $item): $p = $item['product']; ?>
                    <article class="cart-item">
                        <a class="cart-thumb" href="<?= $e($base) ?>/product?id=<?= (int) $p['id'] ?>">
                            <?php if (!empty($p['image_url'])): ?><img src="<?= $e($p['image_url']) ?>" alt="<?= $e($p['name']) ?>" loading="lazy"><?php else: ?><span>✳</span><?php endif; ?>
                        </a>
                        <div class="cart-item-info">
                            <a href="<?= $e($base) ?>/product?id=<?= (int) $p['id'] ?>"><b><?= $e($p['name']) ?></b></a>
                            <small><?= $item['size'] ? 'Size ' . $e($item['size']) : 'Everyday size' ?></small>
                            <div class="cart-item-actions">
                                <form action="<?= $e($base) ?>/cart/update" method="post" data-cart-quantity-update>
                                    <input type="hidden" name="csrf_token" value="<?= $e($csrf_token) ?>"><input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>"><input type="hidden" name="size" value="<?= $e($item['size']) ?>">
                                    <div class="quantity-counter"><button type="button" data-quantity-change="-1" aria-label="Decrease quantity" <?= (int) $item['quantity'] <= 1 ? 'disabled' : '' ?>>−</button><input type="number" name="quantity" data-quantity-input min="1" max="<?= (int) $p['stock'] ?>" step="1" value="<?= (int) $item['quantity'] ?>" aria-label="Quantity" readonly><button type="button" data-quantity-change="1" aria-label="Increase quantity" <?= (int) $item['quantity'] >= (int) $p['stock'] ? 'disabled' : '' ?>>+</button></div>
                                </form>
                                <form action="<?= $e($base) ?>/cart/remove" method="post"><input type="hidden" name="csrf_token" value="<?= $e($csrf_token) ?>"><input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>"><input type="hidden" name="size" value="<?= $e($item['size']) ?>"><button>Remove</button></form>
                            </div>
                        </div>
                        <strong><?= number_format($item['subtotal'], 2) ?> <small>USD</small></strong>
                    </article>
                <?php endforeach; ?>
            </div>
            <aside class="order-summary">
                <p class="eyebrow">A LITTLE SUMMARY</p><h2>Estimated subtotal</h2><div><span>Items</span><b><?= number_format($total, 2) ?> USD</b></div><div><span>Delivery</span><span>Calculated with your address</span></div><hr>
                <?php if (empty($user)): ?>
                    <p>Sign in or create an account to continue.</p><a class="button button-dark full-button" href="<?= $e($base) ?>/login">Sign in to checkout <span>View</span></a>
                <?php else: ?>
                    <form class="checkout-start" data-checkout-form data-base="<?= $e($base) ?>" data-items="<?= $e(json_encode(array_map(static fn ($item) => ['product_id' => $item['product']['id'], 'quantity' => $item['quantity'], 'size' => $item['size'] ?: null], $items), JSON_HEX_APOS | JSON_HEX_QUOT)) ?>">
                        <label>Full name<input name="ship_name" required maxlength="100" value="<?= $e($_SESSION['name'] ?? '') ?>"></label>
                        <label>Phone<input name="ship_phone" required maxlength="30" autocomplete="tel"></label>
                        <label>Delivery address<input name="ship_address" required maxlength="255" autocomplete="street-address"></label>
                        <label>Delivery governorate<select name="ship_city" required><option value="">Choose your governorate</option><option value="amman">Amman</option><option value="zarqa">Zarqa</option><option value="irbid">Irbid</option><option value="balqa">Balqa</option><option value="madaba">Madaba</option><option value="jerash">Jerash</option><option value="ajloun">Ajloun</option><option value="mafraq">Mafraq</option><option value="karak">Karak</option><option value="tafilah">Tafilah</option><option value="maan">Maan</option><option value="aqaba">Aqaba</option></select></label>
                        <button class="button button-dark full-button" type="submit">Continue to secure payment <span>View</span></button><p class="checkout-message" aria-live="polite"></p>
                    </form>
                <?php endif; ?>
                <a class="text-link" href="<?= $e($base) ?>/shop">Keep exploring</a>
            </aside>
        </div>
    <?php endif; ?>
</section>
