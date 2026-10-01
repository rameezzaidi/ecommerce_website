<div class="container" style="padding-bottom:90px">
  <div class="page-head"><span class="eyebrow">Saved for later</span><h1>My wishlist</h1></div>
  <?php if (!$items): ?><div class="empty"><?= icon('heart') ?><h3>Nothing saved yet</h3><p>Tap the heart on any product to save it here.</p><a href="<?= site_url('shop') ?>" class="btn-d mt-2">Browse the shop</a></div>
  <?php else: ?><div class="row g-3 g-lg-4"><?php foreach ($items as $p): ?><div class="col-6 col-md-4 col-lg-3"><?php $this->load->view('front/partials/product_card', array('p' => $p, 'wishlist' => $wishlist)); ?></div><?php endforeach; ?></div><?php endif; ?>
</div>
