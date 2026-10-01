</main>

<section class="news">
  <div class="container">
    <div class="eyebrow">The Dressy Letter</div>
    <h2 style="font-size:clamp(26px,3.4vw,40px);margin:8px 0 6px">First access. Quiet luxury.</h2>
    <p style="color:var(--mute);margin:0">New drops, private sales and styling notes - once a week, never more.</p>
    <form id="newsForm" novalidate><input type="email" name="email" placeholder="Your email address" aria-label="Email" required><button type="submit">Subscribe</button></form>
  </div>
</section>

<footer class="footer">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4">
        <a href="<?= base_url() ?>" class="logo"><?= e(setting('site_name', 'DRESSY')) ?><small><?= e(setting('tagline_small', 'FASHION HOUSE')) ?></small></a>
        <p style="margin-top:20px;max-width:340px"><?= e(setting('about_short', 'Considered essentials and statement pieces, cut with care and delivered worldwide.')) ?></p>
        <div class="soc">
          <?php foreach (array('facebook', 'instagram', 'tiktok') as $s): if (setting('social_' . $s)): ?><a href="<?= e(setting('social_' . $s)) ?>" target="_blank" rel="noopener" aria-label="<?= $s ?>"><?= brand_icon($s, 17) ?></a><?php endif; endforeach; ?>
          <?php if (setting('whatsapp_number')): ?><a href="https://wa.me/<?= e(preg_replace('/\D/', '', setting('whatsapp_number'))) ?>" target="_blank" rel="noopener" aria-label="whatsapp"><?= brand_icon('whatsapp', 17) ?></a><?php endif; ?>
        </div>
      </div>
      <div class="col-6 col-lg-2"><h6>Shop</h6>
        <a href="<?= site_url('shop') ?>">All Products</a>
        <?php foreach (array_slice($nav_tree, 0, 4) as $c): ?><a href="<?= site_url('shop/' . $c->slug) ?>"><?= e($c->name) ?></a><?php endforeach; ?>
        <a href="<?= site_url('shop?sale=1') ?>">Sale</a>
      </div>
      <div class="col-6 col-lg-2"><h6>Services</h6>
        <a href="<?= site_url('customize') ?>">Customize Outfit</a>
        <a href="<?= site_url('demand') ?>">Order on Demand</a>
        <a href="<?= site_url('track-order') ?>">Track Order</a>
        <a href="<?= site_url('account') ?>">My Account</a>
        <a href="<?= site_url('wishlist') ?>">Wishlist</a>
      </div>
      <div class="col-6 col-lg-2"><h6>Company</h6>
        <?php foreach ($footer_pages as $pg): ?><a href="<?= site_url('page/' . $pg->slug) ?>"><?= e($pg->title) ?></a><?php endforeach; ?>
        <a href="<?= site_url('contact') ?>">Contact</a>
      </div>
      <div class="col-6 col-lg-2"><h6>Get in touch</h6>
        <div style="margin-bottom:8px"><?= e(setting('contact_phone')) ?></div>
        <div style="margin-bottom:8px;word-break:break-all"><?= e(setting('contact_email')) ?></div>
        <div style="font-size:13px"><?= e(setting('contact_address')) ?></div>
      </div>
    </div>
    <div class="footer-bottom">
      <div>&copy; <?= date('Y') ?> <?= e(setting('site_name', 'DRESSY')) ?>. All rights reserved.</div>
      <div class="pay-icons"><span>COD</span><span>Bank</span><span>Easypaisa</span><span>JazzCash</span></div>
    </div>
  </div>
</footer>

<?php if (setting('whatsapp_number')): ?>
<a class="wa-float" href="https://wa.me/<?= e(preg_replace('/\D/', '', setting('whatsapp_number'))) ?>?text=<?= rawurlencode('Hi, I need help with an order') ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"><?= brand_icon('whatsapp', 26) ?></a>
<?php endif; ?>

<script src="<?= asset('vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= asset('vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= asset('js/outfit.js') ?>"></script>
<script src="<?= asset('js/store.js') ?>"></script>
<?php if (!empty($page_js)) foreach ($page_js as $js): ?><script src="<?= asset('js/' . $js) ?>"></script><?php endforeach; ?>
</body>
</html>
