<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($seo['title']) ?></title>
<meta name="description" content="<?= e($seo['description']) ?>">
<meta property="og:title" content="<?= e($seo['title']) ?>">
<meta property="og:description" content="<?= e($seo['description']) ?>">
<meta property="og:type" content="website">
<link rel="icon" href="<?= base_url('assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="preload" href="<?= base_url('assets/fonts/inter-latin-400-normal.woff2') ?>" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= asset('vendor/bootstrap/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= asset('css/store.css') ?>">
<script>window.DRESSY={base:"<?= base_url() ?>",csrfName:"<?= $this->security->get_csrf_token_name() ?>",csrfHash:"<?= $this->security->get_csrf_hash() ?>",csrfCookie:"<?= $this->config->item('csrf_cookie_name') ?>",currency:"<?= e(setting('currency_symbol', 'Rs.')) ?>"};</script>
</head>
<body>
<?php if (setting('announcement')): ?>
<div class="announce"><?php foreach (explode('|', setting('announcement')) as $i => $a): ?><?= $i ? '<span class="sep">|</span>' : '' ?><span class="<?= $i ? 'hide-m' : '' ?>"><?= e(trim($a)) ?></span><?php endforeach; ?></div>
<?php endif; ?>

<header class="site-header">
  <div class="container hdr">
    <button class="hdr-btn nav-toggle" data-bs-toggle="offcanvas" data-bs-target="#mobNav" aria-label="Menu"><?= icon('menu', 22) ?></button>
    <a href="<?= base_url() ?>" class="logo" aria-label="<?= e(setting('site_name', 'DRESSY')) ?>"><?= e(setting('site_name', 'DRESSY')) ?><small><?= e(setting('tagline_small', 'FASHION HOUSE')) ?></small></a>

    <ul class="main-nav">
      <li><a href="<?= site_url('shop') ?>" class="<?= uri_active('shop') && !$this->uri->segment(2) ? 'active' : '' ?>">Shop</a></li>
      <?php foreach ($nav_tree as $c): ?>
      <li>
        <a href="<?= site_url('shop/' . $c->slug) ?>" class="<?= $this->uri->segment(2) === $c->slug ? 'active' : '' ?>"><?= e($c->name) ?></a>
        <?php if ($c->children): ?>
        <div class="mega"><div class="container"><div class="row g-4">
          <div class="col-md-3"><h6><?= e($c->name) ?></h6>
            <?php foreach ($c->children as $ch): ?><a class="m-link" href="<?= site_url('shop/' . $ch->slug) ?>"><?= e($ch->name) ?></a><?php endforeach; ?>
            <a class="m-link" href="<?= site_url('shop/' . $c->slug) ?>" style="color:var(--ink);font-weight:600;margin-top:8px">View all <?= icon('arrow-right', 14) ?></a>
          </div>
          <div class="col-md-5 d-none d-md-block"><a href="<?= site_url('shop/' . $c->slug) ?>" class="cat-tile" style="aspect-ratio:16/8"><img src="<?= upload_url($c->image) ?>" alt="<?= e($c->name) ?>" loading="lazy"><div class="lbl"><b><?= e($c->name) ?></b><span>Explore</span></div></a></div>
          <div class="col-md-4 d-none d-md-block"><h6>Make it yours</h6><p style="color:var(--mute);font-size:14px">Build a complete outfit - jeans, tee or shirt and shoes - and preview it on our mannequin before you buy.</p><a href="<?= site_url('customize') ?>" class="btn-o btn-sm">Design your outfit</a></div>
        </div></div></div>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>
      <li><a href="<?= site_url('customize') ?>" class="<?= uri_active('customize') ?>">Customize</a></li>
      <li><a href="<?= site_url('demand') ?>" class="<?= uri_active('demand') ?>">On Demand</a></li>
    </ul>

    <div class="hdr-actions ms-auto ms-xl-0">
      <button class="hdr-btn" id="openSearch" aria-label="Search"><?= icon('search', 21) ?></button>
      <a href="<?= site_url('wishlist') ?>" class="hdr-btn d-none d-sm-inline-flex" aria-label="Wishlist"><?= icon('heart', 21) ?><span class="count" id="wishCount" data-n="<?= (int)$wish_count ?>"><?= (int)$wish_count ?: '' ?></span></a>
      <a href="<?= site_url($customer ? 'account' : 'account/login') ?>" class="hdr-btn" aria-label="Account"><?= icon('user', 21) ?></a>
      <button class="hdr-btn" id="openBag" aria-label="Shopping bag"><?= icon('shopping-bag', 21) ?><span class="count" id="bagCount" data-n="<?= (int)$cart_count ?>"><?= (int)$cart_count ?: '' ?></span></button>
    </div>
  </div>
