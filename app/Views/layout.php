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
  <link rel="stylesheet" href="<?= $e($base) ?>/assets/css/shifa-reviews.css">
  <link rel="stylesheet" href="<?= $e($base) ?>/assets/css/interface.css">
</head>
<body class="min-h-screen antialiased wellness-shell <?= !empty($is_admin) ? 'admin-shell' : '' ?>">
<?php if (empty($is_admin)): ?><div class="announcement"><span>Thoughtful wellness, delivered with care</span><span>Free delivery on orders over 70.52 USD · <b>Made for everyday care</b></span></div><?php endif; ?>
<header class="site-header <?= !empty($is_admin) ? 'admin-site-header' : '' ?>">
  <a class="brand" href="<?= $e($base) ?>/"><span class="brand-mark">✳</span><span>shifa<span class="brand-dot">.</span><small>WELLNESS, YOUR WAY</small></span></a>
  <?php if (empty($is_admin)): ?>
  <nav class="main-nav"><a href="<?= $e($base) ?>/shop">Shop</a><a href="<?= $e($base) ?>/#intentions">Daily essentials</a><a href="<?= $e($base) ?>/#story">Our approach</a></nav>
  <form class="nav-search" action="<?= $e($base) ?>/shop" method="get"><span>⌕</span><input name="q" placeholder="Search wellness" aria-label="Search products"></form>
  <?php endif; ?>
  <div class="header-actions">
    <?php if (!empty($is_admin)): ?><a class="account-link" href="<?= $e($base) ?>/admin">Admin</a><form class="logout-form" method="post" action="<?= $e($base) ?>/logout"><input type="hidden" name="csrf_token" value="<?= $e($logoutToken) ?>"><button>Sign out</button></form><?php elseif (!empty($user)): ?><a class="account-link" href="<?= $e($base) ?>/profile">Hi, <?= $e(explode(' ', $user['name'])[0]) ?></a><a class="bag-link" href="<?= $e($base) ?>/cart" aria-label="Shopping bag" aria-haspopup="dialog" aria-expanded="false" data-cart-open>Bag <span data-cart-count><?= (int) ($cart_count ?? 0) ?></span></a><form class="logout-form" method="post" action="<?= $e($base) ?>/logout"><input type="hidden" name="csrf_token" value="<?= $e($logoutToken) ?>"><button>Sign out</button></form><?php else: ?><a class="account-link" href="<?= $e($base) ?>/login">Sign in</a><a class="bag-link" href="<?= $e($base) ?>/cart" aria-label="Shopping bag" aria-haspopup="dialog" aria-expanded="false" data-cart-open>Bag <span data-cart-count><?= (int) ($cart_count ?? 0) ?></span></a><?php endif; ?>
  </div>
</header>
<?php if ($flash !== ''): ?><div class="flash-message"><?= $e($flash) ?></div><?php endif; ?>
<?php if (!empty($is_admin)): ?><aside class="admin-sidebar"><p class="eyebrow">SHIFA STUDIO</p><nav aria-label="Admin navigation"><?php foreach ([''=>'Overview','/orders'=>'Orders','/products'=>'Products','/catalog'=>'Catalog','/users'=>'Customers','/reviews'=>'Reviews','/payments'=>'Payments'] as $path=>$label): $target=$base.'/admin'.$path; $current=parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?><a href="<?= $e($target) ?>" <?= $current===$target || ($path==='' && $current===$base.'/admin/dashboard') ? 'aria-current="page"' : '' ?>><?= $e($label) ?></a><?php endforeach; ?></nav><a class="sidebar-store" href="<?= $e($base) ?>/">View storefront</a></aside><?php endif; ?>
<main><?php if (is_file($pageFile)) { require $pageFile; } else { ?><section class="section-wrap"><h1>Page is on its way.</h1><a class="button button-dark" href="<?= $e($base) ?>/">Back home</a></section><?php } ?></main>
<?php if (empty($is_admin)): ?>
<?php require __DIR__ . '/partials/cart-drawer.php'; ?>
<footer class="site-footer"><div><a class="brand brand-light" href="<?= $e($base) ?>/"><span class="brand-mark">✳</span><span>shifa<span class="brand-dot">.</span><small>WELLNESS, YOUR WAY</small></span></a><p>Small rituals. Better days.</p></div><nav class="footer-links" aria-label="Footer"><a href="<?= $e($base) ?>/shop">Shop</a><a href="<?= $e($base) ?>/#intentions">Daily essentials</a><a href="<?= $e($base) ?>/#story">Our approach</a></nav><span>© <?= date('Y') ?> Shifa Wellness</span></footer>
<?php endif; ?>
<script src="<?= $e($base) ?>/assets/js/shifa.js" defer></script>
<script src="<?= $e($base) ?>/assets/js/interface.js" defer></script>
</body></html>
