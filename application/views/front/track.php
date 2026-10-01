<div class="container" style="max-width:860px;padding-bottom:90px">
  <div class="page-head"><span class="eyebrow">Where is my order?</span><h1>Track your order</h1><p>Enter your order or demand number (starts with DR or OD) with the email or phone used.</p></div>
  <form class="panel-d mb-4" method="get" action="<?= site_url('track-order') ?>"><div class="row g-3 align-items-end">
    <div class="col-md-5"><label class="lbl">Order / request number</label><input class="input-d" name="no" value="<?= e($no) ?>" placeholder="DR2609301234" required></div>
    <div class="col-md-5"><label class="lbl">Email or phone</label><input class="input-d" name="who" value="<?= e($who) ?>" required></div>
    <div class="col-md-2"><button class="btn-d btn-block" style="padding:14px 10px">Track</button></div></div></form>
  <?php if ($searched && !$o && !$d): ?><div class="alert-d alert-error"><?= icon('alert-circle', 18) ?><span>We could not find a match. Please check the number and the email / phone you used.</span></div><?php endif; ?>
  <?php if ($o): $stages = array('pending', 'confirmed', 'processing', 'shipped', 'delivered'); $idx = array_search($o->status, $stages); ?>
    <div class="panel-d"><div class="d-flex justify-content-between flex-wrap gap-2"><h4 class="m-0">Order <?= e($o->order_no) ?></h4><?= status_badge($o->status) ?></div><?php $this->load->view('front/partials/order_detail', array('o' => $o, 'items' => $items, 'stages' => $stages, 'idx' => $idx)); ?></div>
  <?php endif; ?>
  <?php if ($d): $st = array('pending', 'reviewing', 'quoted', 'accepted', 'confirmed', 'fulfilled'); $di = array_search($d->status, $st); ?>
    <div class="panel-d">
      <div class="d-flex justify-content-between flex-wrap gap-2"><h4 class="m-0">Request <?= e($d->demand_no) ?></h4><?= status_badge($d->status) ?></div>
      <?php if ($d->status === 'rejected'): ?><div class="alert-d alert-error"><?= icon('x-circle', 18) ?><span>Sorry - we could not source this piece.</span></div>
      <?php else: ?><div class="timeline"><?php foreach ($st as $k => $s): ?><div class="s <?= $di !== false && $k <= $di ? 'done' : '' ?>"><?= ucfirst($s) ?></div><?php endforeach; ?></div><?php endif; ?>
      <div class="row g-3"><div class="col-md-6"><b><?= e($d->product_name) ?></b> &times; <?= (int)$d->qty ?><div style="color:var(--mute);font-size:14px"><?= e(trim($d->size . ' ' . $d->color)) ?></div></div>
      <div class="col-md-6"><?php if ($d->quote_price): ?>Our quote: <b><?= money($d->quote_price) ?></b><?= $d->eta_days ? ' &middot; ready in ~' . (int)$d->eta_days . ' days' : '' ?><?php else: ?><span style="color:var(--mute)">Quote pending</span><?php endif; ?></div></div>
      <?php if ($d->admin_note): ?><p class="mt-3 mb-0" style="color:var(--mute)">Note from our team: <?= e($d->admin_note) ?></p><?php endif; ?>
    </div>
  <?php endif; ?>
</div>
