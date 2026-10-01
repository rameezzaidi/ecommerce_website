<!-- HERO -->
<?php if ($heroes): ?>
<section class="hero">
  <div id="heroCar" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6000">
    <?php if (count($heroes) > 1): ?><div class="carousel-indicators"><?php foreach ($heroes as $i => $h): ?><button type="button" data-bs-target="#heroCar" data-bs-slide-to="<?= $i ?>" class="<?= $i ? '' : 'active' ?>" aria-label="Slide <?= $i + 1 ?>"></button><?php endforeach; ?></div><?php endif; ?>
    <div class="carousel-inner">
      <?php foreach ($heroes as $i => $h): ?>
      <div class="carousel-item <?= $i ? '' : 'active' ?>">
        <div class="hero-bg" style="background-image:url('<?= upload_url($h->image) ?>')"></div><div class="hero-shade"></div>
        <div class="container h-100"><div class="hero-copy">
          <?php if ($h->eyebrow): ?><span class="eyebrow"><?= e($h->eyebrow) ?></span><?php endif; ?>
          <h1><?= e($h->title) ?></h1>
          <?php if ($h->subtitle): ?><p><?= e($h->subtitle) ?></p><?php endif; ?>
          <div><a href="<?= e($h->link ? (preg_match('#^https?://#', $h->link) ? $h->link : site_url($h->link)) : site_url('shop')) ?>" class="btn-w"><?= e($h->button_text ?: 'Shop now') ?> <?= icon('arrow-right', 16) ?></a></div>
        </div></div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php if (count($heroes) > 1): ?>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCar" data-bs-slide="prev" aria-label="Previous"><?= icon('chevron-left', 30, 'text-white') ?></button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCar" data-bs-slide="next" aria-label="Next"><?= icon('chevron-right', 30, 'text-white') ?></button>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<!-- TRUST -->
<section class="trust"><div class="container"><div class="row g-0">
  <div class="col-6 col-lg-3"><div class="t"><?= icon('truck') ?><div><b>Free Delivery</b><span>Over <?= money(setting('free_shipping_over', 8000)) ?></span></div></div></div>
  <div class="col-6 col-lg-3"><div class="t"><?= icon('refresh-cw') ?><div><b>Easy Returns</b><span>7-day exchange policy</span></div></div></div>
  <div class="col-6 col-lg-3"><div class="t"><?= icon('shield') ?><div><b>Secure Checkout</b><span>COD &amp; trusted payments</span></div></div></div>
  <div class="col-6 col-lg-3"><div class="t"><?= icon('globe') ?><div><b>Worldwide Shipping</b><span>International delivery</span></div></div></div>
</div></div></section>

<!-- CATEGORIES -->
<section class="sec">
  <div class="container">
    <div class="sec-head"><div><span class="eyebrow">Browse</span><h2>Shop by category</h2></div><a href="<?= site_url('shop') ?>" class="link-arrow">All products <?= icon('arrow-right', 14) ?></a></div>
    <div class="row g-3 g-lg-4">
      <?php foreach (array_slice($nav_tree, 0, 4) as $c): ?>
      <div class="col-6 col-lg-3"><a href="<?= site_url('shop/' . $c->slug) ?>" class="cat-tile reveal"><img src="<?= upload_url($c->image) ?>" alt="<?= e($c->name) ?>" loading="lazy"><div class="lbl"><b><?= e($c->name) ?></b><span>Shop <?= icon('arrow-right', 12) ?></span></div></a></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- FEATURED -->
<section class="sec sec-soft">
  <div class="container">
    <div class="sec-head"><div><span class="eyebrow">Curated</span><h2>Featured pieces</h2></div><a href="<?= site_url('shop') ?>" class="link-arrow">View all <?= icon('arrow-right', 14) ?></a></div>
    <div class="row g-3 g-lg-4">
      <?php foreach ($featured as $p): ?><div class="col-6 col-md-4 col-lg-3 reveal"><?php $this->load->view('front/partials/product_card', array('p' => $p, 'wishlist' => $wishlist)); ?></div><?php endforeach; ?>
    </div>
  </div>
</section>

<!-- PROMOS -->
<?php if ($promos): ?>
<section class="sec" style="padding-bottom:0">
  <div class="container"><div class="row g-3 g-lg-4">
    <?php foreach ($promos as $b): ?>
    <div class="col-md-4"><a class="promo reveal" href="<?= e($b->link ? (preg_match('#^https?://#', $b->link) ? $b->link : site_url($b->link)) : site_url('shop')) ?>"><img src="<?= upload_url($b->image) ?>" alt="" loading="lazy"><div class="in"><?php if ($b->eyebrow): ?><span class="eyebrow"><?= e($b->eyebrow) ?></span><?php endif; ?><h3><?= e($b->title) ?></h3><span class="link-arrow" style="border-color:#fff;align-self:flex-start"><?= e($b->button_text ?: 'Shop now') ?> <?= icon('arrow-right', 14) ?></span></div></a></div>
    <?php endforeach; ?>
  </div></div>
