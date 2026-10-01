<div class="container" style="padding-bottom:90px">
  <div class="page-head d-flex justify-content-between align-items-end flex-wrap gap-3"><div><span class="eyebrow">My account</span><h1>Hello, <?= e(explode(' ', $customer->name)[0]) ?></h1></div><div class="d-flex gap-2"><a class="btn-o btn-sm" href="<?= site_url('account/profile') ?>">Edit profile</a><a class="btn-d btn-sm" href="<?= site_url('account/logout') ?>">Sign out</a></div></div>
  <div class="tabs-d"><a href="?tab=orders" class="<?= $tab === 'orders' ? 'on' : '' ?>">Orders (<?= count($orders) ?>)</a><a href="?tab=demands" class="<?= $tab === 'demands' ? 'on' : '' ?>">Demand requests (<?= count($demands) ?>)</a></div>
  <?php if ($tab === 'orders'): ?>
    <?php if (!$orders): ?><div class="empty"><?= icon('package') ?><h3>No orders yet</h3><a href="<?= site_url('shop') ?>" class="btn-d mt-2">Start shopping</a></div><?php else: ?>
    <div class="table-responsive"><table class="tbl"><tr><th>Order</th><th>Date</th><th>Status</th><th>Payment</th><th style="text-align:right">Total</th><th></th></tr>
    <?php foreach ($orders as $o): ?><tr><td><b><?= e($o->order_no) ?></b></td><td><?= date('d M Y', strtotime($o->created_at)) ?></td><td><?= status_badge($o->status) ?></td><td><?= status_badge($o->payment_status) ?></td><td style="text-align:right"><b><?= money($o->total) ?></b></td><td style="text-align:right"><a class="link-arrow" href="<?= site_url('account/order/' . $o->order_no) ?>">View</a></td></tr><?php endforeach; ?></table></div>
    <?php endif; ?>
  <?php else: ?>
    <?php if (!$demands): ?><div class="empty"><?= icon('package') ?><h3>No demand requests</h3><a href="<?= site_url('demand') ?>" class="btn-d mt-2">Request a piece</a></div><?php else: ?>
    <div class="table-responsive"><table class="tbl"><tr><th>Request</th><th>Product</th><th>Date</th><th>Status</th><th style="text-align:right">Quote</th><th></th></tr>
    <?php foreach ($demands as $d): ?><tr><td><b><?= e($d->demand_no) ?></b></td><td><?= e($d->product_name) ?> &times; <?= (int)$d->qty ?></td><td><?= date('d M Y', strtotime($d->created_at)) ?></td><td><?= status_badge($d->status) ?></td><td style="text-align:right"><?= $d->quote_price ? '<b>' . money($d->quote_price) . '</b>' : '<span style="color:var(--mute-2)">Awaiting</span>' ?></td><td style="text-align:right"><a class="link-arrow" href="<?= site_url('track-order?no=' . $d->demand_no . '&who=' . urlencode($customer->email)) ?>">Track</a></td></tr><?php endforeach; ?></table></div>
    <?php endif; ?>
  <?php endif; ?>
</div>
