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
  <meta name="theme-color" content="#0e2721"><title><?= $e(($title ?? 'Wellness, made personal') . ' · Shifa') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link rel="stylesheet" href="<?= $e($base) ?>/assets/css/shifa.css">
  <link rel="stylesheet" href="<?= $e($base) ?>/assets/css/shifa-home.css">
  <link rel="stylesheet" href="<?= $e($base) ?>/assets/css/shifa-wellness.css">
  <link rel="stylesheet" href="<?= $e($base) ?>/assets/css/shifa-admin.css">
</head>
<body class="min-h-screen antialiased wellness-shell">
<div class="announcement"><span>Thoughtful wellness, delivered with care</span><span>Free delivery on orders over 50 JOD · <b>Made for everyday care</b></span></div>
<header class="site-header">
  <a class="brand" href="<?= $e($base) ?>/"><span class="brand-mark">✳</span><span>shifa<span class="brand-dot">.</span><small>WELLNESS, YOUR WAY</small></span></a>
  <nav class="main-nav"><a href="<?= $e($base) ?>/shop">Shop</a><a href="<?= $e($base) ?>/#intentions">Daily essentials</a><a href="<?= $e($base) ?>/#story">Our approach</a></nav>
  <form class="nav-search" action="<?= $e($base) ?>/shop" method="get"><span>⌕</span><input name="q" placeholder="Search wellness" aria-label="Search products"></form>
  <div class="header-actions">
    <?php if (!empty($is_admin)): ?><a class="account-link" href="<?= $e($base) ?>/admin">Admin</a><form class="logout-form" method="post" action="<?= $e($base) ?>/logout"><input type="hidden" name="csrf_token" value="<?= $e($logoutToken) ?>"><button>Sign out</button></form><?php elseif (!empty($user)): ?><a class="account-link" href="<?= $e($base) ?>/profile">Hi, <?= $e(explode(' ', $user['name'])[0]) ?></a><a class="bag-link" href="<?= $e($base) ?>/cart" aria-label="Shopping bag" aria-haspopup="dialog" aria-expanded="false" data-cart-open>Bag <span data-cart-count><?= (int) ($cart_count ?? 0) ?></span></a><form class="logout-form" method="post" action="<?= $e($base) ?>/logout"><input type="hidden" name="csrf_token" value="<?= $e($logoutToken) ?>"><button>Sign out</button></form><?php else: ?><a class="account-link" href="<?= $e($base) ?>/login">Sign in</a><a class="bag-link" href="<?= $e($base) ?>/cart" aria-label="Shopping bag" aria-haspopup="dialog" aria-expanded="false" data-cart-open>Bag <span data-cart-count><?= (int) ($cart_count ?? 0) ?></span></a><?php endif; ?>
  </div>
</header>
<?php if (!empty($is_admin)): ?><div class="admin-nav"><span>SHIFA / STUDIO</span><a href="<?= $e($base) ?>/admin">Overview</a><a href="<?= $e($base) ?>/admin/products">Products</a><a href="<?= $e($base) ?>/admin/catalog">Catalog</a><a href="<?= $e($base) ?>/admin/orders">Orders</a><a href="<?= $e($base) ?>/admin/users">Customers</a><a href="<?= $e($base) ?>/admin/reviews">Reviews</a><a href="<?= $e($base) ?>/admin/payments">Payments</a></div><?php endif; ?>
<?php if ($flash !== ''): ?><div class="flash-message"><?= $e($flash) ?></div><?php endif; ?>
<main><?php if (is_file($pageFile)) { require $pageFile; } else { ?><section class="section-wrap"><h1>Page is on its way.</h1><a class="button button-dark" href="<?= $e($base) ?>/">Back home</a></section><?php } ?></main>
<?php if (empty($is_admin)) require __DIR__ . '/partials/cart-drawer.php'; ?>
<footer class="site-footer"><div><a class="brand brand-light" href="<?= $e($base) ?>/"><span class="brand-mark">✳</span><span>shifa<span class="brand-dot">.</span><small>WELLNESS, YOUR WAY</small></span></a><p>Small rituals. Better days.</p></div><div class="footer-links"><a href="<?= $e($base) ?>/shop">Shop all</a><?php if (!empty($user)): ?><a href="<?= $e($base) ?>/account/orders">Your orders</a><a href="<?= $e($base) ?>/profile">Profile</a><?php elseif (empty($is_admin)): ?><a href="<?= $e($base) ?>/login">Sign in</a><?php else: ?><a href="<?= $e($base) ?>/admin">Dashboard</a><?php endif; ?></div><span>© <?= date('Y') ?> Shifa Wellness</span></footer>
<script src="<?= $e($base) ?>/assets/js/shifa.js" defer></script>
</body></html>