</header>

<!-- mobile nav -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobNav" style="max-width:86vw">
  <div class="offcanvas-header" style="border-bottom:1px solid var(--line)"><span class="logo" style="font-size:24px"><?= e(setting('site_name', 'DRESSY')) ?></span><button class="hdr-btn" data-bs-dismiss="offcanvas" aria-label="Close"><?= icon('x', 22) ?></button></div>
  <div class="offcanvas-body">
    <a class="d-block py-2 fw-semibold" href="<?= site_url('shop') ?>">Shop All</a>
    <?php foreach ($nav_tree as $c): ?>
      <div class="py-2 border-top"><a class="d-block fw-semibold" href="<?= site_url('shop/' . $c->slug) ?>"><?= e($c->name) ?></a>
        <?php foreach ($c->children as $ch): ?><a class="d-block py-1 ps-3" style="color:var(--mute);font-size:14px" href="<?= site_url('shop/' . $ch->slug) ?>"><?= e($ch->name) ?></a><?php endforeach; ?></div>
    <?php endforeach; ?>
    <div class="border-top pt-3 mt-2">
      <a class="d-block py-2 fw-semibold" href="<?= site_url('customize') ?>">Customize Outfit</a>
      <a class="d-block py-2 fw-semibold" href="<?= site_url('demand') ?>">Order on Demand</a>
      <a class="d-block py-2 fw-semibold" href="<?= site_url('wishlist') ?>">Wishlist</a>
      <a class="d-block py-2 fw-semibold" href="<?= site_url('track-order') ?>">Track Order</a>
      <a class="d-block py-2 fw-semibold" href="<?= site_url('contact') ?>">Contact</a>
    </div>
  </div>
</div>

<!-- search -->
<div class="search-overlay" id="searchBox" role="dialog" aria-label="Search">
  <button class="hdr-btn close-x" id="closeSearch" aria-label="Close"><?= icon('x', 26) ?></button>
  <form action="<?= site_url('shop') ?>" method="get">
    <input type="search" name="q" placeholder="Search the collection" autocomplete="off" aria-label="Search">
    <button class="go" aria-label="Go"><?= icon('arrow-right', 28) ?></button>
  </form>
  <div class="search-tags"><?php foreach (array_slice($nav_tree, 0, 6) as $c): ?><a href="<?= site_url('shop/' . $c->slug) ?>"><?= e($c->name) ?></a><?php endforeach; ?><a href="<?= site_url('shop?sale=1') ?>">On Sale</a></div>
</div>

<!-- bag drawer -->
<div class="drawer-bg" id="drawerBg"></div>
<aside class="drawer" id="bagDrawer" aria-label="Shopping bag"><div class="drawer-h"><h5>Your Bag</h5><button class="hdr-btn" id="closeBag" aria-label="Close"><?= icon('x', 22) ?></button></div><div id="drawerInner" style="display:flex;flex-direction:column;flex:1;min-height:0"></div></aside>
<div class="toast-wrap" id="toasts"></div>

<main id="main">
<?php $fl = flash_html(); if ($fl): ?><div class="container pt-3"><?= $fl ?></div><?php endif; ?>
