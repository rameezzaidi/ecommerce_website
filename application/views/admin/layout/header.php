<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($page_title) ?> | <?= e(setting('site_name', 'DRESSY')) ?> Admin</title>
<link rel="icon" href="<?= base_url('assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= asset('vendor/bootstrap/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
<script>window.ADMIN={base:"<?= base_url() ?>",csrfName:"<?= $this->security->get_csrf_token_name() ?>",csrfHash:"<?= $this->security->get_csrf_hash() ?>",csrfCookie:"<?= $this->config->item('csrf_cookie_name') ?>"};</script>
</head><body>
<?php
$nav = function ($url, $ico, $label, $match, $badge = 0) {
    $seg2 = get_instance()->uri->segment(2);
    $on = ($seg2 === $match) || ($match === 'dashboard' && $seg2 === '');
    return '<a href="' . site_url($url) . '" class="' . ($on ? 'on' : '') . '">' . icon($ico, 18) . '<span>' . $label . '</span>' . ($badge ? '<span class="bdg">' . $badge . '</span>' : '') . '</a>';
};
?>
<aside class="a-side">
  <div class="a-brand"><?= e(setting('site_name', 'DRESSY')) ?><small>ADMIN PANEL</small></div>
  <nav class="a-nav">
    <?= $nav('admin', 'grid', 'Dashboard', 'dashboard') ?>
    <div class="grp">Marketing</div>
    <?= $nav('admin/analytics', 'bar-chart-2', 'Traffic Analytics', 'analytics') ?>
    <?= $nav('admin/tracking-links', 'link-2', 'Tracking Links', 'tracking-links') ?>
    <div class="grp">Sales</div>
    <?= $nav('admin/orders', 'shopping-bag', 'Orders', 'orders', $badges['orders']) ?>
    <?= $nav('admin/demands', 'package', 'Demand Orders', 'demands', $badges['demands']) ?>
    <?= $nav('admin/customers', 'users', 'Customers', 'customers') ?>
    <?= $nav('admin/coupons', 'percent', 'Coupons', 'coupons') ?>
    <div class="grp">Catalog</div>
    <?= $nav('admin/products', 'tag', 'Products', 'products') ?>
    <?= $nav('admin/categories', 'folder', 'Categories', 'categories') ?>
    <?= $nav('admin/custom-parts', 'layers', 'Customizer Items', 'custom-parts') ?>
    <?= $nav('admin/reviews', 'star', 'Reviews', 'reviews', $badges['reviews']) ?>
    <div class="grp">Content</div>
    <?= $nav('admin/banners', 'image', 'Banners', 'banners') ?>
    <?= $nav('admin/pages', 'file-text', 'Pages', 'pages') ?>
    <?= $nav('admin/messages', 'mail', 'Messages', 'messages', $badges['messages']) ?>
    <?= $nav('admin/subscribers', 'send', 'Subscribers', 'subscribers') ?>
    <div class="grp">System</div>
    <?= $nav('admin/settings', 'settings', 'Settings', 'settings') ?>
    <a href="<?= base_url() ?>" target="_blank" rel="noopener"><?= icon('external-link', 18) ?><span>View store</span></a>
  </nav>
  <div class="a-foot"><div style="color:#fff;font-weight:600"><?= e($admin->name) ?></div><div><?= e($admin->email) ?></div><a href="<?= site_url('admin/logout') ?>" style="color:#fff;display:inline-flex;gap:6px;align-items:center;margin-top:8px"><?= icon('log-out', 15) ?> Sign out</a></div>
</aside>
<div class="a-main">
  <div class="a-top"><button class="a-burger" aria-label="Menu"><?= icon('menu', 22) ?></button><h1><?= e($page_title) ?></h1><div class="ms-auto d-flex gap-2 align-items-center"><span class="d-none d-md-inline" style="color:var(--mute);font-size:12.5px"><?= date('D, d M Y') ?></span></div></div>
  <div class="a-body">
  <?= flash_html() ?>
