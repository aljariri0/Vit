<section class="section-wrap dashboard-page admin-page">
    <a class="text-link" href="<?= $e($base) ?>/admin/orders">Back to orders</a>
    <div class="dashboard-heading order-detail-heading">
        <div><p class="eyebrow">ORDER DETAILS</p><h1>Order #<?= str_pad((string) $order['id'], 5, '0', STR_PAD_LEFT) ?><span class="brand-dot">.</span></h1><p>Placed <?= $e(date('F j, Y, g:i a', strtotime($order['created_at']))) ?> · <?= $e($order['customer_name']) ?> · <?= $e($order['email']) ?></p></div>
        <i class="status-pill status-<?= $e($order['status']) ?>"><?= $e(ucfirst($order['status'])) ?></i>
    </div>
    <div class="order-detail-layout">
        <div class="data-card cart-list admin-order-items">
            <h2>Products in this order</h2>
            <?php foreach ($order['items'] as $item): ?>
                <?php $productUrl = (string) ($item['product_url'] ?? ''); ?>
                <article class="cart-item">
                    <?php if ($productUrl !== ''): ?><a class="cart-thumb" href="<?= $e($productUrl) ?>" aria-label="View <?= $e($item['product_name']) ?> details"><?php else: ?><span class="cart-thumb" aria-hidden="true"><?php endif; ?>
                        <?php if (!empty($item['image_url'])): ?><img src="<?= $e($item['image_url']) ?>" alt="<?= $e($item['product_name']) ?>" loading="lazy"><?php else: ?><span>✳</span><?php endif; ?>
                    <?php if ($productUrl !== ''): ?></a><?php else: ?></span><?php endif; ?>
                    <div class="cart-item-info">
                        <?php if ($productUrl !== ''): ?><a href="<?= $e($productUrl) ?>"><b><?= $e($item['product_name']) ?></b></a><?php else: ?><b><?= $e($item['product_name']) ?></b><?php endif; ?>
                        <small><?= (int) $item['quantity'] ?> item<?= (int) $item['quantity'] === 1 ? '' : 's' ?><?= !empty($item['size']) ? ' · ' . $e($item['size']) : '' ?></small>
                    </div>
                    <strong><?= number_format((float) $item['order_total'], 2) ?> USD</strong>
                </article>
            <?php endforeach; ?>
            <?php if (!$order['items']): ?><p class="empty-inline">This order has no product lines.</p><?php endif; ?>
        </div>
        <aside class="order-summary">
            <p class="eyebrow">DELIVERING TO</p><h3><?= $e($order['ship_name']) ?></h3><p><?= $e($order['ship_address']) ?><br><?= $e($order['ship_city']) ?><br><?= $e($order['ship_phone']) ?></p>
            <hr><div><span>Shipping</span><b><?= number_format((float) $order['shipping_total'], 2) ?> USD</b></div><div><span>Total</span><b><?= number_format((float) $order['total'], 2) ?> USD</b></div>
        </aside>
    </div>
</section>
