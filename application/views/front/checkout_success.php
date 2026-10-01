<div class="container" style="max-width:860px;padding:60px 0 90px">
  <div class="text-center">
    <div class="check-big"><?= icon('check', 36) ?></div>
    <span class="eyebrow">Thank you, <?= e(explode(' ', $o->name)[0]) ?></span>
    <h1 style="font-size:clamp(30px,4vw,46px);margin:10px 0 8px">Your order is confirmed</h1>
    <p style="color:var(--mute)">Order <b style="color:var(--ink)"><?= e($o->order_no) ?></b> &middot; we have recorded it and will contact you on <?= e($o->phone) ?> shortly.</p>
  </div>
  <div class="panel-d mt-4">
    <?php foreach ($items as $it): ?>
    <div class="d-flex justify-content-between gap-3 py-2" style="border-bottom:1px solid var(--line)"><div><b style="font-weight:600"><?= e($it->name) ?></b><div style="font-size:13px;color:var(--mute)">Qty <?= (int)$it->qty ?><?= $it->size ? ' &middot; ' . e($it->size) : '' ?><?= $it->color ? ' &middot; ' . e($it->color) : '' ?></div></div><b><?= money($it->line_total) ?></b></div>
    <?php endforeach; ?>
    <div class="sum-row mt-2"><span class="mut">Subtotal</span><span><?= money($o->subtotal) ?></span></div>
    <?php if ($o->discount > 0): ?><div class="sum-row"><span class="mut">Discount</span><span>-<?= money($o->discount) ?></span></div><?php endif; ?>
    <div class="sum-row"><span class="mut">Shipping</span><span><?= $o->shipping > 0 ? money($o->shipping) : 'Free' ?></span></div>
    <div class="sum-row total"><span>Total</span><span><?= money($o->total) ?></span></div>
  </div>
  <div class="row g-3 mt-1">
    <div class="col-md-6"><div class="panel-d h-100"><h4 style="font-size:18px">Delivering to</h4><div style="color:var(--mute);font-size:14.5px"><?= e($o->name) ?><br><?= e($o->address) ?><br><?= e($o->city) ?><?= $o->state ? ', ' . e($o->state) : '' ?> <?= e($o->postal_code) ?><br><?= e($o->country) ?></div></div></div>
    <div class="col-md-6"><div class="panel-d h-100"><h4 style="font-size:18px"><?= e($methods[$o->payment_method][0] ?? 'Payment') ?></h4><div style="color:var(--mute);font-size:14.5px;white-space:pre-line"><?= e(isset($methods[$o->payment_method]) ? ($methods[$o->payment_method][2] ?: $methods[$o->payment_method][1]) : '') ?></div><?php if ($o->payment_method !== 'cod'): ?><p style="font-size:13px;margin:12px 0 0">Please send the payment receipt on WhatsApp quoting <b><?= e($o->order_no) ?></b>.</p><?php endif; ?></div></div>
  </div>
  <div class="text-center mt-5 d-flex gap-3 justify-content-center flex-wrap"><a href="<?= site_url('shop') ?>" class="btn-d">Continue shopping</a><a href="<?= site_url('track-order?no=' . $o->order_no) ?>" class="btn-o">Track order</a></div>
</div>
