<?php $cartCsrf = $_SESSION['store_csrf'] ??= bin2hex(random_bytes(32)); ?>
<div class="cart-drawer" id="cart-drawer" hidden inert data-base="<?= $e($base) ?>" data-csrf="<?= $e($cartCsrf) ?>">
  <button class="cart-drawer-backdrop" type="button" data-cart-close tabindex="-1" aria-label="Close shopping bag"></button>
  <section class="cart-drawer-panel" role="dialog" aria-modal="true" aria-labelledby="cart-drawer-title">
    <header class="cart-drawer-header"><div><p class="eyebrow">YOUR SHIFA EDIT</p><h2 id="cart-drawer-title">Your bag <span data-drawer-count>0</span></h2></div><button class="cart-drawer-close" type="button" data-cart-close aria-label="Close bag">×</button></header>
    <div class="cart-delivery-progress"><p data-delivery-message>Free delivery on orders over 50 JOD.</p><div class="delivery-progress-track" role="progressbar" aria-label="Free delivery progress" aria-valuemin="0" aria-valuemax="50"><span data-delivery-progress></span></div></div>
    <p class="cart-drawer-status" data-cart-status aria-live="polite"></p>
    <div class="cart-drawer-lines" data-cart-lines><div class="drawer-loading"><span class="button-spinner"></span><span>Loading your bag…</span></div></div>
    <div class="cart-drawer-footer"><div class="drawer-subtotal"><span>Subtotal</span><strong><span data-cart-total>0.00</span> JOD</strong></div><p>Delivery is calculated at checkout.</p><a class="button button-dark drawer-checkout" href="<?= $e($base) ?>/cart">Review bag & checkout <span>↗</span></a><a class="drawer-continue" href="<?= $e($base) ?>/shop">Continue shopping</a></div>
  </section>
</div>
