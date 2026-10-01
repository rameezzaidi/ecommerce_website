<div class="container text-center" style="max-width:640px;padding:70px 0 100px">
  <div class="check-big"><?= icon('check', 36) ?></div>
  <span class="eyebrow">Request received</span>
  <h1 style="font-size:clamp(30px,4vw,44px);margin:10px 0 10px">We are on it</h1>
  <p style="color:var(--mute)">Your demand request <b style="color:var(--ink)"><?= e($d->demand_no) ?></b> for <b style="color:var(--ink)"><?= e($d->product_name) ?></b> &times; <?= (int)$d->qty ?> is with our sourcing team. Expect a quote on <?= e($d->phone) ?> or <?= e($d->email) ?> within 24-48 hours.</p>
  <div class="mt-4 d-flex gap-3 justify-content-center flex-wrap"><a href="<?= site_url('track-order?no=' . $d->demand_no . '&who=' . urlencode($d->email)) ?>" class="btn-d">Track request</a><a href="<?= site_url('shop') ?>" class="btn-o">Keep shopping</a></div>
</div>
