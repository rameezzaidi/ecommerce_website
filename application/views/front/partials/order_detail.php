<?php if ($o->status === 'cancelled'): ?><div class="alert-d alert-error"><?= icon('x-circle', 18) ?><span>This order was cancelled.</span></div>
<?php else: ?><div class="timeline"><?php foreach ($stages as $k => $s): ?><div class="s <?= $idx !== false && $k <= $idx ? 'done' : '' ?>"><?= ucfirst($s) ?></div><?php endforeach; ?></div><?php endif; ?>
<div class="row g-4">
  <div class="col-lg-8">
    <?php foreach ($items as $it): ?>
    <div class="d-flex gap-3 py-3" style="border-bottom:1px solid var(--line)">
      <?php if ($it->item_type === 'package'): $meta = json_decode($it->meta, true); ?><div class="outfit-thumb" style="width:80px;flex:none" data-outfit='<?= e(json_encode($meta['outfit'])) ?>'></div>
      <?php else: ?><div style="width:72px;flex:none;aspect-ratio:4/5;background:var(--soft);overflow:hidden"><img src="<?= upload_url($it->image) ?>" alt="" style="width:100%;height:100%;object-fit:cover"></div><?php endif; ?>
      <div style="flex:1"><b style="font-weight:600"><?= e($it->name) ?></b>
        <div style="font-size:13px;color:var(--mute)">Qty <?= (int)$it->qty ?> &times; <?= money($it->price) ?><?= $it->size ? ' &middot; ' . e($it->size) : '' ?><?= $it->color ? ' &middot; ' . e($it->color) : '' ?></div>
        <?php if ($it->item_type === 'package' && !empty($meta['lines'])): ?><div style="font-size:13px;color:var(--mute)"><?php foreach ($meta['lines'] as $l): ?><?= e($l['label']) ?> (<?= e($l['color']) ?><?= $l['size'] ? ', ' . e($l['size']) : '' ?>)<br><?php endforeach; ?></div><?php endif; ?></div>
      <b><?= money($it->line_total) ?></b>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="col-lg-4"><div class="summary" style="position:static">
    <div class="sum-row"><span class="mut">Subtotal</span><span><?= money($o->subtotal) ?></span></div>
    <?php if ($o->discount > 0): ?><div class="sum-row"><span class="mut">Discount</span><span>-<?= money($o->discount) ?></span></div><?php endif; ?>
    <div class="sum-row"><span class="mut">Shipping</span><span><?= $o->shipping > 0 ? money($o->shipping) : 'Free' ?></span></div>
    <div class="sum-row total"><span>Total</span><span><?= money($o->total) ?></span></div>
    <div class="mt-3" style="font-size:13.5px;color:var(--mute)">Payment: <b style="color:var(--ink)"><?= e(strtoupper($o->payment_method)) ?></b> <?= status_badge($o->payment_status) ?><br><br><?= e($o->name) ?><br><?= e($o->address) ?>, <?= e($o->city) ?><br><?= e($o->country) ?></div>
  </div></div>
</div>
