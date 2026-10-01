<div class="cardx">
  <div class="hd"><form class="d-flex gap-2 flex-wrap" method="get"><input class="fi" style="width:220px" name="q" value="<?= e($q) ?>" placeholder="Order no, name, phone...">
    <select class="fi" name="status" style="width:150px" onchange="this.form.submit()"><option value="">All status</option><?php foreach (order_statuses() as $s): ?><option value="<?= $s ?>" <?= $st === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select>
    <select class="fi" name="source" style="width:160px" onchange="this.form.submit()"><option value="">All sources</option><?php foreach (source_meta() as $k => $m): ?><option value="<?= $k ?>" <?= $src === $k ? 'selected' : '' ?>><?= $m['label'] ?></option><?php endforeach; ?></select>
    <button class="btn-a2"><?= icon('search', 15) ?> Filter</button></form><span style="color:var(--mute)"><?= (int)$total ?> orders</span></div>
  <div class="table-responsive"><table class="tb"><thead><tr><th>Order</th><th>Customer</th><th>Location</th><th>Source</th><th>Type</th><th>Payment</th><th>Status</th><th style="text-align:right">Total</th><th></th></tr></thead><tbody>
  <?php if (!$rows): ?><tr><td colspan="9" style="text-align:center;padding:40px;color:var(--mute)">No orders found.</td></tr><?php endif; foreach ($rows as $o): ?>
    <tr><td><a href="<?= site_url('admin/orders/view/' . $o->id) ?>"><b><?= e($o->order_no) ?></b></a><div style="color:var(--mute);font-size:12px"><?= date('d M Y, h:i A', strtotime($o->created_at)) ?></div></td>
    <td><?= e($o->name) ?><div style="color:var(--mute);font-size:12px"><?= e($o->phone) ?></div></td><td><span class="cc"><?= e($o->country_code) ?></span><?= e($o->city) ?></td>
    <td><span class="d-inline-flex align-items-center gap-2"><?= source_icon($o->source, 15) ?><?= e(source_meta($o->source)['label']) ?></span></td>
    <td><span class="badge-st st-muted"><?= e(ucfirst($o->order_type)) ?></span></td><td><?= status_badge($o->payment_status) ?></td><td><?= status_badge($o->status) ?></td><td style="text-align:right"><b><?= money($o->total) ?></b></td>
    <td style="text-align:right"><a class="btn-a2 btn-sm2" href="<?= site_url('admin/orders/view/' . $o->id) ?>">View</a></td></tr><?php endforeach; ?></tbody></table></div>
  <?php if ($pages > 1): ?><div class="bd d-flex justify-content-between" style="border-top:1px solid var(--line);padding:14px 22px"><span style="color:var(--mute)">Page <?= $page ?> of <?= $pages ?></span><div class="pager"><?php $qs = $_GET; for ($n = 1; $n <= $pages; $n++): $qs['page'] = $n; ?><?= $n == $page ? '<span class="cur">' . $n . '</span>' : '<a href="?' . http_build_query($qs) . '">' . $n . '</a>' ?><?php endfor; ?></div></div><?php endif; ?>
</div>