</section>
<?php endif; ?>

<!-- BEST SELLING -->
<section class="sec">
  <div class="container">
    <div class="sec-head"><div><span class="eyebrow">Most loved</span><h2>Best sellers</h2></div><a href="<?= site_url('shop?sort=popular') ?>" class="link-arrow">Shop best sellers <?= icon('arrow-right', 14) ?></a></div>
    <div class="row g-3 g-lg-4">
      <?php foreach ($best as $p): ?><div class="col-6 col-md-4 col-lg-3 reveal"><?php $this->load->view('front/partials/product_card', array('p' => $p, 'wishlist' => $wishlist)); ?></div><?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CUSTOMIZER CTA -->
<section class="cta-black sec">
  <div class="container"><div class="row align-items-center g-5">
    <div class="col-lg-6">
      <span class="eyebrow" style="color:rgba(255,255,255,.6)">Design studio</span>
      <h2 style="margin:14px 0 18px">Build your own outfit,<br>see it before you wear it.</h2>
      <p>Pick jeans, a t-shirt or shirt and shoes. Mix styles and colours on our live mannequin and add the whole look to your bag in one click.</p>
      <div class="steps"><div><i>1</i>Choose pieces</div><div><i>2</i>Preview live</div><div><i>3</i>Add package</div></div>
      <a href="<?= site_url('customize') ?>" class="btn-w">Start designing <?= icon('arrow-right', 16) ?></a>
    </div>
    <div class="col-lg-6"><div class="cta-figs">
      <div class="outfit-thumb" data-outfit='{"jeans":{"type":"jeans","style":"slim","color":"#1f3a5f"},"top":{"type":"tshirt","style":"crew","color":"#f2f2f2"},"shoes":{"type":"shoes","style":"sneaker","color":"#ffffff"}}'></div>
      <div class="outfit-thumb" style="margin-bottom:-12px" data-outfit='{"jeans":{"type":"jeans","style":"straight","color":"#161616"},"top":{"type":"shirt","style":"formal","color":"#ffffff"},"shoes":{"type":"shoes","style":"loafer","color":"#3b2a20"}}'></div>
      <div class="outfit-thumb" data-outfit='{"jeans":{"type":"jeans","style":"jogger","color":"#5a5a5a"},"top":{"type":"tshirt","style":"polo","color":"#0f0f0f"},"shoes":{"type":"shoes","style":"boot","color":"#6b4a2f"}}'></div>
    </div></div>
  </div></div>
</section>

<!-- NEW ARRIVALS -->
<section class="sec">
  <div class="container">
    <div class="sec-head"><div><span class="eyebrow">Just landed</span><h2>New arrivals</h2></div><a href="<?= site_url('shop') ?>" class="link-arrow">Shop new <?= icon('arrow-right', 14) ?></a></div>
    <div class="row g-3 g-lg-4">
      <?php foreach ($new as $p): ?><div class="col-6 col-md-4 col-lg-3 reveal"><?php $this->load->view('front/partials/product_card', array('p' => $p, 'wishlist' => $wishlist)); ?></div><?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ON DEMAND -->
<section class="sec sec-soft">
  <div class="container"><div class="row align-items-center g-4">
    <div class="col-lg-7"><span class="eyebrow">Cannot find it?</span><h2 style="margin:10px 0 12px">Order on demand</h2><p style="color:var(--mute);max-width:560px;margin:0">Sold out, or something you saw somewhere else? Tell us the size, colour and a reference photo - we will source it, quote you a price and a delivery date.</p></div>
    <div class="col-lg-5 text-lg-end"><a href="<?= site_url('demand') ?>" class="btn-d">Request a piece <?= icon('arrow-right', 16) ?></a></div>
  </div></div>
</section>

<?php if ($reviews): ?>
<section class="sec">
  <div class="container">
    <div class="sec-head"><div><span class="eyebrow">Kind words</span><h2>Loved by our customers</h2></div></div>
    <div class="row g-3 g-lg-4">
      <?php foreach ($reviews as $r): ?><div class="col-md-4"><div class="quote reveal"><?= stars($r->rating) ?><p>&ldquo;<?= e($r->comment) ?>&rdquo;</p><small><b style="color:var(--ink)"><?= e($r->name) ?></b> &middot; <?= e($r->product_name) ?></small></div></div><?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
