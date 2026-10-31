<?php
$e = static fn ($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$base = rtrim((string) ($base_path ?? ''), '/');
$pageFile = __DIR__ . '/' . ($page_view ?? '') . '.php';
$flash = $_SESSION['flash'] ?? '';
$logoutToken = $_SESSION['auth_csrf'] ??= bin2hex(random_bytes(32));
unset($_SESSION['flash']);
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#bc8050"><title><?= $e(($title ?? 'Wellness, made personal') . ' · Shifa') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="<?= $e($base) ?>/assets/css/shifa.css">
  <link rel="stylesheet" href="<?= $e($base) ?>/assets/css/shifa-admin.css">
  <?php if (empty($is_admin)): ?><link rel="stylesheet" href="<?= $e($base) ?>/assets/css/storefront.css"><?php endif; ?>
</head>
<body class="<?= empty($is_admin) ? 'vitamins-store' : '' ?> min-h-screen antialiased">
<?php if (empty($is_admin)): ?><a class="skip-link" href="#main-content">Skip to content</a><?php endif; ?>
<header class="site-header">
  <a class="brand" href="<?= $e($base) ?>/"><span class="brand-mark">✳</span><span>shifa<span class="brand-dot">.</span><small>WELLNESS, YOUR WAY</small></span></a>
  <form class="nav-search" action="<?= $e($base) ?>/shop" method="get"><input name="q" value="<?= $e($search ?? '') ?>" placeholder="Search vitamins, supplements & more" aria-label="Search products"><button type="submit" aria-label="Search"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10" cy="10" r="7"/><path d="m15 15 6 6"/></svg></button></form>
  <div class="header-actions">
    <?php if (!empty($is_admin)): ?><a class="account-link" href="<?= $e($base) ?>/admin">Admin</a><?php else: ?>
    <details class="header-dropdown" data-header-dropdown>
      <summary class="header-icon account-icon" aria-label="Account menu" title="Account"><svg width="28" height="28" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="16" cy="10" r="6"/><path d="M5 29c0-7 4.6-12 11-12s11 5 11 12"/></svg></summary>
      <div class="header-popover" aria-label="Account options">
        <?php if (!empty($user)): ?><p class="popover-title">Hi, <?= $e(explode(' ', $user['name'])[0]) ?></p><a class="popover-link" href="<?= $e($base) ?>/profile">My profile <span aria-hidden="true">↗</span></a><a class="popover-link" href="<?= $e($base) ?>/account/orders">My orders <span aria-hidden="true">↗</span></a><form class="popover-logout" method="post" action="<?= $e($base) ?>/logout"><input type="hidden" name="csrf_token" value="<?= $e($logoutToken) ?>"><button type="submit">Sign out</button></form>
        <?php else: ?><p class="popover-title">Your Shifa space</p><p class="popover-description">Sign in to follow your orders and manage your account.</p><a class="popover-primary" href="<?= $e($base) ?>/login">Sign in</a><a class="popover-secondary" href="<?= $e($base) ?>/register">Create an account</a><?php endif; ?>
      </div>
    </details>
    <details class="header-dropdown" data-header-dropdown>
      <summary class="header-icon cart-icon" aria-label="Shopping bag menu, <?= (int) ($cart_count ?? 0) ?> items" title="Shopping bag"><svg width="28" height="28" viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 11h20l2 18H4z"/><path d="M11 14V9a5 5 0 0 1 10 0v5"/></svg><?php if (!empty($cart_count)): ?><span class="cart-icon-count"><?= (int) $cart_count ?></span><?php endif; ?></summary>
      <div class="header-popover" aria-label="Shopping bag summary"><div class="popover-heading"><p class="popover-title">Your bag</p><span><?= (int) ($cart_count ?? 0) ?> <?= (int) ($cart_count ?? 0) === 1 ? 'item' : 'items' ?></span></div><?php if (empty($cart_count)): ?><p class="popover-description">Your bag is empty. Find a little goodness for your everyday.</p><?php else: ?><p class="popover-description">Your essentials are waiting. Open your bag to review products, quantities and totals.</p><?php endif; ?><a class="popover-primary" href="<?= $e($base) ?>/cart">View bag <span aria-hidden="true">↗</span></a><a class="popover-secondary" href="<?= $e($base) ?>/shop">Continue shopping</a></div>
    </details>
    <?php endif; ?>
    <?php if (!empty($is_admin)): ?><form class="logout-form" method="post" action="<?= $e($base) ?>/logout"><input type="hidden" name="csrf_token" value="<?= $e($logoutToken) ?>"><button>Sign out</button></form><?php endif; ?>
  </div>
</header>
<?php if (empty($is_admin)): ?>
<nav class="store-nav" aria-label="Main navigation"><a href="<?= $e($base) ?>/shop">Shop all</a><a href="<?= $e($base) ?>/#shop-by-need">Shop by category</a><a href="<?= $e($base) ?>/#featured">New arrivals</a><a href="<?= $e($base) ?>/shop?q=vitamin">Vitamins</a><a href="<?= $e($base) ?>/#shop-by-brand">Shop by brand</a><a href="<?= $e($base) ?>/shop?q=omega">Omega & fish oil</a><a href="<?= $e($base) ?>/#story">Discover Shifa</a></nav>
<div class="service-strip"><span>◇ &nbsp; Wellness for your everyday</span><span>♡ &nbsp; Your favourite supplement brands</span><span>↗ &nbsp; Discover your next daily essential</span></div>
<div class="announcement"><span>YOUR DAILY DOSE OF FEEL-GOOD &nbsp; | &nbsp; EXPLORE THE SHIFA COLLECTION</span></div>
<?php endif; ?>
<?php if (!empty($is_admin)): ?><div class="admin-nav"><span>SHIFA / STUDIO</span><a href="<?= $e($base) ?>/admin">Overview</a><a href="<?= $e($base) ?>/admin/products">Products</a><a href="<?= $e($base) ?>/admin/catalog">Catalog</a><a href="<?= $e($base) ?>/admin/orders">Orders</a><a href="<?= $e($base) ?>/admin/users">Customers</a><a href="<?= $e($base) ?>/admin/reviews">Reviews</a><a href="<?= $e($base) ?>/admin/payments">Payments</a></div><?php endif; ?>
<?php if ($flash !== ''): ?><div class="flash-message"><?= $e($flash) ?></div><?php endif; ?>
<main id="main-content"><?php if (is_file($pageFile)) { require $pageFile; } else { ?><section class="section-wrap"><h1>Page is on its way.</h1><a class="button button-dark" href="<?= $e($base) ?>/">Back home</a></section><?php } ?></main>
<footer class="site-footer"><div><a class="brand brand-light" href="<?= $e($base) ?>/"><span class="brand-mark">✳</span><span>shifa<span class="brand-dot">.</span><small>WELLNESS, YOUR WAY</small></span></a><p>Small rituals. Better days.</p></div><div class="footer-links"><a href="<?= $e($base) ?>/shop">Shop all</a><a href="<?= $e($base) ?>/account/orders">Your orders</a><a href="<?= $e($base) ?>/profile">Profile</a></div><span>© <?= date('Y') ?> Shifa Wellness</span></footer>
<script src="<?= $e($base) ?>/assets/js/shifa.js" defer></script>
</body></html>
