<div class="d-flex flex-wrap gap-2 mb-3 no-print"><a class="btn-a2" href="<?= site_url('admin/orders') ?>"><?= icon('arrow-left', 15) ?> Orders</a><a class="btn-a2" target="_blank" href="<?= site_url('admin/orders/invoice/' . $o->id) ?>"><?= icon('printer', 15) ?> Print invoice</a>
  <form class="ms-auto" method="post" action="<?= site_url('admin/orders/delete/' . $o->id) ?>" data-confirm="Delete this order permanently?"><?= csrf_field() ?><button class="btn-a2 btn-danger2"><?= icon('trash-2', 15) ?> Delete</button></form></div>
<div class="row g-3">
  <div class="col-xl-8">
    <div class="cardx mb-3"><div class="hd"><h5>Items - <?= e($o->order_no) ?></h5><?= status_badge($o->status) ?></div><div class="table-responsive"><table class="tb"><thead><tr><th></th><th>Item</th><th>Price</th><th>Qty</th><th style="text-align:right">Total</th></tr></thead><tbody>
      <?php foreach ($items as $it): $meta = $it->meta ? json_decode($it->meta, true) : null; ?><tr>
        <td style="width:80px"><?php if ($it->item_type === 'package' && $meta): ?><div class="outfit-thumb" style="width:64px" data-outfit='<?= e(json_encode($meta['outfit'])) ?>'></div><?php else: ?><img class="thumb" src="<?= upload_url($it->image) ?>" alt=""><?php endif; ?></td>
        <td><b><?= e($it->name) ?></b><div style="color:var(--mute);font-size:12.5px"><?= $it->size ? 'Size ' . e($it->size) . ' ' : '' ?><?= $it->color ? '&middot; ' . e($it->color) : '' ?></div>
          <?php if ($meta && !empty($meta['lines'])): foreach ($meta['lines'] as $l): ?><div style="color:var(--mute);font-size:12.5px"><?= e(ucfirst($l['type'])) ?>: <?= e($l['label']) ?> - <?= e($l['color']) ?><?= $l['size'] ? ', size ' . e($l['size']) : '' ?> (<?= money($l['price']) ?>)</div><?php endforeach; if ($meta['package_saving'] > 0): ?><div style="font-size:12.5px"><b>Package saving: -<?= money($meta['package_saving']) ?></b></div><?php endif; endif; ?></td>
        <td><?= money($it->price) ?></td><td><?= (int)$it->qty ?></td><td style="text-align:right"><b><?= money($it->line_total) ?></b></td></tr><?php endforeach; ?></tbody></table></div>
      <div class="bd" style="border-top:1px solid var(--line)"><div class="ms-auto" style="max-width:300px">
        <div class="d-flex justify-content-between py-1"><span style="color:var(--mute)">Subtotal</span><span><?= money($o->subtotal) ?></span></div>
        <?php if ($o->discount > 0): ?><div class="d-flex justify-content-between py-1"><span style="color:var(--mute)">Discount <?= $o->coupon_code ? '(' . e($o->coupon_code) . ')' : '' ?></span><span>-<?= money($o->discount) ?></span></div><?php endif; ?>
        <div class="d-flex justify-content-between py-1"><span style="color:var(--mute)">Shipping</span><span><?= $o->shipping > 0 ? money($o->shipping) : 'Free' ?></span></div>
        <div class="d-flex justify-content-between pt-2 mt-1" style="border-top:1px solid var(--ink);font-size:17px"><b>Total</b><b><?= money($o->total) ?></b></div></div></div></div>
  </div>
  <div class="col-xl-4">
    <form class="cardx mb-3 no-print" method="post" action="<?= site_url('admin/orders/update/' . $o->id) ?>"><?= csrf_field() ?><div class="hd"><h5>Update order</h5></div><div class="bd">
      <label class="fl">Order status</label><select class="fi mb-3" name="status"><?php foreach (order_statuses() as $s): ?><option value="<?= $s ?>" <?= $o->status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select>
      <label class="fl">Payment status</label><select class="fi mb-3" name="payment_status"><?php foreach (array('unpaid', 'paid', 'refunded') as $s): ?><option value="<?= $s ?>" <?= $o->payment_status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select>
      <label class="fl">Internal note</label><textarea class="fi mb-3" name="admin_note"><?= e($o->admin_note) ?></textarea>
      <button class="btn-a w-100 justify-content-center">Save changes</button><div class="hint">Cancelling an order returns the items to stock automatically.</div></div></form>
    <div class="cardx mb-3"><div class="hd"><h5>Customer</h5></div><div class="bd" style="line-height:1.8"><b><?= e($o->name) ?></b><br><?= e($o->email) ?><br><?= e($o->phone) ?><br><br><?= e($o->address) ?><br><?= e($o->city) ?><?= $o->state ? ', ' . e($o->state) : '' ?> <?= e($o->postal_code) ?><br><?= e($o->country) ?><?php if ($o->notes): ?><br><br><i style="color:var(--mute)">&ldquo;<?= e($o->notes) ?>&rdquo;</i><?php endif; ?></div></div>
    <div class="cardx"><div class="hd"><h5>Details</h5></div><div class="bd" style="line-height:2"><span style="color:var(--mute)">Payment:</span> <b><?= e(strtoupper($o->payment_method)) ?></b> <?= status_badge($o->payment_status) ?><br><span style="color:var(--mute)">Traffic source:</span> <b class="d-inline-flex align-items-center gap-2"><?= source_icon($o->source, 15) ?><?= e(source_meta($o->source)['label']) ?></b><br><span style="color:var(--mute)">Placed:</span> <?= date('d M Y, h:i A', strtotime($o->created_at)) ?></div></div>
  </div>
</div>
