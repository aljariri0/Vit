<section class="section-wrap dashboard-page admin-page">
    <div class="dashboard-heading"><div><p class="eyebrow">FULFILMENT, WITH CARE</p><h1>Orders<span class="brand-dot">.</span></h1><p><?= (int) $total ?> orders in your store.</p></div></div>
    <form class="order-filters" action="<?= $e($base) ?>/admin/orders" method="get">
        <input name="q" value="<?= $e($_GET['q'] ?? '') ?>" placeholder="Search name or order number">
        <select name="status"><option value="">Every status</option><?php foreach (['pending','paid','processing','shipped','delivered','cancelled','refunded'] as $status): ?><option value="<?= $e($status) ?>" <?= ($_GET['status'] ?? '') === $status ? 'selected' : '' ?>><?= $e(ucfirst($status)) ?></option><?php endforeach; ?></select>
        <button class="button button-dark">Filter</button>
    </form>
    <div class="data-card" data-server-paged>
        <div class="table-head orders-admin-head"><span>Order</span><span>Customer</span><span>Date</span><span>Status</span><span>Total</span><span>Next step / view</span></div>
        <?php foreach ($orders as $order): ?>
            <?php $orderUrl = $e($base) . '/admin/order?id=' . (int) $order['id']; ?>
            <div class="table-row orders-admin-head">
                <b><a class="admin-order-link" href="<?= $orderUrl ?>">#<?= str_pad((string) $order['id'], 5, '0', STR_PAD_LEFT) ?></a></b>
                <span><?= $e($order['customer_name']) ?><small><?= $e($order['email']) ?></small></span>
                <span><?= $e(date('M j, Y', strtotime($order['created_at']))) ?></span>
                <span><i class="status-pill status-<?= $e($order['status']) ?>"><?= $e(ucfirst($order['status'])) ?></i></span>
                <b><?= number_format((float) $order['total'], 2) ?> USD</b>
                <div class="admin-order-actions">
                    <?php $next = ['pending' => ['processing'], 'paid' => ['shipped'], 'processing' => ['paid', 'shipped'], 'shipped' => ['delivered']][$order['status']] ?? []; ?>
                    <?php if ($next): ?><form action="<?= $e($base) ?>/admin/order/status" method="post"><input type="hidden" name="csrf_token" value="<?= $e($csrf_token) ?>"><input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>"><select name="status" aria-label="Update order status"><?php foreach ($next as $status): ?><option value="<?= $e($status) ?>"><?= $e(ucfirst($status)) ?></option><?php endforeach; ?></select><button class="mini-action">Update</button></form><?php else: ?><span class="muted">—</span><?php endif; ?>
                    <a class="mini-action admin-order-view" href="<?= $orderUrl ?>">View</a>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (!$orders): ?><p class="empty-inline">No orders match this view.</p><?php endif; ?>
    </div>
</section>

<nav class="table-pagination" aria-label="Order pages"><?php $pages=max(1,(int)ceil($total/8)); foreach (['Previous'=>max(1,$page-1),'Next'=>min($pages,$page+1)] as $label=>$target): ?><a class="mini-action" href="?<?= $e(http_build_query(array_merge($_GET,['page'=>$target]))) ?>" <?= ($label==='Previous' && $page<=1)||($label==='Next' && $page>=$pages) ? 'aria-disabled="true" tabindex="-1"' : '' ?>><?= $e($label) ?></a><?php if ($label==='Previous'): ?><span>Page <?= (int)$page ?> of <?= $pages ?> · <?= (int)$total ?> orders</span><?php endif; ?><?php endforeach; ?></nav>
